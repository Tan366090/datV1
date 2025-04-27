<?php
// Database configuration
$host = 'localhost';     // Database host
$dbname = 'qlnhansu';   // Database name
$username = 'root';      // Database username
$password = '';          // Database password
$charset = 'utf8mb4';

// Create connection
try {
    // Create PDO connection
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=$charset",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?> 