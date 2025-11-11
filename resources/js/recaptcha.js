const siteKeyMeta = document.head?.querySelector('meta[name="recaptcha-site-key"]');

if (siteKeyMeta) {
    const siteKey = siteKeyMeta.content;
    let recaptchaLoaderPromise;

    const loadRecaptchaLibrary = () => {
        if (window.grecaptcha) {
            return Promise.resolve(window.grecaptcha);
        }

        if (!recaptchaLoaderPromise) {
            recaptchaLoaderPromise = new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = `https://www.google.com/recaptcha/api.js?render=${siteKey}`;
                script.async = true;
                script.defer = true;
                script.onload = () => resolve(window.grecaptcha);
                script.onerror = () => reject(new Error('Failed to load reCAPTCHA script'));
                document.head.appendChild(script);
            });
        }

        return recaptchaLoaderPromise;
    };

    const requestRecaptchaToken = async (action) => {
        const grecaptcha = await loadRecaptchaLibrary();

        return new Promise((resolve, reject) => {
            grecaptcha.ready(() => {
                grecaptcha.execute(siteKey, { action })
                    .then(resolve)
                    .catch(reject);
            });
        });
    };

    const attachHandlers = () => {
        const forms = document.querySelectorAll('[data-recaptcha="v3"]');
        if (!forms.length) {
            return;
        }

        const eagerEvents = ['focus', 'pointerenter', 'touchstart'];
        eagerEvents.forEach((eventName) => {
            document.addEventListener(eventName, () => {
                loadRecaptchaLibrary().catch((error) => {
                    console.error('Unable to preload reCAPTCHA', error);
                });
            }, { once: true, passive: true });
        });

        forms.forEach((form) => {
            const action = form.dataset.recaptchaAction || 'submit';
            const tokenField = form.querySelector('input[name="recaptcha_token"]');

            form.addEventListener('submit', async (event) => {
                if (form.dataset.recaptchaReady === 'true') {
                    form.dataset.recaptchaReady = '';
                    return;
                }

                event.preventDefault();

                if (!tokenField) {
                    console.warn('Missing hidden input[name="recaptcha_token"]');
                    return;
                }

                try {
                    const token = await requestRecaptchaToken(action);
                    tokenField.value = token;
                    form.dataset.recaptchaReady = 'true';
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                } catch (error) {
                    console.error('Failed to retrieve reCAPTCHA token', error);
                    const errorTarget = form.querySelector('[data-recaptcha-error]');
                    if (errorTarget) {
                        errorTarget.textContent = 'Unable to verify you are human. Please try again.';
                        errorTarget.classList.add('error');
                    }
                }
            });
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', attachHandlers, { once: true });
    } else {
        attachHandlers();
    }
}
