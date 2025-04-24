<?php
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Database connection
    require_once __DIR__ . '/../backend/src/config/database.php';
    
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Update all users' passwords to '123456'
    $query = "UPDATE users SET password_hash = '123456', password_salt = NULL";
    $stmt = $conn->prepare($query);
    $result = $stmt->execute();

    if ($result) {
        echo "Successfully updated all user passwords to '123456'";
    } else {
        echo "Failed to update passwords";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 