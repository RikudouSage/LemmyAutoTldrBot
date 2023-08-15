<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class BusinessInsiderSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.businessinsider.com'];
    }

    protected function getSelector(): string
    {
        return '.post-content p';
    }
}
