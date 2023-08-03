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
        return '[class^="article-body__content"] > p';
    }

    protected function ignoreLast(): int
    {
        return 1;
    }
}
