<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Create employees table
    $sql = "CREATE TABLE IF NOT EXISTS employees (
        employee_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        department_id INT NOT NULL,
        position VARCHAR(100) NOT NULL,
        hire_date DATE NOT NULL,
        salary DECIMAL(10,2) NOT NULL,
        status ENUM('active', 'inactive', 'on_leave') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES user_profiles(user_id),
        FOREIGN KEY (department_id) REFERENCES departments(department_id)
    )";

    $conn->exec($sql);

    // Insert sample data
    $sql = "INSERT INTO employees (user_id, department_id, position, hire_date, salary, status) VALUES 
        (1, 1, 'Software Engineer', '2023-01-01', 5000.00, 'active'),
        (2, 2, 'HR Manager', '2023-02-01', 6000.00, 'active'),
        (3, 3, 'Accountant', '2023-03-01', 4500.00, 'active'),
        (4, 4, 'Marketing Specialist', '2023-04-01', 4800.00, 'active'),
        (5, 5, 'Operations Manager', '2023-05-01', 5500.00, 'active')";

    $conn->exec($sql);

    echo "Employees table created and sample data inserted successfully\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 