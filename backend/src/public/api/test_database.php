<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
$host = 'localhost';
$dbname = 'qlnhansu';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test attendance table structure and data
    $attendance_test = [
        'table_structure' => [],
        'sample_data' => [],
        'count' => 0,
        'date_range' => [],
        'symbol_distribution' => []
    ];
    
    // Get attendance table structure
    $stmt = $pdo->query("DESCRIBE attendance");
    $attendance_test['table_structure'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get sample attendance data with all possible symbols
    $stmt = $pdo->query("
        SELECT a.*, e.full_name 
        FROM attendance a 
        JOIN employees e ON a.user_id = e.employee_id 
        ORDER BY a.attendance_date DESC 
        LIMIT 10
    ");
    $attendance_test['sample_data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total attendance records
    $stmt = $pdo->query("SELECT COUNT(*) FROM attendance");
    $attendance_test['count'] = $stmt->fetchColumn();
    
    // Get date range of attendance records
    $stmt = $pdo->query("SELECT MIN(attendance_date) as min_date, MAX(attendance_date) as max_date FROM attendance");
    $attendance_test['date_range'] = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get symbol distribution
    $stmt = $pdo->query("
        SELECT attendance_symbol, COUNT(*) as count 
        FROM attendance 
        GROUP BY attendance_symbol 
        ORDER BY count DESC
    ");
    $attendance_test['symbol_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Test employees table structure and data
    $employees_test = [
        'table_structure' => [],
        'sample_data' => [],
        'count' => 0,
        'status_distribution' => [],
        'department_distribution' => []
    ];
    
    // Get employees table structure
    $stmt = $pdo->query("DESCRIBE employees");
    $employees_test['table_structure'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get sample employee data with department and position info
    $stmt = $pdo->query("
        SELECT e.*, d.department_name, p.position_name 
        FROM employees e 
        LEFT JOIN departments d ON e.department_id = d.department_id 
        LEFT JOIN positions p ON e.position_id = p.position_id 
        ORDER BY e.employee_id 
        LIMIT 10
    ");
    $employees_test['sample_data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total employee records
    $stmt = $pdo->query("SELECT COUNT(*) FROM employees");
    $employees_test['count'] = $stmt->fetchColumn();
    
    // Get status distribution
    $stmt = $pdo->query("
        SELECT status, COUNT(*) as count 
        FROM employees 
        GROUP BY status 
        ORDER BY count DESC
    ");
    $employees_test['status_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get department distribution
    $stmt = $pdo->query("
        SELECT d.department_name, COUNT(e.employee_id) as count 
        FROM departments d 
        LEFT JOIN employees e ON d.department_id = e.department_id 
        GROUP BY d.department_id 
        ORDER BY count DESC
    ");
    $employees_test['department_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Test departments table structure and data
    $departments_test = [
        'table_structure' => [],
        'sample_data' => [],
        'count' => 0,
        'employee_counts' => []
    ];
    
    // Get departments table structure
    $stmt = $pdo->query("DESCRIBE departments");
    $departments_test['table_structure'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get sample department data with employee counts
    $stmt = $pdo->query("
        SELECT d.*, COUNT(e.employee_id) as employee_count 
        FROM departments d 
        LEFT JOIN employees e ON d.department_id = e.department_id 
        GROUP BY d.department_id 
        ORDER BY d.department_id
    ");
    $departments_test['sample_data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total department records
    $stmt = $pdo->query("SELECT COUNT(*) FROM departments");
    $departments_test['count'] = $stmt->fetchColumn();
    
    // Prepare response
    $response = [
        'success' => true,
        'data' => [
            'attendance_test' => $attendance_test,
            'employees_test' => $employees_test,
            'departments_test' => $departments_test
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    $response = [
        'success' => false,
        'error' => [
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT);
}
?> 