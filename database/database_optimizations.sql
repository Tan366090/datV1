-- Add indexes for frequently searched fields
ALTER TABLE employees ADD INDEX idx_search (full_name, employee_id, email);
ALTER TABLE departments ADD INDEX idx_name (name);
ALTER TABLE positions ADD INDEX idx_department (department_id);
ALTER TABLE user_profiles ADD INDEX idx_user_id (user_id);
ALTER TABLE attendance ADD INDEX idx_employee_date (employee_id, date);
ALTER TABLE payroll ADD INDEX idx_user_month_year (user_id, payroll_month, payroll_year);

-- Create views for complex queries
CREATE OR REPLACE VIEW employee_details AS
SELECT 
    e.*, 
    d.name as department_name, 
    p.name as position_name,
    up.full_name,
    up.avatar_url,
    up.date_of_birth,
    up.gender,
    up.phone_number,
    up.permanent_address,
    up.current_workplace
FROM employees e
LEFT JOIN departments d ON e.department_id = d.id
LEFT JOIN positions p ON e.position_id = p.id
LEFT JOIN user_profiles up ON e.user_id = up.user_id;

CREATE OR REPLACE VIEW department_statistics AS
SELECT 
    d.id,
    d.name,
    COUNT(e.id) as total_employees,
    SUM(CASE WHEN e.status = 'active' THEN 1 ELSE 0 END) as active_employees,
    AVG(p.base_salary_at_time) as avg_salary
FROM departments d
LEFT JOIN employees e ON d.id = e.department_id
LEFT JOIN payroll p ON e.user_id = p.user_id
GROUP BY d.id, d.name;

-- Create triggers for automatic updates
DELIMITER //

CREATE TRIGGER update_employee_count
AFTER INSERT ON employees
FOR EACH ROW
BEGIN
    UPDATE departments 
    SET employee_count = (
        SELECT COUNT(*) 
        FROM employees 
        WHERE department_id = NEW.department_id
    )
    WHERE id = NEW.department_id;
END//

CREATE TRIGGER update_attendance_status
BEFORE INSERT ON attendance
FOR EACH ROW
BEGIN
    IF NEW.time_in IS NULL THEN
        SET NEW.status = 'absent';
    ELSEIF NEW.time_out IS NULL THEN
        SET NEW.status = 'incomplete';
    ELSE
        SET NEW.status = 'complete';
    END IF;
END//

-- Create stored procedures
CREATE PROCEDURE get_employee_statistics(IN dept_id INT)
BEGIN
    SELECT 
        COUNT(*) as total_employees,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_employees,
        AVG(p.base_salary_at_time) as avg_salary
    FROM employees e
    LEFT JOIN payroll p ON e.user_id = p.user_id
    WHERE e.department_id = dept_id;
END//

CREATE PROCEDURE calculate_monthly_payroll(IN month INT, IN year INT)
BEGIN
    INSERT INTO payroll (
        user_id, 
        payroll_month, 
        payroll_year,
        work_days_actual,
        base_salary_at_time,
        bonuses_total,
        social_insurance_deduction,
        total_salary,
        generated_at,
        generated_by_user_id
    )
    SELECT 
        e.user_id,
        month,
        year,
        COUNT(DISTINCT a.date) as work_days,
        e.base_salary,
        COALESCE(SUM(b.amount), 0) as bonuses,
        e.base_salary * 0.08 as insurance,
        e.base_salary + COALESCE(SUM(b.amount), 0) - (e.base_salary * 0.08) as total,
        NOW(),
        1
    FROM employees e
    LEFT JOIN attendance a ON e.user_id = a.employee_id 
        AND MONTH(a.date) = month 
        AND YEAR(a.date) = year
        AND a.status = 'complete'
    LEFT JOIN bonuses b ON e.user_id = b.employee_id 
        AND MONTH(b.date) = month 
        AND YEAR(b.date) = year
    WHERE e.status = 'active'
    GROUP BY e.user_id, e.base_salary;
END//

DELIMITER ; 