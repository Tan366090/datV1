class MobileStats {
    constructor() {
        this.statsContainer = document.querySelector('.mobile-stats');
        this.usageChart = null;
        this.versionChart = null;
        this.setupEventListeners();
        this.initializeCharts();
    }

    setupEventListeners() {
        // Lắng nghe sự kiện khi người dùng thay đổi khoảng thời gian thống kê
        document.querySelectorAll('[data-stats-range]').forEach(button => {
            button.addEventListener('click', (e) => this.handleStatsRangeChange(e));
        });
    }

    async initializeCharts() {
        try {
            // Lấy dữ liệu thống kê sử dụng
            const usageResponse = await fetch('/api/mobile/stats/usage');
            const usageData = await usageResponse.json();

            // Lấy dữ liệu thống kê phiên bản
            const versionResponse = await fetch('/api/mobile/stats/versions');
            const versionData = await versionResponse.json();

            // Khởi tạo biểu đồ sử dụng
            this.initializeUsageChart(usageData);

            // Khởi tạo biểu đồ phiên bản
            this.initializeVersionChart(versionData);

            // Hiển thị thống kê tổng quan
            this.displayOverviewStats(usageData.overview);
        } catch (error) {
            console.error('Mobile stats error:', error);
            this.showError('Có lỗi xảy ra khi tải dữ liệu thống kê');
        }
    }

    initializeUsageChart(data) {
        const ctx = document.getElementById('usageChart').getContext('2d');
        this.usageChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Số phiên sử dụng',
                    data: data.sessions,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }, {
                    label: 'Thời gian sử dụng (phút)',
                    data: data.duration,
                    borderColor: 'rgb(54, 162, 235)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Thống kê sử dụng'
                    }
                }
            }
        });
    }

    initializeVersionChart(data) {
        const ctx = document.getElementById('versionChart').getContext('2d');
        this.versionChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.versions,
                datasets: [{
                    label: 'Số người dùng',
                    data: data.users,
                    backgroundColor: 'rgb(75, 192, 192)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Phân bố phiên bản'
                    }
                }
            }
        });
    }

    displayOverviewStats(data) {
        if (!this.statsContainer) return;

        const statsHtml = `
            <div class="stats-overview">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Tổng số người dùng</h4>
                        <p class="stat-value">${data.totalUsers.toLocaleString()}</p>
                        <p class="stat-change ${data.userGrowth >= 0 ? 'positive' : 'negative'}">
                            <i class="fas fa-${data.userGrowth >= 0 ? 'arrow-up' : 'arrow-down'}"></i>
                            ${Math.abs(data.userGrowth)}%
                        </p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Phiên bản mới nhất</h4>
                        <p class="stat-value">${data.latestVersion}</p>
                        <p class="stat-change ${data.updateRate >= 0 ? 'positive' : 'negative'}">
                            <i class="fas fa-${data.updateRate >= 0 ? 'arrow-up' : 'arrow-down'}"></i>
                            ${Math.abs(data.updateRate)}%
                        </p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Thời gian sử dụng trung bình</h4>
                        <p class="stat-value">${data.avgDuration} phút</p>
                        <p class="stat-change ${data.durationChange >= 0 ? 'positive' : 'negative'}">
                            <i class="fas fa-${data.durationChange >= 0 ? 'arrow-up' : 'arrow-down'}"></i>
                            ${Math.abs(data.durationChange)}%
                        </p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Đánh giá trung bình</h4>
                        <p class="stat-value">${data.avgRating}/5</p>
                        <p class="stat-change ${data.ratingChange >= 0 ? 'positive' : 'negative'}">
                            <i class="fas fa-${data.ratingChange >= 0 ? 'arrow-up' : 'arrow-down'}"></i>
                            ${Math.abs(data.ratingChange)}%
                        </p>
                    </div>
                </div>
            </div>
        `;

        this.statsContainer.insertAdjacentHTML('afterbegin', statsHtml);
    }

    async handleStatsRangeChange(e) {
        const button = e.currentTarget;
        const timeRange = button.dataset.statsRange;
        
        try {
            // Cập nhật trạng thái nút
            document.querySelectorAll('[data-stats-range]').forEach(btn => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            });
            button.classList.remove('btn-outline-primary');
            button.classList.add('btn-primary');

            // Lấy dữ liệu mới
            const response = await fetch(`/api/mobile/stats?timeRange=${timeRange}`);
            const data = await response.json();

            // Cập nhật biểu đồ
            this.updateCharts(data);
        } catch (error) {
            console.error('Stats range change error:', error);
            this.showError('Có lỗi xảy ra khi cập nhật thống kê');
        }
    }

    updateCharts(data) {
        // Cập nhật biểu đồ sử dụng
        this.usageChart.data.labels = data.usage.labels;
        this.usageChart.data.datasets[0].data = data.usage.sessions;
        this.usageChart.data.datasets[1].data = data.usage.duration;
        this.usageChart.update();

        // Cập nhật biểu đồ phiên bản
        this.versionChart.data.labels = data.versions.versions;
        this.versionChart.data.datasets[0].data = data.versions.users;
        this.versionChart.update();

        // Cập nhật thống kê tổng quan
        const overviewContainer = document.querySelector('.stats-overview');
        if (overviewContainer) {
            overviewContainer.remove();
        }
        this.displayOverviewStats(data.overview);
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
    new MobileStats();
}); 