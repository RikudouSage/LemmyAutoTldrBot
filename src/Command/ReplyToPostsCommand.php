<?php

namespace App\Command;

use App\Exception\ContentFetchingFailedException;
use App\Service\SiteHandlerCollection;
use App\SummaryProvider\SummaryProvider;
use Psr\Cache\CacheItemPoolInterface;
use Rikudou\LemmyApi\Enum\Language;
use Rikudou\LemmyApi\Enum\ListingType;
use Rikudou\LemmyApi\Enum\SortType;
use Rikudou\LemmyApi\Exception\LanguageNotAllowedException;
use Rikudou\LemmyApi\LemmyApi;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:reply')]
final class ReplyToPostsCommand extends Command
{
    public function __construct(
        private readonly LemmyApi $api,
        private readonly CacheItemPoolInterface $cache,
        private readonly SiteHandlerCollection $siteHandler,
        private readonly SummaryProvider $summaryProvider,
        private readonly string $instance,
        private readonly string $sourceCodeLink,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $community = null;
        //        $community = $this->api->community()->get('bot_playground');

        $lastHandledIdCache = $this->cache->getItem('lastHandled');
        if ($lastHandledIdCache->isHit()) {
            $lastHandledId = $lastHandledIdCache->get();
        } else {
            $lastHandledId = 0;
        }
        assert(is_int($lastHandledId));
        $storedLastHandledId = $lastHandledId;

        $posts = $this->api->post()->getPosts(
            community: $community,
            sort: SortType::New,
            listingType: ListingType::All,
        );

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

            try {
                $text = $this->siteHandler->getContent($post->post->url);
                if (!$text) {
                    error_log("Failed reading text for {$post->post->url}");
                    continue;
                }
                $summary = $this->summaryProvider->getSummary($text, 5);
                if (!$summary) {
                    error_log("Failed generating summary for {$post->post->url}");
                    continue;
                }

                $response = "This is the best summary I could come up with:\n\n---\n\n" . implode("\n\n", $summary) . "\n\n---\n\nI'm a bot and I'm [open source]({$this->sourceCodeLink})!";

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
