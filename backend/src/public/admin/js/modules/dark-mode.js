// Dark mode module
class DarkMode {
    constructor() {
        this.themeToggle = document.getElementById('themeToggle');
        this.body = document.body;
        this.isDarkMode = localStorage.getItem('darkMode') === 'true';
        this.initialize();
    }

    initialize() {
        // Áp dụng trạng thái ban đầu
        this.applyTheme();

        // Thêm event listener cho nút toggle
        if (this.themeToggle) {
            this.themeToggle.addEventListener('click', () => this.toggle());
        }

        // Kiểm tra theme của hệ thống
        this.checkSystemTheme();

        // Thêm event listener cho thay đổi theme hệ thống
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                if (!localStorage.getItem('darkMode')) {
                    this.isDarkMode = e.matches;
                    this.applyTheme();
                }
            });
        }
    }

    checkSystemTheme() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            if (!localStorage.getItem('darkMode')) {
                this.isDarkMode = true;
                this.applyTheme();
            }
        }
    }

    toggle() {
        this.isDarkMode = !this.isDarkMode;
        localStorage.setItem('darkMode', this.isDarkMode);
        this.applyTheme();
        
        // Dispatch custom event
        const event = new CustomEvent('themeChanged', { detail: { isDarkMode: this.isDarkMode } });
        document.dispatchEvent(event);
    }

    applyTheme() {
        if (this.isDarkMode) {
            this.body.classList.add('dark-mode');
            document.documentElement.setAttribute('data-theme', 'dark');
            if (this.themeToggle) {
                this.themeToggle.classList.add('active');
                this.themeToggle.setAttribute('aria-checked', 'true');
            }
        } else {
            this.body.classList.remove('dark-mode');
            document.documentElement.removeAttribute('data-theme');
            if (this.themeToggle) {
                this.themeToggle.classList.remove('active');
                this.themeToggle.setAttribute('aria-checked', 'false');
            }
        }

        // Cập nhật màu sắc cho các biểu đồ
        this.updateCharts();
    }

    updateCharts() {
        // Lấy tất cả các biểu đồ
        const charts = Chart.instances;
        if (charts) {
            Object.values(charts).forEach(chart => {
                // Cập nhật màu sắc cho biểu đồ
                if (this.isDarkMode) {
                    chart.options.scales.x.grid.color = 'rgba(255, 255, 255, 0.1)';
                    chart.options.scales.y.grid.color = 'rgba(255, 255, 255, 0.1)';
                    chart.options.scales.x.ticks.color = '#e0e0e0';
                    chart.options.scales.y.ticks.color = '#e0e0e0';
                } else {
                    chart.options.scales.x.grid.color = 'rgba(0, 0, 0, 0.1)';
                    chart.options.scales.y.grid.color = 'rgba(0, 0, 0, 0.1)';
                    chart.options.scales.x.ticks.color = '#666';
                    chart.options.scales.y.ticks.color = '#666';
                }
                chart.update();
            });
        }
    }
}

// Export module
export { DarkMode }; 