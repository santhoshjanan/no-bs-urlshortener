<?php

declare(strict_types=1);

namespace Tests\Performance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Url;

class UrlRedirectPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirect_performance_under_100ms(): void
    {
        $url = Url::factory()->create([
            'original_url' => 'https://perf.example/',
            'shortened_url' => 'perf123',
        ]);

        $start = microtime(true);
        $resp = $this->get('/' . $url->shortened_url);
        $duration = microtime(true) - $start;

        $resp->assertStatus(302);
        $this->assertLessThan(0.1, $duration, 'Redirect duration should be under 100ms');
    }
}
