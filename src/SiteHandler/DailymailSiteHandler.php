<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class DailymailSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.dailymail.co.uk'];
    }

    protected function getSelector(): string
    {
        return '[itemprop="articleBody"] > p';
    }
}
