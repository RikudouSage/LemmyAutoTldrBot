<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class ReutersSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.reuters.com'];
    }

    protected function getSelector(): string
    {
        return '[class^="article-body__content"] > div[class^="text__text"]';
    }

    protected function ignoreLast(): int
    {
        return 1;
    }

    protected function getUserAgent(): string
    {
        return 'Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0';
    }
}
