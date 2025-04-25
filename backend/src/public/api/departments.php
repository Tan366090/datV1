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
                d.department_id as id,
                d.department_name as name,
                d.department_code,
                COUNT(e.id) as employee_count,
                u.full_name as manager_name,
                d.description,
                d.status
            FROM departments d
            LEFT JOIN employees e ON d.department_id = e.department_id
            LEFT JOIN users u ON d.manager_id = u.user_id
            GROUP BY d.department_id
            ORDER BY d.department_name";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the data
    $formattedDepartments = array_map(function($dept) {
        return [
            'id' => $dept['id'],
            'name' => $dept['name'],
            'code' => $dept['department_code'],
            'manager' => $dept['manager_name'],
            'employee_count' => $dept['employee_count'],
            'description' => $dept['description'],
            'status' => $dept['status']
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