<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function authenticate($username, $password) {
        try {
            // Query to check if user exists
            $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username AND is_active = 1 LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Since passwords are stored in plain text in the current database
                if ($password === $row['password_hash']) {
                    // Update last login
                    $update = "UPDATE " . $this->table_name . " 
                              SET last_login = CURRENT_TIMESTAMP,
                                  login_attempts = 0
                              WHERE user_id = :user_id";
                    $updateStmt = $this->conn->prepare($update);
                    $updateStmt->bindParam(":user_id", $row['user_id']);
                    $updateStmt->execute();

                    return [
                        'success' => true,
                        'user' => [
                            'user_id' => $row['user_id'],
                            'username' => $row['username'],
                            'role_id' => $row['role_id']
                        ]
                    ];
                }
            }

            // Increment login attempts
            $this->incrementLoginAttempts($username);

            return [
                'success' => false,
                'error' => 'Tên đăng nhập hoặc mật khẩu không đúng'
            ];

        } catch(PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Có lỗi xảy ra trong quá trình đăng nhập'
            ];
        }
    }

    private function incrementLoginAttempts($username) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET login_attempts = login_attempts + 1,
                          last_attempt = CURRENT_TIMESTAMP
                      WHERE username = :username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error incrementing login attempts: " . $e->getMessage());
        }
    }

    public function create($username, $password, $email, $role_id = 4) {
        try {
            // Insert query
            $query = "INSERT INTO " . $this->table_name . " 
                     (username, password_hash, email, role_id, created_at) 
                     VALUES 
                     (:username, :password, :email, :role_id, CURRENT_TIMESTAMP)";

            $stmt = $this->conn->prepare($query);

            // Bind values
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":role_id", $role_id);

            if($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Tạo tài khoản thành công'
                ];
            }

            return [
                'success' => false,
                'error' => 'Không thể tạo tài khoản'
            ];

        } catch(PDOException $e) {
            error_log("Create user error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Có lỗi xảy ra khi tạo tài khoản'
            ];
        }
    }
}
?> 