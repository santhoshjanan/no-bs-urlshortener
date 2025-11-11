<!DOCTYPE html>
<html lang="en">
<head>
    @php($recaptchaSiteKey = config('services.recaptcha.site_key'))

    <!-- Analytics configuration (lazy-loaded for performance) -->
    @if(env('GOOGLE_ANALYTICS_ID'))
        <meta name="google-analytics-id" content="{{env('GOOGLE_ANALYTICS_ID')}}">
    @endif
    @if(env('MICROSOFT_CLARITY_ID'))
        <meta name="clarity-id" content="{{env('MICROSOFT_CLARITY_ID')}}">
    @endif
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if($recaptchaSiteKey)
        <meta name="recaptcha-site-key" content="{{ $recaptchaSiteKey }}">
        <link rel="preconnect" href="https://www.google.com" crossorigin>
        <link rel="preconnect" href="https://www.gstatic.com" crossorigin>
    @endif

    <link rel="preload" href="{{ asset('fonts/space-grotesk/SpaceGrotesk-Variable.woff2') }}" as="font" type="font/woff2" crossorigin>

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('build/manifest.webmanifest') }}">
    <meta name="theme-color" content="#000000">

    <!-- Primary Meta Tags -->
    <title>{{ $title ?? 'No BS URL Shortener - Privacy-First Link Shortening' }}</title>
    <meta name="description" content="{{ $description ?? 'Privacy-first URL shortening service with no tracking. Anonymous, fast, and secure. Free API available.' }}">
    <meta name="keywords" content="URL shortener, link shortener, privacy, anonymous url shortener, free url shortener, url shortener api">
    <meta name="author" content="No BS URL Shortener">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'No BS URL Shortener - Privacy-First Link Shortening' }}">
    <meta property="og:description" content="{{ $description ?? 'Privacy-first URL shortening service with no tracking. Anonymous, fast, and secure. Free API available.' }}">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $title ?? 'No BS URL Shortener - Privacy-First Link Shortening' }}">
    <meta name="twitter:description" content="{{ $description ?? 'Privacy-first URL shortening service with no tracking. Anonymous, fast, and secure. Free API available.' }}">
    <meta name="twitter:image" content="{{ asset('images/og-image.jpg') }}">

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "WebApplication",
      "name": "No BS URL Shortener",
      "description": "Privacy-first URL shortening service - Just does one job, and does it well",
      "url": "{{ url('/') }}",
      "applicationCategory": "UtilityApplication",
      "operatingSystem": "Web",
      "offers": {
        "@@type": "Offer",
        "price": "0",
        "priceCurrency": "USD"
      },
      "featureList": [
        "Anonymous URL shortening",
        "Privacy-first analytics",
        "RESTful API",
        "No personal data tracking",
        "Redis caching for fast performance",
        "Temporary URL support with expiration"
      ],
      "creator": {
        "@@type": "Person",
        "name": "Santhosh J"
      }
    }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('scripts')

    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
            padding-bottom: 100px;
        }

        /* Custom styling for navbar to match site design */
        .vb-navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* Active link styling */
        .vb-navbar-link.active {
            background: var(--vb-primary);
            border: 2px solid var(--vb-black);
            box-shadow: 2px 2px 0 var(--vb-black);
        }
    </style>
</head>
<body>
    <!-- Skip link for keyboard users -->
    <a href="#main-content" class="vb-skip-link">Skip to main content</a>

    <!-- Navigation -->
    <nav class="vb-navbar" role="navigation" aria-label="Main navigation">
        <a href="{{ route('index') }}" class="vb-navbar-brand">No BS URL Shortener</a>

        <!-- Hamburger toggle (auto-hidden on desktop) -->
        <button class="vb-navbar-toggle" type="button" aria-label="Toggle navigation menu">
            <span class="vb-navbar-toggle-bar"></span>
            <span class="vb-navbar-toggle-bar"></span>
            <span class="vb-navbar-toggle-bar"></span>
        </button>

        <ul class="vb-navbar-menu">
            <li><a href="{{ route('index') }}" class="vb-navbar-link {{ request()->routeIs('index') ? 'active' : '' }}">Home</a></li>
            <li><a href="{{ route('about') }}" class="vb-navbar-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a></li>
            <li><a href="{{ route('faq') }}" class="vb-navbar-link {{ request()->routeIs('faq') ? 'active' : '' }}">FAQ</a></li>
            <li><a href="{{ route('privacy') }}" class="vb-navbar-link {{ request()->routeIs('privacy') ? 'active' : '' }}">Privacy</a></li>
            <li><a href="{{ route('terms') }}" class="vb-navbar-link {{ request()->routeIs('terms') ? 'active' : '' }}">Terms</a></li>
        </ul>
    </nav>

    <main id="main-content">
