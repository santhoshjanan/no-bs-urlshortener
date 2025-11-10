<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Url;
use App\Repositories\UrlRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UrlShorteningService
{
    private UrlRepository $urlRepository;

    private CacheService $cacheService;

    private const MAX_ATTEMPTS = 5;

    private const CODE_LENGTH = 6;

    public function __construct(UrlRepository $urlRepository, CacheService $cacheService)
    {
        $this->urlRepository = $urlRepository;
        $this->cacheService = $cacheService;
    }

    public function createShortenedUrl(string $targetUrl, int $attempt = 0): Url
    {
        if ($attempt >= self::MAX_ATTEMPTS) {
            throw new Exception('Unable to generate unique short code');
        }

        $code = Str::random(self::CODE_LENGTH);

        return DB::transaction(function () use ($code, $targetUrl, $attempt) {
            // check collision
            if ($this->urlRepository->findByShortenedCode($code) !== null) {
                return $this->createShortenedUrl($targetUrl, $attempt + 1);
            }

            $url = new Url;
            $url->target_url = $targetUrl;
            $url->shortened_url = $code;
            $url->save();

            // prime cache
            $this->cacheService->putUrl($code, $url);

            return $url;
        });
    }
}
