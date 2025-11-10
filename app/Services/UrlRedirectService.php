<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UrlRepository;
use App\Services\UrlValidationService;
use App\Events\UrlRedirected;
use Illuminate\Support\Facades\Event;
use App\Models\Url;
use Symfony\Component\HttpFoundation\Response;

class UrlRedirectService
{
    private UrlRepository $urlRepository;
    private UrlValidationService $validator;

    public function __construct(UrlRepository $urlRepository, UrlValidationService $validator)
    {
        $this->urlRepository = $urlRepository;
        $this->validator = $validator;
    }

    public function resolveTargetByCode(string $code): ?string
    {
        $url = $this->urlRepository->findByShortenedCode($code);
        if ($url === null) {
            return null;
        }

        if (!$this->validator->isValid($url->target_url)) {
            return null;
        }

        Event::dispatch(new UrlRedirected($url));

        return $url->target_url;
    }
}
