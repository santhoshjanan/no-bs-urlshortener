export default class ApiClient {
    constructor(basePath = '') {
        this.basePath = basePath;
    }

    async shorten(url) {
        const res = await fetch(this.basePath + '/api/shorten', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ url }),
            credentials: 'same-origin',
        });

        if (!res.ok) {
            const err = await res.json().catch(() => ({ message: res.statusText }));
            throw new Error(err.message || 'API error');
        }

        return res.json();
    }
}
