<?php

namespace App\SiteHandler;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpFoundation\Request;

final class TheGlobeAndMailSiteHandler implements SiteHandler
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

        return $host === 'www.theglobeandmail.com';
    }

    public function getContent(string $url): string
    {
        $crawler = $this->browser->request(Request::METHOD_GET, $url);
        $parts = $crawler->filter('.c-article-body__text');
        $content = '';
        foreach ($parts as $part) {
            $content .= $part->nodeValue . "\n\n";
        }

        return trim($content);
    }
}
