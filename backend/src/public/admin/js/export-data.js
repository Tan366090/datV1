class ExportData {
    constructor() {
        this.exportButtons = document.querySelectorAll('.export-btn');
        this.setupEventListeners();
    }

    setupEventListeners() {
        this.exportButtons.forEach(button => {
            button.addEventListener('click', () => this.handleExport(button));
        });
    }

    async handleExport(button) {
        try {
            const dataType = button.dataset.type;
            button.disabled = true;
            button.textContent = 'Exporting...';

            const response = await fetch(`/api/export/${dataType}`);
            
            if (!response.ok) {
                throw new Error('Export failed');
            }

            // Create a blob from the response
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            
            // Create a temporary link and click it to download
            const a = document.createElement('a');
            a.href = url;
            a.download = `${dataType}_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            this.showSuccess('Export completed successfully');
        } catch (error) {
            this.showError('Export failed. Please try again.');
            console.error('Export error:', error);
        } finally {
            button.disabled = false;
            button.textContent = 'Export CSV';
        }
    }

    showSuccess(message) {
        const toast = document.createElement('div');
        toast.className = 'toast toast-success';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    showError(message) {
        const toast = document.createElement('div');
        toast.className = 'toast toast-error';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
}

class ExportUtils {
    static async exportChart(chartId) {
        const chart = document.getElementById(chartId);
        if (!chart) return;

        try {
            const canvas = chart.querySelector('canvas');
            if (!canvas) return;

            // Tạo link tải xuống
            const link = document.createElement('a');
            link.download = `${chartId}_${new Date().toISOString().split('T')[0]}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        } catch (error) {
            console.error('Export chart error:', error);
            alert('Xuất biểu đồ thất bại');
        }
    }

    static async exportTable(tableId, format = 'excel') {
        const table = document.getElementById(tableId);
        if (!table) return;

        try {
            let data = [];
            const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent);
            
            // Lấy dữ liệu từ bảng
            table.querySelectorAll('tbody tr').forEach(row => {
                const rowData = {};
                Array.from(row.cells).forEach((cell, index) => {
                    rowData[headers[index]] = cell.textContent;
                });
                data.push(rowData);
            });

            // Tạo file tùy theo định dạng
            let blob;
            let filename;

            if (format === 'excel') {
                const worksheet = XLSX.utils.json_to_sheet(data);
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet1');
                const excelBuffer = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
                blob = new Blob([excelBuffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                filename = `${tableId}_${new Date().toISOString().split('T')[0]}.xlsx`;
            } else if (format === 'csv') {
                const csv = Papa.unparse(data);
                blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                filename = `${tableId}_${new Date().toISOString().split('T')[0]}.csv`;
            }

            // Tạo link tải xuống
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            link.click();
            URL.revokeObjectURL(link.href);
        } catch (error) {
            console.error('Export table error:', error);
            alert('Xuất dữ liệu thất bại');
        }
    }

    static async exportData(endpoint, format = 'excel') {
        try {
            const response = await fetch(endpoint);
            if (!response.ok) throw new Error('Failed to fetch data');
            
            const data = await response.json();
            
            let blob;
            let filename;

            if (format === 'excel') {
                const worksheet = XLSX.utils.json_to_sheet(data);
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet1');
                const excelBuffer = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
                blob = new Blob([excelBuffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                filename = `${endpoint.split('/').pop()}_${new Date().toISOString().split('T')[0]}.xlsx`;
            } else if (format === 'csv') {
                const csv = Papa.unparse(data);
                blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                filename = `${endpoint.split('/').pop()}_${new Date().toISOString().split('T')[0]}.csv`;
            }

            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            link.click();
            URL.revokeObjectURL(link.href);
        } catch (error) {
            console.error('Export data error:', error);
            alert('Xuất dữ liệu thất bại');
        }
    }
}

// Khởi tạo khi DOM đã load
document.addEventListener('DOMContentLoaded', () => {
    new ExportData();

    // Thêm sự kiện cho các nút export
    document.querySelectorAll('[data-export]').forEach(button => {
        button.addEventListener('click', (e) => {
            const target = e.target.closest('[data-export]');
            const type = target.getAttribute('data-export-type');
            const id = target.getAttribute('data-export-id');
            
            if (type === 'chart') {
                ExportUtils.exportChart(id);
            } else if (type === 'table') {
                ExportUtils.exportTable(id);
            } else if (type === 'data') {
                ExportUtils.exportData(id);
            }
        });
    });
}); 