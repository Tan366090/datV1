<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test database connection
try {
    require_once 'backend/src/api/config/Database.php';
    $database = \App\Config\Database::getInstance();
    $db = $database->getConnection();
    echo "Database connection successful!";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage();
}

// Test session
session_start();
$_SESSION['test'] = 'Session is working';
echo "<br>Session test: " . $_SESSION['test'];

// Test file permissions
$logFile = 'logs/test.log';
try {
    file_put_contents($logFile, "Test log entry\n", FILE_APPEND);
    echo "<br>File permissions test: Log file write successful";
} catch (Exception $e) {
    echo "<br>File permissions test failed: " . $e->getMessage();
}

// Test PHP version and extensions
echo "<br>PHP Version: " . phpversion();
echo "<br>PDO Extension: " . (extension_loaded('pdo') ? 'Enabled' : 'Disabled');
echo "<br>PDO MySQL Extension: " . (extension_loaded('pdo_mysql') ? 'Enabled' : 'Disabled');

require_once 'public/config/session.php';
require_once 'public/config/roles.php';
require_once 'public/middleware/auth.php';

// Khởi tạo session với các tham số bảo mật
ini_set('session.cookie_path', '/QLNhanSu_version1/');
ini_set('session.cookie_domain', '');
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'Lax');

// Kiểm tra nếu đã đăng nhập
if (isset($_SESSION['user_id']) && isset($_SESSION['username']) && isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    $roles = require 'public/config/roles.php';
    
    if (isset($roles[$role])) {
        // Chuyển hướng đến dashboard tương ứng với role
        header("Location: " . $roles[$role]['dashboard']);
        exit();
    }
}

// Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
header("Location: /QLNhanSu_version1/public/login_new.html");
exit();
?> 