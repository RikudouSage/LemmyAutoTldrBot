<?php

namespace App\Service;

use App\Dto\RatioFormatterResult;
use NumberFormatter;
use RuntimeException;

final class RatioFormatter
{
    public function getRatio(string $originalText, string $summary): RatioFormatterResult
    {
        $percentFormatter = new NumberFormatter('en-US', NumberFormatter::PERCENT);
        $numberFormatter = new NumberFormatter('en-US', NumberFormatter::DEFAULT_STYLE);

        $summaryLength = count(preg_split("@\s+@", $summary) ?: []);
        $originalTextLength = count(preg_split("@\s+@", $originalText) ?: []);

        $ratioSaved = 1 - $summaryLength / $originalTextLength;

        return new RatioFormatterResult(
            originalLength: $originalTextLength,
            summaryLength: $summaryLength,
            ratioSaved: $ratioSaved,
            formattedOriginalLength: $numberFormatter->format($originalTextLength) ?: throw new RuntimeException('Failed formatting'),
            formattedSummaryLength: $numberFormatter->format($summaryLength) ?: throw new RuntimeException('Failed formatting'),
            formattedRatioSaved: $percentFormatter->format($ratioSaved) ?: throw new RuntimeException('Failed formatting'),
        );
    }
}
