<?php
// Bật báo lỗi để dễ debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Đảm bảo header JSON được gửi trước
header('Content-Type: application/json; charset=utf-8');

// Kiểm tra đường dẫn
$userModelPath = __DIR__ . '/../backend/src/api/models/User.php';
if (!file_exists($userModelPath)) {
    echo json_encode([
        'error' => 'Không tìm thấy file User.php',
        'path' => $userModelPath
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra kết nối database
try {
    require_once __DIR__ . '/../backend/src/config/Database.php';
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Kiểm tra bảng users
    $query = "SELECT COUNT(*) as count FROM users";
    $stmt = $conn->query($query);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Kết nối database thành công',
        'users_count' => $result['count']
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => 'Lỗi kết nối database',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} 