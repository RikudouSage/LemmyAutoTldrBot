<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class TheVergeSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.theverge.com'];
    }

    protected function getSelector(): string
    {
        return '.duet--article--article-body-component > p';
    }
}
