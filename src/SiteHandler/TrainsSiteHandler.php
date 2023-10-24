<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class TrainsSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.trains.com'];
    }

    protected function getSelector(): string
    {
        return '.entry-content p';
    }
}
