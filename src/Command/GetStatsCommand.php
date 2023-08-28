<?php

namespace App\Command;

use DateTimeImmutable;
use DateTimeZone;
use Rikudou\LemmyApi\Enum\CommentSortType;
use Rikudou\LemmyApi\Enum\SortType;
use Rikudou\LemmyApi\LemmyApi;
use Rikudou\LemmyApi\Response\View\CommentView;
use Rikudou\LemmyApi\Response\View\PersonMentionView;
use Rikudou\LemmyApi\Response\View\PrivateMessageView;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:stats')]
final class GetStatsCommand extends Command
{
    public function __construct(
        private readonly LemmyApi $api,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                name: 'date',
                mode: InputArgument::OPTIONAL,
                description: 'The date to print stats for. Use all for all-time stats',
                default: (new DateTimeImmutable(timezone: new DateTimeZone('UTC')))->format('Y-m-d'),
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $dateArgument = $input->getArgument('date');
        if ($dateArgument === 'all') {
            $startDate = new DateTimeImmutable('2020-01-01');
            $endDate = new DateTimeImmutable('2100-01-01');
        } else {
            $dateArgument = date('Y-m-d', strtotime($dateArgument) ?: throw new RuntimeException('Invalid date'));
            $startDate = new DateTimeImmutable("{$dateArgument}T00:00:00Z");
            $endDate = new DateTimeImmutable("{$dateArgument}T23:59:59Z");
        }

        $commentCount = 0;
        $upvotes = 0;
        $downvotes = 0;
        $neutralCount = 0;
        $positiveCount = 0;
        $negativeCount = 0;
        $goodBots = 0;
        $badBots = 0;

        $communities = [];
        $perInstanceUpvotes = [];
        $perInstanceDownvotes = [];
        $perInstanceComments = [];

        $localUser = $this->api->site()->getSite()->myUser?->localUserView ?? throw new RuntimeException('Failed to get current user');
        $me = $localUser->person;

        $progressBar = $io->createProgressBar();
        $progressBar->setMessage('Initializing...');
        if ($dateArgument === 'all') {
            $progressBar->setMaxSteps($localUser->counts->commentCount);
            $progressBar->setFormat('[%bar%] [%current%/%max%] - %message% (running %elapsed% of ~%estimated%)');
        } else {
            $progressBar->setFormat('[%bar%] [%current%] - %message% (running %elapsed%)');
        }

        $progressBar->start();
        foreach ($this->getComments($startDate, $endDate) as $comment) {
            $progressBar->setMessage("Processing comment from {$comment->comment->published->format('c')}");
            $progressBar->advance();

            ++$commentCount;
            $upvotes += $comment->counts->upvotes - 1;
            $downvotes += $comment->counts->downvotes;
            if ($upvotes > $downvotes) {
                ++$positiveCount;
            } elseif ($downvotes > $upvotes) {
                ++$negativeCount;
            } else {
                ++$neutralCount;
            }
            $goodBots += $this->getGoodBots($comment);
            $badBots += $this->getBadBots($comment);

            $instance = parse_url($comment->community->actorId, PHP_URL_HOST);

            $perInstanceUpvotes[$instance] ??= 0;
            $perInstanceDownvotes[$instance] ??= 0;
            $perInstanceComments[$instance] ??= 0;
            $perInstanceUpvotes[$instance] += $comment->counts->upvotes - 1;
            $perInstanceDownvotes[$instance] += $comment->counts->downvotes;
            $perInstanceComments[$instance] += 1;

            $communities[$comment->community->actorId] ??= 0;
            $communities[$comment->community->actorId] += 1;
        }
        natsort($communities);
        $communities = array_reverse($communities);

        if ($dateArgument !== 'all') {
            $io->comment("Stats for {$dateArgument} (UTC)");
        } else {
            $io->comment('All time stats');
        }
        $io->table([
            'Comments',
            'Upvotes',
            'Downvotes',
            'Negative comments count',
            'Positive comments count',
            'Neutral comments count',
            'Good bots',
            'Bad bots',
        ], [
            [$commentCount, $upvotes, $downvotes, $negativeCount, $positiveCount, $neutralCount, $goodBots, $badBots],
        ]);

        $sentMessageCount = 0;
        $receivedMessageCount = 0;

        foreach ($this->getPrivateMessages($startDate, $endDate) as $privateMessage) {
            if ($privateMessage->creator->id === $me->id) {
                ++$sentMessageCount;
            } else {
                ++$receivedMessageCount;
            }
        }
        $io->table(['Sent messages', 'Received messages'], [[$sentMessageCount, $receivedMessageCount]]);

        $mentionsResponded = 0;
        $mentionsUnresponded = 0;

        foreach ($this->getMentions($startDate, $endDate) as $mention) {
            $comments = $this->api->comment()->getComments(parent: $mention->comment);
            if (!count($comments)) {
                ++$mentionsUnresponded;
                continue;
            }

            $commentsByMe = array_filter($comments, static fn (CommentView $comment) => $comment->creator->id === $me->id);
            if (count($commentsByMe)) {
                ++$mentionsResponded;
            } else {
                ++$mentionsUnresponded;
            }
        }
        $io->table(
            ['Mentions', 'Responded', "Didn't respond"],
            [[$mentionsResponded + $mentionsUnresponded, $mentionsResponded, $mentionsUnresponded]],
        );

        $io->table(
            ['Instance', 'Comments', 'Upvotes', 'Downvotes', 'Like ratio', 'Upvotes per comment'],
            array_map(
                static fn (string $instance, int $upvotes, int $downvotes, int $comments) => [ // @phpstan-ignore-line
                    $instance,
                    $comments,
                    $upvotes,
                    $downvotes,
                    $upvotes !== 0 || $downvotes !== 0 ? number_format($upvotes / ($downvotes + $upvotes) * 100, 2) . '%' : 'N/A',
                    number_format($upvotes / $comments, 2),
                ],
                array_keys($perInstanceUpvotes),
                $perInstanceUpvotes,
                $perInstanceDownvotes,
                $perInstanceComments,
            )
        );

        $io->table(
            ['Community', 'Comment count'],
            array_map(static fn (int $count, string $community) => [$community, $count], $communities, array_keys($communities)),
        );

        return self::SUCCESS;
    }

