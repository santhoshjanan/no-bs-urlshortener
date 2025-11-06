<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use InvalidArgumentException;

class UrlController extends Controller
{

    function generateRandomString(int $minLength, int $maxLength): string
    {
        if ($minLength > $maxLength) throw new InvalidArgumentException("Minimum length cannot be greater than maximum length.");

        // Generate a random length between the given range
        $length = rand($minLength, $maxLength);

        // Generate and return the random string
        return Str::random($length);
    }
    public function web_shortener(Request $request)
    {
        $request->validate([
            'original_url' => [
                'required',
                'url',
                'regex:/^https?:\/\//i'
            ],
            'g-recaptcha-response' => 'required|captcha'
            ],
            [
                'original_url.regex' => 'Only HTTP and HTTPS URLs are allowed.',
                'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
                'g-recaptcha-response.captcha' => 'Captcha error! Try again. If the problem persists, reach out to me using the contact icon in the footer.',
            ],
        );

        return view('index', $this->createShortUrl($request->original_url));
    }

    public function api_shortener(Request $request){
        $request->validate([
            'original_url' => [
                'required',
                'url',
                'regex:/^https?:\/\//i'
            ],
        ], [
            'original_url.regex' => 'Only HTTP and HTTPS URLs are allowed.',
        ]);

        return response()->json($this->createShortUrl($request->original_url), 201);
    }

    protected function createShortUrl(string $originalUrl): array
    {

        // Retry up to 10 times to handle collisions
        $maxAttempts = 10;
        $url = null;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            try {
                $shortened = $this->generateRandomString(4, 6);

                $url = Url::create([
                    'original_url' => $originalUrl,
                    'shortened_url' => $shortened,
                ]);

                // Define cache key based on the shortened URL
                $cacheKey = "shortened_url:{$shortened}";
                Cache::put($cacheKey, $url->original_url, now()->addDays(14));

                return ['original_url'=>$originalUrl, 'shortened_url' => url($shortened)];
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
        throw new \RuntimeException('Unable to generate a unique shortened URL after ' . $maxAttempts . ' attempts.');
    }

    public function redirect(Request $request, $shortened)
    {
        // Define cache key based on the shortened URL
        $cacheKey = "shortened_url:{$shortened}";

        // Attempt to retrieve the original URL from the cache
        $originalUrl = Cache::remember($cacheKey, now()->addDays(14), function () use ($shortened) {
            $url = Url::where('shortened_url', $shortened)->firstOrFail();
            return $url->original_url;
        });

        // Collect privacy-friendly analytics (no personal data)
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
