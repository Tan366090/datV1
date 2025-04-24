<?php
require_once __DIR__ . '/../backend/src/api/auth/SessionHelper.php';
require_once __DIR__ . '/../backend/src/api/models/User.php';

class AutoTestLogin {
    private $testCases = [
        [
            'username' => 'admin',
            'password' => 'admin123',
            'expected_role' => 'admin',
            'description' => 'Test đăng nhập admin'
        ],
        [
            'username' => 'manager',
            'password' => 'manager123',
            'expected_role' => 'manager',
            'description' => 'Test đăng nhập manager'
        ],
        [
            'username' => 'employee',
            'password' => 'employee123',
            'expected_role' => 'employee',
            'description' => 'Test đăng nhập employee'
        ],
        [
            'username' => 'hr',
            'password' => 'hr123',
            'expected_role' => 'hr',
            'description' => 'Test đăng nhập hr'
        ],
        [
            'username' => 'invalid',
            'password' => 'invalid',
            'expected_role' => null,
            'description' => 'Test đăng nhập với tài khoản không tồn tại'
        ]
    ];

    public function runTests() {
        echo "<h2>Bắt đầu kiểm tra đăng nhập</h2>";
        echo "<style>
            .test-result { margin: 10px 0; padding: 10px; border-radius: 5px; }
            .success { background-color: #d4edda; color: #155724; }
            .failure { background-color: #f8d7da; color: #721c24; }
            .info { background-color: #cce5ff; color: #004085; }
        </style>";

        foreach ($this->testCases as $test) {
            $this->runTest($test);
        }
    }

    private function runTest($test) {
        echo "<div class='test-result info'>";
        echo "<strong>Test case:</strong> " . $test['description'] . "<br>";
        echo "<strong>Username:</strong> " . $test['username'] . "<br>";
        
        try {
            // Tạo instance của User model
            $userModel = new User();
            
            // Tìm user theo username
            $user = $userModel->getUserByUsername($test['username']);
            
            if (!$user && $test['expected_role'] === null) {
                echo "<div class='test-result success'>✓ Test passed: Tài khoản không tồn tại như mong đợi</div>";
                return;
            }
            
            if (!$user) {
                throw new Exception("Tài khoản không tồn tại");
            }

            // Kiểm tra trạng thái tài khoản
            if (!$user['is_active']) {
                throw new Exception("Tài khoản đã bị vô hiệu hóa");
            }
            
            // Kiểm tra mật khẩu
            if (!password_verify($test['password'], $user['password_hash'])) {
                throw new Exception("Mật khẩu không chính xác");
            }
            
            // Kiểm tra role
            if ($user['role_name'] !== $test['expected_role']) {
                throw new Exception("Role không khớp. Expected: " . $test['expected_role'] . ", Got: " . $user['role_name']);
            }
            
            echo "<div class='test-result success'>✓ Test passed: Đăng nhập thành công với role " . $user['role_name'] . "</div>";
            
        } catch (Exception $e) {
            echo "<div class='test-result failure'>✗ Test failed: " . $e->getMessage() . "</div>";
        }
        
        echo "</div>";
    }
}

// Chạy tests
$tester = new AutoTestLogin();
$tester->runTests();
?> 