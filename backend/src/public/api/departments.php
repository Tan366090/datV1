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
                d.id as department_id,
                d.name,
                d.description,
                d.manager_id,
                d.status,
                COUNT(e.id) as employee_count,
                m.full_name as manager_name
            FROM departments d
            LEFT JOIN employees e ON d.id = e.department_id
            LEFT JOIN user_profiles m ON d.manager_id = m.id
            GROUP BY d.id
            ORDER BY d.name";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $departments
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 