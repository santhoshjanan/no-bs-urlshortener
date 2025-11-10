<?php

declare(strict_types=1);

namespace Tests\Utilities;

class TestDataBuilder
{
    public static function shortenPayload(string $url): array
    {
        return ['url' => $url];
    }
}
