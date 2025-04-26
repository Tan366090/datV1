<?php
// Middleware functions for authentication
require_once __DIR__ . '/../utils/jwt.php';

class Auth {
    private static $user = null;
    
    /**
     * Authenticate user using JWT token
     * @return bool Whether authentication was successful
     */
    public static function authenticate() {
        $token = getBearerToken();
        if (!$token) {
            return false;
        }
        
        $payload = verifyJWT($token);
        if (!$payload) {
            return false;
        }
        
        self::$user = $payload;
        return true;
    }
    
    /**
     * Get current authenticated user
     * @return array|null User data or null if not authenticated
     */
    public static function getUser() {
        return self::$user;
    }
    
    /**
     * Check if user is authenticated
     * @return bool
     */
    public static function isAuthenticated() {
        return self::$user !== null;
    }
    
    /**
     * Check if user has a specific role
     * @param string $role Role to check
     * @return bool
     */
    public static function hasRole($role) {
        if (!self::isAuthenticated()) {
            return false;
        }
        
        return isset(self::$user['role']) && self::$user['role'] === $role;
    }
    
    /**
     * Check if user has any of the specified roles
     * @param array $roles Roles to check
     * @return bool
     */
    public static function hasAnyRole($roles) {
        if (!self::isAuthenticated()) {
            return false;
        }
        
        return isset(self::$user['role']) && in_array(self::$user['role'], $roles);
    }
    
    /**
     * Require authentication
     * @throws Exception If not authenticated
     */
    public static function requireAuth() {
        if (!self::isAuthenticated()) {
            throw new Exception('Authentication required', 401);
        }
    }
    
    /**
     * Require specific role
     * Redirects to appropriate dashboard if not authenticated or missing role
     * @param string $role Required role
     */
    public static function requireRole($role) {
        self::requireAuth();
        
        if (!self::hasRole($role)) {
            throw new Exception('Access denied: Role ' . $role . ' required', 403);
        }
    }
    
    /**
     * Require any of the specified roles
     * Redirects to appropriate dashboard if not authenticated or missing roles
     * @param array $roles Required roles
     */
    public static function requireAnyRole($roles) {
        self::requireAuth();
        
        if (!self::hasAnyRole($roles)) {
            throw new Exception('Access denied: One of the following roles required: ' . implode(', ', $roles), 403);
        }
    }
}

class AuthMiddleware {
    private static $user = null;
    
    /**
     * Initialize session and get current user
     */
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Bypass authentication for development
        self::$user = [
            'id' => 1,
            'username' => 'admin',
            'role' => 'admin',
            'email' => 'admin@example.com'
        ];
    }
    
    /**
     * Get current authenticated user from session
     * @return array|null User data or null if not authenticated
     */
    public function getCurrentUser() {
        return self::$user;
    }
    
    /**
     * Check if user is authenticated
     * @return bool
     */
    public function isAuthenticated() {
        return true; // Always return true for development
    }
    
    /**
     * Check if user has a specific role
     * @param string $role Role to check
     * @return bool
     */
    public function hasRole($role) {
        return true; // Always return true for development
    }
    
    /**
     * Check if user has any of the specified roles
     * @param array $roles Roles to check
     * @return bool
     */
    public function hasAnyRole($roles) {
        return true; // Always return true for development
    }
    
    /**
     * Require authentication
     * No redirection for development
     */
    public function requireAuth() {
        // Do nothing for development
    }
    
    /**
     * Require specific role
     * No redirection for development
     * @param string $role Required role
     */
    public function requireRole($role) {
        // Do nothing for development
    }
    
    /**
     * Set user data in session
     * @param array $userData User data to store in session
     */
    public function setUser($userData) {
        $_SESSION['user'] = $userData;
        self::$user = $userData;
    }
    
    /**
     * Clear user session
     */
    public function clearUser() {
        unset($_SESSION['user']);
        self::$user = null;
        session_destroy();
    }
    
    /**
     * Get dashboard URL for a specific role
     * @param string $role User role
     * @return string Dashboard URL
     */
    private function getDashboardUrl($role) {
        switch ($role) {
            case 'admin':
                return '/QLNhanSu_version1/public/admin/dashboard.html';
            case 'manager':
                return '/QLNhanSu_version1/public/manager/dashboard.html';
            case 'employee':
                return '/QLNhanSu_version1/public/employee/dashboard.html';
            default:
                return '/QLNhanSu_version1/public/login.html';
        }
    }
    
    /**
     * Get redirect URL based on user role
     * @return string Redirect URL
     */
    public function getRedirectUrl() {
        if (!self::isAuthenticated()) {
            return '/QLNhanSu_version1/public/login.html';
        }
        
        $role = self::$user['role'] ?? '';
        return $this->getDashboardUrl($role);
    }
} 