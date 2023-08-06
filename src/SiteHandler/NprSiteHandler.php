<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class NprSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.npr.org'];
    }

    protected function getSelector(): string
    {
        return '#storytext > p';
    }

    protected function getUserAgent(): string
    {
        return 'Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0';
    }
}
