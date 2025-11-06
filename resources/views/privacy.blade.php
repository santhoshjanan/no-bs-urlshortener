@include('partials.header', [
    'title' => 'Privacy Policy - No BS URL Shortener',
    'description' => 'Our privacy policy. We collect minimal data and respect your privacy. No personal identification data is stored.'
])
    <div class="vb-container" style="margin-top: 3rem;">
        <!-- Page Header -->
        <div class="vb-text-center vb-mb-4">
            <h1 class="vb-h1" style="font-size: 2.5rem; margin-bottom: 1rem;">
                PRIVACY POLICY
            </h1>
            <p style="font-size: 1.125rem; font-weight: 600; max-width: 700px; margin: 0 auto;">
                Last Updated: {{ date('F d, Y') }}
            </p>
        </div>

        <!-- Content Card -->
        <div class="vb-card-static" style="max-width: 900px; margin: 0 auto 2rem;">
            <div class="vb-card-body">
                <div style="background: var(--vb-success); border: var(--vb-border); box-shadow: var(--vb-shadow-md); padding: 2rem; margin-bottom: 2rem;">
                    <h2 class="vb-h3" style="margin-bottom: 1rem; color: var(--vb-black);">TL;DR - The No BS Summary</h2>
                    <p style="font-size: 1.125rem; line-height: 1.7; font-weight: 600; color: var(--vb-black);">
                        We collect almost nothing. Basic page analytics, click timestamps, and referrer domains. No IP addresses,
                        no user agents, no personal data. We use Google Analytics and Microsoft Clarity for site statistics,
                        but your URLs and personal info stay private.
                    </p>
                </div>

                <h2 class="vb-h2">1. Introduction</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    This Privacy Policy explains how No BS URL Shortener ("we", "us", or "our") collects, uses, and protects
                    your information when you use our service. We're committed to privacy and transparency - hence the "No BS"
                    in our name.
                </p>

                <h2 class="vb-h2">2. Information We Collect</h2>
                <h3 class="vb-h3" style="color: var(--vb-black); font-weight: 900; margin-top: 1.5rem;">2.1 URLs You Shorten</h3>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1.5rem;">
                    When you shorten a URL, we store:
                </p>
                <ul style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem; padding-left: 2rem;">
                    <li>The original URL you submitted</li>
                    <li>The shortened URL code we generated</li>
                    <li>Creation timestamp</li>
                    <li>Expiration time (if you set one)</li>
                </ul>

                <h3 class="vb-h3" style="color: var(--vb-black); font-weight: 900;">2.2 Usage Analytics</h3>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1.5rem;">
                    For shortened URLs, we track minimal analytics:
                </p>
                <ul style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem; padding-left: 2rem;">
                    <li><strong>Click count:</strong> Total number of times a shortened URL was accessed</li>
                    <li><strong>Click timestamp:</strong> When each click occurred</li>
                    <li><strong>Referrer domain:</strong> The domain (not full URL) where the click came from</li>
                    <li><strong>Last 100 clicks only:</strong> We only keep the most recent 100 clicks per URL to prevent unbounded data growth</li>
                </ul>

                <h3 class="vb-h3" style="color: var(--vb-black); font-weight: 900;">2.3 What We DON'T Collect</h3>
                <div style="background: var(--vb-danger); border: var(--vb-border); box-shadow: var(--vb-shadow-md); padding: 2rem; margin-bottom: 2rem;">
                    <p style="font-size: 1.125rem; line-height: 1.7; font-weight: 600; margin-bottom: 1rem; color: var(--vb-white);">
                        We explicitly DO NOT collect:
                    </p>
                    <ul style="font-size: 1.125rem; line-height: 1.7; padding-left: 2rem; color: var(--vb-white);">
                        <li>IP addresses</li>
                        <li>User agents or browser information</li>
                        <li>Cookies (except those from Google Analytics and Microsoft Clarity)</li>
                        <li>Personal identification information</li>
                        <li>Account information (we don't have accounts!)</li>
                        <li>Email addresses (unless you contact us)</li>
                        <li>Location data</li>
                    </ul>
                </div>

                <h3 class="vb-h3" style="color: var(--vb-black); font-weight: 900;">2.4 Third-Party Analytics</h3>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1.5rem;">
                    We use Google Analytics and Microsoft Clarity to understand how our website is used. These services may collect:
                </p>
                <ul style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1.5rem; padding-left: 2rem;">
                    <li>Page views and navigation patterns</li>
                    <li>Device and browser information</li>
                    <li>General geographic location (country/city level)</li>
                </ul>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    These analytics tools are subject to their own privacy policies. You can opt out using browser extensions
                    or privacy settings.
                </p>

                <h2 class="vb-h2">3. How We Use Your Information</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1rem;">
                    We use the collected information to:
                </p>
                <ul style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem; padding-left: 2rem;">
                    <li>Provide the URL shortening service</li>
                    <li>Redirect users from shortened URLs to original URLs</li>
                    <li>Track basic usage statistics (click counts)</li>
                    <li>Prevent abuse and spam</li>
                    <li>Improve our service</li>
                </ul>

                <h2 class="vb-h2">4. Data Storage and Security</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1rem;">
                    Your data is stored securely:
                </p>
                <ul style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem; padding-left: 2rem;">
                    <li>All data is stored in a secure PostgreSQL database</li>
                    <li>Frequently accessed URLs are cached in Redis for 14 days</li>
                    <li>We use industry-standard security practices</li>
                    <li>HTTPS encryption for all connections</li>
                    <li>XSS and CSRF protection on all forms</li>
                </ul>

                <h2 class="vb-h2">5. Data Retention</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1rem;">
                    Data retention policies:
                </p>
                <ul style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem; padding-left: 2rem;">
                    <li><strong>Permanent URLs:</strong> Stored indefinitely until service discontinuation or manual deletion</li>
                    <li><strong>Temporary URLs:</strong> Deleted after expiration time you specified</li>
                    <li><strong>Click analytics:</strong> Last 100 clicks per URL are retained</li>
                    <li><strong>Cached data:</strong> Expires after 14 days in Redis</li>
                </ul>

                <h2 class="vb-h2">6. Sharing of Information</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1rem;">
                    We do not sell, trade, or rent your information. We may share information only:
                </p>
                <ul style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem; padding-left: 2rem;">
                    <li>With third-party analytics providers (Google Analytics, Microsoft Clarity) as described above</li>
                    <li>When required by law or to protect our rights</li>
                    <li>With your explicit consent</li>
                </ul>

                <h2 class="vb-h2">7. Your Rights</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1rem;">
                    Since we don't collect personal information or require accounts, there's no profile or personal data to
                    access or delete. However:
                </p>
                <ul style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem; padding-left: 2rem;">
                    <li>You can stop using the service at any time</li>
                    <li>Shortened URLs created cannot be edited or deleted by users (no accounts = no authentication)</li>
                    <li>If you need a specific URL removed, contact us and we'll consider your request</li>
                </ul>

                <h2 class="vb-h2">8. Cookies</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    We do not use cookies directly for our service. However, Google Analytics and Microsoft Clarity may set
                    cookies for analytics purposes. You can disable cookies in your browser settings or use privacy extensions
                    to block third-party analytics.
                </p>

                <h2 class="vb-h2">9. Children's Privacy</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    Our service is not directed at children under 13. We do not knowingly collect information from children.
                    If we discover we've collected data from a child under 13, we'll delete it promptly.
                </p>

                <h2 class="vb-h2">10. Changes to This Policy</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    We may update this policy occasionally. Changes will be posted on this page with an updated "Last Updated"
                    date. Continued use of the service after changes constitutes acceptance of the new policy.
                </p>

                <h2 class="vb-h2">11. Contact</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1rem;">
                    Questions or concerns about privacy? Reach out:
                </p>
                <ul style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem; padding-left: 2rem;">
                    <li>Email: <a href="mailto:viscous.buys4y@icloud.com" style="text-decoration: underline; font-weight: 700;">viscous.buys4y@icloud.com</a></li>
                    <li>Twitter: <a href="https://x.com/santhoshj" target="_blank" rel="noopener noreferrer" style="text-decoration: underline; font-weight: 700;">@santhoshj</a></li>
                    <li>GitHub: <a href="https://github.com/santhoshjanan/no-bs-urlshortener" target="_blank" rel="noopener noreferrer" style="text-decoration: underline; font-weight: 700;">View our code</a></li>
                </ul>

                <div style="background: var(--vb-primary); border: var(--vb-border); box-shadow: var(--vb-shadow-lg); padding: 2rem; text-align: center; margin-top: 3rem;">
                    <h3 class="vb-h3" style="margin-bottom: 1rem; color: var(--vb-black);">Privacy-focused and proud of it!</h3>
                    <p style="font-size: 1.125rem; margin-bottom: 1.5rem; color: var(--vb-black);">
                        Ready to shorten some URLs with confidence?
                    </p>
                    <a href="{{ route('index') }}" class="vb-btn vb-btn-primary vb-btn-lg" style="color: var(--vb-black) !important;">Start Shortening</a>
                </div>
            </div>
        </div>
    </div>

@include('partials.footer')
