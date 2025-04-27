// Loading overlay module
const LoadingOverlay = {
    init() {
        console.log('Loading overlay module initialized');
        this.createOverlay();
    },

    createOverlay() {
        const overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        `;

        const spinner = document.createElement('div');
        spinner.className = 'spinner-border text-light';
        spinner.role = 'status';
        overlay.appendChild(spinner);

        document.body.appendChild(overlay);
    },

    show() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.style.display = 'flex';
        }
    },

    hide() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    LoadingOverlay.init();
}); 