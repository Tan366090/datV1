<?php
// Bật hiển thị lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kiểm tra và include file database
$db_file = __DIR__ . '/../backend/src/config/database.php';
if (!file_exists($db_file)) {
    die("File không tồn tại: " . $db_file);
}
require_once $db_file;

class LoginRedirectTest {
    private $db;
    private $testUsers = [
        [
            'username' => 'admin',
            'password' => 'admin123',
            'role' => 'admin'
        ],
        [
            'username' => 'manager',
            'password' => 'manager123',
            'role' => 'manager'
        ],
        [
            'username' => 'employee',
            'password' => 'employee123',
            'role' => 'employee'
        ],
        [
            'username' => 'hr',
            'password' => 'hr123',
            'role' => 'hr'
        ]
    ];

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function runTest() {
        try {
            echo "<h2>Kiểm tra đăng nhập và chuyển hướng</h2>";
            
            foreach ($this->testUsers as $testUser) {
                echo "<h3>Testing user: {$testUser['username']}</h3>";
                
                // 1. Đăng nhập
                echo "<p>1. Đang đăng nhập...</p>";
                $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->execute([':username' => $testUser['username']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user) {
                    throw new Exception("Không tìm thấy tài khoản {$testUser['username']}");
                }

                // Kiểm tra mật khẩu
                if (!password_verify($testUser['password'], $user['password_hash'])) {
                    throw new Exception("Mật khẩu không đúng cho tài khoản {$testUser['username']}");
                }

                // Khởi tạo session
                session_start();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role_id'];

                // 2. Kiểm tra session
                echo "<p>2. Kiểm tra session...</p>";
                if (!isset($_SESSION['user_id'])) {
                    throw new Exception("Session không hợp lệ");
                }
                echo "<p style='color: green;'>✓ Session hợp lệ</p>";

                // 3. Chuyển hướng đến dashboard
                echo "<p>3. Chuyển hướng đến dashboard...</p>";
                
                // Xác định dashboard dựa trên role
                $dashboardUrl = $this->getDashboardUrl($user['role_id']);
                
                echo "<p style='color: green;'>✓ Chuyển hướng thành công đến: {$dashboardUrl}</p>";
                echo "<p>Bạn sẽ được chuyển hướng trong 3 giây...</p>";
                
                // Hiển thị nút để chuyển hướng thủ công
                echo "<a href='{$dashboardUrl}' style='display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px;'>Chuyển đến Dashboard</a>";
                
                // Chuyển hướng tự động sau 3 giây
                echo "<script>
                    setTimeout(function() {
                        window.location.href = '{$dashboardUrl}';
                    }, 3000);
                </script>";

                // Đăng xuất sau khi test xong
                session_destroy();
                
                echo "<hr>";
            }

        } catch (Exception $e) {
            echo "<div style='color: red; margin-top: 20px; padding: 10px; border: 1px solid red;'>";
            echo "✗ Test thất bại: " . $e->getMessage();
            echo "</div>";
        }
    }

    private function getDashboardUrl($roleId) {
        switch ($roleId) {
            case 1: // admin
                return '/QLNhanSu_version1/public/admin/dashboard.html';
            case 2: // manager
                return '/QLNhanSu_version1/public/manager/dashboard.html';
            case 3: // employee
                return '/QLNhanSu_version1/public/employee/dashboard.html';
            case 4: // hr
                return '/QLNhanSu_version1/public/hr/dashboard.html';
            default:
                return '/QLNhanSu_version1/public/dashboard.html';
        }
    }
}

// Chạy test
$test = new LoginRedirectTest();
$test->runTest();
?> 