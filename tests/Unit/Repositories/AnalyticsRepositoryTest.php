<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Repositories\AnalyticsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AnalyticsRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected AnalyticsRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new AnalyticsRepository;

        // Ensure the analytics table exists - use Laravel's Schema Builder
        if (! DB::getSchemaBuilder()->hasTable('url_analytics')) {
            DB::getSchemaBuilder()->create('url_analytics', function ($table) {
                $table->id();
                $table->unsignedBigInteger('url_id');
                $table->string('referer_domain')->nullable();
                $table->string('user_agent_family')->nullable();
                $table->timestamp('created_at');
            });
        }
    }

    // ==================== Store Tests ====================

    public function test_stores_analytics_data_successfully(): void
    {
        $data = [
            'url_id' => 1,
            'referer_domain' => 'example.com',
            'user_agent_family' => 'Chrome',
            'created_at' => now(),
        ];

        $this->repository->store($data);

        $this->assertDatabaseHas('url_analytics', [
            'url_id' => 1,
            'referer_domain' => 'example.com',
            'user_agent_family' => 'Chrome',
        ]);
    }

    public function test_stores_multiple_analytics_entries(): void
    {
        $data1 = [
            'url_id' => 1,
            'referer_domain' => 'example.com',
            'user_agent_family' => 'Chrome',
            'created_at' => now(),
        ];

        $data2 = [
            'url_id' => 2,
            'referer_domain' => 'test.com',
            'user_agent_family' => 'Firefox',
            'created_at' => now(),
        ];

        $this->repository->store($data1);
        $this->repository->store($data2);

        $this->assertDatabaseHas('url_analytics', [
            'url_id' => 1,
            'referer_domain' => 'example.com',
        ]);

        $this->assertDatabaseHas('url_analytics', [
            'url_id' => 2,
            'referer_domain' => 'test.com',
        ]);
    }

    public function test_stores_analytics_with_null_referer_domain(): void
    {
        $data = [
            'url_id' => 1,
            'referer_domain' => null,
            'user_agent_family' => 'Chrome',
            'created_at' => now(),
        ];

        $this->repository->store($data);

        $this->assertDatabaseHas('url_analytics', [
            'url_id' => 1,
            'referer_domain' => null,
            'user_agent_family' => 'Chrome',
        ]);
    }

    public function test_stores_analytics_with_null_user_agent_family(): void
    {
        $data = [
            'url_id' => 1,
            'referer_domain' => 'example.com',
            'user_agent_family' => null,
            'created_at' => now(),
        ];

        $this->repository->store($data);

        $this->assertDatabaseHas('url_analytics', [
            'url_id' => 1,
            'referer_domain' => 'example.com',
            'user_agent_family' => null,
        ]);
    }

    public function test_stores_analytics_with_all_null_optional_fields(): void
    {
        $data = [
            'url_id' => 1,
            'referer_domain' => null,
            'user_agent_family' => null,
            'created_at' => now(),
        ];

        $this->repository->store($data);

        $this->assertDatabaseHas('url_analytics', [
            'url_id' => 1,
            'referer_domain' => null,
            'user_agent_family' => null,
        ]);
    }

    public function test_stores_timestamp_correctly(): void
    {
        $timestamp = now();
        $data = [
            'url_id' => 1,
            'referer_domain' => 'example.com',
            'user_agent_family' => 'Chrome',
            'created_at' => $timestamp,
        ];

        $this->repository->store($data);

        $record = DB::table('url_analytics')->where('url_id', 1)->first();

        $this->assertNotNull($record);
        $this->assertNotNull($record->created_at);
    }

    // ==================== Different URL IDs Tests ====================

    public function test_stores_analytics_for_different_urls(): void
    {
        $data1 = [
            'url_id' => 1,
            'referer_domain' => 'example.com',
            'user_agent_family' => 'Chrome',
            'created_at' => now(),
        ];

        $data2 = [
            'url_id' => 2,
            'referer_domain' => 'example.com',
            'user_agent_family' => 'Chrome',
            'created_at' => now(),
        ];

        $this->repository->store($data1);
        $this->repository->store($data2);

        $count1 = DB::table('url_analytics')->where('url_id', 1)->count();
        $count2 = DB::table('url_analytics')->where('url_id', 2)->count();

        $this->assertEquals(1, $count1);
        $this->assertEquals(1, $count2);
    }

    public function test_stores_multiple_analytics_for_same_url(): void
    {
        $data1 = [
            'url_id' => 1,
            'referer_domain' => 'example.com',
            'user_agent_family' => 'Chrome',
            'created_at' => now(),
        ];

        $data2 = [
            'url_id' => 1,
            'referer_domain' => 'test.com',
            'user_agent_family' => 'Firefox',
            'created_at' => now(),
        ];

        $this->repository->store($data1);
        $this->repository->store($data2);

        $count = DB::table('url_analytics')->where('url_id', 1)->count();

        $this->assertEquals(2, $count);
    }

    // ==================== Edge Cases ====================

    public function test_handles_international_domain_names(): void
    {
        $data = [
            'url_id' => 1,
            'referer_domain' => '例え.jp',
            'user_agent_family' => 'Safari',
            'created_at' => now(),
        ];

        $this->repository->store($data);

        $this->assertDatabaseHas('url_analytics', [
            'url_id' => 1,
            'referer_domain' => '例え.jp',
        ]);
    }

    public function test_handles_very_long_referer_domain(): void
    {
        $longDomain = str_repeat('a', 200).'.com';

        $data = [
            'url_id' => 1,
            'referer_domain' => $longDomain,
            'user_agent_family' => 'Chrome',
            'created_at' => now(),
        ];

        $this->repository->store($data);

        $record = DB::table('url_analytics')->where('url_id', 1)->first();

        $this->assertNotNull($record);
    }

    public function test_handles_various_user_agent_families(): void
    {
        $userAgents = [
            'Chrome',
            'Firefox',
            'Safari',
            'Edge',
            'Mobile Safari',
            'Chrome Mobile',
            'Samsung Browser',
        ];

        foreach ($userAgents as $index => $ua) {
            $data = [
                'url_id' => $index + 1,
                'referer_domain' => 'example.com',
                'user_agent_family' => $ua,
                'created_at' => now(),
            ];

            $this->repository->store($data);

            $this->assertDatabaseHas('url_analytics', [
                'url_id' => $index + 1,
                'user_agent_family' => $ua,
            ]);
        }
    }

    public function test_handles_special_characters_in_referer_domain(): void
    {
        $data = [
            'url_id' => 1,
            'referer_domain' => 'sub-domain.example.com',
            'user_agent_family' => 'Chrome',
            'created_at' => now(),
        ];

        $this->repository->store($data);

        $this->assertDatabaseHas('url_analytics', [
            'url_id' => 1,
            'referer_domain' => 'sub-domain.example.com',
        ]);
    }

    // ==================== Batch Inserts Tests ====================

    public function test_handles_rapid_sequential_inserts(): void
    {
        for ($i = 1; $i <= 100; $i++) {
            $data = [
                'url_id' => $i,
                'referer_domain' => "example{$i}.com",
                'user_agent_family' => 'Chrome',
                'created_at' => now(),
            ];

            $this->repository->store($data);
        }

        $count = DB::table('url_analytics')->count();

        $this->assertEquals(100, $count);
    }

    public function test_each_insert_is_independent(): void
    {
        $data1 = [
            'url_id' => 1,
            'referer_domain' => 'example.com',
            'user_agent_family' => 'Chrome',
            'created_at' => now(),
        ];

        $this->repository->store($data1);

        $count1 = DB::table('url_analytics')->count();
        $this->assertEquals(1, $count1);

        $data2 = [
            'url_id' => 2,
            'referer_domain' => 'test.com',
            'user_agent_family' => 'Firefox',
            'created_at' => now(),
        ];

        $this->repository->store($data2);

        $count2 = DB::table('url_analytics')->count();
        $this->assertEquals(2, $count2);
    }

    protected function tearDown(): void
    {
        // Clean up the analytics table if it was created for testing
        if (DB::getSchemaBuilder()->hasTable('url_analytics')) {
            DB::table('url_analytics')->truncate();
        }

        parent::tearDown();
    }
}
