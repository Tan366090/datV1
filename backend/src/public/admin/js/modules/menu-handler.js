class AdminMenuHandler {
    constructor() {
        this.sidebar = document.querySelector('.sidebar');
        this.overlay = document.querySelector('.sidebar-overlay');
        this.menuToggle = document.querySelector('.menu-toggle');
        this.menuItems = document.querySelectorAll('.nav-item.has-submenu');
        
        this.init();
    }

    init() {
        if (this.menuToggle && this.sidebar && this.overlay) {
            // Toggle menu
            this.menuToggle.addEventListener('click', () => this.toggleMobileMenu());

            // Close on overlay click
            this.overlay.addEventListener('click', () => this.closeMobileMenu());

            // Auto close on desktop
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    if (window.innerWidth >= 768) {
                        this.closeMobileMenu();
                    }
                }, 250);
            });
        }

        // Handle submenu toggles
        if (this.menuItems) {
            this.menuItems.forEach(item => {
                const link = item.querySelector('.nav-link');
                if (link) {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.toggleSubmenu(item);
                    });
                }
            });
        }
    }

    toggleMobileMenu() {
        if (this.sidebar && this.overlay) {
            this.sidebar.classList.toggle('active');
            this.overlay.classList.toggle('active');
            document.body.classList.toggle('sidebar-open');
        }
    }

    closeMobileMenu() {
        if (this.sidebar && this.overlay) {
            this.sidebar.classList.remove('active');
            this.overlay.classList.remove('active');
            document.body.classList.remove('sidebar-open');
        }
    }

    toggleSubmenu(item) {
        if (!item) return;

        const isOpen = item.classList.contains('open');
        
        // Close all other submenus
        this.menuItems.forEach(otherItem => {
            if (otherItem !== item) {
                otherItem.classList.remove('open');
            }
        });

        // Toggle current submenu
        item.classList.toggle('open');
    }
}

// Initialize when DOM is loaded
if (typeof document !== 'undefined') {
    document.addEventListener('DOMContentLoaded', () => {
        new AdminMenuHandler();
    });
}

// Export for testing
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AdminMenuHandler;
} 