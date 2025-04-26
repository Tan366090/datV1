class GlobalSearch {
    constructor() {
        this.searchInput = document.querySelector('.header-controls .form-control');
        this.searchResults = document.createElement('div');
        this.searchResults.className = 'search-results';
        this.setupEventListeners();
    }

    setupEventListeners() {
        this.searchInput.addEventListener('input', (e) => this.handleSearch(e));
        this.searchInput.addEventListener('focus', () => this.showResults());
        this.searchInput.addEventListener('blur', () => setTimeout(() => this.hideResults(), 200));
    }

    async handleSearch(e) {
        const query = e.target.value.trim();
        if (query.length < 2) {
            this.hideResults();
            return;
        }

        try {
            const results = await this.searchData(query);
            this.displayResults(results);
        } catch (error) {
            console.error('Search error:', error);
            this.displayError();
        }
    }

    async searchData(query) {
        // Gọi API tìm kiếm
        const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
        if (!response.ok) throw new Error('Search failed');
        return await response.json();
    }

    displayResults(results) {
        if (!results || results.length === 0) {
            this.searchResults.innerHTML = '<div class="no-results">Không tìm thấy kết quả</div>';
            return;
        }

        const html = results.map(result => `
            <div class="search-result-item">
                <a href="${result.url}">
                    <i class="${result.icon}"></i>
                    <span>${result.title}</span>
                    <small>${result.description}</small>
                </a>
            </div>
        `).join('');

        this.searchResults.innerHTML = html;
        this.showResults();
    }

    displayError() {
        this.searchResults.innerHTML = '<div class="search-error">Lỗi tìm kiếm</div>';
        this.showResults();
    }

    showResults() {
        if (!this.searchResults.parentNode) {
            this.searchInput.parentNode.appendChild(this.searchResults);
        }
        this.searchResults.style.display = 'block';
    }

    hideResults() {
        if (this.searchResults.parentNode) {
            this.searchResults.style.display = 'none';
        }
    }
}

// Khởi tạo khi DOM đã load
document.addEventListener('DOMContentLoaded', () => {
    new GlobalSearch();
}); 