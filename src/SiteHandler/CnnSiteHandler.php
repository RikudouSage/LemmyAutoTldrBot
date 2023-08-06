<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class CnnSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.cnn.com'];
    }

    protected function getSelector(): string
    {
        return '.article__content > p:not(.footnote)';
    }
}
