// Employee Form Handler
class EmployeeFormHandler {
    constructor() {
        this.departmentSelect = document.getElementById('departmentId');
        this.positionSelect = document.getElementById('positionId');
        this.contractTypeSelect = document.getElementById('contractType');
        this.addEmployeeForm = document.getElementById('addEmployeeForm');
        this.saveButton = document.getElementById('saveEmployeeBtn');
        
        this.initializeEventListeners();
        this.loadInitialData();
    }

    initializeEventListeners() {
        // Khi phòng ban thay đổi, load lại chức vụ tương ứng
        this.departmentSelect.addEventListener('change', () => this.loadPositionsByDepartment());
        
        // Xử lý khi click nút Lưu
        this.saveButton.addEventListener('click', (e) => {
            e.preventDefault();
            this.validateAndSaveEmployee();
        });

        // Thêm validation cho các trường input
        this.addEmployeeForm.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('input', () => {
                this.validateField(input);
                this.syncNameFields(input);
            });
            input.addEventListener('blur', () => this.validateField(input));
        });
    }

    // Hàm đồng bộ giữa trường name và full_name
    syncNameFields(input) {
        const fieldName = input.getAttribute('id');
        if (fieldName === 'employeeName' || fieldName === 'employeeFullName') {
            const nameInput = document.getElementById('employeeName');
            const fullNameInput = document.getElementById('employeeFullName');
            
            if (nameInput && fullNameInput) {
                const nameValue = nameInput.value.trim();
                const fullNameValue = fullNameInput.value.trim();

                if (fieldName === 'employeeName') {
                    // Nếu người dùng đang nhập name
                    if (nameValue && !fullNameValue) {
                        // Nếu name có giá trị và full_name trống, điền full_name
                        fullNameInput.value = nameValue;
                        this.validateField(fullNameInput);
                    }
                } else if (fieldName === 'employeeFullName') {
                    // Nếu người dùng đang nhập full_name
                    if (fullNameValue && !nameValue) {
                        // Nếu full_name có giá trị và name trống, điền name
                        nameInput.value = fullNameValue;
                        this.validateField(nameInput);
                    }
                }
            }
        }
    }

    validateField(input) {
        const value = input.value.trim();
        const fieldName = input.getAttribute('id');
        let isValid = true;
        let message = '';

        switch(fieldName) {
            case 'employeeName':
            case 'employeeFullName':
                // Tên chỉ được chứa chữ cái, dấu cách và dấu tiếng Việt
                const nameRegex = /^[a-zA-ZÀ-ỹ\s]+$/;
                if (value.length < 2) {
                    isValid = false;
                    message = 'Tên phải có ít nhất 2 ký tự';
                } else if (!nameRegex.test(value)) {
                    isValid = false;
                    message = 'Tên chỉ được chứa chữ cái và dấu cách';
                } else {
                    message = 'Tên hợp lệ';
                }
                break;
            case 'employeeEmail':
                // Email phải đúng định dạng và có tên miền hợp lệ
                const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    message = 'Email phải có định dạng: example@domain.com';
                } else {
                    message = 'Email hợp lệ';
                }
                break;
            case 'employeePhone':
                // Số điện thoại phải bắt đầu bằng 0 và có 10 chữ số
                const phoneRegex = /^0[0-9]{9}$/;
                if (!phoneRegex.test(value)) {
                    isValid = false;
                    message = 'Số điện thoại phải bắt đầu bằng 0 và có 10 chữ số';
                } else {
                    message = 'Số điện thoại hợp lệ';
                }
                break;
            case 'employeeBirthday':
                if (value) {
                    const birthDate = new Date(value);
                    const today = new Date();
                    const age = today.getFullYear() - birthDate.getFullYear();
                    if (age < 18) {
                        isValid = false;
                        message = 'Nhân viên phải từ 18 tuổi trở lên';
                    } else if (age > 65) {
                        isValid = false;
                        message = 'Nhân viên không được quá 65 tuổi';
                    } else {
                        message = 'Tuổi hợp lệ';
                    }
                } else {
                    message = '';
                }
                break;
            case 'employeeAddress':
                // Địa chỉ phải có ít nhất 10 ký tự và không chứa ký tự đặc biệt
                const addressRegex = /^[a-zA-Z0-9À-ỹ\s.,-]{10,}$/;
                if (value && !addressRegex.test(value)) {
                    isValid = false;
                    message = 'Địa chỉ phải có ít nhất 10 ký tự và không chứa ký tự đặc biệt';
                } else {
                    message = 'Địa chỉ hợp lệ';
                }
                break;
            case 'departmentId':
                if (value === '') {
                    isValid = false;
                    message = 'Vui lòng chọn phòng ban';
                } else {
                    message = 'Đã chọn phòng ban';
                }
                break;
            case 'positionId':
                if (value === '') {
                    isValid = false;
                    message = 'Vui lòng chọn chức vụ';
                } else if (value === 'new') {
                    const newPosition = document.getElementById('newPosition').value.trim();
                    if (!newPosition) {
                        isValid = false;
                        message = 'Vui lòng nhập tên chức vụ mới';
                    } else if (newPosition.length < 3) {
                        isValid = false;
                        message = 'Tên chức vụ phải có ít nhất 3 ký tự';
                    } else {
                        message = 'Tên chức vụ hợp lệ';
                    }
                } else {
                    message = 'Đã chọn chức vụ';
                }
                break;
            case 'contractType':
                if (value === '') {
                    isValid = false;
                    message = 'Vui lòng chọn loại hợp đồng';
                } else {
                    message = 'Đã chọn loại hợp đồng';
                }
                break;
            case 'baseSalary':
                // Lương phải là số dương, tối thiểu 500 đồng và không quá 100 triệu
                const salary = parseFloat(value);
                if (isNaN(salary) || salary <= 0) {
                    isValid = false;
                    message = 'Lương phải là số dương';
                } else if (salary < 500) {
                    isValid = false;
                    message = 'Lương phải lớn hơn hoặc bằng 500 đồng';
                } else if (salary > 100000000) {
                    isValid = false;
                    message = 'Lương không được vượt quá 100 triệu';
                } else {
                    message = 'Lương hợp lệ';
                }
                break;
            case 'contractStartDate':
                if (value === '') {
                    isValid = false;
                    message = 'Vui lòng chọn ngày bắt đầu hợp đồng';
                } else {
                    const startDate = new Date(value);
                    const today = new Date();
                    if (startDate < today) {
                        isValid = false;
                        message = 'Ngày bắt đầu hợp đồng không được nhỏ hơn ngày hiện tại';
                    } else {
                        message = 'Ngày hợp lệ';
                    }
                }
                break;
            // Kiểm tra các trường của thành viên gia đình
            case 'member-name':
                if (value.length < 2) {
                    isValid = false;
                    message = 'Tên thành viên phải có ít nhất 2 ký tự';
                } else if (!/^[a-zA-ZÀ-ỹ\s]+$/.test(value)) {
                    isValid = false;
                    message = 'Tên thành viên chỉ được chứa chữ cái và dấu cách';
                } else {
                    message = 'Tên thành viên hợp lệ';
                }
                break;
            case 'relationship':
                if (value === '') {
                    isValid = false;
                    message = 'Vui lòng chọn mối quan hệ';
                } else {
                    message = 'Mối quan hệ hợp lệ';
                }
                break;
            case 'member-birthday':
                if (value) {
                    const memberBirthDate = new Date(value);
                    const today = new Date();
                    const age = today.getFullYear() - memberBirthDate.getFullYear();
                    if (age < 0) {
                        isValid = false;
                        message = 'Ngày sinh không được lớn hơn ngày hiện tại';
                    } else if (age > 120) {
                        isValid = false;
                        message = 'Tuổi không hợp lệ';
                    } else {
                        message = 'Ngày sinh hợp lệ';
                    }
                } else {
                    message = '';
                }
                break;
            case 'member-occupation':
                if (value && value.length < 2) {
                    isValid = false;
                    message = 'Nghề nghiệp phải có ít nhất 2 ký tự';
                } else if (value && !/^[a-zA-ZÀ-ỹ\s]+$/.test(value)) {
                    isValid = false;
                    message = 'Nghề nghiệp chỉ được chứa chữ cái và dấu cách';
                } else {
                    message = 'Nghề nghiệp hợp lệ';
                }
                break;
        }

        // Cập nhật trạng thái input
        input.classList.remove('valid', 'invalid', 'pending');
        
        if (value === '') {
            // Nếu trường bắt buộc và chưa nhập
            if (input.hasAttribute('required')) {
                input.classList.add('invalid');
                message = 'Trường này là bắt buộc';
            } else {
                // Nếu trường không bắt buộc và chưa nhập
                input.classList.add('pending');
                message = '';
            }
        } else {
            // Nếu đã nhập giá trị
            input.classList.add(isValid ? 'valid' : 'invalid');
        }

        // Cập nhật thông báo
        let messageElement = input.nextElementSibling;
        if (!messageElement || !messageElement.classList.contains('validation-message')) {
            messageElement = document.createElement('div');
            messageElement.className = 'validation-message';
            input.parentNode.insertBefore(messageElement, input.nextSibling);
        }
        messageElement.textContent = message;
        messageElement.className = 'validation-message ' + (value === '' && !input.hasAttribute('required') ? 'pending' : (isValid ? 'valid' : 'invalid'));
    }

    validateAndSaveEmployee() {
        let isValid = true;
        const invalidFields = [];

        // Validate tất cả các trường bắt buộc
        this.addEmployeeForm.querySelectorAll('input[required], select[required]').forEach(input => {
            this.validateField(input);
            if (!input.value.trim()) {
                isValid = false;
                invalidFields.push(input.getAttribute('id'));
            }
        });

        // Validate thông tin gia đình
        const familyMembers = document.querySelectorAll('.family-member');
        familyMembers.forEach(member => {
            const nameInput = member.querySelector('.member-name');
            const relationshipInput = member.querySelector('.relationship');
            
            // Validate tên thành viên
            if (nameInput.value.trim()) {
                this.validateField(nameInput);
                if (!nameInput.classList.contains('valid')) {
                    isValid = false;
                    invalidFields.push('member-name');
                }
            }
            
            // Validate mối quan hệ
            if (relationshipInput.value.trim()) {
                this.validateField(relationshipInput);
                if (!relationshipInput.classList.contains('valid')) {
                    isValid = false;
                    invalidFields.push('relationship');
                }
            }
            
            // Validate ngày sinh nếu có
            const birthdayInput = member.querySelector('.member-birthday');
            if (birthdayInput.value.trim()) {
                this.validateField(birthdayInput);
                if (!birthdayInput.classList.contains('valid')) {
                    isValid = false;
                    invalidFields.push('member-birthday');
                }
            }
            
            // Validate nghề nghiệp nếu có
            const occupationInput = member.querySelector('.member-occupation');
            if (occupationInput.value.trim()) {
                this.validateField(occupationInput);
                if (!occupationInput.classList.contains('valid')) {
                    isValid = false;
                    invalidFields.push('member-occupation');
                }
            }
        });

        if (!isValid) {
            this.showNotification('Vui lòng kiểm tra lại thông tin đã nhập', 'error');
            invalidFields.forEach(fieldId => {
                const input = document.getElementById(fieldId);
                if (input) {
                    input.classList.add('invalid');
                    input.focus();
                }
            });
            return;
        }

        this.saveEmployee();
    }

    async loadInitialData() {
        try {
            // Load danh sách phòng ban
            await this.loadDepartments();
            
            // Load danh sách loại hợp đồng
            await this.loadContractTypes();
        } catch (error) {
            console.error('Error loading initial data:', error);
            this.showNotification('Có lỗi xảy ra khi tải dữ liệu', 'error');
        }
    }

    async loadDepartments() {
        try {
            const response = await fetch('/api/departments/list');
            const data = await response.json();
            
            if (data.success) {
                // Xóa các option cũ (giữ lại option mặc định)
                while (this.departmentSelect.options.length > 1) {
                    this.departmentSelect.remove(1);
                }
                
                // Thêm các phòng ban mới
                data.data.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept.id;
                    option.textContent = dept.name;
                    this.departmentSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading departments:', error);
            // this.showNotification('Không thể tải danh sách phòng ban', 'error');
        }
    }

    async loadPositionsByDepartment() {
        const departmentId = this.departmentSelect.value;
        
        if (!departmentId) {
            // Nếu không chọn phòng ban, reset chức vụ
            this.resetPositionSelect();
            return;
        }

        try {
            const response = await fetch(`/qlnhansu_V2/backend/src/api/positions.php?department_id=${departmentId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Positions Response:', data);

            // Lấy select element cho vị trí
            const positionSelect = document.getElementById('positionId');
            if (!positionSelect) return;

            // Reset options
            positionSelect.innerHTML = '<option value="">Chọn chức vụ</option>';
            
            // Thêm các vị trí vào select
            let positions = [];
            if (Array.isArray(data)) {
                positions = data;
            } else if (data.data && Array.isArray(data.data)) {
                positions = data.data;
            }

            // Nếu không có chức vụ nào, thêm option "Thêm chức vụ mới"
            if (positions.length === 0) {
                const newOption = document.createElement('option');
                newOption.value = 'new';
                newOption.textContent = '+ Thêm chức vụ mới';
                positionSelect.appendChild(newOption);
                
                // Hiển thị ô nhập chức vụ mới
                const newPositionGroup = document.getElementById('newPositionGroup');
                const newPositionInput = document.getElementById('newPosition');
                newPositionGroup.style.display = 'block';
                newPositionInput.required = true;
                newPositionInput.disabled = false;
                newPositionInput.style.backgroundColor = '#fff';
            } else {
                // Thêm các chức vụ hiện có
                positions.forEach(position => {
                    if (position && position.id && position.name) {
                    const option = document.createElement('option');
                    option.value = position.id;
                    option.textContent = position.name;
                        positionSelect.appendChild(option);
                    }
                });

                // Ẩn ô nhập chức vụ mới
                const newPositionGroup = document.getElementById('newPositionGroup');
                const newPositionInput = document.getElementById('newPosition');
                newPositionGroup.style.display = 'none';
                newPositionInput.value = '';
                newPositionInput.required = false;
                newPositionInput.disabled = true;
                newPositionInput.style.backgroundColor = '#f8f9fa';
            }

        } catch (error) {
            console.error('Error loading positions:', error);
            this.showNotification('Không thể tải danh sách chức vụ', 'error');
        }
    }

    async loadContractTypes() {
        try {
            const response = await fetch('/api/contract-types');
            const data = await response.json();
            
            if (data.success) {
                // Xóa các option cũ (giữ lại option mặc định)
                while (this.contractTypeSelect.options.length > 1) {
                    this.contractTypeSelect.remove(1);
                }
                
                // Thêm các loại hợp đồng mới
                data.data.forEach(contractType => {
                    const option = document.createElement('option');
                    option.value = contractType.id;
                    option.textContent = contractType.name;
                    this.contractTypeSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading contract types:', error);
            // this.showNotification('Không thể tải danh sách loại hợp đồng', 'error');
        }
    }

    resetPositionSelect() {
        while (this.positionSelect.options.length > 1) {
            this.positionSelect.remove(1);
        }
    }

    async saveEmployee() {
        try {
            this.showLoading(true);

            const formData = new FormData(this.addEmployeeForm);
            const name = formData.get('employeeName');
            const fullName = formData.get('employeeFullName');

            // Xử lý name và full_name
            let finalName = '';
            let finalFullName = '';

            if (name && !fullName) {
                // Nếu có name nhưng không có full_name, dùng name cho cả hai
                finalName = name;
                finalFullName = name;
            } else if (!name && fullName) {
                // Nếu có full_name nhưng không có name, dùng full_name cho cả hai
                finalName = fullName;
                finalFullName = fullName;
            } else if (name && fullName) {
                // Nếu có cả hai, ưu tiên dùng full_name
                finalName = fullName;
                finalFullName = fullName;
            }

        const employeeData = {
                name: finalName,
                full_name: finalFullName,
            email: formData.get('employeeEmail'),
            phone: formData.get('employeePhone'),
            birthday: formData.get('employeeBirthday'),
            address: formData.get('employeeAddress'),
                employee_code: formData.get('employeeCode'),
                department_id: formData.get('departmentId'),
                position_id: formData.get('positionId'),
                hire_date: formData.get('hireDate'),
                contract_type: formData.get('contractType'),
                base_salary: formData.get('baseSalary'),
                contract_start_date: formData.get('contractStartDate'),
                family_members: this.getFamilyMembersData()
        };

            const response = await fetch('/qlnhansu_V2/backend/src/api/employees.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(employeeData)
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Thêm nhân viên thành công', 'success');
                this.closeModal();
                // Có thể thêm code để refresh danh sách nhân viên ở đây
            } else {
                throw new Error(result.message || 'Có lỗi xảy ra khi thêm nhân viên');
            }
        } catch (error) {
            console.error('Error saving employee:', error);
            this.showNotification(error.message, 'error');
        } finally {
            this.showLoading(false);
        }
    }

    getFamilyMembersData() {
        const members = [];
        const memberElements = document.querySelectorAll('.family-member');
        
        memberElements.forEach(member => {
            members.push({
                name: member.querySelector('.member-name').value,
                relationship: member.querySelector('.relationship').value,
                birthday: member.querySelector('.member-birthday').value,
                occupation: member.querySelector('.member-occupation').value,
                isDependent: member.querySelector('.member-dependent').checked
            });
        });

        return members;
    }

    showLoading(show) {
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = show ? 'flex' : 'none';
        }
    }

    closeModal() {
        const modal = document.getElementById('addEmployeeModal');
        if (modal) {
            modal.classList.remove('active');
            this.addEmployeeForm.reset();
        }
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);

        // Xóa thông báo sau 5 giây
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 5000);
    }
}

// Khởi tạo khi DOM đã load xong
document.addEventListener('DOMContentLoaded', () => {
    new EmployeeFormHandler();
}); 