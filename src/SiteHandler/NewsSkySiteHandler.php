<?php

namespace App\SiteHandler;

final readonly class NewsSkySiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['news.sky.com'];
    }

    protected function getSelector(): string
    {
        return '.sdc-article-body > p';
    }

    protected function skipIfMatches(): ?string
    {
        return '@^\s*Read more: @';
    }
}
