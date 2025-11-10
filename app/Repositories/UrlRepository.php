<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Url;
use Illuminate\Support\Facades\Cache;

class UrlRepository
{
    public function findByShortenedCode(string $code): ?Url
    {
        $key = "url:{$code}";

        return Cache::remember(
            $key,
            now()->addDays(14),
            fn () => Url::where('shortened_url', $code)->first()
        );
    }
}
