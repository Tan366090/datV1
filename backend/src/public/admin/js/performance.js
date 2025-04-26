$(document).ready(function() {
    // Initialize DataTables
    const performanceTable = $('#performanceTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/vi.json'
        },
        order: [[2, 'desc'], [0, 'asc']],
        pageLength: 10
    });

    // Initialize Chart
    let performanceChart = null;

    // Load employees for dropdowns
    function loadEmployees() {
        $.ajax({
            url: '/api/employees',
            method: 'GET',
            success: function(response) {
                const employeeSelects = $('#employee, #filterEmployee');
                employeeSelects.empty().append('<option value="">Chọn nhân viên</option>');
                response.forEach(employee => {
                    employeeSelects.append(`<option value="${employee.id}">${employee.code} - ${employee.name}</option>`);
                });
            },
            error: function(xhr) {
                showError('Không thể tải danh sách nhân viên');
            }
        });
    }

    // Load performance reviews
    function loadPerformanceReviews() {
        showLoading();
        $.ajax({
            url: '/api/performance/reviews',
            method: 'GET',
            success: function(response) {
                performanceTable.clear();
                response.forEach(review => {
                    performanceTable.row.add([
                        review.employee.code,
                        review.employee.name,
                        `${review.period} ${review.year}`,
                        getScoreBadge(review.score),
                        review.comments,
                        getActionButtons(review)
                    ]);
                });
                performanceTable.draw();
                updatePerformanceChart(response);
                hideLoading();
            },
            error: function(xhr) {
                hideLoading();
                showError('Không thể tải danh sách đánh giá');
            }
        });
    }

    // Update performance chart
    function updatePerformanceChart(reviews) {
        const ctx = document.getElementById('performanceChart').getContext('2d');
        
        // Group reviews by period and calculate average scores
        const periodData = {};
        reviews.forEach(review => {
            const key = `${review.period} ${review.year}`;
            if (!periodData[key]) {
                periodData[key] = {
                    total: 0,
                    count: 0
                };
            }
            periodData[key].total += review.score;
            periodData[key].count++;
        });

        const labels = Object.keys(periodData);
        const data = labels.map(label => (periodData[label].total / periodData[label].count).toFixed(1));

        if (performanceChart) {
            performanceChart.destroy();
        }

        performanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Điểm trung bình',
                    data: data,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Handle form submission
    $('#addReviewForm').on('submit', function(e) {
        e.preventDefault();
        showLoading();
        
        const formData = {
            employeeId: $('#employee').val(),
            period: $('#period').val(),
            year: $('#year').val(),
            score: $('#score').val(),
            comments: $('#comments').val(),
            strengths: $('#strengths').val(),
            improvements: $('#improvements').val()
        };

        $.ajax({
            url: '/api/performance/reviews',
            method: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function(response) {
                hideLoading();
                showSuccess('Thêm đánh giá thành công');
                $('#addReviewModal').modal('hide');
                $('#addReviewForm')[0].reset();
                loadPerformanceReviews();
            },
            error: function(xhr) {
                hideLoading();
                showError(xhr.responseJSON?.message || 'Không thể thêm đánh giá');
            }
        });
    });

    // Handle filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        const filters = {
            employeeId: $('#filterEmployee').val(),
            period: $('#filterPeriod').val(),
            year: $('#filterYear').val(),
            score: $('#filterScore').val()
        };
        loadPerformanceReviews(filters);
    });

    // Handle reset filters
    $('#resetFilters').on('click', function() {
        $('#filterForm')[0].reset();
        loadPerformanceReviews();
    });

    // Helper functions
    function getScoreBadge(score) {
        const badges = {
            1: '<span class="badge bg-danger">1 - Không đạt</span>',
            2: '<span class="badge bg-warning">2 - Cần cải thiện</span>',
            3: '<span class="badge bg-info">3 - Đạt yêu cầu</span>',
            4: '<span class="badge bg-primary">4 - Tốt</span>',
            5: '<span class="badge bg-success">5 - Xuất sắc</span>'
        };
        return badges[score] || score;
    }

    function getActionButtons(review) {
        return `
            <button class="btn btn-sm btn-info view-btn" data-id="${review.id}">
                <i class="fas fa-eye"></i>
            </button>
            <button class="btn btn-sm btn-primary edit-btn" data-id="${review.id}">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-danger delete-btn" data-id="${review.id}">
                <i class="fas fa-trash"></i>
            </button>
        `;
    }

    // Event handlers for action buttons
    $(document).on('click', '.view-btn', function() {
        const reviewId = $(this).data('id');
        // TODO: Implement view review details
    });

    $(document).on('click', '.edit-btn', function() {
        const reviewId = $(this).data('id');
        // TODO: Implement edit review
    });

    $(document).on('click', '.delete-btn', function() {
        const reviewId = $(this).data('id');
        if (confirm('Bạn có chắc chắn muốn xóa đánh giá này?')) {
            $.ajax({
                url: `/api/performance/reviews/${reviewId}`,
                method: 'DELETE',
                success: function(response) {
                    showSuccess('Xóa đánh giá thành công');
                    loadPerformanceReviews();
                },
                error: function(xhr) {
                    showError(xhr.responseJSON?.message || 'Không thể xóa đánh giá');
                }
            });
        }
    });

    // UI helper functions
    function showLoading() {
        $('.loading-spinner').show();
    }

    function hideLoading() {
        $('.loading-spinner').hide();
    }

    function showError(message) {
        $('.error-message').text(message).show();
        setTimeout(() => $('.error-message').hide(), 5000);
    }

    function showSuccess(message) {
        $('.success-message').text(message).show();
        setTimeout(() => $('.success-message').hide(), 5000);
    }

    // Initial load
    loadEmployees();
    loadPerformanceReviews();
});

