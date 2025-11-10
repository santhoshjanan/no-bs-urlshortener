<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RedirectFlowIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_code_returns_404_and_tracks(): void
    {
        $unknown = 'no-such-code-123';

        $resp = $this->get('/' . $unknown);
        $resp->assertStatus(404);

        // Optional: assert analytics entry recorded for 404s if implementation stores them
        $this->assertDatabaseHas('url_analytics', [
            'referer_domain' => null, // implementation may vary; ensure at least a record exists
        ]);
    }
}
