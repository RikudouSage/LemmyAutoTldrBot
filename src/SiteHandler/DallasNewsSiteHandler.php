<?php

namespace App\SiteHandler;

final readonly class DallasNewsSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.dallasnews.com'];
    }

    protected function getSelector(): string
    {
        return '.body-text-paragraph';
    }
}
