<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpFoundation\Request;

#[Memoizable]
#[Memoize]
final readonly class CtvNewsSiteHandler implements SiteHandler
{
    public function __construct(
        private HttpBrowser $browser,
    ) {
    }

    public function supports(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) {
            return false;
        }

        return $host === 'www.ctvnews.ca';
    }

    public function getContent(string $url): string
    {
        $crawler = $this->browser->request(Request::METHOD_GET, $url);
        $parts = $crawler->filter('[role=contentinfo] .c-text > p');
        $content = '';
        foreach ($parts as $part) {
            $content .= $part->nodeValue . "\n\n";
        }

        return trim($content);
    }
}