// Chart instances
let timeDistributionChart;
let performanceChart;
let salaryTrendChart;

// Chart colors
const chartColors = {
    primary: "#2563eb",
    secondary: "#10b981",
    tertiary: "#f59e0b",
    quaternary: "#ef4444",
    quinary: "#8b5cf6"
};

// DOM Elements
const sidebar = document.querySelector('.sidebar');
const mainContent = document.querySelector('.main-content');
const searchInput = document.querySelector('.search-bar input');
const notificationsBadge = document.querySelector('.notifications .badge');
const userDropdown = document.querySelector('.user-dropdown');
const dropdownMenu = document.querySelector('.dropdown-menu');

// Chart Elements
const attendanceChart = document.getElementById('attendanceChart');
const leaveChart = document.getElementById('leaveChart');

// Data Elements
const employeeId = document.getElementById('employeeId');
const department = document.getElementById('department');
const position = document.getElementById('position');
const joinDate = document.getElementById('joinDate');
const attendanceCount = document.getElementById('attendanceCount');
const leaveBalance = document.getElementById('leaveBalance');
const salaryAmount = document.getElementById('salaryAmount');
const performanceScore = document.getElementById('performanceScore');

// Initialize dashboard
document.addEventListener("DOMContentLoaded", async () => {
    await fetchDashboardData();
    createCharts();
    setupChartInteractions();
    setupQuickActions();
    initCharts();
    loadEmployeeData();
});

// Fetch dashboard data
async function fetchDashboardData() {
    try {
        const response = await fetch("/api/employee/dashboard/stats");
        if (!response.ok) {
            throw new Error("Failed to fetch dashboard data");
        }
        const data = await response.json();
        updateStats(data.stats);
        updateCharts(data.charts);
        updateActivities(data.activities);
    } catch (error) {
        console.error("Error fetching dashboard data:", error);
        showError("Không thể tải dữ liệu dashboard");
    }
}

// Update statistics
function updateStats(stats) {
    document.getElementById("onTimeCount").textContent = stats.onTimeCount;
    document.getElementById("lateCount").textContent = stats.lateCount;
    document.getElementById("leaveCount").textContent = stats.leaveCount;
    document.getElementById("leaveBalance").textContent = stats.leaveBalance;
}

// Create charts
function createCharts() {
    // Time Distribution Chart
    const timeCtx = document.getElementById("employeePieChart").getContext("2d");
    timeDistributionChart = new Chart(timeCtx, {
        type: "pie",
        data: {
            labels: ["Làm việc", "Nghỉ phép", "Đi muộn", "Về sớm"],
            datasets: [{
                data: [80, 10, 5, 5],
                backgroundColor: [
                    chartColors.primary,
                    chartColors.secondary,
                    chartColors.tertiary,
                    chartColors.quaternary
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.raw}%`;
                        }
                    }
                }
            }
        }
    });

    // Performance Chart
    const performanceCtx = document.getElementById("employeeBarChart").getContext("2d");
    performanceChart = new Chart(performanceCtx, {
        type: "bar",
        data: {
            labels: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5"],
            datasets: [{
                label: "Hiệu suất (%)",
                data: [85, 90, 88, 92, 95],
                backgroundColor: chartColors.primary,
                borderColor: chartColors.primary,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Hiệu suất: ${context.raw}%`;
                        }
                    }
                }
            }
        }
    });

    // Salary Trend Chart
    const salaryCtx = document.getElementById("employeeLineChart").getContext("2d");
    salaryTrendChart = new Chart(salaryCtx, {
        type: "line",
        data: {
            labels: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5"],
            datasets: [{
                label: "Lương (VND)",
                data: [15000000, 15500000, 16000000, 16500000, 17000000],
                backgroundColor: "rgba(37, 99, 235, 0.2)",
                borderColor: chartColors.primary,
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString("vi-VN") + " VND";
                        }
                    }
                }
            },
            plugins: {
                zoom: {
                    zoom: {
                        wheel: {
                            enabled: true
                        },
                        pinch: {
                            enabled: true
                        },
                        mode: "x"
                    },
                    pan: {
                        enabled: true,
                        mode: "x"
                    }
                }
            }
        }
    });
}

