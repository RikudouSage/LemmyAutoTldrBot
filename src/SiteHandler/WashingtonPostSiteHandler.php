<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class WashingtonPostSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.washingtonpost.com'];
    }

    protected function getSelector(): string
    {
        return '#__NEXT_DATA__';
    }

    public function getContent(string $url): string
    {
        $crawler = $this->getArticleCrawler($url);
        $scriptContent = $crawler->filter($this->getSelector())->text();
        $json = json_decode($scriptContent, true);
        assert(is_array($json));
        $parts = array_filter($json['props']['pageProps']['globalContent']['content_elements'], static fn (array $element) => $element['type'] === 'text');
        $parts = array_map(static fn (array $element) => html_entity_decode(strip_tags($element['content'])), $parts);

        return implode("\n\n", $parts);
    }
}
