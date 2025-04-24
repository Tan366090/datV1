const CONFIG = {
    API_BASE_URL: 'http://localhost/qlnhansu_V2/backend/src/public/api',
    ASSETS_URL: 'http://localhost/qlnhansu_V2/backend/src/public/admin/assets',
    DEFAULT_ERROR_MESSAGE: 'Có lỗi xảy ra, vui lòng thử lại sau',
    API_TIMEOUT: 30000, // 30 seconds
    DEBUG_MODE: true
};

// API Endpoints
const API_ENDPOINTS = {
    EMPLOYEES: `${CONFIG.API_BASE_URL}/employees`,
    ATTENDANCE: `${CONFIG.API_BASE_URL}/attendance`,
    DEPARTMENTS: `${CONFIG.API_BASE_URL}/departments`,
    SALARIES: `${CONFIG.API_BASE_URL}/salaries`,
    AUTH: `${CONFIG.API_BASE_URL}/auth`,
    SETTINGS: `${CONFIG.API_BASE_URL}/settings`
};

// Error Handler
const handleApiError = (error) => {
    if (CONFIG.DEBUG_MODE) {
        console.error('API Error:', error);
    }
    
    if (error.name === 'AbortError') {
        return { error: 'Request timeout' };
    }
    
    if (!navigator.onLine) {
        return { error: 'No internet connection' };
    }
    
    return { error: CONFIG.DEFAULT_ERROR_MESSAGE };
};

// API Fetcher with timeout
const fetchWithTimeout = async (url, options = {}) => {
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), CONFIG.API_TIMEOUT);
    
    try {
        const response = await fetch(url, {
            ...options,
            signal: controller.signal,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        });
        
        clearTimeout(timeout);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        clearTimeout(timeout);
        throw error;
    }
};

const DashboardConfig = {
    apiEndpoint: "http://localhost:8080",
    refreshInterval: 30000, // 30 seconds
    chartColors: {
        primary: "#4ca1af",
        secondary: "#85ffbd",
        danger: "#ff6b6b"
    },
    notifications: {
        enabled: true,
        position: "top-right",
        duration: 5000
    }
}; 