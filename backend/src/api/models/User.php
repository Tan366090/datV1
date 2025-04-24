<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../auth/SessionHelper.php';

class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $username;
    public $password;
    public $role;
    public $email;
    public $last_login;
    public $status;

    public function __construct() {
        try {
            $database = Database::getInstance();
            $this->conn = $database->getConnection();
        } catch (Exception $e) {
            error_log("Database connection error in User class: " . $e->getMessage());
            throw new Exception("Không thể kết nối đến cơ sở dữ liệu");
        }
    }

    public function authenticate($username, $password) {
        error_log("[User Authentication] Starting authentication for username: " . $username);
        
        try {
            $query = "SELECT u.*, r.name as role_name 
                     FROM users u 
                     LEFT JOIN roles r ON u.role_id = r.id 
                     WHERE u.username = :username AND u.is_active = 1";
            
            error_log("[User Authentication] Query: " . $query);
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                error_log("[User Authentication] User not found: " . $username);
                return ['success' => false, 'error' => 'Tên đăng nhập hoặc mật khẩu không đúng'];
            }
            
            error_log("[User Authentication] User found: " . print_r($user, true));
            
            // Temporary: Check plain text password
            if ($password !== $user['password_hash']) {
                error_log("[User Authentication] Password mismatch for user: " . $username);
                return ['success' => false, 'error' => 'Tên đăng nhập hoặc mật khẩu không đúng'];
            }
            
            error_log("[User Authentication] Authentication successful for user: " . $username);
            
            // Update last login
            $this->updateLastLogin($user['user_id']);
            
            return ['success' => true, 'user' => $user];
            
        } catch (Exception $e) {
            error_log("[User Authentication] Exception: " . $e->getMessage());
            return ['success' => false, 'error' => 'Có lỗi xảy ra khi xác thực'];
        }
    }

    private function hashPassword($password, $salt) {
        return hash('sha256', $password . $salt);
    }

    private function incrementLoginAttempts($userId) {
        $query = "UPDATE " . $this->table . " 
                 SET login_attempts = login_attempts + 1,
                     last_login_attempt = NOW()
                 WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    }

    private function resetLoginAttempts($userId) {
        $query = "UPDATE " . $this->table . " 
                 SET login_attempts = 0,
                     last_login_attempt = NULL
                 WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    }

    private function updateLastLogin($userId) {
        $query = "UPDATE " . $this->table . " 
                 SET last_login = NOW()
                 WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    }

    public function getUserById($id) {
        try {
            $query = "SELECT u.*, r.role_name 
                     FROM " . $this->table . " u
                     JOIN roles r ON u.role_id = r.role_id
                     WHERE u.user_id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting user by ID: " . $e->getMessage());
            return false;
        }
    }

    public function updatePassword($userId, $newPassword) {
        try {
            $salt = bin2hex(random_bytes(32));
            $hashedPassword = $this->hashPassword($newPassword, $salt);
            
            $query = "UPDATE " . $this->table . " 
                     SET password_hash = :password,
                         password_salt = :salt
                     WHERE user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':salt', $salt);
            $stmt->bindParam(':user_id', $userId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating password: " . $e->getMessage());
            return false;
        }
    }

    public function logout() {
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return $this->getUserById($_SESSION['user_id']);
        }
        return null;
    }
} 
