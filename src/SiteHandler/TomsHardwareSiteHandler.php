<?php

namespace App\SiteHandler;

final readonly class TomsHardwareSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.tomshardware.com'];
    }

    public function supports(string $url): bool
    {
        return parent::supports($url)
            && str_starts_with(parse_url($url, PHP_URL_PATH) ?: '', '/news/');
    }

    protected function getSelector(): string
    {
        return '#article-body > p';
    }
}
