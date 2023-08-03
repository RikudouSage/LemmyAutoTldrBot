<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class ArsTechnicaSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['arstechnica.com'];
    }

    protected function getSelector(): string
    {
        return '.article-content p';
    }
}
