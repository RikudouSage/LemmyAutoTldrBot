<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class NbcNewsSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.nbcnews.com'];
    }

    protected function getSelector(): string
    {
        return '.article-body__content > p';
    }
}
