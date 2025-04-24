class MenuManager {
    constructor() {
        this.config = {
            recentMenuLimit: 5,
            favorites: [],
            notifications: {
                enabled: true,
                sound: true,
                desktop: true
            }
        };
        this.init();
    }

    init() {
        this.setupMenu();
        this.setupSearch();
        this.setupFavorites();
        if (this.config.notifications && this.config.notifications.enabled) {
            this.setupNotifications();
        }
    }

    setupMenu() {
        const menuItems = document.querySelectorAll(".nav-item.has-submenu");
        menuItems.forEach(item => {
            const link = item.querySelector(".nav-link");
            link.addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                // Close other open submenus
                const otherOpenItems = document.querySelectorAll(".nav-item.has-submenu.open");
                otherOpenItems.forEach(openItem => {
                    if (openItem !== item) {
                        openItem.classList.remove("open");
                    }
                });
                
                // Toggle current submenu
                item.classList.toggle("open");
            });
        });
    }

    setupSearch() {
        const searchInput = document.querySelector(".menu-search input");
        if (searchInput) {
            searchInput.addEventListener("input", (e) => {
                const searchTerm = e.target.value.toLowerCase();
                const menuItems = document.querySelectorAll(".nav-item");
                
                menuItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(searchTerm) ? "block" : "none";
                });
            });
        }
    }

    setupFavorites() {
        const favoriteButtons = document.querySelectorAll(".favorite");
        favoriteButtons.forEach(button => {
            button.addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();
                const menuId = button.closest(".nav-item").dataset.menuId;
                this.toggleFavorite(menuId, button);
            });
        });
    }

    toggleFavorite(menuId, button) {
        const index = this.config.favorites.indexOf(menuId);
        if (index === -1) {
            this.config.favorites.push(menuId);
            button.classList.add("active");
        } else {
            this.config.favorites.splice(index, 1);
            button.classList.remove("active");
        }
        this.updateRecentMenu();
    }

    updateRecentMenu() {
        const recentMenu = document.querySelector(".recent-menu");
        if (recentMenu) {
            recentMenu.innerHTML = `
                <h3>Menu gần đây</h3>
                <ul>
                    ${this.config.favorites.map(id => `
                        <li>
                            <a href="#" data-menu-id="${id}">
                                ${document.querySelector(`[data-menu-id="${id}"]`).textContent}
                            </a>
                        </li>
                    `).join("")}
                </ul>
            `;
        }
    }

    setupNotifications() {
        if (!this.config.notifications) return;
        
        const { sound, desktop } = this.config.notifications;
        
        if (sound) {
            this.setupNotificationSound();
        }
        
        if (desktop) {
            this.setupDesktopNotifications();
        }
    }

    setupNotificationSound() {
        // Implementation for notification sound
    }

    setupDesktopNotifications() {
        // Implementation for desktop notifications
    }
}

export default MenuManager; 