// Common utility functions
const common = {
    showLoading: function() {
        const spinner = document.getElementById("loadingSpinner");
        if (spinner) spinner.style.display = "flex";
    },

    hideLoading: function() {
        const spinner = document.getElementById("loadingSpinner");
        if (spinner) spinner.style.display = "none";
    },

    showError: function(message) {
        const errorDiv = document.getElementById("errorMessage");
        const errorText = document.getElementById("errorText");
        if (errorDiv && errorText) {
            errorText.textContent = message;
            errorDiv.style.display = "flex";
            setTimeout(() => {
                errorDiv.style.display = "none";
            }, 3000);
        }
    },

    formatDate: function(date) {
        return new Date(date).toLocaleDateString("vi-VN");
    },

    formatCurrency: function(amount) {
        return new Intl.NumberFormat("vi-VN", {
            style: "currency",
            currency: "VND"
        }).format(amount);
    }
}; 