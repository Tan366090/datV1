<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../config/database.php';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Get total employees
    $stmt = $db->query("SELECT COUNT(*) as total FROM employees");
    $totalEmployees = $stmt->fetch()['total'] ?? 0;

    // Get KPI completion rate
    $stmt = $db->query("SELECT COALESCE(AVG(completion_rate), 0) as kpi_completion FROM kpi_records WHERE MONTH(created_at) = MONTH(CURRENT_DATE())");
    $kpiCompletion = $stmt->fetch()['kpi_completion'] ?? 0;

    // Get new candidates
    $stmt = $db->query("SELECT COUNT(*) as new_candidates FROM candidates WHERE DATE(created_at) = CURRENT_DATE()");
    $newCandidates = $stmt->fetch()['new_candidates'] ?? 0;

    // Get active projects
    $stmt = $db->query("SELECT COUNT(*) as active_projects FROM projects WHERE status = 'active'");
    $activeProjects = $stmt->fetch()['active_projects'] ?? 0;

    // Get active employees
    $stmt = $db->query("SELECT COUNT(*) as active_employees FROM employees WHERE status = 'active'");
    $activeEmployees = $stmt->fetch()['active_employees'] ?? 0;

    // Get today's attendance rate
    $stmt = $db->query("SELECT 
        COALESCE((COUNT(CASE WHEN status = 'present' THEN 1 END) / NULLIF(COUNT(*), 0)) * 100, 0) as attendance_rate 
        FROM attendance 
        WHERE DATE(created_at) = CURRENT_DATE()");
    $attendanceRate = $stmt->fetch()['attendance_rate'] ?? 0;

    // Get pending leaves
    $stmt = $db->query("SELECT COUNT(*) as pending_leaves FROM leave_requests WHERE status = 'pending'");
    $pendingLeaves = $stmt->fetch()['pending_leaves'] ?? 0;

    // Get monthly salary
    $stmt = $db->query("SELECT COALESCE(SUM(salary), 0) as monthly_salary FROM employees WHERE status = 'active'");
    $monthlySalary = $stmt->fetch()['monthly_salary'] ?? 0;

    // Get inactive employees
    $stmt = $db->query("SELECT COUNT(*) as inactive_employees FROM employees WHERE status = 'inactive'");
    $inactiveEmployees = $stmt->fetch()['inactive_employees'] ?? 0;

    // Get recent employees
    $stmt = $db->query("SELECT 
        e.employee_code,
        e.full_name,
        COALESCE(p.name, 'Chưa xác định') as position,
        COALESCE(d.name, 'Chưa xác định') as department,
        e.join_date,
        e.birth_date,
        e.phone,
        e.email,
        e.address,
        e.status
        FROM employees e
        LEFT JOIN positions p ON e.position_id = p.id
        LEFT JOIN departments d ON e.department_id = d.id
        ORDER BY e.created_at DESC
        LIMIT 5");
    $recentEmployees = $stmt->fetchAll();

    // Get attendance trend
    $stmt = $db->query("SELECT 
        DATE(created_at) as date,
        COALESCE((COUNT(CASE WHEN status = 'present' THEN 1 END) / NULLIF(COUNT(*), 0)) * 100, 0) as rate
        FROM attendance
        WHERE DATE(created_at) >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date");
    $attendanceTrend = $stmt->fetchAll();

    // Get department distribution
    $stmt = $db->query("SELECT 
        COALESCE(d.name, 'Chưa xác định') as department,
        COUNT(e.id) as employee_count
        FROM departments d
        LEFT JOIN employees e ON d.id = e.department_id
        GROUP BY d.id");
    $departmentDistribution = $stmt->fetchAll();

    // Get mobile app stats
    $stmt = $db->query("SELECT 
        COUNT(*) as downloads,
        COUNT(DISTINCT user_id) as active_users,
        COUNT(*) as notifications_sent
        FROM mobile_app_stats");
    $mobileStats = $stmt->fetch();

    // Get backup info
    $stmt = $db->query("SELECT 
        COALESCE(MAX(backup_date), 'Chưa có') as last_backup,
        COALESCE(SUM(file_size), 0) as total_size
        FROM backups
        ORDER BY backup_date DESC
        LIMIT 1");
    $backupInfo = $stmt->fetch();

    $response = [
        'status' => 'success',
        'data' => [
            'total_employees' => (int)$totalEmployees,
            'kpi_completion' => round((float)$kpiCompletion, 2),
            'new_candidates' => (int)$newCandidates,
            'active_projects' => (int)$activeProjects,
            'active_employees' => (int)$activeEmployees,
            'attendance_rate' => round((float)$attendanceRate, 2),
            'pending_leaves' => (int)$pendingLeaves,
            'monthly_salary' => (int)$monthlySalary,
            'inactive_employees' => (int)$inactiveEmployees,
            'recent_employees' => $recentEmployees,
            'attendance_trend' => $attendanceTrend,
            'department_distribution' => $departmentDistribution,
            'mobile_stats' => [
                'downloads' => (int)($mobileStats['downloads'] ?? 0),
                'active_users' => (int)($mobileStats['active_users'] ?? 0),
                'notifications_sent' => (int)($mobileStats['notifications_sent'] ?? 0)
            ],
            'backup_info' => [
                'last_backup' => $backupInfo['last_backup'] ?? 'Chưa có',
                'total_size' => round(($backupInfo['total_size'] ?? 0) / (1024 * 1024), 2) // Convert to MB
            ]
        ]
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?> 