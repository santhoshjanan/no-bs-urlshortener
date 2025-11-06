@include('partials.header', [
    'title' => 'Terms of Service - No BS URL Shortener',
    'description' => 'Terms of Service for No BS URL Shortener. Please read these terms before using our service.'
])

<main>
    <div class="vb-container" style="margin-top: 3rem;">
        <!-- Page Header -->
        <div class="vb-text-center vb-mb-4">
            <h1 class="vb-h1" style="font-size: 2.5rem; margin-bottom: 1rem;">
                TERMS OF SERVICE
            </h1>
            <p style="font-size: 1.125rem; font-weight: 600; max-width: 700px; margin: 0 auto;">
                Last Updated: {{ date('F d, Y') }}
            </p>
        </div>

        <!-- Content Card -->
        <div class="vb-card-static" style="max-width: 900px; margin: 0 auto 2rem;">
            <div class="vb-card-body">
                <div style="background: var(--vb-accent); border: var(--vb-border); box-shadow: var(--vb-shadow-md); padding: 2rem; margin-bottom: 2rem;">
                    <h2 class="vb-h3" style="margin-bottom: 1rem; color: var(--vb-black);">TL;DR - The No BS Summary</h2>
                    <p style="font-size: 1.125rem; line-height: 1.7; font-weight: 600; color: var(--vb-black);">
                        Use the service fairly. Don't abuse it. Don't shorten malicious links. We're not responsible if things
                        go wrong. The service is provided as-is. If you break these rules, we'll ban your access. Pretty simple!
                    </p>
                </div>

                <h2 class="vb-h2">1. Acceptance of Terms</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    By accessing and using No BS URL Shortener (the "Service"), you accept and agree to be bound by these
                    Terms of Service ("Terms"). If you don't agree with these Terms, please don't use the Service.
                </p>

                <h2 class="vb-h2">2. Description of Service</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1rem;">
                    No BS URL Shortener provides:
                </p>
                <ul style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem; padding-left: 2rem;">
                    <li>Free URL shortening service</li>
                    <li>Temporary and permanent shortened URLs</li>
                    <li>RESTful API for programmatic access</li>
                    <li>Basic analytics (click counts)</li>
                    <li>No account required for basic usage</li>
                </ul>

                <h2 class="vb-h2">3. Acceptable Use</h2>
                <h3 class="vb-h3" style="color: var(--vb-black); font-weight: 900; margin-top: 1.5rem;">3.1 Permitted Use</h3>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1.5rem;">
                    You may use the Service for lawful purposes only, including:
                </p>
                <ul style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem; padding-left: 2rem;">
                    <li>Shortening legitimate URLs for personal or business use</li>
                    <li>Sharing shortened URLs on social media, emails, or websites</li>
                    <li>Using our API within the rate limits</li>
                    <li>Creating temporary or permanent shortened links</li>
                </ul>

                <h3 class="vb-h3" style="color: var(--vb-black); font-weight: 900;">3.2 Prohibited Use</h3>
                <div style="background: var(--vb-danger); border: var(--vb-border); box-shadow: var(--vb-shadow-md); padding: 2rem; margin-bottom: 2rem;">
                    <p style="font-size: 1.125rem; line-height: 1.7; font-weight: 600; margin-bottom: 1rem; color: var(--vb-white);">
                        You may NOT use the Service to:
                    </p>
                    <ul style="font-size: 1.125rem; line-height: 1.7; padding-left: 2rem; color: var(--vb-white);">
                        <li>Shorten URLs that link to illegal content</li>
                        <li>Distribute malware, viruses, or malicious code</li>
                        <li>Phishing or scamming users</li>
                        <li>Spam or harassment</li>
                        <li>Adult content without proper warnings</li>
                        <li>Copyright infringement</li>
                        <li>Violate any laws or regulations</li>
                        <li>Circumvent rate limits or abuse the service</li>
                        <li>Attempt to hack, disrupt, or damage the service</li>
                        <li>Scrape or crawl the service without permission</li>
                    </ul>
                </div>

                <h2 class="vb-h2">4. Rate Limits</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1rem;">
                    To prevent abuse, we enforce rate limits:
                </p>
                <ul style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1.5rem; padding-left: 2rem;">
                    <li><strong>Web Interface:</strong> 10 requests per minute per IP address</li>
                    <li><strong>API:</strong> 10 requests per minute per IP address</li>
                </ul>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    If you need higher limits for legitimate use cases, please contact us to discuss options.
                </p>

                <h2 class="vb-h2">5. Content Responsibility</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    You are solely responsible for the URLs you shorten and the content they link to. We do not monitor,
                    verify, or endorse the content of destination URLs. We reserve the right to remove any shortened URL
                    that violates these Terms without notice.
                </p>

                <h2 class="vb-h2">6. No Warranty - Service Provided "As Is"</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1rem;">
                    The Service is provided "AS IS" and "AS AVAILABLE" without warranties of any kind, either express or implied, including but not limited to:
                </p>
                <ul style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem; padding-left: 2rem;">
                    <li>Warranties of merchantability or fitness for a particular purpose</li>
                    <li>Uptime or availability guarantees</li>
                    <li>Accuracy, reliability, or completeness of the Service</li>
                    <li>Security or freedom from viruses or bugs</li>
                </ul>

                <h2 class="vb-h2">7. Limitation of Liability</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1rem;">
                    To the fullest extent permitted by law:
                </p>
                <ul style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem; padding-left: 2rem;">
                    <li>We are not liable for any direct, indirect, incidental, special, or consequential damages</li>
                    <li>We are not responsible for content accessed through shortened URLs</li>
                    <li>We are not liable for lost data, profits, or business interruption</li>
                    <li>You use the Service at your own risk</li>
                </ul>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    This includes, but is not limited to, damages from shortened URLs that stop working, data loss, service
                    interruptions, or malicious content linked through our service.
                </p>

                <h2 class="vb-h2">8. Service Modifications and Termination</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1rem;">
                    We reserve the right to:
                </p>
                <ul style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem; padding-left: 2rem;">
                    <li>Modify, suspend, or discontinue the Service at any time without notice</li>
                    <li>Change these Terms at any time</li>
                    <li>Refuse service to anyone for any reason</li>
                    <li>Remove or disable any shortened URLs without notice</li>
                    <li>Ban IP addresses that violate these Terms</li>
                </ul>

                <h2 class="vb-h2">9. URL Permanence</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    While we intend to keep permanent URLs working indefinitely, we make no guarantees. URLs may stop working
                    due to service discontinuation, technical issues, or policy violations. Temporary URLs expire after the
                    time you specify. We are not responsible if URLs become inaccessible.
                </p>

                <h2 class="vb-h2">10. API Terms</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1rem;">
                    When using our API:
                </p>
                <ul style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem; padding-left: 2rem;">
                    <li>Respect the rate limits</li>
                    <li>Don't abuse or overload the API</li>
                    <li>The API is provided as-is with no uptime guarantees</li>
                    <li>We may change or discontinue the API at any time</li>
                    <li>API responses are provided without warranty</li>
                </ul>

                <h2 class="vb-h2">11. Intellectual Property</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    The Service, including its code, design, and branding, is owned by Santhosh J and is open-sourced under
                    the MIT License. You may use, modify, and distribute the code according to the terms of that license.
                    However, you may not misrepresent the origin of the Service or use our branding without permission.
                </p>

                <h2 class="vb-h2">12. Privacy</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    Your privacy is important to us. Please review our
                    <a href="{{ route('privacy') }}" style="text-decoration: underline; font-weight: 700;">Privacy Policy</a>
                    to understand how we collect and use your information.
                </p>

                <h2 class="vb-h2">13. Indemnification</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    You agree to indemnify and hold harmless No BS URL Shortener and its developer from any claims, damages,
                    losses, or expenses (including legal fees) arising from your use of the Service or violation of these Terms.
                </p>

                <h2 class="vb-h2">14. Governing Law</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    These Terms are governed by the laws of the State of New Jersey, United States, without regard to its
                    conflict of law provisions. Any disputes arising from these Terms or the Service will be resolved in
                    the courts of New Jersey.
                </p>

                <h2 class="vb-h2">15. Changes to Terms</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    We may update these Terms from time to time. Changes will be posted on this page with an updated
                    "Last Updated" date. Your continued use of the Service after changes constitutes acceptance of the new Terms.
                </p>

                <h2 class="vb-h2">16. Severability</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    If any provision of these Terms is found to be unenforceable, the remaining provisions will continue
                    to be valid and enforceable.
                </p>

                <h2 class="vb-h2">17. Contact Information</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1rem;">
                    Questions about these Terms? Contact us:
                </p>
                <ul style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem; padding-left: 2rem;">
                    <li>Email: <a href="mailto:viscous.buys4y@icloud.com" style="text-decoration: underline; font-weight: 700;">viscous.buys4y@icloud.com</a></li>
                    <li>Twitter: <a href="https://x.com/santhoshj" target="_blank" rel="noopener noreferrer" style="text-decoration: underline; font-weight: 700;">@santhoshj</a></li>
                    <li>GitHub: <a href="https://github.com/santhoshjanan/no-bs-urlshortener" target="_blank" rel="noopener noreferrer" style="text-decoration: underline; font-weight: 700;">View our code</a></li>
                </ul>

                <div style="background: var(--vb-primary); border: var(--vb-border); box-shadow: var(--vb-shadow-lg); padding: 2rem; text-align: center; margin-top: 3rem;">
                    <h3 class="vb-h3" style="margin-bottom: 1rem; color: var(--vb-black);">Agreed to the Terms?</h3>
                    <p style="font-size: 1.125rem; margin-bottom: 1.5rem; color: var(--vb-black);">
                        Time to shorten some URLs!
                    </p>
                    <a href="{{ route('index') }}" class="vb-btn vb-btn-primary vb-btn-lg" style="color: var(--vb-black) !important;">Start Now</a>
                </div>
            </div>
        </div>
    </div>
</main>

@include('partials.footer')
