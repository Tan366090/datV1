<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Create departments table
    $sql = "CREATE TABLE IF NOT EXISTS departments (
        department_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        manager_id INT,
        parent_department_id INT,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (manager_id) REFERENCES user_profiles(user_id),
        FOREIGN KEY (parent_department_id) REFERENCES departments(department_id)
    )";

    $conn->exec($sql);

    // Insert sample data
    $sql = "INSERT INTO departments (name, description, status) VALUES 
        ('IT Department', 'Information Technology Department', 'active'),
        ('HR Department', 'Human Resources Department', 'active'),
        ('Finance Department', 'Finance and Accounting Department', 'active'),
        ('Marketing Department', 'Marketing and Sales Department', 'active'),
        ('Operations Department', 'Operations and Logistics Department', 'active')";

    $conn->exec($sql);

    echo "Departments table created and sample data inserted successfully\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 