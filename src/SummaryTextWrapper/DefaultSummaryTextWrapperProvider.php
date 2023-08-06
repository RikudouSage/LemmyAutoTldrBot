<?php

namespace App\SummaryTextWrapper;

use Rikudou\LemmyApi\Response\Model\Community;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(priority: -1_000)]
final readonly class DefaultSummaryTextWrapperProvider implements SummaryTextWrapperProvider
{
    public function __construct(
        private string $sourceCodeLink,
    ) {
    }

    public function supports(Community $community): bool
    {
        return true;
    }

    public function getSummaryText(array $summary): string
    {
        return "This is the best summary I could come up with:\n\n---\n\n" . implode("\n\n", $summary) . "\n\n---\n\nI'm a bot and I'm [open source]({$this->sourceCodeLink})!";
    }
}
