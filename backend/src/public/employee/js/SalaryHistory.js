document.addEventListener("DOMContentLoaded", function () {
    const salaryHistoryTableBody = document.getElementById(
        "salaryHistoryTableBody"
    );
    const filterButton = document.querySelector(".btn-filter");
    const reloadButton = document.getElementById("reloadButton");
    const monthSelect = document.getElementById("month-select");
    const yearInput = document.getElementById("year-select");

    // Ensure the elements exist before adding event listeners
    if (!filterButton) {
        console.error("Filter button not found in the DOM.");
        return;
    }
    if (!reloadButton) {
        console.error("Reload button not found in the DOM.");
        return;
    }

    // Fetch salary history data from the server
    async function fetchSalaryHistory(month = null, year = null) {
        try {
            let url = "http://localhost/qlnhansu/api/getSalaryHistory.php";
            if (month && year) {
                url += `?month=${month}&year=${year}`;
            }

            console.log("Fetching salary history from URL:", url); // Debugging log

            const response = await fetch(url, {
                method: "GET",
                mode: "cors",
            });
            if (!response.ok) {
                const errorText = await response.text(); // Capture server error message
                throw new Error(
                    `Failed to fetch salary history: ${response.status} ${response.statusText}. Server response: ${errorText}`
                );
            }
            const data = await response.json();
            renderSalaryHistoryTable(data);
        } catch (error) {
            console.error("Error fetching salary history:", error);
            alert(
                "An error occurred while fetching salary history. Please check the console for more details."
            );
            salaryHistoryTableBody.innerHTML = `
                <tr>
                    <td colspan="9" style="text-align: center; color: red;">
                        Không thể tải dữ liệu lịch sử lương.
                    </td>
                </tr>
            `;
        }
    }

    // Render salary history data into the table
    function renderSalaryHistoryTable(data) {
        salaryHistoryTableBody.innerHTML = ""; // Clear existing rows

        if (data.length === 0) {
            // Display a message when no data is available
            const emptyRow = document.createElement("tr");
            emptyRow.innerHTML = `
                <td colspan="9" style="text-align: center; color: #888;">
                    Không có dữ liệu để hiển thị
                </td>
            `;
            salaryHistoryTableBody.appendChild(emptyRow);
            return;
        }

        data.forEach((record, index) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${record.history_id}</td>
                <td>${record.employee_id}</td>
                <td>${record.employee_name || "N/A"}</td>
                <td>${record.salary_coefficient || "N/A"}</td>
                <td>${record.salary_level || "N/A"}</td>
                <td>${record.job_position || "N/A"}</td>
                <td>${record.department || "N/A"}</td>
                <td>${record.effective_date || "N/A"}</td>
                <td>
                    <button class="btn btn-danger delete-button" data-id="${record.history_id}">Xóa</button>
                    <button class="btn btn-primary edit-button" data-id="${record.history_id}">Sửa</button>
                </td>
            `;
            salaryHistoryTableBody.appendChild(row);
        });

        // Add event listeners for delete buttons
        document.querySelectorAll(".delete-button").forEach((button) => {
            button.addEventListener("click", function () {
                const historyId = this.dataset.id;
                if (confirm("Bạn có chắc chắn muốn xóa lịch sử này?")) {
                    deleteSalaryHistory(historyId);
                }
            });
        });

        // Add event listeners for edit buttons
        document.querySelectorAll(".edit-button").forEach((button) => {
            button.addEventListener("click", function () {
                const historyId = this.dataset.id;
                alert(`Chức năng sửa chưa được triển khai. ID: ${historyId}`);
            });
        });
    }

    // Delete a salary history record
    async function deleteSalaryHistory(historyId) {
        try {
            const response = await fetch(
                `http://localhost/qlnhansu/api/deleteSalaryHistory.php?id=${historyId}`,
                { method: "DELETE" }
            );
            if (!response.ok) {
                throw new Error("Failed to delete salary history");
            }
            alert("Xóa lịch sử lương thành công!");
            fetchSalaryHistory(); // Reload data
        } catch (error) {
            console.error("Error deleting salary history:", error.message);
            alert("Có lỗi xảy ra khi xóa lịch sử lương.");
        }
    }

    // Event listener for "Lọc" button
    filterButton.addEventListener("click", function () {
        const selectedMonth = parseInt(monthSelect.value, 10);
        const selectedYear = parseInt(yearInput.value, 10);

        // Validate month and year
        if (isNaN(selectedMonth) || selectedMonth < 1 || selectedMonth > 12) {
            alert("Vui lòng chọn tháng hợp lệ (1-12)!");
            return;
        }
        if (isNaN(selectedYear) || selectedYear < 2020 || selectedYear > 2030) {
            alert("Vui lòng chọn năm hợp lệ (2020-2030)!");
            return;
        }

        fetchSalaryHistory(selectedMonth, selectedYear);
    });

    // Event listener for "Load lại dữ liệu" button
    reloadButton.addEventListener("click", function () {
        fetchSalaryHistory(); // Reload all data
    });

    // Initial load
    fetchSalaryHistory();
});
