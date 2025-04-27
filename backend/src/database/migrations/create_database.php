<?php
$host = 'localhost';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS qlnhansu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $conn->exec($sql);
    echo "Database created successfully\n";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 