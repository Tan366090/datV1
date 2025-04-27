// Loading overlay module
class LoadingOverlay {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        document.addEventListener('DOMContentLoaded', () => {
            // Add event listeners for loading overlay
        });
    }

    show() {
        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = `
            <div class="spinner-container">
                <div class="spinner"></div>
                <div class="loading-text">Loading...</div>
            </div>
        `;
        document.body.appendChild(overlay);
    }

    hide() {
        const overlay = document.querySelector('.loading-overlay');
        if (overlay) {
            overlay.remove();
        }
    }
}

// Initialize loading overlay functionality
const loadingOverlay = new LoadingOverlay(); 