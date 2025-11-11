<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Url;
use App\Repositories\AnalyticsRepository;
use App\Services\AnalyticsService;
use Mockery;
use Tests\TestCase;

class AnalyticsServiceTest extends TestCase
{
    protected AnalyticsService $service;

    protected $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mock
        $this->repositoryMock = Mockery::mock(AnalyticsRepository::class);

        // Create service with mocked dependency
        $this->service = new AnalyticsService($this->repositoryMock);
    }

    // ==================== Record Redirect Tests ====================

    public function test_records_redirect_with_full_metadata(): void
    {
        $url = new Url;
        $url->id = 1;

        $meta = [
            'referer_domain' => 'example.com',
            'user_agent_family' => 'Chrome',
        ];

        $this->repositoryMock
            ->shouldReceive('store')
            ->once()
            ->with(Mockery::on(function ($payload) use ($url, $meta) {
                return $payload['url_id'] === $url->id
                    && $payload['referer_domain'] === $meta['referer_domain']
                    && $payload['user_agent_family'] === $meta['user_agent_family']
                    && isset($payload['created_at']);
            }));

        $this->service->recordRedirect($url, $meta);

        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_records_redirect_without_metadata(): void
    {
        $url = new Url;
        $url->id = 1;

        $this->repositoryMock
            ->shouldReceive('store')
            ->once()
            ->with(Mockery::on(function ($payload) use ($url) {
                return $payload['url_id'] === $url->id
                    && $payload['referer_domain'] === null
                    && $payload['user_agent_family'] === null
                    && isset($payload['created_at']);
            }));

        $this->service->recordRedirect($url);

        $this->assertTrue(true);
    }

    public function test_records_redirect_with_partial_metadata(): void
    {
        $url = new Url;
        $url->id = 1;

        $meta = [
            'referer_domain' => 'example.com',
        ];

        $this->repositoryMock
            ->shouldReceive('store')
            ->once()
            ->with(Mockery::on(function ($payload) use ($url, $meta) {
                return $payload['url_id'] === $url->id
                    && $payload['referer_domain'] === $meta['referer_domain']
                    && $payload['user_agent_family'] === null
                    && isset($payload['created_at']);
            }));

        $this->service->recordRedirect($url, $meta);

        $this->assertTrue(true);
    }

    public function test_records_redirect_includes_timestamp(): void
    {
        $url = new Url;
        $url->id = 1;

        $this->repositoryMock
            ->shouldReceive('store')
            ->once()
            ->with(Mockery::on(function ($payload) {
                return isset($payload['created_at'])
                    && $payload['created_at'] instanceof \Illuminate\Support\Carbon;
            }));

        $this->service->recordRedirect($url);

        $this->assertTrue(true);
    }

    public function test_records_redirect_preserves_privacy(): void
    {
        $url = new Url;
        $url->id = 1;

        $meta = [
            'referer_domain' => 'example.com', // Only domain, not full URL
            'user_agent_family' => 'Chrome',  // Only family, not full UA
            'ip_address' => '192.168.1.1',    // Should NOT be stored
        ];

        $this->repositoryMock
            ->shouldReceive('store')
            ->once()
            ->with(Mockery::on(function ($payload) {
                // Verify IP address is NOT included in payload
                return ! isset($payload['ip_address']);
            }));

        $this->service->recordRedirect($url, $meta);

        $this->assertTrue(true);
    }

    public function test_records_redirect_for_different_urls(): void
    {
        $url1 = new Url;
        $url1->id = 1;

        $url2 = new Url;
        $url2->id = 2;

        $this->repositoryMock
            ->shouldReceive('store')
            ->once()
            ->with(Mockery::on(function ($payload) {
                return $payload['url_id'] === 1;
            }));

        $this->repositoryMock
            ->shouldReceive('store')
            ->once()
            ->with(Mockery::on(function ($payload) {
                return $payload['url_id'] === 2;
            }));

        $this->service->recordRedirect($url1);
        $this->service->recordRedirect($url2);

        $this->assertTrue(true);
    }

    // ==================== Edge Cases ====================

    public function test_handles_null_referer_domain(): void
    {
        $url = new Url;
        $url->id = 1;

        $meta = [
            'referer_domain' => null,
            'user_agent_family' => 'Chrome',
        ];

        $this->repositoryMock
            ->shouldReceive('store')
            ->once()
            ->with(Mockery::on(function ($payload) {
                return $payload['referer_domain'] === null;
            }));

        $this->service->recordRedirect($url, $meta);

        $this->assertTrue(true);
    }

    public function test_handles_null_user_agent_family(): void
    {
        $url = new Url;
        $url->id = 1;

        $meta = [
            'referer_domain' => 'example.com',
            'user_agent_family' => null,
        ];

        $this->repositoryMock
            ->shouldReceive('store')
            ->once()
            ->with(Mockery::on(function ($payload) {
                return $payload['user_agent_family'] === null;
            }));

        $this->service->recordRedirect($url, $meta);

        $this->assertTrue(true);
    }

    public function test_handles_empty_metadata_array(): void
    {
        $url = new Url;
        $url->id = 1;

        $meta = [];

        $this->repositoryMock
            ->shouldReceive('store')
            ->once()
            ->with(Mockery::on(function ($payload) {
                return $payload['referer_domain'] === null
                    && $payload['user_agent_family'] === null;
            }));

        $this->service->recordRedirect($url, $meta);

        $this->assertTrue(true);
    }

    public function test_handles_extra_metadata_fields(): void
    {
        $url = new Url;
        $url->id = 1;

        $meta = [
            'referer_domain' => 'example.com',
            'user_agent_family' => 'Chrome',
            'extra_field' => 'should_be_ignored',
        ];

        $this->repositoryMock
            ->shouldReceive('store')
            ->once()
            ->with(Mockery::on(function ($payload) {
                // Verify extra fields are not included
                return ! isset($payload['extra_field']);
            }));

        $this->service->recordRedirect($url, $meta);

        $this->assertTrue(true);
    }

    public function test_records_redirect_with_international_referer(): void
    {
        $url = new Url;
        $url->id = 1;

        $meta = [
            'referer_domain' => '例え.jp',
            'user_agent_family' => 'Safari',
        ];

        $this->repositoryMock
            ->shouldReceive('store')
            ->once()
            ->with(Mockery::on(function ($payload) use ($meta) {
                return $payload['referer_domain'] === $meta['referer_domain'];
            }));

        $this->service->recordRedirect($url, $meta);

        $this->assertTrue(true);
    }

    public function test_records_redirect_with_various_user_agents(): void
    {
        $url = new Url;
        $url->id = 1;

        $userAgents = ['Chrome', 'Firefox', 'Safari', 'Edge', 'Mobile Safari'];

        foreach ($userAgents as $ua) {
            $this->repositoryMock
                ->shouldReceive('store')
                ->once()
                ->with(Mockery::on(function ($payload) use ($ua) {
                    return $payload['user_agent_family'] === $ua;
                }));

            $this->service->recordRedirect($url, ['user_agent_family' => $ua]);
        }

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
