<?php
// Start output buffering at the very beginning
ob_start();

// Test script for API endpoints
echo "<h1>API Test Results</h1>";

// Test database connection
function testDatabaseConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "qlnhansu";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<div style='color: green;'>✓ Database connection successful</div>";
        return $conn;
    } catch(PDOException $e) {
        echo "<div style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</div>";
        return null;
    }
}

// Test API endpoint
function testEndpoint($endpoint, $expectedFields = []) {
    echo "<div>Testing endpoint: $endpoint</div>";
    
    // Set up environment for api_test.php
    $_GET['endpoint'] = $endpoint;
    
    // Start output buffering for the API response
    ob_start();
    
    // Include the API file
    include 'api_test.php';
    
    // Get the output and clean any previous output
    $response = ob_get_clean();
    
    // Clean the response of any HTML/error messages
    $response = preg_replace('/<br \/>\n<b>Warning<\/b>:.*?<br \/>\n/', '', $response);
    $response = trim($response);
    
    echo "<div>Raw Response: <pre>" . htmlspecialchars($response) . "</pre></div>";
    
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "<div style='color: red;'>✗ Invalid JSON response from $endpoint endpoint. Error: " . json_last_error_msg() . "</div>";
        return;
    }
    
    if (isset($data['error'])) {
        echo "<div style='color: red;'>✗ Error in $endpoint endpoint: " . $data['error'] . "</div>";
        return;
    }
    
    // Check if all expected fields are present
    $missingFields = [];
    foreach ($expectedFields as $field) {
        if (!isset($data[$field])) {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        echo "<div style='color: red;'>✗ Missing fields in $endpoint response: " . implode(', ', $missingFields) . "</div>";
        return;
    }
    
    echo "<div style='color: green;'>✓ $endpoint endpoint working correctly</div>";
    echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
}

// Run tests
echo "<h2>Testing Database Connection</h2>";
$conn = testDatabaseConnection();

if ($conn) {
    echo "<h2>Testing API Endpoints</h2>";
    
    // Test metrics endpoint
    echo "<h3>Testing Metrics Endpoint</h3>";
    testEndpoint('metrics', [
        'totalEmployees',
        'activeEmployees',
        'todayAttendance',
        'pendingLeaves',
        'totalSalary',
        'inactiveEmployees'
    ]);
    
    // Test recent employees endpoint
    echo "<h3>Testing Recent Employees Endpoint</h3>";
    testEndpoint('recent_employees');
    
    // Test attendance trends endpoint
    echo "<h3>Testing Attendance Trends Endpoint</h3>";
    testEndpoint('attendance_trends');
    
    // Test department distribution endpoint
    echo "<h3>Testing Department Distribution Endpoint</h3>";
    testEndpoint('department_distribution');
    
    // Test invalid endpoint
    echo "<h3>Testing Invalid Endpoint</h3>";
    testEndpoint('invalid_endpoint');
}

// Clean any remaining output buffer
ob_end_flush();
?> 