<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;
use Rikudou\MemoizeBundle\Attribute\NoMemoize;

#[Memoizable]
#[Memoize]
final readonly class DwSiteHandler extends AbstractSiteHandler
{
    #[NoMemoize]
    public function supports(string $url): bool
    {
        if (!parent::supports($url)) {
            return false;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) {
            return false;
        }

        return str_starts_with($path, '/en/');
    }

    protected function getHostnames(): array
    {
        return ['www.dw.com'];
    }

    protected function getSelector(): string
    {
        return '.rich-text > p';
    }
}
