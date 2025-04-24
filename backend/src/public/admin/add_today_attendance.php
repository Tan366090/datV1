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
    
    // Get all active employees
    $stmt = $conn->query("
        SELECT user_id, username 
        FROM users 
        WHERE role_id != 1 AND status = 'active'
    ");
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $today = date('Y-m-d');
    $now = date('Y-m-d H:i:s');
    
    // Add attendance records for each employee
    foreach ($employees as $employee) {
        $stmt = $conn->prepare("
            INSERT INTO attendance (
                user_id, 
                attendance_date, 
                recorded_at, 
                notes, 
                attendance_symbol, 
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $employee['user_id'],
            $today,
            $now,
            'Check-in đúng giờ',
            'P',
            $now
        ]);
    }
    
    echo "<div style='color: green;'>✓ Đã thêm dữ liệu chấm công cho hôm nay</div>";
    
} catch(PDOException $e) {
    echo "<div style='color: red;'>✗ Lỗi: " . $e->getMessage() . "</div>";
}

// End output buffering
ob_end_flush();
?> 