// Setup chart interactions
function setupChartInteractions() {
    // Time Range Select
    const timeRangeSelect = document.getElementById("timeRangeSelect");
    timeRangeSelect.addEventListener("change", async (e) => {
        const timeRange = e.target.value;
        await fetchDashboardData(timeRange);
    });

    // Salary Chart Zoom Controls
    document.getElementById("salaryZoomInBtn").addEventListener("click", () => {
        salaryTrendChart.zoom(1.1);
    });

    document.getElementById("salaryZoomOutBtn").addEventListener("click", () => {
        salaryTrendChart.zoom(0.9);
    });

    document.getElementById("salaryResetZoomBtn").addEventListener("click", () => {
        salaryTrendChart.resetZoom();
    });

    // Chart Legends
    setupChartLegends();
}

// Setup chart legends
function setupChartLegends() {
    // Time Distribution Legend
    const timeLegend = document.getElementById("timeDistributionLegend");
    timeDistributionChart.data.datasets[0].data.forEach((value, index) => {
        const legendItem = document.createElement("div");
        legendItem.className = "legend-item";
        legendItem.innerHTML = `
            <div class="legend-color" style="background-color: ${timeDistributionChart.data.datasets[0].backgroundColor[index]}"></div>
            <span>${timeDistributionChart.data.labels[index]}: ${value}%</span>
        `;
        timeLegend.appendChild(legendItem);
    });

    // Performance Legend
    const performanceLegend = document.getElementById("performanceLegend");
    performanceChart.data.datasets[0].data.forEach((value, index) => {
        const legendItem = document.createElement("div");
        legendItem.className = "legend-item";
        legendItem.innerHTML = `
            <div class="legend-color" style="background-color: ${chartColors.primary}"></div>
            <span>${performanceChart.data.labels[index]}: ${value}%</span>
        `;
        performanceLegend.appendChild(legendItem);
    });
}

// Setup quick actions
function setupQuickActions() {
    const checkInBtn = document.getElementById("checkInBtn");
    if (checkInBtn) {
        checkInBtn.addEventListener("click", async () => {
            try {
                const response = await fetch("/api/employee/attendance/check-in", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    }
                });
                
                if (!response.ok) {
                    throw new Error("Failed to check in/out");
                }
                
                const result = await response.json();
                showNotification(result.message);
                await fetchDashboardData();
            } catch (error) {
                console.error("Error checking in/out:", error);
                showError("Không thể thực hiện check in/out");
            }
        });
    }
}

// Update charts with new data
function updateCharts(chartData) {
    // Update Time Distribution Chart
    timeDistributionChart.data.datasets[0].data = chartData.timeDistribution;
    timeDistributionChart.update();

    // Update Performance Chart
    performanceChart.data.datasets[0].data = chartData.performance;
    performanceChart.update();

    // Update Salary Trend Chart
    salaryTrendChart.data.datasets[0].data = chartData.salaryTrend;
    salaryTrendChart.update();
}

// Update recent activities
function updateActivities(activities) {
    const activityList = document.getElementById("activityList");
    activityList.innerHTML = "";
    
    activities.forEach(activity => {
        const activityItem = document.createElement("div");
        activityItem.className = "activity-item";
        activityItem.innerHTML = `
            <div class="activity-icon">
                <i class="fas ${getActivityIcon(activity.type)}"></i>
            </div>
            <div class="activity-content">
                <div class="activity-title">${activity.title}</div>
                <div class="activity-time">${activity.time}</div>
            </div>
        `;
        activityList.appendChild(activityItem);
    });
}

// Get activity icon based on type
function getActivityIcon(type) {
    const icons = {
        attendance: "fa-clock",
        leave: "fa-calendar-alt",
        salary: "fa-money-bill-wave",
        training: "fa-graduation-cap",
        default: "fa-info-circle"
    };
    return icons[type] || icons.default;
}

// Show notification
function showNotification(message) {
    const notification = document.createElement("div");
    notification.className = "notification";
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Show error message
function showError(message) {
    const errorDiv = document.createElement("div");
    errorDiv.className = "error-message";
    errorDiv.textContent = message;
    document.querySelector(".main-content").prepend(errorDiv);
    setTimeout(() => errorDiv.remove(), 5000);
}

// Initialize Charts
function initCharts() {
    // Attendance Chart
    new Chart(attendanceChart, {
        type: 'line',
        data: {
            labels: ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'CN'],
            datasets: [{
                label: 'Giờ làm việc',
                data: [8, 8, 8, 8, 8, 4, 0],
                borderColor: '#3498db',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Chấm công tuần này'
                }
            }
        }
    });

    // Leave Chart
    new Chart(leaveChart, {
        type: 'doughnut',
        data: {
            labels: ['Đã dùng', 'Còn lại'],
            datasets: [{
                data: [5, 7],
                backgroundColor: ['#e74c3c', '#2ecc71']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Nghỉ phép năm nay'
                }
            }
        }
    });
}

