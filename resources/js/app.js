import './bootstrap';
import './recaptcha';
import './register-sw';

const loadVibeBrutalism = () => import('./vibe-brutalism.js');

const scheduleVibeHydration = () => {
    const hasVibeComponents = document.querySelector('[class^="vb-"], [class*=" vb-"], [data-vb-component]');
    if (!hasVibeComponents) {
        return;
    }

    const hydrate = () => {
        loadVibeBrutalism().catch((error) => {
            console.error('Failed to load Vibe Brutalism bundle', error);
        });
    };

    if ('requestIdleCallback' in window) {
        window.requestIdleCallback(hydrate);
    } else {
        window.setTimeout(hydrate, 0);
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', scheduleVibeHydration, { once: true });
} else {
    scheduleVibeHydration();
}
