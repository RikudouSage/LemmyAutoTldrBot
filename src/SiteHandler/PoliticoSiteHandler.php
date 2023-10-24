<?php

namespace App\SiteHandler;

final readonly class PoliticoSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.politico.eu'];
    }

    protected function getSelector(): string
    {
        return '.article__content > p';
    }
}