    /**
     * @return iterable<CommentView>
     */
    private function getComments(DateTimeImmutable $startDate, DateTimeImmutable $endDate): iterable
    {
        $me = $this->api->site()->getSite()->myUser?->localUserView->person ?? throw new RuntimeException('Failed to get current user');

        $page = 1;
        do {
            $comments = $this->api->user()->getComments(
                user: $me,
                limit: 50,
                page: $page,
                sort: SortType::New,
            );

            foreach ($comments as $comment) {
                if ($comment->comment->published > $endDate) {
                    continue;
                }
                if ($comment->comment->published < $startDate) {
                    break 2;
                }

                yield $comment;
            }

            ++$page;
        } while (count($comments));
    }

    /**
     * @return iterable<PrivateMessageView>
     */
    private function getPrivateMessages(DateTimeImmutable $startDate, DateTimeImmutable $endDate): iterable
    {
        $page = 1;

        do {
            $messages = $this->api->currentUser()->getPrivateMessages(page: $page);
            foreach ($messages as $message) {
                if ($message->privateMessage->published > $endDate) {
                    continue;
                }
                if ($message->privateMessage->published < $startDate) {
                    break 2;
                }

                yield $message;
            }
            ++$page;
        } while (count($messages));
    }

    /**
     * @return iterable<PersonMentionView>
     */
    private function getMentions(DateTimeImmutable $startDate, DateTimeImmutable $endDate): iterable
    {
        $page = 1;

        do {
            $mentions = $this->api->currentUser()->getMentions(page: $page);
            foreach ($mentions as $mention) {
                if ($mention->personMention->published > $endDate) {
                    continue;
                }
                if ($mention->personMention->published < $startDate) {
                    break 2;
                }

                yield $mention;
            }
            ++$page;
        } while (count($mentions));
    }

    private function getGoodBots(CommentView $parent): int
    {
        $page = 1;

        $handled = [];

        $result = 0;
        do {
            $comments = $this->api->comment()->getComments(page: 1, parent: $parent->comment, sortType: CommentSortType::New);
            $comments = array_filter($comments, static fn (CommentView $comment) => $comment->comment->id !== $parent->comment->id);
            $comments = array_filter($comments, static fn (CommentView $comment) => !in_array($comment->comment->id, $handled, true));
            foreach ($comments as $comment) {
                assert($comment instanceof CommentView);
                $handled[] = $comment->comment->id;
                if (str_contains(mb_strtolower($comment->comment->content), 'good bot')) {
                    ++$result;
                }
            }
            ++$page;
        } while (count($comments));

        return $result;
    }

    private function getBadBots(CommentView $parent): int
    {
        $page = 1;

        $handled = [];

        $result = 0;
        do {
            $comments = $this->api->comment()->getComments(page: 1, parent: $parent->comment);
            $comments = array_filter($comments, static fn (CommentView $comment) => $comment->comment->id !== $parent->comment->id);
            $comments = array_filter($comments, static fn (CommentView $comment) => !in_array($comment->comment->id, $handled, true));
            foreach ($comments as $comment) {
                assert($comment instanceof CommentView);
                $handled[] = $comment->comment->id;
                if (str_contains(mb_strtolower($comment->comment->content), 'bad bot')) {
                    ++$result;
                }
            }
            ++$page;
        } while (count($comments));

        return $result;
    }
}
