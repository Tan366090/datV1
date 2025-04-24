<?php
session_start();
require_once __DIR__ . '/../backend/src/api/auth/SessionHelper.php';
require_once __DIR__ . '/../backend/src/api/models/User.php';

// Khởi tạo session helper
SessionHelper::init();

// Kiểm tra phương thức POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        die('Vui lòng nhập đầy đủ thông tin');
    }
    
    try {
        // Tạo instance của User model
        $userModel = new User();
        
        // Tìm user theo username
        $user = $userModel->findByUsername($username);
        
        if (!$user) {
            die('Tài khoản không tồn tại');
        }
        
        // Kiểm tra mật khẩu
        $isValid = false;
        
        // Kiểm tra nếu là bcrypt hash
        if (password_get_info($user['password_hash'])['algo'] !== null) {
            $isValid = password_verify($password, $user['password_hash']);
        } 
        // Kiểm tra nếu là MD5 hash
        else if (strlen($user['password_hash']) === 32) {
            $isValid = (md5($password) === $user['password_hash']);
        }
        // Kiểm tra plain text (cho admin mặc định)
        else if ($user['password_hash'] === 'admin123' && $username === 'admin') {
            $isValid = ($password === 'admin123');
        }
        
        if (!$isValid) {
            die('Mật khẩu không chính xác');
        }
        
        // Lưu thông tin user vào session
        SessionHelper::setUser([
            'id' => $user['user_id'],
            'username' => $user['username'],
            'role' => $user['role_id'],
            'email' => $user['email'] ?? null
        ]);
        
        // Chuyển hướng theo role
        switch ($user['role_id']) {
            case 'admin':
                header("Location: /QLNhanSu_version1/public/admin/dashboard.html");
                break;
            case 'manager':
                header("Location: /QLNhanSu_version1/public/manager/dashboard.html");
                break;
            case 'employee':
                header("Location: /QLNhanSu_version1/public/employee/dashboard.html");
                break;
            case 'hr':
                header("Location: /QLNhanSu_version1/public/hr/dashboard.html");
                break;
            default:
                die('Không có quyền truy cập');
        }
        
    } catch (Exception $e) {
        die('Lỗi: ' . $e->getMessage());
    }
} else {
    header("Location: test_login.php");
}
?> 