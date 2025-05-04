// DOM Elements
const searchInput = document.getElementById('searchInput');
const departmentFilter = document.getElementById('departmentFilter');
const monthFilter = document.getElementById('monthFilter');
const yearFilter = document.getElementById('yearFilter');
const payrollTableBody = document.getElementById('payrollTableBody');
const pagination = document.getElementById('pagination');
const addPayrollBtn = document.getElementById('addPayrollBtn');
const calculatePayrollBtn = document.getElementById('calculatePayrollBtn');
const exportBtn = document.getElementById('exportBtn');
const reloadBtn = document.getElementById('reloadBtn');
const addPayrollModal = document.getElementById('addPayrollModal');
const closeModalBtn = document.getElementById('closeModalBtn');
const addPayrollForm = document.getElementById('addPayrollForm');
const cancelBtn = document.getElementById('cancelBtn');

// State variables
let currentPage = 1;
const itemsPerPage = 10;
let totalItems = 0;
let payrollData = [];

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadDepartments();
    loadPayrollData();
    setupEventListeners();
});

// Event Listeners
function setupEventListeners() {
    // Search and filter events
    searchInput.addEventListener('input', debounce(handleSearch, 300));
    departmentFilter.addEventListener('change', handleFilter);
    monthFilter.addEventListener('change', handleFilter);
    yearFilter.addEventListener('change', handleFilter);

    // Button events
    addPayrollBtn.addEventListener('click', showAddPayrollModal);
    calculatePayrollBtn.addEventListener('click', handleCalculatePayroll);
    exportBtn.addEventListener('click', handleExport);
    reloadBtn.addEventListener('click', handleReload);
    closeModalBtn.addEventListener('click', hideAddPayrollModal);
    cancelBtn.addEventListener('click', hideAddPayrollModal);

    // Form events
    addPayrollForm.addEventListener('submit', handleAddPayroll);
}

// API Functions
async function loadDepartments() {
    try {
        const response = await fetch('/qlnhansu_V2/backend/src/public/api/departments.php');
        const data = await response.json();
        
        if (data.success) {
            const departments = data.data;
            departmentFilter.innerHTML = '<option value="">Tất cả phòng ban</option>';
            departments.forEach(dept => {
                departmentFilter.innerHTML += `<option value="${dept.id}">${dept.name}</option>`;
            });
        }
    } catch (error) {
        showError('Không thể tải danh sách phòng ban');
    }
}

async function loadPayrollData() {
    showLoading();
    try {
        const response = await fetch('/qlnhansu_V2/backend/src/public/api/payroll.php');
        const data = await response.json();
        
        if (data.success) {
            payrollData = data.data;
            totalItems = payrollData.length;
            updateDashboardCards(data.statistics);
            renderPayrollTable();
            renderPagination();
        } else {
            showError(data.message || 'Không thể tải dữ liệu lương');
        }
    } catch (error) {
        showError('Lỗi khi tải dữ liệu lương');
    } finally {
        hideLoading();
    }
}

