document.addEventListener("DOMContentLoaded", function () {
    // Initial data (replace with actual data fetch if needed)
    let familyMembers = [
        { name: "Nguyễn Thị M", relation: "Mẹ", dob: "1968-03-15" },
        { name: "Nguyễn Văn C", relation: "Con", dob: "2015-11-20" },
        { name: "Nguyễn Thị D", relation: "Con", dob: "2018-07-02" },
    ];

    const familyTableBody = document.getElementById("familyTableBody");
    const btnAddMember = document.getElementById("btnAddMember");
    const memberNameInput = document.getElementById("memberName");
    const memberRelationSelect = document.getElementById("memberRelation");
    const memberDOBInput = document.getElementById("memberDOB");

    const btnEdit = document.querySelector(".btn-edit");
    const btnSave = document.querySelector(".btn-save");
    const btnCancel = document.querySelector(".btn-cancel");
    const formInputs = document.querySelectorAll(".user-details input, .user-details select"); // Select fields to make editable
    const avatarUploadInput = document.getElementById("avatarUpload");
    const avatarPreview = document.getElementById("avatarPreview");
    const cameraIconLabel = document.querySelector(".camera-icon");


    // --- Editing State ---
    let isEditingMode = false;
    let originalFieldValues = {}; // To store values before editing

    function toggleEditMode(edit) {
        isEditingMode = edit;

        // Toggle buttons visibility
        btnEdit.style.display = edit ? "none" : "flex"; // Keep flex for icon alignment
        btnSave.style.display = edit ? "flex" : "none";
        btnCancel.style.display = edit ? "flex" : "none";
        cameraIconLabel.style.display = edit ? "flex" : "none"; // Show camera icon only in edit mode


        // Enable/disable form fields and store original values
        formInputs.forEach(input => {
            if (edit) {
                originalFieldValues[input.id] = input.value; // Store original value
                input.readOnly = false;
                input.classList.add("editable"); // Add class for styling
            } else {
                input.readOnly = true;
                input.classList.remove("editable");
                // Restore original value if cancelled
                if (originalFieldValues[input.id] !== undefined) {
                     input.value = originalFieldValues[input.id];
                }
            }
        });

         // Also handle enabling/disabling family table actions (optional, depends on UX)
         toggleFamilyTableActions(!edit); // Disable family actions while editing main profile

         if(!edit) {
            originalFieldValues = {}; // Clear stored values after save/cancel
         }
    }

     function toggleFamilyTableActions(enabled) {
        const actionButtons = familyTableBody.querySelectorAll(".action-btn");
        actionButtons.forEach(button => {
            button.disabled = !enabled;
            button.style.opacity = enabled ? "1" : "0.5";
            button.style.cursor = enabled ? "pointer" : "not-allowed";
        });
         btnAddMember.disabled = !enabled; // Also disable the add button
         btnAddMember.style.opacity = enabled ? "1" : "0.5";

    }

    // Event listeners for edit/save/cancel buttons
    btnEdit.addEventListener("click", () => toggleEditMode(true));
    btnCancel.addEventListener("click", () => toggleEditMode(false));
    btnSave.addEventListener("click", () => {
        // --- Add validation here if needed ---
        // Example: Check if required fields are filled
        const fullName = document.getElementById("fullName").value.trim();
        if (!fullName) {
            alert("Họ và tên không được để trống.");
            return; // Prevent saving
        }

        // --- Add logic here to send updated data to the server ---
        console.log("Simulating save...");
        alert("Thông tin đã được cập nhật (mô phỏng).");
        // --- End of save logic ---

        toggleEditMode(false); // Exit edit mode after successful save
    });

     // Avatar Preview Logic
    avatarUploadInput.addEventListener("change", function(event) {
        const file = event.target.files[0];
        if (file && file.type.startsWith("image/")) {
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarPreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
             alert("Vui lòng chọn một file hình ảnh hợp lệ.");
        }
    });


    // --- Family Table Logic ---

    // Render bảng thông tin gia đình
    function renderFamilyTable() {
        familyTableBody.innerHTML = ""; // Clear existing rows
        familyMembers.forEach((member, index) => {
            const tr = document.createElement("tr");
            tr.setAttribute("data-index", index); // Add index for easier targeting

            if (member.isEditing) {
                // --- Render row in edit mode ---
                tr.classList.add("editing-row");
                tr.innerHTML = `
                    <td data-label="Tên thành viên">
                        <input type="text" class="edit-input" value="${member.name}" name="editName" required />
                    </td>
                    <td data-label="Mối quan hệ">
                        <select class="edit-input" name="editRelation">
                            <option value="Cha" ${member.relation === "Cha" ? "selected" : ""}>Cha</option>
                            <option value="Mẹ" ${member.relation === "Mẹ" ? "selected" : ""}>Mẹ</option>
                            <option value="Vợ" ${member.relation === "Vợ" ? "selected" : ""}>Vợ</option>
                            <option value="Chồng" ${member.relation === "Chồng" ? "selected" : ""}>Chồng</option>
                            <option value="Con" ${member.relation === "Con" ? "selected" : ""}>Con</option>
                            <option value="Anh" ${member.relation === "Anh" ? "selected" : ""}>Anh</option>
                            <option value="Chị" ${member.relation === "Chị" ? "selected" : ""}>Chị</option>
                            <option value="Em" ${member.relation === "Em" ? "selected" : ""}>Em</option>
                            <option value="Khác" ${member.relation === "Khác" ? "selected" : ""}>Khác</option>
                        </select>
                    </td>
                    <td data-label="Năm sinh">
                        <input type="date" class="edit-input" value="${member.dob}" name="editDOB" required/>
                    </td>
                    <td data-label="Hành động">
                        <button class="action-btn save-member-btn" title="Lưu">
                             <ion-icon name="save-outline"></ion-icon>
                        </button>
                        <button class="action-btn cancel-edit-btn" title="Huỷ">
                             <ion-icon name="close-circle-outline"></ion-icon>
                        </button>
                    </td>
                `;
            } else {
                // --- Render row in display mode ---
                tr.innerHTML = `
                    <td data-label="Tên thành viên">${member.name}</td>
                    <td data-label="Mối quan hệ">${member.relation}</td>
                    <td data-label="Năm sinh">${member.dob || "N/A"}</td>
                    <td data-label="Hành động">
                        <button class="action-btn edit-member-btn" title="Chỉnh sửa">
                             <ion-icon name="create-outline"></ion-icon>
                        </button>
                        <button class="action-btn delete-member-btn" title="Xoá">
                             <ion-icon name="trash-outline"></ion-icon>
                        </button>
                    </td>
                `;
            }
            familyTableBody.appendChild(tr);
        });
         toggleFamilyTableActions(!isEditingMode); // Apply disabled state if necessary
    }

    // Thêm thành viên mới
    function addMember() {
        if (isEditingMode) return; // Prevent adding while main form is editing

        const name = memberNameInput.value.trim();
        const relation = memberRelationSelect.value;
        const dob = memberDOBInput.value;

        if (!name || !dob || !relation) {
            alert("Vui lòng nhập đủ Tên, chọn Quan hệ và Năm sinh.");
            return;
        }
        // Basic date validation (optional)
        if (new Date(dob) > new Date()) {
             alert("Ngày sinh không hợp lệ.");
             return;
        }

        familyMembers.push({ name, relation, dob, isEditing: false }); // Add new member

        // Clear input fields
        memberNameInput.value = "";
        memberDOBInput.value = "";
        memberRelationSelect.value = ""; // Reset dropdown
        renderFamilyTable(); // Re-render the table
    }

    // --- Event Delegation for Table Actions ---
    familyTableBody.addEventListener("click", function(event) {
        const targetButton = event.target.closest(".action-btn");
        if (!targetButton) return; // Exit if click wasn't on an action button

        const row = targetButton.closest("tr");
        const index = parseInt(row.getAttribute("data-index"), 10);

        if (targetButton.classList.contains("delete-member-btn")) {
            // --- Delete Member ---
            if (confirm(`Bạn có chắc muốn xoá thành viên "${familyMembers[index].name}"?`)) {
                familyMembers.splice(index, 1);
                renderFamilyTable();
            }
        } else if (targetButton.classList.contains("edit-member-btn")) {
             // --- Switch to Edit Mode for this member ---
            // Ensure only one row is editable at a time (optional)
            familyMembers.forEach(mem => mem.isEditing = false);
            familyMembers[index].isEditing = true;
            renderFamilyTable();
        } else if (targetButton.classList.contains("cancel-edit-btn")) {
            // --- Cancel Edit for this member ---
            familyMembers[index].isEditing = false;
            renderFamilyTable();
        } else if (targetButton.classList.contains("save-member-btn")) {
            // --- Save Edited Member ---
            const editNameInput = row.querySelector("input[name=\"editName\"]");
            const editRelationSelect = row.querySelector("select[name=\"editRelation\"]");
            const editDOBInput = row.querySelector("input[name=\"editDOB\"]");

            const editName = editNameInput.value.trim();
            const editRelation = editRelationSelect.value;
            const editDOB = editDOBInput.value;

            if (!editName || !editDOB || !editRelation) {
                alert("Vui lòng nhập đủ Tên, chọn Quan hệ và Năm sinh.");
                return; // Prevent saving invalid data
            }
             if (new Date(editDOB) > new Date()) {
                alert("Ngày sinh không hợp lệ.");
                return;
            }

            familyMembers[index].name = editName;
            familyMembers[index].relation = editRelation;
            familyMembers[index].dob = editDOB;
            familyMembers[index].isEditing = false; // Exit editing state
            renderFamilyTable();
        }
    });


    // --- Initial Setup ---
    btnAddMember.addEventListener("click", addMember);
    toggleEditMode(false); // Start in non-editing mode
    renderFamilyTable(); // Initial render of the family table

    // No need for global functions (window.editMember = ...) with event delegation
    // REMOVE/COMMENT OUT the old global assignments if they existed

    // REMOVE the incorrect fetch for sidebar - common.js should handle this
    // fetch("Home.html") ... (REMOVE THIS BLOCK)

}); // End DOMContentLoaded