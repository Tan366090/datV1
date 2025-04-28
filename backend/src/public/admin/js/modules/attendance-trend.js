class AttendanceTrend {
    constructor() {
        this.chart = null;
        this.period = 'week';
        this.initialize();
    }

    initialize() {
        // Setup event listeners
        const periodSelect = document.getElementById('attendancePeriod');
        if (periodSelect) {
            periodSelect.addEventListener('change', (e) => {
                this.period = e.target.value;
                this.loadData();
            });
        }

        // Initial data load
        this.loadData();

        // Auto refresh every 5 minutes
        setInterval(() => {
            this.loadData();
        }, 5 * 60 * 1000);
    }

    // Thêm hàm refreshData để cập nhật dữ liệu ngay lập tức
    refreshData() {
        this.loadData();
    }

    async loadData() {
        try {
            const response = await fetch(`/qlnhansu_V2/backend/src/public/api/dashboard_api.php?endpoint=attendance&period=${this.period}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (!data.success) {
                console.error('Error loading attendance data:', data.error);
                this.showNoData();
                return;
            }

            if (!data.data || data.data.length === 0) {
                this.showNoData();
                return;
            }

            this.updateChart(data.data);
        } catch (error) {
            console.error('Error loading attendance data:', error);
            this.showNoData();
        }
    }

    showNoData() {
        const ctx = document.getElementById('attendanceTrendChart');
        if (!ctx) return;

        if (this.chart) {
            this.chart.destroy();
        }

        this.chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Không có dữ liệu'],
                datasets: [{
                    label: 'Chưa có dữ liệu chấm công',
                    data: [0],
                    backgroundColor: 'rgba(200, 200, 200, 0.5)',
                    borderColor: 'rgb(200, 200, 200)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Ngày'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số nhân viên'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 20
                        }
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            }
        });
    }

    updateChart(data) {
        const ctx = document.getElementById('attendanceTrendChart');
        if (!ctx) return;

        if (this.chart) {
            this.chart.destroy();
        }

        // Group attendance data by type
        const groupedData = {
            working: [], // P, NN
            paidLeave: [], // Ô, Cô, TS, T, P, 1/2P
            unpaidLeave: [], // 1/2K, K
            holiday: [], // CN, NL, NB
            stopped: [] // N
        };

        data.forEach(record => {
            const date = record.date;
            
            // Working
            groupedData.working.push({
                x: date,
                y: record.present + record.half_day_work
            });

            // Paid Leave
            groupedData.paidLeave.push({
                x: date,
                y: record.sick + record.child_care + record.maternity + 
                   record.work_accident + record.half_day_leave
            });

            // Unpaid Leave
            groupedData.unpaidLeave.push({
                x: date,
                y: record.half_day_unpaid + record.unpaid
            });

            // Holiday
            groupedData.holiday.push({
                x: date,
                y: record.sunday + record.holiday + record.compensatory
            });

            // Stopped
            groupedData.stopped.push({
                x: date,
                y: record.stopped
            });
        });

        this.chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(record => record.date),
                datasets: [
                    {
                        label: 'Làm việc (P, NN)',
                        data: groupedData.working,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgb(75, 192, 192)',
                        borderWidth: 1
                    },
                    {
                        label: 'Nghỉ có lương (Ô, Cô, TS, T, P, 1/2P)',
                        data: groupedData.paidLeave,
                        backgroundColor: 'rgba(255, 159, 64, 0.5)',
                        borderColor: 'rgb(255, 159, 64)',
                        borderWidth: 1
                    },
                    {
                        label: 'Nghỉ không lương (1/2K, K)',
                        data: groupedData.unpaidLeave,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgb(255, 99, 132)',
                        borderWidth: 1
                    },
                    {
                        label: 'Nghỉ lễ (CN, NL, NB)',
                        data: groupedData.holiday,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgb(54, 162, 235)',
                        borderWidth: 1
                    },
                    {
                        label: 'Ngừng làm việc (N)',
                        data: groupedData.stopped,
                        backgroundColor: 'rgba(153, 102, 255, 0.5)',
                        borderColor: 'rgb(153, 102, 255)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true,
                        title: {
                            display: true,
                            text: 'Ngày'
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số nhân viên'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.parsed.y} nhân viên`;
                            }
                        }
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 20
                        }
                    }
                }
            }
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.attendanceTrend = new AttendanceTrend();
}); 