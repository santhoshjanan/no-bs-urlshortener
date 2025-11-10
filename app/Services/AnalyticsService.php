<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Url;
use App\Repositories\AnalyticsRepository;

class AnalyticsService
{
    private AnalyticsRepository $repo;

    public function __construct(AnalyticsRepository $repo)
    {
        $this->repo = $repo;
    }

    public function recordRedirect(Url $url, array $meta = []): void
    {
        // keep analytics privacy-friendly: no IPs, only truncated UA, referer domain
        $payload = [
            'url_id' => $url->id,
            'referer_domain' => $meta['referer_domain'] ?? null,
            'user_agent_family' => $meta['user_agent_family'] ?? null,
            'created_at' => now(),
        ];

        $this->repo->store($payload);
    }
}
