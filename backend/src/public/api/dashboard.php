<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception("Could not connect to database");
    }
    
    // Lấy số đơn xin nghỉ phép chờ duyệt
    $stmt = $conn->prepare("SELECT COUNT(*) FROM leaves WHERE status = 'pending'");
    $stmt->execute();
    $pendingLeaves = $stmt->fetchColumn();
    
    // Lấy số lượng nhân viên hoạt động
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE status = 'active'");
    $stmt->execute();
    $activeEmployees = $stmt->fetchColumn();
    
    // Lấy số lượng nhân viên không hoạt động
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE status = 'inactive'");
    $stmt->execute();
    $inactiveEmployees = $stmt->fetchColumn();
    
    // Lấy tỷ lệ chấm công hôm nay
    $today = date('Y-m-d');
    $stmt = $conn->prepare("
        SELECT 
            CASE 
                WHEN (SELECT COUNT(*) FROM users WHERE status = 'active') > 0 
                THEN ROUND((SELECT COUNT(*) FROM attendance WHERE DATE(attendance_date) = :today) * 100.0 / 
                     (SELECT COUNT(*) FROM users WHERE status = 'active'), 1)
                ELSE 0 
            END as attendance_rate
    ");
    $stmt->bindParam(':today', $today);
    $stmt->execute();
    $todayAttendance = $stmt->fetchColumn() . '%';
    
    // Lấy tổng quỹ lương tháng
    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(total_salary), 0) 
        FROM payroll 
        WHERE payroll_month = MONTH(CURRENT_DATE()) 
        AND payroll_year = YEAR(CURRENT_DATE())
    ");
    $stmt->execute();
    $totalSalary = number_format($stmt->fetchColumn() / 1000000, 1) . 'M';
    
    // Lấy danh sách nhân viên mới
    $stmt = $conn->prepare("
        SELECT u.user_id as id, up.full_name as name, p.name as position, u.hire_date as joinDate
        FROM users u
        LEFT JOIN user_profiles up ON u.user_id = up.user_id
        LEFT JOIN positions p ON u.position_id = p.id
        WHERE u.status = 'active' 
        ORDER BY u.hire_date DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $recentEmployees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Lấy hoạt động gần đây từ audit_logs
    $stmt = $conn->prepare("
        SELECT 
            action_type as type,
            CONCAT(
                COALESCE(
                    (SELECT full_name FROM user_profiles WHERE user_id = audit_logs.user_id),
                    'Unknown User'
                ),
                ' ',
                LOWER(action_type),
                ' ',
                COALESCE(target_entity, '')
            ) as text,
            timestamp as time
        FROM audit_logs 
        ORDER BY timestamp DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $recentActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $data = [
        'success' => true,
        'data' => [
            'totalEmployees' => $conn->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'activeEmployees' => $activeEmployees,
            'inactiveEmployees' => $inactiveEmployees,
            'pendingLeaves' => $pendingLeaves,
            'todayAttendance' => $todayAttendance,
            'totalSalary' => $totalSalary,
            'recentEmployees' => $recentEmployees,
            'recentActivities' => $recentActivities
        ]
    ];
    
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your request',
        'debug_message' => $e->getMessage() // Only in development
    ], JSON_UNESCAPED_UNICODE);
} 