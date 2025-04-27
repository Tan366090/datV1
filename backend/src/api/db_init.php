<?php
require_once __DIR__ . '/../config/Database.php';

try {
    $db = Database::getInstance()->getConnection();

    // Create performances table
    $db->exec("CREATE TABLE IF NOT EXISTS performances (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT,
        kpi_score DECIMAL(5,2),
        rating VARCHAR(50),
        period DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employees(id)
    )");

    // Create payroll table
    $db->exec("CREATE TABLE IF NOT EXISTS payroll (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT,
        basic_salary DECIMAL(10,2),
        allowances DECIMAL(10,2),
        deductions DECIMAL(10,2),
        net_salary DECIMAL(10,2),
        period DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employees(id)
    )");

    // Create leaves table
    $db->exec("CREATE TABLE IF NOT EXISTS leaves (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT,
        type VARCHAR(50),
        start_date DATE,
        end_date DATE,
        status VARCHAR(50),
        reason TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employees(id)
    )");

    // Create trainings table
    $db->exec("CREATE TABLE IF NOT EXISTS trainings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        trainer VARCHAR(255),
        start_date DATE,
        end_date DATE,
        status VARCHAR(50),
        participants INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create tasks table
    $db->exec("CREATE TABLE IF NOT EXISTS tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255),
        assignee_id INT,
        due_date DATE,
        status VARCHAR(50),
        priority VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (assignee_id) REFERENCES employees(id)
    )");

    echo json_encode([
        'success' => true,
        'message' => 'Database tables created successfully'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 
 