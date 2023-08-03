<?php

namespace App\SiteHandler;

final readonly class GamerkickSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['gamerkick.com'];
    }

    protected function getSelector(): string
    {
        return '#rs-main-content h4, #rs-main-content p';
    }
}
