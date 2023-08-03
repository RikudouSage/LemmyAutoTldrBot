<?php

namespace App\SiteHandler;

final readonly class TheGlobeAndMailSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.theglobeandmail.com'];
    }

    protected function getSelector(): string
    {
        return '.c-article-body__text';
    }
}
