<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class CtvNewsSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.ctvnews.ca'];
    }

    protected function getSelector(): string
    {
        return '[role=contentinfo] .c-text > p';
    }
}
