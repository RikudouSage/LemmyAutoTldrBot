<?php

namespace App\SiteHandler;

use DOMAttr;
use DOMElement;
use DOMNode;

final readonly class TheBulwarkSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['.thebulwark.com'];
    }

    public function supports(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) {
            return false;
        }

        foreach ($this->getHostnames() as $hostname) {
            if (str_ends_with($host, $hostname)) {
                return true;
            }
        }

        return false;
    }

    protected function getSelector(): string
    {
        return '.body p';
    }

    protected function breakIf(): ?callable
    {
        return static function (DOMNode $node): bool {
            if (!$node instanceof DOMElement) {
                return false;
            }

            if ($node->tagName !== 'p') {
                return false;
            }

            if (!isset($node->attributes['class'])) {
                return false;
            }

            $class = $node->attributes['class'];
            assert($class instanceof DOMAttr);

            return str_contains($class->value, 'button-wrapper');
        };
    }
}
