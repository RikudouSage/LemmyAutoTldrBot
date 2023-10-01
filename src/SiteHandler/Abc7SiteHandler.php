<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class Abc7SiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['abc7.com'];
    }

    protected function getSelector(): string
    {
        return 'article > p';
    }

    protected function skipIfMatches(): ?string
    {
        return '@^MORE: @';
    }
}
