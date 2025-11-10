<?php

declare(strict_types=1);

namespace App\Constants;

final class UrlConstants
{
    public const MIN_CODE_LENGTH = 4;
    public const MAX_CODE_LENGTH = 6;
    public const MAX_MINUTES_PER_YEAR = 525960;
    public const MAX_COLLISION_RETRIES = 10;

    private function __construct()
    {
        // prevent instantiation
    }
}
