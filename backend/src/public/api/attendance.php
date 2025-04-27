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
                a.id,
                a.employee_id,
                e.full_name,
                DATE(a.check_in) as date,
                TIME(a.check_in) as time_in,
                TIME(a.check_out) as time_out,
                a.status
            FROM attendance a
            LEFT JOIN employees e ON a.employee_id = e.id
            ORDER BY a.check_in DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the data
    $formattedAttendance = array_map(function($record) {
        return [
            'id' => $record['id'],
            'employee_id' => $record['employee_id'],
            'employee_name' => $record['full_name'],
            'date' => $record['date'],
            'time_in' => $record['time_in'],
            'time_out' => $record['time_out'],
            'status' => $record['status']
        ];
    }, $attendance);

    echo json_encode([
        'success' => true,
        'data' => $formattedAttendance
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 