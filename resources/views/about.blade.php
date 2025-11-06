@include('partials.header', [
    'title' => 'About - No BS URL Shortener',
    'description' => 'Learn about No BS URL Shortener - a privacy-first, open-source URL shortening service built with Laravel.'
])

<main>
    <div class="vb-container" style="margin-top: 3rem;">
        <!-- Page Header -->
        <div class="vb-text-center vb-mb-4">
            <h1 class="vb-h1" style="font-size: 2.5rem; margin-bottom: 1rem;">
                ABOUT US
            </h1>
        </div>

        <!-- Content Card -->
        <div class="vb-card-static" style="max-width: 900px; margin: 0 auto 2rem;">
            <div class="vb-card-body">
                <h2 class="vb-h2">What is No BS URL Shortener?</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    No BS URL Shortener is exactly what it says on the tin - a straightforward, no-nonsense URL shortening service.
                    No account required, no tracking beyond basic analytics, no BS! Just shorten your URLs and get on with your day.
                </p>

                <h2 class="vb-h2">Why We Built This</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    I built this because I needed a simple URL shortener for my own use. Most services out there want you to
                    create an account, track everything you do, and show you ads. That's not what I wanted, so I built something better.
                </p>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    Since I use it a lot myself, I figured others might find it useful too. So here we are - sharing it with the world!
                </p>

                <h2 class="vb-h2">What Makes Us Different</h2>
                <div style="background: var(--vb-primary); border: var(--vb-border); box-shadow: var(--vb-shadow-md); padding: 2rem; margin-bottom: 2rem;">
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="margin-bottom: 1rem; font-size: 1.125rem; font-weight: 700; color: var(--vb-black);">
                            <span style="color: var(--vb-black); font-size: 1.5rem; font-weight: 900;">✓</span> Privacy-First: No personal data collection
                        </li>
                        <li style="margin-bottom: 1rem; font-size: 1.125rem; font-weight: 700; color: var(--vb-black);">
                            <span style="color: var(--vb-black); font-size: 1.5rem; font-weight: 900;">✓</span> No Account Required: Just use it
                        </li>
                        <li style="margin-bottom: 1rem; font-size: 1.125rem; font-weight: 700; color: var(--vb-black);">
                            <span style="color: var(--vb-black); font-size: 1.5rem; font-weight: 900;">✓</span> Open Source: Check the code yourself
                        </li>
                        <li style="margin-bottom: 1rem; font-size: 1.125rem; font-weight: 700; color: var(--vb-black);">
                            <span style="color: var(--vb-black); font-size: 1.5rem; font-weight: 900;">✓</span> Fast: Redis caching for lightning speed
                        </li>
                        <li style="margin-bottom: 1rem; font-size: 1.125rem; font-weight: 700; color: var(--vb-black);">
                            <span style="color: var(--vb-black); font-size: 1.5rem; font-weight: 900;">✓</span> Temporary URLs: Set expiration times
                        </li>
                        <li style="font-size: 1.125rem; font-weight: 700; color: var(--vb-black);">
                            <span style="color: var(--vb-black); font-size: 1.5rem; font-weight: 900;">✓</span> Free API: Integrate with your apps
                        </li>
                    </ul>
                </div>

                <h2 class="vb-h2">Tech Stack</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1rem;">
                    Built with modern, battle-tested technologies:
                </p>
                <ul style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem; padding-left: 2rem;">
                    <li><strong>Backend:</strong> Laravel 11 - The PHP framework for web artisans</li>
                    <li><strong>Database:</strong> PostgreSQL - Reliable and powerful</li>
                    <li><strong>Cache:</strong> Redis - Lightning-fast data store</li>
                    <li><strong>Frontend:</strong> Vibe Brutalism CSS Framework - Bold, honest design</li>
                    <li><strong>Security:</strong> reCAPTCHA, rate limiting, XSS protection</li>
                </ul>

                <h2 class="vb-h2">Design Philosophy</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    The neo-brutalist design you see isn't just for show - it embodies our "No BS" philosophy. Bold, honest,
                    unapologetic. No smooth corporate aesthetics, no misleading dark patterns. What you see is what you get.
                </p>

                <h2 class="vb-h2">About the Developer</h2>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1rem;">
                    Hi, I'm Santhosh J! I'm a developer based in New Jersey who believes in building tools that solve real problems
                    without all the unnecessary complexity.
                </p>
                <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 2rem;">
                    You can reach me on <a href="https://x.com/santhoshj" target="_blank" rel="noopener noreferrer" style="text-decoration: underline; font-weight: 700;">Twitter</a>,
                    check out the source code on <a href="https://github.com/santhoshjanan/no-bs-urlshortener" target="_blank" rel="noopener noreferrer" style="text-decoration: underline; font-weight: 700;">GitHub</a>,
                    or send me an <a href="mailto:viscous.buys4y@icloud.com" style="text-decoration: underline; font-weight: 700;">email</a>.
                </p>

                <div style="background: var(--vb-accent); border: var(--vb-border); box-shadow: var(--vb-shadow-lg); padding: 2rem; text-align: center; margin-top: 3rem;">
                    <h3 class="vb-h3" style="margin-bottom: 1rem; color: var(--vb-black);">Ready to shorten some URLs?</h3>
                    <a href="{{ route('index') }}" class="vb-btn vb-btn-primary vb-btn-lg" style="color: var(--vb-black) !important;">Get Started Now</a>
                </div>
            </div>
        </div>
    </div>
</main>

@include('partials.footer')
