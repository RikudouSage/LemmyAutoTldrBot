<?php

namespace App\SiteHandler;

use DOMNode;
use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Memoizable]
abstract readonly class AbstractSiteHandler implements SiteHandler
{
    public function __construct(
        protected HttpClientInterface $httpClient,
        protected HttpBrowser $browser,
    ) {
    }

    /**
     * @return array<string>
     */
    abstract protected function getHostnames(): array;

    abstract protected function getSelector(): string;

    public function supports(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) {
            return false;
        }

        return in_array($host, $this->getHostnames(), true);
    }

    #[Memoize]
    public function getContent(string $url): string
    {
        $crawler = $this->getArticleCrawler($url);
        $parts = $crawler->filter($this->getSelector());
        $content = '';

        $count = count($parts);
        $i = 0;
        $ignoreLast = $this->ignoreLast();

        $regex = $this->skipIfMatches();
        $breakCallable = $this->breakIf() ?? static fn () => false;
        $skipCallable = $this->skipIf() ?? static fn () => false;
        $context = [];

        foreach ($parts as $part) {
            if ($i === $count - $ignoreLast) {
                break;
            }
            if ($breakCallable($part, $context)) {
                break;
            }
            ++$i;
            if ($regex && $part->nodeValue && preg_match($regex, $part->nodeValue)) {
                continue;
            }
            if ($skipCallable($part, $context)) {
                continue;
            }
            $content .= $part->nodeValue . "\n\n";
        }

        return trim($content);
    }

    protected function getArticleCrawler(string $url): Crawler
    {
        return $this->browser->request(Request::METHOD_GET, $url, server: [
            'HTTP_USER_AGENT' => $this->getUserAgent(),
        ]);
    }

    protected function ignoreLast(): int
    {
        return 0;
    }

    protected function getUserAgent(): string
    {
        return 'LemmyAutoTldrBot';
    }

    protected function skipIfMatches(): ?string
    {
        return null;
    }

    /**
     * @return (callable(DOMNode $node, array<string, mixed> $context): bool)|null
     */
    protected function skipIf(): ?callable
    {
        return null;
    }

    /**
     * @return (callable(DOMNode $node, array<string, mixed> $context): bool)|null
     */
    protected function breakIf(): ?callable
    {
        return null;
    }
}
