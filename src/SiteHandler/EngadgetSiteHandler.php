<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class EngadgetSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.engadget.com'];
    }

    protected function getSelector(): string
    {
        return '.article-text > p';
    }
}
