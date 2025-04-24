<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "SELECT 
        a.attendance_id,
        a.user_id,
        a.attendance_date,
        a.recorded_at,
        a.attendance_symbol,
        e.full_name
    FROM attendance a
    LEFT JOIN employees e ON a.user_id = e.id
    ORDER BY a.attendance_date DESC";

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