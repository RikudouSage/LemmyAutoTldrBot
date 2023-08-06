<?php

namespace App\SiteHandler;

use DOMElement;
use DOMNode;
use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class VancouverSunSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['vancouversun.com'];
    }

    protected function getSelector(): string
    {
        return '.article-content__content-group > p';
    }

    protected function breakIf(): ?callable
    {
        return static fn (DOMNode $node): bool => $node instanceof DOMElement
            && $node->tagName === 'p'
            && isset($node->attributes['data-async'])
            && filter_var(trim($node->nodeValue ?: ''), FILTER_VALIDATE_EMAIL);
    }
}
