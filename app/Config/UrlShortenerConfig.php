<?php

declare(strict_types=1);

namespace App\Config;

use App\Constants\UrlConstants;

final class UrlShortenerConfig
{
    public static function getMaxRetries(): int
    {
        return (int) config('urlshortener.max_retries', UrlConstants::MAX_COLLISION_RETRIES);
    }

    public static function getMinCodeLength(): int
    {
        return (int) config('urlshortener.min_code_length', UrlConstants::MIN_CODE_LENGTH);
    }

    public static function getMaxCodeLength(): int
    {
        return (int) config('urlshortener.max_code_length', UrlConstants::MAX_CODE_LENGTH);
    }

    private function __construct()
    {
        // static helper
    }
}
