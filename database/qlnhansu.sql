-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2025 at 05:01 AM
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

--
-- Table structure for table `activities`
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
-- Dumping data for table `activities`
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
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `recorded_at` datetime DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  `attendance_symbol` varchar(10) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `user_id`, `attendance_date`, `recorded_at`, `notes`, `attendance_symbol`, `created_at`) VALUES
(5, 5, '2024-03-01', '2024-03-01 08:15:00', 'On time', 'P', '2025-04-21 14:57:56'),
(6, 6, '2024-03-01', '2024-03-01 08:00:00', 'On time', 'P', '2025-04-21 14:57:56'),
(7, 7, '2024-03-01', '2024-03-01 08:20:00', 'On time', 'P', '2025-04-21 14:57:56'),
(8, 8, '2024-03-01', '2024-03-01 08:00:00', 'On time', 'P', '2025-04-21 14:57:56'),
(9, 9, '2024-03-01', '2024-03-01 08:25:00', 'On time', 'P', '2025-04-21 14:57:56'),
(10, 10, '2024-03-01', '2024-03-01 08:00:00', 'On time', 'P', '2025-04-21 14:57:56'),
(15, 5, '2024-04-15', '2025-04-22 03:20:24', 'Check-in Ä‘Ãºng giá»', 'P', '2025-04-22 03:20:24'),
(20, 5, '2024-04-16', '2025-04-22 03:20:24', 'Check-in Ä‘Ãºng giá»', 'P', '2025-04-22 03:20:24'),
(25, 5, '2024-04-17', '2025-04-22 03:20:24', 'Check-in Ä‘Ãºng giá»', 'P', '2025-04-22 03:20:24'),
(30, 5, '2024-04-18', '2025-04-22 03:20:24', 'Check-in Ä‘Ãºng giá»', 'P', '2025-04-22 03:20:24'),
(35, 5, '2024-04-19', '2025-04-22 03:20:24', 'Check-in Ä‘Ãºng giá»', 'P', '2025-04-22 03:20:24'),
(40, 5, '2024-04-20', '2025-04-22 03:20:24', 'Check-in Ä‘Ãºng giá»', 'P', '2025-04-22 03:20:24'),
(45, 5, '2024-04-21', '2025-04-22 03:20:24', 'Check-in Ä‘Ãºng giá»', 'P', '2025-04-22 03:20:24'),
(49, 5, '2025-04-21', '2025-04-21 22:24:39', 'Check-in Ä‘Ãºng giá»', 'P', '2025-04-21 22:24:39'),
(50, 6, '2025-04-21', '2025-04-21 22:24:39', 'Check-in Ä‘Ãºng giá»', 'P', '2025-04-21 22:24:39'),
(51, 7, '2025-04-21', '2025-04-21 22:24:39', 'Check-in Ä‘Ãºng giá»', 'P', '2025-04-21 22:24:39'),
(52, 8, '2025-04-21', '2025-04-21 22:24:39', 'Check-in Ä‘Ãºng giá»', 'P', '2025-04-21 22:24:39'),
(53, 9, '2025-04-21', '2025-04-21 22:24:39', 'Check-in Ä‘Ãºng giá»', 'P', '2025-04-21 22:24:39'),
(54, 10, '2025-04-21', '2025-04-21 22:24:39', 'Check-in Ä‘Ãºng giá»', 'P', '2025-04-21 22:24:39');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
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
-- Table structure for table `benefits`
--

