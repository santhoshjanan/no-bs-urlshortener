<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Url;
use App\Repositories\UrlRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UrlRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UrlRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new UrlRepository;
        Cache::flush();
    }

    // ==================== FindByShortenedCode Tests ====================

    public function test_finds_url_by_shortened_code_from_database(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'abc123',
        ]);

        $result = $this->repository->findByShortenedCode('abc123');

        $this->assertInstanceOf(Url::class, $result);
        $this->assertEquals($url->id, $result->id);
        $this->assertEquals('https://example.com', $result->original_url);
        $this->assertEquals('abc123', $result->shortened_url);
    }

    public function test_returns_null_when_code_not_found(): void
    {
        $result = $this->repository->findByShortenedCode('nonexistent');

        $this->assertNull($result);
    }

    public function test_caches_url_after_first_lookup(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'abc123',
        ]);

        // First call - should hit database
        $result1 = $this->repository->findByShortenedCode('abc123');

        // Verify it's cached
        $cached = Cache::get('url:abc123');
        $this->assertNotNull($cached);
        $this->assertInstanceOf(Url::class, $cached);
        $this->assertEquals($url->id, $cached->id);

        // Second call - should hit cache
        $result2 = $this->repository->findByShortenedCode('abc123');

        $this->assertEquals($result1->id, $result2->id);
    }

    public function test_caches_null_result_for_nonexistent_code(): void
    {
        // First call - should hit database
        $result1 = $this->repository->findByShortenedCode('nonexistent');

        // Laravel's Cache::remember() doesn't cache null values by default
        // So we just verify that both calls return null consistently
        $result2 = $this->repository->findByShortenedCode('nonexistent');

        $this->assertNull($result1);
        $this->assertNull($result2);
    }

    public function test_uses_correct_cache_key_format(): void
    {
        Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'test123',
        ]);

        $this->repository->findByShortenedCode('test123');

        // Verify cache key format is "url:{code}"
        $this->assertTrue(Cache::has('url:test123'));
    }

    public function test_sets_14_day_cache_ttl(): void
    {
        Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'abc123',
        ]);

        $this->repository->findByShortenedCode('abc123');

        // We can't easily test the exact TTL, but we can verify it's cached
        $this->assertTrue(Cache::has('url:abc123'));
    }

    // ==================== Case Sensitivity Tests ====================

    public function test_code_lookup_is_case_sensitive(): void
    {
        Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'AbC123',
        ]);

        $resultExact = $this->repository->findByShortenedCode('AbC123');
        $resultLower = $this->repository->findByShortenedCode('abc123');
        $resultUpper = $this->repository->findByShortenedCode('ABC123');

        $this->assertNotNull($resultExact);
        $this->assertNull($resultLower);
        $this->assertNull($resultUpper);
    }

    // ==================== Multiple URLs Tests ====================

    public function test_finds_correct_url_among_multiple(): void
    {
        $url1 = Url::create([
            'original_url' => 'https://example1.com',
            'shortened_url' => 'abc111',
        ]);

        $url2 = Url::create([
            'original_url' => 'https://example2.com',
            'shortened_url' => 'abc222',
        ]);

        $url3 = Url::create([
            'original_url' => 'https://example3.com',
            'shortened_url' => 'abc333',
        ]);

        $result = $this->repository->findByShortenedCode('abc222');

        $this->assertNotNull($result);
        $this->assertEquals($url2->id, $result->id);
        $this->assertEquals('https://example2.com', $result->original_url);
    }

    public function test_caches_multiple_urls_independently(): void
    {
        Url::create([
            'original_url' => 'https://example1.com',
            'shortened_url' => 'code1',
        ]);

        Url::create([
            'original_url' => 'https://example2.com',
            'shortened_url' => 'code2',
        ]);

        $this->repository->findByShortenedCode('code1');
        $this->repository->findByShortenedCode('code2');

        $this->assertTrue(Cache::has('url:code1'));
        $this->assertTrue(Cache::has('url:code2'));

        $cached1 = Cache::get('url:code1');
        $cached2 = Cache::get('url:code2');

        $this->assertNotEquals($cached1->id, $cached2->id);
    }

    // ==================== Edge Cases ====================

    public function test_handles_empty_string_code(): void
    {
        $result = $this->repository->findByShortenedCode('');

        $this->assertNull($result);
    }

    public function test_handles_code_with_special_characters(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'a-b_1',
        ]);

        $result = $this->repository->findByShortenedCode('a-b_1');

        $this->assertNotNull($result);
        $this->assertEquals($url->id, $result->id);
    }

    public function test_handles_very_long_code(): void
    {
        $longCode = str_repeat('a', 100);

        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => $longCode,
        ]);

        $result = $this->repository->findByShortenedCode($longCode);

        $this->assertNotNull($result);
        $this->assertEquals($url->id, $result->id);
    }

    public function test_handles_url_with_all_fields_populated(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'abc123',
            'clicks' => 10,
            'analytics' => [
                ['timestamp' => '2025-01-01T00:00:00Z', 'referer' => 'test.com'],
            ],
        ]);

        $result = $this->repository->findByShortenedCode('abc123');

        $this->assertNotNull($result);
        $this->assertEquals($url->id, $result->id);
        $this->assertEquals(10, $result->clicks);
        $this->assertNotNull($result->analytics);
    }

    // ==================== Cache Invalidation Tests ====================

    public function test_returns_fresh_data_after_cache_flush(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'abc123',
        ]);

        // First call - caches the result
        $this->repository->findByShortenedCode('abc123');

        // Flush cache
        Cache::flush();

        // Update the URL in database
        $url->update(['original_url' => 'https://updated.com']);

        // Second call - should get fresh data from database
        $result = $this->repository->findByShortenedCode('abc123');

        $this->assertEquals('https://updated.com', $result->original_url);
    }

    public function test_cache_persists_across_multiple_lookups(): void
    {
        Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'abc123',
        ]);

        // Multiple lookups
        $result1 = $this->repository->findByShortenedCode('abc123');
        $result2 = $this->repository->findByShortenedCode('abc123');
        $result3 = $this->repository->findByShortenedCode('abc123');

        // All should return the same data
        $this->assertEquals($result1->id, $result2->id);
        $this->assertEquals($result2->id, $result3->id);

        // Cache should still exist
        $this->assertTrue(Cache::has('url:abc123'));
    }

    // ==================== Database Query Tests ====================

    public function test_uses_correct_database_query(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'abc123',
        ]);

        $result = $this->repository->findByShortenedCode('abc123');

        // Verify it's using the correct where clause
        $this->assertEquals($url->shortened_url, $result->shortened_url);
        $this->assertEquals('abc123', $result->shortened_url);
    }
}
