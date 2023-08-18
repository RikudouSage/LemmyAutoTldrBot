<?php

namespace App\SummaryTextWrapper;

use App\Service\RatioFormatter;
use Rikudou\LemmyApi\Response\Model\Community;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(priority: -1_000)]
final readonly class DefaultSummaryTextWrapperProvider implements SummaryTextWrapperProvider
{
    public function __construct(
        private string $sourceCodeLink,
        private RatioFormatter $ratioFormatter,
    ) {
    }

    public function supports(Community $community): bool
    {
        return true;
    }

    public function getSummaryText(array $summary, string $originalText): string
    {
        $stats = $this->ratioFormatter->getRatio($originalText, implode("\n", $summary));

        return 'This is the best summary I could come up with:'
            . "\n\n---\n\n"
            . implode("\n\n", $summary)
            . "\n\n---\n\n"
            . "The original article contains {$stats->formattedOriginalLength} words, the summary contains {$stats->formattedSummaryLength} words. "
            . "Saved {$stats->formattedRatioSaved}. "
            . "I'm a bot and I'm [open source]({$this->sourceCodeLink})!";
    }
}
