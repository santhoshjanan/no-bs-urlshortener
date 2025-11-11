<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\RecaptchaService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RecaptchaServiceTest extends TestCase
{
    protected RecaptchaService $service;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    // ==================== Configuration Tests ====================

    public function test_is_enabled_returns_true_when_configured(): void
    {
        Config::set('services.recaptcha.site_key', 'test-site-key');
        Config::set('services.recaptcha.secret_key', 'test-secret-key');

        $service = new RecaptchaService;

        $this->assertTrue($service->isEnabled());
    }

    public function test_is_enabled_returns_false_when_site_key_missing(): void
    {
        Config::set('services.recaptcha.site_key', null);
        Config::set('services.recaptcha.secret_key', 'test-secret-key');

        $service = new RecaptchaService;

        $this->assertFalse($service->isEnabled());
    }

    public function test_is_enabled_returns_false_when_secret_key_missing(): void
    {
        Config::set('services.recaptcha.site_key', 'test-site-key');
        Config::set('services.recaptcha.secret_key', null);

        $service = new RecaptchaService;

        $this->assertFalse($service->isEnabled());
    }

    public function test_is_enabled_returns_false_when_both_keys_missing(): void
    {
        Config::set('services.recaptcha.site_key', null);
        Config::set('services.recaptcha.secret_key', null);

        $service = new RecaptchaService;

        $this->assertFalse($service->isEnabled());
    }

    public function test_gets_site_key(): void
    {
        Config::set('services.recaptcha.site_key', 'test-site-key-123');
        Config::set('services.recaptcha.secret_key', 'test-secret-key');

        $service = new RecaptchaService;

        $this->assertEquals('test-site-key-123', $service->getSiteKey());
    }

    public function test_gets_score_threshold(): void
    {
        Config::set('services.recaptcha.site_key', 'test-site-key');
        Config::set('services.recaptcha.secret_key', 'test-secret-key');
        Config::set('services.recaptcha.score_threshold', 0.7);

        $service = new RecaptchaService;

        $this->assertEquals(0.7, $service->getScoreThreshold());
    }

    public function test_uses_default_score_threshold(): void
    {
        Config::set('services.recaptcha.site_key', 'test-site-key');
        Config::set('services.recaptcha.secret_key', 'test-secret-key');
        Config::set('services.recaptcha.score_threshold', null);

        $service = new RecaptchaService;

        $this->assertEquals(0.5, $service->getScoreThreshold());
    }

    // ==================== Verification - Not Enabled Tests ====================

    public function test_verify_returns_error_when_not_enabled(): void
    {
        Config::set('services.recaptcha.site_key', null);
        Config::set('services.recaptcha.secret_key', null);

        $service = new RecaptchaService;

        $result = $service->verify('test-token', 'submit');

        $this->assertFalse($result['success']);
        $this->assertNull($result['score']);
        $this->assertEquals('reCAPTCHA secret key not configured', $result['error']);
    }

    // ==================== Verification - Success Tests ====================

    public function test_verify_returns_success_for_valid_token(): void
    {
        Config::set('services.recaptcha.site_key', 'test-site-key');
        Config::set('services.recaptcha.secret_key', 'test-secret-key');
        Config::set('services.recaptcha.score_threshold', 0.5);

        $service = new RecaptchaService;

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
                'action' => 'submit',
                'challenge_ts' => '2025-11-10T00:00:00Z',
                'hostname' => 'example.com',
            ], 200),
        ]);

        $result = $service->verify('test-token', 'submit');

        $this->assertTrue($result['success']);
        $this->assertEquals(0.9, $result['score']);
        $this->assertNull($result['error']);
    }

    public function test_verify_passes_token_and_action_to_google(): void
    {
        Config::set('services.recaptcha.site_key', 'test-site-key');
        Config::set('services.recaptcha.secret_key', 'test-secret-key');

        $service = new RecaptchaService;

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
                'action' => 'custom_action',
            ], 200),
        ]);

        $service->verify('my-test-token', 'custom_action');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://www.google.com/recaptcha/api/siteverify'
                && $request['secret'] === 'test-secret-key'
                && $request['response'] === 'my-test-token';
        });
    }

    // ==================== Verification - Failure Tests ====================

    public function test_verify_fails_when_google_returns_not_successful(): void
    {
        Config::set('services.recaptcha.site_key', 'test-site-key');
        Config::set('services.recaptcha.secret_key', 'test-secret-key');

        $service = new RecaptchaService;

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ], 200),
        ]);

        $result = $service->verify('invalid-token', 'submit');

        $this->assertFalse($result['success']);
        $this->assertNull($result['score']);
        $this->assertStringContainsString('reCAPTCHA verification failed', $result['error']);
    }

    public function test_verify_fails_when_action_mismatch(): void
    {
        Config::set('services.recaptcha.site_key', 'test-site-key');
        Config::set('services.recaptcha.secret_key', 'test-secret-key');

        $service = new RecaptchaService;

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
                'action' => 'different_action',
            ], 200),
        ]);

        $result = $service->verify('test-token', 'expected_action');

        $this->assertFalse($result['success']);
        $this->assertEquals(0.9, $result['score']);
        $this->assertEquals('reCAPTCHA action mismatch', $result['error']);
    }

    public function test_verify_fails_when_score_below_threshold(): void
    {
        Config::set('services.recaptcha.site_key', 'test-site-key');
        Config::set('services.recaptcha.secret_key', 'test-secret-key');
        Config::set('services.recaptcha.score_threshold', 0.5);

        $service = new RecaptchaService;

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.3,
                'action' => 'submit',
            ], 200),
        ]);

        $result = $service->verify('test-token', 'submit');

        $this->assertFalse($result['success']);
        $this->assertEquals(0.3, $result['score']);
        $this->assertEquals('reCAPTCHA score too low (possible bot)', $result['error']);
    }

    public function test_verify_fails_when_http_request_fails(): void
    {
        Config::set('services.recaptcha.site_key', 'test-site-key');
        Config::set('services.recaptcha.secret_key', 'test-secret-key');

        $service = new RecaptchaService;

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([], 500),
        ]);

        $result = $service->verify('test-token', 'submit');

        $this->assertFalse($result['success']);
        $this->assertNull($result['score']);
        $this->assertEquals('Failed to connect to reCAPTCHA service', $result['error']);
    }

    // ==================== Edge Cases ====================

    public function test_verify_handles_missing_error_codes(): void
    {
        Config::set('services.recaptcha.site_key', 'test-site-key');
        Config::set('services.recaptcha.secret_key', 'test-secret-key');

        $service = new RecaptchaService;

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
            ], 200),
        ]);

        $result = $service->verify('test-token', 'submit');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('unknown', $result['error']);
    }

    public function test_verify_handles_missing_score(): void
    {
        Config::set('services.recaptcha.site_key', 'test-site-key');
        Config::set('services.recaptcha.secret_key', 'test-secret-key');
        Config::set('services.recaptcha.score_threshold', 0.5);

        $service = new RecaptchaService;

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'action' => 'submit',
            ], 200),
        ]);

        $result = $service->verify('test-token', 'submit');

        $this->assertFalse($result['success']);
        $this->assertEquals(0.0, $result['score']);
    }

    public function test_verify_handles_exception_gracefully(): void
    {
        Config::set('services.recaptcha.site_key', 'test-site-key');
        Config::set('services.recaptcha.secret_key', 'test-secret-key');

        $service = new RecaptchaService;

        Http::fake(function () {
            throw new \Exception('Network error');
        });

        $result = $service->verify('test-token', 'submit');

        $this->assertFalse($result['success']);
        $this->assertNull($result['score']);
        $this->assertStringContainsString('Network error', $result['error']);
    }

    // ==================== Score Threshold Tests ====================

    public function test_verify_passes_with_score_equal_to_threshold(): void
    {
        Config::set('services.recaptcha.site_key', 'test-site-key');
        Config::set('services.recaptcha.secret_key', 'test-secret-key');
        Config::set('services.recaptcha.score_threshold', 0.5);

        $service = new RecaptchaService;

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.5,
                'action' => 'submit',
            ], 200),
        ]);

        $result = $service->verify('test-token', 'submit');

        $this->assertTrue($result['success']);
        $this->assertEquals(0.5, $result['score']);
    }

    public function test_verify_passes_with_high_score(): void
    {
        Config::set('services.recaptcha.site_key', 'test-site-key');
        Config::set('services.recaptcha.secret_key', 'test-secret-key');
        Config::set('services.recaptcha.score_threshold', 0.5);

        $service = new RecaptchaService;

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 1.0,
                'action' => 'submit',
            ], 200),
        ]);

        $result = $service->verify('test-token', 'submit');

        $this->assertTrue($result['success']);
        $this->assertEquals(1.0, $result['score']);
    }
}
