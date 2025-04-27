<?php
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../config/Database.php';
    $db = Database::getInstance()->getConnection();

    $stmt = $db->query("
        SELECT l.*, e.name as employee_name 
        FROM leaves l 
        LEFT JOIN employees e ON l.employee_id = e.id
    ");
    $leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $leaves
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 