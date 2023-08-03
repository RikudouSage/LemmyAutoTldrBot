<?php

namespace App\SiteHandler;

use DOMElement;
use Symfony\Component\HttpFoundation\Request;

final readonly class DallasObserverSiteHandler extends AbstractSiteHandler
{
    protected function getHostnames(): array
    {
        return ['www.dallasobserver.com'];
    }

    protected function getSelector(): string
    {
        return '.fdn-content-body';
    }

    public function getContent(string $url): string
    {
        $crawler = $this->browser->request(Request::METHOD_GET, $url, [
            'HTTP_USER_AGENT' => $this->getUserAgent(),
        ]);
        $content = $crawler->filter($this->getSelector())->getNode(0);
        $result = '';
        assert($content instanceof DOMElement);
        foreach ($content->childNodes as $childNode) {
            if ($childNode instanceof DOMElement && $childNode->tagName === 'div') {
                continue;
            }
            if ($childNode instanceof DOMElement && $childNode->tagName === 'br') {
                $result .= "\n";
            }
            $result .= $childNode->textContent;
        }

        return trim($result);
    }
}
