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
                e.id,
                e.employee_code,
                u.full_name,
                d.department_name,
                p.position_name,
                s.basic_salary as salary_amount,
                e.hire_date as join_date,
                u.date_of_birth as birth_date,
                u.phone_number as phone,
                u.email,
                u.address,
                e.status
            FROM employees e
            LEFT JOIN users u ON e.user_id = u.user_id
            LEFT JOIN departments d ON e.department_id = d.department_id
            LEFT JOIN positions p ON e.position_id = p.position_id
            LEFT JOIN salaries s ON e.id = s.employee_id
            ORDER BY e.id DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the data
    $formattedEmployees = array_map(function($emp) {
        return [
            'id' => $emp['id'],
            'employee_id' => $emp['employee_code'],
            'full_name' => $emp['full_name'],
            'position' => $emp['position_name'],
            'department' => $emp['department_name'],
            'join_date' => $emp['join_date'],
            'birth_date' => $emp['birth_date'],
            'phone' => $emp['phone'],
            'email' => $emp['email'],
            'address' => $emp['address'],
            'status' => $emp['status'],
            'salary' => $emp['salary_amount']
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