<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Tạm thời bỏ qua xác thực
// session_start();
// if (!isset($_SESSION['user_id'])) {
//     http_response_code(401);
//     echo json_encode([
//         'success' => false,
//         'message' => 'Unauthorized access'
//     ]);
//     exit();
// }

require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "SELECT 
                e.id,
                e.employee_code,
                e.full_name,
                d.name as department_name,
                p.name as position_name,
                e.salary,
                e.join_date,
                e.birth_date,
                e.phone,
                e.email,
                e.address,
                e.status,
                e.created_at
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN positions p ON e.position_id = p.id
            ORDER BY e.id DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the data
    $formattedEmployees = array_map(function($emp) {
        return [
            'id' => $emp['id'],
            'employee_code' => $emp['employee_code'],
            'full_name' => $emp['full_name'],
            'position' => $emp['position_name'],
            'department' => $emp['department_name'],
            'join_date' => $emp['join_date'],
            'birth_date' => $emp['birth_date'],
            'phone' => $emp['phone'],
            'email' => $emp['email'],
            'address' => $emp['address'],
            'status' => $emp['status'],
            'salary' => $emp['salary'],
            'created_at' => $emp['created_at']
        ];
    }, $employees);

    echo json_encode([
        'success' => true,
        'data' => $formattedEmployees
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 