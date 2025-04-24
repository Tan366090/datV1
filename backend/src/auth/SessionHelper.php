<?php

class SessionHelper {
    private static $sessionStarted = false;

    public static function start() {
        if (!self::$sessionStarted) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            self::$sessionStarted = true;
        }
    }

    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove($key) {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function destroy() {
        self::start();
        session_unset();
        session_destroy();
        self::$sessionStarted = false;
    }

    public static function isAuthenticated() {
        self::start();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function getCurrentUser() {
        self::start();
        if (isset($_SESSION['user_id'])) {
            return [
                'user_id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role_id' => $_SESSION['role_id'],
                'email' => $_SESSION['email'] ?? null,
                'full_name' => $_SESSION['full_name'] ?? null
            ];
        }
        return null;
    }

    public static function setUserData($userData) {
        self::start();
        $_SESSION['user_id'] = $userData['user_id'];
        $_SESSION['username'] = $userData['username'];
        $_SESSION['role_id'] = $userData['role_id'];
        $_SESSION['email'] = $userData['email'] ?? null;
        $_SESSION['full_name'] = $userData['full_name'] ?? null;
    }
} 