<?php
// Start output buffering to prevent headers already sent error
ob_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "qlnhansu";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
    exit();
}

// Function declarations
if (!function_exists('getTotalEmployees')) {
    function getTotalEmployees($conn) {
        try {
            $stmt = $conn->query("SELECT COUNT(*) as total FROM employees");
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch(PDOException $e) {
            return 0;
        }
    }
}

if (!function_exists('getActiveEmployees')) {
    function getActiveEmployees($conn) {
        try {
            $stmt = $conn->query("SELECT COUNT(*) as active FROM employees WHERE status = 'active'");
            return $stmt->fetch(PDO::FETCH_ASSOC)['active'];
        } catch(PDOException $e) {
            return 0;
        }
    }
}

if (!function_exists('getTodayAttendanceRate')) {
    function getTodayAttendanceRate($conn) {
        try {
            $today = date('Y-m-d');
            $stmt = $conn->prepare("
                SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN a.attendance_symbol = 'P' THEN 1 END) as present
                FROM attendance a
                WHERE a.attendance_date = ?
            ");
            $stmt->execute([$today]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['total'] > 0) {
                return round(($result['present'] * 100.0) / $result['total'], 1);
            }
            return 0;
        } catch(PDOException $e) {
            error_log("Error in getTodayAttendanceRate: " . $e->getMessage());
            return 0;
        }
    }
}

if (!function_exists('getPendingLeaves')) {
    function getPendingLeaves($conn) {
        try {
            $stmt = $conn->query("SELECT COUNT(*) as pending FROM leaves WHERE status = 'pending'");
            return $stmt->fetch(PDO::FETCH_ASSOC)['pending'];
        } catch(PDOException $e) {
            return 0;
        }
    }
}

if (!function_exists('getTotalMonthlySalary')) {
    function getTotalMonthlySalary($conn) {
        try {
            $stmt = $conn->query("
                SELECT FORMAT(COALESCE(SUM(total_salary), 0), 2) as total 
                FROM payroll 
                WHERE payroll_month = 4 
                AND payroll_year = 2024
            ");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? "0.00";
        } catch(PDOException $e) {
            error_log("Error in getTotalMonthlySalary: " . $e->getMessage());
            return "0.00";
        }
    }
}

if (!function_exists('getInactiveEmployees')) {
    function getInactiveEmployees($conn) {
        try {
            $stmt = $conn->query("SELECT COUNT(*) as inactive FROM employees WHERE status = 'inactive'");
            return $stmt->fetch(PDO::FETCH_ASSOC)['inactive'];
        } catch(PDOException $e) {
            return 0;
        }
    }
}

if (!function_exists('getRecentEmployees')) {
    function getRecentEmployees($conn) {
        try {
            $stmt = $conn->query("SELECT e.*, d.name as department_name, p.name as position_name 
                FROM employees e 
                LEFT JOIN departments d ON e.department_id = d.id 
                LEFT JOIN positions p ON e.position_id = p.id 
                ORDER BY e.hire_date DESC LIMIT 5");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }
}

if (!function_exists('getAttendanceTrends')) {
    function getAttendanceTrends($conn) {
        try {
            $period = isset($_GET['period']) ? $_GET['period'] : 'week';
            $endDate = date('Y-m-d');
            
            switch($period) {
                case 'week':
                    $startDate = date('Y-m-d', strtotime('-7 days'));
                    break;
                case 'month':
                    $startDate = date('Y-m-d', strtotime('-30 days'));
                    break;
                case 'quarter':
                    $startDate = date('Y-m-d', strtotime('-90 days'));
                    break;
                default:
                    $startDate = date('Y-m-d', strtotime('-7 days'));
            }
            
            // Get total active employees
            $stmt = $conn->query("SELECT COUNT(*) as total FROM users WHERE role_id != 1 AND status = 'active'");
            $totalEmployees = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            if ($totalEmployees == 0) {
                return [];
            }
            
            $stmt = $conn->prepare("
                SELECT 
                    a.attendance_date as date, 
                    COUNT(*) as count,
                    (COUNT(CASE WHEN a.attendance_symbol = 'P' THEN 1 END) * 100.0 / ?) as attendance_rate
                FROM attendance a
                WHERE a.attendance_date BETWEEN ? AND ? 
                GROUP BY a.attendance_date 
                ORDER BY date
            ");
            $stmt->execute([$totalEmployees, $startDate, $endDate]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug log
            error_log("Attendance trends result: " . print_r($result, true));
            
            return $result;
        } catch(PDOException $e) {
            error_log("Error in getAttendanceTrends: " . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('getDepartmentDistribution')) {
    function getDepartmentDistribution($conn) {
        try {
            $stmt = $conn->query("SELECT d.name as department, COUNT(e.id) as count 
                FROM departments d 
                LEFT JOIN employees e ON d.id = e.department_id 
                GROUP BY d.id, d.name");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }
}

if (!function_exists('getRecentActivities')) {
    function getRecentActivities($conn) {
        try {
            $stmt = $conn->query("SELECT a.*, u.username 
                FROM activities a 
                LEFT JOIN users u ON a.user_id = u.id 
                ORDER BY a.created_at DESC LIMIT 10");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }
}

if (!function_exists('getEmployeePerformance')) {
    function getEmployeePerformance($conn) {
        try {
            $stmt = $conn->query("SELECT e.employee_code, e.id, p.rating, p.comments 
                FROM employees e 
                LEFT JOIN performances p ON e.id = p.employee_id 
                WHERE p.created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH) 
                ORDER BY p.rating DESC LIMIT 5");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }
}

if (!function_exists('getUpcomingTrainings')) {
    function getUpcomingTrainings($conn) {
        try {
            $stmt = $conn->query("SELECT t.*, d.name as department_name 
                FROM trainings t 
                LEFT JOIN departments d ON t.department_id = d.id 
                WHERE t.start_date >= CURDATE() 
                ORDER BY t.start_date ASC LIMIT 5");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }
}

// Main API endpoint
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';
    
    try {
        switch($endpoint) {
            case 'metrics':
                $response = [
                    'totalEmployees' => getTotalEmployees($conn),
                    'activeEmployees' => getActiveEmployees($conn),
                    'todayAttendance' => getTodayAttendanceRate($conn),
                    'pendingLeaves' => getPendingLeaves($conn),
                    'totalSalary' => getTotalMonthlySalary($conn),
                    'inactiveEmployees' => getInactiveEmployees($conn)
                ];
                break;
                
            case 'recent_employees':
                $response = getRecentEmployees($conn);
                break;
                
            case 'attendance_trends':
                $response = getAttendanceTrends($conn);
                break;
                
            case 'department_distribution':
                $response = getDepartmentDistribution($conn);
                break;
                
            case 'recent_activities':
                $response = getRecentActivities($conn);
                break;
                
            case 'employee_performance':
                $response = getEmployeePerformance($conn);
                break;
                
            case 'upcoming_trainings':
                $response = getUpcomingTrainings($conn);
                break;
                
            default:
                $response = ['error' => 'Invalid endpoint'];
                break;
        }
        
        // Clear any previous output
        ob_clean();
        
        // Send the JSON response
        echo json_encode($response);
        
    } catch(Exception $e) {
        // Clear any previous output
        ob_clean();
        
        // Send error response
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Method not allowed']);
}

// End output buffering
ob_end_flush();
?> 