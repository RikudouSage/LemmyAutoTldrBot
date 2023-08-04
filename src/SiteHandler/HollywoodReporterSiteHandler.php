<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class HollywoodReporterSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.hollywoodreporter.com'];
    }

    protected function getSelector(): string
    {
        return '.a-content > p';
    }
}
