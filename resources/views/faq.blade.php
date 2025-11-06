@include('partials.header', [
    'title' => 'FAQ - No BS URL Shortener',
    'description' => 'Frequently asked questions about No BS URL Shortener. Learn how to use our service, privacy policies, and more.'
])

<main>
    <div class="vb-container" style="margin-top: 3rem;">
        <!-- Page Header -->
        <div class="vb-text-center vb-mb-4">
            <h1 class="vb-h1" style="font-size: 2.5rem; margin-bottom: 1rem;">
                FREQUENTLY ASKED QUESTIONS
            </h1>
            <p style="font-size: 1.125rem; font-weight: 600; max-width: 700px; margin: 0 auto;">
                Got questions? We've got answers. No BS.
            </p>
        </div>

        <!-- FAQ Items -->
        <div style="max-width: 900px; margin: 0 auto 2rem;">
            <!-- FAQ 1 -->
            <div class="vb-card-static" style="margin-bottom: 1.5rem;">
                <div class="vb-card-body">
                    <h2 class="vb-h3" style="color: var(--vb-primary);">How do I shorten a URL?</h2>
                    <p style="font-size: 1.125rem; line-height: 1.7;">
                        It's dead simple. Just paste your long URL into the input field on the homepage, complete the reCAPTCHA,
                        and click "SHORTEN!". You'll get a shortened URL instantly. No account needed.
                    </p>
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="vb-card-static" style="margin-bottom: 1.5rem;">
                <div class="vb-card-body">
                    <h2 class="vb-h3" style="color: var(--vb-primary);">Do I need to create an account?</h2>
                    <p style="font-size: 1.125rem; line-height: 1.7;">
                        Nope! That's the whole point. No account, no login, no tracking. Just shorten your URL and go.
                        However, this means you won't be able to edit or delete your shortened URLs after creation.
                    </p>
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="vb-card-static" style="margin-bottom: 1.5rem;">
                <div class="vb-card-body">
                    <h2 class="vb-h3" style="color: var(--vb-primary);">What about temporary URLs?</h2>
                    <p style="font-size: 1.125rem; line-height: 1.7;">
                        You can set an expiration time in minutes when creating a shortened URL. Set it to 0 for a permanent link,
                        or specify any number of minutes (up to 1 year) for a temporary link. After the time expires, the link
                        will no longer work.
                    </p>
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="vb-card-static" style="margin-bottom: 1.5rem;">
                <div class="vb-card-body">
                    <h2 class="vb-h3" style="color: var(--vb-primary);">Is there an API I can use?</h2>
                    <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1rem;">
                        Yes! You can programmatically shorten URLs using our REST API. Send a POST request to <code style="background: var(--vb-primary); padding: 0.25rem 0.5rem; font-weight: 700;">/api/shorten</code>
                        with your URL in JSON format.
                    </p>
                    <div style="background: var(--vb-black); color: var(--vb-white); padding: 1rem; border: var(--vb-border); font-family: monospace; overflow-x: auto;">
