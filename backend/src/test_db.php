<?php
// Bật hiển thị lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kiểm tra xem PDO đã được cài đặt chưa
if (!extension_loaded('pdo')) {
    die('PDO extension is not loaded');
}

if (!extension_loaded('pdo_mysql')) {
    die('PDO MySQL extension is not loaded');
}

try {
    require_once __DIR__ . '/config/Database.php';
    
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Test query đơn giản
    $stmt = $conn->query("SELECT 1");
    $result = $stmt->fetch();
    
    if ($result) {
        echo "Kết nối database thành công!";
    } else {
        echo "Có lỗi xảy ra khi test kết nối";
    }
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
    echo "Trace: " . $e->getTraceAsString();
} 