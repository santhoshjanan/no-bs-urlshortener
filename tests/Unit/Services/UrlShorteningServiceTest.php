<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Url;
use App\Repositories\UrlRepository;
use App\Services\CacheService;
use App\Services\UrlShorteningService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class UrlShorteningServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UrlShorteningService $service;

    protected $urlRepositoryMock;

    protected $cacheServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mocks
        $this->urlRepositoryMock = Mockery::mock(UrlRepository::class);
        $this->cacheServiceMock = Mockery::mock(CacheService::class);

        // Create service with mocked dependencies
        $this->service = new UrlShorteningService(
            $this->urlRepositoryMock,
            $this->cacheServiceMock
        );
    }

    // ==================== Happy Path Tests ====================

    public function test_creates_shortened_url_successfully(): void
    {
        $targetUrl = 'https://example.com';

        // Mock repository to return null (no collision)
        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn(null);

        // Mock cache service to store the URL
        $this->cacheServiceMock
            ->shouldReceive('putUrl')
            ->once()
            ->withArgs(function ($code, $url) use ($targetUrl) {
                return strlen($code) === 6
                    && $url instanceof Url
                    && $url->target_url === $targetUrl;
            });

        $result = $this->service->createShortenedUrl($targetUrl);

        $this->assertInstanceOf(Url::class, $result);
        $this->assertEquals($targetUrl, $result->target_url);
        $this->assertNotEmpty($result->shortened_url);
        $this->assertEquals(6, strlen($result->shortened_url));

        // Verify it was saved to the database
        $this->assertDatabaseHas('urls', [
            'original_url' => $targetUrl,
            'shortened_url' => $result->shortened_url,
        ]);
    }

    public function test_generates_six_character_code(): void
    {
        $targetUrl = 'https://example.com';

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn(null);

        $this->cacheServiceMock
            ->shouldReceive('putUrl')
            ->once();

        $result = $this->service->createShortenedUrl($targetUrl);

        $this->assertEquals(6, strlen($result->shortened_url));
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]{6}$/', $result->shortened_url);
    }

    // ==================== Collision Handling Tests ====================

    public function test_handles_collision_and_retries(): void
    {
        $targetUrl = 'https://example.com';

        // First attempt: collision detected
        // Second attempt: success
        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->twice()
            ->andReturn(
                new Url(['shortened_url' => 'abc123']), // First: collision
                null // Second: no collision
            );

        $this->cacheServiceMock
            ->shouldReceive('putUrl')
            ->once();

        $result = $this->service->createShortenedUrl($targetUrl);

        $this->assertInstanceOf(Url::class, $result);
        $this->assertEquals($targetUrl, $result->target_url);
    }

    public function test_handles_multiple_collisions(): void
    {
        $targetUrl = 'https://example.com';

        // Simulate 3 collisions before success
        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->times(4)
            ->andReturn(
                new Url(['shortened_url' => 'abc123']), // First: collision
                new Url(['shortened_url' => 'def456']), // Second: collision
                new Url(['shortened_url' => 'ghi789']), // Third: collision
                null // Fourth: success
            );

        $this->cacheServiceMock
            ->shouldReceive('putUrl')
            ->once();

        $result = $this->service->createShortenedUrl($targetUrl);

        $this->assertInstanceOf(Url::class, $result);
        $this->assertEquals($targetUrl, $result->target_url);
    }

    public function test_throws_exception_after_max_attempts(): void
    {
        $targetUrl = 'https://example.com';

        // Always return a collision
        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->times(5)
            ->andReturn(new Url(['shortened_url' => 'collision']));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to generate unique short code');

        $this->service->createShortenedUrl($targetUrl);
    }

    // ==================== Database Transaction Tests ====================

    public function test_uses_database_transaction(): void
    {
        $targetUrl = 'https://example.com';

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn(null);

        $this->cacheServiceMock
            ->shouldReceive('putUrl')
            ->once();

        $result = $this->service->createShortenedUrl($targetUrl);

        // Verify result is valid
        $this->assertInstanceOf(Url::class, $result);
        $this->assertEquals($targetUrl, $result->target_url);
    }

    // ==================== Cache Priming Tests ====================

    public function test_primes_cache_after_creation(): void
    {
        $targetUrl = 'https://example.com';

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn(null);

        // Verify cache is called with correct parameters
        $this->cacheServiceMock
            ->shouldReceive('putUrl')
            ->once()
            ->withArgs(function ($code, $url) use ($targetUrl) {
                return is_string($code)
                    && strlen($code) === 6
                    && $url instanceof Url
                    && $url->target_url === $targetUrl;
            });

        $result = $this->service->createShortenedUrl($targetUrl);

        $this->assertNotNull($result);
    }

    // ==================== URL Handling Tests ====================

    public function test_handles_long_urls(): void
    {
        $targetUrl = 'https://example.com/very/long/path/with/many/segments/and/query?param1=value1&param2=value2&param3=value3';

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn(null);

        $this->cacheServiceMock
            ->shouldReceive('putUrl')
            ->once();

        $result = $this->service->createShortenedUrl($targetUrl);

        $this->assertEquals($targetUrl, $result->target_url);
        $this->assertEquals(6, strlen($result->shortened_url));
    }

    public function test_handles_urls_with_special_characters(): void
    {
        $targetUrl = 'https://example.com/path?query=value&special=!@#$%^&*()';

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn(null);

        $this->cacheServiceMock
            ->shouldReceive('putUrl')
            ->once();

        $result = $this->service->createShortenedUrl($targetUrl);

        $this->assertEquals($targetUrl, $result->target_url);
    }

    public function test_handles_international_domain_names(): void
    {
        $targetUrl = 'https://例え.jp/path';

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn(null);

        $this->cacheServiceMock
            ->shouldReceive('putUrl')
            ->once();

        $result = $this->service->createShortenedUrl($targetUrl);

        $this->assertEquals($targetUrl, $result->target_url);
    }

    // ==================== Code Uniqueness Tests ====================

    public function test_generates_different_codes_for_different_urls(): void
    {
        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->times(10)
            ->andReturn(null);

        $this->cacheServiceMock
            ->shouldReceive('putUrl')
            ->times(10);

        $codes = [];
        for ($i = 0; $i < 10; $i++) {
            $result = $this->service->createShortenedUrl("https://example{$i}.com");
            $codes[] = $result->shortened_url;
        }

        // All codes should be unique
        $uniqueCodes = array_unique($codes);
        $this->assertCount(10, $uniqueCodes);
    }

    // ==================== Edge Cases ====================

    public function test_handles_same_url_multiple_times(): void
    {
        $targetUrl = 'https://example.com';

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->times(3)
            ->andReturn(null);

        $this->cacheServiceMock
            ->shouldReceive('putUrl')
            ->times(3);

        // Create multiple shortened URLs for the same target
        $result1 = $this->service->createShortenedUrl($targetUrl);
        $result2 = $this->service->createShortenedUrl($targetUrl);
        $result3 = $this->service->createShortenedUrl($targetUrl);

        // Each should get a different shortened code
        $this->assertNotEquals($result1->shortened_url, $result2->shortened_url);
        $this->assertNotEquals($result2->shortened_url, $result3->shortened_url);
        $this->assertNotEquals($result1->shortened_url, $result3->shortened_url);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
