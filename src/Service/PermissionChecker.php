<?php

namespace App\Service;

use Rikudou\LemmyApi\Response\Model\Community;

interface PermissionChecker
{
    public function canPostToCommunity(Community $community): bool;
}
