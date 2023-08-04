<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class GizmodoSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['gizmodo.com'];
    }

    protected function getSelector(): string
    {
        return '.js_post-content > div > p';
    }
}
