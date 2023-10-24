<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class AxiomSpaceSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.axiomspace.com'];
    }

    public function supports(string $url): bool
    {
        if (!parent::supports($url)) {
            return false;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (!is_string($path)) {
            return false;
        }

        return str_starts_with($path, '/news');
    }

    protected function getSelector(): string
    {
        return '.sqs-html-content > p';
    }
}
