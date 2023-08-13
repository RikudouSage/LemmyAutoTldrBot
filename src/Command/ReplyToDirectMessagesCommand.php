<?php

namespace App\Command;

use Rikudou\LemmyApi\LemmyApi;
use Rikudou\LemmyApi\Response\View\PrivateMessageView;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:reply-to-pms')]
final class ReplyToDirectMessagesCommand extends Command
{
    public function __construct(
        private readonly LemmyApi $api,
        private readonly string $currentUsername,
        private readonly string $maintainer,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        error_log('Running command to reply to all direct messages');

        $maintainer = $this->api->user()->get($this->maintainer);
        $maintainerName = $maintainer->name;
        $maintainerInstance = parse_url($maintainer->actorId, PHP_URL_HOST);

        $i = 1;
        foreach ($this->getUnreadMessages() as $privateMessage) {
            $content = $privateMessage->privateMessage->content;
            $author = $privateMessage->creator->name;
            $authorInstance = parse_url($privateMessage->creator->actorId, PHP_URL_HOST);

            $textForMaintainer = "Message to bot {$this->currentUsername} from [@{$author}@{$authorInstance}](/u/{$author}@{$authorInstance}):\n\n---\n\n{$content}";
            $textForSender = "Hi there! I'm a bot and this inbox is not regularly checked. I have forwarded your message to my author, [@{$maintainerName}@{$maintainerInstance}](/u/{$maintainerName}@{$maintainerInstance}).";

            error_log("Replying to private message #{$i}");

            $this->api->currentUser()->sendPrivateMessage(recipient: $maintainer, content: $textForMaintainer);
            $this->api->currentUser()->sendPrivateMessage(recipient: $privateMessage->creator, content: $textForSender);

            $this->api->currentUser()->markPrivateMessageAsRead($privateMessage->privateMessage);
            error_log("Replied to message #{$i}");

            ++$i;
        }

        error_log('Handling direct messages done.');

        return self::SUCCESS;
    }

    /**
     * @return iterable<PrivateMessageView>
     */
    private function getUnreadMessages(): iterable
    {
        $page = 1;
        do {
            $messages = $this->api->currentUser()->getPrivateMessages(page: $page, unreadOnly: true);

            yield from $messages;
            ++$page;
        } while (count($messages));
    }
}
