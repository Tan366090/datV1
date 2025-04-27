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
                d.id,
                d.name,
                COUNT(e.id) as employee_count
            FROM departments d
            LEFT JOIN employees e ON d.id = e.department_id
            GROUP BY d.id
            ORDER BY d.name";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the data
    $formattedDepartments = array_map(function($dept) {
        return [
            'id' => $dept['id'],
            'name' => $dept['name'],
            'employee_count' => $dept['employee_count']
        ];
    }, $departments);

    echo json_encode([
        'success' => true,
        'data' => $formattedDepartments
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 