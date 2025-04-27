<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Get all payroll records
    $query = "SELECT 
                p.id,
                p.employee_id,
                e.full_name as employee_name,
                p.month,
                p.basic_salary,
                p.allowances,
                p.deductions,
                p.net_salary,
                p.status,
                p.created_at,
                p.updated_at
            FROM payroll p
            LEFT JOIN employees e ON p.employee_id = e.id
            ORDER BY p.month DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    $payrolls = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the data
    $formattedPayrolls = array_map(function($pay) {
        return [
            'id' => $pay['id'],
            'employee_id' => $pay['employee_id'],
            'employee_name' => $pay['employee_name'],
            'month' => $pay['month'],
            'basic_salary' => $pay['basic_salary'],
            'allowances' => $pay['allowances'],
            'deductions' => $pay['deductions'],
            'net_salary' => $pay['net_salary'],
            'status' => $pay['status'],
            'created_at' => $pay['created_at'],
            'updated_at' => $pay['updated_at']
        ];
    }, $payrolls);

    echo json_encode([
        'success' => true,
        'data' => $formattedPayrolls
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 