<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class MichaelWestSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['michaelwest.com.au'];
    }

    protected function getSelector(): string
    {
        return '#old-post > p';
    }
}
