<?php

namespace App\Service;

use Rikudou\LemmyApi\Response\Model\Community;
use RuntimeException;

final readonly class PermissionChecker
{
    /**
     * @param array<string> $instanceBlacklist
     * @param array<string> $instanceWhitelist
     * @param array<string> $communityBlacklist
     * @param array<string> $communityWhitelist
     */
    public function __construct(
        private array $instanceBlacklist,
        private array $instanceWhitelist,
        private array $communityBlacklist,
        private array $communityWhitelist,
    ) {
    }

    public function canPostToCommunity(Community $community): bool
    {
        $communityName = $this->getCommunityName($community);
        $instance = $this->getInstance($community);

        if (in_array($communityName, $this->communityWhitelist, true)) {
            return true;
        }
        if (in_array($communityName, $this->communityBlacklist, true)) {
            return false;
        }
        if (in_array($instance, $this->instanceWhitelist, true)) {
            return true;
        }
        if (in_array($instance, $this->instanceBlacklist, true)) {
            return false;
        }

        return true;
    }

    private function getInstance(Community $community): string
    {
        return parse_url($community->actorId, PHP_URL_HOST)
            ?: throw new RuntimeException('Failed to extract host from community.');
    }

    private function getCommunityName(Community $community): string
    {
        return "{$community->name}@{$this->getInstance($community)}";
    }
}
