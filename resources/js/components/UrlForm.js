import Validator from '../utils/Validator';
import ApiClient from '../services/ApiClient';

export default class UrlForm {
    constructor(formSelector, outputSelector, opts = {}) {
        this.form = document.querySelector(formSelector);
        this.output = document.querySelector(outputSelector);
        this.client = new ApiClient(opts.basePath || '');
        if (this.form) {
            this.form.addEventListener('submit', this.onSubmit.bind(this));
        }
    }

    async onSubmit(evt) {
        evt.preventDefault();
        this.clearOutput();
        const input = this.form.querySelector('input[name="url"]');
        const url = input?.value ?? '';
        if (!Validator.isValid(url)) {
            this.showError('Please enter a valid http/https URL.');
            return;
        }

        try {
            const json = await this.client.shorten(url);
            const code = json?.data?.code ?? json?.code ?? null;
            if (!code) throw new Error('Invalid response from server');
            this.showSuccess(code);
        } catch (err) {
            this.showError(err.message || 'Unable to shorten URL');
        }
    }

    showSuccess(code) {
        this.output.textContent = `Short URL created: ${window.location.origin}/${code}`;
        this.output.classList.remove('error');
        this.output.classList.add('success');
    }

    showError(msg) {
        this.output.textContent = msg;
        this.output.classList.remove('success');
        this.output.classList.add('error');
    }

    clearOutput() {
        this.output.textContent = '';
        this.output.classList.remove('error', 'success');
    }
}
