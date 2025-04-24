// Global variables
let currentPage = 1;
let totalPages = 1;
let selectedEmployees = new Set();
let currentDate = new Date();

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    // Set current date
    updateCurrentDate();
    
    // Load initial data
    loadAttendanceData();
    
    // Set up event listeners
    setupEventListeners();
});

// Update current date display
function updateCurrentDate() {
    const dateElement = document.getElementById('currentDate');
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    dateElement.textContent = currentDate.toLocaleDateString('vi-VN', options);
}

// Set up event listeners
function setupEventListeners() {
    // Date navigation
    document.getElementById('prevDay').addEventListener('click', () => {
        currentDate.setDate(currentDate.getDate() - 1);
        updateCurrentDate();
        loadAttendanceData();
    });

    document.getElementById('nextDay').addEventListener('click', () => {
        currentDate.setDate(currentDate.getDate() + 1);
        updateCurrentDate();
        loadAttendanceData();
    });

    // Filter changes
    document.getElementById('departmentFilter').addEventListener('change', loadAttendanceData);
    document.getElementById('statusFilter').addEventListener('change', loadAttendanceData);
    document.getElementById('searchInput').addEventListener('input', debounce(loadAttendanceData, 300));

    // Bulk check-in modal
    document.getElementById('bulkCheckInBtn').addEventListener('click', showBulkCheckInModal);
    document.getElementById('confirmBulkCheckIn').addEventListener('click', handleBulkCheckIn);

    // Export button
    document.getElementById('exportBtn').addEventListener('click', exportAttendanceData);

    // Pagination
    document.querySelector('.pagination').addEventListener('click', handlePagination);
}

// Load attendance data
async function loadAttendanceData() {
    showLoading();
    try {
        const department = document.getElementById('departmentFilter').value;
        const status = document.getElementById('statusFilter').value;
        const search = document.getElementById('searchInput').value;
        
        const response = await fetch(`/api/attendance?page=${currentPage}&department=${department}&status=${status}&search=${search}&date=${formatDate(currentDate)}`);
        const data = await response.json();
        
        if (data.success) {
            renderAttendanceTable(data.data);
            updatePagination(data.total, data.per_page);
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('Có lỗi xảy ra khi tải dữ liệu');
        console.error('Error loading attendance data:', error);
    } finally {
        hideLoading();
    }
}

// Render attendance table
function renderAttendanceTable(employees) {
    const tbody = document.querySelector('#attendanceTable tbody');
    tbody.innerHTML = '';

    employees.forEach(employee => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${employee.employee_id}</td>
            <td>${employee.name}</td>
            <td>${employee.department}</td>
            <td>${employee.position}</td>
            <td>
                <span class="status-badge status-${employee.status.toLowerCase().replace(' ', '-')}">
                    ${employee.status}
                </span>
            </td>
            <td>${employee.check_in_time || '-'}</td>
            <td>${employee.check_out_time || '-'}</td>
            <td>
                <div class="action-buttons">
                    ${employee.status === 'Chưa điểm danh' ? 
                        `<button class="btn-check-in" onclick="handleCheckIn('${employee.employee_id}')">
                            <i class="fas fa-sign-in-alt"></i> Check-in
                        </button>` : ''}
                    ${employee.status === 'Đã check-in' ? 
                        `<button class="btn-check-out" onclick="handleCheckOut('${employee.employee_id}')">
                            <i class="fas fa-sign-out-alt"></i> Check-out
                        </button>` : ''}
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Handle check-in
async function handleCheckIn(employeeId) {
    showLoading();
    try {
        const response = await fetch('/api/attendance/check-in', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                employee_id: employeeId,
                date: formatDate(currentDate)
            })
        });

        const data = await response.json();
        if (data.success) {
            showSuccess('Check-in thành công');
            loadAttendanceData();
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('Có lỗi xảy ra khi check-in');
        console.error('Error during check-in:', error);
    } finally {
        hideLoading();
    }
}

// Handle check-out
async function handleCheckOut(employeeId) {
    showLoading();
    try {
        const response = await fetch('/api/attendance/check-out', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                employee_id: employeeId,
                date: formatDate(currentDate)
            })
        });

        const data = await response.json();
        if (data.success) {
            showSuccess('Check-out thành công');
            loadAttendanceData();
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('Có lỗi xảy ra khi check-out');
        console.error('Error during check-out:', error);
    } finally {
        hideLoading();
    }
}

// Show bulk check-in modal
function showBulkCheckInModal() {
    const modal = new bootstrap.Modal(document.getElementById('bulkCheckInModal'));
    modal.show();
}

// Handle bulk check-in
async function handleBulkCheckIn() {
    showLoading();
    try {
        const selectedIds = Array.from(selectedEmployees);
        const response = await fetch('/api/attendance/bulk-check-in', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                employee_ids: selectedIds,
                date: formatDate(currentDate)
            })
        });

        const data = await response.json();
        if (data.success) {
            showSuccess('Check-in hàng loạt thành công');
            loadAttendanceData();
            bootstrap.Modal.getInstance(document.getElementById('bulkCheckInModal')).hide();
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('Có lỗi xảy ra khi check-in hàng loạt');
        console.error('Error during bulk check-in:', error);
    } finally {
        hideLoading();
    }
}

// Export attendance data
async function exportAttendanceData() {
    showLoading();
    try {
        const department = document.getElementById('departmentFilter').value;
        const status = document.getElementById('statusFilter').value;
        const search = document.getElementById('searchInput').value;
        
        const response = await fetch(`/api/attendance/export?department=${department}&status=${status}&search=${search}&date=${formatDate(currentDate)}`);
        
        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `attendance_report_${formatDate(currentDate)}.xlsx`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            a.remove();
        } else {
            showError('Có lỗi xảy ra khi xuất báo cáo');
        }
    } catch (error) {
        showError('Có lỗi xảy ra khi xuất báo cáo');
        console.error('Error exporting data:', error);
    } finally {
        hideLoading();
    }
}

// Handle pagination
function handlePagination(event) {
    if (event.target.classList.contains('page-link')) {
        event.preventDefault();
        const page = event.target.dataset.page;
        if (page) {
            currentPage = parseInt(page);
            loadAttendanceData();
        }
    }
}

// Update pagination UI
function updatePagination(total, perPage) {
    totalPages = Math.ceil(total / perPage);
    const pagination = document.querySelector('.pagination');
    let html = '';

    // Previous button
    html += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}">Trước</a>
        </li>
    `;

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        html += `
            <li class="page-item ${currentPage === i ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>
        `;
    }

    // Next button
    html += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}">Sau</a>
        </li>
    `;

    pagination.innerHTML = html;
}

// Utility functions
function formatDate(date) {
    return date.toISOString().split('T')[0];
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// UI feedback functions
function showLoading() {
    document.querySelector('.loading-spinner').style.display = 'flex';
}

function hideLoading() {
    document.querySelector('.loading-spinner').style.display = 'none';
}

function showError(message) {
    const errorElement = document.querySelector('.error-message');
    errorElement.textContent = message;
    errorElement.style.display = 'block';
    setTimeout(() => {
        errorElement.style.display = 'none';
    }, 3000);
}

function showSuccess(message) {
    const successElement = document.querySelector('.success-message');
    successElement.textContent = message;
    successElement.style.display = 'block';
    setTimeout(() => {
        successElement.style.display = 'none';
    }, 3000);
} 