<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class BbcComHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.bbc.com'];
    }

    protected function getSelector(): string
    {
        return '[data-component="text-block"]';
    }
}
