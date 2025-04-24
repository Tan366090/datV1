<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once '../../config/database.php';
    
    // Get database connection using singleton pattern
    $db = Database::getInstance();
    $conn = $db->getConnection();

    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;
    
    // Get filter parameters
    $type = isset($_GET['type']) ? $_GET['type'] : null;
    $date = isset($_GET['date']) ? $_GET['date'] : null;
    $search = isset($_GET['search']) ? $_GET['search'] : null;
    
    // Build query with proper join to user_profiles
    $query = "SELECT a.*, COALESCE(up.full_name, u.username) as userName 
              FROM activities a 
              LEFT JOIN users u ON a.user_id = u.user_id 
              LEFT JOIN user_profiles up ON u.user_id = up.user_id 
              WHERE 1=1";
    
    $params = array();
    
    if ($type && $type !== 'all') {
        $query .= " AND a.type = ?";
        $params[] = $type;
    }
    
    if ($date) {
        $query .= " AND DATE(a.created_at) = ?";
        $params[] = $date;
    }
    
    if ($search) {
        $query .= " AND (up.full_name LIKE ? OR u.username LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    // Add sorting
    $query .= " ORDER BY a.created_at DESC";
    
    // Add pagination
    $query .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    error_log("Query: " . $query);
    error_log("Params: " . print_r($params, true));
    
    // Prepare and execute query
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Prepare failed: " . print_r($conn->errorInfo(), true));
        throw new Exception("Prepare failed: " . print_r($conn->errorInfo(), true));
    }
    
    // Bind parameters
    if ($params) {
        foreach ($params as $i => $param) {
            $paramType = is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($i + 1, $param, $paramType);
        }
    }
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . print_r($stmt->errorInfo(), true));
        throw new Exception("Execute failed: " . print_r($stmt->errorInfo(), true));
    }
    
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Fetched activities: " . print_r($activities, true));
    
    // Get total count for pagination
    $countQuery = "SELECT COUNT(*) as total 
                  FROM activities a 
                  LEFT JOIN users u ON a.user_id = u.user_id 
                  LEFT JOIN user_profiles up ON u.user_id = up.user_id 
                  WHERE 1=1";
    $countParams = array();
    
    if ($type && $type !== 'all') {
        $countQuery .= " AND a.type = ?";
        $countParams[] = $type;
    }
    if ($date) {
        $countQuery .= " AND DATE(a.created_at) = ?";
        $countParams[] = $date;
    }
    if ($search) {
        $countQuery .= " AND (up.full_name LIKE ? OR u.username LIKE ?)";
        $countParams[] = "%$search%";
        $countParams[] = "%$search%";
    }
    
    error_log("Count Query: " . $countQuery);
    error_log("Count Params: " . print_r($countParams, true));
    
    $totalStmt = $conn->prepare($countQuery);
    if (!$totalStmt) {
        error_log("Prepare count failed: " . print_r($conn->errorInfo(), true));
        throw new Exception("Prepare count failed: " . print_r($conn->errorInfo(), true));
    }
    
    // Bind parameters for count query
    if ($countParams) {
        foreach ($countParams as $i => $param) {
            $paramType = is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $totalStmt->bindValue($i + 1, $param, $paramType);
        }
    }
    
    if (!$totalStmt->execute()) {
        error_log("Execute count failed: " . print_r($totalStmt->errorInfo(), true));
        throw new Exception("Execute count failed: " . print_r($totalStmt->errorInfo(), true));
    }
    
    $total = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
    error_log("Total count: " . $total);
    
    // Format activities for display
    $formattedActivities = array_map(function($activity) {
        return [
            'id' => $activity['id'],
            'userName' => $activity['userName'],
            'type' => $activity['type'],
            'description' => $activity['description'] ?? '',
            'userAgent' => $activity['user_agent'] ?? '',
            'ipAddress' => $activity['ip_address'] ?? '',
            'status' => $activity['status'] ?? 'active',
            'timestamp' => $activity['created_at']
        ];
    }, $activities);

    echo json_encode([
        'success' => true,
        'activities' => $formattedActivities,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => ceil($total / $limit),
            'total_items' => $total,
            'limit' => $limit
        ]
    ]);

} catch (PDOException $e) {
    error_log("Database error in activities.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("General error in activities.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred: ' . $e->getMessage()
    ]);
} 