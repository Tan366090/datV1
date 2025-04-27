<?php
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../config/Database.php';
    $db = Database::getInstance()->getConnection();

    $stmt = $db->query("
        SELECT t.*, e.name as assignee_name 
        FROM tasks t 
        LEFT JOIN employees e ON t.assignee_id = e.id
    ");
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $tasks
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 