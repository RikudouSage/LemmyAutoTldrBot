<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class FortuneSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['fortune.com', 'www.fortune.com'];
    }

    protected function getSelector(): string
    {
        return '[data-cy="articleContent"] p';
    }
}
