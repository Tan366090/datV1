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
            // Kiểm tra xem API_CONFIG có tồn tại không
            if (!window.API_CONFIG || !window.API_CONFIG.ENDPOINTS || !window.API_CONFIG.ENDPOINTS.RECENT_ITEMS) {
                console.warn('API configuration not found, using default recent items');
                this.updateMenu(this.getDefaultItems());
                return;
            }

            // Sử dụng đường dẫn đúng cho API endpoint
            const response = await fetch('/qlnhansu_V2/backend/src/api/routes/recent-items.php');
            if (!response.ok) {
                console.warn('Failed to load recent items from API, using default items');
                this.updateMenu(this.getDefaultItems());
                return;
            }
            
            const data = await response.json();
            this.updateMenu(data);
        } catch (error) {
            console.warn('Error loading recent items:', error);
            this.updateMenu(this.getDefaultItems());
        }
    },

    getDefaultItems() {
        return [
            {
                title: 'Dashboard',
                url: 'dashboard.html',
                timestamp: new Date().toISOString()
            },
            {
                title: 'Danh sách nhân viên',
                url: 'employees/list.html',
                timestamp: new Date().toISOString()
            },
            {
                title: 'Chấm công',
                url: 'attendance/check.html',
                timestamp: new Date().toISOString()
            }
        ];
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