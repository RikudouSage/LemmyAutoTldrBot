<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class TheGuardianSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.theguardian.com'];
    }

    protected function getSelector(): string
    {
        return '.article-body-viewer-selector > p';
    }
}
