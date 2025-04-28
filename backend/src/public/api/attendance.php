<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "SELECT 
                DATE(e.check_in) as date,
                COUNT(*) as total_employees,
                SUM(CASE WHEN e.status = 'on_time' THEN 1 ELSE 0 END) as on_time_count,
                SUM(CASE WHEN e.status = 'late' THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN e.status = 'absent' THEN 1 ELSE 0 END) as absent_count
            FROM employee_attendance e
            WHERE e.check_in >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(e.check_in)
            ORDER BY date DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $attendance
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 