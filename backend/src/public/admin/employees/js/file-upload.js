// Hiển thị form thêm nhân viên
function showFormOption() {
    document.getElementById('formOption').classList.remove('hidden');
    document.getElementById('fileOption').classList.add('hidden');
}

// Hiển thị form upload file
function showFileOption() {
    document.getElementById('fileOption').classList.remove('hidden');
    document.getElementById('formOption').classList.add('hidden');
}

let previewedEmployees = [];

// Xem trước nội dung file
function previewFile() {
    hideUploadError();
    const fileInput = document.getElementById('employeeFile');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('Vui lòng chọn file txt');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const content = e.target.result;
        const employees = parseFileContent(content);
        previewedEmployees = employees;
        displayPreview(employees);
        document.getElementById('saveToDbBtn').style.display = (employees.length > 0) ? 'inline-block' : 'none';
    };
    reader.readAsText(file);
}

// Phân tích nội dung file
function parseFileContent(content) {
    const lines = content.split('\n');
    const employees = [];
    let currentEmployee = null;
    for (let i = 0; i < lines.length; i++) {
        const line = lines[i].trim();
        if (!line) continue;
        if (line.startsWith('EMP|')) {
            // Nếu có nhân viên trước đó, push vào mảng
            if (currentEmployee) employees.push(currentEmployee);
            const empParts = line.split('|');
            if (empParts.length < 13) {
                console.error(`Lỗi dòng ${i + 1}: Thiếu thông tin nhân viên`);
                continue;
            }
            currentEmployee = {
                employeeName: empParts[1].trim(),
                employeeFullName: empParts[2].trim(),
                employeeEmail: empParts[3].trim(),
                employeePhone: empParts[4].trim(),
                employeeBirthday: empParts[5].trim(),
                employeeAddress: empParts[6].trim(),
                departmentId: empParts[7].trim(),
                positionId: empParts[8].trim(),
                contractType: empParts[9].trim(),
                baseSalary: empParts[10].trim(),
                contractStartDate: empParts[11].trim(),
                contractEndDate: empParts[12].trim(),
                familyMembers: []
            };
        } else if (line.startsWith('FAM|') && currentEmployee) {
            const famParts = line.split('|');
            if (famParts.length < 6) {
                console.error(`Lỗi dòng ${i + 1}: Thiếu thông tin thành viên gia đình`);
                continue;
            }
            currentEmployee.familyMembers.push({
                name: famParts[1].trim(),
                relationship: famParts[2].trim(),
                birthday: famParts[3].trim(),
                occupation: famParts[4].trim(),
                is_dependent: famParts[5].trim() === '1'
            });
        }
    }
    // Push nhân viên cuối cùng nếu có
    if (currentEmployee) employees.push(currentEmployee);
    return employees;
}

// Hiển thị xem trước dữ liệu
function displayPreview(employees) {
    const tbody = document.getElementById('previewTableBody');
    tbody.innerHTML = '';
    employees.forEach(employee => {
        const famHtml = (employee.familyMembers && employee.familyMembers.length > 0)
            ? `<ul style='margin:0;padding-left:18px;'>` + employee.familyMembers.map(fam =>
                `<li><b>${fam.name}</b> (${fam.relationship})${fam.birthday ? ', ' + fam.birthday : ''}${fam.occupation ? ', ' + fam.occupation : ''}${fam.is_dependent ? ', Người phụ thuộc' : ''}</li>`
            ).join('') + '</ul>'
            : '<i>Không có</i>';
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${employee.employeeName}</td>
            <td>${employee.employeeFullName}</td>
            <td>${employee.employeeEmail}</td>
            <td>${employee.employeePhone}</td>
            <td>${employee.employeeBirthday}</td>
            <td>${employee.employeeAddress}</td>
            <td>${employee.departmentId}</td>
            <td>${employee.positionId}</td>
            <td>${employee.contractType}</td>
            <td>${employee.baseSalary}</td>
            <td>${employee.contractStartDate}</td>
            <td>${employee.contractEndDate}</td>
            <td>${famHtml}</td>
        `;
        tbody.appendChild(tr);
    });
    document.getElementById('previewSection').classList.remove('hidden');
}

function showUploadError(message) {
    const errDiv = document.getElementById('uploadError');
    errDiv.innerHTML = message;
    errDiv.style.display = 'block';
    // Cuộn lên đầu modal
    const modal = document.getElementById('addEmployeeByFileModal');
    if (modal) modal.scrollTop = 0;
}

function hideUploadError() {
    const errDiv = document.getElementById('uploadError');
    errDiv.innerHTML = '';
    errDiv.style.display = 'none';
}

function savePreviewedEmployees() {
    hideUploadError();
    if (!previewedEmployees || previewedEmployees.length === 0) {
        showUploadError('Không có dữ liệu để lưu');
        return;
    }
    const statusDiv = document.getElementById('uploadStatus');
    statusDiv.innerHTML = '<div class="alert alert-info">Đang lưu dữ liệu...</div>';
    fetch('/api/employees/batch', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(previewedEmployees)
    })
    .then(async response => {
        if (!response.ok) {
            let msg = 'Lỗi khi lưu dữ liệu';
            try {
                const err = await response.json();
                if (err && err.errors) {
                    msg += '<ul>' + err.errors.map(e => `<li>${e}</li>`).join('') + '</ul>';
                } else if (err && err.message) {
                    msg += '<br>' + err.message;
                }
            } catch {}
            showUploadError(msg);
            statusDiv.innerHTML = '';
            return;
        }
        const result = await response.json();
        statusDiv.innerHTML = `
            <div class="alert alert-success">
                <h5>Kết quả lưu:</h5>
                <p>Tổng số nhân viên: ${result.total}</p>
                <p>Thêm thành công: ${result.success}</p>
                <p>Thất bại: ${result.failed}</p>
                ${result.errors ? `<p>Chi tiết lỗi: ${result.errors.join('<br>')}</p>` : ''}
            </div>
        `;
        hideUploadError();
    })
    .catch(error => {
        showUploadError('Lỗi: ' + error.message);
        statusDiv.innerHTML = '';
    });
} 