<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class EuronewsSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.euronews.com'];
    }

    protected function getSelector(): string
    {
        return '.o-article-newsy__main__body .c-article-summary, .c-article-content > p';
    }
}
