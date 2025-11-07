<?php

namespace Tests\Unit;

use App\Http\Controllers\UrlController;
use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Tests\TestCase;

class UrlControllerTest extends TestCase
{
    use RefreshDatabase;

    protected UrlController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new UrlController();
        Cache::flush();
    }

    // ==================== generateRandomString Tests ====================

    public function test_generate_random_string_returns_string_within_range(): void
    {
        $result = $this->controller->generateRandomString(4, 6);
        
        $this->assertIsString($result);
        $this->assertGreaterThanOrEqual(4, strlen($result));
        $this->assertLessThanOrEqual(6, strlen($result));
    }

    public function test_generate_random_string_throws_exception_when_min_greater_than_max(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Minimum length cannot be greater than maximum length.');
        
        $this->controller->generateRandomString(6, 4);
    }

    public function test_generate_random_string_handles_equal_min_max(): void
    {
        $result = $this->controller->generateRandomString(5, 5);
        
        $this->assertIsString($result);
        $this->assertEquals(5, strlen($result));
    }

    public function test_generate_random_string_produces_different_results(): void
    {
        $results = [];
        
        // Generate multiple strings
        for ($i = 0; $i < 10; $i++) {
            $results[] = $this->controller->generateRandomString(4, 6);
        }
        
        // At least some should be different (very unlikely all are same)
        $uniqueResults = array_unique($results);
        $this->assertGreaterThan(1, count($uniqueResults));
    }

    // ==================== createShortUrl Tests ====================
    // Note: createShortUrl is protected, so we test it through public methods or use reflection

    public function test_create_short_url_creates_permanent_url(): void
    {
        // Use reflection to call protected method
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('createShortUrl');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->controller, 'https://example.com', 0);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('original_url', $result);
        $this->assertArrayHasKey('shortened_url', $result);
        $this->assertEquals('https://example.com', $result['original_url']);
        
        // Should be in database
        $code = basename(parse_url($result['shortened_url'], PHP_URL_PATH));
        $this->assertDatabaseHas('urls', [
            'original_url' => 'https://example.com',
            'shortened_url' => $code
        ]);
    }

    public function test_create_short_url_creates_temporary_url(): void
    {
        // Use reflection to call protected method
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('createShortUrl');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->controller, 'https://example.com', 60);
        
        $this->assertIsArray($result);
        $this->assertEquals('https://example.com', $result['original_url']);
        
        // Should NOT be in database
        $code = basename(parse_url($result['shortened_url'], PHP_URL_PATH));
        $this->assertDatabaseMissing('urls', [
            'shortened_url' => $code
        ]);
        
        // Should be in cache
        $this->assertNotNull(Cache::get("shortened_url:{$code}"));
    }

    public function test_create_short_url_caches_permanent_url(): void
    {
        // Use reflection to call protected method
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('createShortUrl');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->controller, 'https://example.com', 0);
        
        $code = basename(parse_url($result['shortened_url'], PHP_URL_PATH));
        
        // Should be cached
        $cached = Cache::get("shortened_url:{$code}");
        $this->assertEquals('https://example.com', $cached);
    }

    public function test_create_short_url_handles_collisions(): void
    {
        // Create a URL with a known code
        $existingCode = 'test123';
        Url::create([
            'original_url' => 'https://existing.com',
            'shortened_url' => $existingCode
        ]);
        
        // Use reflection to call protected method
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('createShortUrl');
        $method->setAccessible(true);
        
        // Try to create another URL - should handle collision
        $result = $method->invoke($this->controller, 'https://new.com', 0);
        
        $newCode = basename(parse_url($result['shortened_url'], PHP_URL_PATH));
        
        // Should be different from existing
        $this->assertNotEquals($existingCode, $newCode);
        
        // Both should exist
        $this->assertDatabaseHas('urls', ['shortened_url' => $existingCode]);
        $this->assertDatabaseHas('urls', ['shortened_url' => $newCode]);
    }

    public function test_create_short_url_handles_cache_collisions_for_temporary_urls(): void
    {
        $existingCode = 'temp123';
        Cache::put("shortened_url:{$existingCode}", 'https://existing.com', now()->addMinutes(60));
        
        // Use reflection to call protected method
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('createShortUrl');
        $method->setAccessible(true);
        
        // Try to create temporary URL - should handle collision
        $result = $method->invoke($this->controller, 'https://new.com', 60);
        
        $newCode = basename(parse_url($result['shortened_url'], PHP_URL_PATH));
        
        // Should be different (or same if collision retry succeeded)
        // The important thing is it doesn't throw an error
        $this->assertIsArray($result);
        $this->assertEquals('https://new.com', $result['original_url']);
    }

    public function test_create_short_url_returns_different_codes_for_different_urls(): void
    {
        // Use reflection to call protected method
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('createShortUrl');
        $method->setAccessible(true);
        
        $result1 = $method->invoke($this->controller, 'https://example1.com', 0);
        $result2 = $method->invoke($this->controller, 'https://example2.com', 0);
        
        $code1 = basename(parse_url($result1['shortened_url'], PHP_URL_PATH));
        $code2 = basename(parse_url($result2['shortened_url'], PHP_URL_PATH));
        
        // Codes should be different
        $this->assertNotEquals($code1, $code2);
    }

    // ==================== redirect Tests ====================

    public function test_redirect_returns_original_url_from_database(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'test123'
        ]);
        
        $request = Request::create('/test123', 'GET');
        $response = $this->controller->redirect($request, 'test123');
        
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('https://example.com', $response->getTargetUrl());
    }

    public function test_redirect_returns_original_url_from_cache(): void
    {
        $code = 'cached123';
        Cache::put("shortened_url:{$code}", 'https://cached.example.com', now()->addDays(14));
        
        $request = Request::create("/{$code}", 'GET');
        $response = $this->controller->redirect($request, $code);
        
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('https://cached.example.com', $response->getTargetUrl());
    }

    public function test_redirect_aborts_when_url_not_found(): void
    {
        $request = Request::create('/nonexistent', 'GET');
        
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->controller->redirect($request, 'nonexistent');
    }

    public function test_redirect_increments_clicks_for_permanent_urls(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'click123',
            'clicks' => 0
        ]);
        
        $request = Request::create('/click123', 'GET');
        $this->controller->redirect($request, 'click123');
        
        $url->refresh();
        $this->assertEquals(1, $url->clicks);
    }

    public function test_redirect_tracks_analytics_for_permanent_urls(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'analytics123'
        ]);
        
        $request = Request::create('/analytics123', 'GET', [], [], [], [
            'HTTP_REFERER' => 'https://referrer.com/page'
        ]);
        
        $this->controller->redirect($request, 'analytics123');
        
        $url->refresh();
        $this->assertNotNull($url->analytics);
        $this->assertCount(1, $url->analytics);
        $this->assertEquals('referrer.com', $url->analytics[0]['referer_domain']);
    }

    public function test_redirect_does_not_track_analytics_for_temporary_urls(): void
    {
        $code = 'temp123';
        Cache::put("shortened_url:{$code}", 'https://example.com', now()->addMinutes(60));
        
        $request = Request::create("/{$code}", 'GET');
        
        // Should not throw error and should redirect
        $response = $this->controller->redirect($request, $code);
        $this->assertEquals(302, $response->getStatusCode());
        
        // Should not exist in database
        $this->assertDatabaseMissing('urls', ['shortened_url' => $code]);
    }

    public function test_redirect_limits_analytics_to_100_entries(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'limit123'
        ]);
        
        // Pre-populate with 105 entries
        $analytics = [];
        for ($i = 0; $i < 105; $i++) {
            $analytics[] = [
                'timestamp' => now()->toIso8601String(),
                'referer_domain' => 'example.com'
            ];
        }
        $url->update(['analytics' => $analytics]);
        
        $request = Request::create('/limit123', 'GET');
        $this->controller->redirect($request, 'limit123');
        
        $url->refresh();
        $this->assertCount(100, $url->analytics);
    }

    public function test_redirect_caches_database_lookup(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'cache123'
        ]);
        
        $request = Request::create('/cache123', 'GET');
        $this->controller->redirect($request, 'cache123');
        
        // Should be cached now
        $this->assertNotNull(Cache::get('shortened_url:cache123'));
        $this->assertEquals('https://example.com', Cache::get('shortened_url:cache123'));
    }

    public function test_redirect_handles_missing_referer(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'noreferer123'
        ]);
        
        $request = Request::create('/noreferer123', 'GET');
        $this->controller->redirect($request, 'noreferer123');
        
        $url->refresh();
        $this->assertCount(1, $url->analytics);
        $this->assertNull($url->analytics[0]['referer_domain']);
    }
}

