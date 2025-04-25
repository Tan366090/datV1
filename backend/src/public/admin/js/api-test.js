// API Test Utility
const APITest = {
    // Test API connection
    async testConnection() {
        try {
            console.log('Testing API connection...');
            const response = await fetch('/api/test/connection', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('API Connection Test Result:', data);
            return data.success;
        } catch (error) {
            console.error('API Connection Test Error:', error);
            console.error('Error Details:', {
                message: error.message,
                stack: error.stack,
                type: error.name
            });
            return false;
        }
    },

    // Test database connection
    async testDatabase() {
        try {
            console.log('Testing database connection...');
            const response = await fetch('/api/test/database', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Database Test Result:', data);
            return data.success;
        } catch (error) {
            console.error('Database Test Error:', error);
            console.error('Error Details:', {
                message: error.message,
                stack: error.stack,
                type: error.name
            });
            return false;
        }
    },

    // Test API endpoints
    async testEndpoints() {
        const endpoints = [
            '/api/employees',
            '/api/departments',
            '/api/positions',
            '/api/performances',
            '/api/payroll',
            '/api/leaves',
            '/api/trainings',
            '/api/tasks'
        ];

        const results = {};

        for (const endpoint of endpoints) {
            try {
                console.log(`Testing endpoint: ${endpoint}`);
                const response = await fetch(endpoint, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                results[endpoint] = {
                    success: true,
                    data: data
                };
                console.log(`${endpoint} Test Result:`, data);
            } catch (error) {
                results[endpoint] = {
                    success: false,
                    error: {
                        message: error.message,
                        stack: error.stack,
                        type: error.name
                    }
                };
                console.error(`${endpoint} Test Error:`, error);
            }
        }

        return results;
    },

    // Run all tests
    async runAllTests() {
        console.log('Starting API tests...');
        
        const results = {
            connection: await this.testConnection(),
            database: await this.testDatabase(),
            endpoints: await this.testEndpoints()
        };

        // Display results in console
        console.log('All Test Results:', results);

        // Display results in UI
        this.displayResults(results);

        return results;
    },

    // Display results in UI
    displayResults(results) {
        const container = document.createElement('div');
        container.className = 'api-test-results';
        container.innerHTML = `
            <h2>API Test Results</h2>
            <div class="test-result">
                <h3>Connection Test</h3>
                <p>Status: ${results.connection ? 'Success' : 'Failed'}</p>
            </div>
            <div class="test-result">
                <h3>Database Test</h3>
                <p>Status: ${results.database ? 'Success' : 'Failed'}</p>
            </div>
            <div class="test-result">
                <h3>Endpoints Test</h3>
                <div class="endpoints-results">
                    ${Object.entries(results.endpoints).map(([endpoint, result]) => `
                        <div class="endpoint-result">
                            <h4>${endpoint}</h4>
                            <p>Status: ${result.success ? 'Success' : 'Failed'}</p>
                            ${!result.success ? `
                                <div class="error-details">
                                    <p>Error: ${result.error.message}</p>
                                    <p>Type: ${result.error.type}</p>
                                </div>
                            ` : ''}
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

        // Add styles
        const style = document.createElement('style');
        style.textContent = `
            .api-test-results {
                padding: 20px;
                background: #f8f9fa;
                border-radius: 8px;
                margin: 20px;
                font-family: Arial, sans-serif;
            }
            .test-result {
                margin-bottom: 20px;
                padding: 15px;
                background: white;
                border-radius: 5px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .endpoint-result {
                margin: 10px 0;
                padding: 10px;
                background: #f1f1f1;
                border-radius: 4px;
            }
            .error-details {
                color: #dc3545;
                font-size: 0.9em;
                margin-top: 5px;
            }
        `;

        document.head.appendChild(style);
        document.body.appendChild(container);
    }
};

// Run tests when document is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('Running API tests...');
    APITest.runAllTests().catch(error => {
        console.error('Failed to run API tests:', error);
    });
}); 