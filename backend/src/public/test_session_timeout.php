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

class SessionTimeoutTest {
    private $db;
    private $testUser = [
        'username' => 'admin',
        'password' => 'admin123'
    ];

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function runTest() {
        try {
            echo "<h2>Kiểm tra duy trì session trong 5 giây</h2>";
            
            // 1. Đăng nhập
            echo "<p>1. Đang đăng nhập với tài khoản admin...</p>";
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute([':username' => $this->testUser['username']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception("Không tìm thấy tài khoản test");
            }

            // Kiểm tra mật khẩu
            if (!password_verify($this->testUser['password'], $user['password_hash'])) {
                throw new Exception("Mật khẩu không đúng");
            }

            // Khởi tạo session
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role_id'];

            // 2. Kiểm tra session ngay sau khi đăng nhập
            echo "<p>2. Kiểm tra session ngay sau khi đăng nhập...</p>";
            if (!isset($_SESSION['user_id'])) {
                throw new Exception("Session không hợp lệ ngay sau khi đăng nhập");
            }
            echo "<p style='color: green;'>✓ Session hợp lệ</p>";

            // 3. Đợi 5 giây
            echo "<p>3. Đang đợi 5 giây...</p>";
            for ($i = 5; $i > 0; $i--) {
                echo "<p>Đếm ngược: $i giây</p>";
                sleep(1);
                flush();
                ob_flush();
            }

            // 4. Kiểm tra session sau 5 giây
            echo "<p>4. Kiểm tra session sau 5 giây...</p>";
            if (!isset($_SESSION['user_id'])) {
                throw new Exception("Session đã hết hạn sau 5 giây");
            }
            echo "<p style='color: green;'>✓ Session vẫn hợp lệ sau 5 giây</p>";

            // 5. Đăng xuất
            echo "<p>5. Đang đăng xuất...</p>";
            session_destroy();
            echo "<p style='color: green;'>✓ Đăng xuất thành công</p>";

            echo "<div style='color: green; margin-top: 20px; padding: 10px; border: 1px solid green;'>";
            echo "✓ Test thành công: Session được duy trì trong 5 giây";
            echo "</div>";

        } catch (Exception $e) {
            echo "<div style='color: red; margin-top: 20px; padding: 10px; border: 1px solid red;'>";
            echo "✗ Test thất bại: " . $e->getMessage();
            echo "</div>";
        }
    }
}

// Chạy test
$test = new SessionTimeoutTest();
$test->runTest();
?> 