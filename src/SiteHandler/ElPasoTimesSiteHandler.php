<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class ElPasoTimesSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.elpasotimes.com', 'eu.elpasotimes.com'];
    }

    protected function getSelector(): string
    {
        return '.primary-content > p';
    }

    protected function skipIfMatches(): ?string
    {
        return '@^More: @';
    }
}
