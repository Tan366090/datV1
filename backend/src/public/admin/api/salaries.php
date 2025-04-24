<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "SELECT 
        s.id,
        s.user_id,
        e.full_name,
        s.base_salary,
        s.bonus,
        s.allowance,
        s.tax,
        s.insurance,
        s.total_amount,
        s.payment_date,
        s.status
    FROM payroll s
    LEFT JOIN employees e ON s.user_id = e.id
    ORDER BY s.payment_date DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $salaries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $salaries
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 