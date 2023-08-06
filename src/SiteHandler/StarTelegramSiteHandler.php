<?php

namespace App\SiteHandler;

final readonly class StarTelegramSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.star-telegram.com'];
    }

    protected function getSelector(): string
    {
        return '.story-body > p';
    }

    protected function getUserAgent(): string
    {
        return 'Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0';
    }
}
