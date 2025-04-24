<?php
require_once __DIR__ . '/config/Database.php';

try {
    // Connect to database
    $db = Database::getInstance()->getConnection();
    
    // Check if admin user exists
    $stmt = $db->prepare("SELECT user_id FROM users WHERE username = 'admin'");
    $stmt->execute();
    $adminExists = $stmt->fetch();
    
    if (!$adminExists) {
        // Create admin user
        $password = 'admin123';
        $salt = bin2hex(random_bytes(16));
        $password_hash = hash('sha256', $password . $salt);
        
        $stmt = $db->prepare("INSERT INTO users 
            (username, email, password_hash, password_salt, role_id, department_id, position_id, full_name) 
            VALUES ('admin', 'admin@example.com', :password_hash, :password_salt, 1, 1, 1, 'Administrator')");
        
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->bindParam(':password_salt', $salt);
        $stmt->execute();
        
        echo "Admin user created successfully\n";
    } else {
        // Update admin password
        $password = 'admin123';
        $salt = bin2hex(random_bytes(16));
        $password_hash = hash('sha256', $password . $salt);
        
        $stmt = $db->prepare("UPDATE users 
            SET password_hash = :password_hash, 
                password_salt = :password_salt 
            WHERE username = 'admin'");
        
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->bindParam(':password_salt', $salt);
        $stmt->execute();
        
        echo "Admin password updated successfully\n";
    }
    
    echo "Username: admin\n";
    echo "Password: admin123\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} 