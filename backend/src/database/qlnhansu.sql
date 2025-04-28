-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Adjusted based on restructuring
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qlnhansu`
--

-- --------------------------------------------------------
-- Core Organizational Structure & Definitions (Least Dependent)
-- --------------------------------------------------------

--
-- Table structure for table `departments`
--
CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL, -- FK added later to employees
  `parent_id` int(11) DEFAULT NULL, -- FK added later to self
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_department_name` (`name`),
  KEY `idx_dept_manager_id` (`manager_id`),
  KEY `idx_dept_parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `positions` (Job Roles/Titles)
--
CREATE TABLE `positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `department_id` int(11) NOT NULL,
  `salary_grade` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_position_name_dept` (`name`, `department_id`), -- Position name unique within a department
  KEY `fk_positions_department_idx` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `roles` (Access Control)
--
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_role_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `permissions` (Access Control)
--
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL COMMENT 'Unique code for permission checks',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_permission_code` (`code`),
  UNIQUE KEY `uq_permission_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `training_courses`
--
CREATE TABLE `training_courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'e.g., in hours or days',
  `cost` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active' COMMENT 'e.g., active, inactive, draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Table structure for table `assets` (Replaces equipment)
--
CREATE TABLE `assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `asset_code` varchar(50) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL, -- Added from equipment
  `purchase_date` date DEFAULT NULL,
  `purchase_cost` decimal(10,2) DEFAULT NULL,
  `current_value` decimal(10,2) DEFAULT NULL,
  `status` enum('available','assigned','maintenance','disposed','lost','damaged') NOT NULL DEFAULT 'available',
  `location` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_asset_code` (`asset_code`),
  KEY `idx_asset_status` (`status`),
  KEY `idx_asset_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `benefits`
--
CREATE TABLE `benefits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(50) NOT NULL COMMENT 'e.g., Health, Retirement, Allowance',
  `amount` decimal(10,2) DEFAULT NULL COMMENT 'Fixed amount, if applicable',
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `policies`
--
CREATE TABLE `policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `effective_date` date NOT NULL,
  `status` varchar(20) NOT NULL COMMENT 'e.g., draft, active, archived',
  `file_url` varchar(512) DEFAULT NULL COMMENT 'Optional link to policy document',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `holidays`
--
CREATE TABLE `holidays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `description` text DEFAULT NULL,
  `is_recurring` tinyint(1) DEFAULT 0 COMMENT '1 if repeats annually on same date',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- User Accounts & Employee Records
-- --------------------------------------------------------

--
-- Table structure for table `users` (System Accounts)
--
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_salt` varchar(64) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1 COMMENT '0=Inactive, 1=Active',
  `requires_password_change` tinyint(1) DEFAULT 0 COMMENT '1=Must change password on next login',
  `last_login` datetime DEFAULT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `last_attempt` datetime DEFAULT NULL,
  `remember_token` varchar(64) DEFAULT NULL,
  `remember_token_expiry` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uq_username` (`username`),
  UNIQUE KEY `uq_email` (`email`),
  KEY `fk_users_role_idx` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `employees` (HR Records)
--
CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'Link to the user account',
  `employee_code` varchar(20) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `termination_date` date DEFAULT NULL, -- Added for tracking leavers
  `status` enum('active','inactive','terminated','on_leave') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_employee_code` (`employee_code`),
  UNIQUE KEY `uq_employee_user_id` (`user_id`), -- Ensure one employee record per user
  KEY `fk_employees_department_idx` (`department_id`),
  KEY `fk_employees_position_idx` (`position_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Supporting User/Employee Details
-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--
CREATE TABLE `user_profiles` (
  `profile_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `avatar_url` varchar(512) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other','Prefer not to say') DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `current_address` text DEFAULT NULL, -- Added current address
  `emergency_contact_name` varchar(255) DEFAULT NULL, -- Split emergency contact
  `emergency_contact_phone` varchar(20) DEFAULT NULL, -- Split emergency contact
  `bank_account_number` varchar(50) DEFAULT NULL, -- Renamed
  `bank_name` varchar(100) DEFAULT NULL, -- Added bank name
  `tax_code` varchar(20) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `ethnicity` varchar(100) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `marital_status` enum('Single','Married','Divorced','Widowed') DEFAULT NULL,
  `id_card_number` varchar(20) DEFAULT NULL,
  `id_card_issue_date` date DEFAULT NULL,
  `id_card_issue_place` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
   PRIMARY KEY (`profile_id`),
   UNIQUE KEY `uq_user_profiles_user_id` (`user_id`) -- Ensure one profile per user
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `family_members`
--
CREATE TABLE `family_members` (
  `family_member_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL COMMENT 'Link to the Employee record',
  `member_name` varchar(255) NOT NULL,
  `relationship` varchar(100) NOT NULL,
  `date_of_birth` date DEFAULT NULL, -- Changed from year_of_birth
  `occupation` varchar(100) DEFAULT NULL, -- Added occupation
  `is_dependent` tinyint(1) DEFAULT 0, -- Added flag
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`family_member_id`),
  KEY `fk_family_employee_idx` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `contracts`
