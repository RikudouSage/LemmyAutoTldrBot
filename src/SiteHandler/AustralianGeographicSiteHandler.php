<?php

namespace App\SiteHandler;

use DOMNode;
use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class AustralianGeographicSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.australiangeographic.com.au'];
    }

    protected function getSelector(): string
    {
        return '.post-content > p';
    }

    protected function skipIf(): ?callable
    {
        return static function (DOMNode $node) {
            return isset($node->attributes['class']) && $node->attributes['class']->value === 'breadcrumbs';
        };
    }
}
