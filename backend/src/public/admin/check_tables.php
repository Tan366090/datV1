<?php
// Start output buffering
ob_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "qlnhansu";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Checking Attendance Data</h2>";
    
    // Check attendance data
    $stmt = $conn->query("
        SELECT 
            a.*,
            u.username,
            e.employee_code
        FROM attendance a
        LEFT JOIN users u ON a.user_id = u.user_id
        LEFT JOIN employees e ON a.user_id = e.user_id
        ORDER BY a.attendance_date DESC
    ");
    $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Attendance Records</h3>";
    echo "<pre>";
    print_r($attendance);
    echo "</pre>";
    
    // Check today's attendance
    $today = date('Y-m-d');
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN attendance_symbol = 'P' THEN 1 END) as present
        FROM attendance 
        WHERE attendance_date = ?
    ");
    $stmt->execute([$today]);
    $todayStats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h3>Today's Attendance Stats</h3>";
    echo "<pre>";
    print_r($todayStats);
    echo "</pre>";
    
    echo "<h2>Checking Payroll Data</h2>";
    
    // Check payroll data
    $stmt = $conn->query("
        SELECT 
            p.*,
            u.username,
            e.employee_code
        FROM payroll p
        LEFT JOIN users u ON p.user_id = u.user_id
        LEFT JOIN employees e ON p.user_id = e.user_id
        ORDER BY p.payroll_year DESC, p.payroll_month DESC
    ");
    $payroll = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Payroll Records</h3>";
    echo "<pre>";
    print_r($payroll);
    echo "</pre>";
    
    // Check current month's payroll
    $stmt = $conn->query("
        SELECT 
            COUNT(*) as total,
            SUM(total_salary) as total_amount
        FROM payroll 
        WHERE payroll_month = MONTH(CURRENT_DATE()) 
        AND payroll_year = YEAR(CURRENT_DATE())
    ");
    $currentMonthStats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h3>Current Month's Payroll Stats</h3>";
    echo "<pre>";
    print_r($currentMonthStats);
    echo "</pre>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// End output buffering
ob_end_flush();
?> 