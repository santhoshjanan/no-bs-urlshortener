<!DOCTYPE html>
<html lang="en">
<head>
    @if(env('GOOGLE_ANALYTICS_ID'))
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GOOGLE_ANALYTICS_ID') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ env('GOOGLE_ANALYTICS_ID') }}');
    </script>
    @endif
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

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
      "@context": "https://schema.org",
      "@type": "WebApplication",
      "name": "No BS URL Shortener",
      "description": "Privacy-first URL shortening service - Just does one job, and does it well",
      "url": "{{ url('/') }}",
      "applicationCategory": "UtilityApplication",
      "operatingSystem": "Web",
      "offers": {
        "@type": "Offer",
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
        "@type": "Person",
        "name": "Santhosh J"
      }
    }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('scripts')

    @if(env('MICROSOFT_CLARITY_ID'))
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "{{ env('MICROSOFT_CLARITY_ID') }}");
    </script>
    @endif

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

        .vb-nav {
            background-color: var(--vb-white);
            border-bottom: var(--vb-border);
            box-shadow: 0 6px 0 var(--vb-black);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .vb-nav-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .vb-nav-logo {
            font-weight: 900;
            font-size: 1.25rem;
            text-decoration: none;
            color: var(--vb-black);
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }

        .vb-nav-logo:hover {
            color: var(--vb-primary);
        }

        .vb-nav-menu {
            display: flex;
            gap: 0.5rem;
            list-style: none;
            margin: 0;
            padding: 0;
            align-items: center;
            flex-wrap: wrap;
        }

        .vb-nav-link {
            padding: 0.5rem 1rem;
            text-decoration: none;
            color: var(--vb-black);
            font-weight: 700;
            font-size: 0.875rem;
            text-transform: uppercase;
            border: 2px solid transparent;
            transition: all 0.15s ease;
        }

        .vb-nav-link:hover,
        .vb-nav-link.active {
            background: var(--vb-primary);
            border: 2px solid var(--vb-black);
            box-shadow: 2px 2px 0 var(--vb-black);
        }

        @media (max-width: 768px) {
            .vb-nav-content {
                flex-direction: column;
            }

            .vb-nav-menu {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="vb-nav">
        <div class="vb-nav-content">
            <a href="{{ route('index') }}" class="vb-nav-logo">No BS URL Shortener</a>
            <ul class="vb-nav-menu">
                <li><a href="{{ route('index') }}" class="vb-nav-link {{ request()->routeIs('index') ? 'active' : '' }}">Home</a></li>
                <li><a href="{{ route('about') }}" class="vb-nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a></li>
                <li><a href="{{ route('faq') }}" class="vb-nav-link {{ request()->routeIs('faq') ? 'active' : '' }}">FAQ</a></li>
                <li><a href="{{ route('privacy') }}" class="vb-nav-link {{ request()->routeIs('privacy') ? 'active' : '' }}">Privacy</a></li>
                <li><a href="{{ route('terms') }}" class="vb-nav-link {{ request()->routeIs('terms') ? 'active' : '' }}">Terms</a></li>
            </ul>
        </div>
    </nav>