POST /api/shorten<br>
Content-Type: application/json<br>
<br>
{<br>
&nbsp;&nbsp;"original_url": "https://example.com/long/url"<br>
}
                    </div>
                </div>
            </div>

            <!-- FAQ 5 -->
            <div class="vb-card-static" style="margin-bottom: 1.5rem;">
                <div class="vb-card-body">
                    <h2 class="vb-h3" style="color: var(--vb-primary);">Are there any rate limits?</h2>
                    <p style="font-size: 1.125rem; line-height: 1.7;">
                        Yes, to prevent abuse, we limit requests to 10 per minute per IP address. This applies to both the web
                        interface and the API. If you need higher limits, feel free to reach out.
                    </p>
                </div>
            </div>

            <!-- FAQ 6 -->
            <div class="vb-card-static" style="margin-bottom: 1.5rem;">
                <div class="vb-card-body">
                    <h2 class="vb-h3" style="color: var(--vb-primary);">What data do you collect?</h2>
                    <p style="font-size: 1.125rem; line-height: 1.7;">
                        We collect minimal, privacy-friendly analytics: click timestamps and referrer domains (not full URLs).
                        We do NOT collect IP addresses, user agents, or any personal identification data. We also use Google
                        Analytics and Microsoft Clarity for basic page statistics, but your shortened URLs are never logged
                        or associated with your identity.
                    </p>
                </div>
            </div>

            <!-- FAQ 7 -->
            <div class="vb-card-static" style="margin-bottom: 1.5rem;">
                <div class="vb-card-body">
                    <h2 class="vb-h3" style="color: var(--vb-primary);">Can I see statistics for my shortened URLs?</h2>
                    <p style="font-size: 1.125rem; line-height: 1.7;">
                        Currently, no. Since we don't require accounts, there's no way to associate shortened URLs with specific
                        users. We track basic click counts for the service, but these aren't publicly accessible.
                    </p>
                </div>
            </div>

            <!-- FAQ 8 -->
            <div class="vb-card-static" style="margin-bottom: 1.5rem;">
                <div class="vb-card-body">
                    <h2 class="vb-h3" style="color: var(--vb-primary);">Can I customize my shortened URL?</h2>
                    <p style="font-size: 1.125rem; line-height: 1.7;">
                        Not at the moment. Shortened URLs are randomly generated to ensure uniqueness and prevent collisions.
                        This keeps the service simple and fast.
                    </p>
                </div>
            </div>

            <!-- FAQ 9 -->
            <div class="vb-card-static" style="margin-bottom: 1.5rem;">
                <div class="vb-card-body">
                    <h2 class="vb-h3" style="color: var(--vb-primary);">How long do shortened URLs last?</h2>
                    <p style="font-size: 1.125rem; line-height: 1.7;">
                        Permanent URLs (set to 0 minutes) last indefinitely. Temporary URLs expire after the time you specify.
                        We don't automatically delete old URLs, so your permanent links should work forever (or until the service
                        shuts down, which we have no plans for).
                    </p>
                </div>
            </div>

            <!-- FAQ 10 -->
            <div class="vb-card-static" style="margin-bottom: 1.5rem;">
                <div class="vb-card-body">
                    <h2 class="vb-h3" style="color: var(--vb-primary);">What if someone shortens a malicious link?</h2>
                    <p style="font-size: 1.125rem; line-height: 1.7;">
                        We have spam prevention via reCAPTCHA and rate limiting. We only allow HTTP/HTTPS URLs for security.
                        However, we can't verify the content of destination sites. Use shortened URLs from untrusted sources
                        at your own risk. If you find abuse, please report it via email.
                    </p>
                </div>
            </div>

            <!-- FAQ 11 -->
            <div class="vb-card-static" style="margin-bottom: 1.5rem;">
                <div class="vb-card-body">
                    <h2 class="vb-h3" style="color: var(--vb-primary);">Is this service free?</h2>
                    <p style="font-size: 1.125rem; line-height: 1.7;">
                        Yes! Completely free. No hidden fees, no premium tiers, no ads. I built this for myself and decided
                        to share it with the world. If you find it useful and want to support the project, star it on GitHub
                        or tell your friends about it.
                    </p>
                </div>
            </div>

            <!-- FAQ 12 -->
            <div class="vb-card-static" style="margin-bottom: 1.5rem;">
                <div class="vb-card-body">
                    <h2 class="vb-h3" style="color: var(--vb-primary);">Is the code open source?</h2>
                    <p style="font-size: 1.125rem; line-height: 1.7;">
                        Yes! Check out the source code on
                        <a href="https://github.com/santhoshjanan/no-bs-urlshortener" target="_blank" rel="noopener noreferrer" style="text-decoration: underline; font-weight: 700;">GitHub</a>.
                        Feel free to fork it, contribute, or host your own instance.
                    </p>
                </div>
            </div>

            <!-- FAQ 13 -->
            <div class="vb-card-static" style="margin-bottom: 1.5rem;">
                <div class="vb-card-body">
                    <h2 class="vb-h3" style="color: var(--vb-primary);">I have a feature request. How can I suggest it?</h2>
                    <p style="font-size: 1.125rem; line-height: 1.7;">
                        Awesome! Reach out via
                        <a href="mailto:viscous.buys4y@icloud.com" style="text-decoration: underline; font-weight: 700;">email</a> or
                        <a href="https://x.com/santhoshj" target="_blank" rel="noopener noreferrer" style="text-decoration: underline; font-weight: 700;">Twitter</a>.
                        I can't promise I'll implement everything, but I'm always happy to hear ideas!
                    </p>
                </div>
            </div>

            <!-- CTA -->
            <div style="background: var(--vb-success); border: var(--vb-border); box-shadow: var(--vb-shadow-lg); padding: 2rem; text-align: center; margin-top: 3rem;">
                <h3 class="vb-h3" style="margin-bottom: 1rem;">Still have questions?</h3>
                <p style="font-size: 1.125rem; margin-bottom: 1.5rem;">
                    Feel free to reach out - I'm always happy to help!
                </p>
                <a href="mailto:viscous.buys4y@icloud.com" class="vb-btn vb-btn-primary vb-btn-lg">Contact Me</a>
            </div>
        </div>
    </div>
</main>

@include('partials.footer')
