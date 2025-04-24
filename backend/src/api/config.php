<?php
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'QLNhanSu_version1';
$username = 'root';
$password = '';

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        )
    );
    error_log("Database connection established");
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    // Try to create database if it doesn't exist
    try {
        $tempConn = new PDO("mysql:host=$host", $username, $password);
        $tempConn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
        $tempConn = null;
        
        // Try to connect again
        $conn = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            )
        );
        error_log("Database created and connection established");
    } catch (PDOException $e) {
        error_log("Database creation failed: " . $e->getMessage());
        die("Database connection failed");
    }
}

// JWT Configuration
define('JWT_SECRET', 'qlnhansu_secret_key_2024'); // Secret key cho JWT
define('JWT_EXPIRES_IN', '24h');
define('JWT_REFRESH_EXPIRES_IN', '7d');

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Set default timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Set default charset
ini_set('default_charset', 'UTF-8');

// Enable output buffering
ob_start();
?> 