--
CREATE TABLE `contracts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `contract_code` varchar(50) DEFAULT NULL, -- Added contract code
  `contract_type` varchar(50) NOT NULL COMMENT 'e.g., Permanent, Fixed-Term, Intern',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL COMMENT 'NULL for permanent contracts',
  `salary` decimal(15,2) NOT NULL COMMENT 'Base salary defined in contract',
  `salary_currency` varchar(3) NOT NULL DEFAULT 'VND', -- Added currency
  `status` enum('draft','active','expired','terminated') NOT NULL DEFAULT 'draft',
  `file_url` varchar(512) DEFAULT NULL COMMENT 'Link to scanned contract PDF',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_contracts_employee_idx` (`employee_id`),
  KEY `idx_contract_status` (`status`),
  KEY `idx_contract_end_date` (`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `degrees` (Formal Education)
--
CREATE TABLE `degrees` (
  `degree_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `degree_name` varchar(255) NOT NULL COMMENT 'e.g., Bachelor of Science',
  `major` varchar(255) DEFAULT NULL, -- Added major
  `institution` varchar(255) NOT NULL, -- Renamed from validity?
  `graduation_date` date NOT NULL, -- Renamed from issue_date
  `gpa` decimal(4,2) DEFAULT NULL, -- Added GPA
  `attachment_url` varchar(512) DEFAULT NULL COMMENT 'Link to scanned degree',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`degree_id`),
  KEY `fk_degrees_employee_idx` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `certificates` (Professional Certifications)
