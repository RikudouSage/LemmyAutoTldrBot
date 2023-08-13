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
        error_log('Running command to handle posts');

        $lastHandledIdCache = $this->cache->getItem('lastHandled');
        if ($lastHandledIdCache->isHit()) {
            $lastHandledId = $lastHandledIdCache->get();
        } else {
            $lastHandledId = 0;
        }
        assert(is_int($lastHandledId));
        $storedLastHandledId = $lastHandledId;

        error_log("Last previous handled post ID: {$storedLastHandledId}");

        $posts = $this->postService->getPosts($lastHandledId);

        $i = 1;
        foreach ($posts as $post) {
            error_log("Handling post #{$i} (id: {$post->post->id})");
            if ($post->post->id <= $storedLastHandledId) {
                error_log('The post has lower ID than the from previous runs, not handling it');
                break;
            }

            if ($post->post->id > $lastHandledId) {
                $lastHandledId = $post->post->id;
            }

            if (!$post->post->url) {
                error_log("Post doesn't contain a link, skipping");
                continue;
            }

            if (!$this->permissionChecker->canPostToCommunity($post->community)) {
                error_log('Cannot post to the community, skipping');
                continue;
            }

            try {
                $text = $this->siteHandler->getContent($post->post->url);
                if (!$text) {
                    error_log("Failed reading text for {$post->post->url}, skipping");
                    continue;
                }
                $summary = $this->summaryProvider->getSummary($text, $this->summaryParagraphs);
                if (!$summary) {
                    error_log("Failed generating summary for {$post->post->url}, skipping");
                    continue;
                }

                $response = $this->summaryTextWrapper->getResponseText($post->community, $summary);
                if ($response === null) {
                    error_log('Failed generating wrapped summary, skipping');
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
                error_log('Got an exception, skipping');
                continue;
            } finally {
                error_log("Done handling post #{$i}");
                $lastHandledIdCache->set($lastHandledId);
                $this->cache->save($lastHandledIdCache);
            }
        }

        return self::SUCCESS;
    }
}
