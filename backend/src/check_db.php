<?php
require_once __DIR__ . '/config/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Kiểm tra bảng users
    $stmt = $conn->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() === 0) {
        die("Bảng users không tồn tại");
    }
    
    // Kiểm tra cấu trúc bảng users
    $stmt = $conn->query("DESCRIBE users");
    echo "Cấu trúc bảng users:<br>";
    while ($row = $stmt->fetch()) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
    
    // Kiểm tra dữ liệu trong bảng users
    $stmt = $conn->query("SELECT * FROM users");
    echo "<br>Dữ liệu trong bảng users:<br>";
    while ($row = $stmt->fetch()) {
        echo "ID: " . $row['user_id'] . ", Username: " . $row['username'] . ", Email: " . $row['email'] . "<br>";
    }
    
    // Kiểm tra bảng roles
    $stmt = $conn->query("SHOW TABLES LIKE 'roles'");
    if ($stmt->rowCount() === 0) {
        die("<br>Bảng roles không tồn tại");
    }
    
    // Kiểm tra dữ liệu trong bảng roles
    $stmt = $conn->query("SELECT * FROM roles");
    echo "<br>Dữ liệu trong bảng roles:<br>";
    while ($row = $stmt->fetch()) {
        echo "ID: " . $row['role_id'] . ", Name: " . $row['role_name'] . "<br>";
    }
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
} 