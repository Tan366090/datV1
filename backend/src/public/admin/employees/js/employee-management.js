// Khởi tạo các biến toàn cục
const loadingOverlay = {
    show: function() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    },
    hide: function() {
        document.getElementById('loadingOverlay').style.display = 'none';
    }
};

// Xử lý sự kiện khi trang được tải
document.addEventListener('DOMContentLoaded', async () => {
    // Thêm các event listeners
    addEventListeners();

    // Tải dữ liệu bộ lọc
    await loadFilters();

    // Tải dữ liệu nhân viên
    await loadEmployees();

    // Tải dữ liệu dashboard
    await loadDashboardStats();
});

// Thêm các event listeners
function addEventListeners() {
    // Xử lý tìm kiếm
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadEmployees(); // Tải lại danh sách nhân viên khi tìm kiếm
            }, 300); // Debounce 300ms
        });
    }

    // Xử lý lọc phòng ban
    const departmentFilter = document.getElementById('departmentFilter');
    if (departmentFilter) {
        departmentFilter.addEventListener('change', async (e) => {
            const departmentId = e.target.value;
            await loadPositionsByDepartment(departmentId);
        });
    }

    // Xử lý lọc chức vụ
    const positionFilter = document.getElementById('positionFilter');
    if (positionFilter) {
        positionFilter.addEventListener('change', () => {
            loadEmployees(); // Tải lại danh sách nhân viên khi thay đổi chức vụ
        });
    }

    // Xử lý lọc trạng thái
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', () => {
            loadEmployees(); // Tải lại danh sách nhân viên khi thay đổi trạng thái
        });
    }

    // Xử lý nút thêm nhân viên
    document.getElementById('addEmployeeBtn')?.addEventListener('click', () => {
        window.location.href = 'add.html';
    });

    // Xử lý các nút thao tác
    document.getElementById('employeeTableBody')?.addEventListener('click', (e) => {
        const target = e.target.closest('button');
        if (!target) return;

        const id = target.dataset.id;
        if (!id) return;

        if (target.classList.contains('edit-btn')) {
            window.location.href = `edit.html?id=${id}`;
        } else if (target.classList.contains('delete-btn')) {
            deleteEmployee(id);
        }
    });

    // Thêm sự kiện cho nút Xuất Excel
    const exportBtn = document.querySelector('.btn-export');
    if (exportBtn) {
        exportBtn.addEventListener('click', exportEmployeeTableToExcel);
    }

    // Thêm sự kiện cho nút Load lại dữ liệu
    const reloadBtn = document.querySelector('.btn-reload');
    if (reloadBtn) {
        reloadBtn.addEventListener('click', function() {
            loadEmployees();
        });
    }
}

