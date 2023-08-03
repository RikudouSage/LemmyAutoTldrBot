<?php

namespace App\Service;

final readonly class SummaryTextWrapper
{
    public function __construct(
        private string $sourceCodeLink,
    ) {
    }

    /**
     * @param array<string> $summary
     */
    public function getResponseText(array $summary): string
    {
        return "This is the best summary I could come up with:\n\n---\n\n" . implode("\n\n", $summary) . "\n\n---\n\nI'm a bot and I'm [open source]({$this->sourceCodeLink})!";
    }
}
