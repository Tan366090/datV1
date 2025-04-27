/**
 * @module recent-menu
 * @description Handles recent menu items functionality
 */

// Recent menu functionality
const RecentMenu = {
    init() {
        this.setupEventListeners();
        this.loadRecentItems();
    },

    setupEventListeners() {
        const menuToggle = document.getElementById('recentMenuToggle');
        if (menuToggle) {
            menuToggle.addEventListener('click', () => this.toggleMenu());
        }
    },

    async loadRecentItems() {
        try {
            const response = await fetch(window.getApiUrl(window.API_CONFIG.ENDPOINTS.RECENT_ITEMS));
            if (!response.ok) throw new Error('Failed to load recent items');
            const data = await response.json();
            this.updateMenu(data);
        } catch (error) {
            console.error('Error loading recent items:', error);
        }
    },

    updateMenu(items) {
        const menuContainer = document.getElementById('recentMenuContainer');
        if (!menuContainer) return;

        menuContainer.innerHTML = items.map(item => `
            <div class="recent-item">
                <a href="${item.url}">${item.title}</a>
                <span class="recent-time">${new Date(item.timestamp).toLocaleString()}</span>
            </div>
        `).join('');
    },

    toggleMenu() {
        const menu = document.getElementById('recentMenu');
        if (menu) {
            menu.classList.toggle('show');
        }
    }
};

// Initialize recent menu
document.addEventListener('DOMContentLoaded', () => {
    RecentMenu.init();
}); 