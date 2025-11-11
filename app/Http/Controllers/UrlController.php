<?php

namespace App\Http\Controllers;

use App\Models\Url;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use InvalidArgumentException;

class UrlController extends Controller
{
    public function __construct(private readonly RecaptchaService $recaptcha) {}

    public function generateRandomString(int $minLength, int $maxLength): string
    {
        if ($minLength > $maxLength) {
            throw new InvalidArgumentException('Minimum length cannot be greater than maximum length.');
        }

        // Generate a random length between the given range
        $length = rand($minLength, $maxLength);

        // Generate and return the random string
        return Str::random($length);
    }

    public function web_shortener(Request $request)
    {
        $validated = $request->validate([
            'original_url' => [
                'required',
                'url',
                'regex:/^https?:\/\//i',
            ],
            'minutes' => 'nullable|integer|min:0|max:525960',
            'recaptcha_token' => 'required|string',
        ],
            [
                'original_url.regex' => 'Only HTTP and HTTPS URLs are allowed.',
                'minutes.integer' => 'Minutes must be a valid number.',
                'minutes.min' => 'Minutes cannot be negative.',
                'minutes.max' => 'Minutes cannot exceed 525960 (365 days).',
                'recaptcha_token.required' => 'Please verify that you are not a robot.',
            ],
        );

        if (! $this->recaptcha->isEnabled()) {
            return back()
                ->withErrors(['recaptcha' => 'reCAPTCHA is not configured. Please contact support.'])
                ->withInput();
        }

        $verification = $this->recaptcha->verify($validated['recaptcha_token'], 'shorten_form');

        if (! ($verification['success'] ?? false)) {
            return back()
                ->withErrors(['recaptcha' => $verification['error'] ?? 'Unable to verify that you are human.'])
                ->withInput();
        }

        $minutes = (int) ($validated['minutes'] ?? 0);

        return view('index', $this->createShortUrl($validated['original_url'], $minutes));
    }

    public function api_shortener(Request $request)
    {
        $request->validate([
            'original_url' => [
                'required',
                'url',
                'regex:/^https?:\/\//i',
            ],
            'minutes' => 'nullable|integer|min:0|max:525960',
        ], [
            'original_url.regex' => 'Only HTTP and HTTPS URLs are allowed.',
            'minutes.integer' => 'Minutes must be a valid number.',
            'minutes.min' => 'Minutes cannot be negative.',
            'minutes.max' => 'Minutes cannot exceed 525960 (365 days).',
        ]);

        $minutes = $request->input('minutes', 0);

        return response()->json($this->createShortUrl($request->original_url, $minutes), 201);
    }

    protected function createShortUrl(string $originalUrl, int $minutes = 0): array
    {
        // Retry up to 10 times to handle collisions
        $maxAttempts = 10;
        $url = null;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            try {
                $shortened = $this->generateRandomString(4, 6);
                $cacheKey = "shortened_url:{$shortened}";

                // If minutes > 0, this is a temporary URL - store only in Redis
                if ($minutes > 0) {
                    // Check if this shortened code already exists in Redis or DB
                    if (Cache::has($cacheKey) || Url::where('shortened_url', $shortened)->exists()) {
                        // Collision detected, retry
                        continue;
                    }

                    // Store only in Redis with expiry
                    Cache::put($cacheKey, $originalUrl, now()->addMinutes($minutes));

                    return ['original_url' => $originalUrl, 'shortened_url' => url($shortened)];
                }

                // If minutes = 0, this is a permanent URL - normal flow (DB + cache)
                $url = Url::create([
                    'original_url' => $originalUrl,
                    'shortened_url' => $shortened,
                ]);

                // Cache for 14 days
                Cache::put($cacheKey, $url->original_url, now()->addDays(14));

                return ['original_url' => $originalUrl, 'shortened_url' => url($shortened)];
            } catch (\Illuminate\Database\QueryException $e) {
                // If it's a unique constraint violation, retry with a new code
                if ($e->getCode() === '23505' || str_contains($e->getMessage(), 'unique')) {
                    continue;
                }
                // If it's a different error, rethrow it
                throw $e;
            }
        }

        // If we exhausted all attempts, throw an error
        throw new \RuntimeException('Unable to generate a unique shortened URL after '.$maxAttempts.' attempts.');
    }

    public function redirect(Request $request, $shortened)
    {
        // Define cache key based on the shortened URL
        $cacheKey = "shortened_url:{$shortened}";

        // First, try to get from Redis cache (handles both temporary and permanent URLs)
        $originalUrl = Cache::get($cacheKey);

        // If not in cache, try to get from database (permanent URLs only)
        if (! $originalUrl) {
            $url = Url::where('shortened_url', $shortened)->first();

            if ($url) {
                // Found in database - cache it and use it
                $originalUrl = $url->original_url;
                Cache::put($cacheKey, $originalUrl, now()->addDays(14));
            } else {
                // Not found in cache or database - URL doesn't exist or has expired
                abort(404, 'Shortened URL not found or has expired');
            }
        }

        // Collect privacy-friendly analytics only for permanent URLs (those in DB)
        $url = Url::where('shortened_url', $shortened)->first();
        if ($url) {
            $analytics = $url->analytics ?? [];

            // Add click timestamp and basic browser info (no IP, no full user agent)
            $analytics[] = [
                'timestamp' => now()->toIso8601String(),
                'referer_domain' => $request->headers->get('referer') ? parse_url($request->headers->get('referer'), PHP_URL_HOST) : null,
            ];

            // Keep only last 100 clicks to prevent unbounded growth
            if (count($analytics) > 100) {
                $analytics = array_slice($analytics, -100);
            }

            $url->increment('clicks');
            $url->update(['analytics' => $analytics]);
        }

        // Redirect to the original URL
        return redirect($originalUrl);
    }

    public function index()
    {
        return view('index');
    }
}
