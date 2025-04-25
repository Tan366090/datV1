<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Get department stats
    $query = "SELECT 
                d.department_id,
                d.department_name,
                COUNT(e.id) as employee_count,
                COUNT(CASE WHEN e.status = 'active' THEN 1 END) as active_employees,
                COUNT(CASE WHEN e.status = 'inactive' THEN 1 END) as inactive_employees
            FROM departments d
            LEFT JOIN employees e ON d.department_id = e.department_id
            GROUP BY d.department_id
            ORDER BY d.department_name";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $departmentStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get position stats
    $query = "SELECT 
                p.position_id,
                p.position_name,
                COUNT(e.id) as employee_count
            FROM positions p
            LEFT JOIN employees e ON p.position_id = e.position_id
            GROUP BY p.position_id
            ORDER BY p.position_name";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $positionStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total stats
    $query = "SELECT 
                COUNT(*) as total_employees,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active_employees,
                COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive_employees,
                COUNT(DISTINCT department_id) as total_departments,
                COUNT(DISTINCT position_id) as total_positions
            FROM employees";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $totalStats = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'departments' => $departmentStats,
            'positions' => $positionStats,
            'totals' => $totalStats
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 