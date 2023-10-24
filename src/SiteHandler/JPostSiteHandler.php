<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class JPostSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.jpost.com'];
    }

    protected function getSelector(): string
    {
        return '.article-inner-content > p';
    }
}
