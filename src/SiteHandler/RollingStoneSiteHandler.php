<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class RollingStoneSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.rollingstone.com'];
    }

    protected function getSelector(): string
    {
        return '.paragraph';
    }
}
