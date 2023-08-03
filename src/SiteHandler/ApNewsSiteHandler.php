<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class ApNewsSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['apnews.com', 'www.apnews.com'];
    }

    protected function getSelector(): string
    {
        return '.RichTextBody > p';
    }
}
