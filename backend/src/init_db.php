<?php
require_once __DIR__ . '/config/database.php';

try {
    // Connect to MySQL without database
    $pdo = new PDO("mysql:host={$config['host']}", $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$config['database']}");
    echo "Database created successfully\n";
    
    // Select the database
    $pdo->exec("USE {$config['database']}");
    
    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL UNIQUE,
        email VARCHAR(255),
        password_hash VARCHAR(64) NOT NULL,
        password_salt VARCHAR(32) NOT NULL,
        role_id INT NOT NULL,
        department_id INT,
        position_id INT,
        full_name VARCHAR(255),
        is_active BOOLEAN DEFAULT true,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_username (username)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Users table created successfully\n";
    
    // Create roles table
    $pdo->exec("CREATE TABLE IF NOT EXISTS roles (
        role_id INT AUTO_INCREMENT PRIMARY KEY,
        role_name VARCHAR(50) NOT NULL UNIQUE,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Roles table created successfully\n";
    
    // Create departments table
    $pdo->exec("CREATE TABLE IF NOT EXISTS departments (
        department_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Departments table created successfully\n";
    
    // Create positions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS positions (
        position_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Positions table created successfully\n";
    
    // Create login_attempts table
    $pdo->exec("CREATE TABLE IF NOT EXISTS login_attempts (
        attempt_id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL,
        username VARCHAR(255) NOT NULL,
        attempt_time DATETIME NOT NULL,
        status ENUM('success', 'failed') NOT NULL,
        INDEX idx_ip_time (ip_address, attempt_time)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Login attempts table created successfully\n";
    
    // Insert roles
    $pdo->exec("INSERT IGNORE INTO roles (role_id, role_name, description) VALUES 
        (1, 'admin', 'Administrator with full access'),
        (2, 'manager', 'Department manager'),
        (3, 'hr', 'Human Resources staff'),
        (4, 'employee', 'Regular employee')");
    echo "Roles inserted successfully\n";
    
    // Insert departments
    $pdo->exec("INSERT IGNORE INTO departments (department_id, name, description) VALUES 
        (1, 'Human Resources', 'HR Department'),
        (2, 'Information Technology', 'IT Department'),
        (3, 'Finance', 'Finance Department'),
        (4, 'Marketing', 'Marketing Department')");
    echo "Departments inserted successfully\n";
    
    // Insert positions
    $pdo->exec("INSERT IGNORE INTO positions (position_id, name, description) VALUES 
        (1, 'HR Manager', 'Human Resources Manager'),
        (2, 'IT Manager', 'Information Technology Manager'),
        (3, 'Finance Manager', 'Finance Manager'),
        (4, 'Marketing Manager', 'Marketing Manager'),
        (5, 'HR Staff', 'Human Resources Staff'),
        (6, 'Developer', 'Software Developer'),
        (7, 'Accountant', 'Finance Staff'),
        (8, 'Marketing Staff', 'Marketing Staff')");
    echo "Positions inserted successfully\n";
    
    // Insert admin user (password: admin123)
    $password = 'admin123';
    $salt = bin2hex(random_bytes(16));
    $password_hash = hash('sha256', $password . $salt);
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO users 
        (username, email, password_hash, password_salt, role_id, department_id, position_id, full_name) VALUES 
        ('admin', 'admin@example.com', :password_hash, :password_salt, 1, 1, 1, 'Administrator')");
    $stmt->bindParam(':password_hash', $password_hash);
    $stmt->bindParam(':password_salt', $salt);
    $stmt->execute();
    echo "Admin user created successfully\n";
    
    echo "Database initialization completed successfully\n";
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
} 