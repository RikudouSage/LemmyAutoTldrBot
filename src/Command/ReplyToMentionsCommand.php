<?php

namespace App\Command;

use App\Exception\ContentFetchingFailedException;
use App\Service\LinkResolver;
use App\Service\PermissionChecker;
use App\Service\SiteHandlerCollection;
use App\Service\SummaryTextWrapper;
use App\SummaryProvider\SummaryProvider;
use Rikudou\LemmyApi\Enum\CommentSortType;
use Rikudou\LemmyApi\Enum\Language;
use Rikudou\LemmyApi\Enum\ListingType;
use Rikudou\LemmyApi\Exception\LanguageNotAllowedException;
use Rikudou\LemmyApi\LemmyApi;
use Rikudou\LemmyApi\Response\Model\Comment;
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
        private readonly LinkResolver $linkResolver,
        private readonly string $supportCommunity,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        error_log('Running command to reply to mentions');

        $maintainer = $this->api->user()->get($this->maintainer);
        $maintainerName = $maintainer->name;
        $maintainerInstance = parse_url($maintainer->actorId, PHP_URL_HOST);

        $i = 1;
        foreach ($this->getUnreadMentions() as $unreadMention) {
            error_log("Handling mention #{$i}");

            try {
                $hasPermissionToPost = $this->permissionChecker->canPostToCommunity($unreadMention->community);
                $mentionerInstance = parse_url($unreadMention->creator->actorId, PHP_URL_HOST);
                assert(is_string($mentionerInstance));

                error_log('Has permission to post: ' . ($hasPermissionToPost ? 'true' : 'false'));

                $me = $unreadMention->recipient;
                $post = $unreadMention->post;
                if (!$post->url) {
                    error_log('No URL');
                    $this->sendReply(
                        "I'm sorry, I don't see any link in the post, I'm not sure what I should summarize.",
                        $unreadMention,
                        $unreadMention->comment,
                    );
                    continue;
                }

                $url = $post->url;

                try {
                    $topComments = [...$this->getAllTopComments($post)];
                    $topCommentsByMe = array_filter($topComments, static fn (CommentView $comment) => $comment->creator->id === $me->id);

                    if (!count($topCommentsByMe)) {
                        error_log('Creating summary');
                        $articleContent = $this->siteHandler->getContent($url);
                        if (!$articleContent) {
                            error_log('No content from article');
                            $this->sendReply(
                                "I'm sorry, I couldn't create a summary for the article.",
                                $unreadMention,
                                $unreadMention->comment,
                            );
                            continue;
                        }
                        $summary = $this->summaryProvider->getSummary($articleContent, $this->summaryParagraphs);

                        if (!$hasPermissionToPost) {
                            $this->sendReply(implode("\n\n", $summary), $unreadMention);
                            continue;
                        }

                        $response = $this->summaryTextWrapper->getResponseText($unreadMention->community, $summary, $articleContent);
                        if ($response === null) {
                            continue;
                        }

                        try {
                            $summaryComment = $this->api->comment()->create(
                                post: $unreadMention->post,
                                content: $response,
                                language: Language::English,
                            );
                        } catch (LanguageNotAllowedException) {
                            $summaryComment = $this->api->comment()->create(
                                post: $unreadMention->post,
                                content: $response,
                                language: Language::Undetermined,
                            );
                        }
                        $response = 'I just created the summary! ';
                    } else {
                        error_log('Summary already posted');
                        $summaryComment = $topCommentsByMe[array_key_first($topCommentsByMe)];
                        $response = 'I already created the summary. ';
                    }
                    $summaryCommentLink = $this->linkResolver->getCommentLink($summaryComment->comment, $mentionerInstance, $error);
                    $response .= "You can find it at {$summaryCommentLink}.";
                    if ($error) {
                        $response .= " (I tried to create the link for your instance but I failed miserably, for which I'm very sorry).";
                    }

                    $this->sendReply($response, $unreadMention, $unreadMention->comment);
                } catch (ContentFetchingFailedException) {
                    error_log('Unsupported site');

                    $officialSupportCommunityText = '';
                    if ($this->supportCommunity) {
                        $officialSupportCommunityText = " or visit the official community at [!{$this->supportCommunity}](/c/{$this->supportCommunity})";
                    }
                    $response = "I'm sorry, I don't know how to handle links for that site. You may contact my maintainer, [@{$maintainerName}@{$maintainerInstance}](/u/{$maintainerName}@{$maintainerInstance}){$officialSupportCommunityText}, if you wish to add it to supported sites!";
                    $this->sendReply($response, $unreadMention, $unreadMention->comment);
                    if (!$hasPermissionToPost) {
                        $this->api->currentUser()->sendPrivateMessage(
                            recipient: $maintainer,
                            content: "@{$me->name} bot got called for a site it can't handle: {$this->linkResolver->getPostLink($unreadMention->post)}",
                        );
                    }
                    continue;
                }
            } finally {
                error_log("Handling mention #{$i} done.");
                ++$i;
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

    private function sendReply(string $reply, PersonMentionView $mention, ?Comment $parent = null): void
    {
        $mentionerInstance = parse_url($mention->creator->actorId, PHP_URL_HOST) ?: null;
        $hasPermission = $this->permissionChecker->canPostToCommunity($mention->community);
        $text = '';
        if (!$hasPermission) {
            $text .= "I'm replying to the mention at {$this->linkResolver->getPostLink($mention->post, $mentionerInstance)} in private, because I've been forbidden from replying in comments:\n\n---\n\n";
        }
        $text .= $reply;
        if (!$hasPermission) {
            $text .= "\n\n---\n\nIf you believe this bot is useful, please contact the mods of your favorite community and let them know!";
        }

        if ($hasPermission) {
            try {
                $this->api->comment()->create(
                    post: $mention->post,
                    content: $text,
                    language: Language::English,
                    parent: $parent,
                );
            } catch (LanguageNotAllowedException) {
                $this->api->comment()->create(
                    post: $mention->post,
                    content: $text,
                    language: Language::Undetermined,
                    parent: $parent,
                );
            }
        } else {
            $this->api->currentUser()->sendPrivateMessage(
                recipient: $mention->creator,
                content: $text,
            );
        }
    }
}
