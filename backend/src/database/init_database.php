<?php
// Database configuration
$host = 'localhost';
$dbname = 'qlnhansu';
$username = 'root';
$password = '';

try {
    // Create database connection
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");
    
    // Create tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS departments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS positions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS employees (
            id INT AUTO_INCREMENT PRIMARY KEY,
            employee_code VARCHAR(20) UNIQUE NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            position_id INT NOT NULL,
            department_id INT NOT NULL,
            join_date DATE NOT NULL,
            birth_date DATE,
            phone VARCHAR(20),
            email VARCHAR(100),
            address TEXT,
            salary DECIMAL(10,2) DEFAULT 0,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (position_id) REFERENCES positions(id),
            FOREIGN KEY (department_id) REFERENCES departments(id),
            INDEX idx_search (full_name, employee_code, email)
        );
        
        CREATE TABLE IF NOT EXISTS kpi_records (
            id INT AUTO_INCREMENT PRIMARY KEY,
            employee_id INT NOT NULL,
            completion_rate DECIMAL(5,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (employee_id) REFERENCES employees(id)
        );
        
        CREATE TABLE IF NOT EXISTS candidates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(100) NOT NULL,
            position_id INT NOT NULL,
            department_id INT NOT NULL,
            status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (position_id) REFERENCES positions(id),
            FOREIGN KEY (department_id) REFERENCES departments(id)
        );
        
        CREATE TABLE IF NOT EXISTS projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            status ENUM('active', 'completed', 'pending') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS attendance (
            id INT AUTO_INCREMENT PRIMARY KEY,
            employee_id INT NOT NULL,
            check_in DATETIME,
            check_out DATETIME,
            status ENUM('present', 'absent', 'late') DEFAULT 'present',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (employee_id) REFERENCES employees(id)
        );
        
        CREATE TABLE IF NOT EXISTS leave_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            employee_id INT NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            reason TEXT,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (employee_id) REFERENCES employees(id)
        );
        
        CREATE TABLE IF NOT EXISTS mobile_app_stats (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            action_type ENUM('download', 'login', 'notification') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS backups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            backup_date DATETIME NOT NULL,
            file_size BIGINT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");
    
    // Insert sample data
    $pdo->exec("
        -- Insert positions first
        INSERT INTO positions (name) VALUES 
        ('Nhân viên'),
        ('Trưởng phòng'),
        ('Phó phòng'),
        ('Giám đốc');
        
        -- Insert departments second
        INSERT INTO departments (name) VALUES 
        ('Phòng Nhân sự'),
        ('Phòng Kế toán'),
        ('Phòng IT'),
        ('Phòng Kinh doanh');
        
        -- Insert employees third (after positions and departments)
        INSERT INTO employees (employee_code, full_name, position_id, department_id, join_date, salary, status) VALUES 
        ('NV001', 'Nguyễn Văn A', 1, 1, '2023-01-01', 10000000, 'active'),
        ('NV002', 'Trần Thị B', 2, 2, '2023-02-01', 15000000, 'active'),
        ('NV003', 'Lê Văn C', 1, 3, '2023-03-01', 12000000, 'active');
        
        -- Insert KPI records (after employees)
        INSERT INTO kpi_records (employee_id, completion_rate, created_at) VALUES 
        (1, 85.5, '2024-03-20'),
        (2, 92.0, '2024-03-20'),
        (3, 78.5, '2024-03-20');
        
        -- Insert candidates (after positions and departments)
        INSERT INTO candidates (full_name, position_id, department_id, status) VALUES 
        ('Phạm Thị D', 1, 1, 'pending'),
        ('Hoàng Văn E', 2, 2, 'pending');
        
        -- Insert projects
        INSERT INTO projects (name, status) VALUES 
        ('Dự án A', 'active'),
        ('Dự án B', 'active'),
        ('Dự án C', 'completed');
        
        -- Insert attendance (after employees)
        INSERT INTO attendance (employee_id, status, created_at) VALUES 
        (1, 'present', '2024-03-20 08:00:00'),
        (2, 'present', '2024-03-20 08:05:00'),
        (3, 'late', '2024-03-20 08:30:00');
        
        -- Insert leave requests (after employees)
        INSERT INTO leave_requests (employee_id, start_date, end_date, reason, status) VALUES 
        (1, '2024-03-25', '2024-03-26', 'Nghỉ phép', 'pending'),
        (2, '2024-03-27', '2024-03-28', 'Nghỉ ốm', 'pending');
        
        -- Insert mobile app stats (after employees)
        INSERT INTO mobile_app_stats (user_id, action_type) VALUES 
        (1, 'download'),
        (2, 'login'),
        (3, 'notification');
        
        -- Insert backup info
        INSERT INTO backups (backup_date, file_size) VALUES 
        ('2024-03-20 00:00:00', 1024 * 1024 * 100);
    ");
    
    echo "Database initialized successfully!";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 