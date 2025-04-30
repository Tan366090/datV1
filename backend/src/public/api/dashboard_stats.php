<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Include database configuration
    require_once '../../config/database.php';
    
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();

    // Get total employees count
    $query = "SELECT COUNT(*) as total FROM employees e 
              JOIN users u ON e.user_id = u.user_id 
              WHERE e.status = 'active' AND u.role_id != 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $totalEmployees = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get today's date
    $today = date('Y-m-d');

    // Get present employees count for today
    $query = "SELECT COUNT(DISTINCT employee_id) as present FROM attendance 
              WHERE DATE(attendance_date) = :today AND attendance_symbol = 'P'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':today', $today);
    $stmt->execute();
    $presentToday = $stmt->fetch(PDO::FETCH_ASSOC)['present'];

    // Get absent employees count for today
    $query = "SELECT COUNT(DISTINCT employee_id) as absent FROM attendance 
              WHERE DATE(attendance_date) = :today AND attendance_symbol = 'A'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':today', $today);
    $stmt->execute();
    $absentToday = $stmt->fetch(PDO::FETCH_ASSOC)['absent'];

    // Get on-time percentage (employees who arrived on time today)
    $query = "SELECT 
                COALESCE(
                    (COUNT(CASE WHEN TIME(check_in_time) <= '09:00:00' THEN 1 END) * 100.0 / 
                    NULLIF(COUNT(*), 0)),
                    0
                ) as on_time_percentage 
              FROM attendance 
              WHERE DATE(attendance_date) = :today AND attendance_symbol = 'P'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':today', $today);
    $stmt->execute();
    $onTimePercentage = round($stmt->fetch(PDO::FETCH_ASSOC)['on_time_percentage'], 1);

    // Prepare response
    $response = [
        'success' => true,
        'data' => [
            'totalEmployees' => (int)$totalEmployees,
            'presentToday' => (int)$presentToday,
            'absentToday' => (int)$absentToday,
            'onTimePercentage' => (float)$onTimePercentage
        ]
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("General Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?> 