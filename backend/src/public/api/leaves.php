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
                l.leave_id,
                l.employee_id,
                e.employee_code,
                u.full_name as employee_name,
                l.leave_type,
                l.start_date,
                l.end_date,
                l.reason,
                l.status,
                l.approved_by,
                ua.full_name as approved_by_name,
                l.created_at,
                l.updated_at
            FROM leaves l
            LEFT JOIN employees e ON l.employee_id = e.id
            LEFT JOIN users u ON e.user_id = u.user_id
            LEFT JOIN employees ea ON l.approved_by = ea.id
            LEFT JOIN users ua ON ea.user_id = ua.user_id
            ORDER BY l.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    $leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the data
    $formattedLeaves = array_map(function($leave) {
        return [
            'id' => $leave['leave_id'],
            'employee_id' => $leave['employee_id'],
            'employee_code' => $leave['employee_code'],
            'employee_name' => $leave['employee_name'],
            'leave_type' => $leave['leave_type'],
            'start_date' => $leave['start_date'],
            'end_date' => $leave['end_date'],
            'reason' => $leave['reason'],
            'status' => $leave['status'],
            'approved_by' => $leave['approved_by'],
            'approved_by_name' => $leave['approved_by_name'],
            'created_at' => $leave['created_at'],
            'updated_at' => $leave['updated_at']
        ];
    }, $leaves);

    echo json_encode([
        'success' => true,
        'data' => $formattedLeaves
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} 