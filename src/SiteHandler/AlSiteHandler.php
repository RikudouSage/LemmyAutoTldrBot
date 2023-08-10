<?php

namespace App\SiteHandler;

use DOMNode;
use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class AlSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.al.com'];
    }

    protected function getSelector(): string
    {
        return '.entry-content > p';
    }

    protected function breakIf(): ?callable
    {
        return static fn (DOMNode $node) => str_starts_with($node->nodeValue ?? '', 'Related:');
    }
}
