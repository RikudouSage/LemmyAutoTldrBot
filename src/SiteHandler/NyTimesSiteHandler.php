<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class NyTimesSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.nytimes.com'];
    }

    protected function getSelector(): string
    {
        return 'section[name=articleBody] p';
    }
}
