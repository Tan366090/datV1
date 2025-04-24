<?php
// Bật hiển thị lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log function
function logError($message) {
    error_log("[Check Admin Login Debug] " . $message);
}

try {
    logError("Starting admin login check");
    
    require_once __DIR__ . '/config/Database.php';
    logError("Database.php loaded successfully");
    
    $db = Database::getInstance();
    $conn = $db->getConnection();
    logError("Database connection established");
    
    // Lấy thông tin tài khoản admin
    $query = "SELECT user_id, username, password_hash FROM users WHERE username = 'admin'";
    $stmt = $conn->query($query);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admin) {
        echo "Không tìm thấy tài khoản admin trong database!<br>";
        exit;
    }
    
    echo "Thông tin tài khoản admin:<br>";
    echo "Username: " . $admin['username'] . "<br>";
    echo "Password Hash: " . $admin['password_hash'] . "<br>";
    
    // Kiểm tra mật khẩu
    $testPassword = 'admin123';
    $isValid = password_verify($testPassword, $admin['password_hash']);
    
    echo "<br>Kiểm tra mật khẩu:<br>";
    echo "Mật khẩu thử: " . $testPassword . "<br>";
    echo "Kết quả kiểm tra: " . ($isValid ? "Khớp" : "Không khớp") . "<br>";
    
    // Kiểm tra cấu trúc bảng users
    $query = "DESCRIBE users";
    $stmt = $conn->query($query);
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<br>Cấu trúc bảng users:<br>";
    print_r($columns);
    
} catch (Exception $e) {
    logError("Exception: " . $e->getMessage());
    echo "Lỗi: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
    echo "Trace: " . $e->getTraceAsString();
} 