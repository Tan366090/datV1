class ActivityFilter {
    constructor() {
        this.activitiesContainer = document.querySelector('.activities-list');
        this.searchInput = document.querySelector('.activity-search');
        this.filterButtons = document.querySelectorAll('[data-activity-filter]');
        this.setupEventListeners();
        this.loadActivities();
    }

    setupEventListeners() {
        // Lắng nghe sự kiện tìm kiếm
        this.searchInput?.addEventListener('input', (e) => this.handleSearch(e));

        // Lắng nghe sự kiện lọc
        this.filterButtons.forEach(button => {
            button.addEventListener('click', (e) => this.handleFilter(e));
        });
    }

    async loadActivities() {
        try {
            const response = await fetch('/api/activities');
            const data = await response.json();
            
            if (data.success) {
                this.displayActivities(data.activities);
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            console.error('Load activities error:', error);
            this.showError('Có lỗi xảy ra khi tải hoạt động');
        }
    }

    displayActivities(activities) {
        if (!this.activitiesContainer) return;

        const activitiesHtml = activities.map(activity => `
            <div class="activity-item ${activity.type}" data-activity-id="${activity.id}">
                <div class="activity-icon">
                    <i class="fas ${this.getActivityIcon(activity.type)}"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-header">
                        <h4>${activity.title}</h4>
                        <span class="activity-time">${this.formatTime(activity.timestamp)}</span>
                    </div>
                    <p class="activity-description">${activity.description}</p>
                    <div class="activity-meta">
                        <span class="activity-user">
                            <i class="fas fa-user"></i>
                            ${activity.user}
                        </span>
                        <span class="activity-department">
                            <i class="fas fa-building"></i>
                            ${activity.department}
                        </span>
                    </div>
                </div>
            </div>
        `).join('');

        this.activitiesContainer.innerHTML = activitiesHtml;
    }

    getActivityIcon(type) {
        const icons = {
            'login': 'fa-sign-in-alt',
            'logout': 'fa-sign-out-alt',
            'create': 'fa-plus-circle',
            'update': 'fa-edit',
            'delete': 'fa-trash-alt',
            'approve': 'fa-check-circle',
            'reject': 'fa-times-circle',
            'comment': 'fa-comment',
            'upload': 'fa-upload',
            'download': 'fa-download'
        };
        return icons[type] || 'fa-circle';
    }

    formatTime(timestamp) {
        return new Date(timestamp).toLocaleString('vi-VN', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    async handleSearch(e) {
        const searchTerm = e.target.value.toLowerCase();
        const activityItems = document.querySelectorAll('.activity-item');

        activityItems.forEach(item => {
            const title = item.querySelector('h4').textContent.toLowerCase();
            const description = item.querySelector('.activity-description').textContent.toLowerCase();
            const user = item.querySelector('.activity-user').textContent.toLowerCase();
            const department = item.querySelector('.activity-department').textContent.toLowerCase();

            const isVisible = title.includes(searchTerm) || 
                            description.includes(searchTerm) || 
                            user.includes(searchTerm) || 
                            department.includes(searchTerm);

            item.style.display = isVisible ? 'flex' : 'none';
        });
    }

    async handleFilter(e) {
        const button = e.currentTarget;
        const filterType = button.dataset.activityFilter;
        
        try {
            // Cập nhật trạng thái nút
            this.filterButtons.forEach(btn => {
                btn.classList.remove('active');
            });
            button.classList.add('active');

            // Lấy dữ liệu mới
            const response = await fetch(`/api/activities?filter=${filterType}`);
            const data = await response.json();

            if (data.success) {
                this.displayActivities(data.activities);
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            console.error('Filter activities error:', error);
            this.showError('Có lỗi xảy ra khi lọc hoạt động');
        }
    }

    showError(message) {
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-danger border-0';
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
}

// Khởi tạo khi DOM đã load
document.addEventListener('DOMContentLoaded', () => {
    new ActivityFilter();
}); 