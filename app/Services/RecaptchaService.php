<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RecaptchaService
{
    private ?string $siteKey;

    private ?string $secretKey;

    private float $scoreThreshold;

    public function __construct()
    {
        $this->siteKey = config('services.recaptcha.site_key');
        $this->secretKey = config('services.recaptcha.secret_key');
        $threshold = config('services.recaptcha.score_threshold', 0.5);
        $this->scoreThreshold = $threshold !== null ? (float) $threshold : 0.5;
    }

    /**
     * Verify reCAPTCHA v3 token
     *
     * @param  string  $token  The token from the frontend
     * @param  string  $action  The action name (should match frontend)
     * @return array ['success' => bool, 'score' => float|null, 'error' => string|null]
     */
    public function verify(string $token, string $action = 'submit'): array
    {
        if (! $this->isEnabled()) {
            return [
                'success' => false,
                'score' => null,
                'error' => 'reCAPTCHA secret key not configured',
            ];
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $this->secretKey,
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);

            if (! $response->successful()) {
                return [
                    'success' => false,
                    'score' => null,
                    'error' => 'Failed to connect to reCAPTCHA service',
                ];
            }

            $result = $response->json();

            // Check if verification was successful
            if (! ($result['success'] ?? false)) {
                return [
                    'success' => false,
                    'score' => null,
                    'error' => 'reCAPTCHA verification failed: '.implode(', ', $result['error-codes'] ?? ['unknown']),
                ];
            }

            // Check if action matches
            if (($result['action'] ?? '') !== $action) {
                return [
                    'success' => false,
                    'score' => $result['score'] ?? null,
                    'error' => 'reCAPTCHA action mismatch',
                ];
            }

            // Check score threshold
            $score = $result['score'] ?? 0.0;
            if ($score < $this->scoreThreshold) {
                return [
                    'success' => false,
                    'score' => $score,
                    'error' => 'reCAPTCHA score too low (possible bot)',
                ];
            }

            return [
                'success' => true,
                'score' => $score,
                'error' => null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'score' => null,
                'error' => 'reCAPTCHA verification error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Determine if reCAPTCHA is fully configured.
     */
    public function isEnabled(): bool
    {
        return ! empty($this->siteKey) && ! empty($this->secretKey);
    }

    /**
     * Get the site key for frontend use.
     */
    public function getSiteKey(): ?string
    {
        return $this->siteKey;
    }

    /**
     * Get the score threshold.
     */
    public function getScoreThreshold(): float
    {
        return $this->scoreThreshold;
    }
}
