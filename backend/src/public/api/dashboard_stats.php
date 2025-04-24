<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../../config/database.php';

try {
    $database = Database::getInstance();
    $db = $database->getConnection();

    // Get department statistics
    $deptQuery = "SELECT d.name as department_name, COUNT(u.user_id) as count 
                  FROM departments d 
                  LEFT JOIN users u ON d.id = u.department_id 
                  WHERE u.role_id != 1 -- Only count non-admin users
                  GROUP BY d.id, d.name";
    $deptStmt = $db->query($deptQuery);
    $departmentStats = $deptStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get attendance statistics for the last 7 days
    $attendanceQuery = "SELECT DATE_FORMAT(attendance_date, '%d/%m') as date, 
                              (COUNT(CASE WHEN attendance_symbol = 'P' THEN 1 END) * 100.0 / COUNT(*)) as rate
                       FROM attendance 
                       WHERE attendance_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                       GROUP BY attendance_date
                       ORDER BY attendance_date";
    $attendanceStmt = $db->query($attendanceQuery);
    $attendanceStats = $attendanceStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get salary statistics by department
    $salaryQuery = "SELECT d.name as department, SUM(s.total_salary) as total
                    FROM payroll s
                    JOIN users u ON s.user_id = u.user_id
                    JOIN departments d ON u.department_id = d.id
                    WHERE s.payroll_month = MONTH(CURDATE()) AND s.payroll_year = YEAR(CURDATE())
                    GROUP BY d.id, d.name";
    $salaryStmt = $db->query($salaryQuery);
    $salaryStats = $salaryStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get leave statistics
    $leaveQuery = "SELECT 
                   COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
                   COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                   COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected
                   FROM leaves
                   WHERE YEAR(start_date) = YEAR(CURDATE())";
    $leaveStmt = $db->query($leaveQuery);
    $leaveStats = $leaveStmt->fetch(PDO::FETCH_ASSOC);

    // Get total employees
    $stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE role_id != 1");
    $totalEmployees = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get active employees
    $stmt = $db->query("SELECT COUNT(*) as active FROM users WHERE status = 'active' AND role_id != 1");
    $activeEmployees = $stmt->fetch(PDO::FETCH_ASSOC)['active'];

    // Get inactive employees
    $stmt = $db->query("SELECT COUNT(*) as inactive FROM users WHERE status = 'inactive' AND role_id != 1");
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
    $stmt = $db->query("SELECT SUM(total_salary) as total FROM payroll WHERE payroll_month = MONTH(CURRENT_DATE()) AND payroll_year = YEAR(CURRENT_DATE())");
    $totalSalary = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Prepare response data
    $response = [
        'status' => 'success',
        'data' => [
            'departmentStats' => array_map(function($item) {
                return [
                    'name' => $item['department_name'],
                    'count' => (int)$item['count']
                ];
            }, $departmentStats),
            'attendanceStats' => array_map(function($item) {
                return [
                    'date' => $item['date'],
                    'rate' => (float)$item['rate']
                ];
            }, $attendanceStats),
            'salaryStats' => array_map(function($item) {
                return [
                    'department' => $item['department'],
                    'total' => (float)$item['total']
                ];
            }, $salaryStats),
            'leaveStats' => [
                'approved' => (int)$leaveStats['approved'],
                'pending' => (int)$leaveStats['pending'],
                'rejected' => (int)$leaveStats['rejected']
            ],
            'totalEmployees' => (int)$totalEmployees,
            'activeEmployees' => (int)$activeEmployees,
            'inactiveEmployees' => (int)$inactiveEmployees,
            'pendingLeaves' => (int)$pendingLeaves,
            'todayAttendance' => (float)$todayAttendance,
            'totalSalary' => (float)$totalSalary
        ]
    ];

    echo json_encode($response, JSON_NUMERIC_CHECK);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?> 