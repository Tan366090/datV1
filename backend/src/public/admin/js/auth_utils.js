// Authentication utilities
const auth = {
    isAuthenticated: function() {
        const token = localStorage.getItem("token");
        return !!token;
    },

    checkAuth: function() {
        if (!this.isAuthenticated()) {
            // Store the current URL before redirecting
            localStorage.setItem("redirectUrl", window.location.href);
            window.location.href = "/QLNhanSu_version1/public/login_new.html";
            return false;
        }
        return true;
    },

    logout: function() {
        localStorage.removeItem("token");
        localStorage.removeItem("user");
        window.location.href = "/QLNhanSu_version1/public/login_new.html";
    },

    getCurrentUser: function() {
        const user = localStorage.getItem("user");
        return user ? JSON.parse(user) : null;
    },

    updateUserInfo: function() {
        const user = this.getCurrentUser();
        const userNameElement = document.getElementById("userName");
        if (user && userNameElement) {
            userNameElement.textContent = user.name;
        }
    },

    handleNavigation: function(event) {
        // Kiểm tra nếu click vào link
        if (event.target.tagName === "A" || event.target.closest("a")) {
            // Nếu chưa đăng nhập thì lưu URL và chuyển đến trang đăng nhập
            if (!this.isAuthenticated()) {
                event.preventDefault();
                localStorage.setItem("redirectUrl", event.target.href || event.target.closest("a").href);
                window.location.href = "/QLNhanSu_version1/public/login_new.html";
            }
        }
    }
};

// Add event listeners when DOM is loaded
document.addEventListener("DOMContentLoaded", function() {
    // Logout button
    const logoutBtn = document.getElementById("logoutBtn");
    if (logoutBtn) {
        logoutBtn.addEventListener("click", auth.logout);
    }
    
    // Check authentication on protected pages
    const isProtectedPage = !window.location.pathname.includes("login_new.html");
    if (isProtectedPage) {
        auth.checkAuth();
    }
    
    // Update user info if authenticated
    if (auth.isAuthenticated()) {
        auth.updateUserInfo();
    }

    // Add click handler for navigation
    document.body.addEventListener("click", (e) => auth.handleNavigation(e));
}); 