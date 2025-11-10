<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UrlRedirected;
use App\Services\CacheService;

class CacheUrlLookup
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function handle(UrlRedirected $event): void
    {
        $code = $event->url->shortened_url ?? null;
        if ($code !== null) {
            $this->cacheService->putUrl($code, $event->url);
        }
    }
}
