export default class Validator {
    /**
     * Returns true only for well-formed http/https URLs.
     * Uses native URL parsing and enforces scheme whitelist.
     *
     * @param {string} url
     * @returns {boolean}
     */
    static isValid(url) {
        if (typeof url !== 'string') return false;
        const trimmed = url.trim();
        if (trimmed === '') return false;
        // quick regex guard to match project guideline
        if (!/^https?:\/\//i.test(trimmed)) {
            return false;
        }
        try {
            const obj = new URL(trimmed);
            return ['http:', 'https:'].includes(obj.protocol);
        } catch (e) {
            return false;
        }
    }
}
