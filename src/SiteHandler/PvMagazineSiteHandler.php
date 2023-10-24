<?php

namespace App\SiteHandler;

use DOMNode;
use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class PvMagazineSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.pv-magazine.com'];
    }

    protected function getSelector(): string
    {
        return '.entry-content p';
    }

    protected function skipIf(): ?callable
    {
        return static function (DOMNode $node): bool {
            if (!$node->parentNode) {
                return false;
            }
            if (!isset($node->parentNode->attributes['class'])) {
                return false;
            }

            return $node->parentNode->attributes['class']->value === 'disclaimer';
        };
    }
}
