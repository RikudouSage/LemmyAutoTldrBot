<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class TheHillSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['thehill.com'];
    }

    protected function getSelector(): string
    {
        return '.article__text p';
    }
}
