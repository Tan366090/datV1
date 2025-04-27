class UserProfile {
    constructor() {
        this.profileButton = document.querySelector('.dropdown-toggle');
        this.profileMenu = document.querySelector('.dropdown-menu');
        this.setupEventListeners();
        this.loadUserProfile();
    }

    setupEventListeners() {
        this.profileButton.addEventListener('click', () => this.toggleProfileMenu());
        
        // Lắng nghe sự kiện click trên các menu item
        this.profileMenu.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', (e) => this.handleMenuItemClick(e));
        });
    }

    toggleProfileMenu() {
        this.profileMenu.classList.toggle('show');
    }

    async handleMenuItemClick(e) {
        const action = e.target.getAttribute('data-action');
        
        switch(action) {
            case 'profile':
                window.location.href = '/admin/profile.html';
                break;
            case 'settings':
                window.location.href = '/admin/settings.html';
                break;
            case 'logout':
                await this.handleLogout();
                break;
        }
    }

    async loadUserProfile() {
        try {
            const response = await fetch('/api/user/profile');
            if (!response.ok) throw new Error('Failed to load profile');
            
            const profile = await response.json();
            this.updateProfileUI(profile);
        } catch (error) {
            console.error('Profile load error:', error);
        }
    }

    updateProfileUI(profile) {
        // Cập nhật avatar
        const avatar = document.querySelector('.user-avatar');
        if (avatar && profile.avatar) {
            avatar.src = profile.avatar;
        }

        // Cập nhật tên người dùng
        const userName = document.querySelector('.user-name');
        if (userName) {
            userName.textContent = profile.name;
        }

        // Cập nhật role
        const userRole = document.querySelector('.user-role');
        if (userRole) {
            userRole.textContent = profile.role;
        }
    }

    async handleLogout() {
        try {
            const response = await fetch('/api/auth/logout', {
                method: 'POST',
                credentials: 'include'
            });

            if (response.ok) {
                window.location.href = '/login.html';
            } else {
                throw new Error('Logout failed');
            }
        } catch (error) {
            console.error('Logout error:', error);
            alert('Đăng xuất thất bại. Vui lòng thử lại.');
        }
    }
}

// Khởi tạo khi DOM đã load
document.addEventListener('DOMContentLoaded', () => {
    new UserProfile();
}); 