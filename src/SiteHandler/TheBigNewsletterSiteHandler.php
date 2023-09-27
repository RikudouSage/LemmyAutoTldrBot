<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class TheBigNewsletterSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.thebignewsletter.com'];
    }

    protected function getSelector(): string
    {
        return '.body.markup > p, .body.markup ul li > p';
    }

    protected function skipIfMatches(): ?string
    {
        return '@^Welcome to BIG@';
    }
}
