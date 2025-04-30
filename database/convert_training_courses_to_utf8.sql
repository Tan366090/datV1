-- Chuyển đổi bảng training_courses sang UTF-8
ALTER TABLE `training_courses` 
    CONVERT TO CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

-- Cập nhật lại dữ liệu với encoding UTF-8
UPDATE `training_courses` SET 
    `name` = 'Quản lý dự án Agile',
    `description` = 'Khóa học Agile cơ bản'
WHERE `id` = 1;

UPDATE `training_courses` SET 
    `name` = 'Lập trình Python',
    `description` = 'Khóa học Python nâng cao'
WHERE `id` = 2;

UPDATE `training_courses` SET 
    `name` = 'Kỹ năng giao tiếp',
    `description` = 'Đào tạo kỹ năng mềm'
WHERE `id` = 3;

UPDATE `training_courses` SET 
    `name` = 'Quản lý tài chính',
    `description` = 'Khóa học cho quản lý'
WHERE `id` = 4;

UPDATE `training_courses` SET 
    `name` = 'Marketing Digital',
    `description` = 'Chiến lược tiếp thị số'
WHERE `id` = 5;

UPDATE `training_courses` SET 
    `name` = 'An toàn thông tin',
    `description` = 'Bảo mật hệ thống'
WHERE `id` = 6;

UPDATE `training_courses` SET 
    `name` = 'Excel chuyên nghiệp',
    `description` = 'Kỹ năng Excel nâng cao'
WHERE `id` = 7;

UPDATE `training_courses` SET 
    `name` = 'Thiết kế UI/UX',
    `description` = 'Nguyên tắc thiết kế'
WHERE `id` = 8;

UPDATE `training_courses` SET 
    `name` = 'Quản trị cơ sở dữ liệu',
    `description` = 'SQL và NoSQL'
WHERE `id` = 9;

UPDATE `training_courses` SET 
    `name` = 'Kỹ năng lãnh đạo',
    `description` = 'Dành cho quản lý'
WHERE `id` = 10; 