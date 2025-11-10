<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UrlRepository;
use App\Services\UrlValidationService;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class UrlRedirectService
{
    private UrlRepository $urlRepository;
    private UrlValidationService $validator;
    private AnalyticsService $analyticsService;

    public function __construct(
        UrlRepository $urlRepository,
        UrlValidationService $validator,
        AnalyticsService $analyticsService
    ) {
        $this->urlRepository = $urlRepository;
        $this->validator = $validator;
        $this->analyticsService = $analyticsService;
    }

    public function handleRedirect(string $code, Request $request): RedirectResponse
    {
        $url = $this->urlRepository->findByShortenedCode($code);

        if ($url === null) {
            // record a 404 hit for analysis/monitoring (implementation in AnalyticsService)
            $this->analyticsService->track404($code, $request);
            abort(404);
        }

        // record redirect (privacy-friendly) and ensure single DB/cache op
        $this->analyticsService->trackRedirect($url, $request);

        // prefer original_url field; fall back to legacy target_url if present
        $destination = $url->original_url ?? $url->target_url ?? null;
        if ($destination === null || !$this->validator->isValid((string) $destination)) {
            // defensive: treat invalid destination as not found
            $this->analyticsService->track404($code, $request);
            abort(404);
        }

        return $this->safeRedirect((string) $destination);
    }

    private function safeRedirect(string $url): RedirectResponse
    {
        // away() prevents Laravel from treating it as an internal route
        return Redirect::away($url, 302);
    }
}
