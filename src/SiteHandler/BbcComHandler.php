<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class BbcComHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.bbc.com'];
    }

    public function getContent(string $url): string
    {
        if (str_ends_with($url, '.amp')) {
            $url = substr($url, 0, -4);
        }

        return parent::getContent($url);
    }

    protected function getSelector(): string
    {
        return '[data-component="text-block"]';
    }
}
