class MenuHandler {
    constructor() {
        this.menuItems = document.querySelectorAll('.nav-item.has-submenu');
        this.recentMenu = [];
        this.maxRecentItems = 5;
        this.setupEventListeners();
        this.loadRecentMenu();
    }

    setupEventListeners() {
        // Handle menu item clicks
        this.menuItems.forEach(item => {
            const link = item.querySelector('.nav-link');
            const submenu = item.querySelector('.submenu');
            const chevron = item.querySelector('.fa-chevron-right');
            
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleSubmenu(item, chevron);
                this.addToRecentMenu(link);
            });
            
            // Desktop hover effect
            if (window.innerWidth > 768) {
                item.addEventListener('mouseenter', () => {
                    this.openSubmenu(item, chevron);
                });
                
                item.addEventListener('mouseleave', () => {
                    this.closeSubmenu(item, chevron);
                });
            }
        });

        // Handle menu search
        const searchInput = document.querySelector('.menu-search input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.filterMenu(e.target.value);
            });
        }

        // Handle window resize
        window.addEventListener('resize', () => {
            this.handleResponsive();
        });
    }

    toggleSubmenu(item, chevron) {
        const isOpen = item.classList.contains('open');
        
        // Close other open submenus
        this.menuItems.forEach(otherItem => {
            if (otherItem !== item) {
                this.closeSubmenu(otherItem, otherItem.querySelector('.fa-chevron-right'));
            }
        });
        
        if (isOpen) {
            this.closeSubmenu(item, chevron);
        } else {
            this.openSubmenu(item, chevron);
        }
    }

    openSubmenu(item, chevron) {
        item.classList.add('open');
        if (chevron) {
            chevron.style.transform = 'rotate(90deg)';
        }
    }

    closeSubmenu(item, chevron) {
        item.classList.remove('open');
        if (chevron) {
            chevron.style.transform = 'rotate(0deg)';
        }
    }

    filterMenu(searchTerm) {
        const menuItems = document.querySelectorAll('.nav-item');
        const searchTermLower = searchTerm.toLowerCase();
        
        menuItems.forEach(item => {
            const link = item.querySelector('.nav-link');
            const text = link.textContent.toLowerCase();
            const isVisible = text.includes(searchTermLower);
            
            item.style.display = isVisible ? '' : 'none';
        });
    }

    addToRecentMenu(link) {
        const menuId = link.getAttribute('data-menu-id');
        if (!menuId) return;
        
        // Remove if already exists
        this.recentMenu = this.recentMenu.filter(item => item.id !== menuId);
        
        // Add to beginning
        this.recentMenu.unshift({
            id: menuId,
            text: link.textContent.trim(),
            href: link.getAttribute('href')
        });
        
        // Keep only max items
        if (this.recentMenu.length > this.maxRecentItems) {
            this.recentMenu.pop();
        }
        
        this.saveRecentMenu();
        this.updateRecentMenuUI();
    }

    saveRecentMenu() {
        localStorage.setItem('recentMenu', JSON.stringify(this.recentMenu));
    }

    loadRecentMenu() {
        const saved = localStorage.getItem('recentMenu');
        if (saved) {
            this.recentMenu = JSON.parse(saved);
            this.updateRecentMenuUI();
        }
    }

    updateRecentMenuUI() {
        const container = document.querySelector('.recent-menu');
        if (!container) return;
        
        const list = document.createElement('ul');
        list.className = 'recent-menu-list';
        
        this.recentMenu.forEach(item => {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.href = item.href;
            a.textContent = item.text;
            li.appendChild(a);
            list.appendChild(li);
        });
        
        // Clear existing content
        while (container.firstChild) {
            container.removeChild(container.firstChild);
        }
        
        // Add title and list
        const title = document.createElement('h3');
        title.textContent = 'Menu gần đây';
        container.appendChild(title);
        container.appendChild(list);
    }

    handleResponsive() {
        const isMobile = window.innerWidth <= 768;
        
        this.menuItems.forEach(item => {
            const chevron = item.querySelector('.fa-chevron-right');
            
            if (isMobile) {
                this.closeSubmenu(item, chevron);
            }
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new MenuHandler();
}); 