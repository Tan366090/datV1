<?php
require_once __DIR__ . '/../../config/database.php';

try {
    // Drop existing tables if they exist
    $conn->exec("DROP TABLE IF EXISTS employee_sentiment");
    $conn->exec("DROP TABLE IF EXISTS employees");
    $conn->exec("DROP TABLE IF EXISTS positions");
    $conn->exec("DROP TABLE IF EXISTS departments");
    
    // Create departments table
    $query = "CREATE TABLE departments (
        department_id INT AUTO_INCREMENT PRIMARY KEY,
        department_name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB";
    $conn->exec($query);
    echo "Table departments created successfully\n";

    // Create positions table
    $query = "CREATE TABLE positions (
        position_id INT AUTO_INCREMENT PRIMARY KEY,
        position_name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB";
    $conn->exec($query);
    echo "Table positions created successfully\n";

    // Create employees table
    $query = "CREATE TABLE employees (
        employee_id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        position_id INT NOT NULL,
        department_id INT NOT NULL,
        hire_date DATE NOT NULL,
        birth_date DATE,
        phone VARCHAR(20),
        email VARCHAR(100),
        address TEXT,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB";
    $conn->exec($query);
    echo "Table employees created successfully\n";

    // Insert departments
    $query = "INSERT INTO departments (department_name) VALUES 
        ('IT Department'),
        ('HR Department'),
        ('Finance Department'),
        ('Marketing Department'),
        ('Operations Department')";
    $conn->exec($query);
    echo "Sample departments inserted successfully\n";

    // Insert positions
    $query = "INSERT INTO positions (position_name) VALUES 
        ('Software Engineer'),
        ('HR Manager'),
        ('Financial Analyst'),
        ('Marketing Specialist'),
        ('Operations Manager')";
    $conn->exec($query);
    echo "Sample positions inserted successfully\n";

    // Insert employees
    $query = "INSERT INTO employees 
        (full_name, position_id, department_id, hire_date, birth_date, phone, email, address, status) 
    VALUES 
        ('John Doe', 1, 1, '2023-01-01', '1990-01-01', '1234567890', 'john@example.com', '123 Main St', 'active'),
        ('Jane Smith', 2, 2, '2023-02-01', '1991-02-01', '2345678901', 'jane@example.com', '456 Oak St', 'active'),
        ('Bob Johnson', 3, 3, '2023-03-01', '1992-03-01', '3456789012', 'bob@example.com', '789 Pine St', 'active'),
        ('Alice Brown', 4, 4, '2023-04-01', '1993-04-01', '4567890123', 'alice@example.com', '321 Elm St', 'active'),
        ('Charlie Wilson', 5, 5, '2023-05-01', '1994-05-01', '5678901234', 'charlie@example.com', '654 Maple St', 'active')";
    $conn->exec($query);
    echo "Sample employees inserted successfully\n";

    // Add foreign key constraints
    $query = "ALTER TABLE employees 
        ADD CONSTRAINT fk_position 
        FOREIGN KEY (position_id) 
        REFERENCES positions(position_id)";
    $conn->exec($query);

    $query = "ALTER TABLE employees 
        ADD CONSTRAINT fk_department 
        FOREIGN KEY (department_id) 
        REFERENCES departments(department_id)";
    $conn->exec($query);
    echo "Foreign key constraints added successfully\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 