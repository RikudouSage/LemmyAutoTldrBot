<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class AbcAustraliaSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.abc.net.au'];
    }

    protected function getSelector(): string
    {
        return 'div[class*="Article_body"]  > div > div > p';
    }
}
