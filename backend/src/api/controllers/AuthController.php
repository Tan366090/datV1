<?php
namespace App\Controllers;

use App\Utils\ResponseHandler;
use App\Config\Database;
use App\Middleware\AuthMiddleware;

class AuthController {
    private $db;
    private $authMiddleware;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->authMiddleware = new AuthMiddleware();
    }
    
    public function login() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            // Kiểm tra rate limit
            $this->authMiddleware->checkRateLimit($_SERVER['REMOTE_ADDR']);
            
            $conn = $this->db->getConnection();
            
            // Get user by username
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND status = 'active' LIMIT 1");
            $stmt->execute([$data['username']]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($data['password'], $user['password'])) {
                return ResponseHandler::sendUnauthorized('Invalid credentials');
            }
            
            // Generate tokens
            $token = bin2hex(random_bytes(32));
            $refreshToken = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 day'));
            $refreshExpiresAt = date('Y-m-d H:i:s', strtotime('+30 days')); // Refresh token có hiệu lực 30 ngày
            
            // Update user tokens
            $stmt = $conn->prepare("
                UPDATE users 
                SET remember_token = ?, token_expires_at = ?,
                    refresh_token = ?, refresh_token_expires_at = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $token, $expiresAt,
                $refreshToken, $refreshExpiresAt,
                $user['id']
            ]);
            
            // Remove sensitive data
            unset($user['password']);
            unset($user['remember_token']);
            unset($user['token_expires_at']);
            unset($user['refresh_token']);
            unset($user['refresh_token_expires_at']);
            
            // Set remember me cookie if requested
            if (isset($data['remember_me']) && $data['remember_me']) {
                $cookieValue = base64_encode(json_encode([
                    'user_id' => $user['id'],
                    'token' => $refreshToken
                ]));
                setcookie('remember_me', $cookieValue, strtotime('+30 days'), '/', '', true, true);
            }
            
            return ResponseHandler::sendSuccess([
                'user' => $user,
                'token' => $token,
                'expires_at' => $expiresAt,
                'refresh_token' => $refreshToken,
                'refresh_expires_at' => $refreshExpiresAt
            ]);
        } catch (\Exception $e) {
            return ResponseHandler::sendServerError($e->getMessage());
        }
    }
    
    public function refreshToken() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $refreshToken = $data['refresh_token'] ?? '';
            
            if (empty($refreshToken)) {
                return ResponseHandler::sendUnauthorized('Refresh token is required');
            }
            
            $conn = $this->db->getConnection();
            
            // Get user by refresh token
            $stmt = $conn->prepare("SELECT * FROM users WHERE refresh_token = ? AND refresh_token_expires_at > NOW() AND status = 'active' LIMIT 1");
            $stmt->execute([$refreshToken]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ResponseHandler::sendUnauthorized('Invalid or expired refresh token');
            }
            
            // Generate new tokens
            $token = bin2hex(random_bytes(32));
            $newRefreshToken = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 day'));
            $refreshExpiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            // Update tokens
            $stmt = $conn->prepare("
                UPDATE users 
                SET remember_token = ?, token_expires_at = ?,
                    refresh_token = ?, refresh_token_expires_at = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $token, $expiresAt,
                $newRefreshToken, $refreshExpiresAt,
                $user['id']
            ]);
            
            return ResponseHandler::sendSuccess([
                'token' => $token,
                'expires_at' => $expiresAt,
                'refresh_token' => $newRefreshToken,
                'refresh_expires_at' => $refreshExpiresAt
            ]);
        } catch (\Exception $e) {
            return ResponseHandler::sendServerError($e->getMessage());
        }
    }
    
    public function logout() {
        try {
            $user = $_SESSION['user'];
            $conn = $this->db->getConnection();
            
            // Clear user tokens
            $stmt = $conn->prepare("
                UPDATE users 
                SET remember_token = NULL, token_expires_at = NULL,
                    refresh_token = NULL, refresh_token_expires_at = NULL
                WHERE id = ?
            ");
            $stmt->execute([$user['id']]);
            
            // Clear session
            session_destroy();
            
            // Clear remember me cookie
            setcookie('remember_me', '', time() - 3600, '/', '', true, true);
            
            return ResponseHandler::sendSuccess([], 'Logged out successfully');
        } catch (\Exception $e) {
            return ResponseHandler::sendServerError($e->getMessage());
        }
    }
}
?> 