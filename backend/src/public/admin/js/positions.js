// Global variables
let currentPage = 1;
let totalPages = 1;
let positions = [];

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    // Load initial data
    loadPositions();
    
    // Set up event listeners
    setupEventListeners();
});

// Set up event listeners
function setupEventListeners() {
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', debounce(loadPositions, 300));
    document.getElementById('searchBtn').addEventListener('click', loadPositions);

    // Add position
    document.getElementById('savePositionBtn').addEventListener('click', handleAddPosition);

    // Edit position
    document.getElementById('updatePositionBtn').addEventListener('click', handleUpdatePosition);

    // Delete position
    document.getElementById('confirmDeleteBtn').addEventListener('click', handleDeletePosition);

    // Pagination
    document.querySelector('.pagination').addEventListener('click', handlePagination);
}

// Load positions data
async function loadPositions() {
    showLoading();
    try {
        const search = document.getElementById('searchInput').value;
        const response = await fetch(`/api/positions?page=${currentPage}&search=${search}`);
        const data = await response.json();
        
        if (data.success) {
            positions = data.data;
            renderPositionsTable();
            updatePagination(data.total, data.per_page);
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('Có lỗi xảy ra khi tải dữ liệu vị trí');
        console.error('Error loading positions:', error);
    } finally {
        hideLoading();
    }
}

// Render positions table
function renderPositionsTable() {
    const tbody = document.getElementById('positionsTableBody');
    tbody.innerHTML = '';

    positions.forEach(position => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${position.code}</td>
            <td>${position.name}</td>
            <td>${position.description || '-'}</td>
            <td>${position.requirements || '-'}</td>
            <td>${getLevelName(position.level)}</td>
            <td>${formatSalary(position.salary_range)}</td>
            <td>
                <div class="btn-group">
                    <button class="btn btn-sm btn-primary" onclick="showEditModal('${position.id}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="showDeleteModal('${position.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Get level name from level code
function getLevelName(level) {
    const levels = {
        1: 'Nhân viên',
        2: 'Trưởng nhóm',
        3: 'Trưởng phòng',
        4: 'Giám đốc'
    };
    return levels[level] || '-';
}

// Format salary range
function formatSalary(salaryRange) {
    if (!salaryRange) return '-';
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(salaryRange);
}

// Show edit modal
function showEditModal(positionId) {
    const position = positions.find(p => p.id === positionId);
    if (position) {
        document.getElementById('editPositionId').value = position.id;
        document.getElementById('editPositionCode').value = position.code;
        document.getElementById('editPositionName').value = position.name;
        document.getElementById('editDescription').value = position.description || '';
        document.getElementById('editRequirements').value = position.requirements || '';
        document.getElementById('editLevel').value = position.level;
        document.getElementById('editSalaryRange').value = position.salary_range;
        
        const modal = new bootstrap.Modal(document.getElementById('editPositionModal'));
        modal.show();
    }
}

// Show delete modal
function showDeleteModal(positionId) {
    document.getElementById('confirmDeleteBtn').dataset.positionId = positionId;
    const modal = new bootstrap.Modal(document.getElementById('deletePositionModal'));
    modal.show();
}

// Handle add position
async function handleAddPosition() {
    showLoading();
    try {
        const formData = {
            code: document.getElementById('positionCode').value,
            name: document.getElementById('positionName').value,
            description: document.getElementById('description').value,
            requirements: document.getElementById('requirements').value,
            level: document.getElementById('level').value,
            salary_range: document.getElementById('salaryRange').value
        };

        const response = await fetch('/api/positions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        if (data.success) {
            showSuccess('Thêm vị trí thành công');
            bootstrap.Modal.getInstance(document.getElementById('addPositionModal')).hide();
            document.getElementById('addPositionForm').reset();
            loadPositions();
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('Có lỗi xảy ra khi thêm vị trí');
        console.error('Error adding position:', error);
    } finally {
        hideLoading();
    }
}

// Handle update position
async function handleUpdatePosition() {
    showLoading();
    try {
        const positionId = document.getElementById('editPositionId').value;
        const formData = {
            code: document.getElementById('editPositionCode').value,
            name: document.getElementById('editPositionName').value,
            description: document.getElementById('editDescription').value,
            requirements: document.getElementById('editRequirements').value,
            level: document.getElementById('editLevel').value,
            salary_range: document.getElementById('editSalaryRange').value
        };

        const response = await fetch(`/api/positions/${positionId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        if (data.success) {
            showSuccess('Cập nhật vị trí thành công');
            bootstrap.Modal.getInstance(document.getElementById('editPositionModal')).hide();
            loadPositions();
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('Có lỗi xảy ra khi cập nhật vị trí');
        console.error('Error updating position:', error);
    } finally {
        hideLoading();
    }
}

// Handle delete position
async function handleDeletePosition() {
    showLoading();
    try {
        const positionId = document.getElementById('confirmDeleteBtn').dataset.positionId;
        const response = await fetch(`/api/positions/${positionId}`, {
            method: 'DELETE'
        });

        const data = await response.json();
        if (data.success) {
            showSuccess('Xóa vị trí thành công');
            bootstrap.Modal.getInstance(document.getElementById('deletePositionModal')).hide();
            loadPositions();
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('Có lỗi xảy ra khi xóa vị trí');
        console.error('Error deleting position:', error);
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
            loadPositions();
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