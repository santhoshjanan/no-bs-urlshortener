<?php

declare(strict_types=1);

namespace Tests\Utilities;

use Illuminate\Support\Facades\Http;

trait MockServices
{
    protected function mockRecaptchaSuccess(): void
    {
        Http::fake([
            'www.google.com/recaptcha/api/siteverify*' => Http::response(['success' => true], 200),
            '*' => Http::response([], 200),
        ]);
    }

    protected function mockRecaptchaFailure(): void
    {
        Http::fake([
            'www.google.com/recaptcha/api/siteverify*' => Http::response(['success' => false], 200),
            '*' => Http::response([], 200),
        ]);
    }
}
