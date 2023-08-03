<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class BlockClubChicagoSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['blockclubchicago.org'];
    }

    protected function getSelector(): string
    {
        return '#pico > p';
    }
}
