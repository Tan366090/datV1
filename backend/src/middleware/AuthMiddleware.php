<?php
namespace App\Middleware;

use App\Config\SessionManager;

class AuthMiddleware {
    private $sessionManager;

    public function __construct() {
        $this->sessionManager = SessionManager::getInstance();
    }

    public function handle() {
        // Initialize session
        if (!$this->sessionManager->init()) {
            return false;
        }

        // Check if user is authenticated
        if (!$this->sessionManager->isAuthenticated()) {
            return false;
        }

        // Check session timeout
        if (!$this->sessionManager->checkSessionTimeout()) {
            $this->sessionManager->destroy();
            return false;
        }

        return true;
    }

    public function requireRole($requiredRole) {
        if (!$this->handle()) {
            return false;
        }

        $user = $this->sessionManager->getCurrentUser();
        return $user && $user['role'] === $requiredRole;
    }
} 