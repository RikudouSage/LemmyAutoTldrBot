<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class VoxSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.vox.com'];
    }

    protected function getSelector(): string
    {
        return '.c-entry-content > p';
    }
}
