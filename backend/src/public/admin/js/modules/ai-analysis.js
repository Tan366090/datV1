class AIAnalysis {
    constructor() {
        this.hrTrendsChart = null;
        this.sentimentChart = null;
        this.predictionsContainer = document.querySelector('.ai-predictions');
        this.setupEventListeners();
        this.setupCharts();
        this.loadData();
    }

    setupEventListeners() {
        // Lắng nghe sự kiện khi người dùng thay đổi khoảng thời gian phân tích
        document.querySelectorAll('[data-time-range]').forEach(button => {
            button.addEventListener('click', (e) => this.handleTimeRangeChange(e));
        });
    }

    setupCharts() {
        // Thiết lập biểu đồ xu hướng nhân sự
        const hrTrendsCtx = document.getElementById('hrTrendsChart');
        if (hrTrendsCtx) {
            this.hrTrendsChart = new Chart(hrTrendsCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Tổng số nhân viên',
                        data: [],
                        borderColor: '#4CAF50',
                        tension: 0.1
                    }, {
                        label: 'Nhân viên mới',
                        data: [],
                        borderColor: '#2196F3',
                        tension: 0.1
                    }, {
                        label: 'Nhân viên nghỉ việc',
                        data: [],
                        borderColor: '#F44336',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Xu hướng nhân sự'
                        }
                    }
                }
            });
        }

        // Thiết lập biểu đồ phân tích tâm lý
        const sentimentCtx = document.getElementById('sentimentChart');
        if (sentimentCtx) {
            this.sentimentChart = new Chart(sentimentCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Tích cực', 'Trung tính', 'Tiêu cực'],
                    datasets: [{
                        data: [0, 0, 0],
                        backgroundColor: [
                            '#4CAF50',
                            '#FFC107',
                            '#F44336'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Phân tích tâm lý nhân viên'
                        }
                    }
                }
            });
        }
    }

    async loadData() {
        try {
            // Load dữ liệu xu hướng nhân sự
            const hrTrendsResponse = await fetch('/api/ai/hr-trends');
            if (hrTrendsResponse.ok) {
                const hrTrendsData = await hrTrendsResponse.json();
                this.updateHrTrendsChart(hrTrendsData);
                this.updateHrTrendsInsights(hrTrendsData.insights);
            }

            // Load dữ liệu phân tích tâm lý
            const sentimentResponse = await fetch('/api/ai/sentiment');
            if (sentimentResponse.ok) {
                const sentimentData = await sentimentResponse.json();
                this.updateSentimentChart(sentimentData);
                this.updateSentimentInsights(sentimentData.insights);
            }
        } catch (error) {
            console.error('AI Analysis error:', error);
        }
    }

    updateHrTrendsChart(data) {
        if (this.hrTrendsChart) {
            this.hrTrendsChart.data.labels = data.labels;
            this.hrTrendsChart.data.datasets[0].data = data.totalEmployees;
            this.hrTrendsChart.data.datasets[1].data = data.newEmployees;
            this.hrTrendsChart.data.datasets[2].data = data.leftEmployees;
            this.hrTrendsChart.update();
        }
    }

    updateHrTrendsInsights(insights) {
        const insightsContainer = document.getElementById('hrTrendsInsights');
        if (insightsContainer) {
            insightsContainer.innerHTML = insights.map(insight => `
                <li>${insight}</li>
            `).join('');
        }
    }

    updateSentimentChart(data) {
        if (this.sentimentChart) {
            this.sentimentChart.data.datasets[0].data = [
                data.positive,
                data.neutral,
                data.negative
            ];
            this.sentimentChart.update();
        }
    }

    updateSentimentInsights(insights) {
        const insightsContainer = document.getElementById('sentimentInsights');
        if (insightsContainer) {
            insightsContainer.innerHTML = insights.map(insight => `
                <li>${insight}</li>
            `).join('');
        }
    }

    displayPredictions(predictions) {
        if (!this.predictionsContainer) return;

        const predictionsHtml = predictions.map(prediction => `
            <div class="prediction-card ${prediction.type}">
                <div class="prediction-icon">
                    <i class="fas ${this.getPredictionIcon(prediction.type)}"></i>
                </div>
                <div class="prediction-content">
                    <h4>${prediction.title}</h4>
                    <p>${prediction.description}</p>
                    <div class="prediction-meta">
                        <span class="confidence">Độ tin cậy: ${prediction.confidence}%</span>
                        <span class="time">${this.formatTime(prediction.timestamp)}</span>
                    </div>
                </div>
            </div>
        `).join('');

        this.predictionsContainer.innerHTML = predictionsHtml;
    }

    getPredictionIcon(type) {
        const icons = {
            'warning': 'fa-exclamation-triangle',
            'success': 'fa-check-circle',
            'info': 'fa-info-circle',
            'danger': 'fa-times-circle'
        };
        return icons[type] || 'fa-info-circle';
    }

    formatTime(timestamp) {
        return new Date(timestamp).toLocaleString('vi-VN', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    async handleTimeRangeChange(e) {
        const button = e.currentTarget;
        const timeRange = button.dataset.timeRange;
        
        try {
            // Cập nhật trạng thái nút
            document.querySelectorAll('[data-time-range]').forEach(btn => {
                btn.classList.remove('active');
            });
            button.classList.add('active');

            // Lấy dữ liệu mới
            const response = await fetch(`/api/ai/analysis?timeRange=${timeRange}`);
            const data = await response.json();

            // Cập nhật biểu đồ
            this.updateCharts(data);
        } catch (error) {
            console.error('Time range change error:', error);
            this.showError('Có lỗi xảy ra khi cập nhật dữ liệu');
        }
    }

    updateCharts(data) {
        // Cập nhật biểu đồ xu hướng HR
        this.hrTrendsChart.data.labels = data.hrTrends.labels;
        this.hrTrendsChart.data.datasets[0].data = data.hrTrends.totalEmployees;
        this.hrTrendsChart.data.datasets[1].data = data.hrTrends.newEmployees;
        this.hrTrendsChart.data.datasets[2].data = data.hrTrends.leftEmployees;
        this.hrTrendsChart.update();

        // Cập nhật biểu đồ phân tích tâm lý
        this.sentimentChart.data.datasets[0].data = [
            data.sentiment.positive,
            data.sentiment.neutral,
            data.sentiment.negative
        ];
        this.sentimentChart.update();

        // Cập nhật dự đoán
        this.displayPredictions(data.predictions);
    }

    showError(message) {
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-danger border-0';
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
}

// Khởi tạo khi DOM đã load
document.addEventListener('DOMContentLoaded', () => {
    new AIAnalysis();
}); 