<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class BbcUkNewsSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.bbc.co.uk'];
    }

    public function supports(string $url): bool
    {
        return parent::supports($url)
            && str_starts_with(parse_url($url, PHP_URL_PATH) ?: '', '/news');
    }

    protected function getSelector(): string
    {
        return '[data-component="text-block"]';
    }
}
