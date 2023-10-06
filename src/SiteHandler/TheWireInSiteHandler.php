<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class TheWireInSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['thewire.in'];
    }

    protected function getSelector(): string
    {
        return '.grey-text';
    }
}
