<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class TheRegisterSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.theregister.com'];
    }

    protected function getSelector(): string
    {
        return '#body > p';
    }
}
