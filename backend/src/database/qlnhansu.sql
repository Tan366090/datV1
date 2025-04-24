-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 21, 2025 lúc 10:05 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `qlnhansu`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `status` enum('success','warning','error','active') DEFAULT 'success',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `activities`
--

INSERT INTO `activities` (`id`, `user_id`, `type`, `description`, `user_agent`, `ip_address`, `status`, `created_at`) VALUES
(1, 1, 'login', 'User logged in', NULL, NULL, 'success', '2025-04-21 02:23:14'),
(2, 1, 'login', 'Admin logged into the system', 'Mozilla/5.0 (Windows NT 10.0)', '192.168.1.100', 'success', '2025-04-21 02:24:14'),
(3, 2, 'update_profile', 'Updated personal information', 'Mozilla/5.0 (Windows NT 10.0)', '192.168.1.101', 'success', '2025-04-21 02:24:24'),
(4, 3, 'view_document', 'Accessed employee handbook', 'Mozilla/5.0 (Windows NT 10.0)', '192.168.1.102', 'success', '2025-04-21 02:24:40'),
(5, 1, 'approve_leave', 'Approved leave request for employee ID 2', 'Mozilla/5.0 (Windows NT 10.0)', '192.168.1.100', 'success', '2025-04-21 02:24:51'),
(6, 6, 'login', 'Operations Manager logged in', 'Mozilla/5.0 (Windows NT 10.0)', '192.168.1.106', 'success', '2025-04-21 02:25:14'),
(7, 7, 'update_profile', 'Updated HR information', 'Mozilla/5.0 (Windows NT 10.0)', '192.168.1.107', 'success', '2025-04-21 02:25:24'),
(8, 8, 'view_document', 'Accessed development guidelines', 'Mozilla/5.0 (Windows NT 10.0)', '192.168.1.108', 'success', '2025-04-21 02:25:40'),
(9, 9, 'approve_leave', 'Approved leave request for employee ID 3', 'Mozilla/5.0 (Windows NT 10.0)', '192.168.1.109', 'success', '2025-04-21 02:25:51'),
(10, 10, 'login', 'Marketing Specialist logged in', 'Mozilla/5.0 (Windows NT 10.0)', '192.168.1.110', 'success', '2025-04-21 02:26:14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `recorded_at` datetime DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  `attendance_symbol` varchar(10) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ;

--
-- Đang đổ dữ liệu cho bảng `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `user_id`, `attendance_date`, `recorded_at`, `notes`, `attendance_symbol`, `created_at`) VALUES
(1, 1, '2024-03-01', '2024-03-01 08:00:00', 'On time', 'P', '2025-04-21 14:57:56'),
(2, 2, '2024-03-01', '2024-03-01 08:05:00', 'On time', 'P', '2025-04-21 14:57:56'),
(3, 3, '2024-03-01', '2024-03-01 08:10:00', 'On time', 'P', '2025-04-21 14:57:56'),
(4, 4, '2024-03-01', '2024-03-01 08:00:00', 'On time', 'P', '2025-04-21 14:57:56'),
(5, 5, '2024-03-01', '2024-03-01 08:15:00', 'On time', 'P', '2025-04-21 14:57:56'),
(6, 6, '2024-03-01', '2024-03-01 08:00:00', 'On time', 'P', '2025-04-21 14:57:56'),
(7, 7, '2024-03-01', '2024-03-01 08:20:00', 'On time', 'P', '2025-04-21 14:57:56'),
(8, 8, '2024-03-01', '2024-03-01 08:00:00', 'On time', 'P', '2025-04-21 14:57:56'),
(9, 9, '2024-03-01', '2024-03-01 08:25:00', 'On time', 'P', '2025-04-21 14:57:56'),
(10, 10, '2024-03-01', '2024-03-01 08:00:00', 'On time', 'P', '2025-04-21 14:57:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` bigint(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(100) NOT NULL,
  `target_entity` varchar(100) DEFAULT NULL,
  `target_entity_id` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bonuses`
--

CREATE TABLE `bonuses` (
  `bonus_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bonus_type` varchar(20) NOT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `days_off` decimal(4,1) DEFAULT NULL,
  `reason` text NOT NULL,
  `effective_date` date NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `added_by_user_id` int(11) NOT NULL
) ;

--
-- Đang đổ dữ liệu cho bảng `bonuses`
--

INSERT INTO `bonuses` (`bonus_id`, `user_id`, `bonus_type`, `amount`, `days_off`, `reason`, `effective_date`, `created_at`, `added_by_user_id`) VALUES
(1, 1, 'performance', 1000000.00, NULL, 'Outstanding performance Q1 2024', '2024-03-31', '2025-04-21 14:57:56', 1),
(2, 2, 'performance', 800000.00, NULL, 'Good performance Q1 2024', '2024-03-31', '2025-04-21 14:57:56', 1),
(3, 3, 'performance', 800000.00, NULL, 'Good performance Q1 2024', '2024-03-31', '2025-04-21 14:57:56', 1),
(4, 4, 'performance', 800000.00, NULL, 'Good performance Q1 2024', '2024-03-31', '2025-04-21 14:57:56', 1),
(5, 5, 'performance', 800000.00, NULL, 'Good performance Q1 2024', '2024-03-31', '2025-04-21 14:57:56', 1),
(6, 6, 'performance', 800000.00, NULL, 'Good performance Q1 2024', '2024-03-31', '2025-04-21 14:57:56', 1),
(7, 7, 'performance', 500000.00, NULL, 'Good performance Q1 2024', '2024-03-31', '2025-04-21 14:57:56', 1),
(8, 8, 'performance', 600000.00, NULL, 'Good performance Q1 2024', '2024-03-31', '2025-04-21 14:57:56', 1),
(9, 9, 'performance', 500000.00, NULL, 'Good performance Q1 2024', '2024-03-31', '2025-04-21 14:57:56', 1),
(10, 10, 'performance', 500000.00, NULL, 'Good performance Q1 2024', '2024-03-31', '2025-04-21 14:57:56', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `degrees`
--

CREATE TABLE `degrees` (
  `degree_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `degree_name` varchar(255) NOT NULL,
  `issue_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `validity` varchar(50) DEFAULT NULL,
  `attachment_url` varchar(512) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `degrees`
--

INSERT INTO `degrees` (`degree_id`, `user_id`, `degree_name`, `issue_date`, `expiry_date`, `validity`, `attachment_url`, `created_at`, `updated_at`, `is_active`) VALUES
(1, 1, 'Bachelor of Computer Science', '2015-06-15', NULL, 'Permanent', NULL, '2025-04-19 23:00:19', '2025-04-19 23:00:19', 1),
(2, 2, 'Master of Business Administration', '2018-05-20', NULL, 'Permanent', NULL, '2025-04-19 23:00:19', '2025-04-19 23:00:19', 1),
(3, 3, 'Bachelor of Information Technology', '2016-07-10', NULL, 'Permanent', NULL, '2025-04-19 23:00:19', '2025-04-19 23:00:19', 1),
(4, 1, 'Bachelor of Business Administration', '2015-06-15', NULL, 'Permanent', NULL, '2025-04-19 23:06:24', '2025-04-19 23:06:24', 1),
(5, 2, 'Master of Computer Science', '2018-05-20', NULL, 'Permanent', NULL, '2025-04-19 23:06:24', '2025-04-19 23:06:24', 1),
(6, 3, 'Bachelor of Information Technology', '2016-07-10', NULL, 'Permanent', NULL, '2025-04-19 23:06:24', '2025-04-19 23:06:24', 1),
(7, 7, 'Bachelor of Human Resources', '2017-06-15', NULL, 'Permanent', NULL, '2025-04-19 23:00:19', '2025-04-19 23:00:19', 1),
(8, 8, 'Bachelor of Computer Science', '2018-05-20', NULL, 'Permanent', NULL, '2025-04-19 23:00:19', '2025-04-19 23:00:19', 1),
(9, 9, 'Bachelor of Accounting', '2019-07-10', NULL, 'Permanent', NULL, '2025-04-19 23:00:19', '2025-04-19 23:00:19', 1),
(10, 10, 'Bachelor of Marketing', '2018-06-15', NULL, 'Permanent', NULL, '2025-04-19 23:00:19', '2025-04-19 23:00:19', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `manager_id`, `created_at`, `updated_at`, `parent_id`) VALUES
(1, 'Human Resources', 'HR Department', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', NULL),
(2, 'Information Technology', 'IT Department', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', NULL),
(3, 'Finance', 'Finance Department', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', NULL),
(4, 'Marketing', 'Marketing Department', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', NULL),
(5, 'Operations', 'Operations Department', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_url` varchar(512) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `documents`
--

INSERT INTO `documents` (`id`, `title`, `description`, `file_url`, `document_type`, `uploaded_by`, `department_id`, `created_at`, `updated_at`) VALUES
(1, 'Employee Handbook', 'Company policies and procedures', '/documents/handbook.pdf', 'policy', 1, 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(2, 'IT Security Guidelines', 'IT security best practices', '/documents/security.pdf', 'guideline', 2, 2, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(3, 'Financial Procedures', 'Financial management procedures', '/documents/finance.pdf', 'procedure', 3, 3, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(4, 'Marketing Strategy', 'Company marketing strategy', '/documents/marketing.pdf', 'strategy', 4, 4, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(5, 'Operations Manual', 'Operations procedures manual', '/documents/operations.pdf', 'manual', 5, 5, '2025-04-21 14:57:56', '2025-04-21 14:57:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `email_verification_tokens`
--

CREATE TABLE `email_verification_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `employee_code` varchar(20) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `contract_type` varchar(50) DEFAULT NULL,
  `contract_start_date` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `employees`
--

INSERT INTO `employees` (`id`, `user_id`, `employee_code`, `department_id`, `position_id`, `hire_date`, `contract_type`, `contract_start_date`, `contract_end_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'EMP001', 1, 1, '2023-01-01', 'full_time', '2023-01-01', '2025-12-31', 'active', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(2, 2, 'EMP002', 1, 1, '2023-01-01', 'full_time', '2023-01-01', '2025-12-31', 'active', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(3, 3, 'EMP003', 2, 3, '2023-01-01', 'full_time', '2023-01-01', '2025-12-31', 'active', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(4, 4, 'EMP004', 3, 5, '2023-01-01', 'full_time', '2023-01-01', '2025-12-31', 'active', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(5, 5, 'EMP005', 4, 7, '2023-01-01', 'full_time', '2023-01-01', '2025-12-31', 'active', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(6, 6, 'EMP006', 5, 9, '2023-01-01', 'full_time', '2023-01-01', '2025-12-31', 'active', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(7, 7, 'EMP007', 1, 2, '2023-01-01', 'full_time', '2023-01-01', '2025-12-31', 'active', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(8, 8, 'EMP008', 2, 4, '2023-01-01', 'full_time', '2023-01-01', '2025-12-31', 'active', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(9, 9, 'EMP009', 3, 6, '2023-01-01', 'full_time', '2023-01-01', '2025-12-31', 'active', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(10, 10, 'EMP010', 4, 8, '2023-01-01', 'full_time', '2023-01-01', '2025-12-31', 'active', '2025-04-21 14:57:56', '2025-04-21 14:57:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `employee_positions`
--

CREATE TABLE `employee_positions` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `employee_positions`
--

INSERT INTO `employee_positions` (`id`, `employee_id`, `position_id`, `start_date`, `end_date`, `is_current`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2023-01-01', NULL, 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(2, 2, 1, '2023-01-01', NULL, 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(3, 3, 3, '2023-01-01', NULL, 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(4, 4, 5, '2023-01-01', NULL, 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(5, 5, 7, '2023-01-01', NULL, 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(6, 6, 9, '2023-01-01', NULL, 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(7, 7, 2, '2023-01-01', NULL, 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(8, 8, 4, '2023-01-01', NULL, 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(9, 9, 6, '2023-01-01', NULL, 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(10, 10, 8, '2023-01-01', NULL, 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `employee_trainings`
--

CREATE TABLE `employee_trainings` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `training_id` int(11) NOT NULL,
  `status` varchar(20) DEFAULT 'registered',
  `result` varchar(20) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `employee_trainings`
--

INSERT INTO `employee_trainings` (`id`, `employee_id`, `training_id`, `status`, `result`, `feedback`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'registered', NULL, NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(2, 2, 1, 'registered', NULL, NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(3, 3, 2, 'registered', NULL, NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(4, 4, 3, 'registered', NULL, NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(5, 5, 4, 'registered', NULL, NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(6, 6, 5, 'registered', NULL, NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(7, 7, 1, 'registered', NULL, NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(8, 8, 2, 'registered', NULL, NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(9, 9, 3, 'registered', NULL, NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(10, 10, 4, 'registered', NULL, NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `equipment_assignments`
--

CREATE TABLE `equipment_assignments` (
  `id` int(11) NOT NULL,
  `equipment_name` varchar(255) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `assigned_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'assigned',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Đang đổ dữ liệu cho bảng `equipment_assignments`
--

INSERT INTO `equipment_assignments` (`id`, `equipment_name`, `employee_id`, `assigned_date`, `return_date`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Laptop Dell XPS', 1, '2023-01-01', NULL, 'assigned', 'Primary work laptop', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(2, 'MacBook Pro', 2, '2023-01-01', NULL, 'assigned', 'Development machine', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(3, 'Desktop PC', 3, '2023-01-01', NULL, 'assigned', 'Workstation', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(4, 'Laptop HP EliteBook', 4, '2023-01-01', NULL, 'assigned', 'Finance work laptop', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(5, 'MacBook Air', 5, '2023-01-01', NULL, 'assigned', 'Marketing work laptop', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(6, 'Desktop PC', 6, '2023-01-01', NULL, 'assigned', 'Operations workstation', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(7, 'Laptop Lenovo ThinkPad', 7, '2023-01-01', NULL, 'assigned', 'HR work laptop', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(8, 'MacBook Pro', 8, '2023-01-01', NULL, 'assigned', 'Development machine', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(9, 'Laptop Dell Latitude', 9, '2023-01-01', NULL, 'assigned', 'Finance work laptop', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(10, 'MacBook Air', 10, '2023-01-01', NULL, 'assigned', 'Marketing work laptop', '2025-04-21 14:57:56', '2025-04-21 14:57:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `family_members`
--

CREATE TABLE `family_members` (
  `family_member_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `member_name` varchar(255) NOT NULL,
  `relationship` varchar(100) NOT NULL,
  `year_of_birth` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `family_members`
--

INSERT INTO `family_members` (`family_member_id`, `profile_id`, `member_name`, `relationship`, `year_of_birth`, `created_at`, `updated_at`) VALUES
(1, 1, 'Jane Doe', 'Spouse', 1992, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(2, 2, 'John Smith', 'Spouse', 1990, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(3, 3, 'Mary Wilson', 'Spouse', 1989, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(4, 4, 'Sarah Brown', 'Spouse', 1991, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(5, 5, 'David Lee', 'Spouse', 1988, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(6, 6, 'Lisa Chen', 'Spouse', 1993, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(7, 7, 'Mike Johnson', 'Spouse', 1994, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(8, 8, 'Anna Davis', 'Spouse', 1992, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(9, 9, 'Tom Wilson', 'Spouse', 1991, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(10, 10, 'Peter Brown', 'Spouse', 1990, '2025-04-21 14:57:56', '2025-04-21 14:57:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `holidays`
--

CREATE TABLE `holidays` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `description` text DEFAULT NULL,
  `is_recurring` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `holidays`
--

INSERT INTO `holidays` (`id`, `name`, `date`, `description`, `is_recurring`, `created_at`, `updated_at`) VALUES
(1, 'New Year', '2024-01-01', 'New Year Day', 1, '2025-04-19 23:06:24', '2025-04-19 23:06:24'),
(2, 'Tet Holiday', '2024-02-10', 'Lunar New Year', 1, '2025-04-19 23:06:24', '2025-04-19 23:06:24'),
(3, 'Reunification Day', '2024-04-30', 'National Holiday', 1, '2025-04-19 23:06:24', '2025-04-19 23:06:24'),
(4, 'Labor Day', '2024-05-01', 'International Workers Day', 1, '2025-04-19 23:06:24', '2025-04-19 23:06:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `leaves`
--

CREATE TABLE `leaves` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Đang đổ dữ liệu cho bảng `leaves`
--

INSERT INTO `leaves` (`id`, `employee_id`, `leave_type`, `start_date`, `end_date`, `reason`, `status`, `approved_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'annual', '2024-04-01', '2024-04-03', 'Annual vacation', 'approved', 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(2, 2, 'sick', '2024-03-25', '2024-03-26', 'Medical appointment', 'approved', 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(3, 3, 'annual', '2024-04-15', '2024-04-16', 'Personal matters', 'pending', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(4, 4, 'annual', '2024-04-10', '2024-04-12', 'Annual vacation', 'approved', 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(5, 5, 'sick', '2024-04-15', '2024-04-15', 'Medical appointment', 'pending', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(6, 6, 'annual', '2024-04-20', '2024-04-22', 'Personal matters', 'approved', 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(7, 7, 'annual', '2024-04-05', '2024-04-07', 'Annual vacation', 'approved', 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(8, 8, 'sick', '2024-03-28', '2024-03-28', 'Medical appointment', 'approved', 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(9, 9, 'annual', '2024-04-08', '2024-04-10', 'Annual vacation', 'approved', 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(10, 10, 'sick', '2024-04-18', '2024-04-18', 'Medical appointment', 'pending', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ;

--
-- Đang đổ dữ liệu cho bảng `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 1, 'New Task Assigned', 'You have been assigned a new task: Complete HR Report', 'task', 0, '2025-04-21 14:57:56'),
(2, 2, 'System Update', 'IT system maintenance scheduled for next week', 'system', 0, '2025-04-21 14:57:56'),
(3, 3, 'Meeting Reminder', 'Team meeting tomorrow at 10:00 AM', 'meeting', 0, '2025-04-21 14:57:56'),
(4, 4, 'Document Review', 'Please review the new financial procedures', 'document', 0, '2025-04-21 14:57:56'),
(5, 5, 'Training Registration', 'New training session available for registration', 'training', 0, '2025-04-21 14:57:56'),
(6, 6, 'System Update', 'Operations system maintenance scheduled', 'system', 0, '2025-04-21 14:57:56'),
(7, 7, 'Task Assignment', 'New HR task assigned: Employee onboarding', 'task', 0, '2025-04-21 14:57:56'),
(8, 8, 'Training Reminder', 'Software development training tomorrow', 'training', 0, '2025-04-21 14:57:56'),
(9, 9, 'Document Review', 'Please review Q1 financial reports', 'document', 0, '2025-04-21 14:57:56'),
(10, 10, 'Meeting Reminder', 'Marketing team meeting at 2 PM', 'meeting', 0, '2025-04-21 14:57:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payroll`
--

CREATE TABLE `payroll` (
  `payroll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payroll_month` int(11) NOT NULL CHECK (`payroll_month` between 1 and 12),
  `payroll_year` int(11) NOT NULL,
  `work_days_actual` decimal(4,1) NOT NULL,
  `base_salary_at_time` decimal(15,2) NOT NULL,
  `bonuses_total` decimal(15,2) DEFAULT 0.00,
  `social_insurance_deduction` decimal(15,2) NOT NULL,
  `other_deductions` decimal(15,2) DEFAULT 0.00,
  `total_salary` decimal(15,2) NOT NULL,
  `generated_at` datetime DEFAULT current_timestamp(),
  `generated_by_user_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL
) ;

--
-- Đang đổ dữ liệu cho bảng `payroll`
--

INSERT INTO `payroll` (`payroll_id`, `user_id`, `payroll_month`, `payroll_year`, `work_days_actual`, `base_salary_at_time`, `bonuses_total`, `social_insurance_deduction`, `other_deductions`, `total_salary`, `generated_at`, `generated_by_user_id`, `notes`) VALUES
(1, 1, 3, 2024, 22.0, 30000000.00, 1000000.00, 1500000.00, 0.00, 29500000.00, '2025-04-21 14:57:56', 1, NULL),
(2, 2, 3, 2024, 22.0, 25000000.00, 800000.00, 1250000.00, 0.00, 24550000.00, '2025-04-21 14:57:56', 1, NULL),
(3, 3, 3, 2024, 22.0, 25000000.00, 800000.00, 1250000.00, 0.00, 24550000.00, '2025-04-21 14:57:56', 1, NULL),
(4, 4, 3, 2024, 22.0, 25000000.00, 800000.00, 1250000.00, 0.00, 24550000.00, '2025-04-21 14:57:56', 1, NULL),
(5, 5, 3, 2024, 22.0, 25000000.00, 800000.00, 1250000.00, 0.00, 24550000.00, '2025-04-21 14:57:56', 1, NULL),
(6, 6, 3, 2024, 22.0, 25000000.00, 800000.00, 1250000.00, 0.00, 24550000.00, '2025-04-21 14:57:56', 1, NULL),
(7, 7, 3, 2024, 22.0, 15000000.00, 500000.00, 750000.00, 0.00, 14750000.00, '2025-04-21 14:57:56', 1, NULL),
(8, 8, 3, 2024, 22.0, 18000000.00, 600000.00, 900000.00, 0.00, 17700000.00, '2025-04-21 14:57:56', 1, NULL),
(9, 9, 3, 2024, 22.0, 15000000.00, 500000.00, 750000.00, 0.00, 14750000.00, '2025-04-21 14:57:56', 1, NULL),
(10, 10, 3, 2024, 22.0, 15000000.00, 500000.00, 750000.00, 0.00, 14750000.00, '2025-04-21 14:57:56', 1, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payrolls`
--

CREATE TABLE `payrolls` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `allowances` decimal(10,2) DEFAULT 0.00,
  `deductions` decimal(10,2) DEFAULT 0.00,
  `net_salary` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `payrolls`
--

INSERT INTO `payrolls` (`id`, `employee_id`, `month`, `year`, `basic_salary`, `allowances`, `deductions`, `net_salary`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 2024, 30000000.00, 2000000.00, 1500000.00, 30500000.00, 'approved', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(2, 2, 3, 2024, 25000000.00, 1500000.00, 1250000.00, 25250000.00, 'approved', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(3, 3, 3, 2024, 25000000.00, 1500000.00, 1250000.00, 25250000.00, 'approved', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(4, 4, 3, 2024, 25000000.00, 1500000.00, 1250000.00, 25250000.00, 'approved', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(5, 5, 3, 2024, 25000000.00, 1500000.00, 1250000.00, 25250000.00, 'approved', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(6, 6, 3, 2024, 25000000.00, 1500000.00, 1250000.00, 25250000.00, 'approved', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(7, 7, 3, 2024, 15000000.00, 1000000.00, 750000.00, 15250000.00, 'approved', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(8, 8, 3, 2024, 18000000.00, 1200000.00, 900000.00, 18300000.00, 'approved', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(9, 9, 3, 2024, 15000000.00, 1000000.00, 750000.00, 15250000.00, 'approved', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(10, 10, 3, 2024, 15000000.00, 1000000.00, 750000.00, 15250000.00, 'approved', '2025-04-21 14:57:56', '2025-04-21 14:57:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `performances`
--

CREATE TABLE `performances` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `review_date` date NOT NULL,
  `performance_score` decimal(4,2) NOT NULL,
  `strengths` text DEFAULT NULL,
  `weaknesses` text DEFAULT NULL,
  `goals` text DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'draft',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `positions`
--

CREATE TABLE `positions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `department_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `salary_grade` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `positions`
--

INSERT INTO `positions` (`id`, `name`, `description`, `department_id`, `created_at`, `updated_at`, `salary_grade`) VALUES
(1, 'HR Manager', 'Human Resources Manager', 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'M1'),
(2, 'HR Specialist', 'Human Resources Specialist', 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'S1'),
(3, 'IT Manager', 'Information Technology Manager', 2, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'M1'),
(4, 'Software Developer', 'Software Developer', 2, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'S1'),
(5, 'Finance Manager', 'Finance Manager', 3, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'M1'),
(6, 'Accountant', 'Accountant', 3, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'S1'),
(7, 'Marketing Manager', 'Marketing Manager', 4, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'M1'),
(8, 'Marketing Specialist', 'Marketing Specialist', 4, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'S1'),
(9, 'Operations Manager', 'Operations Manager', 5, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'M1'),
(10, 'Operations Coordinator', 'Operations Coordinator', 5, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'S1');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rate_limits`
--

CREATE TABLE `rate_limits` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `endpoint` varchar(255) NOT NULL,
  `request_count` int(11) NOT NULL DEFAULT 1,
  `window_start` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'admin', 'System administrator with full access'),
(2, 'manager', 'Department manager with elevated privileges'),
(3, 'hr', 'Human resources staff with HR-related access'),
(4, 'employee', 'Regular employee with basic access');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `salary_history`
--

CREATE TABLE `salary_history` (
  `salary_history_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `effective_date` date NOT NULL,
  `job_position` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `salary_coefficient` decimal(10,2) NOT NULL,
  `salary_level` varchar(50) NOT NULL,
  `decision_attachment_url` varchar(512) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `recorded_by_user_id` int(11) DEFAULT NULL
) ;

--
-- Đang đổ dữ liệu cho bảng `salary_history`
--

INSERT INTO `salary_history` (`salary_history_id`, `user_id`, `effective_date`, `job_position`, `department`, `salary_coefficient`, `salary_level`, `decision_attachment_url`, `created_at`, `recorded_by_user_id`) VALUES
(1, 1, '2023-01-01', 'HR Manager', 'Human Resources', 3.00, 'Senior', NULL, '2025-04-21 14:57:56', NULL),
(2, 2, '2023-01-01', 'HR Manager', 'Human Resources', 2.50, 'Mid', NULL, '2025-04-21 14:57:56', NULL),
(3, 3, '2023-01-01', 'IT Manager', 'Information Technology', 3.00, 'Senior', NULL, '2025-04-21 14:57:56', NULL),
(4, 4, '2023-01-01', 'Finance Manager', 'Finance', 3.00, 'Senior', NULL, '2025-04-21 14:57:56', NULL),
(5, 5, '2023-01-01', 'Marketing Manager', 'Marketing', 3.00, 'Senior', NULL, '2025-04-21 14:57:56', NULL),
(6, 6, '2023-01-01', 'Operations Manager', 'Operations', 3.00, 'Senior', NULL, '2025-04-21 14:57:56', NULL),
(7, 7, '2023-01-01', 'HR Specialist', 'Human Resources', 2.00, 'Junior', NULL, '2025-04-21 14:57:56', NULL),
(8, 8, '2023-01-01', 'Software Developer', 'Information Technology', 2.00, 'Junior', NULL, '2025-04-21 14:57:56', NULL),
(9, 9, '2023-01-01', 'Accountant', 'Finance', 2.00, 'Junior', NULL, '2025-04-21 14:57:56', NULL),
(10, 10, '2023-01-01', 'Marketing Specialist', 'Marketing', 2.00, 'Junior', NULL, '2025-04-21 14:57:56', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `due_date` date DEFAULT NULL,
  `priority` varchar(20) DEFAULT 'medium',
  `status` varchar(20) DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Đang đổ dữ liệu cho bảng `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `description`, `assigned_to`, `assigned_by`, `due_date`, `priority`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Complete HR Report', 'Monthly HR performance report', 1, 1, '2024-04-15', 'high', 'pending', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(2, 'Update IT System', 'System maintenance and updates', 2, 1, '2024-04-20', 'medium', 'pending', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(3, 'Review Financial Statements', 'Q1 financial review', 3, 1, '2024-04-25', 'high', 'pending', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(4, 'Marketing Campaign', 'New product launch campaign', 4, 1, '2024-04-30', 'high', 'pending', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(5, 'Operations Review', 'Monthly operations review', 5, 1, '2024-04-28', 'medium', 'pending', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(6, 'Employee Training', 'New employee orientation', 6, 1, '2024-04-22', 'high', 'pending', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(7, 'System Development', 'New feature development', 7, 1, '2024-04-18', 'high', 'pending', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(8, 'Budget Planning', 'Q2 budget planning', 8, 1, '2024-04-17', 'high', 'pending', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(9, 'Social Media Campaign', 'Social media marketing campaign', 9, 1, '2024-04-19', 'medium', 'pending', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(10, 'Process Improvement', 'Operations process improvement', 10, 1, '2024-04-21', 'high', 'pending', '2025-04-21 14:57:56', '2025-04-21 14:57:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `trainings`
--

CREATE TABLE `trainings` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `trainer` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'planned',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `trainings`
--

INSERT INTO `trainings` (`id`, `name`, `description`, `start_date`, `end_date`, `location`, `trainer`, `status`, `created_at`, `updated_at`) VALUES
(1, 'New HR Policies', 'Training on updated HR policies and procedures', '2024-04-10', '2024-04-11', 'Training Room A', 'External Trainer', 'planned', '2025-04-15 13:53:46', '2025-04-15 13:53:46'),
(2, 'IT Security', 'Basic IT security training', '2024-04-15', '2024-04-16', 'Online', 'Internal IT Team', 'planned', '2025-04-15 13:53:46', '2025-04-15 13:53:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_salt` varchar(64) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `requires_password_change` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `department_id` int(11) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `employee_code` varchar(20) DEFAULT NULL,
  `contract_type` varchar(50) DEFAULT NULL,
  `contract_start_date` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `password_salt`, `role_id`, `is_active`, `requires_password_change`, `created_at`, `updated_at`, `department_id`, `position_id`, `hire_date`, `status`, `employee_code`, `contract_type`, `contract_start_date`, `contract_end_date`, `last_login`) VALUES
(1, 'admin', 'admin@company.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, 1, 1, 0, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 1, 1, NULL, 'active', 'EMP001', 'full_time', '2023-01-01', '2025-12-31', NULL),
(2, 'manager', 'manager@company.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, 2, 1, 0, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 1, 1, NULL, 'active', 'EMP002', 'full_time', '2023-01-01', '2025-12-31', NULL),
(3, 'employee1', 'employee1@company.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, 4, 1, 0, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 2, 3, NULL, 'active', 'EMP003', 'full_time', '2023-01-01', '2025-12-31', NULL),
(4, 'employee2', 'employee2@company.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, 4, 1, 0, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 3, 5, NULL, 'active', 'EMP004', 'full_time', '2023-01-01', '2025-12-31', NULL),
(5, 'employee3', 'employee3@company.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, 4, 1, 0, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 4, 7, NULL, 'active', 'EMP005', 'full_time', '2023-01-01', '2025-12-31', NULL),
(6, 'employee4', 'employee4@company.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, 4, 1, 0, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 5, 9, NULL, 'active', 'EMP006', 'full_time', '2023-01-01', '2025-12-31', NULL),
(7, 'employee5', 'employee5@company.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, 4, 1, 0, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 1, 2, NULL, 'active', 'EMP007', 'full_time', '2023-01-01', '2025-12-31', NULL),
(8, 'employee6', 'employee6@company.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, 4, 1, 0, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 2, 4, NULL, 'active', 'EMP008', 'full_time', '2023-01-01', '2025-12-31', NULL),
(9, 'employee7', 'employee7@company.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, 4, 1, 0, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 3, 6, NULL, 'active', 'EMP009', 'full_time', '2023-01-01', '2025-12-31', NULL),
(10, 'employee8', 'employee8@company.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, 4, 1, 0, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 4, 8, NULL, 'active', 'EMP010', 'full_time', '2023-01-01', '2025-12-31', NULL);

--
-- Bẫy `users`
--
DELIMITER $$
CREATE TRIGGER `update_users_timestamp` BEFORE UPDATE ON `users` FOR EACH ROW BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_profiles`
--

CREATE TABLE `user_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `avatar_url` varchar(512) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `current_workplace` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `gender` varchar(10) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL,
  `tax_code` varchar(20) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `ethnicity` varchar(100) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `marital_status` varchar(20) DEFAULT NULL,
  `id_card_number` varchar(20) DEFAULT NULL,
  `id_card_issue_date` date DEFAULT NULL,
  `id_card_issue_place` varchar(255) DEFAULT NULL
) ;

--
-- Đang đổ dữ liệu cho bảng `user_profiles`
--

INSERT INTO `user_profiles` (`profile_id`, `user_id`, `full_name`, `avatar_url`, `date_of_birth`, `permanent_address`, `current_workplace`, `created_at`, `updated_at`, `gender`, `phone_number`, `emergency_contact`, `bank_account`, `tax_code`, `nationality`, `ethnicity`, `religion`, `marital_status`, `id_card_number`, `id_card_issue_date`, `id_card_issue_place`) VALUES
(1, 1, 'Admin User', NULL, '1990-01-01', '123 Main St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456789', 'Jane Doe', '1234567890', '123456789', 'Vietnamese', 'Kinh', 'None', 'Single', '123456789', '2010-01-01', 'Hanoi'),
(2, 2, 'HR Manager', NULL, '1985-05-15', '456 Park Ave, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456788', 'John Smith', '0987654321', '987654321', 'Vietnamese', 'Kinh', 'None', 'Married', '987654321', '2012-05-15', 'Hanoi'),
(3, 3, 'IT Manager', NULL, '1988-08-20', '789 Oak St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456787', 'Mary Wilson', '5678901234', '567890123', 'Vietnamese', 'Kinh', 'None', 'Married', '567890123', '2008-08-20', 'Hanoi'),
(4, 4, 'Finance Manager', NULL, '1987-03-10', '321 Pine St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456786', 'Sarah Brown', '4321098765', '432109876', 'Vietnamese', 'Kinh', 'None', 'Single', '432109876', '2007-03-10', 'Hanoi'),
(5, 5, 'Marketing Manager', NULL, '1986-11-25', '654 Elm St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456785', 'David Lee', '8765432109', '876543210', 'Vietnamese', 'Kinh', 'None', 'Married', '876543210', '2006-11-25', 'Hanoi'),
(6, 6, 'Operations Manager', NULL, '1989-07-30', '987 Maple St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456784', 'Lisa Chen', '2345678901', '234567890', 'Vietnamese', 'Kinh', 'None', 'Single', '234567890', '2009-07-30', 'Hanoi'),
(7, 7, 'HR Specialist', NULL, '1992-02-14', '147 Cedar St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456783', 'Mike Johnson', '3456789012', '345678901', 'Vietnamese', 'Kinh', 'None', 'Single', '345678901', '2012-02-14', 'Hanoi'),
(8, 8, 'Software Developer', NULL, '1991-09-05', '258 Birch St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456782', 'Anna Davis', '4567890123', '456789012', 'Vietnamese', 'Kinh', 'None', 'Married', '456789012', '2011-09-05', 'Hanoi'),
(9, 9, 'Accountant', NULL, '1993-04-20', '369 Spruce St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456781', 'Tom Wilson', '5678901234', '567890123', 'Vietnamese', 'Kinh', 'None', 'Single', '567890123', '2013-04-20', 'Hanoi'),
(10, 10, 'Marketing Specialist', NULL, '1990-12-15', '741 Walnut St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456780', 'Peter Brown', '6789012345', '678901234', 'Vietnamese', 'Kinh', 'None', 'Married', '678901234', '2010-12-15', 'Hanoi');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `work_schedules`
--

CREATE TABLE `work_schedules` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `work_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `schedule_type` varchar(20) DEFAULT 'normal',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Đang đổ dữ liệu cho bảng `work_schedules`
--

INSERT INTO `work_schedules` (`id`, `employee_id`, `work_date`, `start_time`, `end_time`, `schedule_type`, `created_at`, `updated_at`) VALUES
(1, 1, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(2, 2, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(3, 3, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(4, 4, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(5, 5, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(6, 6, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(7, 7, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(8, 8, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(9, 9, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(10, 10, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activities_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_attendance_date_user` (`attendance_date`,`user_id`);

--
-- Chỉ mục cho bảng `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_audit_logs_user_id` (`user_id`),
  ADD KEY `idx_audit_logs_timestamp` (`timestamp`),
  ADD KEY `idx_audit_logs_action_type` (`action_type`),
  ADD KEY `idx_audit_logs_target` (`target_entity`,`target_entity_id`);

--
-- Chỉ mục cho bảng `bonuses`
--
ALTER TABLE `bonuses`
  ADD PRIMARY KEY (`bonus_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `added_by_user_id` (`added_by_user_id`);

--
-- Chỉ mục cho bảng `degrees`
--
ALTER TABLE `degrees`
  ADD PRIMARY KEY (`degree_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `manager_id` (`manager_id`);

--
-- Chỉ mục cho bảng `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `department_id` (`department_id`);

--
-- Chỉ mục cho bảng `email_verification_tokens`
--
ALTER TABLE `email_verification_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_code` (`employee_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `position_id` (`position_id`);

--
-- Chỉ mục cho bảng `employee_positions`
--
ALTER TABLE `employee_positions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `position_id` (`position_id`);

--
-- Chỉ mục cho bảng `employee_trainings`
--
ALTER TABLE `employee_trainings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `training_id` (`training_id`);

--
-- Chỉ mục cho bảng `equipment_assignments`
--
ALTER TABLE `equipment_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Chỉ mục cho bảng `family_members`
--
ALTER TABLE `family_members`
  ADD PRIMARY KEY (`family_member_id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Chỉ mục cho bảng `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_leave_status` (`status`,`employee_id`);

--
-- Chỉ mục cho bảng `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_address` (`ip_address`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`payroll_id`),
  ADD UNIQUE KEY `uq_payroll_user_month_year` (`user_id`,`payroll_month`,`payroll_year`),
  ADD KEY `generated_by_user_id` (`generated_by_user_id`),
  ADD KEY `idx_payroll_month_year` (`payroll_month`,`payroll_year`);

--
-- Chỉ mục cho bảng `payrolls`
--
ALTER TABLE `payrolls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Chỉ mục cho bảng `performances`
--
ALTER TABLE `performances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `reviewer_id` (`reviewer_id`);

--
-- Chỉ mục cho bảng `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Chỉ mục cho bảng `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_ip_endpoint` (`ip_address`,`endpoint`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Chỉ mục cho bảng `salary_history`
--
ALTER TABLE `salary_history`
  ADD PRIMARY KEY (`salary_history_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `recorded_by_user_id` (`recorded_by_user_id`);

--
-- Chỉ mục cho bảng `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `assigned_by` (`assigned_by`);

--
-- Chỉ mục cho bảng `trainings`
--
ALTER TABLE `trainings`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Chỉ mục cho bảng `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD KEY `fk_user_profiles_user` (`user_id`);

--
-- Chỉ mục cho bảng `work_schedules`
--
ALTER TABLE `work_schedules`
  ADD KEY `fk_work_schedules_employee` (`employee_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payroll`
--
ALTER TABLE `payroll`
  MODIFY `payroll_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payrolls`
--
ALTER TABLE `payrolls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_attendance_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `bonuses`
--
ALTER TABLE `bonuses`
  ADD CONSTRAINT `fk_bonuses_added_by` FOREIGN KEY (`added_by_user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_bonuses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `fk_departments_manager` FOREIGN KEY (`manager_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_ibfk_3` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `equipment_assignments`
--
ALTER TABLE `equipment_assignments`
  ADD CONSTRAINT `fk_equipment_assignments_employee` FOREIGN KEY (`employee_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `fk_leaves_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_leaves_employee` FOREIGN KEY (`employee_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `fk_payroll_generated_by` FOREIGN KEY (`generated_by_user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_payroll_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `payrolls`
--
ALTER TABLE `payrolls`
  ADD CONSTRAINT `payrolls_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `fk_positions_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `salary_history`
--
ALTER TABLE `salary_history`
  ADD CONSTRAINT `fk_salary_history_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_tasks_assigned_by` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_tasks_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `fk_user_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `work_schedules`
--
ALTER TABLE `work_schedules`
  ADD CONSTRAINT `fk_work_schedules_employee` FOREIGN KEY (`employee_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
