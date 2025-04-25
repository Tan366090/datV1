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
                t.training_id,
                t.training_name,
                t.description,
                t.start_date,
                t.end_date,
                t.location,
                t.trainer,
                t.cost,
                t.status,
                COUNT(et.employee_id) as participant_count
            FROM trainings t
            LEFT JOIN employee_trainings et ON t.training_id = et.training_id
            GROUP BY t.training_id
            ORDER BY t.start_date DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the data
    $formattedTrainings = array_map(function($training) {
        return [
            'id' => $training['training_id'],
            'name' => $training['training_name'],
            'description' => $training['description'],
            'start_date' => $training['start_date'],
            'end_date' => $training['end_date'],
            'location' => $training['location'],
            'trainer' => $training['trainer'],
            'cost' => $training['cost'],
            'status' => $training['status'],
            'participant_count' => $training['participant_count']
        ];
    }, $trainings);

    echo json_encode([
        'success' => true,
        'data' => $formattedTrainings
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} 