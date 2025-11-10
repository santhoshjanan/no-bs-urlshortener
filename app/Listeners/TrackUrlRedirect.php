<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UrlRedirected;
use App\Services\AnalyticsService;

class TrackUrlRedirect
{
    private AnalyticsService $analytics;

    public function __construct(AnalyticsService $analytics)
    {
        $this->analytics = $analytics;
    }

    public function handle(UrlRedirected $event): void
    {
        // derive privacy-friendly meta
        $referer = $event->meta['referer'] ?? null;
        $refererDomain = null;
        if ($referer !== null) {
            $host = parse_url((string) $referer, PHP_URL_HOST);
            $refererDomain = $host ?: null;
        }

        $userAgentFamily = $event->meta['ua_family'] ?? null;

        $this->analytics->recordRedirect($event->url, [
            'referer_domain' => $refererDomain,
            'user_agent_family' => $userAgentFamily,
        ]);
    }
}
