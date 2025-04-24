-- Cập nhật cấu trúc bảng activities
ALTER TABLE `activities` 
MODIFY COLUMN `status` ENUM('success', 'warning', 'error', 'active') DEFAULT 'success',
MODIFY COLUMN `description` TEXT NOT NULL,
MODIFY COLUMN `user_agent` VARCHAR(255),
MODIFY COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Cập nhật dữ liệu hiện có
UPDATE `activities` SET `status` = 'success' WHERE `status` = 'active'; 