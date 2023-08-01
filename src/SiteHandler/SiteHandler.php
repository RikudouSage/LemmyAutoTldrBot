<?php

namespace App\SiteHandler;

use App\Exception\ContentFetchingFailedException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.site_handler')]
interface SiteHandler
{
    public function supports(string $url): bool;

    /**
     * @throws ContentFetchingFailedException
     */
    public function getContent(string $url): string;
}
