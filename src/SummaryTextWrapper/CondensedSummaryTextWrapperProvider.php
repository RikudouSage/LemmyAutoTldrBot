<?php

namespace App\SummaryTextWrapper;

use Rikudou\LemmyApi\Response\Model\Community;

final readonly class CondensedSummaryTextWrapperProvider implements SummaryTextWrapperProvider
{
    /**
     * @param array<string> $condensedInstances
     * @param array<string> $condensedCommunities
     */
    public function __construct(
        private array $condensedInstances,
        private array $condensedCommunities,
    ) {
    }

    public function supports(Community $community): bool
    {
        $instance = parse_url($community->actorId, PHP_URL_HOST);
        $fullCommunityName = "{$community->name}@{$instance}";

        return in_array($fullCommunityName, $this->condensedCommunities, true)
            || in_array($instance, $this->condensedInstances, true);
    }

    public function getSummaryText(array $summary): string
    {
        $stringSummary = implode("\n\n", $summary);

        return <<<CANT_THINK_OF_UNIQUE_DELIMITER
            🤖 I'm a bot that provides automatic summaries for articles:
            ::: spoiler Click here to see the summary
            {$stringSummary}
            :::
            CANT_THINK_OF_UNIQUE_DELIMITER;
    }
}
