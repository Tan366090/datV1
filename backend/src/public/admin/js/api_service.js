// API Service
const API_BASE_URL = "http://localhost:3000/api";

const api = {
    // Generic API call function
    async call(endpoint, method = "GET", data = null) {
        try {
            const token = localStorage.getItem("token");
            const headers = {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${token}`
            };

            const config = {
                method,
                headers,
                credentials: "include"
            };

            if (data && (method === "POST" || method === "PUT")) {
                config.body = JSON.stringify(data);
            }

            const response = await fetch(`${API_BASE_URL}${endpoint}`, config);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error("API call failed:", error);
            throw error;
        }
    },

    // Employee endpoints
    employees: {
        getAll: () => api.call("/employees"),
        getById: (id) => api.call(`/employees/${id}`),
        create: (data) => api.call("/employees", "POST", data),
        update: (id, data) => api.call(`/employees/${id}`, "PUT", data),
        delete: (id) => api.call(`/employees/${id}`, "DELETE")
    },

    // Department endpoints
    departments: {
        getAll: () => api.call("/departments"),
        getById: (id) => api.call(`/departments/${id}`),
        create: (data) => api.call("/departments", "POST", data),
        update: (id, data) => api.call(`/departments/${id}`, "PUT", data),
        delete: (id) => api.call(`/departments/${id}`, "DELETE")
    },

    // Position endpoints
    positions: {
        getAll: () => api.call("/positions"),
        getById: (id) => api.call(`/positions/${id}`),
        create: (data) => api.call("/positions", "POST", data),
        update: (id, data) => api.call(`/positions/${id}`, "PUT", data),
        delete: (id) => api.call(`/positions/${id}`, "DELETE")
    }
}; 