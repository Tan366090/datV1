-- Insert default roles
INSERT INTO roles (role_name, description) VALUES
('admin', 'Administrator with full access'),
('manager', 'Department manager with elevated access'),
('employee', 'Regular employee');

-- Insert default admin user
INSERT INTO users (username, email, password_hash, role_id, status, email_verified) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'active', 1);

-- Insert admin profile
INSERT INTO user_profiles (user_id, full_name, date_of_birth, gender) VALUES
(1, 'System Administrator', '1990-01-01', 'other'); 