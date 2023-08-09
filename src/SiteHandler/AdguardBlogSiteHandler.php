<?php

namespace App\SiteHandler;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class AdguardBlogSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['adguard.com'];
    }

    public function supports(string $url): bool
    {
        return parent::supports($url)
            && str_starts_with(parse_url($url, PHP_URL_PATH) ?: '', '/en/blog/');
    }

    protected function getSelector(): string
    {
        return '.content-block__text > p, .content-block__text > ul  > li';
    }
}
