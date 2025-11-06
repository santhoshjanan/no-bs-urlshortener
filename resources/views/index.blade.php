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
    <title>No BS URL Shortener - Privacy-First Link Shortening</title>
    <meta name="description" content="Privacy-first URL shortening service with no tracking. Anonymous, fast, and secure. Free API available.">
    <meta name="keywords" content="URL shortener, link shortener, privacy, anonymous url shortener, free url shortener, url shortener api">
    <meta name="author" content="No BS URL Shortener">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="No BS URL Shortener - Privacy-First Link Shortening">
    <meta property="og:description" content="Privacy-first URL shortening service with no tracking. Anonymous, fast, and secure. Free API available.">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="No BS URL Shortener - Privacy-First Link Shortening">
    <meta name="twitter:description" content="Privacy-first URL shortening service with no tracking. Anonymous, fast, and secure. Free API available.">
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

    {!! NoCaptcha::renderJs() !!}

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

        .vb-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: var(--vb-white);
            border-top: var(--vb-border);
            box-shadow: 0 -6px 0 var(--vb-black);
            padding: 1rem;
            z-index: 100;
        }

        .vb-footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .vb-footer-left {
            font-weight: 700;
            font-size: 0.875rem;
        }

        .vb-footer-right {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .vb-footer-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: var(--vb-white);
            border: 2px solid var(--vb-black);
            box-shadow: 2px 2px 0 var(--vb-black);
            transition: all 0.15s ease;
            text-decoration: none;
            color: var(--vb-black);
        }

        .vb-footer-icon:hover {
            transform: translate(1px, 1px);
            box-shadow: 1px 1px 0 var(--vb-black);
            background: var(--vb-primary);
        }

        @media (max-width: 768px) {
            .vb-footer-content {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <main>
        <div class="vb-container" style="margin-top: 3rem;">
            <!-- Header -->
            <div class="vb-text-center vb-mb-4">
                <h1 class="vb-h1" style="font-size: 3rem; margin-bottom: 1rem;">
                    NO BS<br>URL SHORTENER
                </h1>
                <div style="background: var(--vb-primary); border: var(--vb-border); box-shadow: var(--vb-shadow-md); padding: 1.5rem; margin: 2rem auto; max-width: 800px; font-size: 1.125rem; font-weight: 600;">
                    I understand you are here to get a shortened URL.<br>
                    And that's what you get from this site. <span style="font-size: 1.5rem;">NO BS! 🔥</span>
                </div>
            </div>

            <!-- Main Card -->
            <div class="vb-card-static" style="max-width: 900px; margin: 0 auto;">
                <div class="vb-card-body">
                    <form action="{{ route('shorten') }}" method="POST">
                        @csrf

                        <!-- URL Input Group -->
                        <div class="vb-form-group">
                            <label class="vb-label" for="original_url">Enter URL to shorten:</label>
                            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                <input
                                    type="url"
                                    name="original_url"
                                    id="original_url"
                                    class="vb-input"
                                    placeholder="https://example.com/your/long/url"
                                    value="{{ $original_url ?? old('original_url') }}"
                                    required
                                    style="flex: 1; min-width: 250px;"
                                >
                                <button type="submit" class="vb-btn vb-btn-primary vb-btn-lg" style="min-width: 150px;">
                                    SHORTEN!
                                </button>
                            </div>
                        </div>

                        <!-- Minutes Input -->
                        <div class="vb-form-group">
                            <label class="vb-label" for="minutes">Minutes:</label>
                            <input
                                type="number"
                                name="minutes"
                                id="minutes"
                                class="vb-input"
                                min="0"
                                max="525960"
                                value="0"
                                style="max-width: 200px;"
                            >
                            <small style="display: block; margin-top: 0.5rem; color: var(--vb-black); opacity: 0.7;">
                                Set 0 for permanent one. Set timer for a temporary url.
                            </small>
                        </div>

                        <!-- reCAPTCHA -->
                        <div class="vb-form-group">
                            {!! NoCaptcha::display() !!}
                        </div>
                    </form>

                    <!-- Shortened URL Result -->
                    @if (isset($shortened_url))
                        <div style="margin-top: 2rem; padding: 2rem; background: var(--vb-success); border: var(--vb-border); box-shadow: var(--vb-shadow-lg);">
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                                <span class="vb-h4" style="margin: 0;">YOUR SHORTENED URL:</span>
                                <span style="cursor: help;" title="Since there is no session saved, the shortened URL will be shown only once. Once you refresh the page, you cannot retrieve it. But you can create a new one. Don't forget to copy it! 🔥">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                      <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2"/>
                                    </svg>
                                </span>
                            </div>
                            <div style="display: flex; gap: 1rem; align-items: stretch; flex-wrap: wrap;">
                                <input
                                    type="text"
                                    class="vb-input"
                                    value="{{ $shortened_url }}"
                                    readonly
                                    id="shortened-url-display"
                                    style="flex: 1; min-width: 250px; background: var(--vb-white);"
                                >
                                <button
                                    type="button"
                                    class="vb-btn vb-btn-accent vb-btn-lg"
                                    onclick="copyToClipboard('{{ $shortened_url }}')"
                                    style="min-width: 120px;"
                                >
                                    COPY
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Errors -->
                    @if ($errors->any())
                        <div class="vb-alert vb-alert-danger" style="margin-top: 2rem;">
                            <div class="vb-h5" style="margin-bottom: 0.5rem;">⚠ ERRORS:</div>
                            <ul style="margin: 0; padding-left: 1.5rem;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="vb-footer">
        <div class="vb-footer-content">
            <div class="vb-footer-left">
                Made with ❤️ and Laravel, in New Jersey! &copy; {{ date("Y") }}
            </div>
            <div class="vb-footer-right">
                <a href="#" class="vb-footer-icon" title="Stack: Laravel, Redis and Postgres">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8.235 1.559a.5.5 0 0 0-.47 0l-7.5 4a.5.5 0 0 0 0 .882L3.188 8 .264 9.559a.5.5 0 0 0 0 .882l7.5 4a.5.5 0 0 0 .47 0l7.5-4a.5.5 0 0 0 0-.882L12.813 8l2.922-1.559a.5.5 0 0 0 0-.882zM8 9.433 1.562 6 8 2.567 14.438 6z"/>
                    </svg>
                </a>
                <a href="#" class="vb-footer-icon" title="How: I use this app a lot. So sharing with the world. I dont lose anything 🤷🏼‍♂️">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M5.933.87a2.89 2.89 0 0 1 4.134 0l.622.638.89-.011a2.89 2.89 0 0 1 2.924 2.924l-.01.89.636.622a2.89 2.89 0 0 1 0 4.134l-.637.622.011.89a2.89 2.89 0 0 1-2.924 2.924l-.89-.01-.622.636a2.89 2.89 0 0 1-4.134 0l-.622-.637-.89.011a2.89 2.89 0 0 1-2.924-2.924l.01-.89-.636-.622a2.89 2.89 0 0 1 0-4.134l.637-.622-.011-.89a2.89 2.89 0 0 1 2.924-2.924l.89.01zM7.002 11a1 1 0 1 0 2 0 1 1 0 0 0-2 0m1.602-2.027c.04-.534.198-.815.846-1.26.674-.475 1.05-1.09 1.05-1.986 0-1.325-.92-2.227-2.262-2.227-1.02 0-1.792.492-2.1 1.29A1.7 1.7 0 0 0 6 5.48c0 .393.203.64.545.64.272 0 .455-.147.564-.51.158-.592.525-.915 1.074-.915.61 0 1.03.446 1.03 1.084 0 .563-.208.885-.822 1.325-.619.433-.926.914-.926 1.64v.111c0 .428.208.745.585.745.336 0 .504-.24.554-.627"/>
                    </svg>
                </a>
                <a href="#" class="vb-footer-icon" title="Privacy: I collect basic statistics of page. None of the personal identification data nor user input are recorded.">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M5.338 1.59a61 61 0 0 0-2.837.856.48.48 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.7 10.7 0 0 0 2.287 2.233c.346.244.652.42.893.533q.18.085.293.118a1 1 0 0 0 .101.025 1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56"/>
                        <path d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                    </svg>
                </a>
                <a href="mailto:viscous.buys4y@icloud.com" class="vb-footer-icon" title="Feedback: Please click on this icon to send me your feedback and/or feature request. I will be happy to hear from you!">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M2 2A2 2 0 0 0 .05 3.555L8 8.414l7.95-4.859A2 2 0 0 0 14 2zm-2 9.8V4.698l5.803 3.546zm6.761-2.97-6.57 4.026A2 2 0 0 0 2 14h6.256A4.5 4.5 0 0 1 8 12.5a4.49 4.49 0 0 1 1.606-3.446l-.367-.225L8 9.586zM16 9.671V4.697l-5.803 3.546.338.208A4.5 4.5 0 0 1 12.5 8c1.414 0 2.675.652 3.5 1.671"/>
                        <path d="M15.834 12.244c0 1.168-.577 2.025-1.587 2.025-.503 0-1.002-.228-1.12-.648h-.043c-.118.416-.543.643-1.015.643-.77 0-1.259-.542-1.259-1.434v-.529c0-.844.481-1.4 1.26-1.4.585 0 .87.333.953.63h.03v-.568h.905v2.19c0 .272.18.42.411.42.315 0 .639-.415.639-1.39v-.118c0-1.277-.95-2.326-2.484-2.326h-.04c-1.582 0-2.64 1.067-2.64 2.724v.157c0 1.867 1.237 2.654 2.57 2.654h.045c.507 0 .935-.07 1.18-.18v.731c-.219.1-.643.175-1.237.175h-.044C10.438 16 9 14.82 9 12.646v-.214C9 10.36 10.421 9 12.485 9h.035c2.12 0 3.314 1.43 3.314 3.034zm-4.04.21v.227c0 .586.227.8.581.8.31 0 .564-.17.564-.743v-.367c0-.516-.275-.708-.572-.708-.346 0-.573.245-.573.791"/>
                    </svg>
                </a>
                <a href="//x.com/santhoshj" target="_blank" class="vb-footer-icon" title="Easiest way to reach me is Twitter. (X is a social media app. Twitter is an emotion. Twitter daaw!!💪💪💪)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/>
                    </svg>
                </a>
            </div>
        </div>
    </footer>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Use vibe-brutalism toast
                VB.toast('Copied to clipboard!', 'success', 3000);
            }).catch(err => {
                console.error('Failed to copy:', err);
                VB.toast('Failed to copy', 'danger', 3000);
            });
        }
    </script>
</body>
</html>