class PerformanceOptimizer {
    constructor() {
        this.cache = new Map();
        this.debounceTimers = new Map();
        this.throttleTimers = new Map();
        this.intersectionObservers = new Map();
    }

    // Cache management
    setCache(key, value, ttl = 300000) { // 5 minutes default
        this.cache.set(key, {
            value,
            expiry: Date.now() + ttl
        });
    }

    getCache(key) {
        const item = this.cache.get(key);
        if (!item) return null;
        
        if (Date.now() > item.expiry) {
            this.cache.delete(key);
            return null;
        }
        
        return item.value;
    }

    clearCache() {
        this.cache.clear();
    }

    // Debounce function calls
    debounce(func, wait = 300) {
        return (...args) => {
            const key = func.toString();
            clearTimeout(this.debounceTimers.get(key));
            this.debounceTimers.set(key, setTimeout(() => {
                func(...args);
                this.debounceTimers.delete(key);
            }, wait));
        };
    }

    // Throttle function calls
    throttle(func, limit = 300) {
        return (...args) => {
            const key = func.toString();
            if (!this.throttleTimers.has(key)) {
                func(...args);
                this.throttleTimers.set(key, setTimeout(() => {
                    this.throttleTimers.delete(key);
                }, limit));
            }
        };
    }

    // Lazy loading with Intersection Observer
    setupLazyLoading(selector, callback) {
        const elements = document.querySelectorAll(selector);
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    callback(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.1
        });

        elements.forEach(element => observer.observe(element));
        this.intersectionObservers.set(selector, observer);
    }

    // Image optimization
    optimizeImages() {
        const images = document.querySelectorAll('img[data-src]');
        images.forEach(img => {
            if (img.complete) {
                this.loadImage(img);
            } else {
                img.addEventListener('load', () => this.loadImage(img));
            }
        });
    }

    loadImage(img) {
        const src = img.getAttribute('data-src');
        if (src) {
            img.src = src;
            img.removeAttribute('data-src');
        }
    }

    // Resource preloading
    preloadResources() {
        const resources = [
            '/admin/css/dashboard.css',
            '/admin/js/chart.js',
            '/admin/js/table.js'
        ];

        resources.forEach(resource => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = resource.endsWith('.css') ? 'style' : 'script';
            link.href = resource;
            document.head.appendChild(link);
        });
    }

    // Memory management
    cleanup() {
        // Clear all timers
        this.debounceTimers.forEach(timer => clearTimeout(timer));
        this.throttleTimers.forEach(timer => clearTimeout(timer));
        this.debounceTimers.clear();
        this.throttleTimers.clear();

        // Disconnect all observers
        this.intersectionObservers.forEach(observer => observer.disconnect());
        this.intersectionObservers.clear();

        // Clear cache
        this.clearCache();
    }
}

// Initialize performance optimizer
const optimizer = new PerformanceOptimizer();

// Export optimized functions
export const debounce = optimizer.debounce.bind(optimizer);
export const throttle = optimizer.throttle.bind(optimizer);
export const setCache = optimizer.setCache.bind(optimizer);
export const getCache = optimizer.getCache.bind(optimizer);

// Initialize optimizations
document.addEventListener('DOMContentLoaded', () => {
    // Setup lazy loading for images
    optimizer.setupLazyLoading('img[data-src]', (img) => {
        optimizer.loadImage(img);
    });

    // Optimize existing images
    optimizer.optimizeImages();

    // Preload critical resources
    optimizer.preloadResources();
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    optimizer.cleanup();
}); 