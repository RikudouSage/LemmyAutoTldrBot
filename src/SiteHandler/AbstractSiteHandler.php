<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpFoundation\Request;

#[Memoizable]
#[Memoize]
abstract readonly class AbstractSiteHandler implements SiteHandler
{
    public function __construct(
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

    public function getContent(string $url): string
    {
        $crawler = $this->browser->request(Request::METHOD_GET, $url);
        $parts = $crawler->filter($this->getSelector());
        $content = '';

        $count = count($parts);
        $i = 0;
        $ignoreLast = $this->ignoreLast();

        foreach ($parts as $part) {
            if ($i === $count - $ignoreLast) {
                break;
            }
            ++$i;
            $content .= $part->nodeValue . "\n\n";
        }

        return trim($content);
    }

    protected function ignoreLast(): int
    {
        return 0;
    }
}
