<?php

namespace App\SiteHandler;

final readonly class AxiosSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.axios.com'];
    }

    protected function getSelector(): string
    {
        return '#main-content p, #main-content li';
    }
}
