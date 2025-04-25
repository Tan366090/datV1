// Test script to verify data fetching from storage
const StorageTest = {
    async testDataFetching() {
        console.log('Starting storage test...');
        
        try {
            // Test fetching employees
            console.log('Testing employees data...');
            const employees = await fetch('http://localhost/qlnhansu_V2/backend/src/public/api/data.php?action=getData&table=employees', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            const employeesData = await employees.json();
            console.log('Employees response:', employeesData);
            
            // Test fetching departments
            console.log('Testing departments data...');
            const departments = await fetch('http://localhost/qlnhansu_V2/backend/src/public/api/data.php?action=getData&table=departments', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            const departmentsData = await departments.json();
            console.log('Departments response:', departmentsData);
            
            // Test fetching positions
            console.log('Testing positions data...');
            const positions = await fetch('http://localhost/qlnhansu_V2/backend/src/public/api/data.php?action=getData&table=positions', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            const positionsData = await positions.json();
            console.log('Positions response:', positionsData);
            
            // Display results
            const results = {
                employees: employeesData?.success ? 'Success' : 'Failed',
                departments: departmentsData?.success ? 'Success' : 'Failed',
                positions: positionsData?.success ? 'Success' : 'Failed'
            };
            
            console.log('Test Results:', results);
            
            // Show notification
            alert('Storage test completed. Check console for results.');
            
            return results;
        } catch (error) {
            console.error('Storage test failed:', error);
            alert('Storage test failed: ' + error.message);
            return null;
        }
    }
};

// Run test when page loads
document.addEventListener('DOMContentLoaded', () => {
    // Add test button to header
    const headerControls = document.querySelector('.header-controls');
    if (headerControls) {
        const testButton = document.createElement('button');
        testButton.className = 'btn btn-warning';
        testButton.innerHTML = '<i class="fas fa-vial"></i> Test Storage';
        testButton.onclick = () => StorageTest.testDataFetching();
        headerControls.appendChild(testButton);
    }
}); 