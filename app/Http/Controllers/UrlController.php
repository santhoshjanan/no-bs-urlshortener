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
        return view('index', $this->shorten($request));
    }

    public function api_shortener(Request $request){
        return response()->json($this->shorten($request), 201);
    }
    public function shorten(Request $request)
    {
        $request->validate([
            'original_url' => 'required|url',
            'g-recaptcha-response' => 'required|captcha'
            ],
            [
                'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
                'g-recaptcha-response.captcha' => 'Captcha error! Try again. If the problem persists, reach out to me using the contact icon in the footer.',
            ],
        );

        $shortened = $this->generateRandomString(4, 6);

        // Define cache key based on the shortened URL
        $cacheKey = "shortened_url:{$shortened}";

        // Attempt to retrieve the original URL from the cache

        $url = Url::create([
            'original_url' => $request->original_url,
            'shortened_url' => $shortened,
        ]);

        Cache::remember($cacheKey, now()->addDays(14), function () use ($url) {return $url;});

        return ['original_url'=>$request->original_url, 'shortened_url' => url($shortened)];
    }

    public function redirect($shortened)
    {
        // Define cache key based on the shortened URL
        $cacheKey = "shortened_url:{$shortened}";

        // Attempt to retrieve the original URL from the cache
        $originalUrl = Cache::remember($cacheKey, now()->addDays(14), function () use ($shortened) {
            $url = Url::where('shortened_url', $shortened)->firstOrFail();
            return $url->original_url;
        })->original_url;

        // Increment analytics if needed (optional)
        Url::where('shortened_url', $shortened)->increment('clicks');

        // Redirect to the original URL
        return redirect($originalUrl);
    }

    public function index()
    {
        return view('index');
    }
}
