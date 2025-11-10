<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

class UrlShorteningIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_url_shortening_flow(): void
    {
        // create payload
        $original = 'https://example.com/path?'.Str::random(6);
        $payload = ['url' => $original];

        // call API (assumes route /api/shorten -> returns {code})
        $resp = $this->postJson('/api/shorten', $payload);
        $resp->assertStatus(201);
        $code = $resp->json('data.code');
        $this->assertIsString($code);

        // DB contains record
        $this->assertDatabaseHas('urls', [
            'shortened_url' => $code,
            'original_url' => $original,
        ]);

        // cache primed (14-day key)
        $cached = Cache::get("url:{$code}");
        $this->assertNotNull($cached);
        $this->assertInstanceOf(Url::class, $cached);
        $this->assertEquals($original, $cached->original_url);

        // perform redirect
        $redirectResp = $this->get('/'.$code);
        $redirectResp->assertStatus(302);
        $this->assertStringStartsWith($original, $redirectResp->headers->get('Location'));

        // analytics recorded (privacy-friendly)
        $this->assertDatabaseHas('url_analytics', [
            'url_id' => $cached->id,
        ]);
    }
}
