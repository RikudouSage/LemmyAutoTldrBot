<?php

namespace App\Dto;

final readonly class RatioFormatterResult
{
    public function __construct(
        public int $originalLength,
        public int $summaryLength,
        public float $ratioSaved,
        public string $formattedOriginalLength,
        public string $formattedSummaryLength,
        public string $formattedRatioSaved,
    ) {
    }
}
