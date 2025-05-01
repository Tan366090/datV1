class MenuHandler {
    constructor() {
        this.mainContent = document.querySelector('.main-content');
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Basic menu toggle functionality
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const sidebarOverlay = document.querySelector('.sidebar-overlay');

        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            });
        }

        // Handle submenu
        const menuItems = document.querySelectorAll('.nav-item.has-submenu');
        menuItems.forEach(item => {
            const link = item.querySelector('.nav-link');
            const submenu = item.querySelector('.submenu');
            
            link.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Close other submenus
                menuItems.forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('open');
                    }
                });
                
                // Toggle current submenu
                item.classList.toggle('open');
            });
        });

        // Close submenu when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.nav-item.has-submenu')) {
                menuItems.forEach(item => {
                    item.classList.remove('open');
                });
            }
        });

        // Handle menu item clicks
        document.addEventListener('click', async (e) => {
            const menuItem = e.target.closest('.nav-link');
            if (menuItem && !menuItem.closest('.has-submenu')) {
                e.preventDefault();
                const url = menuItem.getAttribute('href');
                if (url) {
                    try {
                        // Show loading state
                        this.showLoading();
                        
                        // Fetch content
                        const response = await fetch(url);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const content = await response.text();
                        
                        // Update main content
                        if (this.mainContent) {
                            this.mainContent.innerHTML = content;
                            this.reinitializeScripts();
                        }
                        
                        // Update active menu item
                        this.updateActiveMenuItem(menuItem);
                        
                        // Update browser history
                        history.pushState({ url: url }, '', url);
                        
                        // Hide loading state
                        this.hideLoading();
                    } catch (error) {
                        console.error('Error loading content:', error);
                        this.showError('Không thể tải nội dung');
                        this.hideLoading();
                    }
                }
            }
        });

        // Handle browser back/forward
        window.addEventListener('popstate', async (e) => {
            if (e.state && e.state.url) {
                try {
                    this.showLoading();
                    const response = await fetch(e.state.url);
                    const content = await response.text();
                    if (this.mainContent) {
                        this.mainContent.innerHTML = content;
                        this.reinitializeScripts();
                    }
                    this.hideLoading();
                } catch (error) {
                    console.error('Error loading content:', error);
                    this.showError('Không thể tải nội dung');
                    this.hideLoading();
                }
            }
        });
    }

    reinitializeScripts() {
        // Reinitialize any scripts that need to be run after content is loaded
        const scripts = this.mainContent.querySelectorAll('script');
        scripts.forEach(script => {
            const newScript = document.createElement('script');
            newScript.textContent = script.textContent;
            script.parentNode.replaceChild(newScript, script);
        });
    }

    updateActiveMenuItem(activeItem) {
        // Remove active class from all menu items
        document.querySelectorAll('.nav-link').forEach(item => {
            item.classList.remove('active');
        });
        
        // Add active class to clicked item
        activeItem.classList.add('active');
        
        // Also update parent nav-item if exists
        const parentNavItem = activeItem.closest('.nav-item');
        if (parentNavItem) {
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            parentNavItem.classList.add('active');
        }
    }

    showLoading() {
        if (this.mainContent) {
            this.mainContent.innerHTML = `
                <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
        }
    }

    hideLoading() {
        // Loading state is automatically removed when content is loaded
    }

    showError(message) {
        if (this.mainContent) {
            this.mainContent.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    ${message}
                </div>
            `;
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new MenuHandler();
}); 