<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class BoingBoingSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['boingboing.net'];
    }

    protected function getSelector(): string
    {
        return '.entry-content p';
    }
}
