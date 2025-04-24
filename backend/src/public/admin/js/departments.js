// Global variables
let currentPage = 1;
let totalPages = 1;
let departments = [];
let managers = [];

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    // Load initial data
    loadDepartments();
    loadManagers();
    
    // Set up event listeners
    setupEventListeners();
});

// Set up event listeners
function setupEventListeners() {
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', debounce(loadDepartments, 300));
    document.getElementById('searchBtn').addEventListener('click', loadDepartments);

    // Add department
    document.getElementById('saveDepartmentBtn').addEventListener('click', handleAddDepartment);

    // Edit department
    document.getElementById('updateDepartmentBtn').addEventListener('click', handleUpdateDepartment);

    // Delete department
    document.getElementById('confirmDeleteBtn').addEventListener('click', handleDeleteDepartment);

    // Pagination
    document.querySelector('.pagination').addEventListener('click', handlePagination);
}

// Load departments data
async function loadDepartments() {
    showLoading();
    try {
        const search = document.getElementById('searchInput').value;
        const response = await fetch(`/api/departments?page=${currentPage}&search=${search}`);
        const data = await response.json();
        
        if (data.success) {
            departments = data.data;
            renderDepartmentsTable();
            updatePagination(data.total, data.per_page);
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('Có lỗi xảy ra khi tải dữ liệu phòng ban');
        console.error('Error loading departments:', error);
    } finally {
        hideLoading();
    }
}

// Load managers for dropdowns
async function loadManagers() {
    try {
        const response = await fetch('/api/employees/managers');
        const data = await response.json();
        
        if (data.success) {
            managers = data.data;
            updateManagerDropdowns();
        }
    } catch (error) {
        console.error('Error loading managers:', error);
    }
}

// Update manager dropdowns in both add and edit forms
function updateManagerDropdowns() {
    const managerSelects = [
        document.getElementById('manager'),
        document.getElementById('editManager')
    ];

    managerSelects.forEach(select => {
        if (select) {
            select.innerHTML = '<option value="">Chọn trưởng phòng</option>';
            managers.forEach(manager => {
                const option = document.createElement('option');
                option.value = manager.id;
                option.textContent = `${manager.name} (${manager.employee_id})`;
                select.appendChild(option);
            });
        }
    });
}

// Render departments table
function renderDepartmentsTable() {
    const tbody = document.getElementById('departmentsTableBody');
    tbody.innerHTML = '';

    departments.forEach(department => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${department.code}</td>
            <td>${department.name}</td>
            <td>${department.manager_name || '-'}</td>
            <td>${department.employee_count}</td>
            <td>${department.description || '-'}</td>
            <td>
                <div class="btn-group">
                    <button class="btn btn-sm btn-primary" onclick="showEditModal('${department.id}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="showDeleteModal('${department.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Show edit modal
function showEditModal(departmentId) {
    const department = departments.find(d => d.id === departmentId);
    if (department) {
        document.getElementById('editDepartmentId').value = department.id;
        document.getElementById('editDepartmentCode').value = department.code;
        document.getElementById('editDepartmentName').value = department.name;
        document.getElementById('editManager').value = department.manager_id || '';
        document.getElementById('editDescription').value = department.description || '';
        
        const modal = new bootstrap.Modal(document.getElementById('editDepartmentModal'));
        modal.show();
    }
}

// Show delete modal
function showDeleteModal(departmentId) {
    document.getElementById('confirmDeleteBtn').dataset.departmentId = departmentId;
    const modal = new bootstrap.Modal(document.getElementById('deleteDepartmentModal'));
    modal.show();
}

// Handle add department
async function handleAddDepartment() {
    showLoading();
    try {
        const formData = {
            code: document.getElementById('departmentCode').value,
            name: document.getElementById('departmentName').value,
            manager_id: document.getElementById('manager').value,
            description: document.getElementById('description').value
        };

        const response = await fetch('/api/departments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        if (data.success) {
            showSuccess('Thêm phòng ban thành công');
            bootstrap.Modal.getInstance(document.getElementById('addDepartmentModal')).hide();
            document.getElementById('addDepartmentForm').reset();
            loadDepartments();
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('Có lỗi xảy ra khi thêm phòng ban');
        console.error('Error adding department:', error);
    } finally {
        hideLoading();
    }
}

// Handle update department
async function handleUpdateDepartment() {
    showLoading();
    try {
        const departmentId = document.getElementById('editDepartmentId').value;
        const formData = {
            code: document.getElementById('editDepartmentCode').value,
            name: document.getElementById('editDepartmentName').value,
            manager_id: document.getElementById('editManager').value,
            description: document.getElementById('editDescription').value
        };

        const response = await fetch(`/api/departments/${departmentId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        if (data.success) {
            showSuccess('Cập nhật phòng ban thành công');
            bootstrap.Modal.getInstance(document.getElementById('editDepartmentModal')).hide();
            loadDepartments();
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('Có lỗi xảy ra khi cập nhật phòng ban');
        console.error('Error updating department:', error);
    } finally {
        hideLoading();
    }
}

// Handle delete department
async function handleDeleteDepartment() {
    showLoading();
    try {
        const departmentId = document.getElementById('confirmDeleteBtn').dataset.departmentId;
        const response = await fetch(`/api/departments/${departmentId}`, {
            method: 'DELETE'
        });

        const data = await response.json();
        if (data.success) {
            showSuccess('Xóa phòng ban thành công');
            bootstrap.Modal.getInstance(document.getElementById('deleteDepartmentModal')).hide();
            loadDepartments();
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('Có lỗi xảy ra khi xóa phòng ban');
        console.error('Error deleting department:', error);
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
            loadDepartments();
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