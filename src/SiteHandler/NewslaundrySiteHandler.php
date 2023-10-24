<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class NewslaundrySiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.newslaundry.com'];
    }

    protected function getSelector(): string
    {
        return '.story-element';
    }
}
