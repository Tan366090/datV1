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

class LoginRedirectLogTest {
    private $db;
    private $logFile = __DIR__ . '/../logs/login_redirect.log';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        // Tạo thư mục logs nếu chưa tồn tại
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
    }

    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        echo "<p>$message</p>";
    }

    public function runTest() {
        try {
            // Kiểm tra nếu đã đăng nhập
            session_start();
            if (isset($_SESSION['user_id'])) {
                $this->redirectToDashboard($_SESSION['role']);
                return;
            }

            echo "<h2>Kiểm tra quá trình chuyển hướng khi đăng nhập</h2>";
            $this->log("Bắt đầu test chuyển hướng đăng nhập");
            
            // Lấy thông tin đăng nhập từ form
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                // Hiển thị form đăng nhập nếu chưa có thông tin
                $this->showLoginForm();
                return;
            }
            
            // 1. Đăng nhập
            $this->log("1. Đang đăng nhập với tài khoản: $username");
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception("Không tìm thấy tài khoản $username");
            }

            // Kiểm tra mật khẩu
            if (!password_verify($password, $user['password_hash'])) {
                throw new Exception("Mật khẩu không đúng cho tài khoản $username");
            }

            $this->log("✓ Đăng nhập thành công với tài khoản $username");

            // 2. Khởi tạo session
            $this->log("2. Khởi tạo session...");
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role_id'];
            
            // Cấu hình session
            ini_set('session.cookie_lifetime', 86400); // 24 giờ
            ini_set('session.gc_maxlifetime', 86400); // 24 giờ
            session_regenerate_id(true);

            // 3. Kiểm tra session
            $this->log("3. Kiểm tra session...");
            if (!isset($_SESSION['user_id'])) {
                throw new Exception("Session không hợp lệ");
            }
            $this->log("✓ Session hợp lệ");

            // 4. Chuyển hướng đến dashboard
            $this->redirectToDashboard($user['role_id']);

        } catch (Exception $e) {
            $this->log("✗ Test thất bại: " . $e->getMessage());
            echo "<div style='color: red; margin-top: 20px; padding: 10px; border: 1px solid red;'>";
            echo "✗ Test thất bại: " . $e->getMessage();
            echo "</div>";
            $this->showLoginForm();
        }
    }

    private function redirectToDashboard($roleId) {
        $dashboardUrl = $this->getDashboardUrl($roleId);
        $this->log("✓ Chuyển hướng đến: $dashboardUrl");
        
        // Sử dụng JavaScript để chuyển hướng và ngăn chặn quay lại
        echo "<script>
            window.location.replace('$dashboardUrl');
        </script>";
        exit;
    }

    private function showLoginForm() {
        echo '
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-center">Đăng nhập</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Tên đăng nhập</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Đăng nhập</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
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
$test = new LoginRedirectLogTest();
$test->runTest();
?> 