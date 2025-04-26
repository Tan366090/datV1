class DarkMode {
    constructor() {
        this.toggleButton = document.getElementById('darkModeToggle');
        this.isDarkMode = localStorage.getItem('darkMode') === 'true';
        this.setupEventListeners();
        this.applyDarkMode();
    }

    setupEventListeners() {
        if (this.toggleButton) {
            this.toggleButton.addEventListener('click', () => this.toggleDarkMode());
        }
    }

    toggleDarkMode() {
        this.isDarkMode = !this.isDarkMode;
        localStorage.setItem('darkMode', this.isDarkMode);
        this.applyDarkMode();
    }

    applyDarkMode() {
        if (this.isDarkMode) {
            document.body.classList.add('dark-mode');
            this.updateToggleButton(true);
        } else {
            document.body.classList.remove('dark-mode');
            this.updateToggleButton(false);
        }
    }

    updateToggleButton(isDark) {
        if (this.toggleButton) {
            const icon = this.toggleButton.querySelector('i');
            if (icon) {
                icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
            }
        }
    }
}

// Khởi tạo khi DOM đã load
document.addEventListener('DOMContentLoaded', () => {
    new DarkMode();
}); 