<?php

namespace App\SiteHandler;

final readonly class NprSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.npr.org'];
    }

    protected function getSelector(): string
    {
        return '#storytext > p';
    }
}
