<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class NewsweekSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.newsweek.com'];
    }

    protected function getSelector(): string
    {
        return '.article-body > p';
    }
}