// Load Employee Data
async function loadEmployeeData() {
    try {
        const response = await fetch('/api/employee/dashboard');
        const data = await response.json();

        // Update personal info
        employeeId.textContent = data.employeeId;
        department.textContent = data.department;
        position.textContent = data.position;
        joinDate.textContent = data.joinDate;

        // Update stats
        attendanceCount.textContent = data.attendanceCount;
        leaveBalance.textContent = data.leaveBalance;
        salaryAmount.textContent = data.salaryAmount;
        performanceScore.textContent = data.performanceScore;

        // Update notifications
        notificationsBadge.textContent = data.notificationsCount;

        // Update charts
        updateCharts(data.attendanceData, data.leaveData);
    } catch (error) {
        console.error('Error loading employee data:', error);
    }
}

// Update Charts
function updateCharts(attendanceData, leaveData) {
    // Update attendance chart
    attendanceChart.data.datasets[0].data = attendanceData;
    attendanceChart.update();

    // Update leave chart
    leaveChart.data.datasets[0].data = leaveData;
    leaveChart.update();
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    initCharts();
    loadEmployeeData();
});

// Search functionality
searchInput.addEventListener('input', (e) => {
    const searchTerm = e.target.value.toLowerCase();
    // Implement search logic
});

// User dropdown toggle
userDropdown.addEventListener('click', () => {
    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
});

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    if (!userDropdown.contains(e.target)) {
        dropdownMenu.style.display = 'none';
    }
});

// Responsive sidebar toggle
const sidebarToggle = document.querySelector('.sidebar-toggle');
if (sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        mainContent.classList.toggle('active');
    });
}

// Handle window resize
window.addEventListener('resize', () => {
    if (window.innerWidth <= 992) {
        sidebar.classList.add('collapsed');
    } else {
        sidebar.classList.remove('collapsed');
    }
});

// Initialize tooltips
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Handle notifications
const notificationItems = document.querySelectorAll('.notification-item');
notificationItems.forEach(item => {
    item.addEventListener('click', () => {
        item.classList.add('read');
        // Update notification count
        const count = parseInt(notificationsBadge.textContent);
        if (count > 0) {
            notificationsBadge.textContent = count - 1;
        }
    });
});

// Handle leave request form
const leaveRequestForm = document.getElementById('leaveRequestForm');
if (leaveRequestForm) {
    leaveRequestForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(leaveRequestForm);
        
        try {
            const response = await fetch('/api/employee/leave-request', {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                // Show success message
                alert('Đơn xin nghỉ phép đã được gửi thành công!');
                leaveRequestForm.reset();
                // Reload leave data
                loadEmployeeData();
            } else {
                throw new Error('Failed to submit leave request');
            }
        } catch (error) {
            console.error('Error submitting leave request:', error);
            alert('Có lỗi xảy ra khi gửi đơn xin nghỉ phép. Vui lòng thử lại sau.');
        }
    });
}

// Handle training registration
const trainingItems = document.querySelectorAll('.training-item');
trainingItems.forEach(item => {
    const registerBtn = item.querySelector('.register-btn');
    if (registerBtn) {
        registerBtn.addEventListener('click', async () => {
            const trainingId = item.dataset.trainingId;
            
            try {
                const response = await fetch(`/api/employee/training/${trainingId}/register`, {
                    method: 'POST'
                });
                
                if (response.ok) {
                    // Show success message
                    alert('Đăng ký khóa đào tạo thành công!');
                    // Update UI
                    registerBtn.disabled = true;
                    registerBtn.textContent = 'Đã đăng ký';
                } else {
                    throw new Error('Failed to register for training');
                }
            } catch (error) {
                console.error('Error registering for training:', error);
                alert('Có lỗi xảy ra khi đăng ký khóa đào tạo. Vui lòng thử lại sau.');
            }
        });
    }
}); 