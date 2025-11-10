<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Url;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    private const URL_TTL_DAYS = 14;

    public function putUrl(string $code, Url $url): void
    {
        $key = $this->urlCacheKey($code);
        $ttl = now()->addDays(self::URL_TTL_DAYS);
        Cache::put($key, $url, $ttl);
    }

    public function getUrl(string $code): ?Url
    {
        return Cache::get($this->urlCacheKey($code));
    }

    private function urlCacheKey(string $code): string
    {
        return "url:{$code}";
    }
}
