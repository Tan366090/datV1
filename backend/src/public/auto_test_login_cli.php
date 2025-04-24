<?php
require_once __DIR__ . '/../backend/src/api/auth/SessionHelper.php';
require_once __DIR__ . '/../backend/src/api/models/User.php';

class AutoTestLoginCLI {
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
        echo "=== Bắt đầu kiểm tra đăng nhập ===\n\n";
        
        $totalTests = count($this->testCases);
        $passedTests = 0;
        $failedTests = 0;

        foreach ($this->testCases as $test) {
            $result = $this->runTest($test);
            if ($result) {
                $passedTests++;
            } else {
                $failedTests++;
            }
        }

        echo "\n=== Tổng kết ===\n";
        echo "Tổng số test: $totalTests\n";
        echo "Test thành công: $passedTests\n";
        echo "Test thất bại: $failedTests\n";
        echo "Tỷ lệ thành công: " . round(($passedTests/$totalTests)*100, 2) . "%\n";
    }

    private function runTest($test) {
        echo "Test case: " . $test['description'] . "\n";
        echo "Username: " . $test['username'] . "\n";
        
        try {
            $userModel = new User();
            $user = $userModel->getUserByUsername($test['username']);
            
            if (!$user && $test['expected_role'] === null) {
                echo "✓ Test passed: Tài khoản không tồn tại như mong đợi\n\n";
                return true;
            }
            
            if (!$user) {
                throw new Exception("Tài khoản không tồn tại");
            }

            if (!$user['is_active']) {
                throw new Exception("Tài khoản đã bị vô hiệu hóa");
            }
            
            if (!password_verify($test['password'], $user['password_hash'])) {
                throw new Exception("Mật khẩu không chính xác");
            }
            
            if ($user['role_name'] !== $test['expected_role']) {
                throw new Exception("Role không khớp. Expected: " . $test['expected_role'] . ", Got: " . $user['role_name']);
            }
            
            echo "✓ Test passed: Đăng nhập thành công với role " . $user['role_name'] . "\n\n";
            return true;
            
        } catch (Exception $e) {
            echo "✗ Test failed: " . $e->getMessage() . "\n\n";
            return false;
        }
    }
}

// Chạy tests
$tester = new AutoTestLoginCLI();
$tester->runTests();
?> 