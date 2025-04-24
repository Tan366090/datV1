-- Add PRIMARY KEY constraints if they don't exist
ALTER TABLE departments ADD PRIMARY KEY (id);
ALTER TABLE positions ADD PRIMARY KEY (id);
ALTER TABLE users ADD PRIMARY KEY (user_id);

-- Update departments table to add auto_increment
ALTER TABLE departments MODIFY id INT NOT NULL AUTO_INCREMENT;

-- Update positions table to add auto_increment
ALTER TABLE positions MODIFY id INT NOT NULL AUTO_INCREMENT;

-- Update users table to add auto_increment
ALTER TABLE users MODIFY user_id INT NOT NULL AUTO_INCREMENT;

-- Drop foreign key constraints first
ALTER TABLE payrolls DROP FOREIGN KEY payrolls_ibfk_1;

-- Temporarily disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- Drop tables
DROP TABLE IF EXISTS payrolls;
DROP TABLE IF EXISTS employees;

-- Create employees table
CREATE TABLE employees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    employee_code VARCHAR(20) UNIQUE,
    department_id INT,
    position_id INT,
    hire_date DATE,
    contract_type VARCHAR(50),
    contract_start_date DATE,
    contract_end_date DATE,
    status VARCHAR(20) DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE SET NULL
);

-- Insert existing users into employees table
INSERT INTO employees (user_id, employee_code, department_id, position_id, hire_date, contract_type, contract_start_date, contract_end_date, status)
SELECT 
    user_id,
    employee_code,
    department_id,
    position_id,
    hire_date,
    contract_type,
    contract_start_date,
    contract_end_date,
    status
FROM users
WHERE role_id IN (2, 3, 4) -- Only insert non-admin users
ON DUPLICATE KEY UPDATE
    department_id = VALUES(department_id),
    position_id = VALUES(position_id),
    hire_date = VALUES(hire_date),
    contract_type = VALUES(contract_type),
    contract_start_date = VALUES(contract_start_date),
    contract_end_date = VALUES(contract_end_date),
    status = VALUES(status);

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Create family_members table
CREATE TABLE IF NOT EXISTS family_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    relationship VARCHAR(50) NOT NULL,
    dob DATE NOT NULL,
    occupation VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- Create audit_logs table
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Create payrolls table
CREATE TABLE IF NOT EXISTS payrolls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    month INT NOT NULL,
    year INT NOT NULL,
    basic_salary DECIMAL(10,2) NOT NULL,
    allowances DECIMAL(10,2) DEFAULT 0,
    deductions DECIMAL(10,2) DEFAULT 0,
    net_salary DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- Create departments table if not exists
CREATE TABLE IF NOT EXISTS departments (
    department_id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL,
    description TEXT,
    manager_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (manager_id) REFERENCES employees(id)
);

-- Create positions table if not exists
CREATE TABLE IF NOT EXISTS positions (
    position_id INT AUTO_INCREMENT PRIMARY KEY,
    position_name VARCHAR(100) NOT NULL,
    description TEXT,
    department_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(department_id)
); 