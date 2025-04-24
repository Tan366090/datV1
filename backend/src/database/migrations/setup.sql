-- Drop existing tables
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS activities;
SET FOREIGN_KEY_CHECKS=1;

-- Create roles table
CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

-- Insert default roles
INSERT INTO roles (role_name) VALUES ('admin'), ('manager'), ('employee');

-- Create users table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    username VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
);

-- Create login_attempts table
CREATE TABLE login_attempts (
    attempt_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    email VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent TEXT,
    status ENUM('success', 'failed') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Create activities table
CREATE TABLE IF NOT EXISTS `activities` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `description` TEXT NOT NULL,
    `user_agent` VARCHAR(255),
    `ip_address` VARCHAR(45),
    `status` ENUM('success', 'warning', 'error') DEFAULT 'success',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample activity data
INSERT INTO `activities` (`user_id`, `type`, `description`, `user_agent`, `ip_address`, `status`) VALUES
(1, 'edit', 'Đã cập nhật thông tin cá nhân', 'Windows 10 - Chrome', '192.168.1.1', 'success'),
(1, 'login', 'Đăng nhập vào hệ thống', 'Windows 10 - Firefox', '192.168.1.1', 'success'),
(2, 'view', 'Xem thông tin nhân viên #3', 'MacOS - Safari', '192.168.1.2', 'success'),
(3, 'create', 'Tạo mới nhân viên', 'Windows 11 - Edge', '192.168.1.3', 'success'),
(1, 'edit', 'Cập nhật thông tin lương', 'Windows 10 - Chrome', '192.168.1.1', 'warning'),
(2, 'delete', 'Xóa tài liệu', 'MacOS - Chrome', '192.168.1.2', 'success'),
(4, 'login', 'Đăng nhập thất bại', 'Ubuntu - Firefox', '192.168.1.4', 'error'),
(1, 'edit', 'Thay đổi phòng ban', 'Windows 10 - Chrome', '192.168.1.1', 'success'),
(3, 'create', 'Tạo yêu cầu nghỉ phép', 'Windows 11 - Edge', '192.168.1.3', 'success'),
(2, 'view', 'Xem báo cáo tháng', 'MacOS - Safari', '192.168.1.2', 'success');

-- Insert test user
INSERT INTO users (email, username, password_hash, role_id) VALUES 
('admin@example.com', 'admin', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 1); 