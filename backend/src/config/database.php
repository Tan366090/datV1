<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'qlnhansu');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Create logs directory if it doesn't exist
if (!file_exists(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0777, true);
}

class Database {
    private static $host = DB_HOST;
    private static $db_name = DB_NAME;
    private static $username = DB_USER;
    private static $password = DB_PASS;
    private static $charset = DB_CHARSET;
    private static $conn;

    public static function getConnection() {
        if (self::$conn === null) {
            try {
                self::$conn = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=" . self::$charset,
                    self::$username,
                    self::$password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch(PDOException $e) {
                error_log("Database connection error: " . $e->getMessage());
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$conn;
    }
}

// Initialize database connection
try {
    $db = Database::getConnection();
} catch (Exception $e) {
    error_log("Database connection failed: " . $e->getMessage());
}

// Return configuration array for DataStore
return [
    'host' => DB_HOST,
    'database' => DB_NAME,
    'username' => DB_USER,
    'password' => DB_PASS,
    'charset' => DB_CHARSET
];
?> 