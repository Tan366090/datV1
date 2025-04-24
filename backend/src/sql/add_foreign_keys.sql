-- Add foreign key constraints to departments table
ALTER TABLE departments 
ADD CONSTRAINT fk_departments_manager 
FOREIGN KEY (manager_id) 
REFERENCES employees(id) 
ON DELETE SET NULL;

-- Add foreign key constraints to positions table
ALTER TABLE positions 
ADD CONSTRAINT fk_positions_department 
FOREIGN KEY (department_id) 
REFERENCES departments(id) 
ON DELETE CASCADE; 