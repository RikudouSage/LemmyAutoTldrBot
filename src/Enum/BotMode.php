<?php

namespace App\Enum;

enum BotMode: string
{
    case All = 'all';
    case WhitelistOnly = 'whitelist';
}
