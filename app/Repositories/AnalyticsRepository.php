<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class AnalyticsRepository
{
    public function store(array $data): void
    {
        DB::table('url_analytics')->insert([
            'url_id' => $data['url_id'],
            'referer_domain' => $data['referer_domain'],
            'user_agent_family' => $data['user_agent_family'],
            'created_at' => $data['created_at'],
        ]);
    }
}
