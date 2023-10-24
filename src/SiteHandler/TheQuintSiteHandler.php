<?php

namespace App\SiteHandler;

use DOMNode;
use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class TheQuintSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.thequint.com'];
    }

    protected function getSelector(): string
    {
        return '.story-element p';
    }

    protected function skipIf(): ?callable
    {
        return static function (DOMNode $node): bool {
            if (!$node->nodeValue) {
                return false;
            }

            return str_starts_with($node->nodeValue, '(The writer is a') || str_starts_with($node->nodeValue, '(At The Quint');
        };
    }
}
