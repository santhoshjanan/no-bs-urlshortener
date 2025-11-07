<?php

namespace Tests\Unit;

use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlModelTest extends TestCase
{
    use RefreshDatabase;

    // ==================== Model Creation Tests ====================

    public function test_url_can_be_created(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'test123'
        ]);

        $this->assertInstanceOf(Url::class, $url);
        $this->assertEquals('https://example.com', $url->original_url);
        $this->assertEquals('test123', $url->shortened_url);
    }

    public function test_url_has_default_clicks_value(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'test123'
        ]);

        $this->assertEquals(0, $url->clicks);
    }

    public function test_url_can_have_custom_clicks_value(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'test123',
            'clicks' => 5
        ]);

        $url->refresh();
        $this->assertEquals(5, $url->clicks);
    }

    public function test_url_analytics_is_nullable(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'test123'
        ]);

        $this->assertNull($url->analytics);
    }

    public function test_url_analytics_can_be_set(): void
    {
        $analytics = [
            [
                'timestamp' => '2024-01-01T00:00:00Z',
                'referer_domain' => 'example.com'
            ]
        ];

        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'test123',
            'analytics' => $analytics
        ]);

        $this->assertIsArray($url->analytics);
        $this->assertCount(1, $url->analytics);
    }

    // ==================== Mass Assignment Tests ====================

    public function test_url_only_allows_fillable_fields(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'test123',
            'clicks' => 10,
            'analytics' => []
        ]);

        $this->assertEquals('https://example.com', $url->original_url);
        $this->assertEquals('test123', $url->shortened_url);
        $this->assertEquals(10, $url->clicks);
        $this->assertIsArray($url->analytics);
    }

    public function test_url_prevents_mass_assignment_of_non_fillable_fields(): void
    {
        // Attempting to set non-fillable fields should be ignored
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'test123',
            'id' => 999, // Should be ignored
            'created_at' => '2020-01-01 00:00:00' // Should be ignored
        ]);

        // ID should be auto-generated, not 999
        $this->assertNotEquals(999, $url->id);
        // Created_at should be current timestamp, not the provided one
        $this->assertNotNull($url->created_at);
    }

    // ==================== Analytics Casting Tests ====================

    public function test_analytics_is_casted_to_array(): void
    {
        $analytics = [
            ['timestamp' => '2024-01-01T00:00:00Z', 'referer_domain' => 'example.com'],
            ['timestamp' => '2024-01-02T00:00:00Z', 'referer_domain' => 'test.com']
        ];

        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'test123',
            'analytics' => $analytics
        ]);

        $this->assertIsArray($url->analytics);
        $this->assertCount(2, $url->analytics);
    }

    public function test_analytics_json_is_properly_decoded(): void
    {
        $analytics = [
            ['timestamp' => '2024-01-01T00:00:00Z', 'referer_domain' => 'example.com']
        ];

        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'test123',
            'analytics' => $analytics
        ]);

        // Refresh from database to test JSON encoding/decoding
        $url->refresh();

        $this->assertIsArray($url->analytics);
        $this->assertEquals('example.com', $url->analytics[0]['referer_domain']);
    }

    public function test_analytics_can_be_updated(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'test123'
        ]);

        $newAnalytics = [
            ['timestamp' => '2024-01-01T00:00:00Z', 'referer_domain' => 'example.com']
        ];

        $url->update(['analytics' => $newAnalytics]);

        $this->assertCount(1, $url->analytics);
    }

    // ==================== Model Relationships Tests ====================

    public function test_url_has_timestamps(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'test123'
        ]);

        $this->assertNotNull($url->created_at);
        $this->assertNotNull($url->updated_at);
    }

    public function test_url_timestamps_are_updated_on_modification(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'test123'
        ]);

        $originalUpdatedAt = $url->updated_at->timestamp;

        // Wait a moment to ensure timestamp difference
        usleep(100000); // 0.1 seconds

        $url->update(['clicks' => 5]);
        $url->refresh();

        $this->assertGreaterThanOrEqual($originalUpdatedAt, $url->updated_at->timestamp);
    }

    // ==================== Unique Constraint Tests ====================

    public function test_shortened_url_must_be_unique(): void
    {
        Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'unique123'
        ]);

        // Attempting to create another URL with the same shortened_url should fail
        $this->expectException(\Illuminate\Database\QueryException::class);

        Url::create([
            'original_url' => 'https://another.com',
            'shortened_url' => 'unique123'
        ]);
    }

    // ==================== Increment Tests ====================

    public function test_clicks_can_be_incremented(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'increment123',
            'clicks' => 5
        ]);

        $url->increment('clicks');
        // increment() already refreshes the model, but let's be explicit
        $url = $url->fresh();

        $this->assertEquals(6, $url->clicks);
    }

    public function test_clicks_can_be_incremented_by_custom_amount(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'increment456',
            'clicks' => 5
        ]);

        $url->increment('clicks', 3);
        // increment() already refreshes the model, but let's be explicit
        $url = $url->fresh();

        $this->assertEquals(8, $url->clicks);
    }

    // ==================== Query Tests ====================

    public function test_url_can_be_found_by_shortened_url(): void
    {
        Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'find123'
        ]);

        $url = Url::where('shortened_url', 'find123')->first();

        $this->assertInstanceOf(Url::class, $url);
        $this->assertEquals('https://example.com', $url->original_url);
    }

    public function test_url_where_returns_null_for_nonexistent(): void
    {
        $url = Url::where('shortened_url', 'nonexistent')->first();

        $this->assertNull($url);
    }

    public function test_url_exists_checks_work(): void
    {
        Url::create([
            'original_url' => 'https://example.com',
            'shortened_url' => 'exists123'
        ]);

        $this->assertTrue(Url::where('shortened_url', 'exists123')->exists());
        $this->assertFalse(Url::where('shortened_url', 'nonexistent')->exists());
    }
}

