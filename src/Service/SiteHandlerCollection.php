<?php

namespace App\Service;

use App\Exception\ContentFetchingFailedException;
use App\SiteHandler\SiteHandler;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

final readonly class SiteHandlerCollection
{
    /**
     * @param iterable<SiteHandler> $handlers
     */
    public function __construct(
        #[TaggedIterator('app.site_handler')]
        private iterable $handlers,
    ) {
    }

    /**
     * @throws ContentFetchingFailedException
     */
    public function getContent(string $url): string
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($url)) {
                return $handler->getContent($url);
            }
        }

        throw new ContentFetchingFailedException("No handler found for site: {$url}");
    }
}
