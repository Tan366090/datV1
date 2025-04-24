class EmployeeList {
    constructor() {
        this.currentPage = 1;
        this.totalPages = 1;
        this.pageSize = 10;
        this.employees = [];
        this.departments = [];
        this.positions = [];
        this.filters = {
            search: '',
            department: '',
            status: ''
        };

        this.initializeElements();
        this.setupEventListeners();
        this.loadDepartments();
        this.loadPositions();
        this.loadEmployees();
    }

    initializeElements() {
        // Table elements
        this.employeeTable = document.getElementById('employeeTable');
        this.tableBody = this.employeeTable.querySelector('tbody');

        // Filter elements
        this.searchInput = document.getElementById('searchInput');
        this.departmentFilter = document.getElementById('departmentFilter');
        this.statusFilter = document.getElementById('statusFilter');

        // Pagination elements
        this.prevPageBtn = document.getElementById('prevPage');
        this.nextPageBtn = document.getElementById('nextPage');
        this.pageNumbers = document.getElementById('pageNumbers');

        // Modal elements
        this.employeeModal = new bootstrap.Modal(document.getElementById('employeeModal'));
        this.employeeForm = document.getElementById('employeeForm');
        this.saveEmployeeBtn = document.getElementById('saveEmployeeBtn');
        this.modalTitle = document.getElementById('modalTitle');
        this.addEmployeeBtn = document.getElementById('addEmployeeBtn');
        this.exportBtn = document.getElementById('exportBtn');
    }

    setupEventListeners() {
        // Search input
        this.searchInput.addEventListener('input', (e) => {
            this.filters.search = e.target.value;
            this.currentPage = 1;
            this.loadEmployees();
        });

        // Department filter
        this.departmentFilter.addEventListener('change', (e) => {
            this.filters.department = e.target.value;
            this.currentPage = 1;
            this.loadEmployees();
        });

        // Status filter
        this.statusFilter.addEventListener('change', (e) => {
            this.filters.status = e.target.value;
            this.currentPage = 1;
            this.loadEmployees();
        });

        // Pagination
        this.prevPageBtn.addEventListener('click', () => {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.loadEmployees();
            }
        });

        this.nextPageBtn.addEventListener('click', () => {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.loadEmployees();
            }
        });

        // Add employee button
        this.addEmployeeBtn.addEventListener('click', () => {
            this.openAddModal();
        });

        // Save employee button
        this.saveEmployeeBtn.addEventListener('click', () => {
            this.saveEmployee();
        });

        // Export button
        this.exportBtn.addEventListener('click', () => {
            this.exportToExcel();
        });
    }

    async loadDepartments() {
        try {
            const response = await fetch('/api/departments');
            const data = await response.json();
            
            if (data.success) {
                this.departments = data.data;
                this.renderDepartmentOptions();
            }
        } catch (error) {
            console.error('Error loading departments:', error);
            this.showNotification('Lỗi khi tải danh sách phòng ban', 'error');
        }
    }

    async loadPositions() {
        try {
            const response = await fetch('/api/positions');
            const data = await response.json();
            
            if (data.success) {
                this.positions = data.data;
                this.renderPositionOptions();
            }
        } catch (error) {
            console.error('Error loading positions:', error);
            this.showNotification('Lỗi khi tải danh sách vị trí', 'error');
        }
    }

    async loadEmployees() {
        try {
            const queryParams = new URLSearchParams({
                page: this.currentPage,
                pageSize: this.pageSize,
                search: this.filters.search,
                department: this.filters.department,
                status: this.filters.status
            });

            const response = await fetch(`/api/employees?${queryParams}`);
            const data = await response.json();
            
            if (data.success) {
                this.employees = data.data.employees;
                this.totalPages = Math.ceil(data.data.total / this.pageSize);
                this.renderEmployees();
                this.renderPagination();
            }
        } catch (error) {
            console.error('Error loading employees:', error);
            this.showNotification('Lỗi khi tải danh sách nhân viên', 'error');
        }
    }

    renderEmployees() {
        this.tableBody.innerHTML = '';
        
        this.employees.forEach(employee => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${employee.employee_id}</td>
                <td>${employee.full_name}</td>
                <td>${employee.gender === 'male' ? 'Nam' : employee.gender === 'female' ? 'Nữ' : 'Khác'}</td>
                <td>${this.formatDate(employee.birth_date)}</td>
                <td>${employee.phone}</td>
                <td>${employee.email}</td>
                <td>${this.getDepartmentName(employee.department_id)}</td>
                <td>${this.getPositionName(employee.position_id)}</td>
                <td>
                    <span class="status-badge ${employee.status === 'active' ? 'status-active' : 'status-inactive'}">
                        ${employee.status === 'active' ? 'Đang làm việc' : 'Đã nghỉ việc'}
                    </span>
                </td>
                <td>
                    <button class="action-btn view-btn" onclick="employeeList.viewEmployee(${employee.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn edit-btn" onclick="employeeList.openEditModal(${employee.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn delete-btn" onclick="employeeList.deleteEmployee(${employee.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            this.tableBody.appendChild(row);
        });
    }

    renderPagination() {
        this.pageNumbers.innerHTML = '';
        
        for (let i = 1; i <= this.totalPages; i++) {
            const button = document.createElement('button');
            button.textContent = i;
            button.classList.toggle('active', i === this.currentPage);
            button.addEventListener('click', () => {
                this.currentPage = i;
                this.loadEmployees();
            });
            this.pageNumbers.appendChild(button);
        }

        this.prevPageBtn.disabled = this.currentPage === 1;
        this.nextPageBtn.disabled = this.currentPage === this.totalPages;
    }

    renderDepartmentOptions() {
        this.departmentFilter.innerHTML = `
            <option value="">Tất cả phòng ban</option>
            ${this.departments.map(dept => `
                <option value="${dept.id}">${dept.name}</option>
            `).join('')}
        `;

        // Also update department options in the form
        const formDepartmentSelect = this.employeeForm.querySelector('select[name="department_id"]');
        if (formDepartmentSelect) {
            formDepartmentSelect.innerHTML = this.departments.map(dept => `
                <option value="${dept.id}">${dept.name}</option>
            `).join('');
        }
    }

    renderPositionOptions() {
        const formPositionSelect = this.employeeForm.querySelector('select[name="position_id"]');
        if (formPositionSelect) {
            formPositionSelect.innerHTML = this.positions.map(pos => `
                <option value="${pos.id}">${pos.name}</option>
            `).join('');
        }
    }

    openAddModal() {
        this.modalTitle.textContent = 'Thêm nhân viên';
        this.employeeForm.reset();
        this.employeeModal.show();
    }

    openEditModal(employeeId) {
        const employee = this.employees.find(emp => emp.id === employeeId);
        if (!employee) return;

        this.modalTitle.textContent = 'Chỉnh sửa nhân viên';
        
        // Fill form with employee data
        Object.keys(employee).forEach(key => {
            const input = this.employeeForm.querySelector(`[name="${key}"]`);
            if (input) {
                input.value = employee[key];
            }
        });

        this.employeeModal.show();
    }

    async saveEmployee() {
        try {
            const formData = new FormData(this.employeeForm);
            const employeeData = Object.fromEntries(formData.entries());

            const response = await fetch('/api/employees', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(employeeData)
            });

            const data = await response.json();
            
            if (data.success) {
                this.employeeModal.hide();
                this.showNotification('Lưu nhân viên thành công', 'success');
                this.loadEmployees();
            } else {
                this.showNotification(data.error || 'Lỗi khi lưu nhân viên', 'error');
            }
        } catch (error) {
            console.error('Error saving employee:', error);
            this.showNotification('Lỗi khi lưu nhân viên', 'error');
        }
    }

    async deleteEmployee(employeeId) {
        if (!confirm('Bạn có chắc chắn muốn xóa nhân viên này?')) return;

        try {
            const response = await fetch(`/api/employees/${employeeId}`, {
                method: 'DELETE'
            });

            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Xóa nhân viên thành công', 'success');
                this.loadEmployees();
            } else {
                this.showNotification(data.error || 'Lỗi khi xóa nhân viên', 'error');
            }
        } catch (error) {
            console.error('Error deleting employee:', error);
            this.showNotification('Lỗi khi xóa nhân viên', 'error');
        }
    }

    viewEmployee(employeeId) {
        window.location.href = `/admin/employees/view.html?id=${employeeId}`;
    }

    async exportToExcel() {
        try {
            const queryParams = new URLSearchParams({
                search: this.filters.search,
                department: this.filters.department,
                status: this.filters.status
            });

            const response = await fetch(`/api/employees/export?${queryParams}`);
            const blob = await response.blob();
            
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'danh_sach_nhan_vien.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            a.remove();
        } catch (error) {
            console.error('Error exporting to Excel:', error);
            this.showNotification('Lỗi khi xuất file Excel', 'error');
        }
    }

    getDepartmentName(departmentId) {
        const department = this.departments.find(dept => dept.id === departmentId);
        return department ? department.name : '';
    }

    getPositionName(positionId) {
        const position = this.positions.find(pos => pos.id === positionId);
        return position ? position.name : '';
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN');
    }

    showNotification(message, type = 'info') {
        // Implement notification system
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.employeeList = new EmployeeList();
}); 