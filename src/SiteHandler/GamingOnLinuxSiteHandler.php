<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class GamingOnLinuxSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.gamingonlinux.com'];
    }

    protected function getSelector(): string
    {
        return '.content p, .content li';
    }
}
