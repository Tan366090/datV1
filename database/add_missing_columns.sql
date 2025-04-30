-- Thêm cột evaluation_date vào bảng performances
ALTER TABLE `performances`
ADD COLUMN `evaluation_date` date DEFAULT NULL AFTER `review_date`,
ADD COLUMN `score` decimal(4,2) DEFAULT NULL AFTER `performance_score`;

-- Cập nhật dữ liệu cho các cột mới
UPDATE `performances` 
SET `evaluation_date` = `review_date`,
    `score` = `performance_score`;

-- Thêm cột amount vào bảng payroll
ALTER TABLE `payroll`
ADD COLUMN `amount` decimal(15,2) DEFAULT NULL AFTER `net_salary`;

-- Cập nhật dữ liệu cho cột amount
UPDATE `payroll` 
SET `amount` = `gross_salary`;

-- Thêm cột type vào bảng leaves
ALTER TABLE `leaves`
ADD COLUMN `type` varchar(50) DEFAULT NULL AFTER `leave_type`;

-- Cập nhật dữ liệu cho cột type
UPDATE `leaves` 
SET `type` = `leave_type`;

-- Thêm các ràng buộc
ALTER TABLE `performances`
ADD CONSTRAINT `chk_evaluation_date` CHECK (`evaluation_date` IS NULL OR `evaluation_date` >= `review_period_start` AND `evaluation_date` <= `review_period_end`),
ADD CONSTRAINT `chk_score` CHECK (`score` IS NULL OR `score` >= 0 AND `score` <= 5);

ALTER TABLE `payroll`
ADD CONSTRAINT `chk_amount` CHECK (`amount` IS NULL OR `amount` >= 0);

ALTER TABLE `leaves`
ADD CONSTRAINT `chk_type` CHECK (`type` IN ('Annual', 'Sick', 'Unpaid', 'Maternity', 'Paternity', 'Bereavement', 'Other')); 