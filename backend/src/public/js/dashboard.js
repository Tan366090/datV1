async function loadDashboardData() {
    try {
        const response = await fetch("/QLNhanSu_version1/api/dashboard_stats.php", {
            headers: {
                "Accept": "application/json",
                "Content-Type": "application/json"
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error("Response was not JSON");
        }

        const data = await response.json();
        if (data.status !== "success") {
            throw new Error(data.message || "Failed to load dashboard data");
        }

        // Process and display data
        updateDashboard(data.data);
    } catch (error) {
        console.error("Error loading dashboard data:", error);
        showError("Không thể tải dữ liệu dashboard. Vui lòng thử lại sau.");
    }
}

function updateDashboard(data) {
    // Update stats cards
    document.querySelector(".stats-cards .card:nth-child(1) .number").textContent = data.totalEmployees;
    document.querySelector(".stats-cards .card:nth-child(2) .number").textContent = data.departmentStats.length;
    document.querySelector(".stats-cards .card:nth-child(3) .number").textContent = formatCurrency(data.totalSalary);
    document.querySelector(".stats-cards .card:nth-child(4) .number").textContent = data.todayAttendance + "%";

    // Update department chart
    const deptCtx = document.getElementById("departmentChart").getContext("2d");
    new Chart(deptCtx, {
        type: "pie",
        data: {
            labels: data.departmentStats.map(d => d.name),
            datasets: [{
                data: data.departmentStats.map(d => d.count),
                backgroundColor: [
                    "#2563eb",
                    "#10b981",
                    "#f59e0b",
                    "#ef4444",
                    "#8b5cf6",
                ],
            }],
        },
    });

    // Update salary chart
    const salaryCtx = document.getElementById("salaryChart").getContext("2d");
    new Chart(salaryCtx, {
        type: "bar",
        data: {
            labels: data.salaryStats.map(s => s.department),
            datasets: [{
                label: "Tổng lương",
                data: data.salaryStats.map(s => s.total),
                backgroundColor: "#2563eb",
            }],
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
        },
    });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND"
    }).format(amount);
}

function showError(message) {
    const errorDiv = document.getElementById("error-message");
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = "block";
    }
}

// Initialize dashboard when the DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    loadDashboardData();
}); 