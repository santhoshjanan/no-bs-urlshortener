<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Trusted Types Policy - Must be loaded first -->
    <script nonce="{{ $cspNonce }}">
        // Create Trusted Types policies for safe script loading
        if (window.trustedTypes && trustedTypes.createPolicy) {
            // Default policy for general use
            trustedTypes.createPolicy('default', {
                createScriptURL: (url) => {
                    // Allow same-origin and whitelisted external scripts
                    const allowedOrigins = [
                        location.origin,
                        'https://www.google.com',
                        'https://www.gstatic.com',
                        'https://www.googletagmanager.com',
                        'https://www.clarity.ms'
                    ];

                    try {
                        const urlObj = new URL(url, location.origin);
                        if (allowedOrigins.some(origin => urlObj.href.startsWith(origin))) {
                            return urlObj.href;
                        }
                    } catch (e) {
                        console.error('Invalid URL for script:', url);
                    }
                    throw new TypeError('Script URL not allowed: ' + url);
                },
                createHTML: (html) => html, // Allow HTML (can be restricted further if needed)
                createScript: (script) => script // Allow inline scripts
            });

            // Specific policy for reCAPTCHA
            trustedTypes.createPolicy('recaptcha-policy', {
                createScriptURL: (url) => {
                    if (url.startsWith('https://www.google.com/recaptcha/') ||
                        url.startsWith('https://www.gstatic.com/recaptcha/')) {
                        return url;
                    }
                    throw new TypeError('Only reCAPTCHA scripts allowed');
                }
            });

            // Specific policy for Microsoft Clarity
            trustedTypes.createPolicy('clarity-policy', {
                createScriptURL: (url) => {
                    if (url.startsWith('https://www.clarity.ms/tag/')) {
                        return url;
                    }
                    throw new TypeError('Only Clarity scripts allowed');
                }
            });
        }
    </script>

    <!-- Preconnect to external domains for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://www.googletagmanager.com">
    <link rel="dns-prefetch" href="https://www.clarity.ms">

    <!-- Google Fonts: Space Grotesk with font-display swap for performance -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">

    @if(env('GOOGLE_ANALYTICS_ID'))
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GOOGLE_ANALYTICS_ID') }}"></script>
    <script nonce="{{ $cspNonce }}">
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ env('GOOGLE_ANALYTICS_ID') }}');
    </script>
    @endif
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
    <script type="application/ld+json" nonce="{{ $cspNonce }}">
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

    @if(env('MICROSOFT_CLARITY_ID'))
    <script type="text/javascript" nonce="{{ $cspNonce }}">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);
            t.async=1;

            // Use Trusted Types policy if available
            var clarityURL = "https://www.clarity.ms/tag/"+i;
            if (window.trustedTypes && trustedTypes.createPolicy) {
                var policy = trustedTypes.defaultPolicy || trustedTypes.createPolicy('clarity-policy', {
                    createScriptURL: function(url) { return url; }
                });
                t.src = policy.createScriptURL(clarityURL);
            } else {
                t.src = clarityURL;
            }

            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "{{ env('MICROSOFT_CLARITY_ID') }}");
    </script>
    @endif

    <style nonce="{{ $cspNonce }}">
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
