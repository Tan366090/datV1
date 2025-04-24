<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get total employees
    $stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE role != 'admin'");
    $totalEmployees = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get active employees
    $stmt = $db->query("SELECT COUNT(*) as active FROM users WHERE status = 'active' AND role != 'admin'");
    $activeEmployees = $stmt->fetch(PDO::FETCH_ASSOC)['active'];

    // Get inactive employees
    $stmt = $db->query("SELECT COUNT(*) as inactive FROM users WHERE status = 'inactive' AND role != 'admin'");
    $inactiveEmployees = $stmt->fetch(PDO::FETCH_ASSOC)['inactive'];

    // Get pending leaves
    $stmt = $db->query("SELECT COUNT(*) as pending FROM leaves WHERE status = 'pending'");
    $pendingLeaves = $stmt->fetch(PDO::FETCH_ASSOC)['pending'];

    // Get today's attendance percentage
    $today = date('Y-m-d');
    $stmt = $db->prepare("
        SELECT 
            (COUNT(CASE WHEN attendance_symbol = 'P' THEN 1 END) * 100.0 / COUNT(*)) as attendance_rate
        FROM attendance 
        WHERE attendance_date = ?
    ");
    $stmt->execute([$today]);
    $todayAttendance = round($stmt->fetch(PDO::FETCH_ASSOC)['attendance_rate'], 1);

    // Get total monthly salary
    $stmt = $db->query("SELECT SUM(amount) as total FROM payroll WHERE MONTH(payment_date) = MONTH(CURRENT_DATE())");
    $totalSalary = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $response = [
        'status' => 'success',
        'data' => [
            'totalEmployees' => $totalEmployees,
            'activeEmployees' => $activeEmployees,
            'inactiveEmployees' => $inactiveEmployees,
            'pendingLeaves' => $pendingLeaves,
            'todayAttendance' => $todayAttendance ?? 0,
            'totalSalary' => $totalSalary ?? 0
        ]
    ];

    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?> 