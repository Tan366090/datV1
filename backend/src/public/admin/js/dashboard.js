// Common utilities
const CommonUtils = {
    formatDate: (date) => {
        return new Date(date).toLocaleDateString('vi-VN');
    },
    formatCurrency: (amount) => {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }
};

// Authentication utilities
const AuthUtils = {
    isAuthenticated: () => {
        return localStorage.getItem('token') !== null;
    },
    logout: () => {
        localStorage.removeItem('token');
        window.location.href = '/login_new.html';
    }
};

// Permission utilities
const PermissionUtils = {
    hasPermission: (permission) => {
        const userPermissions = JSON.parse(localStorage.getItem('permissions') || '[]');
        return userPermissions.includes(permission);
    }
};

// Notification utilities
const NotificationUtils = {
    show: (message, type = 'info') => {
        const container = document.getElementById('notificationContainer');
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        container.appendChild(notification);
        setTimeout(() => notification.remove(), 5000);
    }
};

// UI utilities
const UIUtils = {
    toggleDarkMode: () => {
        document.body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
    },
    toggleSidebar: () => {
        document.querySelector('.sidebar').classList.toggle('collapsed');
    }
};

// API utilities
const APIUtils = {
    async fetchData(endpoint) {
        try {
            const response = await fetch(`/api/${endpoint}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });
            if (!response.ok) throw new Error('API request failed');
            return await response.json();
        } catch (error) {
            NotificationUtils.show('Lỗi khi tải dữ liệu', 'error');
            console.error(error);
        }
    }
};

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Check for dark mode preference
    if (localStorage.getItem('darkMode') === 'true') {
        document.body.classList.add('dark-mode');
    }

    // Handle menu toggle
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
    });

    // Close sidebar when clicking overlay
    sidebarOverlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
    });

    // Handle window resize
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        }
    });
}); 