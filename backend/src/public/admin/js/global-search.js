class GlobalSearch {
    constructor() {
        this.searchInput = document.querySelector('.header-controls .form-control');
        this.searchResults = document.createElement('div');
        this.searchResults.className = 'search-results';
        this.searchInput.parentNode.appendChild(this.searchResults);
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
            const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
            const results = await response.json();
            this.displayResults(results);
        } catch (error) {
            console.error('Search error:', error);
            this.displayError();
        }
    }

    displayResults(results) {
        this.searchResults.innerHTML = '';
        
        if (results.length === 0) {
            this.searchResults.innerHTML = '<div class="search-result-item">Không tìm thấy kết quả</div>';
            return;
        }

        results.forEach(result => {
            const item = document.createElement('div');
            item.className = 'search-result-item';
        
            // Nếu có ảnh đại diện, ví dụ: result.avatar
            let avatarHtml = '';
            if (result.avatar) {
                avatarHtml = `<img class="avatar" src="${result.avatar}" alt="avatar" />`;
            } else {
                avatarHtml = `<div class="avatar"></div>`; // fallback
            }
        
            item.innerHTML = `
                ${avatarHtml}
                <div style="flex:1; min-width:0">
                    <div class="type">${result.type}</div>
                    <div class="title">${result.title}</div>
                    <div class="description">${result.description}</div>
                </div>
            `;
            item.addEventListener('click', () => this.handleResultClick(result));
            this.searchResults.appendChild(item);
        });

        this.showResults();
    }

    displayError() {
        this.searchResults.innerHTML = '<div class="search-result-item">Có lỗi xảy ra khi tìm kiếm</div>';
        this.showResults();
    }

    showResults() {
        this.searchResults.classList.add('active');
    }

    hideResults() {
        this.searchResults.classList.remove('active');
    }

    handleResultClick(result) {
        // Handle navigation based on result type
        switch(result.type) {
            case 'employee':
                window.location.href = `/admin/employees/${result.id}`;
                break;
            case 'department':
                window.location.href = `/admin/departments/${result.id}`;
                break;
            case 'project':
                window.location.href = `/admin/projects/${result.id}`;
                break;
            case 'document':
                window.location.href = `/admin/documents/${result.id}`;
                break;
        }
    }
}

// Initialize global search when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new GlobalSearch();
}); 