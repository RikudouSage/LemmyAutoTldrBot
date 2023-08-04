<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class ScienceDailySiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.sciencedaily.com'];
    }

    protected function getSelector(): string
    {
        return '#text > p';
    }
}
