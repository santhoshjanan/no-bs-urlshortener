<?php

namespace Tests\Feature;

use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UrlShortenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('No BS URL Shortener');
    }

    public function test_api_shortener_creates_shortened_url(): void
    {
        $response = $this->postJson('/api/shorten', [
            'original_url' => 'https://example.com'
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'original_url',
            'shortened_url'
        ]);

        $this->assertDatabaseHas('urls', [
            'original_url' => 'https://example.com'
        ]);
    }

    public function test_api_shortener_requires_url(): void
    {
        $response = $this->postJson('/api/shorten', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['original_url']);
    }

    public function test_api_shortener_validates_url_format(): void
    {
        $response = $this->postJson('/api/shorten', [
            'original_url' => 'not-a-valid-url'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['original_url']);
    }

    public function test_api_shortener_blocks_non_http_protocols(): void
    {
        $response = $this->postJson('/api/shorten', [
            'original_url' => 'javascript:alert(1)'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['original_url']);
    }

    public function test_redirect_works_for_valid_shortened_url(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'test123'
        ]);

        $response = $this->get('/test123');

        $response->assertRedirect('https://example.com');
    }

    public function test_redirect_increments_click_count(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'test456',
            'clicks' => 0
        ]);

        $this->get('/test456');

        $this->assertDatabaseHas('urls', [
            'shortened_url' => 'test456',
            'clicks' => 1
        ]);
    }

    public function test_redirect_returns_404_for_invalid_shortened_url(): void
    {
        $response = $this->get('/nonexistent');

        $response->assertStatus(404);
    }

    public function test_redirect_caches_url_lookup(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'cached123'
        ]);

        // First request - should cache
        $this->get('/cached123');

        // Check that cache was set
        $this->assertNotNull(Cache::get('shortened_url:cached123'));
    }

    public function test_collision_handling_generates_unique_shortened_url(): void
    {
        // Create a URL with a specific shortened code
        Url::create([
            'original_url' => 'https://first.com',
            'shortened_url' => 'abcd'
        ]);

        // Mock to force collision by checking if we get different codes
        $response = $this->postJson('/api/shorten', [
            'original_url' => 'https://second.com'
        ]);

        $response->assertStatus(201);

        // The second URL should get a different shortened code
        $this->assertEquals(2, Url::count());
        $urls = Url::all();
        $this->assertNotEquals($urls[0]->shortened_url, $urls[1]->shortened_url);
    }

    public function test_rate_limiting_blocks_excessive_requests(): void
    {
        // Make 11 requests (limit is 10 per minute)
        for ($i = 0; $i < 11; $i++) {
            $response = $this->postJson('/api/shorten', [
                'original_url' => 'https://example.com'
            ]);

            if ($i < 10) {
                $response->assertStatus(201);
            } else {
                $response->assertStatus(429); // Too Many Requests
            }
        }
    }

    public function test_analytics_are_tracked_on_redirect(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'analytics123'
        ]);

        $this->withHeaders([
            'Referer' => 'https://referrer.com/page'
        ])->get('/analytics123');

        $url->refresh();

        $this->assertNotNull($url->analytics);
        $this->assertIsArray($url->analytics);
        $this->assertCount(1, $url->analytics);
        $this->assertEquals('referrer.com', $url->analytics[0]['referer_domain']);
    }
}
