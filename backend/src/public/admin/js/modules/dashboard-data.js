class DashboardData {
    constructor() {
        this.data = {
            total_employees: 0,
            kpi_completion: 0,
            new_candidates: 0,
            active_projects: 0,
            active_employees: 0,
            attendance_rate: 0,
            pending_leaves: 0,
            monthly_salary: 0,
            inactive_employees: 0,
            department_distribution: [],
            attendance_trend: [],
            mobile_stats: {
                downloads: 0,
                active_users: 0,
                notifications_sent: 0
            },
            backup_info: {
                last_backup: 'Chưa có',
                total_size: 0
            }
        };
    }

    async loadData() {
        try {
            // Fetch data from API
            const response = await fetch('/qlnhansu_V2/backend/src/public/api/dashboard_api.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            this.data = data;
            this.updateUI();
        } catch (error) {
            console.error('Error loading dashboard data:', error);
        }
    }

    updateUI() {
        // Update quick stats
        this.updateElementText('totalEmployees', this.data.total_employees || 0);
        this.updateElementText('kpiCompletion', `${this.data.kpi_completion || 0}%`);
        this.updateElementText('newCandidates', this.data.new_candidates || 0);
        this.updateElementText('activeProjects', this.data.active_projects || 0);
        this.updateElementText('activeEmployees', this.data.active_employees || 0);
        this.updateElementText('attendanceRate', `${this.data.attendance_rate || 0}%`);
        this.updateElementText('pendingLeaves', this.data.pending_leaves || 0);
        this.updateElementText('monthlySalary', this.formatCurrency(this.data.monthly_salary || 0));
        this.updateElementText('inactiveEmployees', this.data.inactive_employees || 0);

        // Update recent employees table
        this.updateRecentEmployeesTable();

        // Update attendance trend chart
        this.updateAttendanceTrendChart();

        // Update department distribution chart
        this.updateDepartmentChart();

        // Update mobile stats
        this.updateElementText('mobileDownloads', this.data.mobile_stats?.downloads || 0);
        this.updateElementText('activeUsers', this.data.mobile_stats?.active_users || 0);
        this.updateElementText('notificationsSent', this.data.mobile_stats?.notifications_sent || 0);

        // Update backup info
        this.updateElementText('lastBackup', this.data.backup_info?.last_backup || 'Chưa có');
        this.updateElementText('backupSize', `${this.data.backup_info?.total_size || 0} MB`);
    }

    updateElementText(elementId, value) {
        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = value;
        } else {
            console.warn(`Element with id '${elementId}' not found`);
        }
    }

    updateRecentEmployeesTable() {
        const tableBody = document.getElementById('recentEmployees');
        if (!tableBody) return;

        tableBody.innerHTML = '';
        this.data.recent_employees.forEach(employee => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${employee.employee_id}</td>
                <td>${employee.full_name}</td>
                <td>${employee.position}</td>
                <td>${employee.department}</td>
                <td>${employee.join_date}</td>
                <td>${employee.birth_date}</td>
                <td>${employee.phone}</td>
                <td>${employee.email}</td>
                <td>${employee.address}</td>
                <td>${employee.status}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="viewEmployee(${employee.id})">Xem</button>
                </td>
            `;
            tableBody.appendChild(row);
        });
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

        // Group department data by status
        const groupedData = {
            active: [],
            probation: [],
            inactive: [],
            onLeave: []
        };

        this.data.department_distribution.forEach(record => {
            const department = record.department;
            
            // Active employees
            groupedData.active.push({
                x: department,
                y: record.employee_count
            });

            // Probation employees
            groupedData.probation.push({
                x: department,
                y: record.probation_employees
            });

            // Inactive employees
            groupedData.inactive.push({
                x: department,
                y: record.inactive_employees
            });

            // On leave employees
            groupedData.onLeave.push({
                x: department,
                y: record.on_leave_employees
            });
        });

        if (window.departmentChart) {
            window.departmentChart.destroy();
        }

        window.departmentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: this.data.department_distribution.map(record => record.department),
                datasets: [
                    {
                        label: 'Đang làm việc',
                        data: groupedData.active,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgb(75, 192, 192)',
                        borderWidth: 1
                    },
                    {
                        label: 'Thử việc',
                        data: groupedData.probation,
                        backgroundColor: 'rgba(255, 159, 64, 0.5)',
                        borderColor: 'rgb(255, 159, 64)',
                        borderWidth: 1
                    },
                    {
                        label: 'Đã nghỉ việc',
                        data: groupedData.inactive,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgb(255, 99, 132)',
                        borderWidth: 1
                    },
                    {
                        label: 'Đang nghỉ phép',
                        data: groupedData.onLeave,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgb(54, 162, 235)',
                        borderWidth: 1
                    }
                ]
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

    formatCurrency(value) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(value);
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN');
    }
}

// Initialize dashboard data when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const dashboardData = new DashboardData();
    dashboardData.loadData();
    
    // Refresh data every 5 minutes
    setInterval(() => dashboardData.loadData(), 5 * 60 * 1000);
}); 