<?php

declare(strict_types=1);

namespace App\Constants;

final class AnalyticsConstants
{
    // truncate user agent to this many chars for privacy
    public const USER_AGENT_TRUNCATE_LENGTH = 100;

    // analytics retention in days
    public const RETENTION_DAYS = 365;

    private function __construct()
    {
        // prevent instantiation
    }
}
