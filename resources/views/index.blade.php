@push('scripts')
    <script nonce="{{ $cspNonce }}">
        // Lazy load reCAPTCHA only when user interacts with the form
        let recaptchaLoaded = false;

        function loadRecaptcha() {
            if (recaptchaLoaded) return;
            recaptchaLoaded = true;

            // Load reCAPTCHA API with Trusted Types support
            const script = document.createElement('script');

            // Use Trusted Types policy if available
            const scriptURL = 'https://www.google.com/recaptcha/api.js?render=explicit';
            if (window.trustedTypes && trustedTypes.createPolicy) {
                const policy = trustedTypes.defaultPolicy || trustedTypes.createPolicy('recaptcha-policy', {
                    createScriptURL: (url) => url
                });
                script.src = policy.createScriptURL(scriptURL);
            } else {
                script.src = scriptURL;
            }

            script.async = true;
            script.defer = true;
            script.onload = function() {
                // Render reCAPTCHA when script is loaded
                if (window.grecaptcha) {
                    grecaptcha.render('recaptcha-container', {
                        'sitekey': '{{ config('no-captcha.sitekey') }}'
                    });
                }
            };
            document.head.appendChild(script);
        }

        // Load reCAPTCHA when user focuses on any form input
        document.addEventListener('DOMContentLoaded', function() {
            const formInputs = document.querySelectorAll('#original_url, #minutes');
            formInputs.forEach(input => {
                input.addEventListener('focus', loadRecaptcha, { once: true });
            });

            // Also load on form hover as a backup
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('mouseenter', loadRecaptcha, { once: true });
            }
        });
    </script>
@endpush

@include('partials.header')
        <div class="vb-container" style="margin-top: 3rem;">
            <!-- Header -->
            <div class="vb-text-center vb-mb-4">
                <h1 class="vb-h1" style="font-size: 3rem; margin-bottom: 1rem;">
                    NO BS<br>URL SHORTENER
                </h1>
                <div style="background: var(--vb-primary); border: var(--vb-border); box-shadow: var(--vb-shadow-md); padding: 1.5rem; margin: 2rem auto; max-width: 800px; font-size: 1.125rem; font-weight: 600; color: var(--vb-black);">
                    I understand you are here to get a shortened URL.<br>
                    And that's what you get from this site. <span style="font-size: 1.5rem; color: var(--vb-black);">NO BS! 🔥</span>
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

                        <!-- reCAPTCHA (lazy loaded) -->
                        <div class="vb-form-group">
                            <div id="recaptcha-container" class="g-recaptcha" data-sitekey="{{ config('no-captcha.sitekey') }}"></div>
                            <noscript>
                                <div style="padding: 1rem; background: var(--vb-warning); border: var(--vb-border);">
                                    Please enable JavaScript to use reCAPTCHA.
                                </div>
                            </noscript>
                        </div>
                    </form>

                    <!-- Shortened URL Result -->
                    @if (isset($shortened_url))
                        <div style="margin-top: 2rem; padding: 2rem; background: var(--vb-success); border: var(--vb-border); box-shadow: var(--vb-shadow-lg);">
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                                <span class="vb-h4" style="margin: 0; color: var(--vb-black);">YOUR SHORTENED URL:</span>
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

@include('partials.footer')

<script nonce="{{ $cspNonce }}">
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
