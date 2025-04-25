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

    // Format the data
    $formattedPerformances = array_map(function($perf) {
        return [
            'id' => $perf['performance_id'],
            'employee_id' => $perf['employee_id'],
            'employee_code' => $perf['employee_code'],
            'employee_name' => $perf['employee_name'],
            'reviewer_id' => $perf['reviewer_id'],
            'reviewer_name' => $perf['reviewer_name'],
            'review_period' => $perf['review_period'],
            'performance_score' => $perf['performance_score'],
            'strengths' => $perf['strengths'],
            'weaknesses' => $perf['weaknesses'],
            'goals' => $perf['goals'],
            'review_date' => $perf['review_date'],
            'next_review_date' => $perf['next_review_date'],
            'status' => $perf['status']
        ];
    }, $performances);

    echo json_encode([
        'success' => true,
        'data' => $formattedPerformances
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} 