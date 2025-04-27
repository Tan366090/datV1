import { CommonUtils, AuthUtils, PermissionUtils, NotificationUtils, UIUtils } from './utils.js';
import { APIUtils } from './api.js';
import { showLoading, hideLoading, showError, showSuccess } from './common.js';
import { checkAuth } from './auth_utils.js';
import { initPermissionCheck } from './permission.js';

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
    },
    showLoading: () => {
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) loadingOverlay.style.display = 'flex';
    },
    hideLoading: () => {
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) loadingOverlay.style.display = 'none';
    }
};

// API utilities
const APIUtils = {
    baseUrl: '/qlnhansu_V2/backend/src/api',
    
    async fetchWithRetry(endpoint, options = {}, retryCount = 0) {
        try {
            const response = await fetch(`${this.baseUrl}/${endpoint}`, {
                ...options,
                headers: {
                    'Content-Type': 'application/json',
                    ...options.headers
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            if (retryCount < 3) {
                await new Promise(resolve => setTimeout(resolve, 1000 * (retryCount + 1)));
                return this.fetchWithRetry(endpoint, options, retryCount + 1);
            }
            throw error;
        }
    }
};

// Dashboard functionality
export class Dashboard {
    constructor() {
        this.isLoading = false;
        this.charts = {};
        this.data = {
            employees: [],
            departments: [],
            positions: [],
            performances: [],
            payroll: [],
            leaves: [],
            trainings: [],
            tasks: []
        };
        this.initCharts();
        this.loadData();
        this.setupEventListeners();
    }

    async init() {
        try {
            await checkAuth();
            await initPermissionCheck();
            await this.loadData();
        } catch (error) {
            console.error('Dashboard initialization failed:', error);
            showError('Failed to initialize dashboard. Please try refreshing the page.');
        }
    }

    async loadData() {
        if (this.isLoading) return;

        this.isLoading = true;
        CommonUtils.showLoading();

        try {
            const [
                employees,
                departments,
                positions,
                performances,
                payroll,
                leaves,
                trainings,
                tasks
            ] = await Promise.all([
                APIUtils.fetchData('employees'),
                APIUtils.fetchData('departments'),
                APIUtils.fetchData('positions'),
                APIUtils.fetchData('performances'),
                APIUtils.fetchData('payroll'),
                APIUtils.fetchData('leaves'),
                APIUtils.fetchData('trainings'),
                APIUtils.fetchData('tasks')
            ]);

            this.data = {
                employees,
                departments,
                positions,
                performances,
                payroll,
                leaves,
                trainings,
                tasks
            };

            await this.updateMetrics();
            await this.updateCharts();
            await this.updateRecentEmployees();
        } catch (error) {
            console.error("Error loading dashboard data:", error);
            NotificationUtils.show("Lỗi khi tải dữ liệu dashboard", "error");
        } finally {
            this.isLoading = false;
            CommonUtils.hideLoading();
        }
    }

    async updateMetrics() {
        const { employees = [], performances = [], payroll = [], leaves = [] } = this.data;

        try {
            // Update employee metrics
            document.getElementById("totalEmployees").textContent = employees.length || 0;
            document.getElementById("activeEmployees").textContent = 
                employees.filter(emp => emp?.status === "active").length || 0;
            document.getElementById("inactiveEmployees").textContent = 
                employees.filter(emp => emp?.status === "inactive").length || 0;

            // Update performance metrics
            const avgPerformance = performances.length > 0 
                ? performances.reduce((sum, perf) => sum + (perf?.rating || 0), 0) / performances.length 
                : 0;
            document.getElementById("kpiCompletion").textContent = 
                `${Math.round(avgPerformance)}%`;

            // Update payroll metrics
            const totalSalary = payroll.length > 0
                ? payroll.reduce((sum, pay) => sum + (pay?.amount || 0), 0)
                : 0;
            document.getElementById("totalSalary").textContent = 
                CommonUtils.formatCurrency(totalSalary);

            // Update leaves metrics
            const pendingLeaves = leaves.length > 0
                ? leaves.filter(leave => leave?.status === "pending").length
                : 0;
            document.getElementById("pendingLeaves").textContent = pendingLeaves;

            // Update attendance metrics
            const today = new Date().toISOString().split('T')[0];
            const todayAttendance = employees.length > 0
                ? employees.filter(emp => emp?.last_attendance_date === today).length
                : 0;
            const attendanceRate = employees.length > 0 
                ? (todayAttendance / employees.length) * 100 
                : 0;
            document.getElementById("todayAttendance").textContent = 
                `${Math.round(attendanceRate)}%`;
        } catch (error) {
            console.error("Error updating metrics:", error);
            NotificationUtils.show("Lỗi khi cập nhật số liệu", "error");
        }
    }

    async updateCharts() {
        const { employees, departments } = this.data;

        // Update attendance chart
        const attendanceData = this.processAttendanceData(employees);
        this.charts.attendance.data.labels = attendanceData.labels;
        this.charts.attendance.data.datasets[0].data = attendanceData.values;
        this.charts.attendance.update();

        // Update department chart
        const departmentData = this.processDepartmentData(departments);
        this.charts.department.data.labels = departmentData.labels;
        this.charts.department.data.datasets[0].data = departmentData.values;
        this.charts.department.update();
    }

    processAttendanceData(employees) {
        const attendanceByDate = {};
        employees.forEach((emp) => {
            if (!attendanceByDate[emp.last_attendance_date]) {
                attendanceByDate[emp.last_attendance_date] = { present: 0, total: 0 };
            }
            attendanceByDate[emp.last_attendance_date].total++;
            if (emp.status === "present") {
                attendanceByDate[emp.last_attendance_date].present++;
            }
        });

        const labels = Object.keys(attendanceByDate).sort();
        const values = labels.map((date) => {
            const { present, total } = attendanceByDate[date];
            return (present / total) * 100;
        });

        return { labels, values };
    }

    processDepartmentData(departments) {
        return {
            labels: departments.map((d) => d.name),
            values: departments.map((d) => d.employee_count),
        };
    }

    async updateRecentEmployees() {
        const { employees } = this.data;
        const recentEmployees = employees
            .sort((a, b) => new Date(b.join_date) - new Date(a.join_date))
            .slice(0, 10);

        const tbody = document.getElementById("recentEmployees");
        tbody.innerHTML = recentEmployees
            .map((emp) => `
                <tr>
                    <td>${emp.employee_id}</td>
                    <td>${emp.full_name}</td>
                    <td>${emp.position}</td>
                    <td>${emp.department}</td>
                    <td>${CommonUtils.formatDate(emp.join_date)}</td>
                    <td>${CommonUtils.formatDate(emp.birth_date)}</td>
                    <td>${emp.phone}</td>
                    <td>${emp.email}</td>
                    <td>${emp.address}</td>
                    <td>${emp.status}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="window.location.href='employees/edit.html?id=${emp.id}'">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
            `)
            .join("");
    }

    initCharts() {
        // Initialize attendance chart
        const attendanceCtx = document.getElementById('attendanceChart');
        if (attendanceCtx) {
            this.charts.attendance = new Chart(attendanceCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Tỷ lệ điểm danh',
                        data: [],
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }

        // Initialize department chart
        const departmentCtx = document.getElementById('departmentChart');
        if (departmentCtx) {
            this.charts.department = new Chart(departmentCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Số nhân viên theo phòng ban',
                        data: [],
                        backgroundColor: 'rgba(54, 162, 235, 0.5)'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }

    setupEventListeners() {
        // Dark mode toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        if (darkModeToggle) {
            darkModeToggle.addEventListener('click', () => {
                UIUtils.toggleDarkMode();
            });
        }

        // Logout button
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', () => {
                AuthUtils.logout();
            });
        }
    }

    cleanup() {
        // Cleanup charts
        Object.values(this.charts).forEach((chart) => chart.destroy());
        this.charts = {};

        // Remove event listeners
        document.getElementById('darkModeToggle')?.removeEventListener('click', UIUtils.toggleDarkMode);
        document.getElementById('logoutBtn')?.removeEventListener('click', AuthUtils.logout);
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const dashboard = new Dashboard();
    dashboard.init();
}); 