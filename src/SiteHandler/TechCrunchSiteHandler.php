<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class TechCrunchSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['techcrunch.com'];
    }

    protected function getSelector(): string
    {
        return '.article-content > p';
    }
}
