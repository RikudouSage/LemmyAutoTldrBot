<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class PhoronixSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.phoronix.com'];
    }

    protected function getSelector(): string
    {
        return '.content';
    }
}
