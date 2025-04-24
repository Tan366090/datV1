<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Check if database.php exists
    $dbConfigPath = '../../config/database.php';
    if (!file_exists($dbConfigPath)) {
        throw new Exception("Database configuration file not found at: $dbConfigPath");
    }
    
    require_once $dbConfigPath;
    
    // Check if Database class exists
    if (!class_exists('Database')) {
        throw new Exception("Database class not found in database.php");
    }
    
    // Try to get database instance
    try {
        $db = Database::getInstance();
    } catch (Exception $e) {
        throw new Exception("Failed to get database instance: " . $e->getMessage());
    }
    
    // Try to get connection
    try {
        $conn = $db->getConnection();
        if (!$conn) {
            throw new Exception("Database connection is null");
        }
    } catch (Exception $e) {
        throw new Exception("Failed to get database connection: " . $e->getMessage());
    }
    
    echo "Database connection successful!<br>";
    
    // Check if tables exist
    $tables = ['activities', 'users', 'user_profiles'];
    foreach ($tables as $table) {
        try {
            $stmt = $conn->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "Table '$table' exists<br>";
                
                // Check table structure
                $stmt = $conn->query("DESCRIBE $table");
                echo "Structure of '$table':<br>";
                echo "<pre>";
                print_r($stmt->fetchAll());
                echo "</pre>";
            } else {
                echo "Table '$table' does NOT exist<br>";
            }
        } catch (PDOException $e) {
            echo "Error checking table '$table': " . $e->getMessage() . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
    
    // Check environment variables
    echo "<br>Environment variables:<br>";
    echo "DB_HOST: " . (isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : 'not set') . "<br>";
    echo "DB_NAME: " . (isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : 'not set') . "<br>";
    echo "DB_USER: " . (isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : 'not set') . "<br>";
    echo "DB_PASSWORD: " . (isset($_ENV['DB_PASSWORD']) ? 'set' : 'not set') . "<br>";
}
?> 