// Hàm tải danh sách nhân viên
async function loadEmployees(page = 1, perPage = 10) {
    try {
        loadingOverlay.show();
        
        // Lấy các tham số lọc
        const search = document.getElementById('searchInput').value;
        const departmentId = document.getElementById('departmentFilter').value;
        const positionId = document.getElementById('positionFilter').value;
        const status = document.getElementById('statusFilter').value;

        // Tạo query string
        const params = new URLSearchParams({
            page: page,
            per_page: perPage,
            ...(search && { search }),
            ...(departmentId && { department_id: departmentId }),
            ...(positionId && { position_id: positionId }),
            ...(status && { status })
        });

        // Gọi API
        const response = await fetch(`/qlnhansu_V2/backend/src/api/employees.php?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        if (data.success && data.data) {
            displayEmployees(data.data);
            updatePagination(data.total, data.page, data.per_page);
        } else {
            displayEmployees([]); // Hiển thị "Không có dữ liệu"
            showError(data.message || 'Không thể tải danh sách nhân viên');
        }
    } catch (error) {
        console.error('Lỗi tải nhân viên:', error);
        displayEmployees([]); // Hiển thị "Không có dữ liệu" khi có lỗi
        if (error instanceof TypeError && error.message.includes('JSON')) {
            showError('Lỗi: Server trả về dữ liệu không hợp lệ');
        } else {
            showError('Có lỗi xảy ra khi tải danh sách nhân viên: ' + error.message);
        }
    } finally {
        loadingOverlay.hide();
    }
}

// Hàm cập nhật phân trang
function updatePagination(total, currentPage, perPage) {
    const totalPages = Math.ceil(total / perPage);
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    // Nút Previous
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `
        <a class="page-link" href="#" aria-label="Previous" ${currentPage === 1 ? 'tabindex="-1"' : ''}>
            <span aria-hidden="true">&laquo;</span>
        </a>
    `;
    prevLi.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage > 1) {
            loadEmployees(currentPage - 1, perPage);
        }
    });
    pagination.appendChild(prevLi);

    // Các nút trang
    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === currentPage ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
        li.addEventListener('click', (e) => {
            e.preventDefault();
            loadEmployees(i, perPage);
        });
        pagination.appendChild(li);
    }

    // Nút Next
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
    nextLi.innerHTML = `
        <a class="page-link" href="#" aria-label="Next" ${currentPage === totalPages ? 'tabindex="-1"' : ''}>
            <span aria-hidden="true">&raquo;</span>
        </a>
    `;
    nextLi.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage < totalPages) {
            loadEmployees(currentPage + 1, perPage);
        }
    });
    pagination.appendChild(nextLi);
}

// Hàm hiển thị danh sách nhân viên
function displayEmployees(employees) {
    const tbody = document.getElementById('employeeTableBody');
    tbody.innerHTML = '';

    if (!employees || employees.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td colspan="11" class="text-center py-4">
                <div class="no-data-message">
                    <i class="fas fa-info-circle"></i>
                    <p>Không có dữ liệu</p>
                </div>
            </td>
        `;
        tbody.appendChild(row);
        return;
    }

    // Sắp xếp nhân viên theo phòng ban
    const sortedEmployees = [...employees].sort((a, b) => {
        const deptA = a.department_name || '';
        const deptB = b.department_name || '';
        return deptA.localeCompare(deptB);
    });

    sortedEmployees.forEach((employee, index) => {
        const row = document.createElement('tr');
        
        // Chọn ảnh đại diện: ưu tiên avatar_url, nếu không có thì theo giới tính
        let avatarSrc = '';
        if (employee.avatar_url && employee.avatar_url.trim() !== '') {
            avatarSrc = employee.avatar_url;
        } else if (employee.gender === 'Female') {
            avatarSrc = 'human.png';
        } else {
            avatarSrc = 'employee.png';
        }

        // Format status
        const statusClass = employee.status === 'active' ? 'badge-success' : 
                          employee.status === 'inactive' ? 'badge-warning' : 
                          employee.status === 'terminated' ? 'badge-danger' : 'badge-info';
        const statusText = employee.status === 'active' ? 'Đang làm việc' :
                         employee.status === 'inactive' ? 'Nghỉ việc' :
                         employee.status === 'terminated' ? 'Đã nghỉ' : 'Nghỉ phép';

        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${employee.employee_code || ''}</td>
            <td>
                <img src="${avatarSrc}" 
                     alt="Avatar" 
                     class="rounded-circle"
                     width="40" 
                     height="40">
            </td>
            <td>${employee.full_name || employee.username || ''}</td>
            <td>${employee.birth_date ? new Date(employee.birth_date).toLocaleDateString('vi-VN') : ''}</td>
            <td>${employee.phone_number || ''}</td>
            <td><span class="badge ${statusClass}">${statusText}</span></td>
            <td data-department-id="${employee.department_id || ''}">${employee.department_name || ''}</td>
            <td data-position-id="${employee.position_id || ''}">${employee.position_name || ''}</td>
            <td>${employee.email || ''}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn-action btn-edit edit-btn" data-id="${employee.id}">Sửa</button>
                    <button class="btn-action btn-delete delete-btn" data-id="${employee.id}">Xóa</button>
                </div>
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

// Thêm CSS cho thông báo không có dữ liệu
const style = document.createElement('style');
style.textContent = `
    .no-data-message {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        color: #6c757d;
    }
    .no-data-message i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    .no-data-message p {
        margin: 0;
        font-size: 1.1rem;
    }
`;
document.head.appendChild(style);

// Hàm tải chức vụ theo phòng ban
async function loadPositionsByDepartment(departmentId) {
    try {
        const positionFilter = document.getElementById('positionFilter');
        if (!positionFilter) return;

        // Reset dropdown chức vụ
        positionFilter.innerHTML = '<option value="">Tất cả chức vụ</option>';

        // Nếu không chọn phòng ban, không cần load chức vụ
        if (!departmentId) {
            await loadEmployees(); // Tải lại danh sách nhân viên
            return;
        }

        // Gọi API lấy chức vụ theo phòng ban
        const response = await fetch(`/qlnhansu_V2/backend/src/api/positions.php?department_id=${departmentId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        if (data.success && data.data) {
            // Thêm các chức vụ vào dropdown
            data.data.forEach(pos => {
                const option = document.createElement('option');
                option.value = pos.id;
                option.textContent = pos.name;
                positionFilter.appendChild(option);
            });
        } else {
            showError(data.message || 'Không thể tải danh sách chức vụ');
        }

        // Tải lại danh sách nhân viên sau khi load chức vụ
        await loadEmployees();
    } catch (error) {
        console.error('Lỗi tải chức vụ:', error);
        showError('Có lỗi xảy ra khi tải danh sách chức vụ');
    }
}

// Hàm tải bộ lọc
async function loadFilters() {
    try {
        // Tải danh sách phòng ban
        const deptResponse = await fetch('/qlnhansu_V2/backend/src/api/departments.php', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (!deptResponse.ok) {
            throw new Error(`HTTP error! status: ${deptResponse.status}`);
        }

        const deptData = await deptResponse.json();
        
        if (deptData.success && deptData.data) {
            // Tải phòng ban
            const departmentFilter = document.getElementById('departmentFilter');
            if (departmentFilter) {
                departmentFilter.innerHTML = '<option value="">Tất cả phòng ban</option>';
                deptData.data.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept.id;
                    option.textContent = dept.name;
                    departmentFilter.appendChild(option);
                });
            }
        } else {
            showError(deptData.message || 'Không thể tải danh sách phòng ban');
        }

        // Reset dropdown chức vụ
        const positionFilter = document.getElementById('positionFilter');
        if (positionFilter) {
            positionFilter.innerHTML = '<option value="">Tất cả chức vụ</option>';
        }

        // Tải lại danh sách nhân viên sau khi load bộ lọc
        await loadEmployees();
    } catch (error) {
        console.error('Lỗi tải bộ lọc:', error);
        showError('Có lỗi xảy ra khi tải dữ liệu bộ lọc');
    }
}

// Hàm lọc nhân viên
function filterEmployees(searchTerm = null, departmentId = null, positionId = null, status = null) {
    const rows = document.querySelectorAll('#employeeTableBody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const name = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
        const empCode = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const deptId = row.querySelector('td:nth-child(8)').dataset.departmentId;
        const posId = row.querySelector('td:nth-child(9)').dataset.positionId;
        const stat = row.querySelector('td:nth-child(7)').textContent.toLowerCase();

        let show = true;

        // Lọc theo từ khóa tìm kiếm
        if (searchTerm) {
            const searchLower = searchTerm.toLowerCase();
            if (!name.includes(searchLower) && !empCode.includes(searchLower)) {
                show = false;
            }
        }

        // Lọc theo phòng ban
        if (departmentId && departmentId !== '') {
            if (deptId !== departmentId) {
                show = false;
            }
        }

        // Lọc theo chức vụ
        if (positionId && positionId !== '') {
            if (posId !== positionId) {
                show = false;
            }
        }

        // Lọc theo trạng thái
        if (status && status !== '') {
            const statusMap = {
                'active': 'đang làm việc',
                'inactive': 'nghỉ việc',
                'terminated': 'đã nghỉ',
                'on_leave': 'nghỉ phép'
            };
            if (!stat.includes(statusMap[status])) {
                show = false;
            }
        }

        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });

    // Cập nhật số lượng nhân viên hiển thị
    updateEmployeeCounts(visibleCount);
}

// Hàm cập nhật số lượng nhân viên
function updateEmployeeCounts(visibleCount) {
    const totalEmployees = document.getElementById('totalEmployees');
    if (totalEmployees) {
        totalEmployees.textContent = visibleCount;
    }
}

// Hàm hiển thị lỗi
function showError(message) {
    const notificationContainer = document.getElementById('notificationContainer');
    if (!notificationContainer) return;

    const notification = document.createElement('div');
    notification.className = 'alert alert-danger alert-dismissible fade show';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    notificationContainer.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Hàm hiển thị thành công
function showSuccess(message) {
    const notificationContainer = document.getElementById('notificationContainer');
    if (!notificationContainer) return;

    const notification = document.createElement('div');
    notification.className = 'alert alert-success alert-dismissible fade show';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    notificationContainer.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Hàm xóa nhân viên
window.deleteEmployee = async function(id) {
    if (confirm('Bạn có chắc chắn muốn xóa nhân viên này?')) {
        try {
            loadingOverlay.show();
            
            const response = await fetch(`/qlnhansu_V2/backend/src/api/employees.php?id=${id}`, {
                method: 'DELETE'
            });
            
            const data = await response.json();
            
            if (data.success) {
                showSuccess('Xóa nhân viên thành công');
                await loadEmployees();
            } else {
                showError(data.message || 'Không thể xóa nhân viên');
            }
        } catch (error) {
            console.error('Lỗi xóa nhân viên:', error);
            showError('Có lỗi xảy ra khi xóa nhân viên');
        } finally {
            loadingOverlay.hide();
        }
    }
};

// Hàm tải dữ liệu dashboard
async function loadDashboardStats() {
    try {
        // Lấy tổng số nhân viên
        const empRes = await fetch('/qlnhansu_V2/backend/src/api/employees.php?page=1&per_page=1', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        let totalEmployees = 0;
        if (empRes.ok) {
            const empData = await empRes.json();
            if (empData.total !== undefined) {
                totalEmployees = empData.total;
                document.getElementById('totalEmployees').textContent = totalEmployees;
            }
        }

        // Lấy tổng số phòng ban
        const deptRes = await fetch('/qlnhansu_V2/backend/src/api/departments.php', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        if (deptRes.ok) {
            const deptData = await deptRes.json();
            if (deptData.success && Array.isArray(deptData.data)) {
                document.getElementById('totalDepartments').textContent = deptData.data.length;
            }
        }

        // Lấy tổng số chức vụ (nếu muốn hiển thị)
        // const posRes = await fetch('/qlnhansu_V2/backend/src/api/positions.php', {
        //     method: 'GET',
        //     headers: {
        //         'Accept': 'application/json',
        //         'Content-Type': 'application/json'
        //     }
        // });
        // if (posRes.ok) {
        //     const posData = await posRes.json();
        //     if (posData.success && Array.isArray(posData.data)) {
        //         // document.getElementById('totalPositions').textContent = posData.data.length;
        //     }
        // }

        // Đang làm việc = tổng nhân viên (giả sử tất cả đều đang làm việc, hoặc cần API riêng để lấy số đang làm việc)
        document.getElementById('activeEmployees').textContent = totalEmployees;
        // Nhân viên mới: cần API riêng, hoặc để 0 nếu chưa có
        document.getElementById('newEmployees').textContent = 0;
    } catch (err) {
        console.error('Lỗi tải dữ liệu dashboard:', err);
    }
}

function exportEmployeeTableToExcel() {
    // Lấy bảng
    const table = document.querySelector('.employee-table table');
    if (!table) return;

    // Tạo mảng dữ liệu, loại bỏ cột "Thao tác"
    const rows = Array.from(table.querySelectorAll('tr')).map(tr => {
        const cells = Array.from(tr.querySelectorAll('th,td'));
        // Loại bỏ cột cuối cùng (Thao tác)
        return cells.slice(0, -1).map(cell => cell.innerText.trim());
    });

    // Tạo worksheet và workbook
    const ws = XLSX.utils.aoa_to_sheet(rows);

    // Định dạng: bôi đậm dòng tiêu đề, căn giữa, border
    const range = XLSX.utils.decode_range(ws['!ref']);
    for (let C = range.s.c; C <= range.e.c; ++C) {
        const cellAddress = XLSX.utils.encode_cell({ r: 0, c: C });
        if (!ws[cellAddress]) continue;
        ws[cellAddress].s = {
            font: { bold: true, color: { rgb: "FFFFFF" } },
            alignment: { horizontal: "center", vertical: "center" },
            fill: { fgColor: { rgb: "4F8CFF" } }
        };
    }
    // Thêm border cho toàn bộ bảng
    for (let R = range.s.r; R <= range.e.r; ++R) {
        for (let C = range.s.c; C <= range.e.c; ++C) {
            const cellAddress = XLSX.utils.encode_cell({ r: R, c: C });
            if (!ws[cellAddress]) continue;
            ws[cellAddress].s = ws[cellAddress].s || {};
            ws[cellAddress].s.border = {
                top: { style: "thin", color: { rgb: "CCCCCC" } },
                bottom: { style: "thin", color: { rgb: "CCCCCC" } },
                left: { style: "thin", color: { rgb: "CCCCCC" } },
                right: { style: "thin", color: { rgb: "CCCCCC" } }
            };
        }
    }
    // Tự động căn chỉnh độ rộng cột
    ws['!cols'] = rows[0].map((_, i) => {
        const maxLen = Math.max(...rows.map(row => (row[i] ? row[i].length : 0)));
        return { wch: Math.max(10, maxLen + 2) };
    });

    // Tạo workbook và xuất file
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "DanhSachNhanVien");
    XLSX.writeFile(wb, 'DanhSachNhanVien.xlsx');
} 