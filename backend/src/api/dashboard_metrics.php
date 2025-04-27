<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/UserProfile.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get total employees
    $query = "SELECT COUNT(*) as total FROM user_profiles";
    $stmt = $conn->query($query);
    $totalEmployees = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get active employees
    $query = "SELECT COUNT(*) as total FROM users WHERE status = 'active'";
    $stmt = $conn->query($query);
    $activeEmployees = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get inactive employees
    $query = "SELECT COUNT(*) as total FROM users WHERE status = 'inactive'";
    $stmt = $conn->query($query);
    $inactiveEmployees = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get KPI completion (example calculation)
    $query = "SELECT COALESCE(AVG(completion_rate), 0) as avg_completion FROM kpi_records WHERE MONTH(date) = MONTH(CURRENT_DATE())";
    $stmt = $conn->query($query);
    $kpiCompletion = $stmt->fetch(PDO::FETCH_ASSOC)['avg_completion'];
    
    // Get new candidates
    $query = "SELECT COUNT(*) as total FROM candidates WHERE status = 'new'";
    $stmt = $conn->query($query);
    $newCandidates = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get active projects
    $query = "SELECT COUNT(*) as total FROM projects WHERE status = 'active'";
    $stmt = $conn->query($query);
    $activeProjects = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get today's attendance rate
    $query = "SELECT 
        COALESCE((COUNT(CASE WHEN status = 'present' THEN 1 END) * 100.0 / NULLIF(COUNT(*), 0)), 0) as attendance_rate 
        FROM attendance 
        WHERE DATE(date) = CURRENT_DATE";
    $stmt = $conn->query($query);
    $attendanceRate = $stmt->fetch(PDO::FETCH_ASSOC)['attendance_rate'];
    
    // Get pending leave requests
    $query = "SELECT COUNT(*) as total FROM leave_requests WHERE status = 'pending'";
    $stmt = $conn->query($query);
    $pendingLeaves = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get total monthly salary
    $query = "SELECT COALESCE(SUM(net_salary), 0) as total FROM payroll WHERE MONTH(date) = MONTH(CURRENT_DATE())";
    $stmt = $conn->query($query);
    $monthlySalary = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Return all metrics
    echo json_encode([
        'status' => 'success',
        'data' => [
            'total_employees' => (int)$totalEmployees,
            'kpi_completion' => round((float)$kpiCompletion, 2),
            'new_candidates' => (int)$newCandidates,
            'active_projects' => (int)$activeProjects,
            'active_employees' => (int)$activeEmployees,
            'attendance_rate' => round((float)$attendanceRate, 2),
            'pending_leaves' => (int)$pendingLeaves,
            'monthly_salary' => (float)$monthlySalary,
            'inactive_employees' => (int)$inactiveEmployees
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 