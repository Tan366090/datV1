<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Credentials: true');
    http_response_code(200);
    exit();
}

// Set CORS headers for all responses
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "SELECT 
                p.performance_id,
                p.employee_id,
                e.employee_code,
                u.full_name as employee_name,
                p.reviewer_id,
                ur.full_name as reviewer_name,
                p.review_period,
                p.performance_score,
                p.strengths,
                p.weaknesses,
                p.goals,
                p.review_date,
                p.next_review_date,
                p.status
            FROM performances p
            LEFT JOIN employees e ON p.employee_id = e.id
            LEFT JOIN users u ON e.user_id = u.user_id
            LEFT JOIN employees er ON p.reviewer_id = er.id
            LEFT JOIN users ur ON er.user_id = ur.user_id
            ORDER BY p.review_date DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    $performances = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $performances
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} 