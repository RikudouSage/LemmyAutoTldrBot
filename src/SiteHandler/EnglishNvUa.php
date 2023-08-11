<?php

namespace App\SiteHandler;

use DOMNode;
use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class EnglishNvUa extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['english.nv.ua'];
    }

    protected function getSelector(): string
    {
        return '.article-content-body .content_wrapper > p, .subtitle > p';
    }

    protected function breakIf(): ?callable
    {
        return static fn (DOMNode $node) => isset($node->attributes['style']);
    }
}
