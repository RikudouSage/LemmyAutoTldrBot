<?php

namespace App\SiteHandler;

use DOMElement;
use DOMNode;

final readonly class TexasTribuneSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.texastribune.org'];
    }

    protected function getSelector(): string
    {
        return '.c-story-body > p, .c-story-body > hr';
    }

    protected function skipIf(): ?callable
    {
        return static function (DOMNode $node, array &$context): bool {
            if ($node instanceof DOMElement && $node->tagName === 'hr' && !isset($context['afterFirstHr'])) {
                $context['afterFirstHr'] = true;

                return true;
            }
            if (!isset($context['afterFirstHr']) || isset($context['afterSecondHr'])) {
                return true;
            }
            if ($node instanceof DOMElement && $node->tagName === 'hr') {
                $context['afterSecondHr'] = true;

                return true;
            }

            return false;
        };
    }
}
