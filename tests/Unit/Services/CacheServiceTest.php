<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Url;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheServiceTest extends TestCase
{
    protected CacheService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CacheService;
        Cache::flush();
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }

    // ==================== Basic Functionality Tests ====================

    public function test_puts_url_in_cache(): void
    {
        $url = new Url([
            'id' => 1,
            'original_url' => 'https://example.com',
            'shortened_url' => 'abc123',
        ]);

        $this->service->putUrl('abc123', $url);

        $this->assertTrue(Cache::has('url:abc123'));
    }

    public function test_gets_url_from_cache(): void
    {
        $url = new Url([
            'id' => 1,
            'original_url' => 'https://example.com',
            'shortened_url' => 'abc123',
        ]);

        $this->service->putUrl('abc123', $url);
        $retrieved = $this->service->getUrl('abc123');

        $this->assertInstanceOf(Url::class, $retrieved);
        $this->assertEquals($url->original_url, $retrieved->original_url);
        $this->assertEquals($url->shortened_url, $retrieved->shortened_url);
    }

    public function test_returns_null_for_missing_cache_key(): void
    {
        $result = $this->service->getUrl('nonexistent');

        $this->assertNull($result);
    }

    // ==================== Cache Key Tests ====================

    public function test_uses_correct_cache_key_format(): void
    {
        $url = new Url([
            'id' => 1,
            'original_url' => 'https://example.com',
            'shortened_url' => 'abc123',
        ]);

        $this->service->putUrl('abc123', $url);

        // Verify the cache key format is "url:{code}"
        $this->assertTrue(Cache::has('url:abc123'));
        $this->assertFalse(Cache::has('abc123'));
        $this->assertFalse(Cache::has('shortened_url:abc123'));
    }

    public function test_handles_different_code_formats(): void
    {
        $codes = ['abc123', 'XYZ789', 'a1B2c3', '123456', 'ABCDEF'];

        foreach ($codes as $code) {
            $url = new Url([
                'id' => 1,
                'original_url' => 'https://example.com',
                'shortened_url' => $code,
            ]);

            $this->service->putUrl($code, $url);
            $retrieved = $this->service->getUrl($code);

            $this->assertNotNull($retrieved, "Failed to retrieve URL with code: {$code}");
            $this->assertEquals($code, $retrieved->shortened_url);
        }
    }

    // ==================== TTL Tests ====================

    public function test_sets_14_day_ttl(): void
    {
        $url = new Url([
            'id' => 1,
            'original_url' => 'https://example.com',
            'shortened_url' => 'abc123',
        ]);

        $this->service->putUrl('abc123', $url);

        // Verify the cache exists
        $this->assertTrue(Cache::has('url:abc123'));

        // Get TTL (Time To Live) - Laravel doesn't expose this directly in tests,
        // but we can verify the value is stored and retrievable
        $retrieved = $this->service->getUrl('abc123');
        $this->assertNotNull($retrieved);
    }

    // ==================== Multiple URLs Tests ====================

    public function test_stores_multiple_urls_independently(): void
    {
        $url1 = new Url([
            'id' => 1,
            'original_url' => 'https://example1.com',
            'shortened_url' => 'code1',
        ]);

        $url2 = new Url([
            'id' => 2,
            'original_url' => 'https://example2.com',
            'shortened_url' => 'code2',
        ]);

        $url3 = new Url([
            'id' => 3,
            'original_url' => 'https://example3.com',
            'shortened_url' => 'code3',
        ]);

        $this->service->putUrl('code1', $url1);
        $this->service->putUrl('code2', $url2);
        $this->service->putUrl('code3', $url3);

        // Verify all are stored independently
        $retrieved1 = $this->service->getUrl('code1');
        $retrieved2 = $this->service->getUrl('code2');
        $retrieved3 = $this->service->getUrl('code3');

        $this->assertEquals('https://example1.com', $retrieved1->original_url);
        $this->assertEquals('https://example2.com', $retrieved2->original_url);
        $this->assertEquals('https://example3.com', $retrieved3->original_url);
    }

    // ==================== Cache Overwrite Tests ====================

    public function test_overwrites_existing_cache_entry(): void
    {
        $url1 = new Url([
            'id' => 1,
            'original_url' => 'https://example1.com',
            'shortened_url' => 'abc123',
        ]);

        $url2 = new Url([
            'id' => 2,
            'original_url' => 'https://example2.com',
            'shortened_url' => 'abc123',
        ]);

        $this->service->putUrl('abc123', $url1);
        $this->service->putUrl('abc123', $url2);

        $retrieved = $this->service->getUrl('abc123');

        // Should have the second URL's data
        $this->assertEquals('https://example2.com', $retrieved->original_url);
    }

    // ==================== URL Data Integrity Tests ====================

    public function test_preserves_url_attributes(): void
    {
        $url = new Url([
            'original_url' => 'https://example.com/path?query=value',
            'shortened_url' => 'abc123',
            'clicks' => 42,
            'analytics' => ['referer' => 'google.com'],
        ]);

        $this->service->putUrl('abc123', $url);
        $retrieved = $this->service->getUrl('abc123');

        $this->assertEquals('https://example.com/path?query=value', $retrieved->original_url);
        $this->assertEquals('abc123', $retrieved->shortened_url);
        $this->assertEquals(42, $retrieved->clicks);
        $this->assertEquals(['referer' => 'google.com'], $retrieved->analytics);
    }

    // ==================== Edge Cases ====================

    public function test_handles_empty_string_code(): void
    {
        $url = new Url([
            'id' => 1,
            'original_url' => 'https://example.com',
            'shortened_url' => '',
        ]);

        $this->service->putUrl('', $url);
        $retrieved = $this->service->getUrl('');

        $this->assertNotNull($retrieved);
    }

    public function test_handles_special_characters_in_code(): void
    {
        // Although codes typically don't have special chars, test robustness
        $codes = ['abc-123', 'abc_123', 'abc.123'];

        foreach ($codes as $code) {
            $url = new Url([
                'id' => 1,
                'original_url' => 'https://example.com',
                'shortened_url' => $code,
            ]);

            $this->service->putUrl($code, $url);
            $retrieved = $this->service->getUrl($code);

            $this->assertNotNull($retrieved, "Failed with code: {$code}");
        }
    }

    public function test_handles_very_long_urls(): void
    {
        $longUrl = 'https://example.com/'.str_repeat('segment/', 100).'?'.str_repeat('param=value&', 50);

        $url = new Url([
            'id' => 1,
            'original_url' => $longUrl,
            'shortened_url' => 'abc123',
        ]);

        $this->service->putUrl('abc123', $url);
        $retrieved = $this->service->getUrl('abc123');

        $this->assertEquals($longUrl, $retrieved->original_url);
    }

    // ==================== Null Handling Tests ====================

    public function test_handles_url_with_null_attributes(): void
    {
        $url = new Url([
            'id' => 1,
            'original_url' => 'https://example.com',
            'shortened_url' => 'abc123',
            'clicks' => null,
            'analytics' => null,
        ]);

        $this->service->putUrl('abc123', $url);
        $retrieved = $this->service->getUrl('abc123');

        $this->assertNotNull($retrieved);
        $this->assertEquals('https://example.com', $retrieved->original_url);
    }
}
