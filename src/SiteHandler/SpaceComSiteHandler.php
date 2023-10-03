<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class SpaceComSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.space.com'];
    }

    protected function getSelector(): string
    {
        return '#article-body > p, p.strapline';
    }

    protected function skipIfMatches(): ?string
    {
        return '@^Related: @';
    }
}
