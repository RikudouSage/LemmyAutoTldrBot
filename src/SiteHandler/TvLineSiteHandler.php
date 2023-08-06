<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class TvLineSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['tvline.com'];
    }

    protected function getSelector(): string
    {
        return '[data-alias=gutenberg-content__content] > p';
    }
}
