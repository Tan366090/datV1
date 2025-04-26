class LoadingOverlay {
    constructor() {
        this.overlay = document.querySelector('.loading-overlay');
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Lắng nghe sự kiện bắt đầu tải
        document.addEventListener('loadingStart', () => this.show());

        // Lắng nghe sự kiện kết thúc tải
        document.addEventListener('loadingEnd', () => this.hide());

        // Lắng nghe các sự kiện AJAX
        this.setupAjaxListeners();
    }

    setupAjaxListeners() {
        // Lắng nghe sự kiện trước khi gửi AJAX
        document.addEventListener('ajaxStart', () => this.show());

        // Lắng nghe sự kiện sau khi hoàn thành AJAX
        document.addEventListener('ajaxEnd', () => this.hide());

        // Lắng nghe sự kiện lỗi AJAX
        document.addEventListener('ajaxError', () => this.hide());
    }

    show() {
        if (this.overlay) {
            this.overlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    hide() {
        if (this.overlay) {
            this.overlay.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // Phương thức tiện ích để bọc các promise
    async withLoading(promise) {
        this.show();
        try {
            const result = await promise;
            return result;
        } finally {
            this.hide();
        }
    }
}

// Khởi tạo khi DOM đã load
document.addEventListener('DOMContentLoaded', () => {
    window.loadingOverlay = new LoadingOverlay();
}); 