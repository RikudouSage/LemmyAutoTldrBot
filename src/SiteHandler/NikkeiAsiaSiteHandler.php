<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Memoizable]
#[Memoize]
final readonly class NikkeiAsiaSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['asia.nikkei.com'];
    }

    protected function getArticleCrawler(string $url): Crawler
    {
        $path = parse_url($url, PHP_URL_PATH);
        assert(is_string($path));
        $base64Path = base64_encode($path);
        $newUrl = "https://asia.nikkei.com/__service/v1/piano/article_access/{$base64Path}";
        $response = $this->httpClient->request(Request::METHOD_GET, $newUrl, [
            'headers' => [
                'User-Agent' => $this->getUserAgent(),
            ],
        ]);
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new RuntimeException('Failed getting article content');
        }
        $json = json_decode($response->getContent(), true);
        assert(is_array($json));

        return new Crawler($json['body']);
    }

    protected function getSelector(): string
    {
        return 'p';
    }
}
