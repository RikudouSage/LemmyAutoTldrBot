<?php

namespace App\SummaryProvider;

interface SummaryProvider
{
    /**
     * @return array<string>
     */
    public function getSummary(string $text, int $sentences): array;

    public function getId(): string;
}
