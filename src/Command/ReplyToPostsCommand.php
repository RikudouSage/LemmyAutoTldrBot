<?php

namespace App\Command;

use App\Exception\ContentFetchingFailedException;
use App\Service\PermissionChecker;
use App\Service\PostService;
use App\Service\SiteHandlerCollection;
use App\Service\SummaryTextWrapper;
use App\SummaryProvider\SummaryProvider;
use Psr\Cache\CacheItemPoolInterface;
use Rikudou\LemmyApi\Enum\Language;
use Rikudou\LemmyApi\Exception\LanguageNotAllowedException;
use Rikudou\LemmyApi\LemmyApi;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:reply-to-posts')]
final class ReplyToPostsCommand extends Command
{
    public function __construct(
        private readonly LemmyApi $api,
        private readonly CacheItemPoolInterface $cache,
        private readonly SiteHandlerCollection $siteHandler,
        private readonly SummaryProvider $summaryProvider,
        private readonly PostService $postService,
        private readonly string $instance,
        private readonly PermissionChecker $permissionChecker,
        private readonly int $summaryParagraphs,
        private readonly SummaryTextWrapper $summaryTextWrapper,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $lastHandledIdCache = $this->cache->getItem('lastHandled');
        if ($lastHandledIdCache->isHit()) {
            $lastHandledId = $lastHandledIdCache->get();
        } else {
            $lastHandledId = 0;
        }
        assert(is_int($lastHandledId));
        $storedLastHandledId = $lastHandledId;

        $posts = $this->postService->getPosts($lastHandledId);

        foreach ($posts as $post) {
            if ($post->post->id <= $storedLastHandledId) {
                break;
            }

            if ($post->post->id > $lastHandledId) {
                $lastHandledId = $post->post->id;
            }

            if (!$post->post->url) {
                continue;
            }

            if (!$this->permissionChecker->canPostToCommunity($post->community)) {
                continue;
            }

            try {
                $text = $this->siteHandler->getContent($post->post->url);
                if (!$text) {
                    error_log("Failed reading text for {$post->post->url}");
                    continue;
                }
                $summary = $this->summaryProvider->getSummary($text, $this->summaryParagraphs);
                if (!$summary) {
                    error_log("Failed generating summary for {$post->post->url}");
                    continue;
                }

                $response = $this->summaryTextWrapper->getResponseText($post->community, $summary);
                if ($response === null) {
                    continue;
                }

                try {
                    $this->api->comment()->create(
                        post: $post->post,
                        content: $response,
                        language: Language::English
                    );
                    error_log("Replying to '{$this->instance}/post/{$post->post->id}' using model '{$this->summaryProvider->getId()}'");
                } catch (LanguageNotAllowedException) {
                    $this->api->comment()->create(
                        post: $post->post,
                        content: $response,
                        language: Language::Undetermined
                    );
                    error_log("Replying to '{$this->instance}/post/{$post->post->id}' using model '{$this->summaryProvider->getId()}'");
                }
            } catch (ContentFetchingFailedException|LanguageNotAllowedException) {
                continue;
            } finally {
                $lastHandledIdCache->set($lastHandledId);
                $this->cache->save($lastHandledIdCache);
            }
        }

        return self::SUCCESS;
    }
}
