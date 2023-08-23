<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class MagazineScienceForThePeople extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['magazine.scienceforthepeople.org'];
    }

    protected function getSelector(): string
    {
        return '.herald-entry-content > p';
    }
}
