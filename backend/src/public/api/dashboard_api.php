<?php
// Bật báo lỗi chi tiết
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cấu hình CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Xử lý preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Kiểm tra authentication
function checkAuth() {
    // Cho phép test trong môi trường development
    if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1') {
        return true;
    }

    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized: No token provided']);
        exit();
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);
    if (empty($token)) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized: Invalid token']);
        exit();
    }

    return $token;
}

// Kết nối database
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        $configPath = __DIR__ . '/../../config/database.php';
        if (!file_exists($configPath)) {
            throw new Exception("Database configuration file not found");
        }

        $dbConfig = require $configPath;
        
        $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 5
        ];

        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $options);
    }

    return $pdo;
}

// Lấy thống kê tổng quan
function getDashboardStats() {
    try {
        $pdo = getDBConnection();
        
        // Lấy số lượng nhân viên
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM employees");
        $totalEmployees = $stmt->fetch()['total'];
        
        // Lấy số lượng nhân viên đang làm việc
        $stmt = $pdo->query("SELECT COUNT(*) as active FROM employees WHERE status = 'active'");
        $activeEmployees = $stmt->fetch()['active'];
        
        // Lấy số lượng phòng ban
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM departments");
        $totalDepartments = $stmt->fetch()['total'];
        
        // Lấy tổng lương tháng này
        $stmt = $pdo->query("SELECT SUM(amount) as total FROM payroll WHERE MONTH(payment_date) = MONTH(CURRENT_DATE())");
        $totalSalary = $stmt->fetch()['total'];
        
        return [
            'employees' => [
                'total' => $totalEmployees,
                'active' => $activeEmployees
            ],
            'departments' => $totalDepartments,
            'salary' => $totalSalary
        ];
    } catch (Exception $e) {
        throw new Exception("Error getting dashboard stats: " . $e->getMessage());
    }
}

// Lấy dữ liệu chấm công
function getAttendanceData($period = 'week') {
    try {
        $pdo = getDBConnection();
        $sql = "SELECT 
                    DATE(attendance_date) as date,
                    COUNT(*) as total,
                    SUM(CASE WHEN attendance_symbol = 'P' THEN 1 ELSE 0 END) as present
                FROM attendance 
                WHERE attendance_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 1 $period)
                GROUP BY DATE(attendance_date)
                ORDER BY date";
                
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        throw new Exception("Error getting attendance data: " . $e->getMessage());
    }
}

// Lấy dữ liệu phòng ban
function getDepartmentData() {
    try {
        $pdo = getDBConnection();
        $sql = "SELECT 
                    d.name,
                    COUNT(e.id) as employee_count
                FROM departments d
                LEFT JOIN employees e ON e.department_id = d.id
                GROUP BY d.id, d.name";
                
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        throw new Exception("Error getting department data: " . $e->getMessage());
    }
}

// Lấy danh sách nhân viên mới nhất
function getRecentEmployees($limit = 10) {
    try {
        $pdo = getDBConnection();
        $sql = "SELECT 
                    e.*,
                    d.name as department_name,
                    p.name as position_name
                FROM employees e
                LEFT JOIN departments d ON e.department_id = d.id
                LEFT JOIN positions p ON e.position_id = p.id
                ORDER BY e.created_at DESC
                LIMIT $limit";
                
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        throw new Exception("Error getting recent employees: " . $e->getMessage());
    }
}

// Xử lý request
try {
    // Kiểm tra authentication
    checkAuth();
    
    // Lấy endpoint từ URL
    $endpoint = $_GET['endpoint'] ?? '';
    
    switch ($endpoint) {
        case 'stats':
            $data = getDashboardStats();
            break;
            
        case 'attendance':
            $period = $_GET['period'] ?? 'week';
            $data = getAttendanceData($period);
            break;
            
        case 'departments':
            $data = getDepartmentData();
            break;
            
        case 'recent-employees':
            $limit = $_GET['limit'] ?? 10;
            $data = getRecentEmployees($limit);
            break;
            
        default:
            throw new Exception("Invalid endpoint");
    }
    
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 