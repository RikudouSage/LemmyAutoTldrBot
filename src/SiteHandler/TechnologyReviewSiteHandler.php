<?php

namespace App\SiteHandler;

use DOMNode;
use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class TechnologyReviewSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.technologyreview.com'];
    }

    protected function getSelector(): string
    {
        return '#content--body p';
    }

    protected function skipIf(): ?callable
    {
        return static function (DOMNode $node): bool {
            if (!isset($node->attributes['class'])) {
                return false;
            }

            return $node->attributes['class']->value === 'imageSet__caption';
        };
    }
}
