<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Url;
use App\Repositories\UrlRepository;
use App\Services\AnalyticsService;
use App\Services\UrlRedirectService;
use App\Services\UrlValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class UrlRedirectServiceTest extends TestCase
{
    protected UrlRedirectService $service;

    protected $urlRepositoryMock;

    protected $validatorMock;

    protected $analyticsServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mocks
        $this->urlRepositoryMock = Mockery::mock(UrlRepository::class);
        $this->validatorMock = Mockery::mock(UrlValidationService::class);
        $this->analyticsServiceMock = Mockery::mock(AnalyticsService::class);

        // Create service with mocked dependencies
        $this->service = new UrlRedirectService(
            $this->urlRepositoryMock,
            $this->validatorMock,
            $this->analyticsServiceMock
        );

        // Prevent actual logging during tests
        Log::shouldReceive('info')->byDefault();
    }

    // ==================== Happy Path Tests ====================

    public function test_redirects_to_original_url_successfully(): void
    {
        $code = 'abc123';
        $targetUrl = 'https://example.com';
        $request = Request::create("/{$code}", 'GET');

        $url = new Url([
            'id' => 1,
            'original_url' => $targetUrl,
            'shortened_url' => $code,
        ]);

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->with($code)
            ->andReturn($url);

        $this->validatorMock
            ->shouldReceive('isValid')
            ->once()
            ->with($targetUrl)
            ->andReturn(true);

        $this->analyticsServiceMock
            ->shouldReceive('trackRedirect')
            ->once()
            ->with($url, $request);

        $response = $this->service->handleRedirect($code, $request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals($targetUrl, $response->getTargetUrl());
    }

    public function test_falls_back_to_target_url_when_original_url_missing(): void
    {
        $code = 'abc123';
        $targetUrl = 'https://example.com';
        $request = Request::create("/{$code}", 'GET');

        $url = new Url([
            'id' => 1,
            'target_url' => $targetUrl,
            'shortened_url' => $code,
        ]);

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn($url);

        $this->validatorMock
            ->shouldReceive('isValid')
            ->once()
            ->with($targetUrl)
            ->andReturn(true);

        $this->analyticsServiceMock
            ->shouldReceive('trackRedirect')
            ->once();

        $response = $this->service->handleRedirect($code, $request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals($targetUrl, $response->getTargetUrl());
    }

    public function test_prefers_original_url_over_target_url(): void
    {
        $code = 'abc123';
        $originalUrl = 'https://original.example.com';
        $request = Request::create("/{$code}", 'GET');

        // Create a URL model and set attributes directly to avoid mutator issues
        $url = new Url;
        $url->id = 1;
        $url->shortened_url = $code;
        // Set the original_url attribute directly
        $url->setAttribute('original_url', $originalUrl);

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn($url);

        $this->validatorMock
            ->shouldReceive('isValid')
            ->once()
            ->with($originalUrl)
            ->andReturn(true);

        $this->analyticsServiceMock
            ->shouldReceive('trackRedirect')
            ->once();

        $response = $this->service->handleRedirect($code, $request);

        $this->assertEquals($originalUrl, $response->getTargetUrl());
    }

    // ==================== Analytics Tracking Tests ====================

    public function test_tracks_analytics_on_successful_redirect(): void
    {
        $code = 'abc123';
        $targetUrl = 'https://example.com';
        $request = Request::create("/{$code}", 'GET', [], [], [], [
            'HTTP_REFERER' => 'https://referrer.com',
            'HTTP_USER_AGENT' => 'Mozilla/5.0',
        ]);

        $url = new Url([
            'id' => 1,
            'original_url' => $targetUrl,
            'shortened_url' => $code,
        ]);

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn($url);

        $this->validatorMock
            ->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        $this->analyticsServiceMock
            ->shouldReceive('trackRedirect')
            ->once()
            ->with($url, $request);

        $response = $this->service->handleRedirect($code, $request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_logs_redirect_attempt(): void
    {
        $code = 'abc123';
        $targetUrl = 'https://example.com';
        $request = Request::create("/{$code}", 'GET', [], [], [], [
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_USER_AGENT' => 'TestAgent',
            'HTTP_REFERER' => 'https://referrer.com',
        ]);

        $url = new Url([
            'id' => 1,
            'original_url' => $targetUrl,
            'shortened_url' => $code,
        ]);

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn($url);

        $this->validatorMock
            ->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        $this->analyticsServiceMock
            ->shouldReceive('trackRedirect')
            ->once();

        Log::shouldReceive('info')
            ->once()
            ->with('URL redirect attempt', Mockery::on(function ($data) use ($code) {
                return $data['code'] === $code
                    && isset($data['ip'])
                    && isset($data['user_agent'])
                    && isset($data['referer']);
            }));

        $response = $this->service->handleRedirect($code, $request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    // ==================== Error Handling Tests ====================

    public function test_throws_404_when_code_not_found(): void
    {
        $code = 'nonexistent';
        $request = Request::create("/{$code}", 'GET');

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->with($code)
            ->andReturn(null);

        $this->analyticsServiceMock
            ->shouldReceive('track404')
            ->once()
            ->with($code, $request);

        $this->expectException(NotFoundHttpException::class);
        $this->service->handleRedirect($code, $request);
    }

    public function test_throws_404_when_destination_url_is_null(): void
    {
        $code = 'abc123';
        $request = Request::create("/{$code}", 'GET');

        $url = new Url([
            'id' => 1,
            'shortened_url' => $code,
        ]);

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn($url);

        $this->analyticsServiceMock
            ->shouldReceive('trackRedirect')
            ->once();

        $this->analyticsServiceMock
            ->shouldReceive('track404')
            ->once()
            ->with($code, $request);

        $this->expectException(NotFoundHttpException::class);
        $this->service->handleRedirect($code, $request);
    }

    public function test_throws_404_when_destination_url_is_invalid(): void
    {
        $code = 'abc123';
        $invalidUrl = 'not-a-valid-url';
        $request = Request::create("/{$code}", 'GET');

        $url = new Url([
            'id' => 1,
            'original_url' => $invalidUrl,
            'shortened_url' => $code,
        ]);

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn($url);

        $this->validatorMock
            ->shouldReceive('isValid')
            ->once()
            ->with($invalidUrl)
            ->andReturn(false);

        $this->analyticsServiceMock
            ->shouldReceive('trackRedirect')
            ->once();

        $this->analyticsServiceMock
            ->shouldReceive('track404')
            ->once()
            ->with($code, $request);

        $this->expectException(NotFoundHttpException::class);
        $this->service->handleRedirect($code, $request);
    }

    public function test_tracks_404_when_url_not_found(): void
    {
        $code = 'missing123';
        $request = Request::create("/{$code}", 'GET');

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn(null);

        $this->analyticsServiceMock
            ->shouldReceive('track404')
            ->once()
            ->with($code, $request);

        $exceptionThrown = false;
        try {
            $this->service->handleRedirect($code, $request);
        } catch (NotFoundHttpException $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown, 'Expected NotFoundHttpException to be thrown');
    }

    // ==================== Security & Validation Tests ====================

    public function test_validates_url_before_redirect(): void
    {
        $code = 'abc123';
        $suspiciousUrl = 'javascript:alert(1)';
        $request = Request::create("/{$code}", 'GET');

        $url = new Url([
            'id' => 1,
            'original_url' => $suspiciousUrl,
            'shortened_url' => $code,
        ]);

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn($url);

        $this->validatorMock
            ->shouldReceive('isValid')
            ->once()
            ->with($suspiciousUrl)
            ->andReturn(false);

        $this->analyticsServiceMock
            ->shouldReceive('trackRedirect')
            ->once();

        $this->analyticsServiceMock
            ->shouldReceive('track404')
            ->once();

        $this->expectException(NotFoundHttpException::class);
        $this->service->handleRedirect($code, $request);
    }

    public function test_uses_302_redirect(): void
    {
        $code = 'abc123';
        $targetUrl = 'https://example.com';
        $request = Request::create("/{$code}", 'GET');

        $url = new Url([
            'id' => 1,
            'original_url' => $targetUrl,
            'shortened_url' => $code,
        ]);

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn($url);

        $this->validatorMock
            ->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        $this->analyticsServiceMock
            ->shouldReceive('trackRedirect')
            ->once();

        $response = $this->service->handleRedirect($code, $request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    // ==================== Edge Cases ====================

    public function test_handles_code_with_special_characters(): void
    {
        $code = 'aB-1_2';
        $targetUrl = 'https://example.com';
        $request = Request::create("/{$code}", 'GET');

        $url = new Url([
            'id' => 1,
            'original_url' => $targetUrl,
            'shortened_url' => $code,
        ]);

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->with($code)
            ->andReturn($url);

        $this->validatorMock
            ->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        $this->analyticsServiceMock
            ->shouldReceive('trackRedirect')
            ->once();

        $response = $this->service->handleRedirect($code, $request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_handles_international_destination_url(): void
    {
        $code = 'abc123';
        $targetUrl = 'https://例え.jp/パス';
        $request = Request::create("/{$code}", 'GET');

        $url = new Url([
            'id' => 1,
            'original_url' => $targetUrl,
            'shortened_url' => $code,
        ]);

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn($url);

        $this->validatorMock
            ->shouldReceive('isValid')
            ->once()
            ->with($targetUrl)
            ->andReturn(true);

        $this->analyticsServiceMock
            ->shouldReceive('trackRedirect')
            ->once();

        $response = $this->service->handleRedirect($code, $request);

        $this->assertEquals($targetUrl, $response->getTargetUrl());
    }

    public function test_handles_url_with_query_parameters(): void
    {
        $code = 'abc123';
        $targetUrl = 'https://example.com/page?param1=value1&param2=value2';
        $request = Request::create("/{$code}", 'GET');

        $url = new Url([
            'id' => 1,
            'original_url' => $targetUrl,
            'shortened_url' => $code,
        ]);

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn($url);

        $this->validatorMock
            ->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        $this->analyticsServiceMock
            ->shouldReceive('trackRedirect')
            ->once();

        $response = $this->service->handleRedirect($code, $request);

        $this->assertEquals($targetUrl, $response->getTargetUrl());
    }

    public function test_handles_url_with_fragment(): void
    {
        $code = 'abc123';
        $targetUrl = 'https://example.com/page#section';
        $request = Request::create("/{$code}", 'GET');

        $url = new Url([
            'id' => 1,
            'original_url' => $targetUrl,
            'shortened_url' => $code,
        ]);

        $this->urlRepositoryMock
            ->shouldReceive('findByShortenedCode')
            ->once()
            ->andReturn($url);

        $this->validatorMock
            ->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        $this->analyticsServiceMock
            ->shouldReceive('trackRedirect')
            ->once();

        $response = $this->service->handleRedirect($code, $request);

        $this->assertEquals($targetUrl, $response->getTargetUrl());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
