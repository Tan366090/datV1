-- Insert sample KPI records
INSERT INTO kpi_records (user_id, completion_rate, date) VALUES
(1, 85.5, CURRENT_DATE),
(2, 92.0, CURRENT_DATE),
(3, 78.5, CURRENT_DATE),
(4, 95.0, CURRENT_DATE),
(5, 88.0, CURRENT_DATE);

-- Insert sample candidates
INSERT INTO candidates (name, email, phone, position_applied, status) VALUES
('Nguyen Van A', 'nguyenvana@email.com', '0123456789', 'Developer', 'new'),
('Tran Thi B', 'tranthib@email.com', '0987654321', 'Designer', 'new'),
('Le Van C', 'levanc@email.com', '0369852147', 'Manager', 'new');

-- Insert sample projects
INSERT INTO projects (name, description, start_date, end_date, status) VALUES
('Project A', 'Description for Project A', CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), 'active'),
('Project B', 'Description for Project B', CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 60 DAY), 'active'),
('Project C', 'Description for Project C', CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 45 DAY), 'active');

-- Insert sample attendance records
INSERT INTO attendance (user_id, date, time_in, time_out, status) VALUES
(1, CURRENT_DATE, '08:00:00', '17:00:00', 'present'),
(2, CURRENT_DATE, '08:30:00', '17:30:00', 'present'),
(3, CURRENT_DATE, '09:00:00', '17:00:00', 'late'),
(4, CURRENT_DATE, '08:15:00', '17:15:00', 'present'),
(5, CURRENT_DATE, '08:45:00', '17:45:00', 'present');

-- Insert sample leave requests
INSERT INTO leave_requests (user_id, start_date, end_date, reason, status) VALUES
(1, DATE_ADD(CURRENT_DATE, INTERVAL 1 DAY), DATE_ADD(CURRENT_DATE, INTERVAL 2 DAY), 'Personal leave', 'pending'),
(2, DATE_ADD(CURRENT_DATE, INTERVAL 3 DAY), DATE_ADD(CURRENT_DATE, INTERVAL 4 DAY), 'Family event', 'pending'),
(3, DATE_ADD(CURRENT_DATE, INTERVAL 5 DAY), DATE_ADD(CURRENT_DATE, INTERVAL 6 DAY), 'Medical checkup', 'pending');

-- Insert sample payroll records
INSERT INTO payroll (user_id, month, year, base_salary, allowances, deductions, net_salary, status, date) VALUES
(1, MONTH(CURRENT_DATE), YEAR(CURRENT_DATE), 15000000, 2000000, 1000000, 16000000, 'pending', CURRENT_DATE),
(2, MONTH(CURRENT_DATE), YEAR(CURRENT_DATE), 12000000, 1500000, 800000, 12700000, 'pending', CURRENT_DATE),
(3, MONTH(CURRENT_DATE), YEAR(CURRENT_DATE), 10000000, 1000000, 500000, 10500000, 'pending', CURRENT_DATE),
(4, MONTH(CURRENT_DATE), YEAR(CURRENT_DATE), 18000000, 2500000, 1200000, 19300000, 'pending', CURRENT_DATE),
(5, MONTH(CURRENT_DATE), YEAR(CURRENT_DATE), 20000000, 3000000, 1500000, 21500000, 'pending', CURRENT_DATE); 