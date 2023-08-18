<?php

namespace App\Service;

use App\SummaryTextWrapper\SummaryTextWrapperProvider;
use Rikudou\LemmyApi\Response\Model\Community;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

final readonly class SummaryTextWrapper
{
    /**
     * @param iterable<SummaryTextWrapperProvider> $providers
     */
    public function __construct(
        #[TaggedIterator('app.summary_wrapper')]
        private iterable $providers,
    ) {
    }

    /**
     * @param array<string> $summary
     */
    public function getResponseText(Community $community, array $summary, string $originalText): ?string
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($community)) {
                return $provider->getSummaryText($summary, $originalText);
            }
        }

        return null;
    }
}
