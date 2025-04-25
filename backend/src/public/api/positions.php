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
                p.position_id,
                p.position_name,
                p.description,
                p.department_id,
                d.department_name,
                p.salary_grade,
                p.status,
                COUNT(e.id) as employee_count
            FROM positions p
            LEFT JOIN departments d ON p.department_id = d.department_id
            LEFT JOIN employees e ON p.position_id = e.position_id
            GROUP BY p.position_id
            ORDER BY p.position_name";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the data
    $formattedPositions = array_map(function($pos) {
        return [
            'id' => $pos['position_id'],
            'name' => $pos['position_name'],
            'description' => $pos['description'],
            'department_id' => $pos['department_id'],
            'department_name' => $pos['department_name'],
            'salary_grade' => $pos['salary_grade'],
            'status' => $pos['status'],
            'employee_count' => $pos['employee_count']
        ];
    }, $positions);

    echo json_encode([
        'success' => true,
        'data' => $formattedPositions
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} 