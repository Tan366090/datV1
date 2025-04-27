<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/database.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check if it's a GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Get sentiment data from the database
        $query = "SELECT 
                    e.employee_id,
                    e.full_name,
                    p.position_name,
                    d.department_name,
                    a.sentiment_score,
                    a.analysis_date
                FROM employees e
                JOIN positions p ON e.position_id = p.position_id
                JOIN departments d ON e.department_id = d.department_id
                LEFT JOIN employee_sentiment a ON e.employee_id = a.employee_id
                ORDER BY a.analysis_date DESC
                LIMIT 10";

        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format the response
        $response = [
            'status' => 'success',
            'data' => $result
        ];

        echo json_encode($response);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
} 