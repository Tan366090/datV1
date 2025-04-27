<?php
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // First connect without specifying a database
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS qlnhansu";
    $conn->exec($sql);
    echo "Database qlnhansu created or already exists\n";

    // Now connect to the specific database
    $conn = new PDO("mysql:host=$host;dbname=qlnhansu", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Disable foreign key checks
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Drop existing tables
    $tables = [
        'employee_sentiment',
        'employees',
        'positions',
        'departments'
    ];

    foreach ($tables as $table) {
        try {
            $conn->exec("DROP TABLE IF EXISTS $table");
            echo "Dropped table $table if it existed\n";
        } catch (PDOException $e) {
            echo "Error dropping table $table: " . $e->getMessage() . "\n";
        }
    }

    // Create departments table
    try {
        $sql = "CREATE TABLE departments (
            department_id INT PRIMARY KEY,
            department_name VARCHAR(100)
        ) ENGINE=MyISAM";
        $conn->exec($sql);
        echo "Created table departments\n";
        sleep(1); // Wait for 1 second
    } catch (PDOException $e) {
        echo "Error creating departments table: " . $e->getMessage() . "\n";
    }

    // Create positions table
    try {
        $sql = "CREATE TABLE positions (
            position_id INT PRIMARY KEY,
            position_name VARCHAR(100)
        ) ENGINE=MyISAM";
        $conn->exec($sql);
        echo "Created table positions\n";
        sleep(1); // Wait for 1 second
    } catch (PDOException $e) {
        echo "Error creating positions table: " . $e->getMessage() . "\n";
    }

    // Create employees table
    try {
        $sql = "CREATE TABLE employees (
            employee_id INT PRIMARY KEY,
            full_name VARCHAR(100),
            position_id INT,
            department_id INT
        ) ENGINE=MyISAM";
        $conn->exec($sql);
        echo "Created table employees\n";
        sleep(1); // Wait for 1 second
    } catch (PDOException $e) {
        echo "Error creating employees table: " . $e->getMessage() . "\n";
    }

    // Create employee_sentiment table
    try {
        $sql = "CREATE TABLE employee_sentiment (
            sentiment_id INT PRIMARY KEY,
            employee_id INT,
            sentiment_score DECIMAL(5,2)
        ) ENGINE=MyISAM";
        $conn->exec($sql);
        echo "Created table employee_sentiment\n";
        sleep(1); // Wait for 1 second
    } catch (PDOException $e) {
        echo "Error creating employee_sentiment table: " . $e->getMessage() . "\n";
    }

    // Insert sample data into departments
    try {
        $sql = "INSERT INTO departments (department_id, department_name) VALUES 
            (1, 'IT Department'),
            (2, 'HR Department'),
            (3, 'Finance Department'),
            (4, 'Marketing Department'),
            (5, 'Operations Department')";
        $conn->exec($sql);
        echo "Inserted sample departments\n";
        sleep(1); // Wait for 1 second
    } catch (PDOException $e) {
        echo "Error inserting departments: " . $e->getMessage() . "\n";
    }

    // Insert sample data into positions
    try {
        $sql = "INSERT INTO positions (position_id, position_name) VALUES 
            (1, 'Software Engineer'),
            (2, 'HR Manager'),
            (3, 'Financial Analyst'),
            (4, 'Marketing Specialist'),
            (5, 'Operations Manager')";
        $conn->exec($sql);
        echo "Inserted sample positions\n";
        sleep(1); // Wait for 1 second
    } catch (PDOException $e) {
        echo "Error inserting positions: " . $e->getMessage() . "\n";
    }

    // Insert sample data into employees
    try {
        $sql = "INSERT INTO employees (employee_id, full_name, position_id, department_id) VALUES 
            (1, 'John Doe', 1, 1),
            (2, 'Jane Smith', 2, 2),
            (3, 'Bob Johnson', 3, 3),
            (4, 'Alice Brown', 4, 4),
            (5, 'Charlie Wilson', 5, 5)";
        $conn->exec($sql);
        echo "Inserted sample employees\n";
        sleep(1); // Wait for 1 second
    } catch (PDOException $e) {
        echo "Error inserting employees: " . $e->getMessage() . "\n";
    }

    // Insert sample data into employee_sentiment
    try {
        $sql = "INSERT INTO employee_sentiment (sentiment_id, employee_id, sentiment_score) VALUES 
            (1, 1, 0.75),
            (2, 2, 0.85),
            (3, 3, 0.65),
            (4, 4, 0.90),
            (5, 5, 0.70)";
        $conn->exec($sql);
        echo "Inserted sample sentiment data\n";
        sleep(1); // Wait for 1 second
    } catch (PDOException $e) {
        echo "Error inserting sentiment data: " . $e->getMessage() . "\n";
    }

    // Modify departments table
    try {
        $sql = "ALTER TABLE departments 
            MODIFY department_id INT AUTO_INCREMENT,
            MODIFY department_name VARCHAR(100) NOT NULL,
            ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        $conn->exec($sql);
        echo "Modified departments table\n";
    } catch (PDOException $e) {
        echo "Error modifying departments table: " . $e->getMessage() . "\n";
    }

    // Modify positions table
    try {
        $sql = "ALTER TABLE positions 
            MODIFY position_id INT AUTO_INCREMENT,
            MODIFY position_name VARCHAR(100) NOT NULL,
            ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        $conn->exec($sql);
        echo "Modified positions table\n";
    } catch (PDOException $e) {
        echo "Error modifying positions table: " . $e->getMessage() . "\n";
    }

    // Modify employees table
    try {
        $sql = "ALTER TABLE employees 
            MODIFY employee_id INT AUTO_INCREMENT,
            MODIFY full_name VARCHAR(100) NOT NULL,
            MODIFY position_id INT NOT NULL,
            MODIFY department_id INT NOT NULL,
            ADD COLUMN hire_date DATE NOT NULL,
            ADD COLUMN birth_date DATE,
            ADD COLUMN phone VARCHAR(20),
            ADD COLUMN email VARCHAR(100),
            ADD COLUMN address TEXT,
            ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active',
            ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            ADD INDEX idx_position (position_id),
            ADD INDEX idx_department (department_id)";
        $conn->exec($sql);
        echo "Modified employees table\n";
    } catch (PDOException $e) {
        echo "Error modifying employees table: " . $e->getMessage() . "\n";
    }

    // Modify employee_sentiment table
    try {
        $sql = "ALTER TABLE employee_sentiment 
            MODIFY sentiment_id INT AUTO_INCREMENT,
            MODIFY employee_id INT NOT NULL,
            MODIFY sentiment_score DECIMAL(5,2) NOT NULL,
            ADD COLUMN analysis_date DATETIME NOT NULL,
            ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            ADD INDEX idx_employee (employee_id)";
        $conn->exec($sql);
        echo "Modified employee_sentiment table\n";
    } catch (PDOException $e) {
        echo "Error modifying employee_sentiment table: " . $e->getMessage() . "\n";
    }

    // Add foreign key constraints
    try {
        $sql = "ALTER TABLE employees 
            ADD CONSTRAINT fk_position FOREIGN KEY (position_id) REFERENCES positions(position_id),
            ADD CONSTRAINT fk_department FOREIGN KEY (department_id) REFERENCES departments(department_id)";
        $conn->exec($sql);
        echo "Added foreign key constraints to employees table\n";
    } catch (PDOException $e) {
        echo "Error adding foreign key constraints to employees table: " . $e->getMessage() . "\n";
    }

    try {
        $sql = "ALTER TABLE employee_sentiment 
            ADD CONSTRAINT fk_employee FOREIGN KEY (employee_id) REFERENCES employees(employee_id)";
        $conn->exec($sql);
        echo "Added foreign key constraint to employee_sentiment table\n";
    } catch (PDOException $e) {
        echo "Error adding foreign key constraint to employee_sentiment table: " . $e->getMessage() . "\n";
    }

    // Update sample data in employees table
    try {
        $sql = "UPDATE employees SET 
            hire_date = '2023-01-01',
            birth_date = '1990-01-01',
            phone = '1234567890',
            email = 'john@example.com',
            address = '123 Main St',
            status = 'active'
            WHERE employee_id = 1";
        $conn->exec($sql);
        echo "Updated employee 1\n";

        $sql = "UPDATE employees SET 
            hire_date = '2023-02-01',
            birth_date = '1991-02-01',
            phone = '2345678901',
            email = 'jane@example.com',
            address = '456 Oak St',
            status = 'active'
            WHERE employee_id = 2";
        $conn->exec($sql);
        echo "Updated employee 2\n";

        $sql = "UPDATE employees SET 
            hire_date = '2023-03-01',
            birth_date = '1992-03-01',
            phone = '3456789012',
            email = 'bob@example.com',
            address = '789 Pine St',
            status = 'active'
            WHERE employee_id = 3";
        $conn->exec($sql);
        echo "Updated employee 3\n";

        $sql = "UPDATE employees SET 
            hire_date = '2023-04-01',
            birth_date = '1993-04-01',
            phone = '4567890123',
            email = 'alice@example.com',
            address = '321 Elm St',
            status = 'active'
            WHERE employee_id = 4";
        $conn->exec($sql);
        echo "Updated employee 4\n";

        $sql = "UPDATE employees SET 
            hire_date = '2023-05-01',
            birth_date = '1994-05-01',
            phone = '5678901234',
            email = 'charlie@example.com',
            address = '654 Maple St',
            status = 'active'
            WHERE employee_id = 5";
        $conn->exec($sql);
        echo "Updated employee 5\n";
    } catch (PDOException $e) {
        echo "Error updating employees: " . $e->getMessage() . "\n";
    }

    // Update sample data in employee_sentiment table
    try {
        $sql = "UPDATE employee_sentiment SET 
            analysis_date = '2024-04-27 10:00:00'
            WHERE sentiment_id = 1";
        $conn->exec($sql);
        echo "Updated sentiment 1\n";

        $sql = "UPDATE employee_sentiment SET 
            analysis_date = '2024-04-27 10:00:00'
            WHERE sentiment_id = 2";
        $conn->exec($sql);
        echo "Updated sentiment 2\n";

        $sql = "UPDATE employee_sentiment SET 
            analysis_date = '2024-04-27 10:00:00'
            WHERE sentiment_id = 3";
        $conn->exec($sql);
        echo "Updated sentiment 3\n";

        $sql = "UPDATE employee_sentiment SET 
            analysis_date = '2024-04-27 10:00:00'
            WHERE sentiment_id = 4";
        $conn->exec($sql);
        echo "Updated sentiment 4\n";

        $sql = "UPDATE employee_sentiment SET 
            analysis_date = '2024-04-27 10:00:00'
            WHERE sentiment_id = 5";
        $conn->exec($sql);
        echo "Updated sentiment 5\n";
    } catch (PDOException $e) {
        echo "Error updating sentiment data: " . $e->getMessage() . "\n";
    }

    // Enable foreign key checks
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "All tables modified successfully\n";

} catch(PDOException $e) {
    // Enable foreign key checks even if there's an error
    if (isset($conn)) {
        $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
    }
    echo "Error: " . $e->getMessage() . "\n";
} 