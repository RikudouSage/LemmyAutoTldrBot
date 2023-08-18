<?php

namespace App\SummaryTextWrapper;

use Rikudou\LemmyApi\Response\Model\Community;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.summary_wrapper')]
interface SummaryTextWrapperProvider
{
    public function supports(Community $community): bool;

    /**
     * @param array<string> $summary
     */
    public function getSummaryText(array $summary, string $originalText): string;
}
