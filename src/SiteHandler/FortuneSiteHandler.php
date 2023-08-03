<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class FortuneSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['fortune.com', 'www.fortune.com'];
    }

    protected function getSelector(): string
    {
        return '[data-cy="articleContent"] p';
    }

    public function getContent(string $url): string
    {
        if (str_ends_with($url, '/amp/')) {
            $url = substr($url, 0, -strlen('/amp/'));
        }
        if (str_ends_with($url, '/amp')) {
            $url = substr($url, 0, -strlen('/amp'));
        }

        return parent::getContent($url);
    }
}
