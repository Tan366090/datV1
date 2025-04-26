import { CommonUtils, NotificationUtils, UIUtils } from '../assets/js/main.js';

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
    },
    showLoading: () => {
        // Implementation of showLoading method
    },
    hideLoading: () => {
        // Implementation of hideLoading method
    }
};

// API utilities
const APIUtils = {
    baseUrl: 'http://localhost/qlnhansu_V2/backend/src/public/api',
    maxRetries: 3,
    retryDelay: 1000,
    cache: new Map(),
    cacheTimeout: 5 * 60 * 1000, // 5 minutes

    // Authentication handling
    getAuthHeaders() {
        const token = localStorage.getItem('token');
        return {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Access-Control-Allow-Origin': '*',
            'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers': 'Content-Type, Authorization'
        };
    },

    // Enhanced error handling
    handleError(error, endpoint) {
        const errorTypes = {
            'NetworkError': 'Lỗi kết nối mạng',
            'SyntaxError': 'Lỗi định dạng dữ liệu',
            'TypeError': 'Lỗi xử lý dữ liệu',
            'default': 'Lỗi không xác định'
        };

        const errorMessage = errorTypes[error.name] || errorTypes.default;
        console.error(`Error in ${endpoint}:`, error);
        NotificationUtils.show(`${errorMessage}: ${error.message}`, 'error');
    },

    // Cache management
    setCache(key, data) {
        this.cache.set(key, {
            data,
            timestamp: Date.now()
        });
    },

    getCache(key) {
        const cached = this.cache.get(key);
        if (cached && Date.now() - cached.timestamp < this.cacheTimeout) {
            return cached.data;
        }
        this.cache.delete(key);
        return null;
    },

    // Enhanced fetch with retry and error handling
    async fetchWithRetry(endpoint, options = {}, retryCount = 0) {
        try {
            const response = await fetch(`${this.baseUrl}/${endpoint}`, {
                ...options,
                headers: {
                    ...this.getAuthHeaders(),
                    ...options.headers
                },
                credentials: 'include'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            return data;
        } catch (error) {
            if (retryCount < this.maxRetries) {
                await new Promise(resolve => setTimeout(resolve, this.retryDelay * (retryCount + 1)));
                return this.fetchWithRetry(endpoint, options, retryCount + 1);
            }
            this.handleError(error, endpoint);
            return null;
        }
    },

    // GET request with caching
    async get(endpoint, options = {}) {
        const cacheKey = `GET:${endpoint}:${JSON.stringify(options)}`;
        const cachedData = this.getCache(cacheKey);
        if (cachedData) {
            return cachedData;
        }

        const data = await this.fetchWithRetry(endpoint, {
            method: 'GET',
            ...options
        });

        if (data) {
            this.setCache(cacheKey, data);
        }
        return data;
    },

    // POST request
    async post(endpoint, body, options = {}) {
        return this.fetchWithRetry(endpoint, {
            method: 'POST',
            body: JSON.stringify(body),
            ...options
        });
    },

    // PUT request
    async put(endpoint, body, options = {}) {
        return this.fetchWithRetry(endpoint, {
            method: 'PUT',
            body: JSON.stringify(body),
            ...options
        });
    },

    // DELETE request
    async delete(endpoint, options = {}) {
        return this.fetchWithRetry(endpoint, {
            method: 'DELETE',
            ...options
        });
    },

    // PATCH request
    async patch(endpoint, body, options = {}) {
        return this.fetchWithRetry(endpoint, {
            method: 'PATCH',
            body: JSON.stringify(body),
            ...options
        });
    },

    // Query builder for GET requests
    buildQuery(params) {
        const query = new URLSearchParams();
        Object.entries(params).forEach(([key, value]) => {
            if (value !== undefined && value !== null) {
                query.append(key, value);
            }
        });
        return query.toString();
    },

    // Enhanced data fetching with query parameters
    async fetchData(endpoint, params = {}) {
        const queryString = this.buildQuery(params);
        const url = queryString ? `${endpoint}?${queryString}` : endpoint;
        return this.get(url);
    },

    // Clear cache
    clearCache() {
        this.cache.clear();
    },

    // Clear specific cache
    clearCacheForEndpoint(endpoint) {
        for (const [key] of this.cache) {
            if (key.startsWith(`GET:${endpoint}`)) {
                this.cache.delete(key);
            }
        }
    },

    async fetchMetrics() {
        return this.fetchData('employees');
    },

    async fetchAttendanceChart(period = 'week') {
        return this.fetchData('employees');
    },

    async fetchDepartmentChart() {
        return this.fetchData('departments');
    },

    async fetchRecentEmployees() {
        return this.fetchData('employees');
    },

    async fetchRecentActivities() {
        return this.fetchData('activities');
    },

    async fetchDashboardStats() {
        return this.fetchData('employees');
    }
};

// Dashboard functionality
class Dashboard {
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
            await this.loadData();
            this.updateMetrics();
            this.updateCharts();
            this.updateRecentEmployees();
        } catch (error) {
            console.error('Error initializing dashboard:', error);
            this.showError('Không thể khởi tạo dashboard. Vui lòng thử lại sau.');
        }
    }

    async loadData() {
        try {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) loadingOverlay.style.display = 'flex';

            const endpoints = [
                'employees',
                'departments',
                'positions',
                'performances',
                'payroll',
                'leaves',
                'trainings',
                'tasks'
            ];

            const promises = endpoints.map(endpoint => 
                APIUtils.fetchData(endpoint)
                    .then(data => {
                        this.data[endpoint] = data || [];
                        return data;
                    })
                    .catch(error => {
                        console.error(`Error loading ${endpoint}:`, error);
                        this.data[endpoint] = [];
                        return null;
                    })
            );

            await Promise.all(promises);

            if (loadingOverlay) loadingOverlay.style.display = 'none';
        } catch (error) {
            console.error('Error loading data:', error);
            this.showError('Không thể tải dữ liệu. Vui lòng thử lại sau.');
            throw error;
        }
    }

    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger';
        errorDiv.textContent = message;
        document.body.insertBefore(errorDiv, document.body.firstChild);
        setTimeout(() => errorDiv.remove(), 5000);
    }

    initCharts() {
        // Initialize attendance chart
        const attendanceCtx = document.getElementById("attendanceChart");
        if (attendanceCtx) {
            if (this.charts.attendance) {
                this.charts.attendance.destroy();
            }
            this.charts.attendance = new Chart(attendanceCtx, {
                type: "line",
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: "Tỷ lệ chấm công",
                            data: [],
                            borderColor: "#4CAF50",
                            tension: 0.1,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                },
            });
        }

        // Initialize department chart
        const departmentCtx = document.getElementById("departmentChart");
        if (departmentCtx) {
            if (this.charts.department) {
                this.charts.department.destroy();
            }
            this.charts.department = new Chart(departmentCtx, {
                type: "pie",
                data: {
                    labels: [],
                    datasets: [
                        {
                            data: [],
                            backgroundColor: [
                                "#FF6384",
                                "#36A2EB",
                                "#FFCE56",
                                "#4BC0C0",
                                "#9966FF",
                            ],
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                },
            });
        }
    }

    updateMetrics() {
        const { employees, performances, payroll, leaves } = this.data;

        // Update employee metrics
        document.getElementById("totalEmployees").textContent = employees.length;
        document.getElementById("activeEmployees").textContent = 
            employees.filter(emp => emp.status === "active").length;
        document.getElementById("inactiveEmployees").textContent = 
            employees.filter(emp => emp.status === "inactive").length;

        // Update performance metrics
        const avgPerformance = performances.reduce((sum, perf) => sum + perf.rating, 0) / performances.length;
        document.getElementById("kpiCompletion").textContent = 
            CommonUtils.formatPercent(avgPerformance / 100);

        // Update payroll metrics
        const totalSalary = payroll.reduce((sum, pay) => sum + pay.amount, 0);
        document.getElementById("totalSalary").textContent = 
            CommonUtils.formatCurrency(totalSalary);

        // Update leaves metrics
        const pendingLeaves = leaves.filter(leave => leave.status === "pending").length;
        document.getElementById("pendingLeaves").textContent = pendingLeaves;

        // Update attendance metrics
        const today = new Date().toISOString().split('T')[0];
        const todayAttendance = employees.filter(emp => emp.last_attendance_date === today).length;
        const attendanceRate = (todayAttendance / employees.length) * 100;
        document.getElementById("todayAttendance").textContent = 
            CommonUtils.formatPercent(attendanceRate / 100);
    }

    updateCharts() {
        const { employees, departments } = this.data;

        // Update attendance chart
        const attendanceData = this.processAttendanceData(employees);
        if (this.charts.attendance) {
            this.charts.attendance.data.labels = attendanceData.labels;
            this.charts.attendance.data.datasets[0].data = attendanceData.values;
            this.charts.attendance.update();
        }

        // Update department chart
        const departmentData = this.processDepartmentData(departments);
        if (this.charts.department) {
            this.charts.department.data.labels = departmentData.labels;
            this.charts.department.data.datasets[0].data = departmentData.values;
            this.charts.department.update();
        }
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

    updateRecentEmployees() {
        const { employees } = this.data;
        const recentEmployees = employees
            .sort((a, b) => new Date(b.join_date) - new Date(a.join_date))
            .slice(0, 10);

        const tbody = document.getElementById("recentEmployees");
        if (tbody) {
            tbody.innerHTML = recentEmployees
                .map(
                    (emp) => `
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
                `
                )
                .join("");
        }
    }

    setupEventListeners() {
        // Dark mode toggle
        const darkModeToggle = document.getElementById("darkModeToggle");
        if (darkModeToggle) {
            darkModeToggle.addEventListener("click", () => {
                UIUtils.toggleDarkMode();
            });
        }

        // Logout button
        const logoutBtn = document.getElementById("logoutBtn");
        if (logoutBtn) {
            logoutBtn.addEventListener("click", () => {
                localStorage.removeItem("token");
                window.location.href = "/login.html";
            });
        }

        // Attendance period change
        const attendancePeriod = document.getElementById("attendancePeriod");
        if (attendancePeriod) {
            attendancePeriod.addEventListener("change", async (e) => {
                await this.updateAttendanceChart(e.target.value);
            });
        }
    }

    async updateAttendanceChart(period) {
        try {
            const data = await APIUtils.fetchData(`employees?period=${period}`);
            if (this.charts.attendance) {
                const attendanceData = this.processAttendanceData(data);
                this.charts.attendance.data.labels = attendanceData.labels;
                this.charts.attendance.data.datasets[0].data = attendanceData.values;
                this.charts.attendance.update();
            }
        } catch (error) {
            console.error("Error updating attendance chart:", error);
        }
    }

    cleanup() {
        // Cleanup charts
        Object.values(this.charts).forEach((chart) => chart.destroy());
        this.charts = {};

        // Remove event listeners
        const darkModeToggle = document.getElementById("darkModeToggle");
        if (darkModeToggle) {
            darkModeToggle.removeEventListener("click", UIUtils.toggleDarkMode);
        }

        const logoutBtn = document.getElementById("logoutBtn");
        if (logoutBtn) {
            logoutBtn.removeEventListener("click", this.handleLogout);
        }

        const attendancePeriod = document.getElementById("attendancePeriod");
        if (attendancePeriod) {
            attendancePeriod.removeEventListener("change", this.handleAttendancePeriodChange);
        }
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    const dashboard = new Dashboard();

    // Check for dark mode preference
    if (localStorage.getItem("darkMode") === "true") {
        document.body.classList.add("dark-mode");
    }

    // Cleanup on page unload
    window.addEventListener("unload", () => {
        dashboard.cleanup();
    });
}); 