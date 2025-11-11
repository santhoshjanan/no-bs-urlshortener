<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\UrlValidationService;
use Tests\TestCase;

class UrlValidationServiceTest extends TestCase
{
    protected UrlValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UrlValidationService;
    }

    // ==================== Valid URL Tests ====================

    public function test_validates_http_url(): void
    {
        $this->assertTrue($this->service->isValid('http://example.com'));
    }

    public function test_validates_https_url(): void
    {
        $this->assertTrue($this->service->isValid('https://example.com'));
    }

    public function test_validates_url_with_path(): void
    {
        $this->assertTrue($this->service->isValid('https://example.com/path/to/page'));
    }

    public function test_validates_url_with_query_parameters(): void
    {
        $this->assertTrue($this->service->isValid('https://example.com/page?param1=value1&param2=value2'));
    }

    public function test_validates_url_with_fragment(): void
    {
        $this->assertTrue($this->service->isValid('https://example.com/page#section'));
    }

    public function test_validates_url_with_port(): void
    {
        $this->assertTrue($this->service->isValid('https://example.com:8080/path'));
    }

    public function test_validates_subdomain_url(): void
    {
        $this->assertTrue($this->service->isValid('https://subdomain.example.com'));
    }

    public function test_validates_url_with_credentials(): void
    {
        $this->assertTrue($this->service->isValid('https://user:pass@example.com'));
    }

    // ==================== Invalid URL Tests ====================

    public function test_rejects_empty_string(): void
    {
        $this->assertFalse($this->service->isValid(''));
    }

    public function test_rejects_whitespace_only(): void
    {
        $this->assertFalse($this->service->isValid('   '));
    }

    public function test_rejects_url_without_scheme(): void
    {
        $this->assertFalse($this->service->isValid('example.com'));
    }

    public function test_rejects_url_with_ftp_scheme(): void
    {
        $this->assertFalse($this->service->isValid('ftp://example.com'));
    }

    public function test_rejects_url_with_file_scheme(): void
    {
        $this->assertFalse($this->service->isValid('file:///path/to/file'));
    }

    public function test_rejects_url_with_javascript_scheme(): void
    {
        $this->assertFalse($this->service->isValid('javascript:alert(1)'));
    }

    public function test_rejects_invalid_url_format(): void
    {
        $this->assertFalse($this->service->isValid('not a url'));
    }

    public function test_rejects_url_without_host(): void
    {
        $this->assertFalse($this->service->isValid('https://'));
    }

    // ==================== Whitespace Handling Tests ====================

    public function test_trims_whitespace_before_validation(): void
    {
        $this->assertTrue($this->service->isValid('  https://example.com  '));
    }

    public function test_rejects_whitespace_string_after_trim(): void
    {
        $this->assertFalse($this->service->isValid('     '));
    }

    // ==================== Blocked Domains Tests ====================

    public function test_blocks_malware_domain(): void
    {
        $this->assertFalse($this->service->isValid('https://malware.com'));
    }

    public function test_blocks_malware_subdomain(): void
    {
        $this->assertFalse($this->service->isValid('https://subdomain.malware.com'));
    }

    public function test_blocks_phishing_domain(): void
    {
        $this->assertFalse($this->service->isValid('https://phishing.net'));
    }

    public function test_blocks_phishing_subdomain(): void
    {
        $this->assertFalse($this->service->isValid('https://www.phishing.net'));
    }

    public function test_allows_non_blocked_domain(): void
    {
        $this->assertTrue($this->service->isValid('https://safe-site.com'));
    }

    public function test_blocks_case_insensitive_domain(): void
    {
        $this->assertFalse($this->service->isValid('https://MALWARE.COM'));
        $this->assertFalse($this->service->isValid('https://Phishing.NET'));
    }

    // ==================== Edge Cases ====================

    public function test_handles_international_domain_names(): void
    {
        // Note: PHP's filter_var doesn't handle raw international domain names
        // They need to be in punycode format (xn--)
        $this->assertTrue($this->service->isValid('https://xn--r8jz45g.jp'));

        // Raw international domains fail validation (expected behavior)
        $this->assertFalse($this->service->isValid('https://例え.jp'));
    }

    public function test_handles_very_long_urls(): void
    {
        $longPath = str_repeat('/segment', 100);
        $this->assertTrue($this->service->isValid("https://example.com{$longPath}"));
    }

    public function test_handles_url_with_many_query_parameters(): void
    {
        $params = [];
        for ($i = 0; $i < 50; $i++) {
            $params[] = "param{$i}=value{$i}";
        }
        $queryString = implode('&', $params);
        $this->assertTrue($this->service->isValid("https://example.com?{$queryString}"));
    }

    public function test_handles_url_with_special_characters_in_path(): void
    {
        $this->assertTrue($this->service->isValid('https://example.com/path-with-dashes_and_underscores'));
    }

    public function test_handles_url_with_encoded_characters(): void
    {
        $this->assertTrue($this->service->isValid('https://example.com/path%20with%20spaces'));
    }

    // ==================== Security Tests ====================

    public function test_rejects_xss_attempts_in_javascript_protocol(): void
    {
        $this->assertFalse($this->service->isValid('javascript:alert(document.cookie)'));
    }

    public function test_rejects_data_protocol(): void
    {
        $this->assertFalse($this->service->isValid('data:text/html,<script>alert(1)</script>'));
    }

    public function test_rejects_vbscript_protocol(): void
    {
        $this->assertFalse($this->service->isValid('vbscript:msgbox(1)'));
    }

    // ==================== Case Sensitivity Tests ====================

    public function test_accepts_uppercase_http_scheme(): void
    {
        $this->assertTrue($this->service->isValid('HTTP://example.com'));
    }

    public function test_accepts_uppercase_https_scheme(): void
    {
        $this->assertTrue($this->service->isValid('HTTPS://example.com'));
    }

    public function test_accepts_mixed_case_scheme(): void
    {
        $this->assertTrue($this->service->isValid('HtTpS://example.com'));
    }

    // ==================== Localhost and IP Address Tests ====================

    public function test_allows_localhost(): void
    {
        $this->assertTrue($this->service->isValid('http://localhost'));
        $this->assertTrue($this->service->isValid('http://localhost:8080'));
    }

    public function test_allows_ipv4_address(): void
    {
        $this->assertTrue($this->service->isValid('http://192.168.1.1'));
    }

    public function test_allows_ipv6_address(): void
    {
        $this->assertTrue($this->service->isValid('http://[::1]'));
        $this->assertTrue($this->service->isValid('http://[2001:db8::1]'));
    }
}