--
CREATE TABLE `certificates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `issuing_organization` varchar(255) NOT NULL,
  `issue_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `credential_id` varchar(100) DEFAULT NULL,
  `file_url` varchar(512) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_certificates_employee_idx` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `insurance`
--
CREATE TABLE `insurance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `insurance_type` varchar(50) NOT NULL COMMENT 'e.g., Social, Health, Unemployment',
  `policy_number` varchar(100) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `employee_contribution` decimal(15,2) DEFAULT NULL, -- Split contributions
  `employer_contribution` decimal(15,2) DEFAULT NULL, -- Split contributions
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_insurance_employee_idx` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `salary_history`
--
CREATE TABLE `salary_history` (
  `salary_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `effective_date` date NOT NULL,
  `previous_salary` decimal(15,2) DEFAULT NULL, -- Added for tracking changes
  `new_salary` decimal(15,2) NOT NULL,
  `salary_currency` varchar(3) NOT NULL DEFAULT 'VND', -- Added currency
  `reason` text DEFAULT NULL COMMENT 'Reason for change (e.g., Promotion, Annual Review)',
  `decision_attachment_url` varchar(512) DEFAULT NULL,
  `recorded_by_user_id` int(11) DEFAULT NULL COMMENT 'User who recorded the change',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`salary_history_id`),
  KEY `fk_salaryhist_employee_idx` (`employee_id`),
  KEY `fk_salaryhist_recorder_idx` (`recorded_by_user_id`),
  KEY `idx_salaryhist_effective_date` (`effective_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `employee_positions` (Track position history)
--
CREATE TABLE `employee_positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL, -- Added department context
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL COMMENT 'NULL if current position',
  `is_current` tinyint(1) GENERATED ALWAYS AS (if(`end_date` is null,1,0)) STORED, -- Generated column
  `reason_for_change` text DEFAULT NULL, -- Added context
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_emppos_employee_idx` (`employee_id`),
  KEY `fk_emppos_position_idx` (`position_id`),
  KEY `fk_emppos_department_idx` (`department_id`),
  KEY `idx_emppos_current_employee` (`employee_id`,`is_current`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------
-- Operational HR Tables (Attendance, Leave, Payroll, etc.)
-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--
CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `check_in_time` time DEFAULT NULL, -- Split times
  `check_out_time` time DEFAULT NULL, -- Split times
  `work_duration_hours` decimal(4,2) DEFAULT NULL, -- Calculated duration
  `attendance_symbol` varchar(10) DEFAULT NULL COMMENT 'e.g., P (Present), A (Absent), L (Leave), WFH',
  `notes` text DEFAULT NULL,
  `recorded_at` datetime DEFAULT current_timestamp(), -- When record was created/modified
  `source` varchar(50) DEFAULT 'manual' COMMENT 'e.g., manual, biometric, system', -- Added source
  PRIMARY KEY (`attendance_id`),
  UNIQUE KEY `uq_attendance_employee_date` (`employee_id`, `attendance_date`),
  KEY `fk_attendance_employee_idx` (`employee_id`),
  KEY `idx_attendance_date` (`attendance_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `leaves`
--
CREATE TABLE `leaves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `leave_type` varchar(50) NOT NULL COMMENT 'e.g., Annual, Sick, Unpaid, Maternity',
  `start_date` datetime NOT NULL COMMENT 'Include time for partial days',
  `end_date` datetime NOT NULL COMMENT 'Include time for partial days',
  `leave_duration_days` decimal(4,1) NOT NULL, -- Calculated duration
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected','cancelled') DEFAULT 'pending',
  `approved_by_user_id` int(11) DEFAULT NULL COMMENT 'User who approved/rejected',
  `approver_comments` text DEFAULT NULL, -- Added comments
  `attachment_url` varchar(512) DEFAULT NULL, -- Added attachment URL
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_leaves_employee_idx` (`employee_id`),
  KEY `fk_leaves_approver_idx` (`approved_by_user_id`),
  KEY `idx_leave_status_employee` (`employee_id`, `status`),
  KEY `idx_leave_dates` (`start_date`, `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `payroll` (Consolidated from payroll and payrolls)
--
CREATE TABLE `payroll` (
  `payroll_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `pay_period_start` date NOT NULL, -- Changed to period
  `pay_period_end` date NOT NULL, -- Changed to period
  `work_days_payable` decimal(4,1) NOT NULL COMMENT 'Days eligible for payment in period',
  `base_salary_period` decimal(15,2) NOT NULL COMMENT 'Base salary for the pay period',
  `allowances_total` decimal(15,2) DEFAULT 0.00,
  `bonuses_total` decimal(15,2) DEFAULT 0.00,
  `deductions_total` decimal(15,2) DEFAULT 0.00,
  `gross_salary` decimal(15,2) NOT NULL,
  `tax_deduction` decimal(15,2) DEFAULT 0.00, -- Specific tax
  `insurance_deduction` decimal(15,2) DEFAULT 0.00, -- Specific insurance
  `net_salary` decimal(15,2) NOT NULL COMMENT 'Take-home pay',
  `payment_date` date DEFAULT NULL, -- Date salary paid
  `status` enum('pending','calculated','approved','paid','rejected') DEFAULT 'pending',
  `generated_at` datetime DEFAULT current_timestamp(),
  `generated_by_user_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`payroll_id`),
  UNIQUE KEY `uq_payroll_employee_period` (`employee_id`, `pay_period_start`, `pay_period_end`),
  KEY `fk_payroll_employee_idx` (`employee_id`),
  KEY `fk_payroll_generator_idx` (`generated_by_user_id`),
  KEY `idx_payroll_period` (`pay_period_start`, `pay_period_end`),
  KEY `idx_payroll_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `bonuses` (Individual bonus records)
--
CREATE TABLE `bonuses` (
  `bonus_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `bonus_type` varchar(50) NOT NULL COMMENT 'e.g., Performance, Referral, Spot',
  `amount` decimal(15,2) DEFAULT NULL,
  `effective_date` date NOT NULL COMMENT 'Date bonus applies',
  `payroll_id` int(11) DEFAULT NULL COMMENT 'FK to payroll where this bonus was included',
  `reason` text NOT NULL,
  `status` enum('pending','approved','paid','rejected') DEFAULT 'pending',
  `approved_by_user_id` int(11) DEFAULT NULL, -- Renamed from added_by_user_id
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`bonus_id`),
  KEY `fk_bonuses_employee_idx` (`employee_id`),
  KEY `fk_bonuses_approver_idx` (`approved_by_user_id`),
  KEY `fk_bonuses_payroll_idx` (`payroll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `work_schedules`
--
CREATE TABLE `work_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `work_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `break_duration_minutes` int(11) DEFAULT 0, -- Added break time
  `schedule_type` enum('normal','overtime','shift','flexible') DEFAULT 'normal',
  `notes` text DEFAULT NULL, -- Added notes
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_schedule_employee_date` (`employee_id`, `work_date`),
  KEY `fk_worksch_employee_idx` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Performance & Development
-- --------------------------------------------------------

--
-- Table structure for table `performances` (Consolidated from performances and evaluations)
--
CREATE TABLE `performances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `reviewer_user_id` int(11) NOT NULL COMMENT 'User performing the review',
  `review_period_start` date NOT NULL,
  `review_period_end` date NOT NULL,
  `review_date` date NOT NULL,
  `performance_score` decimal(4,2) DEFAULT NULL,
  `strengths` text DEFAULT NULL,
  `areas_for_improvement` text DEFAULT NULL, -- Renamed weaknesses
  `employee_comments` text DEFAULT NULL, -- Added employee comments
  `reviewer_comments` text DEFAULT NULL, -- Renamed comments
  `goals_for_next_period` text DEFAULT NULL, -- Renamed goals
  `status` enum('draft','submitted','acknowledged','completed') DEFAULT 'draft',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_perf_employee_idx` (`employee_id`),
  KEY `fk_perf_reviewer_idx` (`reviewer_user_id`),
  KEY `idx_perf_period` (`review_period_start`, `review_period_end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `kpi` (Key Performance Indicators)
--
CREATE TABLE `kpi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `metric_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL, -- Added description
  `target_value` decimal(15,2) DEFAULT NULL,
  `actual_value` decimal(15,2) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL, -- Added unit (e.g., %, units, hours)
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_kpi_employee_idx` (`employee_id`),
  KEY `idx_kpi_period` (`period_start`, `period_end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `training_registrations`
--
CREATE TABLE `training_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `registration_date` date NOT NULL,
  `status` enum('registered','attended','completed','failed','cancelled') NOT NULL DEFAULT 'registered',
  `completion_date` date DEFAULT NULL, -- Added completion date
  `score` decimal(5,2) DEFAULT NULL, -- Added score
  `feedback` text DEFAULT NULL, -- Added feedback
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_reg_employee_course` (`employee_id`, `course_id`), -- Prevent duplicate registration
  KEY `fk_reg_employee_idx` (`employee_id`),
  KEY `fk_reg_course_idx` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `training_evaluations` (Evaluation *of* the training/course)
--
CREATE TABLE `training_evaluations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registration_id` int(11) NOT NULL COMMENT 'Link to the specific registration being evaluated',
  `evaluator_employee_id` int(11) NOT NULL COMMENT 'Employee providing the evaluation',
  `evaluation_date` date NOT NULL,
  `rating_content` int(11) DEFAULT NULL COMMENT 'Scale (e.g., 1-5)',
  `rating_instructor` int(11) DEFAULT NULL COMMENT 'Scale (e.g., 1-5)',
  `rating_materials` int(11) DEFAULT NULL COMMENT 'Scale (e.g., 1-5)',
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_eval_registration_evaluator` (`registration_id`, `evaluator_employee_id`),
  KEY `fk_eval_registration_idx` (`registration_id`),
  KEY `fk_eval_evaluator_idx` (`evaluator_employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Recruitment & Onboarding
-- --------------------------------------------------------

--
-- Table structure for table `recruitment_campaigns`
--
CREATE TABLE `recruitment_campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('draft','active','closed','cancelled') NOT NULL DEFAULT 'draft',
  `created_by_user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_reccamp_creator_idx` (`created_by_user_id`),
  KEY `idx_reccamp_status` (`status`),
  KEY `idx_reccamp_dates` (`start_date`, `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `job_positions` (Specific Job Openings/Postings - may differ from general `positions`)
--
CREATE TABLE `job_positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) DEFAULT NULL, -- Link to a campaign (optional)
  `position_id` int(11) NOT NULL, -- Link to the general position role
  `title_override` varchar(255) DEFAULT NULL COMMENT 'Specific title for this opening, if different',
  `department_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `responsibilities` text DEFAULT NULL,
  `salary_range_min` decimal(15,2) DEFAULT NULL,
  `salary_range_max` decimal(15,2) DEFAULT NULL,
  `status` enum('draft','open','closed','on_hold') NOT NULL DEFAULT 'draft',
  `posting_date` date DEFAULT NULL,
  `closing_date` date DEFAULT NULL,
  `hiring_manager_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_jobpos_campaign_idx` (`campaign_id`),
  KEY `fk_jobpos_position_idx` (`position_id`),
  KEY `fk_jobpos_department_idx` (`department_id`),
  KEY `fk_jobpos_manager_idx` (`hiring_manager_user_id`),
  KEY `idx_jobpos_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `job_applications`
--
CREATE TABLE `job_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_position_id` int(11) NOT NULL COMMENT 'FK to the specific job opening',
  -- `campaign_id` int(11) NOT NULL, -- Redundant if job_position links to campaign
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `resume_url` varchar(512) DEFAULT NULL,
  `cover_letter_url` varchar(512) DEFAULT NULL, -- Changed to URL
  `source` varchar(100) DEFAULT NULL COMMENT 'e.g., LinkedIn, Website, Referral', -- Added source
  `status` enum('new','reviewing','shortlisted','interviewing','assessment','offered','hired','rejected','withdrawn') NOT NULL DEFAULT 'new',
  `applied_at` datetime DEFAULT current_timestamp(), -- Renamed created_at
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_application_email_job` (`email`, `job_position_id`),
  KEY `fk_jobapp_jobpos_idx` (`job_position_id`),
  KEY `idx_jobapp_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `interviews`
--
CREATE TABLE `interviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_application_id` int(11) NOT NULL,
  `interviewer_employee_id` int(11) NOT NULL,
  `interview_datetime` datetime NOT NULL,
  `duration_minutes` int(11) DEFAULT 60, -- Added duration
  `location` varchar(255) DEFAULT NULL, -- Added location (or link for virtual)
  `type` varchar(50) NOT NULL COMMENT 'e.g., Screening, Technical, HR, Panel',
  `status` enum('scheduled','completed','cancelled','rescheduled') NOT NULL DEFAULT 'scheduled',
  `interviewer_feedback` text DEFAULT NULL, -- Renamed notes
  `candidate_feedback` text DEFAULT NULL, -- Added candidate feedback
  `score` decimal(4,2) DEFAULT NULL, -- Added score
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_interviews_application_idx` (`job_application_id`),
  KEY `fk_interviews_interviewer_idx` (`interviewer_employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `onboarding`
--
CREATE TABLE `onboarding` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `buddy_employee_id` int(11) DEFAULT NULL COMMENT 'Assigned buddy',
  `checklist_items_json` json DEFAULT NULL COMMENT 'JSON array of tasks/items',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_onboarding_employee` (`employee_id`), -- Usually one onboarding plan per employee
  KEY `fk_onboarding_employee_idx` (`employee_id`),
  KEY `fk_onboarding_buddy_idx` (`buddy_employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Projects & Tasks
-- --------------------------------------------------------

--
-- Table structure for table `projects`
--
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `project_code` varchar(50) DEFAULT NULL, -- Added project code
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('planning','active','completed','on_hold','cancelled') NOT NULL DEFAULT 'planning',
  `manager_employee_id` int(11) DEFAULT NULL,
  `budget` decimal(15,2) DEFAULT NULL, -- Added budget
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_project_code` (`project_code`),
  KEY `fk_projects_manager_idx` (`manager_employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `project_tasks`
--
CREATE TABLE `project_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `assigned_to_employee_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL, -- Added start date
  `due_date` date DEFAULT NULL,
  `estimated_hours` decimal(5,2) DEFAULT NULL, -- Added estimate
  `actual_hours` decimal(5,2) DEFAULT NULL, -- Added actual
  `priority` enum('low','medium','high','critical') DEFAULT 'medium', -- Changed to ENUM
  `status` enum('pending','in_progress','completed','blocked','cancelled') NOT NULL DEFAULT 'pending',
  `parent_task_id` int(11) DEFAULT NULL, -- Added for subtasks
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_projtasks_project_idx` (`project_id`),
  KEY `fk_projtasks_assignee_idx` (`assigned_to_employee_id`),
  KEY `fk_projtasks_parent_idx` (`parent_task_id`),
  KEY `idx_projtasks_status` (`status`),
  KEY `idx_projtasks_due_date` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `project_resources` (Link employees/assets to projects)
--
CREATE TABLE `project_resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `resource_type` enum('employee','asset','other') NOT NULL,
  `resource_id` int(11) NOT NULL COMMENT 'FK to employees.id or assets.id based on type',
  `role` varchar(100) DEFAULT NULL COMMENT 'Role if employee resource',
  `allocation_percentage` int(11) DEFAULT NULL COMMENT 'Percentage of time/resource allocated',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_projres_project_res` (`project_id`, `resource_type`, `resource_id`),
  KEY `fk_projres_project_idx` (`project_id`),
  KEY `idx_projres_resource` (`resource_type`, `resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `tasks` (General tasks, not project specific)
--
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `assigned_to_employee_id` int(11) DEFAULT NULL, -- Changed from user_id
  `assigned_by_user_id` int(11) NOT NULL,
  `due_date` date DEFAULT NULL,
  `priority` enum('low','medium','high','critical') DEFAULT 'medium',
  `status` enum('pending','in_progress','completed','blocked','cancelled') DEFAULT 'pending',
  `related_entity_type` varchar(50) DEFAULT NULL COMMENT 'e.g., employee, onboarding, performance',
  `related_entity_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_tasks_assignee_idx` (`assigned_to_employee_id`),
  KEY `fk_tasks_assigner_idx` (`assigned_by_user_id`),
  KEY `idx_tasks_status` (`status`),
  KEY `idx_tasks_due_date` (`due_date`),
  KEY `idx_tasks_related_entity` (`related_entity_type`, `related_entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Asset Management
-- --------------------------------------------------------

--
-- Table structure for table `asset_assignments` (Replaces equipment_assignments)
--
CREATE TABLE `asset_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `assigned_date` date NOT NULL,
  `expected_return_date` date DEFAULT NULL,
  `actual_return_date` date DEFAULT NULL,
  `condition_out` text DEFAULT NULL, -- Added condition tracking
  `condition_in` text DEFAULT NULL, -- Added condition tracking
  `status` enum('active','returned','lost','damaged') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `assigned_by_user_id` int(11) DEFAULT NULL, -- Added assigner
  `returned_to_user_id` int(11) DEFAULT NULL, -- Added receiver
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_assetassign_asset_idx` (`asset_id`),
  KEY `fk_assetassign_employee_idx` (`employee_id`),
  KEY `fk_assetassign_assigner_idx` (`assigned_by_user_id`),
  KEY `fk_assetassign_receiver_idx` (`returned_to_user_id`),
  KEY `idx_assetassign_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `asset_maintenance`
--
CREATE TABLE `asset_maintenance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `maintenance_type` enum('preventive','corrective','inspection','upgrade') NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` datetime NOT NULL, -- Changed to datetime
  `end_date` datetime DEFAULT NULL, -- Changed to datetime
  `cost` decimal(10,2) DEFAULT NULL,
  `vendor` varchar(255) DEFAULT NULL, -- Added vendor
  `status` enum('scheduled','in_progress','completed','cancelled','failed') NOT NULL DEFAULT 'scheduled',
  `created_by_user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_assetmaint_asset_idx` (`asset_id`),
  KEY `fk_assetmaint_creator_idx` (`created_by_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Documents
-- --------------------------------------------------------

--
-- Table structure for table `documents`
--
CREATE TABLE `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_url` varchar(512) NOT NULL COMMENT 'URL to the primary/latest version',
  `document_type` varchar(50) NOT NULL COMMENT 'e.g., Policy, Guideline, Template, Report',
  `uploaded_by_user_id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL COMMENT 'Department this document belongs to (optional)',
  `access_level` enum('public','internal','restricted') DEFAULT 'internal', -- Added access control
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_docs_uploader_idx` (`uploaded_by_user_id`),
  KEY `fk_docs_department_idx` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `document_versions`
--
CREATE TABLE `document_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `version_number` varchar(20) NOT NULL,
  `file_url` varchar(512) NOT NULL COMMENT 'URL to this specific version',
  `changes_description` text DEFAULT NULL,
  `created_by_user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_docver_doc_version` (`document_id`, `version_number`),
  KEY `fk_docver_document_idx` (`document_id`),
  KEY `fk_docver_creator_idx` (`created_by_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Security & Logging
-- --------------------------------------------------------

--
-- Table structure for table `role_permissions` (Link Roles to Permissions)
--
CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `fk_roleperm_permission_idx` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `email_verification_tokens`
--
CREATE TABLE `email_verification_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_emailver_token` (`token`),
  KEY `fk_emailver_user_idx` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `password_reset_tokens`
--
CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pwdreset_token` (`token`),
  KEY `fk_pwdreset_user_idx` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `sessions` (For web session management)
--
CREATE TABLE `sessions` (
  `session_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `expires` int(11) UNSIGNED NOT NULL,
  `data` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`session_id`),
  KEY `idx_sessions_expires` (`expires`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `activities` (User Activity Log)
--
CREATE TABLE `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'User performing action, NULL if system',
  `type` varchar(100) NOT NULL COMMENT 'e.g., LOGIN, LOGOUT, UPDATE_PROFILE, CREATE_LEAVE',
  `description` text NOT NULL,
  `target_entity` varchar(100) DEFAULT NULL COMMENT 'e.g., Employee, LeaveRequest',
  `target_entity_id` int(11) DEFAULT NULL,
  `status` enum('success','warning','error','info') DEFAULT 'info',
  `user_agent` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_activities_user_idx` (`user_id`),
  KEY `idx_activities_type` (`type`),
  KEY `idx_activities_target` (`target_entity`, `target_entity_id`),
  KEY `idx_activities_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `audit_logs` (Detailed Change Tracking)
--
CREATE TABLE `audit_logs` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'User making the change, NULL if system',
  `action_type` enum('CREATE','UPDATE','DELETE') NOT NULL,
  `target_entity` varchar(100) NOT NULL,
  `target_entity_id` int(11) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'JSON containing old and new values' CHECK (json_valid(`details`)),
  PRIMARY KEY (`log_id`),
  KEY `idx_audit_user_id` (`user_id`),
  KEY `idx_audit_timestamp` (`timestamp`),
  KEY `idx_audit_action_type` (`action_type`),
  KEY `idx_audit_target` (`target_entity`, `target_entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `system_logs` (Application/Server Level Logs)
--
CREATE TABLE `system_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `log_type` varchar(50) NOT NULL COMMENT 'e.g., Application, Database, Security',
  `log_level` enum('debug','info','notice','warning','error','critical','alert','emergency') NOT NULL,
  `message` text NOT NULL,
  `context` json DEFAULT NULL COMMENT 'Additional context as JSON',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'User associated with the event, if applicable',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_syslog_type` (`log_type`),
  KEY `idx_syslog_level` (`log_level`),
  KEY `fk_syslog_user_idx` (`user_id`),
  KEY `idx_syslog_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `login_attempts` (Track failed login attempts)
--
CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `username_attempted` varchar(100) DEFAULT NULL, -- Added username
  `attempt_time` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_loginatt_ip_time` (`ip_address`,`attempt_time`),
  KEY `idx_loginatt_user_time` (`username_attempted`,`attempt_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `rate_limits`
--
CREATE TABLE `rate_limits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `endpoint` varchar(255) NOT NULL,
  `request_count` int(11) NOT NULL DEFAULT 1,
  `window_start` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ratelimit_ip_endpoint_window` (`ip_address`,`endpoint`,`window_start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `backup_logs`
--
CREATE TABLE `backup_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `backup_type` enum('full','incremental','differential') NOT NULL,
  `file_path` varchar(512) NOT NULL,
  `file_size_bytes` bigint(20) NOT NULL,
  `status` enum('success','failed','in_progress') NOT NULL,
  `error_message` text DEFAULT NULL,
  `duration_seconds` int(11) DEFAULT NULL, -- Added duration
  `created_by_user_id` int(11) DEFAULT NULL COMMENT 'User initiating backup, NULL if automated',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_backuplog_creator_idx` (`created_by_user_id`),
  KEY `idx_backuplog_status` (`status`),
  KEY `idx_backuplog_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------
-- System Configuration & Reporting
-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--
CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_type` enum('string','integer','boolean','json','array') NOT NULL DEFAULT 'string',
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 if readable by non-admins (e.g., site name)',
  `created_by_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_setting_key` (`setting_key`),
  KEY `fk_sysset_creator_idx` (`created_by_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `report_templates`
--
CREATE TABLE `report_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `template_type` varchar(50) NOT NULL COMMENT 'e.g., SQL, Predefined',
  `query_or_definition` text NOT NULL COMMENT 'SQL query or definition for predefined reports',
  `parameters` json DEFAULT NULL COMMENT 'JSON defining required parameters',
  `output_format` enum('csv','pdf','html','json') DEFAULT 'csv', -- Added format
  `created_by_user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_reptemp_creator_idx` (`created_by_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `report_schedules`
--
CREATE TABLE `report_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL, -- Added name for schedule
  `schedule_type` enum('daily','weekly','monthly','quarterly','yearly','on_demand') NOT NULL,
  `schedule_time` time DEFAULT NULL COMMENT 'Time of day for scheduled runs',
  `schedule_day_of_week` tinyint(1) DEFAULT NULL COMMENT '1=Sun, 7=Sat (for weekly)',
  `schedule_day_of_month` tinyint(2) DEFAULT NULL COMMENT '1-31 (for monthly)',
  `recipients_json` json NOT NULL COMMENT 'JSON array of email addresses or user IDs',
  `parameters_json` json DEFAULT NULL COMMENT 'JSON of parameters to run with', -- Added parameters
  `status` enum('active','inactive','error') NOT NULL DEFAULT 'active',
  `last_run_at` datetime DEFAULT NULL,
  `next_run_at` datetime DEFAULT NULL,
  `created_by_user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_repsched_template_idx` (`template_id`),
  KEY `fk_repsched_creator_idx` (`created_by_user_id`),
  KEY `idx_repsched_next_run` (`next_run_at`),
  KEY `idx_repsched_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `report_executions`
--
CREATE TABLE `report_executions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT, -- Changed to bigint
  `template_id` int(11) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL COMMENT 'Null if run manually',
  `parameters_json` json DEFAULT NULL COMMENT 'Parameters used for this execution',
  `status` enum('pending','running','completed','failed','cancelled') NOT NULL DEFAULT 'pending',
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `result_url` varchar(512) DEFAULT NULL COMMENT 'URL to the generated report file',
  `result_metadata_json` json DEFAULT NULL COMMENT 'e.g., row count, file size', -- Added metadata
  `error_message` text DEFAULT NULL,
  `executed_by_user_id` int(11) DEFAULT NULL COMMENT 'User who triggered manual run',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_repexec_template_idx` (`template_id`),
  KEY `fk_repexec_schedule_idx` (`schedule_id`),
  KEY `fk_repexec_executor_idx` (`executed_by_user_id`),
  KEY `idx_repexec_status` (`status`),
  KEY `idx_repexec_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Notifications
-- --------------------------------------------------------
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'Recipient user',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info' COMMENT 'e.g., info, warning, success, reminder',
  `related_entity_type` varchar(50) DEFAULT NULL,
  `related_entity_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` datetime DEFAULT NULL, -- Added read timestamp
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_notifications_user_idx` (`user_id`),
  KEY `idx_notifications_read_user` (`user_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Add Foreign Key Constraints
-- (Done after all tables are created to avoid order issues)
-- --------------------------------------------------------

-- Constraints for `departments`
ALTER TABLE `departments`
  ADD CONSTRAINT `fk_departments_manager` FOREIGN KEY (`manager_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_departments_parent` FOREIGN KEY (`parent_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Constraints for `positions`
ALTER TABLE `positions`
  ADD CONSTRAINT `fk_positions_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE; -- Cascade delete positions if dept is deleted

-- Constraints for `users`
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE; -- Prevent deleting role if users exist

-- Constraints for `employees`
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_employees_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE, -- Cascade delete employee if user account deleted
  ADD CONSTRAINT `fk_employees_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_employees_position` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Constraints for `user_profiles`
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `fk_user_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Constraints for `family_members`
ALTER TABLE `family_members`
  ADD CONSTRAINT `fk_family_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Constraints for `contracts`
ALTER TABLE `contracts`
  ADD CONSTRAINT `fk_contracts_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Constraints for `degrees`
ALTER TABLE `degrees`
  ADD CONSTRAINT `fk_degrees_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Constraints for `certificates`
ALTER TABLE `certificates`
  ADD CONSTRAINT `fk_certificates_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Constraints for `insurance`
ALTER TABLE `insurance`
  ADD CONSTRAINT `fk_insurance_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Constraints for `salary_history`
ALTER TABLE `salary_history`
  ADD CONSTRAINT `fk_salaryhist_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_salaryhist_recorder` FOREIGN KEY (`recorded_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Constraints for `employee_positions`
ALTER TABLE `employee_positions`
  ADD CONSTRAINT `fk_emppos_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_emppos_position` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_emppos_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- Constraints for `attendance`
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_attendance_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Constraints for `leaves`
ALTER TABLE `leaves`
  ADD CONSTRAINT `fk_leaves_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_leaves_approver` FOREIGN KEY (`approved_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Constraints for `payroll`
ALTER TABLE `payroll`
  ADD CONSTRAINT `fk_payroll_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payroll_generator` FOREIGN KEY (`generated_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Constraints for `bonuses`
ALTER TABLE `bonuses`
  ADD CONSTRAINT `fk_bonuses_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bonuses_approver` FOREIGN KEY (`approved_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bonuses_payroll` FOREIGN KEY (`payroll_id`) REFERENCES `payroll` (`payroll_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Constraints for `work_schedules`
ALTER TABLE `work_schedules`
  ADD CONSTRAINT `fk_worksch_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Constraints for `performances`
ALTER TABLE `performances`
  ADD CONSTRAINT `fk_perf_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_perf_reviewer` FOREIGN KEY (`reviewer_user_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- Constraints for `kpi`
ALTER TABLE `kpi`
  ADD CONSTRAINT `fk_kpi_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Constraints for `training_registrations`
ALTER TABLE `training_registrations`
  ADD CONSTRAINT `fk_reg_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reg_course` FOREIGN KEY (`course_id`) REFERENCES `training_courses` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- Constraints for `training_evaluations`
ALTER TABLE `training_evaluations`
  ADD CONSTRAINT `fk_eval_registration` FOREIGN KEY (`registration_id`) REFERENCES `training_registrations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eval_evaluator` FOREIGN KEY (`evaluator_employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Constraints for `recruitment_campaigns`
ALTER TABLE `recruitment_campaigns`
  ADD CONSTRAINT `fk_reccamp_creator` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- Constraints for `job_positions`
ALTER TABLE `job_positions`
  ADD CONSTRAINT `fk_jobpos_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `recruitment_campaigns` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_jobpos_position` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_jobpos_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_jobpos_manager` FOREIGN KEY (`hiring_manager_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Constraints for `job_applications`
ALTER TABLE `job_applications`
  ADD CONSTRAINT `fk_jobapp_jobpos` FOREIGN KEY (`job_position_id`) REFERENCES `job_positions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE; -- Prevent deleting job posting if applications exist

-- Constraints for `interviews`
ALTER TABLE `interviews`
  ADD CONSTRAINT `fk_interviews_application` FOREIGN KEY (`job_application_id`) REFERENCES `job_applications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_interviews_interviewer` FOREIGN KEY (`interviewer_employee_id`) REFERENCES `employees` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- Constraints for `onboarding`
ALTER TABLE `onboarding`
  ADD CONSTRAINT `fk_onboarding_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_onboarding_buddy` FOREIGN KEY (`buddy_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Constraints for `projects`
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_projects_manager` FOREIGN KEY (`manager_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Constraints for `project_tasks`
ALTER TABLE `project_tasks`
  ADD CONSTRAINT `fk_projtasks_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_projtasks_assignee` FOREIGN KEY (`assigned_to_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_projtasks_parent` FOREIGN KEY (`parent_task_id`) REFERENCES `project_tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Constraints for `project_resources`
ALTER TABLE `project_resources`
  ADD CONSTRAINT `fk_projres_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
  -- Note: FK for resource_id needs application logic based on resource_type

-- Constraints for `tasks`
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_tasks_assignee` FOREIGN KEY (`assigned_to_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tasks_assigner` FOREIGN KEY (`assigned_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- Constraints for `asset_assignments`
ALTER TABLE `asset_assignments`
  ADD CONSTRAINT `fk_assetassign_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_assetassign_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_assetassign_assigner` FOREIGN KEY (`assigned_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_assetassign_receiver` FOREIGN KEY (`returned_to_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Constraints for `asset_maintenance`
ALTER TABLE `asset_maintenance`
  ADD CONSTRAINT `fk_assetmaint_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_assetmaint_creator` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- Constraints for `documents`
ALTER TABLE `documents`
  ADD CONSTRAINT `fk_docs_uploader` FOREIGN KEY (`uploaded_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_docs_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Constraints for `document_versions`
ALTER TABLE `document_versions`
  ADD CONSTRAINT `fk_docver_document` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_docver_creator` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- Constraints for `role_permissions`
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_roleperm_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_roleperm_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Constraints for `email_verification_tokens`
ALTER TABLE `email_verification_tokens`
  ADD CONSTRAINT `fk_emailver_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Constraints for `password_reset_tokens`
ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `fk_pwdreset_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Constraints for `activities`
ALTER TABLE `activities`
  ADD CONSTRAINT `fk_activities_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Constraints for `audit_logs`
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Constraints for `system_logs`
ALTER TABLE `system_logs`
  ADD CONSTRAINT `fk_syslog_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Constraints for `backup_logs`
ALTER TABLE `backup_logs`
  ADD CONSTRAINT `fk_backuplog_creator` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Constraints for `system_settings`
ALTER TABLE `system_settings`
  ADD CONSTRAINT `fk_sysset_creator` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Constraints for `report_templates`
ALTER TABLE `report_templates`
  ADD CONSTRAINT `fk_reptemp_creator` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- Constraints for `report_schedules`
ALTER TABLE `report_schedules`
  ADD CONSTRAINT `fk_repsched_template` FOREIGN KEY (`template_id`) REFERENCES `report_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_repsched_creator` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- Constraints for `report_executions`
ALTER TABLE `report_executions`
  ADD CONSTRAINT `fk_repexec_template` FOREIGN KEY (`template_id`) REFERENCES `report_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_repexec_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `report_schedules` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_repexec_executor` FOREIGN KEY (`executed_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Constraints for `notifications`
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Deprecated/Removed Tables (Original script had these):
-- candidates (Consider using job_applications directly)
-- trainings (Replaced by training_courses)
-- employee_trainings (Replaced by training_registrations)
-- payrolls (Merged into payroll)
-- evaluations (Merged into performances)
-- equipment (Merged into assets)
-- equipment_assignments (Merged into asset_assignments)
-- recruitment (Covered by recruitment_campaigns/job_positions/job_applications)

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;