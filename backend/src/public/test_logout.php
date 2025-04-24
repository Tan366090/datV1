<?php
session_start();
require_once __DIR__ . '/../backend/src/api/auth/SessionHelper.php';

// Khởi tạo session helper
SessionHelper::init();

// Hủy session
SessionHelper::destroy();

// Chuyển hướng về trang đăng nhập
header("Location: test_login.php");
exit;
?> 