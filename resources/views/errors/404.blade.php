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
    <title>404 - URL Not Found | No BS URL Shortener</title>
    <meta name="description" content="The shortened URL you're looking for doesn't exist or has expired.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
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

        .error-404 {
            font-size: 10rem;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            margin-bottom: 2rem;
            background: var(--vb-danger);
            border: var(--vb-border);
            box-shadow: var(--vb-shadow-xl);
            padding: 2rem;
            color: var(--vb-white);
        }

        @media (max-width: 768px) {
            .error-404 {
                font-size: 6rem;
            }
        }
    </style>
</head>
<body>
    <main>
        <div class="vb-container" style="max-width: 800px;">
            <div class="vb-text-center">
                <div class="error-404">404</div>

                <div class="vb-card">
                    <div class="vb-card-header" style="background: var(--vb-black); color: var(--vb-white);">
                        URL NOT FOUND
                    </div>
                    <div class="vb-card-body">
                        <p style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem;">
                            The shortened URL you're looking for doesn't exist or may have expired.
                        </p>
                        <p style="margin-bottom: 2rem;">
                            Double-check the URL or create a new one below.
                        </p>
                        <a href="{{ route('index') }}" class="vb-btn vb-btn-primary vb-btn-lg vb-btn-block">
                            GO TO HOMEPAGE
                        </a>
                    </div>
                </div>

                <div style="margin-top: 2rem; padding: 1.5rem; background: var(--vb-primary); border: var(--vb-border); box-shadow: var(--vb-shadow-md); font-weight: 600;">
                    💡 TIP: Make sure you copied the entire shortened URL correctly!
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
                <a href="mailto:viscous.buys4y@icloud.com" class="vb-footer-icon" title="Feedback: Send me your feedback!">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M2 2A2 2 0 0 0 .05 3.555L8 8.414l7.95-4.859A2 2 0 0 0 14 2zm-2 9.8V4.698l5.803 3.546zm6.761-2.97-6.57 4.026A2 2 0 0 0 2 14h6.256A4.5 4.5 0 0 1 8 12.5a4.49 4.49 0 0 1 1.606-3.446l-.367-.225L8 9.586zM16 9.671V4.697l-5.803 3.546.338.208A4.5 4.5 0 0 1 12.5 8c1.414 0 2.675.652 3.5 1.671"/>
                        <path d="M15.834 12.244c0 1.168-.577 2.025-1.587 2.025-.503 0-1.002-.228-1.12-.648h-.043c-.118.416-.543.643-1.015.643-.77 0-1.259-.542-1.259-1.434v-.529c0-.844.481-1.4 1.26-1.4.585 0 .87.333.953.63h.03v-.568h.905v2.19c0 .272.18.42.411.42.315 0 .639-.415.639-1.39v-.118c0-1.277-.95-2.326-2.484-2.326h-.04c-1.582 0-2.64 1.067-2.64 2.724v.157c0 1.867 1.237 2.654 2.57 2.654h.045c.507 0 .935-.07 1.18-.18v.731c-.219.1-.643.175-1.237.175h-.044C10.438 16 9 14.82 9 12.646v-.214C9 10.36 10.421 9 12.485 9h.035c2.12 0 3.314 1.43 3.314 3.034zm-4.04.21v.227c0 .586.227.8.581.8.31 0 .564-.17.564-.743v-.367c0-.516-.275-.708-.572-.708-.346 0-.573.245-.573.791"/>
                    </svg>
                </a>
                <a href="//x.com/santhoshj" target="_blank" class="vb-footer-icon" title="Follow me on Twitter!">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/>
                    </svg>
                </a>
            </div>
        </div>
    </footer>
</body>
</html>