CREATE TABLE `benefits` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bonuses`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `bonuses`
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
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `position_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `resume_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `issuing_organization` varchar(255) NOT NULL,
  `issue_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `credential_id` varchar(100) DEFAULT NULL,
  `file_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `contract_type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `salary` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `degrees`
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
-- Dumping data for table `degrees`
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
-- Table structure for table `departments`
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
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `manager_id`, `created_at`, `updated_at`, `parent_id`) VALUES
(1, 'Human Resources', 'HR Department', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', NULL),
(2, 'Information Technology', 'IT Department', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', NULL),
(3, 'Finance', 'Finance Department', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', NULL),
(4, 'Marketing', 'Marketing Department', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', NULL),
(5, 'Operations', 'Operations Department', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `documents`
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
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `title`, `description`, `file_url`, `document_type`, `uploaded_by`, `department_id`, `created_at`, `updated_at`) VALUES
(1, 'Employee Handbook', 'Company policies and procedures', '/documents/handbook.pdf', 'policy', 1, 1, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(2, 'IT Security Guidelines', 'IT security best practices', '/documents/security.pdf', 'guideline', 2, 2, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(3, 'Financial Procedures', 'Financial management procedures', '/documents/finance.pdf', 'procedure', 3, 3, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(4, 'Marketing Strategy', 'Company marketing strategy', '/documents/marketing.pdf', 'strategy', 4, 4, '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(5, 'Operations Manual', 'Operations procedures manual', '/documents/operations.pdf', 'manual', 5, 5, '2025-04-21 14:57:56', '2025-04-21 14:57:56');

-- --------------------------------------------------------

--
-- Table structure for table `document_versions`
--

CREATE TABLE `document_versions` (
  `id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `version_number` varchar(20) NOT NULL,
  `file_url` varchar(255) NOT NULL,
  `changes_description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_verification_tokens`
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
-- Table structure for table `employees`
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
-- Dumping data for table `employees`
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
-- Table structure for table `employee_positions`
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
-- Dumping data for table `employee_positions`
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
-- Table structure for table `employee_trainings`
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
-- Dumping data for table `employee_trainings`
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
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_cost` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipment_assignments`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `equipment_assignments`
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
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `evaluator_id` int(11) NOT NULL,
  `evaluation_date` date NOT NULL,
  `performance_score` int(11) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `family_members`
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
-- Dumping data for table `family_members`
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
-- Table structure for table `holidays`
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
-- Dumping data for table `holidays`
--

INSERT INTO `holidays` (`id`, `name`, `date`, `description`, `is_recurring`, `created_at`, `updated_at`) VALUES
(1, 'New Year', '2024-01-01', 'New Year Day', 1, '2025-04-19 23:06:24', '2025-04-19 23:06:24'),
(2, 'Tet Holiday', '2024-02-10', 'Lunar New Year', 1, '2025-04-19 23:06:24', '2025-04-19 23:06:24'),
(3, 'Reunification Day', '2024-04-30', 'National Holiday', 1, '2025-04-19 23:06:24', '2025-04-19 23:06:24'),
(4, 'Labor Day', '2024-05-01', 'International Workers Day', 1, '2025-04-19 23:06:24', '2025-04-19 23:06:24');

-- --------------------------------------------------------

--
-- Table structure for table `insurance`
--

CREATE TABLE `insurance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `insurance_type` varchar(50) NOT NULL,
  `policy_number` varchar(100) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `premium` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interviews`
--

CREATE TABLE `interviews` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `interviewer_id` int(11) NOT NULL,
  `interview_date` datetime NOT NULL,
  `type` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_positions`
--

CREATE TABLE `job_positions` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `department_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `salary_range_min` decimal(10,2) DEFAULT NULL,
  `salary_range_max` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kpi`
--

CREATE TABLE `kpi` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `metric_name` varchar(255) NOT NULL,
  `target_value` decimal(10,2) DEFAULT NULL,
  `actual_value` decimal(10,2) DEFAULT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `leaves`
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
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `notifications`
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
-- Table structure for table `onboarding`
--

CREATE TABLE `onboarding` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
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
-- Table structure for table `payroll`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `payroll`
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
(10, 10, 3, 2024, 22.0, 15000000.00, 500000.00, 750000.00, 0.00, 14750000.00, '2025-04-21 14:57:56', 1, NULL),
(11, 1, 4, 2024, 22.0, 15000000.00, 2000000.00, 1500000.00, 500000.00, 15000000.00, '2025-04-22 03:20:24', 1, 'LÆ°Æ¡ng thÃ¡ng 4'),
(12, 2, 4, 2024, 22.0, 12000000.00, 1500000.00, 1200000.00, 400000.00, 12000000.00, '2025-04-22 03:20:24', 1, 'LÆ°Æ¡ng thÃ¡ng 4'),
(13, 3, 4, 2024, 22.0, 10000000.00, 1000000.00, 1000000.00, 300000.00, 10000000.00, '2025-04-22 03:20:24', 1, 'LÆ°Æ¡ng thÃ¡ng 4'),
(14, 4, 4, 2024, 22.0, 8000000.00, 800000.00, 800000.00, 200000.00, 8000000.00, '2025-04-22 03:20:24', 1, 'LÆ°Æ¡ng thÃ¡ng 4'),
(15, 5, 4, 2024, 22.0, 7000000.00, 700000.00, 700000.00, 100000.00, 7000000.00, '2025-04-22 03:20:24', 1, 'LÆ°Æ¡ng thÃ¡ng 4'),
(16, 6, 4, 2024, 22.0, 9000000.00, 900000.00, 900000.00, 300000.00, 9000000.00, '2025-04-22 03:20:24', 1, 'LÆ°Æ¡ng thÃ¡ng 4'),
(17, 7, 4, 2024, 22.0, 11000000.00, 1100000.00, 1100000.00, 400000.00, 11000000.00, '2025-04-22 03:20:24', 1, 'LÆ°Æ¡ng thÃ¡ng 4'),
(18, 8, 4, 2024, 22.0, 13000000.00, 1300000.00, 1300000.00, 500000.00, 13000000.00, '2025-04-22 03:20:24', 1, 'LÆ°Æ¡ng thÃ¡ng 4'),
(19, 9, 4, 2024, 22.0, 14000000.00, 1400000.00, 1400000.00, 600000.00, 14000000.00, '2025-04-22 03:20:24', 1, 'LÆ°Æ¡ng thÃ¡ng 4'),
(20, 10, 4, 2024, 22.0, 16000000.00, 1600000.00, 1600000.00, 700000.00, 16000000.00, '2025-04-22 03:20:24', 1, 'LÆ°Æ¡ng thÃ¡ng 4');

-- --------------------------------------------------------

--
-- Table structure for table `payrolls`
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
-- Dumping data for table `payrolls`
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
-- Table structure for table `performances`
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
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `code`, `description`, `created_at`) VALUES
(1, 'view_users', 'view_users', 'Xem danh s??ch ng?????i d??ng', '2025-04-23 13:22:15'),
(2, 'create_users', 'create_users', 'T???o ng?????i d??ng m???i', '2025-04-23 13:22:15'),
(3, 'edit_users', 'edit_users', 'S???a th??ng tin ng?????i d??ng', '2025-04-23 13:22:15'),
(4, 'delete_users', 'delete_users', 'X??a ng?????i d??ng', '2025-04-23 13:22:15'),
(5, 'view_departments', 'view_departments', 'Xem danh s??ch ph??ng ban', '2025-04-23 13:22:15'),
(6, 'manage_departments', 'manage_departments', 'Qu???n l?? ph??ng ban', '2025-04-23 13:22:15'),
(7, 'view_salary', 'view_salary', 'Xem th??ng tin l????ng', '2025-04-23 13:22:15'),
(8, 'manage_salary', 'manage_salary', 'Qu???n l?? l????ng', '2025-04-23 13:22:15'),
(9, 'view_leaves', 'view_leaves', 'Xem ????n t???', '2025-04-23 13:22:15'),
(10, 'manage_leaves', 'manage_leaves', 'Qu???n l?? ????n t???', '2025-04-23 13:22:15'),
(11, 'view_attendance', 'view_attendance', 'Xem ch???m c??ng', '2025-04-23 13:22:15'),
(12, 'manage_attendance', 'manage_attendance', 'Qu???n l?? ch???m c??ng', '2025-04-23 13:22:15'),
(13, 'view_documents', 'view_documents', 'Xem t??i li???u', '2025-04-23 13:22:15'),
(14, 'manage_documents', 'manage_documents', 'Qu???n l?? t??i li???u', '2025-04-23 13:22:15'),
(15, 'view_evaluations', 'view_evaluations', 'Xem ????nh gi??', '2025-04-23 13:22:15'),
(16, 'manage_evaluations', 'manage_evaluations', 'Qu???n l?? ????nh gi??', '2025-04-23 13:22:15'),
(17, 'view_trainings', 'view_trainings', 'Xem ????o t???o', '2025-04-23 13:22:15'),
(18, 'manage_trainings', 'manage_trainings', 'Qu???n l?? ????o t???o', '2025-04-23 13:22:15'),
(19, 'view_equipment', 'view_equipment', 'Xem thi???t b???', '2025-04-23 13:22:15'),
(20, 'manage_equipment', 'manage_equipment', 'Qu???n l?? thi???t b???', '2025-04-23 13:22:15');

-- --------------------------------------------------------

--
-- Table structure for table `policies`
--

CREATE TABLE `policies` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `effective_date` date NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `positions`
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
-- Dumping data for table `positions`
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
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_resources`
--

CREATE TABLE `project_resources` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `resource_type` varchar(50) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `allocation_percentage` int(11) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_tasks`
--

CREATE TABLE `project_tasks` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rate_limits`
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
-- Table structure for table `recruitment`
--

CREATE TABLE `recruitment` (
  `id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `requirements` text DEFAULT NULL,
  `responsibilities` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Super Admin', 'Qu???n tr??? vi??n cao c???p - c?? to??n quy???n tr??n h??? th???ng', '2025-04-23 13:22:15'),
(2, 'HR Manager', 'Qu???n l?? nh??n s??? - qu???n l?? th??ng tin nh??n vi??n v?? c??c quy tr??nh HR', '2025-04-23 13:22:15'),
(3, 'Department Manager', 'Qu???n l?? ph??ng ban - qu???n l?? nh??n vi??n trong ph??ng ban', '2025-04-23 13:22:15'),
(4, 'Employee', 'Nh??n vi??n - ng?????i d??ng c?? b???n c???a h??? th???ng', '2025-04-23 13:22:15');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES
(1, 1, '2025-04-23 13:22:15'),
(1, 2, '2025-04-23 13:22:15'),
(1, 3, '2025-04-23 13:22:15'),
(1, 4, '2025-04-23 13:22:15'),
(1, 5, '2025-04-23 13:22:15'),
(1, 6, '2025-04-23 13:22:15'),
(1, 7, '2025-04-23 13:22:15'),
(1, 8, '2025-04-23 13:22:15'),
(1, 9, '2025-04-23 13:22:15'),
(1, 10, '2025-04-23 13:22:15'),
(1, 11, '2025-04-23 13:22:15'),
(1, 12, '2025-04-23 13:22:15'),
(1, 13, '2025-04-23 13:22:15'),
(1, 14, '2025-04-23 13:22:15'),
(1, 15, '2025-04-23 13:22:15'),
(1, 16, '2025-04-23 13:22:15'),
(1, 17, '2025-04-23 13:22:15'),
(1, 18, '2025-04-23 13:22:15'),
(1, 19, '2025-04-23 13:22:15'),
(1, 20, '2025-04-23 13:22:15'),
(2, 1, '2025-04-23 13:22:15'),
(2, 2, '2025-04-23 13:22:15'),
(2, 3, '2025-04-23 13:22:15'),
(2, 5, '2025-04-23 13:22:15'),
(2, 6, '2025-04-23 13:22:15'),
(2, 7, '2025-04-23 13:22:15'),
(2, 8, '2025-04-23 13:22:15'),
(2, 9, '2025-04-23 13:22:15'),
(2, 10, '2025-04-23 13:22:15'),
(2, 11, '2025-04-23 13:22:15'),
(2, 12, '2025-04-23 13:22:15'),
(2, 13, '2025-04-23 13:22:15'),
(2, 14, '2025-04-23 13:22:15'),
(2, 15, '2025-04-23 13:22:15'),
(2, 16, '2025-04-23 13:22:15'),
(2, 17, '2025-04-23 13:22:15'),
(2, 18, '2025-04-23 13:22:15'),
(2, 19, '2025-04-23 13:22:15'),
(2, 20, '2025-04-23 13:22:15'),
(3, 1, '2025-04-23 13:22:15'),
(3, 5, '2025-04-23 13:22:15'),
(3, 7, '2025-04-23 13:22:15'),
(3, 9, '2025-04-23 13:22:15'),
(3, 10, '2025-04-23 13:22:15'),
(3, 11, '2025-04-23 13:22:15'),
(3, 12, '2025-04-23 13:22:15'),
(3, 13, '2025-04-23 13:22:15'),
(3, 15, '2025-04-23 13:22:15'),
(3, 16, '2025-04-23 13:22:15'),
(3, 17, '2025-04-23 13:22:15'),
(3, 19, '2025-04-23 13:22:15'),
(4, 1, '2025-04-23 13:22:15'),
(4, 5, '2025-04-23 13:22:15'),
(4, 7, '2025-04-23 13:22:15'),
(4, 9, '2025-04-23 13:22:15'),
(4, 11, '2025-04-23 13:22:15'),
(4, 13, '2025-04-23 13:22:15'),
(4, 15, '2025-04-23 13:22:15'),
(4, 17, '2025-04-23 13:22:15'),
(4, 19, '2025-04-23 13:22:15');

-- --------------------------------------------------------

--
-- Table structure for table `salary_history`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `salary_history`
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
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `session_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `expires` int(11) UNSIGNED NOT NULL,
  `data` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`session_id`, `expires`, `data`) VALUES
('edNB58r8bE929FZODxkFlUnw81X0rb4r', 1745486529, '{\"cookie\":{\"originalMaxAge\":86400000,\"expires\":\"2025-04-24T07:01:36.470Z\",\"secure\":false,\"httpOnly\":true,\"path\":\"/\"},\"user\":{\"id\":4,\"username\":\"hr\",\"role_id\":4,\"full_name\":\"HR Staff\"}}');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tasks`
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
-- Table structure for table `trainings`
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
-- Dumping data for table `trainings`
--

INSERT INTO `trainings` (`id`, `name`, `description`, `start_date`, `end_date`, `location`, `trainer`, `status`, `created_at`, `updated_at`) VALUES
(1, 'New HR Policies', 'Training on updated HR policies and procedures', '2024-04-10', '2024-04-11', 'Training Room A', 'External Trainer', 'planned', '2025-04-15 13:53:46', '2025-04-15 13:53:46'),
(2, 'IT Security', 'Basic IT security training', '2024-04-15', '2024-04-16', 'Online', 'Internal IT Team', 'planned', '2025-04-15 13:53:46', '2025-04-15 13:53:46');

-- --------------------------------------------------------

--
-- Table structure for table `training_courses`
--

CREATE TABLE `training_courses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `training_evaluations`
--

CREATE TABLE `training_evaluations` (
  `id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `evaluator_id` int(11) NOT NULL,
  `evaluation_date` date NOT NULL,
  `score` int(11) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `training_registrations`
--

CREATE TABLE `training_registrations` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `registration_date` date NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
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
  `last_login` datetime DEFAULT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `last_attempt` datetime DEFAULT NULL,
  `remember_token` varchar(64) DEFAULT NULL,
  `remember_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `password_salt`, `role_id`, `is_active`, `requires_password_change`, `created_at`, `updated_at`, `department_id`, `position_id`, `hire_date`, `status`, `employee_code`, `contract_type`, `contract_start_date`, `contract_end_date`, `last_login`, `login_attempts`, `last_attempt`, `remember_token`, `remember_token_expiry`) VALUES
(1, 'admin', 'admin@example.com', 'admin123', NULL, 1, 1, 0, '2025-04-22 16:57:14', '2025-04-23 16:12:45', NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, '2025-04-23 16:12:45', 0, NULL, NULL, NULL),
(2, 'manager', 'manager@example.com', 'manager123', NULL, 2, 1, 0, '2025-04-22 16:57:14', '2025-04-23 16:12:45', NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, '2025-04-23 16:12:45', 0, NULL, NULL, NULL),
(3, 'employee', 'employee@example.com', 'employee123', NULL, 3, 1, 0, '2025-04-22 16:57:15', '2025-04-23 16:12:45', NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, '2025-04-23 16:12:45', 0, NULL, NULL, NULL),
(4, 'hr', 'hr@example.com', 'hr123', NULL, 4, 1, 0, '2025-04-22 16:57:15', '2025-04-23 16:12:45', NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, '2025-04-23 16:12:45', 0, NULL, NULL, NULL),
(5, 'employee3', 'employee3@company.com', '123456', NULL, 4, 1, 0, '2025-04-21 14:57:56', '2025-04-23 09:47:42', 4, 7, NULL, 'active', 'EMP005', 'full_time', '2023-01-01', '2025-12-31', NULL, 0, NULL, NULL, NULL),
(6, 'employee4', 'employee4@company.com', '123456', NULL, 4, 1, 0, '2025-04-21 14:57:56', '2025-04-23 09:47:58', 5, 9, NULL, 'active', 'EMP006', 'full_time', '2023-01-01', '2025-12-31', NULL, 0, NULL, NULL, NULL),
(7, 'employee5', 'employee5@company.com', '123456', NULL, 4, 1, 0, '2025-04-21 14:57:56', '2025-04-23 09:48:05', 1, 2, NULL, 'active', 'EMP007', 'full_time', '2023-01-01', '2025-12-31', NULL, 0, NULL, NULL, NULL),
(8, 'employee6', 'employee6@company.com', '123456', NULL, 4, 1, 0, '2025-04-21 14:57:56', '2025-04-23 09:48:13', 2, 4, NULL, 'active', 'EMP008', 'full_time', '2023-01-01', '2025-12-31', NULL, 0, NULL, NULL, NULL),
(9, 'employee7', 'employee7@company.com', '123456\r\n', NULL, 4, 1, 0, '2025-04-21 14:57:56', '2025-04-23 09:48:22', 3, 6, NULL, 'active', 'EMP009', 'full_time', '2023-01-01', '2025-12-31', NULL, 0, NULL, NULL, NULL),
(10, 'employee8', 'employee8@company.com', '123456', NULL, 4, 1, 0, '2025-04-21 14:57:56', '2025-04-23 09:48:33', 4, 8, NULL, 'active', 'EMP010', 'full_time', '2023-01-01', '2025-12-31', NULL, 0, NULL, NULL, NULL);

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `update_users_timestamp` BEFORE UPDATE ON `users` FOR EACH ROW BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_profiles`
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
(10, 10, 'Marketing Specialist', NULL, '1990-12-15', '741 Walnut St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456780', 'Peter Brown', '6789012345', '678901234', 'Vietnamese', 'Kinh', 'None', 'Married', '678901234', '2010-12-15', 'Hanoi'),
(1, 1, 'Admin User', NULL, '1990-01-01', '123 Main St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456789', 'Jane Doe', '1234567890', '123456789', 'Vietnamese', 'Kinh', 'None', 'Single', '123456789', '2010-01-01', 'Hanoi'),
(2, 2, 'HR Manager', NULL, '1985-05-15', '456 Park Ave, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456788', 'John Smith', '0987654321', '987654321', 'Vietnamese', 'Kinh', 'None', 'Married', '987654321', '2012-05-15', 'Hanoi'),
(3, 3, 'IT Manager', NULL, '1988-08-20', '789 Oak St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456787', 'Mary Wilson', '5678901234', '567890123', 'Vietnamese', 'Kinh', 'None', 'Married', '567890123', '2008-08-20', 'Hanoi'),
(4, 4, 'Finance Manager', NULL, '1987-03-10', '321 Pine St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456786', 'Sarah Brown', '4321098765', '432109876', 'Vietnamese', 'Kinh', 'None', 'Single', '432109876', '2007-03-10', 'Hanoi'),
(5, 5, 'Marketing Manager', NULL, '1986-11-25', '654 Elm St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456785', 'David Lee', '8765432109', '876543210', 'Vietnamese', 'Kinh', 'None', 'Married', '876543210', '2006-11-25', 'Hanoi'),
(6, 6, 'Operations Manager', NULL, '1989-07-30', '987 Maple St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456784', 'Lisa Chen', '2345678901', '234567890', 'Vietnamese', 'Kinh', 'None', 'Single', '234567890', '2009-07-30', 'Hanoi'),
(7, 7, 'HR Specialist', NULL, '1992-02-14', '147 Cedar St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456783', 'Mike Johnson', '3456789012', '345678901', 'Vietnamese', 'Kinh', 'None', 'Single', '345678901', '2012-02-14', 'Hanoi'),
(8, 8, 'Software Developer', NULL, '1991-09-05', '258 Birch St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456782', 'Anna Davis', '4567890123', '456789012', 'Vietnamese', 'Kinh', 'None', 'Married', '456789012', '2011-09-05', 'Hanoi'),
(9, 9, 'Accountant', NULL, '1993-04-20', '369 Spruce St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456781', 'Tom Wilson', '5678901234', '567890123', 'Vietnamese', 'Kinh', 'None', 'Single', '567890123', '2013-04-20', 'Hanoi'),
(10, 10, 'Marketing Specialist', NULL, '1990-12-15', '741 Walnut St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456780', 'Peter Brown', '6789012345', '678901234', 'Vietnamese', 'Kinh', 'None', 'Married', '678901234', '2010-12-15', 'Hanoi'),
(1, 1, 'Admin User', NULL, '1990-01-01', '123 Main St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456789', 'Jane Doe', '1234567890', '123456789', 'Vietnamese', 'Kinh', 'None', 'Single', '123456789', '2010-01-01', 'Hanoi'),
(2, 2, 'HR Manager', NULL, '1985-05-15', '456 Park Ave, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456788', 'John Smith', '0987654321', '987654321', 'Vietnamese', 'Kinh', 'None', 'Married', '987654321', '2012-05-15', 'Hanoi'),
(3, 3, 'IT Manager', NULL, '1988-08-20', '789 Oak St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456787', 'Mary Wilson', '5678901234', '567890123', 'Vietnamese', 'Kinh', 'None', 'Married', '567890123', '2008-08-20', 'Hanoi'),
(4, 4, 'Finance Manager', NULL, '1987-03-10', '321 Pine St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456786', 'Sarah Brown', '4321098765', '432109876', 'Vietnamese', 'Kinh', 'None', 'Single', '432109876', '2007-03-10', 'Hanoi'),
(5, 5, 'Marketing Manager', NULL, '1986-11-25', '654 Elm St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456785', 'David Lee', '8765432109', '876543210', 'Vietnamese', 'Kinh', 'None', 'Married', '876543210', '2006-11-25', 'Hanoi'),
(6, 6, 'Operations Manager', NULL, '1989-07-30', '987 Maple St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456784', 'Lisa Chen', '2345678901', '234567890', 'Vietnamese', 'Kinh', 'None', 'Single', '234567890', '2009-07-30', 'Hanoi'),
(7, 7, 'HR Specialist', NULL, '1992-02-14', '147 Cedar St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456783', 'Mike Johnson', '3456789012', '345678901', 'Vietnamese', 'Kinh', 'None', 'Single', '345678901', '2012-02-14', 'Hanoi'),
(8, 8, 'Software Developer', NULL, '1991-09-05', '258 Birch St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456782', 'Anna Davis', '4567890123', '456789012', 'Vietnamese', 'Kinh', 'None', 'Married', '456789012', '2011-09-05', 'Hanoi'),
(9, 9, 'Accountant', NULL, '1993-04-20', '369 Spruce St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456781', 'Tom Wilson', '5678901234', '567890123', 'Vietnamese', 'Kinh', 'None', 'Single', '567890123', '2013-04-20', 'Hanoi'),
(10, 10, 'Marketing Specialist', NULL, '1990-12-15', '741 Walnut St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456780', 'Peter Brown', '6789012345', '678901234', 'Vietnamese', 'Kinh', 'None', 'Married', '678901234', '2010-12-15', 'Hanoi'),
(1, 1, 'Admin User', NULL, '1990-01-01', '123 Main St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456789', 'Jane Doe', '1234567890', '123456789', 'Vietnamese', 'Kinh', 'None', 'Single', '123456789', '2010-01-01', 'Hanoi'),
(2, 2, 'HR Manager', NULL, '1985-05-15', '456 Park Ave, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456788', 'John Smith', '0987654321', '987654321', 'Vietnamese', 'Kinh', 'None', 'Married', '987654321', '2012-05-15', 'Hanoi'),
(3, 3, 'IT Manager', NULL, '1988-08-20', '789 Oak St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456787', 'Mary Wilson', '5678901234', '567890123', 'Vietnamese', 'Kinh', 'None', 'Married', '567890123', '2008-08-20', 'Hanoi'),
(4, 4, 'Finance Manager', NULL, '1987-03-10', '321 Pine St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456786', 'Sarah Brown', '4321098765', '432109876', 'Vietnamese', 'Kinh', 'None', 'Single', '432109876', '2007-03-10', 'Hanoi'),
(5, 5, 'Marketing Manager', NULL, '1986-11-25', '654 Elm St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456785', 'David Lee', '8765432109', '876543210', 'Vietnamese', 'Kinh', 'None', 'Married', '876543210', '2006-11-25', 'Hanoi'),
(6, 6, 'Operations Manager', NULL, '1989-07-30', '987 Maple St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456784', 'Lisa Chen', '2345678901', '234567890', 'Vietnamese', 'Kinh', 'None', 'Single', '234567890', '2009-07-30', 'Hanoi'),
(7, 7, 'HR Specialist', NULL, '1992-02-14', '147 Cedar St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456783', 'Mike Johnson', '3456789012', '345678901', 'Vietnamese', 'Kinh', 'None', 'Single', '345678901', '2012-02-14', 'Hanoi'),
(8, 8, 'Software Developer', NULL, '1991-09-05', '258 Birch St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Male', '0123456782', 'Anna Davis', '4567890123', '456789012', 'Vietnamese', 'Kinh', 'None', 'Married', '456789012', '2011-09-05', 'Hanoi'),
(9, 9, 'Accountant', NULL, '1993-04-20', '369 Spruce St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456781', 'Tom Wilson', '5678901234', '567890123', 'Vietnamese', 'Kinh', 'None', 'Single', '567890123', '2013-04-20', 'Hanoi'),
(10, 10, 'Marketing Specialist', NULL, '1990-12-15', '741 Walnut St, Hanoi', NULL, '2025-04-21 14:57:56', '2025-04-21 14:57:56', 'Female', '0123456780', 'Peter Brown', '6789012345', '678901234', 'Vietnamese', 'Kinh', 'None', 'Married', '678901234', '2010-12-15', 'Hanoi'),
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
-- Table structure for table `work_schedules`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `work_schedules`
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
(10, 10, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(1, 1, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(2, 2, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(3, 3, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(4, 4, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(5, 5, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(6, 6, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(7, 7, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(8, 8, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(9, 9, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(10, 10, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(1, 1, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(2, 2, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(3, 3, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(4, 4, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(5, 5, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(6, 6, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(7, 7, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(8, 8, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(9, 9, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(10, 10, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(1, 1, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(2, 2, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(3, 3, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(4, 4, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(5, 5, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(6, 6, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(7, 7, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(8, 8, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(9, 9, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
(10, 10, '2024-03-18', '08:00:00', '17:00:00', 'normal', '2025-04-21 14:57:56', '2025-04-21 14:57:56'),
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
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activities_user_id_foreign` (`user_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_attendance_date_user` (`attendance_date`,`user_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_audit_logs_user_id` (`user_id`),
  ADD KEY `idx_audit_logs_timestamp` (`timestamp`),
  ADD KEY `idx_audit_logs_action_type` (`action_type`),
  ADD KEY `idx_audit_logs_target` (`target_entity`,`target_entity_id`);

--
-- Indexes for table `benefits`
--
ALTER TABLE `benefits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bonuses`
--
ALTER TABLE `bonuses`
  ADD PRIMARY KEY (`bonus_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `added_by_user_id` (`added_by_user_id`);

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `position_id` (`position_id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `degrees`
--
ALTER TABLE `degrees`
  ADD PRIMARY KEY (`degree_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `manager_id` (`manager_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `document_versions`
--
ALTER TABLE `document_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `email_verification_tokens`
--
ALTER TABLE `email_verification_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_code` (`employee_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `position_id` (`position_id`);

--
-- Indexes for table `employee_positions`
--
ALTER TABLE `employee_positions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `position_id` (`position_id`);

--
-- Indexes for table `employee_trainings`
--
ALTER TABLE `employee_trainings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `training_id` (`training_id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `equipment_assignments`
--
ALTER TABLE `equipment_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `evaluator_id` (`evaluator_id`);

--
-- Indexes for table `family_members`
--
ALTER TABLE `family_members`
  ADD PRIMARY KEY (`family_member_id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `insurance`
--
ALTER TABLE `insurance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `interviews`
--
ALTER TABLE `interviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `interviewer_id` (`interviewer_id`);

--
-- Indexes for table `job_positions`
--
ALTER TABLE `job_positions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `kpi`
--
ALTER TABLE `kpi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_leave_status` (`status`,`employee_id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_address` (`ip_address`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `onboarding`
--
ALTER TABLE `onboarding`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`payroll_id`),
  ADD UNIQUE KEY `uq_payroll_user_month_year` (`user_id`,`payroll_month`,`payroll_year`),
  ADD KEY `generated_by_user_id` (`generated_by_user_id`),
  ADD KEY `idx_payroll_month_year` (`payroll_month`,`payroll_year`);

--
-- Indexes for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `performances`
--
ALTER TABLE `performances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `reviewer_id` (`reviewer_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_code` (`code`);

--
-- Indexes for table `policies`
--
ALTER TABLE `policies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `manager_id` (`manager_id`);

--
-- Indexes for table `project_resources`
--
ALTER TABLE `project_resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project_tasks`
--
ALTER TABLE `project_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_ip_endpoint` (`ip_address`,`endpoint`);

--
-- Indexes for table `recruitment`
--
ALTER TABLE `recruitment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `position_id` (`position_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `salary_history`
--
ALTER TABLE `salary_history`
  ADD PRIMARY KEY (`salary_history_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `recorded_by_user_id` (`recorded_by_user_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `assigned_by` (`assigned_by`);

--
-- Indexes for table `trainings`
--
ALTER TABLE `trainings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training_courses`
--
ALTER TABLE `training_courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training_evaluations`
--
ALTER TABLE `training_evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registration_id` (`registration_id`),
  ADD KEY `evaluator_id` (`evaluator_id`);

--
-- Indexes for table `training_registrations`
--
ALTER TABLE `training_registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD KEY `fk_user_profiles_user` (`user_id`);

--
-- Indexes for table `work_schedules`
--
ALTER TABLE `work_schedules`
  ADD KEY `fk_work_schedules_employee` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `benefits`
--
ALTER TABLE `benefits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `document_versions`
--
ALTER TABLE `document_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `insurance`
--
ALTER TABLE `insurance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `interviews`
--
ALTER TABLE `interviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_positions`
--
ALTER TABLE `job_positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kpi`
--
ALTER TABLE `kpi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `onboarding`
--
ALTER TABLE `onboarding`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `payroll_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `payrolls`
--
ALTER TABLE `payrolls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `policies`
--
ALTER TABLE `policies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_resources`
--
ALTER TABLE `project_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_tasks`
--
ALTER TABLE `project_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recruitment`
--
ALTER TABLE `recruitment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `training_courses`
--
ALTER TABLE `training_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `training_evaluations`
--
ALTER TABLE `training_evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `training_registrations`
--
ALTER TABLE `training_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_attendance_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `bonuses`
--
ALTER TABLE `bonuses`
  ADD CONSTRAINT `fk_bonuses_added_by` FOREIGN KEY (`added_by_user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_bonuses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`);

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `contracts_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `fk_departments_manager` FOREIGN KEY (`manager_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `document_versions`
--
ALTER TABLE `document_versions`
  ADD CONSTRAINT `document_versions_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`),
  ADD CONSTRAINT `document_versions_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_ibfk_3` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `equipment_assignments`
--
ALTER TABLE `equipment_assignments`
  ADD CONSTRAINT `fk_equipment_assignments_employee` FOREIGN KEY (`employee_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `evaluations_ibfk_2` FOREIGN KEY (`evaluator_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `insurance`
--
ALTER TABLE `insurance`
  ADD CONSTRAINT `insurance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `interviews`
--
ALTER TABLE `interviews`
  ADD CONSTRAINT `interviews_ibfk_1` FOREIGN KEY (`interviewer_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `job_positions`
--
ALTER TABLE `job_positions`
  ADD CONSTRAINT `job_positions_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `kpi`
--
ALTER TABLE `kpi`
  ADD CONSTRAINT `kpi_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `fk_leaves_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_leaves_employee` FOREIGN KEY (`employee_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `onboarding`
--
ALTER TABLE `onboarding`
  ADD CONSTRAINT `onboarding_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `fk_payroll_generated_by` FOREIGN KEY (`generated_by_user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_payroll_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD CONSTRAINT `payrolls_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `fk_positions_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`manager_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `project_resources`
--
ALTER TABLE `project_resources`
  ADD CONSTRAINT `project_resources_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

--
-- Constraints for table `project_tasks`
--
ALTER TABLE `project_tasks`
  ADD CONSTRAINT `project_tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `project_tasks_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `employees` (`id`);

--
-- Constraints for table `recruitment`
--
ALTER TABLE `recruitment`
  ADD CONSTRAINT `recruitment_ibfk_1` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`),
  ADD CONSTRAINT `recruitment_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `salary_history`
--
ALTER TABLE `salary_history`
  ADD CONSTRAINT `fk_salary_history_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_tasks_assigned_by` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_tasks_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `training_evaluations`
--
ALTER TABLE `training_evaluations`
  ADD CONSTRAINT `training_evaluations_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `training_registrations` (`id`),
  ADD CONSTRAINT `training_evaluations_ibfk_2` FOREIGN KEY (`evaluator_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `training_registrations`
--
ALTER TABLE `training_registrations`
  ADD CONSTRAINT `training_registrations_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `training_registrations_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `training_courses` (`id`);

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `fk_user_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `work_schedules`
--
ALTER TABLE `work_schedules`
  ADD CONSTRAINT `fk_work_schedules_employee` FOREIGN KEY (`employee_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
