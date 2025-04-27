// Dark mode module
const DarkMode = {
    init() {
        console.log('Dark mode module initialized');
        this.loadPreference();
        this.setupEventListeners();
    },

    loadPreference() {
        const isDarkMode = localStorage.getItem('darkMode') === 'true';
        this.toggleDarkMode(isDarkMode);
    },

    setupEventListeners() {
        const toggleButton = document.getElementById('darkModeToggle');
        if (toggleButton) {
            toggleButton.addEventListener('click', () => {
                const isDarkMode = document.body.classList.contains('dark-mode');
                this.toggleDarkMode(!isDarkMode);
            });
        }
    },

    toggleDarkMode(enable) {
        document.body.classList.toggle('dark-mode', enable);
        localStorage.setItem('darkMode', enable);
        
        // Update icon
        const icon = document.querySelector('#darkModeToggle i');
        if (icon) {
            icon.className = enable ? 'fas fa-sun' : 'fas fa-moon';
        }
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    DarkMode.init();
}); 