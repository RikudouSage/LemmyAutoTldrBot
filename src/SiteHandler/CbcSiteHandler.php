<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class CbcSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.cbc.ca'];
    }

    protected function getSelector(): string
    {
        return '.story > p';
    }

    protected function getUserAgent(): string
    {
        return 'Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0';
    }
}
