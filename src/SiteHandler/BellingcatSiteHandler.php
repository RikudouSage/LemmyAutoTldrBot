<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class BellingcatSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.bellingcat.com'];
    }

    protected function getSelector(): string
    {
        return '.singular__content__text__content p';
    }

    protected function ignoreLast(): int
    {
        return 1;
    }
}
