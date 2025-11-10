<?php

declare(strict_types=1);

namespace App\Services;

class UrlValidator
{
    private const ALLOWED_SCHEMES = ['http', 'https'];

    private const BLOCKED_DOMAINS = [
        'malware.com',
        'phishing.net',
    ];

    public function isValid(string $url): bool
    {
        $url = trim($url);
        if ($url === '') {
            return false;
        }

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if (! in_array($scheme, self::ALLOWED_SCHEMES, true)) {
            return false;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if ($host === '') {
            return false;
        }

        return ! $this->isBlockedDomain($host);
    }

    private function isBlockedDomain(string $host): bool
    {
        foreach (self::BLOCKED_DOMAINS as $blocked) {
            $blocked = strtolower($blocked);
            if ($host === $blocked) {
                return true;
            }
            // match subdomains: example => *.example
            if (substr($host, -(strlen($blocked) + 1)) === '.'.$blocked) {
                return true;
            }
        }

        return false;
    }
}
