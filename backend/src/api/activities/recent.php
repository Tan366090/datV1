<?php
// Start output buffering
ob_start();

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON header
header('Content-Type: application/json');

require_once '../../config/database.php';
require_once '../../middlewares/auth.php';

try {
    // Clear any previous output
    ob_clean();

    // Verify user is logged in and is a manager
    checkAuth();
    checkRole('manager');

    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("
        (SELECT 
            'leave' as type,
            CONCAT(up.first_name, ' ', up.last_name) as title,
            CONCAT('Đã yêu cầu nghỉ phép từ ', DATE_FORMAT(lr.start_date, '%d/%m/%Y'), ' đến ', DATE_FORMAT(lr.end_date, '%d/%m/%Y')) as description,
            lr.created_at as timestamp
        FROM leave_requests lr
        JOIN users u ON lr.user_id = u.user_id
        JOIN user_profiles up ON u.user_id = up.user_id
        WHERE lr.status = 'pending')
        
        UNION ALL
        
        (SELECT 
            'attendance' as type,
            CONCAT(up.first_name, ' ', up.last_name) as title,
            CASE 
                WHEN a.check_out IS NULL THEN CONCAT('Đã check in lúc ', DATE_FORMAT(a.check_in, '%H:%i'))
                ELSE CONCAT('Đã check out lúc ', DATE_FORMAT(a.check_out, '%H:%i'))
            END as description,
            COALESCE(a.check_out, a.check_in) as timestamp
        FROM attendance a
        JOIN users u ON a.user_id = u.user_id
        JOIN user_profiles up ON u.user_id = up.user_id
        WHERE DATE(a.check_in) = CURDATE())
        
        UNION ALL
        
        (SELECT 
            'salary' as type,
            CONCAT(up.first_name, ' ', up.last_name) as title,
            CONCAT('Đã được thanh toán lương tháng ', DATE_FORMAT(sh.payment_date, '%m/%Y')) as description,
            sh.payment_date as timestamp
        FROM salary_history sh
        JOIN users u ON sh.user_id = u.user_id
        JOIN user_profiles up ON u.user_id = up.user_id
        WHERE DATE_FORMAT(sh.payment_date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m'))
        
        ORDER BY timestamp DESC
        LIMIT 10
    ");
    $stmt->execute();
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $activities
    ]);
} catch (Exception $e) {
    // Clear any previous output
    ob_clean();
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi tải hoạt động gần đây: ' . $e->getMessage()
    ]);
}

// End output buffering and send response
ob_end_flush(); 