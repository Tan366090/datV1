<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Try to connect to MySQL directly
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "MySQL connection successful!<br>";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'qlnhansu'");
    if ($stmt->rowCount() > 0) {
        echo "Database 'qlnhansu' exists<br>";
        
        // Try to select the database
        $pdo->exec("USE qlnhansu");
        echo "Successfully selected database 'qlnhansu'<br>";
        
        // Check tables
        $tables = ['activities', 'users', 'user_profiles'];
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "Table '$table' exists<br>";
            } else {
                echo "Table '$table' does NOT exist<br>";
            }
        }
    } else {
        echo "Database 'qlnhansu' does NOT exist<br>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
}
?> 