<?php

namespace App\SiteHandler;

use DOMNode;
use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;

#[Memoizable]
#[Memoize]
final readonly class BalkanInsightSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['balkaninsight.com'];
    }

    protected function getSelector(): string
    {
        return '.post_teaser > p, .btArticleBody .btText > p';
    }

    protected function skipIf(): ?callable
    {
        return static function (DOMNode $node): bool {
            $children = array_map(static fn (DOMNode $childNode) => $childNode->nodeName, [...$node->childNodes]);

            return in_array('img', $children, true);
        };
    }
}
