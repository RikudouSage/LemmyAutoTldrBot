<?php

namespace App\SiteHandler;

use DOMElement;
use DOMNode;
use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class FuturismSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['futurism.com'];
    }

    protected function getSelector(): string
    {
        return '.post-content > p';
    }

    protected function skipIf(): ?callable
    {
        return static fn (DOMNode $node): bool => $node instanceof DOMElement
            && $node->tagName === 'p'
            && isset($node->attributes['class']);
    }
}
