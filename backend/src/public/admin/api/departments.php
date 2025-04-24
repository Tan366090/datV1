<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "SELECT 
        d.id,
        d.name,
        d.description,
        d.manager_id,
        e.full_name as manager_name,
        COUNT(emp.id) as employee_count
    FROM departments d
    LEFT JOIN employees e ON d.manager_id = e.id
    LEFT JOIN employees emp ON emp.department_id = d.id
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