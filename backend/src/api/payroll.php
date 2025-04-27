<?php
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../config/Database.php';
    $db = Database::getInstance()->getConnection();

    $stmt = $db->query("
        SELECT p.*, e.name as employee_name 
        FROM payroll p 
        LEFT JOIN employees e ON p.employee_id = e.id
    ");
    $payroll = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $payroll
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 