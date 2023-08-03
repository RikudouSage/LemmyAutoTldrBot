<?php

namespace App\Command;

use App\Exception\ContentFetchingFailedException;
use App\Service\CommentLinkHandler;
use App\Service\PermissionChecker;
use App\Service\SiteHandlerCollection;
use App\Service\SummaryTextWrapper;
use App\SummaryProvider\SummaryProvider;
use Rikudou\LemmyApi\Enum\CommentSortType;
use Rikudou\LemmyApi\Enum\Language;
use Rikudou\LemmyApi\Enum\ListingType;
use Rikudou\LemmyApi\Exception\LanguageNotAllowedException;
use Rikudou\LemmyApi\LemmyApi;
use Rikudou\LemmyApi\Response\Model\Post;
use Rikudou\LemmyApi\Response\View\CommentView;
use Rikudou\LemmyApi\Response\View\PersonMentionView;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:reply-to-mentions')]
final class ReplyToMentionsCommand extends Command
{
    public function __construct(
        private readonly LemmyApi $api,
        private readonly PermissionChecker $permissionChecker,
        private readonly SiteHandlerCollection $siteHandler,
        private readonly string $maintainer,
        private readonly SummaryProvider $summaryProvider,
        private readonly int $summaryParagraphs,
        private readonly SummaryTextWrapper $summaryTextWrapper,
        private readonly CommentLinkHandler $commentLinkHandler,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $maintainer = $this->api->user()->get($this->maintainer);
        $maintainerName = $maintainer->name;
        $maintainerInstance = parse_url($maintainer->actorId, PHP_URL_HOST);

        foreach ($this->getUnreadMentions() as $unreadMention) {
            try {
                $mentionerInstance = parse_url($unreadMention->creator->actorId, PHP_URL_HOST);
                assert(is_string($mentionerInstance));

                if (!$this->permissionChecker->canPostToCommunity($unreadMention->community)) {
                    $communityInstance = parse_url($unreadMention->community->actorId, PHP_URL_HOST);
                    $this->api->currentUser()->sendPrivateMessage(
                        recipient: $unreadMention->creator,
                        content: "Hi there! I see you mentioned me in a post in !{$unreadMention->community->name}@{$communityInstance} but I'm afraid I'm not allowed to participate in that community.\n\nIf you feel I would be helpful, contact the mods of the community and let them know!",
                    );
                    continue;
                }
                $me = $unreadMention->recipient;
                $post = $unreadMention->post;
                if (!$post->url) {
                    $response = "I'm sorry, I don't see any link in the post, I'm not sure what I should summarize.";

                    try {
                        $this->api->comment()->create(
                            post: $unreadMention->post,
                            content: $response,
                            language: Language::English,
                            parent: $unreadMention->comment,
                        );
                    } catch (LanguageNotAllowedException) {
                        $this->api->comment()->create(
                            post: $unreadMention->post,
                            content: $response,
                            language: Language::Undetermined,
                            parent: $unreadMention->comment,
                        );
                    }
                    continue;
                }

                $url = $post->url;

                try {
                    $topComments = [...$this->getAllTopComments($post)];
                    $topCommentsByMe = array_filter($topComments, static fn (CommentView $comment) => $comment->creator->id === $me->id);

                    if (!count($topCommentsByMe)) {
                        $articleContent = $this->siteHandler->getContent($url);
                        $summary = $this->summaryProvider->getSummary($articleContent, $this->summaryParagraphs);

                        try {
                            $summaryComment = $this->api->comment()->create(
                                post: $unreadMention->post,
                                content: $this->summaryTextWrapper->getResponseText($summary),
                                language: Language::English,
                            );
                        } catch (LanguageNotAllowedException) {
                            $summaryComment = $this->api->comment()->create(
                                post: $unreadMention->post,
                                content: $this->summaryTextWrapper->getResponseText($summary),
                                language: Language::Undetermined,
                            );
                        }
                        $response = 'I just created the summary! ';
                    } else {
                        $summaryComment = $topCommentsByMe[array_key_first($topCommentsByMe)];
                        $response = 'I already created the summary. ';
                    }
                    $summaryCommentLink = $this->commentLinkHandler->getCommentLink($summaryComment->comment, $mentionerInstance, $error);
                    $response .= "You can find it at {$summaryCommentLink}.";
                    if ($error) {
                        $response .= " (I tried to create the link for your instance but I failed miserably, for which I'm very sorry).";
                    }

                    try {
                        $this->api->comment()->create(
                            post: $unreadMention->post,
                            content: $response,
                            language: Language::English,
                            parent: $unreadMention->comment,
                        );
                    } catch (LanguageNotAllowedException) {
                        $this->api->comment()->create(
                            post: $unreadMention->post,
                            content: $response,
                            language: Language::Undetermined,
                            parent: $unreadMention->comment,
                        );
                    }
                } catch (ContentFetchingFailedException) {
                    $response = "I'm sorry, I don't know how to handle links for that site. You may contact my maintainer, [@{$maintainerName}@{$maintainerInstance}](/u/{$maintainerName}@{$maintainerInstance}), if you wish to add it to supported sites!";

                    try {
                        $this->api->comment()->create(
                            post: $unreadMention->post,
                            content: $response,
                            language: Language::English,
                            parent: $unreadMention->comment,
                        );
                    } catch (LanguageNotAllowedException) {
                        $this->api->comment()->create(
                            post: $unreadMention->post,
                            content: $response,
                            language: Language::Undetermined,
                            parent: $unreadMention->comment,
                        );
                    }
                    continue;
                }
            } finally {
                $this->api->currentUser()->markMentionAsRead($unreadMention->personMention);
            }
        }

        return self::SUCCESS;
    }

    /**
     * @return iterable<PersonMentionView>
     */
    private function getUnreadMentions(): iterable
    {
        $page = 1;
        do {
            $mentions = $this->api->currentUser()->getMentions(page: $page, unreadOnly: true);

            yield from $mentions;
            ++$page;
        } while (count($mentions));
    }

    /**
     * @return iterable<CommentView>
     */
    private function getAllTopComments(Post $post): iterable
    {
        $alreadySent = [];
        $page = 1;
        do {
            $comments = $this->api->comment()->getComments(
                maxDepth: 1,
                page: $page,
                post: $post,
                sortType: CommentSortType::New,
                listingType: ListingType::All,
            );
            $comments = array_filter($comments, static fn (CommentView $comment) => !in_array($comment->comment->id, $alreadySent, true));

            yield from $comments;
            $alreadySent = array_merge($alreadySent, array_map(static fn (CommentView $comment) => $comment->comment->id, $comments));
            ++$page;
        } while (count($comments));
    }
}
