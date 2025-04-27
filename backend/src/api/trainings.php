<?php
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../config/Database.php';
    $db = Database::getInstance()->getConnection();

    $stmt = $db->query("
        SELECT t.*, 
               COUNT(et.employee_id) as participants_count
        FROM trainings t
        LEFT JOIN employee_trainings et ON t.id = et.training_id
        GROUP BY t.id
    ");
    $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $trainings
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 