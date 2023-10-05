<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class IndependentCoUkSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.independent.co.uk'];
    }

    protected function getSelector(): string
    {
        return '#main > p';
    }
}
