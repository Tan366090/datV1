document.addEventListener("DOMContentLoaded", function () {
    const historyTableBody = document.getElementById("historyTableBody");
    const filterButton = document.querySelector(".btn-filter");
    const reloadButton = document.getElementById("reloadButton");
    const monthSelect = document.getElementById("month-select");
    const yearInput = document.getElementById("year-select");

    // Fetch attendance history data from the server
    async function fetchAttendanceHistory(month = null, year = null) {
        try {
            let url =
                "http://localhost/QLNhanSu_version1/api/attendance_History.php";
            if (month && year) {
                url += `?month=${month}&year=${year}`;
            }

            console.log("Fetching from URL:", url);

            const response = await fetch(url, {
                method: "GET",
                mode: "cors", // Ensure CORS mode
                headers: {
                    "Content-Type": "application/json",
                },
            });

            console.log("Response status:", response.status);
            console.log("Response headers:", response.headers);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log("Response data:", result);

            if (result.success && Array.isArray(result.data)) {
                renderHistoryTable(result.data);
            } else {
                throw new Error(
                    result.error || "Invalid data format received from server"
                );
            }
        } catch (error) {
            console.error("Error fetching attendance history:", error);
            historyTableBody.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; color: red;">
                        Có lỗi xảy ra khi tải dữ liệu: ${error.message}
                    </td>
                </tr>
            `;
        }
    }

    // Render attendance history data into the table
    function renderHistoryTable(data) {
        historyTableBody.innerHTML = ""; // Clear existing rows

        if (data.length === 0) {
            historyTableBody.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center;">
                        Không có dữ liệu để hiển thị
                    </td>
                </tr>
            `;
            return;
        }

        data.forEach((record, index) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${formatDate(record.attendance_date)}</td>
                <td>${formatTime(record.recorded_at)}</td>
                <td><span class="${getStatusClass(
                    record.attendance_symbol
                )}">${getStatusText(record.attendance_symbol)}</span></td>
                <td>${record.notes || ""}</td>
            `;
            historyTableBody.appendChild(row);
        });
    }

    // Helper function to format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString("vi-VN", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
        });
    }

    // Helper function to format time
    function formatTime(timeString) {
        if (!timeString) return "N/A";
        const time = new Date(timeString);
        return time.toLocaleTimeString("vi-VN", {
            hour: "2-digit",
            minute: "2-digit",
        });
    }

    // Helper function to get status text
    function getStatusText(symbol) {
        const statusMap = {
            P: "Đi làm",
            "1/2P": "Nghỉ nửa ngày phép",
            AL: "Nghỉ phép",
            A: "Vắng không phép",
            WFH: "Làm việc tại nhà",
            L: "Đến muộn",
            SL: "Ốm",
            Cô: "Chăm sóc con ốm",
            TS: "Nghỉ thai sản",
            T: "Tai nạn lao động",
            CN: "Chủ nhật",
            NL: "Nghỉ lễ",
            NB: "Nghỉ bù",
            "1/2K": "Nghỉ nửa ngày không lương",
            K: "Nghỉ không lương",
            N: "Ngừng làm việc",
            NN: "Làm nửa ngày có lương",
        };
        return statusMap[symbol] || symbol;
    }

    // Helper function to get status class
    function getStatusClass(symbol) {
        const classMap = {
            P: "status-work",
            "1/2P": "status-half-leave",
            AL: "status-leave",
            A: "status-absent",
            WFH: "status-wfh",
            L: "status-late",
            SL: "status-sick",
            Cô: "status-child-sick",
            TS: "status-maternity",
            T: "status-accident",
            CN: "status-sunday",
            NL: "status-holiday",
            NB: "status-compensatory",
            "1/2K": "status-half-unpaid",
            K: "status-unpaid",
            N: "status-stopped",
            NN: "status-half-paid",
        };
        return classMap[symbol] || "status-unknown";
    }

    // Event listeners
    filterButton.addEventListener("click", function () {
        const month = monthSelect.value;
        const year = yearInput.value;
        fetchAttendanceHistory(month, year);
    });

    reloadButton.addEventListener("click", function () {
        fetchAttendanceHistory();
    });

    // Initial load
    fetchAttendanceHistory();
});
