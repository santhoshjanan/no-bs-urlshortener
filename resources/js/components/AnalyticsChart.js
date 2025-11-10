export default class AnalyticsChart {
    constructor(canvasSelector) {
        this.canvas = document.querySelector(canvasSelector);
    }

    render(data = []) {
        if (!this.canvas) return;
        const ctx = this.canvas.getContext?.('2d');
        if (!ctx) {
            this.canvas.textContent = `Views: ${data.length}`;
            return;
        }
        // Placeholder: implement charting with Chart.js in future
    }
}