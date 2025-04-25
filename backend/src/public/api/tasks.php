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
                t.task_id,
                t.title,
                t.description,
                t.employee_id,
                e.employee_code,
                u.full_name as employee_name,
                t.project_id,
                p.project_name,
                t.start_date,
                t.end_date,
                t.priority,
                t.status,
                t.progress,
                t.created_by,
                uc.full_name as created_by_name,
                t.created_at,
                t.updated_at
            FROM tasks t
            LEFT JOIN employees e ON t.employee_id = e.id
            LEFT JOIN users u ON e.user_id = u.user_id
            LEFT JOIN projects p ON t.project_id = p.project_id
            LEFT JOIN employees ec ON t.created_by = ec.id
            LEFT JOIN users uc ON ec.user_id = uc.user_id
            ORDER BY t.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the data
    $formattedTasks = array_map(function($task) {
        return [
            'id' => $task['task_id'],
            'title' => $task['title'],
            'description' => $task['description'],
            'employee_id' => $task['employee_id'],
            'employee_code' => $task['employee_code'],
            'employee_name' => $task['employee_name'],
            'project_id' => $task['project_id'],
            'project_name' => $task['project_name'],
            'start_date' => $task['start_date'],
            'end_date' => $task['end_date'],
            'priority' => $task['priority'],
            'status' => $task['status'],
            'progress' => $task['progress'],
            'created_by' => $task['created_by'],
            'created_by_name' => $task['created_by_name'],
            'created_at' => $task['created_at'],
            'updated_at' => $task['updated_at']
        ];
    }, $tasks);

    echo json_encode([
        'success' => true,
        'data' => $formattedTasks
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} 