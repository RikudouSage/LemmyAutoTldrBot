<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class KsatSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.ksat.com'];
    }

    protected function getSelector(): string
    {
        return '.articleBody > p, .articleBody li';
    }
}
