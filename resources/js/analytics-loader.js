/**
 * Lazy Analytics Loader
 * Delays loading of analytics scripts until after page is interactive
 * Improves initial page load performance and Total Blocking Time
 */

let analyticsLoaded = false;

/**
 * Loads Google Analytics
 */
function loadGoogleAnalytics() {
    const gaId = document.querySelector('meta[name="google-analytics-id"]')?.content;
    if (!gaId) return;

    // Load gtag.js
    const script = document.createElement('script');
    script.async = true;
    script.src = `https://www.googletagmanager.com/gtag/js?id=${gaId}`;
    document.head.appendChild(script);

    // Initialize gtag
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    window.gtag = gtag;
    gtag('js', new Date());
    gtag('config', gaId);
}

/**
 * Loads Microsoft Clarity
 */
function loadClarity() {
    const clarityId = document.querySelector('meta[name="clarity-id"]')?.content;
    if (!clarityId) return;

    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", clarityId);
}

/**
 * Initialize analytics after page is interactive
 */
function initializeAnalytics() {
    if (analyticsLoaded) return;
    analyticsLoaded = true;

    loadGoogleAnalytics();
    loadClarity();
}

/**
 * Delay analytics loading until page is idle
 */
function scheduleAnalytics() {
    // Wait for page to be interactive
    if (document.readyState === 'complete') {
        // Page already loaded, schedule for idle time
        if ('requestIdleCallback' in window) {
            requestIdleCallback(initializeAnalytics, { timeout: 2000 });
        } else {
            setTimeout(initializeAnalytics, 2000);
        }
    } else {
        // Wait for page load
        window.addEventListener('load', () => {
            if ('requestIdleCallback' in window) {
                requestIdleCallback(initializeAnalytics, { timeout: 2000 });
            } else {
                setTimeout(initializeAnalytics, 2000);
            }
        }, { once: true });
    }
}

// Also load on first user interaction (click, scroll, keydown)
const interactionEvents = ['click', 'scroll', 'keydown', 'touchstart'];
const loadOnInteraction = () => {
    initializeAnalytics();
    // Remove listeners after first interaction
    interactionEvents.forEach(event => {
        document.removeEventListener(event, loadOnInteraction);
    });
};

interactionEvents.forEach(event => {
    document.addEventListener(event, loadOnInteraction, { once: true, passive: true });
});

// Schedule delayed loading as fallback
scheduleAnalytics();
