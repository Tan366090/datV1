class DashboardData {
    constructor() {
        this.apiUrl = '/qlnhansu_V2/backend/src/api/test_dashboard_data.php';
        this.data = null;
    }

    async loadData() {
        try {
            const response = await fetch(this.apiUrl);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const result = await response.json();
            
            if (result.status === 'success') {
                this.data = result.data;
                this.updateUI();
            } else {
                throw new Error(result.message || 'Failed to load data');
            }
        } catch (error) {
            console.error('Error loading dashboard data:', error);
            this.showError('Không thể tải dữ liệu dashboard');
        }
    }

    updateUI() {
        // Update quick stats
        document.getElementById('totalEmployees').textContent = this.data.total_employees || 0;
        document.getElementById('kpiCompletion').textContent = `${this.data.kpi_completion || 0}%`;
        document.getElementById('newCandidates').textContent = this.data.new_candidates || 0;
        document.getElementById('activeProjects').textContent = this.data.active_projects || 0;
        document.getElementById('activeEmployees').textContent = this.data.active_employees || 0;
        document.getElementById('attendanceRate').textContent = `${this.data.attendance_rate || 0}%`;
        document.getElementById('pendingLeaves').textContent = this.data.pending_leaves || 0;
        document.getElementById('monthlySalary').textContent = this.formatCurrency(this.data.monthly_salary || 0);
        document.getElementById('inactiveEmployees').textContent = this.data.inactive_employees || 0;

        // Update recent employees table
        this.updateRecentEmployeesTable();

        // Update attendance trend chart
        this.updateAttendanceTrendChart();

        // Update department distribution chart
        this.updateDepartmentChart();

        // Update mobile stats
        document.getElementById('mobileDownloads').textContent = this.data.mobile_stats.downloads || 0;
        document.getElementById('activeUsers').textContent = this.data.mobile_stats.active_users || 0;
        document.getElementById('notificationsSent').textContent = this.data.mobile_stats.notifications_sent || 0;

        // Update backup info
        document.getElementById('lastBackup').textContent = this.data.backup_info.last_backup || 'Chưa có';
        document.getElementById('backupSize').textContent = `${this.data.backup_info.total_size || 0} MB`;
    }

    updateRecentEmployeesTable() {
        const tbody = document.querySelector('#recentEmployeesTable tbody');
        if (!tbody) return;

        tbody.innerHTML = this.data.recent_employees.map(emp => `
            <tr>
                <td>${emp.employee_code || ''}</td>
                <td>${emp.full_name || ''}</td>
                <td>${emp.position || ''}</td>
                <td>${emp.department || ''}</td>
                <td>${this.formatDate(emp.join_date)}</td>
                <td>${this.formatDate(emp.birth_date)}</td>
                <td>${emp.phone || ''}</td>
                <td>${emp.email || ''}</td>
                <td>${emp.address || ''}</td>
                <td>${emp.status || ''}</td>
                <td>
                    <button class="btn btn-sm btn-primary">Sửa</button>
                    <button class="btn btn-sm btn-danger">Xóa</button>
                </td>
            </tr>
        `).join('');
    }

    updateAttendanceTrendChart() {
        const ctx = document.getElementById('attendanceTrendChart');
        if (!ctx) return;

        const labels = this.data.attendance_trend.map(item => this.formatDate(item.date));
        const data = this.data.attendance_trend.map(item => item.rate);

        if (window.attendanceChart) {
            window.attendanceChart.destroy();
        }

        window.attendanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tỷ lệ chấm công',
                    data: data,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    updateDepartmentChart() {
        const ctx = document.getElementById('departmentChart');
        if (!ctx) return;

        const labels = this.data.department_distribution.map(item => item.department);
        const data = this.data.department_distribution.map(item => item.employee_count);

        if (window.departmentChart) {
            window.departmentChart.destroy();
        }

        window.departmentChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc',
                        '#f6c23e',
                        '#e74a3b'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }

    formatDate(date) {
        if (!date) return '';
        return new Intl.DateTimeFormat('vi-VN', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).format(new Date(date));
    }

    showError(message) {
        const errorContainer = document.getElementById('errorContainer');
        if (errorContainer) {
            errorContainer.textContent = message;
            errorContainer.style.display = 'block';
            setTimeout(() => {
                errorContainer.style.display = 'none';
            }, 5000);
        }
    }
}

// Initialize dashboard data when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const dashboardData = new DashboardData();
    dashboardData.loadData();
    
    // Refresh data every 5 minutes
    setInterval(() => dashboardData.loadData(), 5 * 60 * 1000);
}); 