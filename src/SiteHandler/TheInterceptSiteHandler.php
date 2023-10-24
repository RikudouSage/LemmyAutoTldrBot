<?php

namespace App\SiteHandler;

final readonly class TheInterceptSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['theintercept.com'];
    }

    protected function getSelector(): string
    {
        return '.entry-content__content > p';
    }
}
