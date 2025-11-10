<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Generate a unique nonce for this request
        $nonce = base64_encode(random_bytes(16));

        // Share the nonce with all views
        View::share('cspNonce', $nonce);

        /** @var Response $response */
        $response = $next($request);

        // Build CSP policy with nonce and third-party services
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' https://www.google.com/recaptcha/ https://www.gstatic.com/recaptcha/ https://www.googletagmanager.com https://www.clarity.ms",
            "style-src 'self' 'nonce-{$nonce}' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data: https://www.googletagmanager.com https://www.google-analytics.com",
            "connect-src 'self' https://www.google-analytics.com https://www.clarity.ms https://region1.google-analytics.com https://analytics.google.com",
            "frame-src https://www.google.com/recaptcha/",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "object-src 'none'",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);

        // Trusted Types - Start in report-only mode, then switch to enforcement
        // Report-only mode: monitors violations without breaking the app
        $trustedTypesPolicy = implode('; ', [
            "require-trusted-types-for 'script'",
            "trusted-types default recaptcha-policy clarity-policy"
        ]);

        // Use report-only mode for testing (comment out after testing)
        $response->headers->set('Content-Security-Policy-Report-Only', $trustedTypesPolicy);

        // Uncomment below to enforce Trusted Types after testing (and remove report-only above)
        // $response->headers->set('Content-Security-Policy', $csp . '; ' . $trustedTypesPolicy);

        return $response;
    }
}
