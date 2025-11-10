<?php

declare(strict_types=1);

namespace Tests\Integration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Utilities\UrlTestTrait;

class AnalyticsIntegrationTest extends TestCase
{
    use RefreshDatabase;
    use UrlTestTrait;

    public function test_analytics_recorded_on_redirect(): void
    {
        $url = $this->createTestUrl([
            'original_url' => 'https://analytics.example/test',
            'shortened_url' => 'analytics123',
        ]);

        $resp = $this->get('/'.$url->shortened_url);
        $resp->assertStatus(302);

        $this->assertDatabaseHas('url_analytics', [
            'url_id' => $url->id,
        ]);
    }
}
