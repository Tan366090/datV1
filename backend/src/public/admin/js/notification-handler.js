class NotificationHandler {
    constructor() {
        this.notificationContainer = document.querySelector('.notification-container');
        this.notificationCount = document.querySelector('.notification-count');
        this.setupEventListeners();
        this.initializeNotifications();
    }

    setupEventListeners() {
        // Lắng nghe sự kiện từ server
        this.setupWebSocket();

        // Lắng nghe sự kiện click trên nút thông báo
        const notificationButton = document.querySelector('.notification-button');
        if (notificationButton) {
            notificationButton.addEventListener('click', () => this.toggleNotifications());
        }

        // Lắng nghe sự kiện click bên ngoài để đóng thông báo
        document.addEventListener('click', (e) => this.handleOutsideClick(e));
    }

    setupWebSocket() {
        const socket = new WebSocket('ws://' + window.location.host + '/ws/notifications');
        
        socket.onmessage = (event) => {
            const notification = JSON.parse(event.data);
            this.addNotification(notification);
        };

        socket.onclose = () => {
            // Thử kết nối lại sau 5 giây
            setTimeout(() => this.setupWebSocket(), 5000);
        };
    }

    async initializeNotifications() {
        try {
            const response = await fetch('/api/notifications');
            if (response.ok) {
                const notifications = await response.json();
                this.displayNotifications(notifications);
                this.updateNotificationCount(notifications.length);
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }

    addNotification(notification) {
        if (!this.notificationContainer) return;

        const notificationElement = this.createNotificationElement(notification);
        this.notificationContainer.insertBefore(notificationElement, this.notificationContainer.firstChild);
        this.updateNotificationCount(1, true);

        // Tự động ẩn thông báo sau 5 giây
        setTimeout(() => {
            notificationElement.classList.add('fade-out');
            setTimeout(() => notificationElement.remove(), 300);
        }, 5000);
    }

    createNotificationElement(notification) {
        const element = document.createElement('div');
        element.className = `notification-item ${notification.type}`;
        element.innerHTML = `
            <div class="notification-icon">
                <i class="fas ${this.getNotificationIcon(notification.type)}"></i>
            </div>
            <div class="notification-content">
                <h6>${notification.title}</h6>
                <p>${notification.message}</p>
                <small>${this.formatTime(notification.timestamp)}</small>
            </div>
            <button class="notification-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        return element;
    }

    displayNotifications(notifications) {
        if (!this.notificationContainer) return;

        this.notificationContainer.innerHTML = notifications.map(notification => `
            <div class="notification-item ${notification.type}">
                <div class="notification-icon">
                    <i class="fas ${this.getNotificationIcon(notification.type)}"></i>
                </div>
                <div class="notification-content">
                    <h6>${notification.title}</h6>
                    <p>${notification.message}</p>
                    <small>${this.formatTime(notification.timestamp)}</small>
                </div>
                <button class="notification-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('');
    }

    updateNotificationCount(count, increment = false) {
        if (!this.notificationCount) return;

        if (increment) {
            const currentCount = parseInt(this.notificationCount.textContent) || 0;
            this.notificationCount.textContent = currentCount + count;
        } else {
            this.notificationCount.textContent = count;
        }

        if (parseInt(this.notificationCount.textContent) > 0) {
            this.notificationCount.style.display = 'block';
        } else {
            this.notificationCount.style.display = 'none';
        }
    }

    toggleNotifications() {
        if (this.notificationContainer) {
            this.notificationContainer.classList.toggle('show');
        }
    }

    handleOutsideClick(e) {
        if (this.notificationContainer && 
            !this.notificationContainer.contains(e.target) && 
            !e.target.closest('.notification-button')) {
            this.notificationContainer.classList.remove('show');
        }
    }

    getNotificationIcon(type) {
        const icons = {
            'success': 'fa-check-circle',
            'error': 'fa-exclamation-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle'
        };
        return icons[type] || 'fa-bell';
    }

    formatTime(timestamp) {
        return new Date(timestamp).toLocaleString('vi-VN', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }
}

// Khởi tạo khi DOM đã load
document.addEventListener('DOMContentLoaded', () => {
    new NotificationHandler();
}); 