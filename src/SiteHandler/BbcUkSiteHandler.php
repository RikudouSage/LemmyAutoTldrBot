<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class BbcUkSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.bbc.co.uk'];
    }

    protected function getSelector(): string
    {
        return 'section.body-content';
    }
}