// Table Rendering
function renderPayrollTable() {
    const filteredData = filterPayrollData();
    const paginatedData = paginateData(filteredData);
    
    payrollTableBody.innerHTML = '';
    
    paginatedData.forEach((payroll, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${(currentPage - 1) * itemsPerPage + index + 1}</td>
            <td>${payroll.employee_code}</td>
            <td>${payroll.employee_name}</td>
            <td>${payroll.department}</td>
            <td>${formatCurrency(payroll.basic_salary)}</td>
            <td>${formatCurrency(payroll.allowance)}</td>
            <td>${formatCurrency(payroll.bonus)}</td>
            <td>${formatCurrency(payroll.deduction)}</td>
            <td>${formatCurrency(payroll.net_salary)}</td>
            <td>${payroll.month}/${payroll.year}</td>
            <td><span class="badge ${getStatusBadgeClass(payroll.status)}">${payroll.status}</span></td>
            <td>
                <div class="btn-group">
                    <button class="btn btn-sm btn-primary" onclick="viewPayroll(${payroll.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="editPayroll(${payroll.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deletePayroll(${payroll.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        payrollTableBody.appendChild(row);
    });
}

// Helper Functions
function filterPayrollData() {
    const searchTerm = searchInput.value.toLowerCase();
    const departmentId = departmentFilter.value;
    const month = monthFilter.value;
    const year = yearFilter.value;

    return payrollData.filter(payroll => {
        const matchesSearch = payroll.employee_name.toLowerCase().includes(searchTerm) ||
                            payroll.employee_code.toLowerCase().includes(searchTerm);
        const matchesDepartment = !departmentId || payroll.department_id === departmentId;
        const matchesMonth = !month || payroll.month === month;
        const matchesYear = !year || payroll.year === year;

        return matchesSearch && matchesDepartment && matchesMonth && matchesYear;
    });
}

function paginateData(data) {
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return data.slice(start, end);
}

function renderPagination() {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    pagination.innerHTML = '';

    if (totalPages <= 1) return;

    // Previous button
    pagination.innerHTML += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Trước</a>
        </li>
    `;

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        pagination.innerHTML += `
            <li class="page-item ${currentPage === i ? 'active' : ''}">
                <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
            </li>
        `;
    }

    // Next button
    pagination.innerHTML += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Sau</a>
        </li>
    `;
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

function getStatusBadgeClass(status) {
    switch (status.toLowerCase()) {
        case 'đã thanh toán':
            return 'bg-success';
        case 'chờ thanh toán':
            return 'bg-warning';
        case 'đã hủy':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

// Event Handlers
function handleSearch() {
    currentPage = 1;
    renderPayrollTable();
    renderPagination();
}

function handleFilter() {
    currentPage = 1;
    renderPayrollTable();
    renderPagination();
}

function changePage(page) {
    if (page < 1 || page > Math.ceil(totalItems / itemsPerPage)) return;
    currentPage = page;
    renderPayrollTable();
    renderPagination();
}

function showAddPayrollModal() {
    addPayrollModal.style.display = 'block';
}

function hideAddPayrollModal() {
    addPayrollModal.style.display = 'none';
    addPayrollForm.reset();
}

async function handleAddPayroll(event) {
    event.preventDefault();
    showLoading();

    try {
        const formData = new FormData(addPayrollForm);
        const response = await fetch('/qlnhansu_V2/backend/src/public/api/payroll.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showSuccess('Thêm phiếu lương thành công');
            hideAddPayrollModal();
            loadPayrollData();
        } else {
            showError(data.message || 'Thêm phiếu lương thất bại');
        }
    } catch (error) {
        showError('Lỗi khi thêm phiếu lương');
    } finally {
        hideLoading();
    }
}

async function handleCalculatePayroll() {
    showLoading();
    try {
        const response = await fetch('/qlnhansu_V2/backend/src/public/api/payroll.php?action=calculate', {
            method: 'POST'
        });

        const data = await response.json();

        if (data.success) {
            showSuccess('Tính lương tự động thành công');
            loadPayrollData();
        } else {
            showError(data.message || 'Tính lương tự động thất bại');
        }
    } catch (error) {
        showError('Lỗi khi tính lương tự động');
    } finally {
        hideLoading();
    }
}

async function handleExport() {
    showLoading();
    try {
        const response = await fetch('/qlnhansu_V2/backend/src/public/api/payroll.php?action=export', {
            method: 'GET'
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `payroll_${new Date().toISOString().split('T')[0]}.xlsx`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            a.remove();
        } else {
            showError('Xuất file thất bại');
        }
    } catch (error) {
        showError('Lỗi khi xuất file');
    } finally {
        hideLoading();
    }
}

function handleReload() {
    currentPage = 1;
    searchInput.value = '';
    departmentFilter.value = '';
    monthFilter.value = '';
    yearFilter.value = '';
    loadPayrollData();
}

// Utility Functions
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

// UI Functions
function showLoading() {
    document.getElementById('loadingOverlay').style.display = 'flex';
}

function hideLoading() {
    document.getElementById('loadingOverlay').style.display = 'none';
}

function showError(message) {
    const notificationContainer = document.getElementById('notificationContainer');
    const notification = document.createElement('div');
    notification.className = 'alert alert-danger alert-dismissible fade show';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    notificationContainer.appendChild(notification);
    setTimeout(() => notification.remove(), 5000);
}

function showSuccess(message) {
    const notificationContainer = document.getElementById('notificationContainer');
    const notification = document.createElement('div');
    notification.className = 'alert alert-success alert-dismissible fade show';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    notificationContainer.appendChild(notification);
    setTimeout(() => notification.remove(), 5000);
}

// CRUD Operations
async function viewPayroll(id) {
    showLoading();
    try {
        const response = await fetch(`/qlnhansu_V2/backend/src/public/api/payroll.php?id=${id}`);
        const data = await response.json();

        if (data.success) {
            // Implement view payroll details
            console.log('View payroll:', data.data);
        } else {
            showError(data.message || 'Không thể xem chi tiết phiếu lương');
        }
    } catch (error) {
        showError('Lỗi khi xem chi tiết phiếu lương');
    } finally {
        hideLoading();
    }
}

async function editPayroll(id) {
    showLoading();
    try {
        const response = await fetch(`/qlnhansu_V2/backend/src/public/api/payroll.php?id=${id}`);
        const data = await response.json();

        if (data.success) {
            // Implement edit payroll
            console.log('Edit payroll:', data.data);
        } else {
            showError(data.message || 'Không thể chỉnh sửa phiếu lương');
        }
    } catch (error) {
        showError('Lỗi khi chỉnh sửa phiếu lương');
    } finally {
        hideLoading();
    }
}

async function deletePayroll(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa phiếu lương này?')) return;

    showLoading();
    try {
        const response = await fetch(`/qlnhansu_V2/backend/src/public/api/payroll.php?id=${id}`, {
            method: 'DELETE'
        });

        const data = await response.json();

        if (data.success) {
            showSuccess('Xóa phiếu lương thành công');
            loadPayrollData();
        } else {
            showError(data.message || 'Xóa phiếu lương thất bại');
        }
    } catch (error) {
        showError('Lỗi khi xóa phiếu lương');
    } finally {
        hideLoading();
    }
} 