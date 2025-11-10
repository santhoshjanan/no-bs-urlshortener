<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class UrlRedirectService
{
    private UrlValidator $validator;

    public function __construct(UrlValidator $validator)
    {
        $this->validator = $validator;
    }

    public function safeRedirect(string $url): RedirectResponse
    {
        if (!$this->validator->isValid($url)) {
            abort(400, 'Invalid redirect URL');
        }

        // use away() so Laravel doesn't treat this as an internal route
        return Redirect::away($url, 302);
    }
}
