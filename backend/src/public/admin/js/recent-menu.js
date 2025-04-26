class RecentMenu {
    constructor() {
        this.recentMenuContainer = document.querySelector('.recent-menu');
        this.maxItems = 5;
        this.storageKey = 'recentMenuItems';
        this.setupEventListeners();
        this.loadRecentItems();
    }

    setupEventListeners() {
        // Lắng nghe sự kiện click trên menu items
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', (e) => {
                if (!e.target.closest('.recent-menu')) {
                    this.addRecentItem(link);
                }
            });
        });
    }

    addRecentItem(link) {
        const item = {
            text: link.textContent.trim(),
            href: link.getAttribute('href'),
            icon: link.querySelector('i')?.className || 'fas fa-link',
            timestamp: new Date().getTime()
        };

        let recentItems = this.getRecentItems();
        
        // Loại bỏ item cũ nếu đã tồn tại
        recentItems = recentItems.filter(i => i.href !== item.href);
        
        // Thêm item mới vào đầu
        recentItems.unshift(item);
        
        // Giới hạn số lượng items
        if (recentItems.length > this.maxItems) {
            recentItems = recentItems.slice(0, this.maxItems);
        }

        // Lưu vào localStorage
        localStorage.setItem(this.storageKey, JSON.stringify(recentItems));
        
        // Cập nhật UI
        this.renderRecentItems(recentItems);
    }

    getRecentItems() {
        const items = localStorage.getItem(this.storageKey);
        return items ? JSON.parse(items) : [];
    }

    renderRecentItems(items) {
        if (!this.recentMenuContainer) return;

        const html = items.map(item => `
            <a href="${item.href}" class="nav-link">
                <i class="${item.icon}"></i>
                <span>${item.text}</span>
            </a>
        `).join('');

        this.recentMenuContainer.innerHTML = `
            <h3>Menu gần đây</h3>
            ${html}
        `;
    }

    loadRecentItems() {
        const items = this.getRecentItems();
        this.renderRecentItems(items);
    }
}

// Khởi tạo khi DOM đã load
document.addEventListener('DOMContentLoaded', () => {
    new RecentMenu();
}); 