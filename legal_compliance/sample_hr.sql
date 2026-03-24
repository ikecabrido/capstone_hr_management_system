-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2026 at 02:23 PM
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
-- Database: `sample_hr`
--

-- --------------------------------------------------------

--
-- Table structure for table `allowances`
--

CREATE TABLE `allowances` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `type` enum('fixed','percentage') DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `allowances`
--

INSERT INTO `allowances` (`id`, `name`, `type`, `amount`) VALUES
(1, 'Rice Allowance', 'fixed', 1500.00),
(2, 'Teaching Load Allowance', 'fixed', 4000.00),
(3, 'Transportation Allowance', 'fixed', 1200.00);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `status` enum('present','absent','late','on_leave') DEFAULT 'present'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `date`, `time_in`, `time_out`, `status`) VALUES
(1, 1, '2026-02-01', '08:00:00', '17:00:00', 'present'),
(2, 1, '2026-02-02', '08:05:00', '17:00:00', 'late'),
(3, 1, '2026-02-03', NULL, NULL, 'absent'),
(4, 2, '2026-02-01', '08:00:00', '17:00:00', 'present'),
(5, 2, '2026-02-02', '08:00:00', '17:00:00', 'present'),
(6, 3, '2026-02-01', NULL, NULL, 'absent'),
(7, 3, '2026-02-02', '08:15:00', '17:00:00', 'late'),
(8, 4, '2026-02-01', '08:00:00', '17:00:00', 'present'),
(9, 5, '2026-02-01', '08:00:00', '17:00:00', 'present'),
(10, 5, '2026-02-02', NULL, NULL, 'absent'),
(11, 6, '2026-02-01', '08:00:00', '17:00:00', 'present'),
(12, 6, '2026-02-02', '08:10:00', '17:00:00', 'late');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `compliance_categories`
--

CREATE TABLE `compliance_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `weight` int(11) DEFAULT 10,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `compliance_categories`
--

INSERT INTO `compliance_categories` (`id`, `name`, `description`, `weight`, `is_active`, `created_at`) VALUES
(1, 'Employment Compliance', 'Checks if employee status follows the law (probation period, contract validity, job classification)', 90, 1, '2026-03-17 06:47:27'),
(2, 'Leave Law Compliance', 'Maternity, Paternity, Solo Parent, Service Incentive Leave compliance', 20, 1, '2026-03-17 06:47:27'),
(3, 'Benefits Compliance', 'SSS, PhilHealth, Pag-IBIG government-mandated benefits', 20, 1, '2026-03-17 06:47:27'),
(4, 'Working Conditions Compliance', 'Working hours, overtime tracking, rest days', 15, 1, '2026-03-17 06:47:27'),
(5, 'Workplace Protection Compliance', 'Anti-sexual harassment, Safe Spaces, incident handling', 15, 1, '2026-03-17 06:47:27'),
(6, 'Data Privacy Compliance', 'Employee consent records, access logs, data protection', 10, 1, '2026-03-17 06:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `compliance_checks`
--

CREATE TABLE `compliance_checks` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `employee_id` int(11) NOT NULL,
  `law_type` varchar(100) NOT NULL,
  `status` enum('compliant','at_risk','non_compliant') DEFAULT 'compliant',
  `remarks` text DEFAULT NULL,
  `date_checked` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `compliance_items`
--

CREATE TABLE `compliance_items` (
  `id` int(11) NOT NULL,
  `compliance_id` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT 'Legal',
  `description` text DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('Pending','Compliant','Overdue') DEFAULT 'Pending',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `compliance_logs`
--

CREATE TABLE `compliance_logs` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `compliance_logs`
--

INSERT INTO `compliance_logs` (`id`, `employee_id`, `action`, `details`, `created_at`) VALUES
(1, 3, 'REMINDER_SENT', 'Please complete your pending compliance requirements.', '2026-03-17 08:58:58'),
(2, 3, 'REMINDER_SENT', 'Please complete your pending compliance requirements.', '2026-03-17 11:25:05'),
(3, 3, 'REMINDER_SENT', 'Please complete your pending compliance requirements.', '2026-03-17 11:56:33'),
(4, 3, 'REMINDER_SENT', 'Subject: Compliance Reminder - Action Required | Message: Dear Employee,\r\n\r\nThis is a reminder regarding your pending compliance requirements. Please complete the necessary documents and training at your earliest convenience.\r\n\r\nThank you,\r\nHuman Resources Department', '2026-03-17 12:06:28'),
(5, 4, 'REMINDER_SENT', 'Subject: Compliance Reminder - Action Required | Message: Dear Employee,\r\n\r\nThis is a reminder regarding your pending compliance requirements. Please complete the necessary documents and training at your earliest convenience.\r\n\r\nThank you,\r\nHuman Resources Department', '2026-03-17 13:44:32'),
(6, 4, 'REMINDER_SENT', 'Subject: Compliance Reminder - Action Required | Message: Dear Employee,\r\n\r\nThis is a reminder regarding your pending compliance requirements. Please complete the necessary documents and training at your earliest convenience.\r\n\r\nThank you,\r\nHuman Resources Department', '2026-03-17 13:44:38'),
(7, 6, 'REMINDER_SENT', 'Subject: Compliance Reminder - Action Required | Message: Dear Employee,\r\n\r\nThis is a reminder regarding your pending compliance requirements. Please complete the necessary documents and training at your earliest convenience.\r\n\r\nThank you,\r\nHuman Resources Department', '2026-03-18 16:00:26'),
(8, 17, 'REMINDER_SENT', 'Subject: Compliance Reminder - Action Required | Message: Dear Employee,\r\n\r\nThis is a reminder regarding your pending compliance requirements. Please complete the necessary documents and training at your earliest convenience.\r\n\r\nThank you,\r\nHuman Resources Department', '2026-03-18 16:04:32'),
(9, 12, 'REMINDER_SENT', 'Subject: Compliance Reminder - Action Required | Message: Dear Employee,\r\n\r\nThis is a reminder regarding your pending compliance requirements. Please complete the necessary documents and training at your earliest convenience.\r\n\r\nThank you,\r\nHuman Resources Department', '2026-03-19 04:33:19'),
(10, 3, 'REMINDER_SENT', 'Subject: Compliance Reminder - Action Required | Message: Dear Employee,\r\n\r\nThis is a reminder regarding your pending compliance requirements. Please complete the necessary documents and training at your earliest convenience.\r\n\r\nThank you,\r\nHuman Resources Department', '2026-03-19 04:42:03'),
(11, 3, 'REMINDER_SENT', 'Subject: Compliance Reminder - Action Required | Message: Dear Employee,\r\n\r\nThis is a reminder regarding your pending compliance requirements. Please complete the necessary documents and training at your earliest convenience.\r\n\r\nThank you,\r\nHuman Resources Department', '2026-03-19 05:05:17');

-- --------------------------------------------------------

--
-- Table structure for table `compliance_records`
--

CREATE TABLE `compliance_records` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `law_type` varchar(100) NOT NULL COMMENT 'e.g., maternity_leave, paternity_leave, sss, philhealth, pagibig',
  `compliance_status` enum('compliant','at_risk','non_compliant') DEFAULT 'compliant',
  `last_checked` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `compliance_reports`
--

CREATE TABLE `compliance_reports` (
  `id` int(11) NOT NULL,
  `report_type` varchar(50) NOT NULL COMMENT 'employee_compliance, leave, incident, policy_acknowledgment',
  `title` varchar(255) NOT NULL,
  `generated_by` int(11) NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `parameters` text DEFAULT NULL COMMENT 'JSON of filters used',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `compliance_results`
--

CREATE TABLE `compliance_results` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `rule_id` int(11) NOT NULL,
  `result` enum('compliant','at_risk','non_compliant') NOT NULL,
  `actual_value` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `checked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `compliance_rules`
--

CREATE TABLE `compliance_rules` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `law_name` varchar(255) NOT NULL,
  `rule_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `condition_field` varchar(100) NOT NULL,
  `condition_operator` varchar(20) NOT NULL,
  `expected_value` varchar(255) DEFAULT NULL,
  `severity` enum('critical','high','medium','low') DEFAULT 'medium',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `compliance_rules`
--

INSERT INTO `compliance_rules` (`id`, `category_id`, `law_name`, `rule_name`, `description`, `condition_field`, `condition_operator`, `expected_value`, `severity`, `is_active`, `created_at`) VALUES
(1, 1, 'Labor Code', 'Probationary Period Limit', 'Probationary employees should not exceed 6 months', 'employment_type', '=', 'Probationary', 'critical', 1, '2026-03-17 06:47:27'),
(2, 1, 'Labor Code', 'Contract Validity', 'Contract employees must have valid contract dates', 'contract_end_date', '>=', 'NOW()', 'high', 1, '2026-03-17 06:47:27'),
(3, 1, 'Labor Code', 'Job Classification', 'Employee must have correct job role classification', 'position_id', 'IS NOT', 'NULL', 'medium', 1, '2026-03-17 06:47:27'),
(4, 2, 'RA 11210', 'Maternity Leave Eligibility', 'Female employees employed >= 6 months are eligible for 105 days maternity leave', 'employment_type', '=', 'Regular', 'critical', 1, '2026-03-17 06:47:27'),
(5, 2, 'RA 8187', 'Paternity Leave Usage', 'Married fathers entitled to 7 days paternity leave per delivery', 'paternity_leave_used', '>=', '0', 'high', 1, '2026-03-17 06:47:27'),
(6, 2, 'RA 8972', 'Solo Parent Leave', 'Solo parents entitled to 7 days leave', 'solo_parent_status', '=', '1', 'high', 1, '2026-03-17 06:47:27'),
(7, 2, 'Labor Code', 'Service Incentive Leave', 'Employees with >= 1 year service entitled to 5 days SIL', 'sil_balance', '>=', '5', 'medium', 1, '2026-03-17 06:47:27'),
(8, 3, 'SSS Act', 'SSS Contribution', 'All employees must have SSS contribution', 'sss_number', 'IS NOT', 'NULL', 'critical', 1, '2026-03-17 06:47:27'),
(9, 3, 'PhilHealth Act', 'PhilHealth Coverage', 'All employees must have PhilHealth coverage', 'philhealth_number', 'IS NOT', 'NULL', 'critical', 1, '2026-03-17 06:47:27'),
(10, 3, 'Pag-IBIG Act', 'Pag-IBIG Coverage', 'All employees must have Pag-IBIG coverage', 'pagibig_number', 'IS NOT', 'NULL', 'critical', 1, '2026-03-17 06:47:27'),
(11, 3, 'SSS Act', 'SSS Contribution Status', 'SSS contributions must be current/not delayed', 'sss_status', '=', 'current', 'high', 1, '2026-03-17 06:47:27'),
(12, 4, 'Labor Code', 'Working Hours', 'Regular working hours should not exceed 8 hours/day', 'daily_hours', '<=', '8', 'high', 1, '2026-03-17 06:47:27'),
(13, 4, 'Labor Code', 'Overtime Tracking', 'Overtime must be properly documented', 'ot_hours', '>=', '0', 'medium', 1, '2026-03-17 06:47:27'),
(14, 4, 'Labor Code', 'Rest Day', 'Employees must have at least 1 rest day per week', 'rest_days_per_week', '>=', '1', 'high', 1, '2026-03-17 06:47:27'),
(15, 4, 'Labor Code', 'Overtime Limit', 'Overtime should not exceed 8 hours per week', 'weekly_ot', '<=', '8', 'medium', 1, '2026-03-17 06:47:27'),
(16, 5, 'RA 7877', 'Anti-Sexual Harassment Policy', 'Anti-sexual harassment policy must be acknowledged', 'harassment_policy_acknowledged', '=', '1', 'critical', 1, '2026-03-17 06:47:27'),
(17, 5, 'RA 11313', 'Safe Spaces Compliance', 'Safe Spaces policy must be in place and acknowledged', 'safe_spaces_acknowledged', '=', '1', 'high', 1, '2026-03-17 06:47:27'),
(18, 5, 'RA 11058', 'OSH Policy', 'Occupational Safety and Health policy must be acknowledged', 'osh_policy_acknowledged', '=', '1', 'high', 1, '2026-03-17 06:47:27'),
(19, 5, 'Labor Code', 'Incident Reporting', 'All incidents must be properly reported and resolved', 'unresolved_incidents', '=', '0', 'high', 1, '2026-03-17 06:47:27'),
(20, 6, 'RA 10173', 'Data Privacy Consent', 'Employee must have signed data privacy consent', 'data_consent', '=', '1', 'critical', 1, '2026-03-17 06:47:27'),
(21, 6, 'RA 10173', 'Privacy Policy Acknowledgment', 'Employee must acknowledge privacy policy', 'privacy_policy_acknowledged', '=', '1', 'high', 1, '2026-03-17 06:47:27'),
(22, 6, 'RA 10173', 'Data Access Logs', 'Data access must be logged', 'access_logs_exist', '=', '1', 'medium', 1, '2026-03-17 06:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `compliance_summary`
--

CREATE TABLE `compliance_summary` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `employment_score` decimal(5,2) DEFAULT 0.00,
  `leave_score` decimal(5,2) DEFAULT 0.00,
  `benefits_score` decimal(5,2) DEFAULT 0.00,
  `working_conditions_score` decimal(5,2) DEFAULT 0.00,
  `workplace_protection_score` decimal(5,2) DEFAULT 0.00,
  `data_privacy_score` decimal(5,2) DEFAULT 0.00,
  `overall_score` decimal(5,2) DEFAULT 0.00,
  `status` enum('compliant','at_risk','non_compliant') DEFAULT 'non_compliant',
  `critical_issues` int(11) DEFAULT 0,
  `high_risks` int(11) DEFAULT 0,
  `medium_risks` int(11) DEFAULT 0,
  `low_risks` int(11) DEFAULT 0,
  `last_checked` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `compliance_summary`
--

INSERT INTO `compliance_summary` (`id`, `employee_id`, `employment_score`, `leave_score`, `benefits_score`, `working_conditions_score`, `workplace_protection_score`, `data_privacy_score`, `overall_score`, `status`, `critical_issues`, `high_risks`, `medium_risks`, `low_risks`, `last_checked`) VALUES
(1, 1, 94.00, 90.00, 89.00, 88.00, 91.00, 94.00, 91.00, 'compliant', 0, 0, 1, 1, '2026-03-17 09:32:23'),
(2, 2, 90.00, 86.00, 85.00, 84.00, 87.00, 90.00, 87.00, 'compliant', 0, 0, 1, 1, '2026-03-17 09:32:23'),
(3, 3, 78.00, 75.00, 74.00, 72.00, 76.00, 81.00, 76.00, 'at_risk', 1, 1, 1, 1, '2026-03-17 09:32:23'),
(4, 4, 85.00, 82.00, 80.00, 78.00, 82.00, 85.00, 82.00, 'at_risk', 0, 1, 2, 1, '2026-03-17 09:32:23'),
(5, 5, 65.00, 60.00, 58.00, 60.00, 65.00, 70.00, 63.00, 'non_compliant', 1, 2, 2, 1, '2026-03-17 09:32:23'),
(6, 6, 87.00, 84.00, 82.00, 80.00, 84.00, 87.00, 84.00, 'compliant', 0, 0, 1, 1, '2026-03-17 09:32:23'),
(7, 7, 93.00, 90.00, 88.00, 86.00, 90.00, 93.00, 90.00, 'compliant', 0, 0, 1, 0, '2026-03-17 09:32:23'),
(8, 8, 87.00, 84.00, 82.00, 80.00, 84.00, 87.00, 84.00, 'compliant', 0, 0, 1, 1, '2026-03-17 09:32:23'),
(9, 9, 76.00, 72.00, 70.00, 68.00, 73.00, 80.00, 73.00, 'at_risk', 0, 1, 2, 1, '2026-03-17 09:32:23'),
(10, 10, 84.00, 81.00, 79.00, 77.00, 81.00, 84.00, 81.00, 'at_risk', 0, 1, 1, 1, '2026-03-17 09:32:23'),
(11, 11, 96.00, 94.00, 92.00, 90.00, 94.00, 98.00, 94.00, 'compliant', 0, 0, 0, 0, '2026-03-17 09:32:23'),
(12, 12, 91.00, 88.00, 86.00, 84.00, 88.00, 91.00, 88.00, 'compliant', 0, 0, 1, 0, '2026-03-17 09:32:23'),
(13, 13, 79.00, 76.00, 74.00, 72.00, 76.00, 79.00, 76.00, 'at_risk', 0, 1, 2, 1, '2026-03-17 09:32:23'),
(14, 14, 86.00, 83.00, 81.00, 79.00, 83.00, 86.00, 83.00, 'compliant', 0, 0, 1, 1, '2026-03-17 09:32:23');

-- --------------------------------------------------------

--
-- Table structure for table `compliance_tasks`
--

CREATE TABLE `compliance_tasks` (
  `id` int(11) NOT NULL,
  `compliance_id` int(11) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `task_name` varchar(255) NOT NULL,
  `deadline` date DEFAULT NULL,
  `status` enum('Pending','Completed') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_privacy_consents`
--

CREATE TABLE `data_privacy_consents` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `consent_type` varchar(50) NOT NULL COMMENT 'data_processing, marketing, third_party',
  `is_consented` tinyint(1) DEFAULT 0,
  `consent_date` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deductions`
--

CREATE TABLE `deductions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `type` enum('fixed','percentage') DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `is_statutory` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deductions`
--

INSERT INTO `deductions` (`id`, `name`, `type`, `amount`, `is_statutory`) VALUES
(1, 'SSS', 'fixed', 1125.00, 1),
(2, 'PhilHealth', 'fixed', 450.00, 1),
(3, 'Pag-IBIG', 'fixed', 200.00, 1),
(4, 'Withholding Tax', 'fixed', 2500.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(10) UNSIGNED NOT NULL,
  `department_code` varchar(20) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `manager_id` int(10) UNSIGNED DEFAULT NULL,
  `parent_department_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_code`, `department_name`, `description`, `manager_id`, `parent_department_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'EXEC', 'Executive Administration', 'Top-level management and executive functionss', NULL, NULL, 'Active', '2026-02-11 12:51:52', '2026-02-11 13:42:00'),
(2, 'PRES', 'Office of the President', 'Executive leadership of the institution - Led by: Dr. Maria M. Vicente (President/CEO)', NULL, 1, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(3, 'EVP', 'Executive Vice President', 'Executive Vice President office - Led by: Ms. Edith M. Vicente (EVP)', NULL, 1, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(4, 'VPAA', 'Vice President for Academic Affairs', 'Oversees all academic programs and faculty - Led by: Dr. Charlie I. Cariño (VPAA)', NULL, 1, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(5, 'VPAD', 'Vice President for Admin and Finance', 'Oversees administrative and financial operations - Led by: Engr. Diosdado T. Lleno (VPAD/School Director)', NULL, 1, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(10, 'COE', 'College of Engineering', 'Engineering and technical programs', NULL, 15, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(11, 'COIT', 'College of Information Technology', 'IT and computer science programs', NULL, 15, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(12, 'CHTBAM', 'College of Hospitality, Tourism, and Business Asset Management', 'Hospitality, Tourism, and Business Management programs - Led by: Dr. Ryan M. Ignacio (Dean)', NULL, 4, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(13, 'COED', 'College of Education', 'Teacher education programs', NULL, 15, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(14, 'CON', 'College of Nursing', 'Nursing and healthcare programs', NULL, 15, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(15, 'CAS', 'College of Arts and Sciences', 'Arts, Sciences, and Humanities programs', NULL, 4, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(16, 'COBA', 'College of Business and Accountancy', 'Business and Accountancy programs', NULL, 15, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(17, 'CCJ', 'College of Criminal Justice', 'Criminology and justice programs', NULL, 15, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(20, 'SHS', 'Senior High School', 'Senior High School department - Led by: Dr. Romeo L. Fernandez (Principal)', NULL, 4, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(21, 'STEM', 'Academic Track - STEM', 'Science, Technology, Engineering, Mathematics strand', NULL, 20, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(22, 'ABM', 'Academic Track - ABM', 'Accountancy, Business, and Management strand', NULL, 20, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(23, 'HUMSS', 'Academic Track - HUMSS', 'Humanities and Social Sciences strand', NULL, 20, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(24, 'GAS', 'Academic Track - GAS', 'General Academic Strand', NULL, 20, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(25, 'TVLICT', 'TVL Track - ICT', 'Information and Communications Technology strand', NULL, 20, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(26, 'TVLHE', 'TVL Track - Home Economics', 'Home Economics strand', NULL, 20, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(27, 'TVLSMAW', 'TVL Track - Industrial Arts', 'Shielded Metal Arc Welding strand', NULL, 20, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(28, 'SPORTS', 'Sports Track', 'Sports and athletics programs', NULL, 20, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(29, 'ARDSGN', 'Arts and Design Track', 'Arts and Design programs', NULL, 20, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(30, 'HR', 'Human Resources Department', 'HR and administrative services - Led by: Elieza V. Caballero (OIC-HR)', NULL, 5, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(31, 'ACCT', 'Accounting Department', 'Financial management and accounting - Led by: Ms. Marianne A. Vicente (Head)', NULL, 5, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(32, 'FIN', 'Finance Office', 'Budget and financial operations', NULL, 5, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(33, 'REG', 'Registrar Office', 'Enrollment and registration services', NULL, 4, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(34, 'ADMISSION', 'Admission Office', 'Student recruitment and admission', NULL, 4, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(35, 'SPS', 'Student Personnel Services', 'Student services and support - Led by: Ms. Sarah C. Nogueras (Head)', NULL, 4, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(36, 'OSA', 'Office of Student Affairs', 'Student affairs and activities - Led by: Mr. Ian M. Erguiza (Coordinator)', NULL, 4, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(37, 'GUIDANCE', 'Guidance and Counseling', 'Academic counseling and student guidance', NULL, 36, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(38, 'LIBRARY', 'Library Services', 'Library resources and management', NULL, 4, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(39, 'ITSUPPORT', 'IT Support', 'Technical support and IT services', NULL, 5, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(40, 'SECURITY', 'Office of Safety and Security', 'Campus security and safety', NULL, 5, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(41, 'MAINTENANCE', 'Physical Plant Facilities', 'Campus maintenance and facilities', NULL, 5, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(42, 'CAMPUSMIN', 'Campus Ministry', 'Spiritual and religious activities - Led by: Dr. Anthony Bermudez (Volunteer-In-Charge)', NULL, 36, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(50, 'BULACAN', 'Bulacan Campus', 'Bestlink College - Bulacan Campus - Led by: Dr. Milagros O. Luang (School Director) - Led by: Dr. Rosalinda R. Deleste (President)', NULL, 1, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(51, 'BULACAN-SHS', 'Bulacan SHS', 'Senior High School - Bulacan Campus', NULL, 50, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(52, 'BULACAN-COL', 'Bulacan College', 'College programs - Bulacan Campus', NULL, 50, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(53, 'BULACAN-ADMIN', 'Bulacan Administration', 'Bulacan campus administration', NULL, 50, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(54, 'BULACAN-SEC', 'Bulacan Security', 'Security for Bulacan campus - Led by: Antonio Dawagan, Sr. (Head)', NULL, 50, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(60, 'QC-COORD', 'Quezon City Coordinator', 'QC campus coordination - Led by: Erickson Castro Mulawin (Coordinator)', NULL, 1, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(1, 'EXEC', 'Executive Administration', 'Top-level management and executive functionss', NULL, NULL, 'Active', '2026-02-11 12:51:52', '2026-02-11 13:42:00'),
(2, 'PRES', 'Office of the President', 'Executive leadership of the institution - Led by: Dr. Maria M. Vicente (President/CEO)', NULL, 1, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(3, 'EVP', 'Executive Vice President', 'Executive Vice President office - Led by: Ms. Edith M. Vicente (EVP)', NULL, 1, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(4, 'VPAA', 'Vice President for Academic Affairs', 'Oversees all academic programs and faculty - Led by: Dr. Charlie I. Cariño (VPAA)', NULL, 1, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(5, 'VPAD', 'Vice President for Admin and Finance', 'Oversees administrative and financial operations - Led by: Engr. Diosdado T. Lleno (VPAD/School Director)', NULL, 1, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(10, 'COE', 'College of Engineering', 'Engineering and technical programs', NULL, 15, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(11, 'COIT', 'College of Information Technology', 'IT and computer science programs', NULL, 15, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(12, 'CHTBAM', 'College of Hospitality, Tourism, and Business Asset Management', 'Hospitality, Tourism, and Business Management programs - Led by: Dr. Ryan M. Ignacio (Dean)', NULL, 4, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(13, 'COED', 'College of Education', 'Teacher education programs', NULL, 15, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(14, 'CON', 'College of Nursing', 'Nursing and healthcare programs', NULL, 15, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(15, 'CAS', 'College of Arts and Sciences', 'Arts, Sciences, and Humanities programs', NULL, 4, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(16, 'COBA', 'College of Business and Accountancy', 'Business and Accountancy programs', NULL, 15, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(17, 'CCJ', 'College of Criminal Justice', 'Criminology and justice programs', NULL, 15, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(20, 'SHS', 'Senior High School', 'Senior High School department - Led by: Dr. Romeo L. Fernandez (Principal)', NULL, 4, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(21, 'STEM', 'Academic Track - STEM', 'Science, Technology, Engineering, Mathematics strand', NULL, 20, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(22, 'ABM', 'Academic Track - ABM', 'Accountancy, Business, and Management strand', NULL, 20, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(23, 'HUMSS', 'Academic Track - HUMSS', 'Humanities and Social Sciences strand', NULL, 20, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(24, 'GAS', 'Academic Track - GAS', 'General Academic Strand', NULL, 20, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(25, 'TVLICT', 'TVL Track - ICT', 'Information and Communications Technology strand', NULL, 20, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(26, 'TVLHE', 'TVL Track - Home Economics', 'Home Economics strand', NULL, 20, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(27, 'TVLSMAW', 'TVL Track - Industrial Arts', 'Shielded Metal Arc Welding strand', NULL, 20, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(28, 'SPORTS', 'Sports Track', 'Sports and athletics programs', NULL, 20, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(29, 'ARDSGN', 'Arts and Design Track', 'Arts and Design programs', NULL, 20, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(30, 'HR', 'Human Resources Department', 'HR and administrative services - Led by: Elieza V. Caballero (OIC-HR)', NULL, 5, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(31, 'ACCT', 'Accounting Department', 'Financial management and accounting - Led by: Ms. Marianne A. Vicente (Head)', NULL, 5, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(32, 'FIN', 'Finance Office', 'Budget and financial operations', NULL, 5, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(33, 'REG', 'Registrar Office', 'Enrollment and registration services', NULL, 4, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(34, 'ADMISSION', 'Admission Office', 'Student recruitment and admission', NULL, 4, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(35, 'SPS', 'Student Personnel Services', 'Student services and support - Led by: Ms. Sarah C. Nogueras (Head)', NULL, 4, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(36, 'OSA', 'Office of Student Affairs', 'Student affairs and activities - Led by: Mr. Ian M. Erguiza (Coordinator)', NULL, 4, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(37, 'GUIDANCE', 'Guidance and Counseling', 'Academic counseling and student guidance', NULL, 36, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(38, 'LIBRARY', 'Library Services', 'Library resources and management', NULL, 4, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(39, 'ITSUPPORT', 'IT Support', 'Technical support and IT services', NULL, 5, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(40, 'SECURITY', 'Office of Safety and Security', 'Campus security and safety', NULL, 5, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(41, 'MAINTENANCE', 'Physical Plant Facilities', 'Campus maintenance and facilities', NULL, 5, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(42, 'CAMPUSMIN', 'Campus Ministry', 'Spiritual and religious activities - Led by: Dr. Anthony Bermudez (Volunteer-In-Charge)', NULL, 36, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(50, 'BULACAN', 'Bulacan Campus', 'Bestlink College - Bulacan Campus - Led by: Dr. Milagros O. Luang (School Director) - Led by: Dr. Rosalinda R. Deleste (President)', NULL, 1, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(51, 'BULACAN-SHS', 'Bulacan SHS', 'Senior High School - Bulacan Campus', NULL, 50, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(52, 'BULACAN-COL', 'Bulacan College', 'College programs - Bulacan Campus', NULL, 50, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(53, 'BULACAN-ADMIN', 'Bulacan Administration', 'Bulacan campus administration', NULL, 50, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(54, 'BULACAN-SEC', 'Bulacan Security', 'Security for Bulacan campus - Led by: Antonio Dawagan, Sr. (Head)', NULL, 50, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52'),
(60, 'QC-COORD', 'Quezon City Coordinator', 'QC campus coordination - Led by: Erickson Castro Mulawin (Coordinator)', NULL, 1, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52');

-- --------------------------------------------------------

--
-- Table structure for table `department_roles`
--

CREATE TABLE `department_roles` (
  `id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `system_role` varchar(50) NOT NULL,
  `redirect_page` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_roles`
--

INSERT INTO `department_roles` (`id`, `department_name`, `system_role`, `redirect_page`, `description`, `created_at`) VALUES
(1, 'Human Resources', 'hr_admin', 'legal_compliance/legal_compliance.php', 'HR Admin - Full HR access', '2026-03-18 03:37:11'),
(2, 'Information Technology', 'it_admin', 'time_attendance/time_attendance.php', 'IT Staff - System administration', '2026-03-18 03:37:11'),
(3, 'Finance', 'payroll', 'payroll/payroll.php', 'Finance - Payroll and accounting', '2026-03-18 03:37:11'),
(4, 'Legal', 'compliance', 'legal_compliance/legal_compliance.php', 'Legal - Compliance and legal', '2026-03-18 03:37:11'),
(5, 'Clinic', 'clinic', 'clinic/clinic.php', 'Health Services', '2026-03-18 03:37:11'),
(6, 'Academic', 'employee', 'employee_portal/employee_portal.php', 'Faculty/Staff Portal', '2026-03-18 03:37:11'),
(7, 'Administration', 'admin', 'legal_compliance/legal_compliance.php', 'Administrative access', '2026-03-18 03:37:11');

-- --------------------------------------------------------

--
-- Table structure for table `dependents`
--

CREATE TABLE `dependents` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(200) DEFAULT NULL,
  `relationship` enum('Spouse','Child','Parent','Sibling','Other') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `beneficiary_percentage` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dependents`
--

INSERT INTO `dependents` (`id`, `employee_id`, `full_name`, `relationship`, `date_of_birth`, `beneficiary_percentage`, `created_at`) VALUES
(17, 1, 'Maria Clara Doe', 'Spouse', '1987-04-20', 40.00, '2026-02-08 11:44:10'),
(18, 1, 'John Michael Doe Jr.', 'Child', '2012-08-15', 30.00, '2026-02-08 11:44:10'),
(19, 1, 'Sarah Elizabeth Doe', 'Child', '2015-12-03', 30.00, '2026-02-08 11:44:10'),
(23, 4, 'Susan Cheng Chen', 'Spouse', '1978-06-15', 30.00, '2026-02-08 11:44:11'),
(24, 4, 'Michael Robert Chen Jr.', 'Child', '2008-02-28', 25.00, '2026-02-08 11:44:11'),
(25, 4, 'Jennifer Anne Chen', 'Child', '2010-07-14', 25.00, '2026-02-08 11:44:11'),
(26, 4, 'David William Chen', 'Child', '2013-03-20', 20.00, '2026-02-08 11:44:11'),
(27, 6, 'James Edward Lee', 'Child', '2015-06-10', 50.00, '2026-02-08 11:44:11'),
(28, 8, 'Jennifer Taylor Thompson', 'Spouse', '1972-11-25', 35.00, '2026-02-08 11:44:11'),
(29, 8, 'Robert William Thompson IV', 'Child', '2005-08-30', 25.00, '2026-02-08 11:44:11'),
(30, 8, 'Emma Louise Thompson', 'Child', '2008-04-12', 20.00, '2026-02-08 11:44:11'),
(31, 8, 'William Henry Thompson', 'Child', '2010-09-05', 20.00, '2026-02-08 11:44:11'),
(32, 9, 'Antonio Ramirez Gonzales', 'Spouse', '1984-03-18', 40.00, '2026-02-08 11:44:11'),
(33, 9, 'Maria Isabella Gonzales', 'Child', '2013-07-22', 30.00, '2026-02-08 11:44:11'),
(34, 9, 'Antonio Miguel Gonzales', 'Child', '2016-01-15', 30.00, '2026-02-08 11:44:11'),
(35, 11, 'Carlos Rivera Bautista', 'Spouse', '1985-12-08', 40.00, '2026-02-08 11:44:11'),
(36, 11, 'Ana Sofia Bautista', 'Child', '2011-05-30', 30.00, '2026-02-08 11:44:11'),
(37, 11, 'Carlos Andres Bautista', 'Child', '2014-10-12', 30.00, '2026-02-08 11:44:11'),
(38, 12, 'James Daniel Dela Cruz', 'Child', '2010-03-25', 50.00, '2026-02-08 11:44:11'),
(39, 12, 'Paul Matthew Dela Cruz', 'Child', '2012-08-08', 50.00, '2026-02-08 11:44:11'),
(40, 14, 'Patricia Santos Mendoza', 'Spouse', '1986-09-22', 35.00, '2026-02-08 11:44:11'),
(41, 14, 'Kevin James Mendoza', 'Child', '2010-12-05', 25.00, '2026-02-08 11:44:11'),
(42, 14, 'Andrew Samuel Mendoza', 'Child', '2013-06-18', 20.00, '2026-02-08 11:44:11'),
(43, 14, 'Faith Marie Mendoza', 'Child', '2016-02-28', 20.00, '2026-02-08 11:44:11'),
(44, 18, 'Angela Marie Villanueva', 'Spouse', '1983-07-14', 40.00, '2026-02-08 11:44:11'),
(45, 18, 'Christopher Ian Villanueva', 'Child', '2009-04-25', 30.00, '2026-02-08 11:44:11'),
(46, 18, 'Joshua David Villanueva', 'Child', '2012-11-10', 30.00, '2026-02-08 11:44:11'),
(47, 20, 'Diana Rose Santos', 'Spouse', '1984-05-08', 35.00, '2026-02-08 11:44:11'),
(48, 20, 'Carlos Antonio Santos Jr.', 'Child', '2008-09-20', 25.00, '2026-02-08 11:44:11'),
(49, 20, 'Sofia Isabel Santos', 'Child', '2011-03-15', 20.00, '2026-02-08 11:44:11'),
(50, 20, 'Gabriel Miguel Santos', 'Child', '2014-07-30', 20.00, '2026-02-08 11:44:11'),
(17, 1, 'Maria Clara Doe', 'Spouse', '1987-04-20', 40.00, '2026-02-08 11:44:10'),
(18, 1, 'John Michael Doe Jr.', 'Child', '2012-08-15', 30.00, '2026-02-08 11:44:10'),
(19, 1, 'Sarah Elizabeth Doe', 'Child', '2015-12-03', 30.00, '2026-02-08 11:44:10'),
(23, 4, 'Susan Cheng Chen', 'Spouse', '1978-06-15', 30.00, '2026-02-08 11:44:11'),
(24, 4, 'Michael Robert Chen Jr.', 'Child', '2008-02-28', 25.00, '2026-02-08 11:44:11'),
(25, 4, 'Jennifer Anne Chen', 'Child', '2010-07-14', 25.00, '2026-02-08 11:44:11'),
(26, 4, 'David William Chen', 'Child', '2013-03-20', 20.00, '2026-02-08 11:44:11'),
(27, 6, 'James Edward Lee', 'Child', '2015-06-10', 50.00, '2026-02-08 11:44:11'),
(28, 8, 'Jennifer Taylor Thompson', 'Spouse', '1972-11-25', 35.00, '2026-02-08 11:44:11'),
(29, 8, 'Robert William Thompson IV', 'Child', '2005-08-30', 25.00, '2026-02-08 11:44:11'),
(30, 8, 'Emma Louise Thompson', 'Child', '2008-04-12', 20.00, '2026-02-08 11:44:11'),
(31, 8, 'William Henry Thompson', 'Child', '2010-09-05', 20.00, '2026-02-08 11:44:11'),
(32, 9, 'Antonio Ramirez Gonzales', 'Spouse', '1984-03-18', 40.00, '2026-02-08 11:44:11'),
(33, 9, 'Maria Isabella Gonzales', 'Child', '2013-07-22', 30.00, '2026-02-08 11:44:11'),
(34, 9, 'Antonio Miguel Gonzales', 'Child', '2016-01-15', 30.00, '2026-02-08 11:44:11'),
(35, 11, 'Carlos Rivera Bautista', 'Spouse', '1985-12-08', 40.00, '2026-02-08 11:44:11'),
(36, 11, 'Ana Sofia Bautista', 'Child', '2011-05-30', 30.00, '2026-02-08 11:44:11'),
(37, 11, 'Carlos Andres Bautista', 'Child', '2014-10-12', 30.00, '2026-02-08 11:44:11'),
(38, 12, 'James Daniel Dela Cruz', 'Child', '2010-03-25', 50.00, '2026-02-08 11:44:11'),
(39, 12, 'Paul Matthew Dela Cruz', 'Child', '2012-08-08', 50.00, '2026-02-08 11:44:11'),
(40, 14, 'Patricia Santos Mendoza', 'Spouse', '1986-09-22', 35.00, '2026-02-08 11:44:11'),
(41, 14, 'Kevin James Mendoza', 'Child', '2010-12-05', 25.00, '2026-02-08 11:44:11'),
(42, 14, 'Andrew Samuel Mendoza', 'Child', '2013-06-18', 20.00, '2026-02-08 11:44:11'),
(43, 14, 'Faith Marie Mendoza', 'Child', '2016-02-28', 20.00, '2026-02-08 11:44:11'),
(44, 18, 'Angela Marie Villanueva', 'Spouse', '1983-07-14', 40.00, '2026-02-08 11:44:11'),
(45, 18, 'Christopher Ian Villanueva', 'Child', '2009-04-25', 30.00, '2026-02-08 11:44:11'),
(46, 18, 'Joshua David Villanueva', 'Child', '2012-11-10', 30.00, '2026-02-08 11:44:11'),
(47, 20, 'Diana Rose Santos', 'Spouse', '1984-05-08', 35.00, '2026-02-08 11:44:11'),
(48, 20, 'Carlos Antonio Santos Jr.', 'Child', '2008-09-20', 25.00, '2026-02-08 11:44:11'),
(49, 20, 'Sofia Isabel Santos', 'Child', '2011-03-15', 20.00, '2026-02-08 11:44:11'),
(50, 20, 'Gabriel Miguel Santos', 'Child', '2014-07-30', 20.00, '2026-02-08 11:44:11');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `document_type` varchar(100) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` enum('Valid','Expired','Pending') DEFAULT 'Valid',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `employee_id`, `document_type`, `file_path`, `issue_date`, `expiry_date`, `status`, `uploaded_at`) VALUES
(5, 1, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-01-15', '2025-01-15', 'Valid', '2026-02-08 12:35:03'),
(6, 1, 'Professional License (LET)', 'documents/let_license.pdf', '2023-06-20', '2026-03-01', 'Valid', '2026-02-08 12:35:03'),
(7, 1, 'Training Certificate', 'documents/seminar_2023.pdf', '2023-11-10', NULL, 'Valid', '2026-02-08 12:35:03'),
(8, 1, 'Medical Certificate', 'documents/medical_2024.pdf', '2024-06-01', '2025-06-01', 'Valid', '2026-02-08 12:35:03'),
(13, 3, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-03-10', '2025-03-10', 'Valid', '2026-02-08 12:35:03'),
(14, 3, 'Professional License (ICT)', 'documents/ict_license.pdf', '2023-07-01', '2026-07-01', 'Valid', '2026-02-08 12:35:03'),
(15, 3, 'Training Certificate', 'documents/tech_seminar.pdf', '2024-01-25', NULL, 'Valid', '2026-02-08 12:35:03'),
(16, 3, 'IT Certification', 'documents/ccna_cert.pdf', '2023-05-15', '2026-05-15', 'Valid', '2026-02-08 12:35:03'),
(17, 4, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-01-05', '2025-01-05', 'Valid', '2026-02-08 12:35:03'),
(18, 4, 'Professional License (LET)', 'documents/let_license.pdf', '1998-04-20', '2026-02-17', 'Valid', '2026-02-08 12:35:03'),
(19, 4, 'Board Exam Result', 'documents/board_1998.pdf', '1998-04-15', NULL, 'Valid', '2026-02-08 12:35:03'),
(20, 4, 'Diploma', 'documents/masters_diploma.pdf', '2005-06-30', NULL, 'Valid', '2026-02-08 12:35:03'),
(21, 5, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-04-01', '2025-04-01', 'Valid', '2026-02-08 12:35:03'),
(22, 5, 'Professional License (LET)', 'documents/let_license.pdf', '2014-08-20', '2027-08-20', 'Valid', '2026-02-08 12:35:03'),
(23, 5, 'Transcript of Records', 'documents/tor_undergrad.pdf', '2014-03-15', NULL, 'Valid', '2026-02-08 12:35:03'),
(24, 5, 'Training Certificate', 'documents/educ_seminar.pdf', '2023-12-05', NULL, 'Valid', '2026-02-08 12:35:03'),
(25, 6, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-02-28', '2025-02-28', 'Valid', '2026-02-08 12:35:03'),
(26, 6, 'Professional License (Engineering)', 'documents/engineering_license.pdf', '2022-09-10', '2025-09-10', 'Valid', '2026-02-08 12:35:03'),
(27, 6, 'Board Exam Result', 'documents/eng_board_2022.pdf', '2022-09-01', NULL, 'Valid', '2026-02-08 12:35:03'),
(28, 6, 'IT Certification', 'documents/python_cert.pdf', '2023-11-20', NULL, 'Valid', '2026-02-08 12:35:03'),
(29, 7, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-05-15', '2025-05-15', 'Valid', '2026-02-08 12:35:03'),
(30, 7, 'Professional Librarian License', 'documents/librarian_license.pdf', '2023-03-20', '2026-03-20', 'Valid', '2026-02-08 12:35:03'),
(31, 7, 'Transcript of Records', 'documents/tor_mlis.pdf', '2019-04-10', NULL, 'Valid', '2026-02-08 12:35:03'),
(32, 7, 'Training Certificate', 'documents/library_seminar.pdf', '2024-01-30', NULL, 'Valid', '2026-02-08 12:35:03'),
(33, 8, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-01-10', '2025-01-10', 'Valid', '2026-02-08 12:35:03'),
(34, 8, 'Board Exam Result', 'documents/mba_diploma.pdf', '1995-06-30', NULL, 'Valid', '2026-02-08 12:35:03'),
(35, 8, 'Government ID', 'documents/diploma_phd.pdf', '2000-03-15', NULL, 'Valid', '2026-02-08 12:35:03'),
(36, 8, 'Professional License', 'documents/executive_cert.pdf', '2023-06-01', NULL, 'Valid', '2026-02-08 12:35:03'),
(37, 9, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-03-25', '2025-03-25', 'Valid', '2026-02-08 12:35:03'),
(38, 9, 'Professional License (CPA)', 'documents/cpa_license.pdf', '2012-09-05', '2027-09-05', 'Valid', '2026-02-08 12:35:03'),
(39, 9, 'Board Exam Result', 'documents/cpa_board_2012.pdf', '2012-09-01', NULL, 'Valid', '2026-02-08 12:35:03'),
(40, 9, 'Transcript of Records', 'documents/tor_bsais.pdf', '2012-04-20', NULL, 'Valid', '2026-02-08 12:35:03'),
(41, 10, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-04-20', '2025-04-20', 'Valid', '2026-02-08 12:35:03'),
(42, 10, 'Professional License (LET)', 'documents/let_license.pdf', '2016-08-15', '2029-08-15', 'Valid', '2026-02-08 12:35:03'),
(43, 10, 'Vocational Certificate', 'documents/vocational_license.pdf', '2020-03-10', NULL, 'Valid', '2026-02-08 12:35:03'),
(44, 10, 'Training Certificate', 'documents/vocational_training.pdf', '2023-10-15', NULL, 'Valid', '2026-02-08 12:35:03'),
(45, 11, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-02-14', '2025-02-14', 'Valid', '2026-02-08 12:35:03'),
(46, 11, 'Tourism License', 'documents/tourism_cert.pdf', '2019-06-20', '2025-06-20', 'Valid', '2026-02-08 12:35:03'),
(47, 11, 'Food Handler Certificate', 'documents/food_handler.pdf', '2023-05-01', '2025-05-01', 'Valid', '2026-02-08 12:35:03'),
(48, 11, 'Transcript of Records', 'documents/tor_bshm.pdf', '2013-04-05', NULL, 'Valid', '2026-02-08 12:35:03'),
(49, 12, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-01-25', '2025-01-25', 'Valid', '2026-02-08 12:35:03'),
(50, 12, 'Police Clearance', 'documents/police_clearance.pdf', '2024-01-20', '2025-01-20', 'Valid', '2026-02-08 12:35:03'),
(51, 12, 'Criminology License', 'documents/crim_license.pdf', '2017-09-15', '2027-09-15', 'Valid', '2026-02-08 12:35:03'),
(52, 12, 'Board Exam Result', 'documents/crim_board_2017.pdf', '2017-09-01', NULL, 'Valid', '2026-02-08 12:35:03'),
(53, 13, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-05-05', '2025-05-05', 'Valid', '2026-02-08 12:35:03'),
(54, 13, 'Tourism Certification', 'documents/tourism_cert.pdf', '2022-04-10', '2027-04-10', 'Valid', '2026-02-08 12:35:03'),
(55, 13, 'Travel Agency License', 'documents/travel_agency.pdf', '2023-08-01', '2025-08-01', 'Valid', '2026-02-08 12:35:03'),
(56, 13, 'Training Certificate', 'documents/tourism_training.pdf', '2024-02-20', NULL, 'Valid', '2026-02-08 12:35:03'),
(57, 14, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-03-01', '2025-03-01', 'Valid', '2026-02-08 12:35:03'),
(58, 14, 'IT Certification', 'documents/comptia_cert.pdf', '2022-11-15', '2025-11-15', 'Valid', '2026-02-08 12:35:03'),
(59, 14, 'Project Management', 'documents/pmp_cert.pdf', '2023-06-20', '2026-06-20', 'Valid', '2026-02-08 12:35:03'),
(60, 14, 'Training Certificate', 'documents/tech_cert.pdf', '2024-01-10', NULL, 'Valid', '2026-02-08 12:35:03'),
(61, 15, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-04-10', '2025-04-10', 'Valid', '2026-02-08 12:35:03'),
(62, 15, 'Professional License (Psychology)', 'documents/psych_license.pdf', '2020-04-20', '2026-04-20', 'Valid', '2026-02-08 12:35:03'),
(63, 15, 'Guidance Certificate', 'documents/guidance_cert.pdf', '2021-03-15', NULL, 'Valid', '2026-02-08 12:35:03'),
(64, 15, 'Training Certificate', 'documents/counseling_seminar.pdf', '2023-09-20', NULL, 'Valid', '2026-02-08 12:35:03'),
(65, 16, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-02-08', '2025-02-08', 'Valid', '2026-02-08 12:35:03'),
(66, 16, 'Physical Education License', 'documents/pe_license.pdf', '2021-09-01', '2026-09-01', 'Valid', '2026-02-08 12:35:03'),
(67, 16, 'Sports Certificate', 'documents/sports_coach.pdf', '2022-05-15', NULL, 'Valid', '2026-02-08 12:35:03'),
(68, 16, 'Training Certificate', 'documents/pe_training.pdf', '2023-11-30', NULL, 'Valid', '2026-02-08 12:35:03'),
(69, 17, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-05-20', '2025-05-20', 'Valid', '2026-02-08 12:35:03'),
(70, 17, 'Research Certification', 'documents/research_cert.pdf', '2024-01-15', NULL, 'Valid', '2026-02-08 12:35:03'),
(71, 17, 'IT Certification', 'documents/data_science_cert.pdf', '2023-12-01', '2026-12-01', 'Valid', '2026-02-08 12:35:03'),
(72, 17, 'Training Certificate', 'documents/research_seminar.pdf', '2024-02-28', NULL, 'Valid', '2026-02-08 12:35:03'),
(73, 18, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-01-08', '2025-01-08', 'Valid', '2026-02-08 12:35:03'),
(74, 18, 'Civil Service Eligibility', 'documents/cse_2010.pdf', '2010-10-15', NULL, 'Valid', '2026-02-08 12:35:03'),
(75, 18, 'HR Certification', 'documents/hr_cert.pdf', '2023-04-20', '2026-04-20', 'Valid', '2026-02-08 12:35:03'),
(76, 18, 'Training Certificate', 'documents/hr_seminar.pdf', '2024-03-15', NULL, 'Valid', '2026-02-08 12:35:03'),
(77, 19, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-06-01', '2025-06-01', 'Valid', '2026-02-08 12:35:03'),
(78, 19, 'Professional License (ICT)', 'documents/ict_license.pdf', '2024-02-20', '2027-02-20', 'Valid', '2026-02-08 12:35:03'),
(79, 19, 'IT Certification', 'documents/web_dev_cert.pdf', '2023-08-15', NULL, 'Valid', '2026-02-08 12:35:03'),
(80, 19, 'Training Certificate', 'documents/tech_training.pdf', '2024-01-22', NULL, 'Valid', '2026-02-08 12:35:03'),
(81, 20, 'NBI Clearance', 'documents/nbi_2024.pdf', '2024-02-01', '2025-02-01', 'Valid', '2026-02-08 12:35:03'),
(82, 20, 'Professional License (CPA)', 'documents/cpa_license.pdf', '2008-09-10', '2027-09-10', 'Valid', '2026-02-08 12:35:03'),
(83, 20, 'Board Exam Result', 'documents/cpa_board_2008.pdf', '2008-09-01', NULL, 'Valid', '2026-02-08 12:35:03'),
(84, 20, 'MBA Diploma', 'documents/mba_diploma.pdf', '2012-06-30', NULL, 'Valid', '2026-02-08 12:35:03'),
(85, 1, 'Old Training Certificate', 'documents/old_seminar.pdf', '2022-01-10', '2023-01-10', 'Expired', '2026-02-08 12:35:03'),
(86, 3, 'Expired Certification', 'documents/expired_cert.pdf', '2022-05-15', '2023-05-15', 'Expired', '2026-02-08 12:35:03'),
(87, 5, 'Medical Certificate Expired', 'documents/medical_expired.pdf', '2023-05-01', '2024-05-01', 'Expired', '2026-02-08 12:35:03'),
(88, 7, 'Old NBI Clearance', 'documents/nbi_2023.pdf', '2023-02-15', '2024-02-15', 'Expired', '2026-02-08 12:35:03'),
(89, 9, 'Pending Certification', 'documents/pending_cert.pdf', '2024-06-01', NULL, 'Pending', '2026-02-08 12:35:03'),
(90, 12, 'Pending License Renewal', 'documents/pending_renewal.pdf', '2024-06-15', NULL, 'Pending', '2026-02-08 12:35:03'),
(91, 1, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-01-15', '2025-01-15', 'Valid', '2026-02-08 12:55:28'),
(92, 1, 'Professional_License', 'documents/Professional License (LET).pdf', '2023-06-20', '2026-06-20', 'Valid', '2026-02-08 12:55:28'),
(93, 1, 'Training_Certificate', 'documents/Training_Certificate.pdf', '2023-11-10', NULL, 'Valid', '2026-02-08 12:55:28'),
(94, 1, 'Medical_Certificate', 'documents/NBI Clearance.pdf', '2024-06-01', '2025-06-01', 'Valid', '2026-02-08 12:55:28'),
(99, 3, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-03-10', '2025-03-10', 'Valid', '2026-02-08 12:55:28'),
(100, 3, 'Professional_License', 'documents/IT Certification.pdf', '2023-07-01', '2026-07-01', 'Valid', '2026-02-08 12:55:28'),
(101, 3, 'Training_Certificate', 'documents/Training_Certificate.pdf', '2024-01-25', NULL, 'Valid', '2026-02-08 12:55:28'),
(102, 3, 'IT_Certification', 'documents/IT Certification.pdf', '2023-05-15', '2026-05-15', 'Valid', '2026-02-08 12:55:28'),
(103, 4, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-01-05', '2025-01-05', 'Valid', '2026-02-08 12:55:28'),
(104, 4, 'Professional_License', 'documents/Professional License (LET).pdf', '1998-04-20', '2025-04-20', 'Valid', '2026-02-08 12:55:28'),
(105, 4, 'Board_Exam_Result', 'documents/Training_Certificate.pdf', '1998-04-15', NULL, 'Valid', '2026-02-08 12:55:28'),
(106, 4, 'Diploma', 'documents/Training_Certificate.pdf', '2005-06-30', NULL, 'Valid', '2026-02-08 12:55:28'),
(107, 5, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-04-01', '2025-04-01', 'Valid', '2026-02-08 12:55:28'),
(108, 5, 'Professional_License', 'documents/Professional License (LET).pdf', '2014-08-20', '2027-08-20', 'Valid', '2026-02-08 12:55:28'),
(109, 5, 'Transcript_of_Records', 'documents/Training_Certificate.pdf', '2014-03-15', NULL, 'Valid', '2026-02-08 12:55:28'),
(110, 5, 'Training_Certificate', 'documents/Training_Certificate.pdf', '2023-12-05', NULL, 'Valid', '2026-02-08 12:55:28'),
(111, 6, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-02-28', '2025-02-28', 'Valid', '2026-02-08 12:55:28'),
(112, 6, 'Professional_License', 'documents/Professional License (LET).pdf', '2022-09-10', '2025-09-10', 'Valid', '2026-02-08 12:55:28'),
(113, 6, 'Board_Exam_Result', 'documents/Training_Certificate.pdf', '2022-09-01', NULL, 'Valid', '2026-02-08 12:55:28'),
(114, 6, 'IT_Certification', 'documents/IT Certification.pdf', '2023-11-20', NULL, 'Valid', '2026-02-08 12:55:28'),
(115, 7, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-05-15', '2025-05-15', 'Valid', '2026-02-08 12:55:28'),
(116, 7, 'Professional_License', 'documents/Professional License (LET).pdf', '2023-03-20', '2026-03-20', 'Valid', '2026-02-08 12:55:28'),
(117, 7, 'Transcript_of_Records', 'documents/Training_Certificate.pdf', '2019-04-10', NULL, 'Valid', '2026-02-08 12:55:28'),
(118, 7, 'Training_Certificate', 'documents/Training_Certificate.pdf', '2024-01-30', NULL, 'Valid', '2026-02-08 12:55:28'),
(119, 8, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-01-10', '2025-01-10', 'Valid', '2026-02-08 12:55:28'),
(120, 8, 'Board_Exam_Result', 'documents/Training_Certificate.pdf', '1995-06-30', NULL, 'Valid', '2026-02-08 12:55:28'),
(121, 8, 'Diploma', 'documents/Training_Certificate.pdf', '2000-03-15', NULL, 'Valid', '2026-02-08 12:55:28'),
(122, 8, 'Other', 'documents/Training_Certificate.pdf', '2023-06-01', NULL, 'Valid', '2026-02-08 12:55:28'),
(123, 9, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-03-25', '2025-03-25', 'Valid', '2026-02-08 12:55:28'),
(124, 9, 'Professional_License', 'documents/Professional License (LET).pdf', '2012-09-05', '2027-09-05', 'Valid', '2026-02-08 12:55:28'),
(125, 9, 'Board_Exam_Result', 'documents/Training_Certificate.pdf', '2012-09-01', NULL, 'Valid', '2026-02-08 12:55:28'),
(126, 9, 'Transcript_of_Records', 'documents/Training_Certificate.pdf', '2012-04-20', NULL, 'Valid', '2026-02-08 12:55:28'),
(127, 10, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-04-20', '2025-04-20', 'Valid', '2026-02-08 12:55:28'),
(128, 10, 'Professional_License', 'documents/Professional License (LET).pdf', '2016-08-15', '2029-08-15', 'Valid', '2026-02-08 12:55:28'),
(129, 10, 'Other', 'documents/Physical Education License.pdf', '2020-03-10', NULL, 'Valid', '2026-02-08 12:55:28'),
(130, 10, 'Training_Certificate', 'documents/Training_Certificate.pdf', '2023-10-15', NULL, 'Valid', '2026-02-08 12:55:28'),
(131, 11, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-02-14', '2025-02-14', 'Valid', '2026-02-08 12:55:28'),
(132, 11, 'Other', 'documents/Training_Certificate.pdf', '2019-06-20', '2025-06-20', 'Valid', '2026-02-08 12:55:28'),
(133, 11, 'Medical_Certificate', 'documents/Food Handler Certificate.pdf', '2023-05-01', '2025-05-01', 'Valid', '2026-02-08 12:55:28'),
(134, 11, 'Transcript_of_Records', 'documents/Training_Certificate.pdf', '2013-04-05', NULL, 'Valid', '2026-02-08 12:55:28'),
(135, 12, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-01-25', '2025-01-25', 'Valid', '2026-02-08 12:55:28'),
(136, 12, 'Police_Clearance', 'documents/NBI Clearance.pdf', '2024-01-20', '2025-01-20', 'Valid', '2026-02-08 12:55:28'),
(137, 12, 'Professional_License', 'documents/Professional License (LET).pdf', '2017-09-15', '2027-09-15', 'Valid', '2026-02-08 12:55:28'),
(138, 12, 'Board_Exam_Result', 'documents/Training_Certificate.pdf', '2017-09-01', NULL, 'Valid', '2026-02-08 12:55:28'),
(139, 13, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-05-05', '2025-05-05', 'Valid', '2026-02-08 12:55:28'),
(140, 13, 'Other', 'documents/Training_Certificate.pdf', '2022-04-10', '2027-04-10', 'Valid', '2026-02-08 12:55:28'),
(141, 13, 'Other', 'documents/Training_Certificate.pdf', '2023-08-01', '2025-08-01', 'Valid', '2026-02-08 12:55:28'),
(142, 13, 'Training_Certificate', 'documents/Training_Certificate.pdf', '2024-02-20', NULL, 'Valid', '2026-02-08 12:55:28'),
(143, 14, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-03-01', '2025-03-01', 'Valid', '2026-02-08 12:55:28'),
(144, 14, 'IT_Certification', 'documents/IT Certification.pdf', '2022-11-15', '2025-11-15', 'Valid', '2026-02-08 12:55:28'),
(145, 14, 'Other', 'documents/Training_Certificate.pdf', '2023-06-20', '2026-06-20', 'Valid', '2026-02-08 12:55:28'),
(146, 14, 'Training_Certificate', 'documents/Training_Certificate.pdf', '2024-01-10', NULL, 'Valid', '2026-02-08 12:55:28'),
(147, 15, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-04-10', '2025-04-10', 'Valid', '2026-02-08 12:55:28'),
(148, 15, 'Professional_License', 'documents/Professional License (LET).pdf', '2020-04-20', '2026-04-20', 'Valid', '2026-02-08 12:55:28'),
(149, 15, 'Other', 'documents/Guidance Certificate.pdf', '2021-03-15', NULL, 'Valid', '2026-02-08 12:55:28'),
(150, 15, 'Training_Certificate', 'documents/Training_Certificate.pdf', '2023-09-20', NULL, 'Valid', '2026-02-08 12:55:28'),
(151, 16, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-02-08', '2025-02-08', 'Valid', '2026-02-08 12:55:28'),
(152, 16, 'Professional_License', 'documents/Physical Education License.pdf', '2021-09-01', '2026-09-01', 'Valid', '2026-02-08 12:55:28'),
(153, 16, 'Other', 'documents/Training_Certificate.pdf', '2022-05-15', NULL, 'Valid', '2026-02-08 12:55:28'),
(154, 16, 'Training_Certificate', 'documents/Training_Certificate.pdf', '2023-11-30', NULL, 'Valid', '2026-02-08 12:55:28'),
(155, 17, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-05-20', '2025-05-20', 'Valid', '2026-02-08 12:55:28'),
(156, 17, 'Other', 'documents/Training_Certificate.pdf', '2024-01-15', NULL, 'Valid', '2026-02-08 12:55:28'),
(157, 17, 'IT_Certification', 'documents/IT Certification.pdf', '2023-12-01', '2026-12-01', 'Valid', '2026-02-08 12:55:28'),
(158, 17, 'Training_Certificate', 'documents/Training_Certificate.pdf', '2024-02-28', NULL, 'Valid', '2026-02-08 12:55:28'),
(159, 18, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-01-08', '2025-01-08', 'Valid', '2026-02-08 12:55:28'),
(160, 18, 'Civil_Service_Eligibility', 'documents/Training_Certificate.pdf', '2010-10-15', NULL, 'Valid', '2026-02-08 12:55:28'),
(161, 18, 'Other', 'documents/Training_Certificate.pdf', '2023-04-20', '2026-04-20', 'Valid', '2026-02-08 12:55:28'),
(162, 18, 'Training_Certificate', 'documents/Training_Certificate.pdf', '2024-03-15', NULL, 'Valid', '2026-02-08 12:55:28'),
(163, 19, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-06-01', '2025-06-01', 'Valid', '2026-02-08 12:55:28'),
(164, 19, 'Professional_License', 'documents/IT Certification.pdf', '2024-02-20', '2027-02-20', 'Valid', '2026-02-08 12:55:28'),
(165, 19, 'IT_Certification', 'documents/IT Certification.pdf', '2023-08-15', NULL, 'Valid', '2026-02-08 12:55:28'),
(166, 19, 'Training_Certificate', 'documents/Training_Certificate.pdf', '2024-01-22', NULL, 'Valid', '2026-02-08 12:55:28'),
(167, 20, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2024-02-01', '2025-02-01', 'Valid', '2026-02-08 12:55:28'),
(168, 20, 'Professional_License', 'documents/Professional License (LET).pdf', '2008-09-10', '2027-09-10', 'Valid', '2026-02-08 12:55:28'),
(169, 20, 'Board_Exam_Result', 'documents/Training_Certificate.pdf', '2008-09-01', NULL, 'Valid', '2026-02-08 12:55:28'),
(170, 20, 'Diploma', 'documents/Training_Certificate.pdf', '2012-06-30', NULL, 'Valid', '2026-02-08 12:55:28'),
(171, 1, 'Training_Certificate', 'documents/Old Training Certificate.pdf', '2022-01-10', '2023-01-10', 'Expired', '2026-02-08 12:55:28'),
(172, 3, 'IT_Certification', 'documents/IT Certification.pdf', '2022-05-15', '2023-05-15', 'Expired', '2026-02-08 12:55:28'),
(173, 5, 'Medical_Certificate', 'documents/NBI Clearance.pdf', '2023-05-01', '2024-05-01', 'Expired', '2026-02-08 12:55:28'),
(174, 7, 'NBI_Clearance', 'documents/NBI Clearance.pdf', '2023-02-15', '2024-02-15', 'Expired', '2026-02-08 12:55:28'),
(175, 9, 'IT_Certification', 'documents/IT Certification.pdf', '2024-06-01', NULL, 'Pending', '2026-02-08 12:55:28'),
(176, 12, 'Professional_License', 'documents/Professional License (LET).pdf', '2024-06-15', NULL, 'Pending', '2026-02-08 12:55:28');

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `institution_name` varchar(200) DEFAULT NULL,
  `degree` varchar(100) DEFAULT NULL,
  `field_of_study` varchar(100) DEFAULT NULL,
  `year_started` year(4) DEFAULT NULL,
  `year_completed` year(4) DEFAULT NULL,
  `honors` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`id`, `employee_id`, `institution_name`, `degree`, `field_of_study`, `year_started`, `year_completed`, `honors`, `created_at`) VALUES
(5, 1, 'University of the Philippines', 'Bachelor of Science in Business Administration', 'Business Management', '2003', '2007', 'Cum Laude', '2026-02-08 12:08:07'),
(6, 1, 'Ateneo de Manila University', 'Master of Business Administration', 'Business Administration', '2008', '2010', 'Dean\'s List', '2026-02-08 12:08:07'),
(9, 3, 'University of Santo Tomas', 'Bachelor of Science in Information Technology', 'Computer Science', '2008', '2012', NULL, '2026-02-08 12:08:07'),
(10, 3, 'De La Salle University', 'Master of Science in Computer Science', 'Data Science', '2013', '2015', NULL, '2026-02-08 12:08:07'),
(11, 4, 'Harvard University', 'Bachelor of Arts', 'Economics', '1993', '1997', 'Magna Cum Laude', '2026-02-08 12:08:07'),
(12, 4, 'Stanford University', 'Master of Business Administration', 'Business', '1998', '2000', NULL, '2026-02-08 12:08:07'),
(13, 4, 'MIT Sloan', 'Doctor of Philosophy', 'Management', '2001', '2005', NULL, '2026-02-08 12:08:07'),
(14, 5, 'Miriam College', 'Bachelor of Science in Elementary Education', 'Primary Education', '2010', '2014', 'Summa Cum Laude', '2026-02-08 12:08:07'),
(15, 5, 'University of the Philippines', 'Master of Education', 'Curriculum Development', '2015', '2017', NULL, '2026-02-08 12:08:07'),
(16, 6, 'University of the Philippines', 'Bachelor of Science in Computer Engineering', 'Computer Engineering', '2006', '2010', NULL, '2026-02-08 12:08:07'),
(17, 6, 'Ateneo de Manila University', 'Master of Engineering', 'Computer Engineering', '2011', '2013', NULL, '2026-02-08 12:08:07'),
(18, 7, 'University of the Philippines', 'Bachelor of Library and Information Science', 'Library Science', '2013', '2017', NULL, '2026-02-08 12:08:07'),
(19, 7, 'Philippine Normal University', 'Master of Library Science', 'Information Science', '2018', '2020', NULL, '2026-02-08 12:08:07'),
(20, 8, 'Yale University', 'Bachelor of Arts', 'Political Science', '1988', '1992', NULL, '2026-02-08 12:08:07'),
(21, 8, 'Columbia University', 'Master of Education', 'Higher Education Administration', '1993', '1995', NULL, '2026-02-08 12:08:07'),
(22, 8, 'Harvard University', 'Doctor of Education', 'Educational Leadership', '1996', '2000', NULL, '2026-02-08 12:08:07'),
(23, 9, 'Ateneo de Manila University', 'Bachelor of Science in Asian Studies', 'International Relations', '2004', '2008', NULL, '2026-02-08 12:08:07'),
(24, 9, 'University of Asia Pacific', 'Master of Arts', 'International Affairs', '2009', '2011', NULL, '2026-02-08 12:08:07'),
(25, 10, 'Technological University of the Philippines', 'Bachelor of Technical Teacher Education', 'Automotive Technology', '2009', '2013', NULL, '2026-02-08 12:08:07'),
(26, 10, 'University of the Philippines', 'Master of Vocational Education', 'Technical Education', '2014', '2016', NULL, '2026-02-08 12:08:07'),
(27, 11, 'De La Salle University', 'Bachelor of Science in Hospitality Management', 'Hotel and Restaurant Management', '2005', '2009', NULL, '2026-02-08 12:08:08'),
(28, 11, 'Asian Institute of Management', 'Master of Business Administration', 'Hospitality Management', '2010', '2012', NULL, '2026-02-08 12:08:08'),
(29, 12, 'University of the Philippines', 'Bachelor of Arts in Criminology', 'Criminology', '2001', '2005', NULL, '2026-02-08 12:08:08'),
(30, 12, 'Philippine National Police Academy', 'Master of Criminal Justice', 'Criminal Justice Administration', '2006', '2008', NULL, '2026-02-08 12:08:08'),
(31, 13, 'De La Salle University', 'Bachelor of Science in Tourism Management', 'Tourism Management', '2012', '2016', NULL, '2026-02-08 12:08:08'),
(32, 13, 'University of Santo Tomas', 'Master of Arts in Tourism', 'Tourism and Hospitality', '2017', '2019', NULL, '2026-02-08 12:08:08'),
(33, 14, 'University of Santo Tomas', 'Bachelor of Science in Information Systems', 'Information Systems', '2007', '2011', NULL, '2026-02-08 12:08:08'),
(34, 14, 'Asian Institute of Management', 'Master of Science in Information Technology', 'IT Management', '2012', '2014', NULL, '2026-02-08 12:08:08'),
(35, 15, 'Miriam College', 'Bachelor of Science in Psychology', 'Psychology', '2011', '2015', NULL, '2026-02-08 12:08:08'),
(36, 15, 'University of the Philippines', 'Master of Arts in Psychology', 'Counseling Psychology', '2016', '2018', NULL, '2026-02-08 12:08:08'),
(37, 16, 'Philippine Normal University', 'Bachelor of Physical Education', 'Physical Education', '2008', '2012', NULL, '2026-02-08 12:08:08'),
(38, 16, 'University of the Philippines', 'Master of Physical Education', 'Sports Management', '2013', '2015', NULL, '2026-02-08 12:08:08'),
(39, 17, 'Ateneo de Manila University', 'Bachelor of Science in Management Engineering', 'Engineering Management', '2014', '2018', NULL, '2026-02-08 12:08:08'),
(40, 17, 'University of the Philippines', 'Master of Science in Research and Development', 'Research Management', '2019', '2021', NULL, '2026-02-08 12:08:08'),
(41, 18, 'De La Salle University', 'Bachelor of Science in Psychology', 'Industrial Psychology', '2002', '2006', NULL, '2026-02-08 12:08:08'),
(42, 18, 'Asian Institute of Management', 'Master of Business Administration', 'Human Resource Management', '2007', '2009', NULL, '2026-02-08 12:08:08'),
(43, 19, 'University of Santo Tomas', 'Bachelor of Science in Information Technology', 'Web Development', '2015', '2019', NULL, '2026-02-08 12:08:08'),
(44, 19, 'De La Salle University', 'Master of Information Technology', 'Software Engineering', '2020', '2022', NULL, '2026-02-08 12:08:08'),
(45, 20, 'Harvard Business School', 'Bachelor of Science', 'Business Administration', '2000', '2004', 'Summa Cum Laude', '2026-02-08 12:08:08'),
(46, 20, 'INSEAD', 'Master of Business Administration', 'International Business', '2005', '2007', NULL, '2026-02-08 12:08:08');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('Active','Inactive','On Leave','Terminated') DEFAULT 'Active',
  `category_id` int(11) DEFAULT 1,
  `position_id` int(11) DEFAULT 1,
  `hire_date` date DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `marital_status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_id`, `first_name`, `last_name`, `email`, `status`, `category_id`, `position_id`, `hire_date`, `department`, `position`, `gender`, `marital_status`) VALUES
(1, 'BCP-1001', 'Niki', 'Zepanya', 'nikizepanyablackburner@gmail.com', 'Active', 1, 1, '2026-03-03', 'Human Resources', 'HR Manager', 'Female', NULL),
(2, 'BCP-1002', 'Ana', 'Cruz', 'ana.cruz@school.edu', 'Active', 1, 1, '2020-01-15', 'Human Resources', 'HR Manager', 'Female', NULL),
(3, 'BCP-1003', 'Mark', 'Lee', 'mark.lee@school.edu', 'Active', 1, 2, '2019-03-20', 'Information Technology', 'IT Specialist', 'Male', NULL),
(4, 'BCP-1004', 'John', 'Rey', 'john.rey@school.edu', 'Active', 2, 3, '2018-06-01', 'Finance', 'Accountant', 'Male', NULL),
(5, 'BCP-2001', 'Niki', 'Zepanya', 'niki.hr@school.edu', 'Active', 2, 1, '2022-01-10', 'Human Resources', 'HR Manager', 'Female', NULL),
(6, 'BCP-2002', 'Ana', 'Cruz', 'ana.hr@school.edu', 'Active', 2, 2, '2023-02-15', 'Human Resources', 'HR Staff', 'Female', NULL),
(7, 'BCP-2003', 'Patricia', 'Go', 'patricia.admin@school.edu', 'Active', 2, 3, '2021-05-20', 'Administration', 'Admin Officer', 'Female', NULL),
(8, 'BCP-2004', 'Lance', 'Tan', 'lance.admin@school.edu', 'Active', 2, 4, '2022-07-11', 'Administration', 'Executive Assistant', 'Male', NULL),
(9, 'BCP-2005', 'Maria', 'Santos', 'maria.acad@school.edu', 'Active', 1, 5, '2020-06-01', 'Academic Affairs', 'Instructor', 'Female', NULL),
(10, 'BCP-2006', 'Jose', 'Garcia', 'jose.acad@school.edu', 'Active', 1, 6, '2018-03-15', 'Academic Affairs', 'Professor', 'Male', NULL),
(11, 'BCP-2007', 'Mark', 'Lee', 'mark.it@school.edu', 'Active', 2, 7, '2019-04-10', 'Information Technology', 'IT Specialist', 'Male', NULL),
(12, 'BCP-2008', 'Sophia', 'Tan', 'sophia.it@school.edu', 'Active', 2, 8, '2021-08-22', 'Information Technology', 'System Administrator', 'Female', NULL),
(13, 'BCP-2009', 'John', 'Rey', 'john.fin@school.edu', 'Active', 2, 9, '2017-09-01', 'Finance', 'Accountant', 'Male', NULL),
(14, 'BCP-2010', 'Brian', 'Flores', 'brian.fin@school.edu', 'Active', 2, 10, '2022-11-30', 'Finance', 'Cashier', 'Male', NULL),
(15, 'BCP-2011', 'Angela', 'Torres', 'angela.reg@school.edu', 'Active', 2, 11, '2019-01-25', 'Registrar', 'Registrar Officer', 'Female', NULL),
(16, 'BCP-2012', 'Diane', 'Castro', 'diane.reg@school.edu', 'Active', 2, 12, '2020-03-18', 'Registrar', 'Registrar Assistant', 'Female', NULL),
(17, 'BCP-2013', 'Pedro', 'Lopez', 'pedro.maint@school.edu', 'Active', 2, 13, '2016-10-10', 'Maintenance', 'Janitor', 'Male', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_adjustments`
--

CREATE TABLE `employee_adjustments` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `payroll_period_id` int(11) NOT NULL,
  `type` enum('allowance','deduction') NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_allowances`
--

CREATE TABLE `employee_allowances` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `allowance_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_allowances`
--

INSERT INTO `employee_allowances` (`id`, `employee_id`, `allowance_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 2),
(4, 3, 3),
(5, 4, 3);

-- --------------------------------------------------------

--
-- Table structure for table `employee_categories`
--

CREATE TABLE `employee_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_categories`
--

INSERT INTO `employee_categories` (`id`, `name`) VALUES
(1, 'Teaching'),
(2, 'Non-Teaching'),
(3, 'Business and Administration'),
(4, 'Executive');

-- --------------------------------------------------------

--
-- Table structure for table `employee_deductions`
--

CREATE TABLE `employee_deductions` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `deduction_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_deductions`
--

INSERT INTO `employee_deductions` (`id`, `employee_id`, `deduction_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 2, 4),
(6, 3, 1),
(7, 3, 2),
(8, 3, 3),
(9, 3, 4),
(10, 4, 4),
(11, 5, 1),
(12, 5, 2),
(13, 5, 3);

-- --------------------------------------------------------

--
-- Table structure for table `employee_leave_eligibility`
--

CREATE TABLE `employee_leave_eligibility` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type` varchar(20) NOT NULL,
  `application_date` date NOT NULL,
  `criteria_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`criteria_data`)),
  `documents_uploaded` tinyint(1) DEFAULT 0,
  `eligibility_status` varchar(20) DEFAULT 'pending',
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_salary`
--

CREATE TABLE `employee_salary` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `salary_structure_id` int(11) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `effective_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_salary`
--

INSERT INTO `employee_salary` (`id`, `employee_id`, `salary_structure_id`, `rate`, `effective_date`) VALUES
(1, 1, 1, NULL, '2023-01-01'),
(2, 2, NULL, 500.00, '2023-01-01'),
(3, 3, 3, NULL, '2023-01-01'),
(4, 4, 4, NULL, '2023-01-01'),
(5, 5, 5, NULL, '2023-01-01'),
(6, 6, NULL, 550.00, '2024-08-01');

-- --------------------------------------------------------

--
-- Table structure for table `employee_shifts`
--

CREATE TABLE `employee_shifts` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employment_details`
--

CREATE TABLE `employment_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `position_title` varchar(100) DEFAULT NULL,
  `employment_type` enum('Regular','Contractual','Probationary','Part-Time','Intern') DEFAULT 'Regular',
  `employment_status` enum('Active','Suspended','Resigned','Terminated') DEFAULT 'Active',
  `job_level` varchar(50) DEFAULT NULL,
  `salary_rate` decimal(15,2) DEFAULT 0.00,
  `salary_type` enum('Monthly','Daily','Hourly') DEFAULT 'Monthly',
  `work_location` varchar(100) DEFAULT NULL,
  `supervisor_id` int(10) UNSIGNED DEFAULT NULL,
  `contract_start_date` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `regularization_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employment_details`
--

INSERT INTO `employment_details` (`id`, `employee_id`, `position_title`, `employment_type`, `employment_status`, `job_level`, `salary_rate`, `salary_type`, `work_location`, `supervisor_id`, `contract_start_date`, `contract_end_date`, `regularization_date`, `created_at`, `updated_at`) VALUES
(16, 1, 'Senior Human Resources Manager', 'Regular', 'Active', 'Manager', 145000.00, 'Monthly', 'Main Office', NULL, '2018-03-15', NULL, '2019-03-15', '2026-02-08 11:39:09', '2026-02-08 11:39:09'),
(18, 3, 'Junior Web Developer', 'Probationary', 'Active', 'Junior', 30000.00, 'Monthly', 'Remote', 2, '2024-09-01', '2025-03-01', NULL, '2026-02-08 11:39:09', '2026-02-08 11:39:09'),
(19, 4, 'IT Security Specialist', 'Contractual', 'Active', 'Mid-Level', 65000.00, 'Monthly', 'Main Office', 1, '2024-01-01', '2025-12-31', NULL, '2026-02-08 11:39:09', '2026-02-08 11:39:09'),
(20, 5, 'Facility Maintenance Technician', 'Part-Time', 'Active', 'Entry', 1200.00, 'Daily', 'Branch Office', NULL, '2023-08-15', NULL, NULL, '2026-02-08 11:39:09', '2026-02-08 11:39:09'),
(21, 6, 'Web Development Intern', 'Intern', 'Active', 'Entry', 200.00, 'Hourly', 'Remote', 3, '2025-01-15', '2025-06-30', NULL, '2026-02-08 11:39:09', '2026-02-08 11:39:09'),
(22, 7, 'Senior Finance Manager', 'Regular', 'Active', 'Manager', 135000.00, 'Monthly', 'Main Office', NULL, '2019-07-01', NULL, '2020-07-01', '2026-02-08 11:39:09', '2026-02-08 11:39:09'),
(23, 8, 'Construction Worker', 'Contractual', 'Active', 'Entry', 1000.00, 'Daily', 'On-Site', 7, '2024-03-01', '2024-12-31', NULL, '2026-02-08 11:39:09', '2026-02-08 11:39:09'),
(24, 9, 'Human Resources Officer', 'Regular', 'Suspended', 'Mid-Level', 55000.00, 'Monthly', 'Main Office', 1, '2021-02-01', NULL, '2022-02-01', '2026-02-08 11:39:09', '2026-02-08 11:39:09'),
(25, 10, 'Marketing Specialist', 'Regular', 'Resigned', 'Mid-Level', 48000.00, 'Monthly', 'Remote', 1, '2020-04-01', '2024-08-31', '2021-04-01', '2026-02-08 11:39:09', '2026-02-08 11:39:09'),
(26, 11, 'Senior Graphic Designer', 'Regular', 'Active', 'Senior', 75000.00, 'Monthly', 'Main Office', NULL, '2019-11-01', NULL, '2020-11-01', '2026-02-08 11:39:09', '2026-02-08 11:39:09'),
(27, 12, 'Data Analyst', 'Probationary', 'Active', 'Junior', 35000.00, 'Monthly', 'Remote', 2, '2024-10-15', '2025-04-15', NULL, '2026-02-08 11:39:09', '2026-02-08 11:39:09'),
(28, 13, 'Operations Manager', 'Regular', 'Active', 'Manager', 125000.00, 'Monthly', 'Main Office', NULL, '2017-05-01', NULL, '2018-05-01', '2026-02-08 11:39:09', '2026-02-08 11:39:09'),
(29, 14, 'Customer Service Representative', 'Part-Time', 'Active', 'Entry', 850.00, 'Daily', 'Branch Office', 13, '2023-05-20', NULL, NULL, '2026-02-08 11:39:09', '2026-02-08 11:39:09'),
(30, 15, 'Network Administrator', 'Contractual', 'Active', 'Senior', 95000.00, 'Monthly', 'Main Office', 1, '2023-01-01', '2026-01-01', NULL, '2026-02-08 11:39:09', '2026-02-08 11:39:09'),
(31, 16, 'Maintenance Technician', 'Part-Time', 'Active', 'Entry', 180.00, 'Hourly', 'On-Site', 13, '2024-02-01', NULL, NULL, '2026-02-08 11:39:09', '2026-02-08 11:39:09'),
(32, 17, 'Senior Accountant', 'Regular', 'Active', 'Senior', 88000.00, 'Monthly', 'Main Office', 7, '2020-09-01', NULL, '2021-09-01', '2026-02-08 11:39:09', '2026-02-08 11:39:09'),
(33, 18, 'Junior Graphic Designer', 'Probationary', 'Active', 'Junior', 28000.00, 'Monthly', 'Remote', 11, '2024-11-01', '2025-05-01', NULL, '2026-02-08 11:39:09', '2026-02-08 11:39:09'),
(34, 19, 'Sales Representative', 'Regular', 'Terminated', 'Junior', 32000.00, 'Monthly', 'Branch Office', 13, '2022-01-15', '2024-06-30', '2023-01-15', '2026-02-08 11:39:09', '2026-02-08 11:39:09'),
(35, 20, 'Full Stack Developer', 'Regular', 'Active', 'Senior', 92000.00, 'Monthly', 'Remote', 2, '2021-03-01', NULL, '2022-03-01', '2026-02-08 11:39:09', '2026-02-08 11:39:09');

-- --------------------------------------------------------

--
-- Table structure for table `employment_history`
--

CREATE TABLE `employment_history` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `reason_for_change` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employment_types`
--

CREATE TABLE `employment_types` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employment_types`
--

INSERT INTO `employment_types` (`id`, `name`) VALUES
(1, 'Regular'),
(2, 'Contractual'),
(3, 'Part-Time');

-- --------------------------------------------------------

--
-- Table structure for table `government_contributions`
--

CREATE TABLE `government_contributions` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `contribution_type` varchar(20) NOT NULL COMMENT 'sss, philhealth, pagibig',
  `ee_number` varchar(50) DEFAULT NULL,
  `er_number` varchar(50) DEFAULT NULL,
  `monthly_contribution` decimal(10,2) DEFAULT 0.00,
  `status` enum('active','suspended','terminated') DEFAULT 'active',
  `effective_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `id` int(11) NOT NULL,
  `holiday_name` varchar(100) DEFAULT NULL,
  `holiday_date` date DEFAULT NULL,
  `type` enum('regular','special') DEFAULT 'regular'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `incidents`
--

CREATE TABLE `incidents` (
  `id` int(11) NOT NULL,
  `incident_id` varchar(20) DEFAULT NULL,
  `reporter_id` int(11) DEFAULT NULL COMMENT 'NULL for anonymous',
  `incident_type` varchar(50) NOT NULL COMMENT 'harassment, bullying, misconduct, safety_violation, discrimination',
  `severity` enum('low','medium','high','critical') DEFAULT 'medium',
  `category` varchar(100) DEFAULT NULL,
  `violation_type` enum('minor','major') DEFAULT 'minor',
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `incident_date` date NOT NULL,
  `incident_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `complainant_name` varchar(100) DEFAULT NULL,
  `respondent_name` varchar(100) DEFAULT NULL,
  `witnesses` text DEFAULT NULL,
  `reported_by` varchar(50) DEFAULT 'Employee',
  `is_anonymous` tinyint(1) DEFAULT 0,
  `is_confidential` tinyint(1) DEFAULT 0,
  `status` enum('open','under_review','in_progress','pending_approval','resolved','rejected','escalated','closed_no_violation') DEFAULT 'open',
  `assigned_to` int(11) DEFAULT NULL,
  `assigned_hr_id` int(11) DEFAULT NULL,
  `resolution_notes` text DEFAULT NULL,
  `decision` varchar(100) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `request_info_notes` text DEFAULT NULL,
  `sla_deadline` timestamp NULL DEFAULT NULL,
  `escalation_level` int(1) DEFAULT 0,
  `repeat_offender` tinyint(1) DEFAULT 0,
  `violation_count` int(11) DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `previous_status` varchar(20) DEFAULT NULL,
  `status_changed_at` timestamp NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incidents`
--

INSERT INTO `incidents` (`id`, `incident_id`, `reporter_id`, `incident_type`, `severity`, `category`, `violation_type`, `title`, `description`, `incident_date`, `incident_time`, `location`, `complainant_name`, `respondent_name`, `witnesses`, `reported_by`, `is_anonymous`, `is_confidential`, `status`, `assigned_to`, `assigned_hr_id`, `resolution_notes`, `decision`, `approved_by`, `approved_at`, `rejection_reason`, `request_info_notes`, `sla_deadline`, `escalation_level`, `repeat_offender`, `violation_count`, `remarks`, `previous_status`, `status_changed_at`, `resolved_at`, `created_at`, `updated_at`) VALUES
(60, 'INC-2026-0001', NULL, 'misconduct', 'medium', NULL, 'minor', 'insubordunation', 'asa', '2026-03-23', '10:00:00', 'Conference Room', 'Ana Cruz', 'Patricia Go', 'Mark Lee', 'Compliance', 0, 1, 'resolved', NULL, 0, '', 'verbal_warning', 999, '2026-03-23 16:36:38', NULL, NULL, NULL, 0, 0, 0, NULL, 'open', '2026-03-23 16:36:31', '2026-03-23 16:36:38', '2026-03-23 16:35:55', '2026-03-23 16:36:38'),
(61, 'INC-2026-001', 1, 'attendance', 'low', 'Tardiness', 'minor', 'Repeated Late Attendance', 'Employee has been late 5 times this month.', '2026-03-20', '08:45:00', 'Other', 'Juan Dela Cruz', 'Pedro Santos', 'Maria Lopez', 'Employee', 0, 0, 'resolved', 2, 0, '', 'verbal_warning', 999, '2026-03-23 16:55:40', NULL, NULL, '2026-03-26 16:41:29', 0, 0, 1, 'First warning issued', 'under_review', '2026-03-23 16:54:36', '2026-03-23 16:55:40', '2026-03-23 16:41:29', '2026-03-23 16:55:40'),
(62, 'INC-2026-002', 2, 'misconduct', 'medium', 'Workplace Conflict', 'major', 'Verbal Argument Between Employees', 'Two employees engaged in a heated argument during work hours.', '2026-03-18', '14:30:00', 'Human Resources', 'Ana Reyes', 'Mark Cruz', 'John Lim', 'Employee', 0, 1, 'under_review', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-28 16:41:29', 1, 0, 0, 'Needs mediation', 'open', '2026-03-23 16:41:29', NULL, '2026-03-23 16:41:29', '2026-03-23 16:51:23'),
(63, 'INC-2026-003', 3, 'safety', 'high', 'Data Privacy', 'major', 'Unauthorized Data Access', 'Employee accessed confidential files without permission.', '2026-03-15', '10:15:00', 'Parking Lot', 'HR Department', 'Kevin Tan', 'System Logs', 'System', 0, 1, 'in_progress', 4, 0, 'Investigation ongoing', NULL, NULL, NULL, NULL, NULL, '2026-03-25 16:41:29', 2, 1, 2, 'Repeat offense suspected', 'under_review', '2026-03-23 16:41:29', NULL, '2026-03-23 16:41:29', '2026-03-23 16:54:17'),
(64, 'INC-2026-004', 4, 'Harassment', 'critical', 'Workplace Harassment', 'major', 'Harassment Complaint Filed', 'Employee reported inappropriate behavior from colleague.', '2026-03-10', '16:00:00', 'Admin Office', 'Lisa Gomez', 'Carlos Mendoza', 'Angela Yu', 'Employee', 0, 1, 'resolved', 2, 3, 'Employee suspended for 2 weeks', 'Approved', 1, '2026-03-23 16:41:29', NULL, NULL, '2026-03-24 16:41:29', 2, 1, 3, 'Case closed successfully', 'in_progress', '2026-03-23 16:41:29', '2026-03-23 16:41:29', '2026-03-23 16:41:29', '2026-03-23 16:42:16');

-- --------------------------------------------------------

--
-- Table structure for table `incident_audit_log`
--

CREATE TABLE `incident_audit_log` (
  `id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `old_status` varchar(20) DEFAULT NULL,
  `new_status` varchar(20) DEFAULT NULL,
  `performed_by` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incident_audit_log`
--

INSERT INTO `incident_audit_log` (`id`, `incident_id`, `action`, `old_status`, `new_status`, `performed_by`, `notes`, `created_at`) VALUES
(7, 31, 'action_executed', NULL, 'request_evidence', 999, '', '2026-03-23 09:39:30'),
(8, 31, 'action_executed', NULL, 'request_evidence', 999, '', '2026-03-23 09:39:43'),
(9, 31, 'action_executed', NULL, 'schedule_interview', 999, '', '2026-03-23 09:39:56'),
(17, 31, 'status_changed', 'open', 'closed_no_violation', 0, '', '2026-03-23 11:10:04'),
(34, 50, 'created', NULL, 'open', 0, 'Incident created', '2026-03-23 16:05:56'),
(42, 58, 'created', NULL, 'open', 0, 'Incident created', '2026-03-23 16:19:12'),
(43, 59, 'created', NULL, 'open', 0, 'Incident created', '2026-03-23 16:19:24'),
(44, 60, 'created', NULL, 'open', 0, 'Incident created', '2026-03-23 16:35:55'),
(45, 60, 'status_changed', 'open', 'under_review', 0, '', '2026-03-23 16:36:31'),
(46, 60, 'decision', NULL, 'resolved', 999, 'Decision: verbal_warning - ', '2026-03-23 16:36:38'),
(47, 61, 'status_changed', 'open', 'under_review', 0, '', '2026-03-23 16:50:29'),
(48, 61, 'status_changed', 'under_review', 'in_progress', 0, '', '2026-03-23 16:54:36'),
(49, 61, 'decision', NULL, 'resolved', 999, 'Decision: verbal_warning - ', '2026-03-23 16:55:40');

-- --------------------------------------------------------

--
-- Table structure for table `incident_evidence`
--

CREATE TABLE `incident_evidence` (
  `id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `incident_memos`
--

CREATE TABLE `incident_memos` (
  `id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `action_type` varchar(100) NOT NULL,
  `action_name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `recipients` text DEFAULT NULL,
  `sent_by` int(11) NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `delivery_status` enum('pending','sent','delivered','failed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incident_memos`
--

INSERT INTO `incident_memos` (`id`, `incident_id`, `action_type`, `action_name`, `content`, `recipients`, `sent_by`, `sent_at`, `delivery_status`) VALUES
(1, 32, 'verbal_warning', 'Verbal Warning Issued', 'INCIDENT NOTIFICATION MEMO\n===========================\n\nDate: March 23, 2026\nTime: 10:36 AM\n\nRE: Incident Notification - INC-2026-0006\n\nDear Involved Party,\n\nThis is to notify you that an action has been taken regarding the incident case referenced above.\n\nACTION TAKEN: Verbal Warning Issued\n\nNOTES:\nsss\n\nINCIDENT DETAILS:\n- Type: Harassment\n- Title: Verbal Harassment Incident\n- Date: March 10, 2026\n- Status: Resolved\n\nIf you have any questions or require further information, please contact the HR Department.\n\nThis is an automated notification. Please do not reply to this message.\n\nSincerely,\nHR Department\nAction initiated by: User ID: 999', 'John Rey, Brian Flores, Array', 999, '2026-03-23 09:36:05', 'sent'),
(2, 32, 'suspend_employee', 'Employee Suspended', 'INCIDENT NOTIFICATION MEMO\n===========================\n\nDate: March 23, 2026\nTime: 10:36 AM\n\nRE: Incident Notification - INC-2026-0006\n\nDear Involved Party,\n\nThis is to notify you that an action has been taken regarding the incident case referenced above.\n\nACTION TAKEN: Employee Suspended\n\nINCIDENT DETAILS:\n- Type: Harassment\n- Title: Verbal Harassment Incident\n- Date: March 10, 2026\n- Status: Resolved\n\nIf you have any questions or require further information, please contact the HR Department.\n\nThis is an automated notification. Please do not reply to this message.\n\nSincerely,\nHR Department\nAction initiated by: User ID: 999', 'John Rey, Brian Flores, Array', 999, '2026-03-23 09:36:36', 'sent'),
(3, 31, 'request_evidence', 'Evidence Requested', 'INCIDENT NOTIFICATION MEMO\n===========================\n\nDate: March 23, 2026\nTime: 10:39 AM\n\nRE: Incident Notification - INC-2026-0005\n\nDear Involved Party,\n\nThis is to notify you that an action has been taken regarding the incident case referenced above.\n\nACTION TAKEN: Evidence Requested\n\nINCIDENT DETAILS:\n- Type: Policy\n- Title: Policy Violation\n- Date: February 15, 2026\n- Status: Open\n\nIf you have any questions or require further information, please contact the HR Department.\n\nThis is an automated notification. Please do not reply to this message.\n\nSincerely,\nHR Department\nAction initiated by: User ID: 999', 'Maria Santos, Pedro Lopez, Array', 999, '2026-03-23 09:39:30', 'sent'),
(4, 31, 'request_evidence', 'Evidence Requested', 'INCIDENT NOTIFICATION MEMO\n===========================\n\nDate: March 23, 2026\nTime: 10:39 AM\n\nRE: Incident Notification - INC-2026-0005\n\nDear Involved Party,\n\nThis is to notify you that an action has been taken regarding the incident case referenced above.\n\nACTION TAKEN: Evidence Requested\n\nINCIDENT DETAILS:\n- Type: Policy\n- Title: Policy Violation\n- Date: February 15, 2026\n- Status: Open\n\nIf you have any questions or require further information, please contact the HR Department.\n\nThis is an automated notification. Please do not reply to this message.\n\nSincerely,\nHR Department\nAction initiated by: User ID: 999', 'Maria Santos, Pedro Lopez, Array', 999, '2026-03-23 09:39:43', 'sent'),
(5, 31, 'schedule_interview', 'Interview Scheduled', 'INCIDENT NOTIFICATION MEMO\n===========================\n\nDate: March 23, 2026\nTime: 10:39 AM\n\nRE: Incident Notification - INC-2026-0005\n\nDear Involved Party,\n\nThis is to notify you that an action has been taken regarding the incident case referenced above.\n\nACTION TAKEN: Interview Scheduled\n\nINCIDENT DETAILS:\n- Type: Policy\n- Title: Policy Violation\n- Date: February 15, 2026\n- Status: Open\n\nIf you have any questions or require further information, please contact the HR Department.\n\nThis is an automated notification. Please do not reply to this message.\n\nSincerely,\nHR Department\nAction initiated by: User ID: 999', 'Maria Santos, Pedro Lopez, Array', 999, '2026-03-23 09:39:56', 'sent'),
(6, 33, 'refer_hr', 'Referred to HR', 'INCIDENT NOTIFICATION MEMO\n===========================\n\nDate: March 23, 2026\nTime: 11:23 AM\n\nRE: Incident Notification - INC-2026-0006\n\nDear Involved Party,\n\nThis is to notify you that an action has been taken regarding the incident case referenced above.\n\nACTION TAKEN: Referred to HR\n\nNOTES:\ndfdfd\n\nINCIDENT DETAILS:\n- Type: Misconduct\n- Title: Insubordination\n- Date: March 23, 2026\n- Status: Under Review\n\nIf you have any questions or require further information, please contact the HR Department.\n\nThis is an automated notification. Please do not reply to this message.\n\nSincerely,\nHR Department\nAction initiated by: User ID: 999', 'Niki Zepanya, Ana Cruz, Array, Compliance', 999, '2026-03-23 10:23:20', 'sent'),
(7, 33, 'request_evidence', 'Evidence Requested', 'INCIDENT NOTIFICATION MEMO\n===========================\n\nDate: March 23, 2026\nTime: 11:24 AM\n\nRE: Incident Notification - INC-2026-0006\n\nDear Involved Party,\n\nThis is to notify you that an action has been taken regarding the incident case referenced above.\n\nACTION TAKEN: Evidence Requested\n\nINCIDENT DETAILS:\n- Type: Misconduct\n- Title: Insubordination\n- Date: March 23, 2026\n- Status: Under Review\n\nIf you have any questions or require further information, please contact the HR Department.\n\nThis is an automated notification. Please do not reply to this message.\n\nSincerely,\nHR Department\nAction initiated by: User ID: 999', 'Niki Zepanya, Ana Cruz, Array, Compliance', 999, '2026-03-23 10:24:13', 'sent'),
(8, 33, 'assign_investigator', 'Investigator Assigned', 'INCIDENT NOTIFICATION MEMO\n===========================\n\nDate: March 23, 2026\nTime: 11:34 AM\n\nRE: Incident Notification - INC-2026-0006\n\nDear Involved Party,\n\nThis is to notify you that an action has been taken regarding the incident case referenced above.\n\nACTION TAKEN: Investigator Assigned\n\nINCIDENT DETAILS:\n- Type: Misconduct\n- Title: Insubordination\n- Date: March 23, 2026\n- Status: Pending Approval\n\nIf you have any questions or require further information, please contact the HR Department.\n\nThis is an automated notification. Please do not reply to this message.\n\nSincerely,\nHR Department\nAction initiated by: User ID: 999', 'Niki Zepanya, Ana Cruz, Array, Compliance', 999, '2026-03-23 10:34:29', 'sent');

-- --------------------------------------------------------

--
-- Table structure for table `incident_memo_delivery`
--

CREATE TABLE `incident_memo_delivery` (
  `id` int(11) NOT NULL,
  `memo_id` int(11) NOT NULL,
  `recipient_name` varchar(255) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `acknowledged` tinyint(4) DEFAULT 0,
  `acknowledged_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incident_memo_delivery`
--

INSERT INTO `incident_memo_delivery` (`id`, `memo_id`, `recipient_name`, `incident_id`, `acknowledged`, `acknowledged_at`, `delivered_at`) VALUES
(1, 1, 'John Rey', 32, 0, NULL, '2026-03-23 09:36:05'),
(2, 1, 'Brian Flores', 32, 0, NULL, '2026-03-23 09:36:05'),
(3, 1, 'Array', 32, 0, NULL, '2026-03-23 09:36:05'),
(4, 2, 'John Rey', 32, 0, NULL, '2026-03-23 09:36:36'),
(5, 2, 'Brian Flores', 32, 0, NULL, '2026-03-23 09:36:36'),
(6, 2, 'Array', 32, 0, NULL, '2026-03-23 09:36:36'),
(7, 3, 'Maria Santos', 31, 0, NULL, '2026-03-23 09:39:30'),
(8, 3, 'Pedro Lopez', 31, 0, NULL, '2026-03-23 09:39:30'),
(9, 3, 'Array', 31, 0, NULL, '2026-03-23 09:39:30'),
(10, 4, 'Maria Santos', 31, 0, NULL, '2026-03-23 09:39:43'),
(11, 4, 'Pedro Lopez', 31, 0, NULL, '2026-03-23 09:39:43'),
(12, 4, 'Array', 31, 0, NULL, '2026-03-23 09:39:43'),
(13, 5, 'Maria Santos', 31, 0, NULL, '2026-03-23 09:39:56'),
(14, 5, 'Pedro Lopez', 31, 0, NULL, '2026-03-23 09:39:56'),
(15, 5, 'Array', 31, 0, NULL, '2026-03-23 09:39:56'),
(16, 6, 'Niki Zepanya', 33, 0, NULL, '2026-03-23 10:23:20'),
(17, 6, 'Ana Cruz', 33, 0, NULL, '2026-03-23 10:23:20'),
(18, 6, 'Array', 33, 0, NULL, '2026-03-23 10:23:20'),
(19, 6, 'Compliance', 33, 0, NULL, '2026-03-23 10:23:20'),
(20, 7, 'Niki Zepanya', 33, 0, NULL, '2026-03-23 10:24:13'),
(21, 7, 'Ana Cruz', 33, 0, NULL, '2026-03-23 10:24:13'),
(22, 7, 'Array', 33, 0, NULL, '2026-03-23 10:24:13'),
(23, 7, 'Compliance', 33, 0, NULL, '2026-03-23 10:24:13'),
(24, 8, 'Niki Zepanya', 33, 0, NULL, '2026-03-23 10:34:29'),
(25, 8, 'Ana Cruz', 33, 0, NULL, '2026-03-23 10:34:29'),
(26, 8, 'Array', 33, 0, NULL, '2026-03-23 10:34:29'),
(27, 8, 'Compliance', 33, 0, NULL, '2026-03-23 10:34:29');

-- --------------------------------------------------------

--
-- Table structure for table `incident_notes`
--

CREATE TABLE `incident_notes` (
  `id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `added_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `incident_notifications`
--

CREATE TABLE `incident_notifications` (
  `id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_approvals`
--

CREATE TABLE `leave_approvals` (
  `id` int(11) NOT NULL,
  `leave_application_id` int(11) NOT NULL,
  `approver_type` enum('manager','hr','payroll') NOT NULL,
  `approver_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL,
  `comments` text DEFAULT NULL,
  `approved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_attachments`
--

CREATE TABLE `leave_attachments` (
  `id` int(11) NOT NULL,
  `leave_application_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_balances`
--

CREATE TABLE `leave_balances` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type` varchar(50) NOT NULL COMMENT 'maternity, paternity, solo_parent, service_incentive, special_women, sick, vacation',
  `year` int(11) NOT NULL,
  `days_available` decimal(5,1) DEFAULT 0.0,
  `days_used` decimal(5,1) DEFAULT 0.0,
  `carried_over_days` decimal(5,1) DEFAULT 0.0,
  `accrual_added` decimal(5,1) DEFAULT 0.0,
  `last_accrual_date` date DEFAULT NULL,
  `is_pro_rated` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_documents`
--

CREATE TABLE `leave_documents` (
  `id` int(11) NOT NULL,
  `leave_id` int(11) NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_documents`
--

INSERT INTO `leave_documents` (`id`, `leave_id`, `document_type`, `file_path`, `uploaded_at`) VALUES
(1, 1, 'Medical Certificate', 'uploads/leave_documents/maternity_medical_cert.pdf', '2026-03-18 15:44:19'),
(2, 1, 'SSS Maternity Benefit Form', 'uploads/leave_documents/maternity_sss_form.pdf', '2026-03-18 15:44:19'),
(3, 2, 'Marriage Certificate', 'uploads/leave_documents/paternity_marriage_cert.pdf', '2026-03-18 15:44:19'),
(4, 2, 'Wife Delivery Proof', 'uploads/leave_documents/paternity_delivery_proof.pdf', '2026-03-18 15:44:19'),
(5, 3, 'Medical Certificate', 'uploads/leave_documents/sick_medical_cert.pdf', '2026-03-18 15:44:19'),
(6, 5, 'Death Certificate', 'uploads/leave_documents/bereavement_death_cert.pdf', '2026-03-18 15:44:19'),
(7, 7, 'Medical Certificate', 'uploads/leave_documents/approved_sick_medical_cert.pdf', '2026-03-18 15:44:19');

-- --------------------------------------------------------

--
-- Table structure for table `leave_history`
--

CREATE TABLE `leave_history` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `leave_application_id` int(11) NOT NULL,
  `action_type` enum('filed','approved','rejected','cancelled','modified','credit_adjusted') NOT NULL,
  `previous_balance` decimal(5,1) DEFAULT NULL,
  `new_balance` decimal(5,1) DEFAULT NULL,
  `days_affected` decimal(5,1) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `performed_by` int(11) DEFAULT NULL,
  `performed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_notifications`
--

CREATE TABLE `leave_notifications` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_application_id` int(11) DEFAULT NULL,
  `notification_type` enum('submitted','manager_approved','manager_rejected','hr_approved','hr_rejected','payroll_processed','reminder') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_payroll_integrations`
--

CREATE TABLE `leave_payroll_integrations` (
  `id` int(11) NOT NULL,
  `leave_application_id` int(11) NOT NULL,
  `payroll_run_id` int(11) DEFAULT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type_code` varchar(20) NOT NULL,
  `days_deducted` decimal(5,1) NOT NULL,
  `deduction_amount` decimal(10,2) DEFAULT 0.00,
  `processed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_policy_configurations`
--

CREATE TABLE `leave_policy_configurations` (
  `id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `max_consecutive_days` decimal(5,1) DEFAULT 0.0,
  `min_advance_days` int(11) DEFAULT 0,
  `requires_medical_certificate` tinyint(1) DEFAULT 0,
  `accrual_rate` decimal(5,4) DEFAULT 0.0000,
  `accrual_frequency` varchar(20) DEFAULT 'monthly',
  `can_carry_over` tinyint(1) DEFAULT 0,
  `max_carry_over_days` decimal(5,1) DEFAULT 0.0,
  `effective_from` date DEFAULT NULL,
  `effective_to` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_policy_configurations`
--

INSERT INTO `leave_policy_configurations` (`id`, `leave_type_id`, `category_id`, `max_consecutive_days`, `min_advance_days`, `requires_medical_certificate`, `accrual_rate`, `accrual_frequency`, `can_carry_over`, `max_carry_over_days`, `effective_from`, `effective_to`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 15.0, 3, 0, 1.2500, 'monthly', 1, 5.0, NULL, NULL, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(2, 2, NULL, 15.0, 2, 1, 1.2500, 'monthly', 1, 5.0, NULL, NULL, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(3, 3, NULL, 105.0, 30, 1, 0.0000, 'none', 0, 0.0, NULL, NULL, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(4, 4, NULL, 7.0, 15, 0, 0.0000, 'none', 0, 0.0, NULL, NULL, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(5, 5, NULL, 7.0, 15, 1, 0.0000, 'none', 0, 0.0, NULL, NULL, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(6, 6, NULL, 5.0, 3, 0, 0.4200, 'monthly', 1, 3.0, NULL, NULL, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(7, 7, NULL, 5.0, 14, 0, 0.0000, 'none', 0, 0.0, NULL, NULL, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(8, 8, NULL, 5.0, 1, 0, 0.4200, 'monthly', 1, 3.0, NULL, NULL, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(9, 9, NULL, 30.0, 7, 0, 0.0000, 'none', 0, 0.0, NULL, NULL, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39');

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` decimal(5,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `manager_approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `hr_approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `checked_by` int(11) DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `checked_at` timestamp NULL DEFAULT NULL,
  `hr_comments` text DEFAULT NULL,
  `manager_comments` text DEFAULT NULL,
  `credit_deducted` decimal(5,2) DEFAULT NULL,
  `payroll_processed` tinyint(1) DEFAULT 0,
  `payroll_processed_at` timestamp NULL DEFAULT NULL,
  `payroll_period_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `checklist_data` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `employee_id`, `leave_type`, `start_date`, `end_date`, `total_days`, `reason`, `attachment_path`, `status`, `manager_approval_status`, `hr_approval_status`, `checked_by`, `reviewed_by`, `reviewed_at`, `checked_at`, `hr_comments`, `manager_comments`, `credit_deducted`, `payroll_processed`, `payroll_processed_at`, `payroll_period_id`, `created_at`, `updated_at`, `checklist_data`) VALUES
(1, 1, 'Maternity Leave', '2026-04-01', '2026-07-15', 105.00, 'Pregnancy with expected delivery date July 2026 - Required for maternity coverage under RA 11210', NULL, 'approved', 'pending', 'pending', 999, NULL, NULL, '2026-03-18 14:55:14', '', NULL, NULL, 0, NULL, NULL, '2026-03-18 13:57:26', '2026-03-18 14:55:14', NULL),
(2, 2, 'Paternity Leave', '2026-03-20', '2026-03-26', 7.00, 'Wife Maria Santos scheduled for normal delivery on March 22, 2026', NULL, 'approved', 'pending', 'pending', 999, NULL, NULL, '2026-03-18 15:17:19', '', NULL, NULL, 0, NULL, NULL, '2026-03-18 13:57:26', '2026-03-18 15:17:19', '{\"requirements\":{\"marriage_proof\":true,\"delivery_proof\":true,\"childbirth_claims\":true,\"service_duration\":true},\"hrChecks\":{\"valid_marriage\":true,\"documents_submitted\":true,\"within_entitlement\":true}}'),
(3, 3, 'Sick Leave', '2026-03-10', '2026-03-11', 2.00, 'Medical certificate attached - Upper respiratory infection', NULL, 'pending', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-03-18 13:57:26', '2026-03-18 13:57:26', NULL),
(4, 4, 'Vacation Leave', '2026-04-10', '2026-04-15', 6.00, 'Family vacation to Palawan - filed 2 weeks in advance', NULL, 'pending', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-03-18 13:57:26', '2026-03-18 13:57:26', NULL),
(5, 5, 'Bereavement Leave', '2026-03-05', '2026-03-07', 3.00, 'Death of father - death certificate attached', NULL, 'pending', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-03-18 13:57:26', '2026-03-18 13:57:26', NULL),
(6, 6, 'Emergency Leave', '2026-03-12', '2026-03-12', 1.00, 'Immediate family medical emergency - hospitalization', NULL, 'pending', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-03-18 13:57:26', '2026-03-18 13:57:26', NULL),
(7, 1, 'Sick Leave', '2026-02-10', '2026-02-12', 3.00, 'Flu with fever', NULL, 'pending', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-03-18 13:57:26', '2026-03-18 14:48:47', NULL),
(8, 2, 'Vacation Leave', '2026-02-15', '2026-02-18', 4.00, 'Personal matter', NULL, 'pending', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-03-18 13:57:26', '2026-03-18 14:48:52', NULL),
(9, 3, 'Emergency Leave', '2026-02-20', '2026-02-20', 1.00, 'Power outage at home', NULL, 'rejected', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-03-18 13:57:26', '2026-03-18 13:57:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `max_days_per_year` decimal(5,1) DEFAULT 0.0,
  `requires_attachment` tinyint(1) DEFAULT 0,
  `requires_approval_level` int(11) DEFAULT 1 COMMENT '1=Manager, 2=HR, 3=Both',
  `is_paid` tinyint(1) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `code`, `name`, `description`, `max_days_per_year`, `requires_attachment`, `requires_approval_level`, `is_paid`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'VACATION', 'Vacation Leave', 'Regular vacation leave with pay', 15.0, 0, 1, 1, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(2, 'SICK', 'Sick Leave', 'Sick leave with pay under labor regulations', 15.0, 1, 2, 1, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(3, 'MATERNITY', 'Maternity Leave', 'Maternity leave under Magna Carta for Women (RA 9710)', 105.0, 1, 3, 1, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(4, 'PATERNITY', 'Paternity Leave', 'Paternity leave under Magna Carta for Women (RA 9710)', 7.0, 0, 2, 1, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(5, 'SOLO_PARENT', 'Solo Parent Leave', 'Solo Parent Leave under RA 8972', 7.0, 1, 2, 1, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(6, 'BEREAVEMENT', 'Bereavement Leave', 'Leave for immediate family death', 5.0, 0, 1, 1, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(7, 'MARRIAGE', 'Marriage Leave', 'Leave for marriage ceremony', 5.0, 0, 1, 1, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(8, 'EMERGENCY', 'Emergency Leave', 'Emergency/calamity leave', 5.0, 0, 1, 1, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(9, 'UNPAID', 'Unpaid Leave', 'Leave without pay', 30.0, 0, 2, 0, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(10, 'MVA', 'Menstruation Leave', 'Menstrual leave (if applicable under company policy)', 0.0, 0, 1, 1, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(11, 'REHABILITATION', 'Rehabilitation Leave', 'Leave for work-related injury rehabilitation', 0.0, 1, 3, 1, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(12, 'JUDICIAL', 'Judicial Leave', 'Leave for court appearances', 0.0, 1, 2, 1, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39'),
(13, 'STUDY', 'Study Leave', 'Educational/learning leave', 0.0, 1, 2, 0, 1, '2026-03-18 04:43:39', '2026-03-18 04:43:39');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `type` varchar(50) DEFAULT 'info',
  `link` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `link`, `is_read`, `created_at`) VALUES
(19, 1, 'Welcome to the Employee Management System!', 'Your account has been successfully set up. You can now access all features of the system.', 'success', NULL, 1, '2026-02-11 22:20:21'),
(20, 1, 'New Employee Added', 'John Doe has joined the Engineering department as a Software Developer.', 'info', NULL, 1, '2026-02-11 22:20:21'),
(21, 1, 'Leave Request Approved', 'Your leave request for Feb 15-16 has been approved by your manager.', 'success', NULL, 1, '2026-02-11 22:20:21');

-- --------------------------------------------------------

--
-- Table structure for table `overtime`
--

CREATE TABLE `overtime` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `hours` decimal(5,2) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `approved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `part_time_hours`
--

CREATE TABLE `part_time_hours` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `payroll_period_id` int(11) DEFAULT NULL,
  `hours_worked` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `part_time_hours`
--

INSERT INTO `part_time_hours` (`id`, `employee_id`, `payroll_period_id`, `hours_worked`) VALUES
(3, 2, 30, 45.00),
(4, 6, 30, 40.00);

-- --------------------------------------------------------

--
-- Table structure for table `payroll_periods`
--

CREATE TABLE `payroll_periods` (
  `id` int(11) NOT NULL,
  `period_name` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `pay_date` date DEFAULT NULL,
  `status` enum('open','processing','closed') DEFAULT 'open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll_periods`
--

INSERT INTO `payroll_periods` (`id`, `period_name`, `start_date`, `end_date`, `pay_date`, `status`) VALUES
(30, 'Jan 1-15, 2026', '2026-01-01', '2026-01-15', '2026-03-20', 'closed'),
(31, 'Jan 16-31, 2026', '2026-01-16', '2026-01-31', '2026-02-05', 'open');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_runs`
--

CREATE TABLE `payroll_runs` (
  `id` int(11) NOT NULL,
  `payroll_period_id` int(11) DEFAULT NULL,
  `processed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('draft','finalized') DEFAULT 'draft'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll_runs`
--

INSERT INTO `payroll_runs` (`id`, `payroll_period_id`, `processed_at`, `status`) VALUES
(15, 30, '2026-03-05 05:55:34', 'finalized');

-- --------------------------------------------------------

--
-- Table structure for table `payslips`
--

CREATE TABLE `payslips` (
  `id` int(11) NOT NULL,
  `payroll_run_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `gross_pay` decimal(10,2) DEFAULT NULL,
  `total_deductions` decimal(10,2) DEFAULT NULL,
  `net_pay` decimal(10,2) DEFAULT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payslips`
--

INSERT INTO `payslips` (`id`, `payroll_run_id`, `employee_id`, `gross_pay`, `total_deductions`, `net_pay`, `generated_at`) VALUES
(57, 15, 1, 15000.00, 1750.00, 13250.00, '2026-03-05 05:55:33'),
(58, 15, 2, 22500.00, 2125.00, 20375.00, '2026-03-05 05:55:33'),
(59, 15, 3, 11000.00, 1550.00, 9450.00, '2026-03-05 05:55:33'),
(60, 15, 4, 9000.00, 1360.00, 7640.00, '2026-03-05 05:55:33'),
(61, 15, 5, 7500.00, 1150.00, 6350.00, '2026-03-05 05:55:34'),
(62, 15, 6, 22000.00, 2100.00, 19900.00, '2026-03-05 05:55:34');

-- --------------------------------------------------------

--
-- Table structure for table `payslip_items`
--

CREATE TABLE `payslip_items` (
  `id` int(11) NOT NULL,
  `payslip_id` int(11) DEFAULT NULL,
  `item_type` enum('earning','deduction') DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payslip_items`
--

INSERT INTO `payslip_items` (`id`, `payslip_id`, `item_type`, `description`, `amount`) VALUES
(1, 57, 'earning', 'Basic Salary', 15000.00),
(2, 57, 'deduction', 'SSS', 900.00),
(3, 57, 'deduction', 'PhilHealth', 750.00),
(4, 57, 'deduction', 'Pag-IBIG', 100.00),
(5, 58, 'earning', 'Basic Salary', 22500.00),
(6, 58, 'deduction', 'SSS', 900.00),
(7, 58, 'deduction', 'PhilHealth', 1125.00),
(8, 58, 'deduction', 'Pag-IBIG', 100.00),
(9, 59, 'earning', 'Basic Salary', 11000.00),
(10, 59, 'deduction', 'SSS', 900.00),
(11, 59, 'deduction', 'PhilHealth', 550.00),
(12, 59, 'deduction', 'Pag-IBIG', 100.00),
(13, 60, 'earning', 'Basic Salary', 9000.00),
(14, 60, 'deduction', 'SSS', 810.00),
(15, 60, 'deduction', 'PhilHealth', 450.00),
(16, 60, 'deduction', 'Pag-IBIG', 100.00),
(17, 61, 'earning', 'Basic Salary', 7500.00),
(18, 61, 'deduction', 'SSS', 675.00),
(19, 61, 'deduction', 'PhilHealth', 375.00),
(20, 61, 'deduction', 'Pag-IBIG', 100.00),
(21, 62, 'earning', 'Basic Salary', 22000.00),
(22, 62, 'deduction', 'SSS', 900.00),
(23, 62, 'deduction', 'PhilHealth', 1100.00),
(24, 62, 'deduction', 'Pag-IBIG', 100.00);

-- --------------------------------------------------------

--
-- Table structure for table `philippine_laws`
--

CREATE TABLE `philippine_laws` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL COMMENT 'RA_11210, RA_9710, etc',
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `philippine_laws`
--

INSERT INTO `philippine_laws` (`id`, `code`, `title`, `description`, `effective_date`, `created_at`) VALUES
(1, 'RA_11210', 'Expanded Maternity Leave Law', '105 days maternity leave for qualified women', '2019-03-11', '2026-03-17 06:09:33'),
(2, 'RA_9710', 'Magna Carta of Women', 'Gender equality and anti-discrimination', '2009-08-14', '2026-03-17 06:09:33'),
(3, 'RA_8187', 'Paternity Leave Act', '7 days paternity leave for married fathers', '1996-06-17', '2026-03-17 06:09:33'),
(4, 'RA_10151', 'Night Workers Act', 'Protection for night shift workers', '2010-06-23', '2026-03-17 06:09:33'),
(5, 'RA_11058', 'Occupational Safety and Health Law', 'Workplace safety standards', '2018-06-27', '2026-03-17 06:09:33'),
(6, 'RA_11199', 'SSS Act of 2019', 'Social Security System amendments', '2019-02-22', '2026-03-17 06:09:33'),
(7, 'RA_7877', 'Anti-Sexual Harassment Act', 'Anti-sexual harassment in workplace', '1995-02-24', '2026-03-17 06:09:33'),
(8, 'RA_11313', 'Safe Spaces Act (Bawal Bastos Law)', 'Anti-gender based harassment', '2019-04-15', '2026-03-17 06:09:33'),
(9, 'RA_10911', 'Anti-Age Discrimination Act', 'Prohibition of age discrimination', '2016-07-22', '2026-03-17 06:09:33'),
(10, 'RA_7277', 'Magna Carta for Disabled Persons', 'Rights and privileges for PWDs', '1992-03-24', '2026-03-17 06:09:33'),
(11, 'RA_10173', 'Data Privacy Act', 'Data protection and privacy', '2012-09-12', '2026-03-17 06:09:33'),
(12, 'RA_8972', 'Solo Parents Leave Act', '7 days leave for solo parents', '2000-11-01', '2026-03-17 06:09:33'),
(13, 'RA 11210', 'Expanded Maternity Leave Law', '105 days maternity leave for qualified women', '2019-03-11', '2026-03-17 06:43:05'),
(14, 'RA 8187', 'Paternity Leave Act', '7 days paternity leave for married fathers', '1996-06-17', '2026-03-17 06:43:05'),
(15, 'RA 8972', 'Solo Parents Leave Act', '7 days leave for solo parents', '2000-11-01', '2026-03-17 06:43:05'),
(16, 'RA 9710', 'Magna Carta of Women', 'Gender equality and anti-discrimination', '2009-08-14', '2026-03-17 06:43:05'),
(17, 'RA 11058', 'Occupational Safety and Health Law', 'Workplace safety standards', '2018-06-27', '2026-03-17 06:43:05'),
(18, 'RA 7877', 'Anti-Sexual Harassment Act', 'Anti-sexual harassment in workplace', '1995-02-24', '2026-03-17 06:43:05'),
(19, 'RA 11313', 'Safe Spaces Act', 'Anti-gender based harassment', '2019-04-15', '2026-03-17 06:43:05'),
(20, 'RA 10173', 'Data Privacy Act', 'Data protection and privacy', '2012-09-12', '2026-03-17 06:43:05'),
(21, 'RA 11210', 'Expanded Maternity Leave Law', '105 days maternity leave for qualified women', '2019-03-11', '2026-03-17 08:49:23'),
(22, 'RA 8187', 'Paternity Leave Act', '7 days paternity leave for married fathers', '1996-06-17', '2026-03-17 08:49:23'),
(23, 'RA 8972', 'Solo Parents Leave Act', '7 days leave for solo parents', '2000-11-01', '2026-03-17 08:49:23'),
(24, 'RA 9710', 'Magna Carta of Women', 'Gender equality and anti-discrimination', '2009-08-14', '2026-03-17 08:49:23'),
(25, 'RA 11058', 'Occupational Safety and Health Law', 'Workplace safety standards', '2018-06-27', '2026-03-17 08:49:23'),
(26, 'RA 7877', 'Anti-Sexual Harassment Act', 'Anti-sexual harassment in workplace', '1995-02-24', '2026-03-17 08:49:23'),
(27, 'RA 11313', 'Safe Spaces Act', 'Anti-gender based harassment', '2019-04-15', '2026-03-17 08:49:23'),
(28, 'RA 10173', 'Data Privacy Act', 'Data protection and privacy', '2012-09-12', '2026-03-17 08:49:23');

-- --------------------------------------------------------

--
-- Table structure for table `policies`
--

CREATE TABLE `policies` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `version` varchar(20) DEFAULT '1.0',
  `content` longtext DEFAULT NULL,
  `status` enum('Draft','Rejected','Published','Pending Approval') DEFAULT 'Draft',
  `created_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `published_by` int(11) DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_mandatory` tinyint(1) DEFAULT 0,
  `acknowledgment_required` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `policies`
--

INSERT INTO `policies` (`id`, `title`, `category_id`, `category`, `version`, `content`, `status`, `created_by`, `approved_by`, `published_by`, `effective_date`, `expiry_date`, `approved_at`, `published_at`, `created_at`, `updated_at`, `is_mandatory`, `acknowledgment_required`) VALUES
(4, 'Employee Code of Ethics', 6, 'HR', '1.0', 'All employees must adhere to ethical standards including honesty, integrity, and professionalism in all institutional activities.', 'Published', 18, 18, 18, '2026-01-01', NULL, '2026-01-05 09:00:00', '2026-01-06 08:00:00', '2026-03-18 08:24:37', '2026-03-18 11:51:10', 1, 1),
(5, 'Attendance and Timekeeping Policy', 2, 'HR', '1.0', 'Employees must follow official working hours. Absences and tardiness must be properly documented and approved.', 'Published', 18, 18, 18, '2026-01-01', NULL, '2026-01-04 10:00:00', '2026-01-05 09:00:00', '2026-03-18 08:24:37', '2026-03-18 11:51:15', 1, 1),
(6, 'IT Acceptable Use Policy', 4, 'IT', '1.0', 'Use of institutional IT resources must be for official purposes only. Unauthorized access and misuse are strictly prohibited.', 'Published', 18, 18, NULL, '2026-02-01', NULL, '2026-02-05 11:00:00', NULL, '2026-03-18 08:24:37', '2026-03-18 11:58:03', 1, 1),
(7, 'Confidentiality Policy', 6, 'Legal', '1.0', 'Employees must protect confidential information including student records, employee data, and institutional documents.', 'Published', 18, 18, 18, '2026-01-01', NULL, '2026-01-06 01:00:00', '2026-01-07 09:00:00', '2026-03-18 08:24:37', '2026-03-18 11:53:43', 1, 1),
(8, 'Grievance Handling Policy', 6, 'HR', '1.0', 'Employees may file complaints through proper channels. All grievances will be handled fairly and confidentially.', 'Rejected', 18, 18, 18, '2026-01-01', NULL, '2026-01-07 02:00:00', '2026-01-08 09:30:00', '2026-03-18 08:24:37', '2026-03-18 11:51:28', 1, 1),
(9, 'Disciplinary Action Policy', 1, 'HR', '1.0', 'Defines offenses and corresponding penalties. Ensures due process before disciplinary action is enforced.', 'Published', 18, 18, NULL, '2026-02-01', NULL, '2026-02-10 10:00:00', NULL, '2026-03-18 08:24:37', '2026-03-18 11:53:48', 1, 1),
(10, 'Academic Integrity Policy', 1, 'Academic', '1.0', 'Faculty and staff must uphold academic honesty and prevent cheating, plagiarism, and misconduct.', 'Pending Approval', 18, 18, 18, '2026-01-01', NULL, '2026-01-06 03:00:00', '2026-01-07 10:00:00', '2026-03-18 08:24:37', '2026-03-18 11:55:19', 1, 1),
(11, 'Faculty Workload Policy', 1, 'Academic', '1.0', 'Defines teaching load, preparation hours, and consultation hours for faculty members.', 'Published', 18, NULL, NULL, '2026-03-01', NULL, NULL, NULL, '2026-03-18 08:24:37', '2026-03-18 11:53:52', 0, 1),
(12, 'Recruitment and Hiring Policy', 6, 'HR', '1.0', 'Outlines hiring procedures including job posting, screening, interviews, and onboarding.', 'Pending Approval', 18, NULL, NULL, '2026-03-10', NULL, NULL, NULL, '2026-03-18 08:24:37', '2026-03-18 16:27:10', 1, 0),
(13, 'Benefits and Compensation Policy', NULL, 'HR Policies', '1.0', 'Defines salary structure, allowances, and government-mandated benefits.', 'Pending Approval', 18, 18, NULL, '2026-02-01', NULL, '2026-02-08 04:00:00', NULL, '2026-03-18 08:24:37', '2026-03-23 16:44:40', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `policy_acknowledgments`
--

CREATE TABLE `policy_acknowledgments` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `policy_id` int(11) NOT NULL,
  `acknowledged_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `browser_info` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `policy_approvals`
--

CREATE TABLE `policy_approvals` (
  `id` int(11) NOT NULL,
  `policy_id` int(11) NOT NULL,
  `action` enum('Submitted','Approved','Rejected') NOT NULL,
  `remarks` text DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `policy_approvals`
--

INSERT INTO `policy_approvals` (`id`, `policy_id`, `action`, `remarks`, `approved_by`, `created_at`) VALUES
(4, 12, 'Submitted', 'Submitted for approval', 999, '2026-03-18 11:24:27'),
(5, 12, 'Submitted', 'Submitted for approval', 999, '2026-03-18 11:24:35'),
(9, 7, 'Submitted', 'Submitted for approval', 999, '2026-03-18 11:49:38'),
(10, 7, 'Submitted', 'Submitted for approval', 999, '2026-03-18 11:49:59'),
(11, 7, 'Submitted', 'Submitted for approval', 999, '2026-03-18 11:50:34'),
(12, 7, 'Submitted', 'Submitted for approval', 999, '2026-03-18 11:50:48'),
(13, 7, 'Submitted', 'Submitted for approval', 999, '2026-03-18 11:50:53'),
(14, 7, 'Submitted', 'Submitted for approval', 999, '2026-03-18 11:52:19'),
(15, 7, 'Submitted', 'Submitted for approval', 999, '2026-03-18 11:52:42'),
(16, 12, 'Submitted', 'Submitted for approval', 999, '2026-03-18 16:27:10');

-- --------------------------------------------------------

--
-- Table structure for table `policy_categories`
--

CREATE TABLE `policy_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `policy_categories`
--

INSERT INTO `policy_categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'HR', 'Human Resources policies', '2026-03-18 04:40:15', '2026-03-18 04:40:15'),
(2, 'Privacy', 'Data privacy and protection policies', '2026-03-18 04:40:15', '2026-03-18 04:40:15'),
(3, 'Safety', 'Workplace safety and health policies', '2026-03-18 04:40:15', '2026-03-18 04:40:15'),
(4, 'Legal', 'Legal compliance policies', '2026-03-18 04:40:15', '2026-03-18 04:40:15'),
(5, 'General', 'General company policies', '2026-03-18 04:40:15', '2026-03-18 04:40:15'),
(6, 'Finance', 'Financial policies', '2026-03-18 04:40:15', '2026-03-18 04:40:15');

-- --------------------------------------------------------

--
-- Table structure for table `policy_notifications`
--

CREATE TABLE `policy_notifications` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `policy_id` int(11) DEFAULT NULL,
  `notification_type` enum('new_policy','acknowledgment_required','acknowledgment_overdue','policy_approved','policy_rejected','policy_published') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `policy_versions`
--

CREATE TABLE `policy_versions` (
  `id` int(11) NOT NULL,
  `policy_id` int(11) NOT NULL,
  `version` varchar(20) NOT NULL,
  `content` longtext DEFAULT NULL,
  `changes_summary` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `policy_versions`
--

INSERT INTO `policy_versions` (`id`, `policy_id`, `version`, `content`, `changes_summary`, `created_by`, `created_at`) VALUES
(96, 13, '1.0', 'Defines salary structure, allowances, and government-mandated benefits.', '', 999, '2026-03-23 11:12:49'),
(101, 13, '1.0', 'Defines salary structure, allowances, and government-mandated benefits.', 'sdd', 999, '2026-03-23 16:44:40');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `level` varchar(50) DEFAULT NULL,
  `employment_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `title`, `category_id`, `department_id`, `level`, `employment_type`) VALUES
(1, 'Instructor', 1, NULL, NULL, NULL),
(2, 'Professor', 1, NULL, NULL, NULL),
(3, 'Registrar', 2, NULL, NULL, NULL),
(4, 'Cashier', 2, NULL, NULL, NULL),
(5, 'Janitor', 2, NULL, NULL, NULL),
(11, 'President', 4, 2, 'Executive', 'Full-time'),
(12, 'Executive Vice President', 4, 3, 'Executive', 'Full-time'),
(13, 'Vice President for Academic Affairs', 4, 4, 'Executive', 'Full-time'),
(14, 'Vice President for Admin and Finance', 4, 5, 'Executive', 'Full-time'),
(15, 'Executive Assistant', 2, 2, 'Staff', 'Full-time'),
(16, 'Dean - Engineering', 1, 10, 'Management', 'Full-time'),
(17, 'Program Head - Engineering', 1, 10, 'Supervisor', 'Full-time'),
(18, 'Instructor - Engineering', 1, 10, 'Entry', 'Full-time'),
(19, 'Dean - IT', 1, 11, 'Management', 'Full-time'),
(20, 'Program Head - IT', 1, 11, 'Supervisor', 'Full-time'),
(21, 'Instructor - IT', 1, 11, 'Entry', 'Full-time'),
(22, 'Dean - Education', 1, 13, 'Management', 'Full-time'),
(23, 'Instructor - Education', 1, 13, 'Entry', 'Full-time'),
(24, 'Dean - Nursing', 1, 14, 'Management', 'Full-time'),
(25, 'Clinical Instructor', 1, 14, 'Entry', 'Full-time'),
(26, 'Dean - CAS', 1, 15, 'Management', 'Full-time'),
(27, 'Instructor - CAS', 1, 15, 'Entry', 'Full-time'),
(28, 'Dean - COBA', 1, 16, 'Management', 'Full-time'),
(29, 'Instructor - COBA', 1, 16, 'Entry', 'Full-time'),
(30, 'Dean - CCJ', 1, 17, 'Management', 'Full-time'),
(31, 'Instructor - CCJ', 1, 17, 'Entry', 'Full-time'),
(32, 'Dean - CHTBAM', 1, 12, 'Management', 'Full-time'),
(33, 'Instructor - CHTBAM', 1, 12, 'Entry', 'Full-time'),
(34, 'SHS Principal', 1, 20, 'Management', 'Full-time'),
(35, 'SHS Coordinator', 1, 20, 'Supervisor', 'Full-time'),
(36, 'SHS Teacher', 1, 20, 'Entry', 'Full-time'),
(37, 'STEM Coordinator', 1, 21, 'Supervisor', 'Full-time'),
(38, 'STEM Teacher', 1, 21, 'Entry', 'Full-time'),
(39, 'ABM Coordinator', 1, 22, 'Supervisor', 'Full-time'),
(40, 'ABM Teacher', 1, 22, 'Entry', 'Full-time'),
(41, 'HUMSS Teacher', 1, 23, 'Entry', 'Full-time'),
(42, 'GAS Teacher', 1, 24, 'Entry', 'Full-time'),
(43, 'ICT Instructor', 1, 25, 'Entry', 'Full-time'),
(44, 'Home Economics Instructor', 1, 26, 'Entry', 'Full-time'),
(45, 'Industrial Arts Instructor', 1, 27, 'Entry', 'Full-time'),
(46, 'Sports Coach', 1, 28, 'Entry', 'Full-time'),
(47, 'Arts Instructor', 1, 29, 'Entry', 'Full-time'),
(48, 'HR Manager', 2, 30, 'Management', 'Full-time'),
(49, 'HR Officer', 2, 30, 'Supervisor', 'Full-time'),
(50, 'HR Assistant', 2, 30, 'Entry', 'Full-time'),
(51, 'Accounting Manager', 2, 31, 'Management', 'Full-time'),
(52, 'Accountant', 2, 31, 'Professional', 'Full-time'),
(53, 'Accounting Clerk', 2, 31, 'Entry', 'Full-time'),
(54, 'Cashier', 2, 31, 'Entry', 'Full-time');

-- --------------------------------------------------------

--
-- Table structure for table `report_notes`
--

CREATE TABLE `report_notes` (
  `id` int(11) NOT NULL,
  `payroll_period_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `risks`
--

CREATE TABLE `risks` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL COMMENT 'legal, operational, employee_related',
  `description` text NOT NULL,
  `severity` enum('low','medium','high','critical') DEFAULT 'medium',
  `status` enum('identified','mitigating','mitigated','closed') DEFAULT 'identified',
  `related_employee_id` int(11) DEFAULT NULL,
  `identified_by` int(11) NOT NULL,
  `mitigation_plan` text DEFAULT NULL,
  `target_date` date DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `risk_alerts`
--

CREATE TABLE `risk_alerts` (
  `id` int(11) NOT NULL,
  `risk_id` int(11) NOT NULL,
  `alert_type` varchar(50) NOT NULL COMMENT 'expiring_contract, missing_benefits, overworked',
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `risk_flags`
--

CREATE TABLE `risk_flags` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `rule_id` int(11) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `severity` enum('critical','high','medium','low') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_resolved` tinyint(1) DEFAULT 0,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `risk_flags`
--

INSERT INTO `risk_flags` (`id`, `employee_id`, `rule_id`, `category`, `severity`, `title`, `description`, `is_resolved`, `resolved_at`, `created_at`) VALUES
(25, 3, NULL, 'Employment Compliance', 'critical', 'Probationary Period Exceeded', 'Employee has been on probationary status for more than 6 months without regularization', 1, '2026-03-17 08:40:53', '2026-03-17 08:08:43'),
(26, 3, NULL, 'Leave Law Compliance', 'high', 'Maternity Leave Eligibility Issue', 'Female employee may not meet the 6-month employment requirement for full maternity leave benefits\n\nEscalation Notes: Error loading risk flag details.', 0, NULL, '2026-03-17 08:08:43'),
(27, 2, NULL, 'Leave Law Compliance', 'high', 'Service Incentive Leave Balance Low', 'Employee has less than 5 days Service Incentive Leave balance', 0, NULL, '2026-03-17 08:08:43'),
(28, 2, NULL, 'Benefits Compliance', 'high', 'Incomplete Government Benefits', 'Employee may have incomplete or pending government benefit registrations', 0, NULL, '2026-03-17 08:08:43'),
(29, 1, NULL, 'Workplace Protection', 'high', 'Pending Policy Acknowledgment', 'Employee has not acknowledged the latest workplace safety policy update', 1, '2026-03-17 13:44:42', '2026-03-17 08:08:43'),
(30, 3, NULL, 'Data Privacy Compliance', 'medium', 'Data Privacy Consent Expiring', 'Employee data privacy consent needs renewal', 0, NULL, '2026-03-17 08:08:43'),
(31, 1, NULL, 'Employment Compliance', 'high', 'Probationary Period Exceeded', 'Employee has exceeded probationary period without performance review', 0, NULL, '2026-03-18 16:46:06'),
(32, 2, NULL, 'Leave Law Compliance', 'medium', 'Unfiled Vacation Leave', 'Employee has unused vacation leaves that were not filed', 0, NULL, '2026-03-18 16:46:06'),
(33, 3, NULL, 'Data Privacy Compliance', 'critical', 'Confidential Data Access', 'Employee accessed sensitive employee data without authorization', 0, NULL, '2026-03-18 16:46:06'),
(34, 4, NULL, 'Benefits Compliance', 'high', 'Incomplete SSS Enrollment', 'Employee social security enrollment incomplete', 0, NULL, '2026-03-18 16:46:06'),
(35, 5, NULL, 'Workplace Protection', 'medium', 'Missing Safety Certification', 'Employee has not completed mandatory safety training', 0, NULL, '2026-03-18 16:46:06'),
(36, 6, NULL, 'Employment Compliance', 'low', 'Late Performance Review', 'Employee pending performance evaluation', 0, NULL, '2026-03-18 16:46:06'),
(37, 7, NULL, 'Leave Law Compliance', 'high', 'Sick Leave Abuse', 'Employee exceeded allocated sick leave without proper documentation', 0, NULL, '2026-03-18 16:46:06'),
(38, 8, NULL, 'Data Privacy Compliance', 'medium', 'Expired Data Access Consent', 'Employee access consent expired', 0, NULL, '2026-03-18 16:46:06'),
(39, 9, NULL, 'Benefits Compliance', 'critical', 'Pending Government Benefits', 'Employee benefits contributions pending', 0, NULL, '2026-03-18 16:46:06'),
(40, 10, NULL, 'Workplace Protection', 'high', 'Incident Report Pending', 'Workplace incident report for employee not submitted', 0, NULL, '2026-03-18 16:46:06'),
(41, 11, NULL, 'Employment Compliance', 'medium', 'Contract Renewal Due', 'Employee contract is nearing expiration', 0, NULL, '2026-03-18 16:46:06'),
(42, 12, NULL, 'Leave Law Compliance', 'high', 'Maternity/Paternity Leave Eligibility', 'Employee may not meet leave eligibility criteria', 0, NULL, '2026-03-18 16:46:06'),
(43, 13, NULL, 'Data Privacy Compliance', 'high', 'Unauthorized System Access', 'Employee attempted access to restricted IT systems', 0, NULL, '2026-03-18 16:46:06'),
(44, 14, NULL, 'Benefits Compliance', 'medium', 'Incomplete PhilHealth Enrollment', 'Employee PhilHealth enrollment is incomplete', 0, NULL, '2026-03-18 16:46:06'),
(45, 15, NULL, 'Workplace Protection', 'medium', 'Policy Acknowledgment Pending', 'Employee has not acknowledged latest workplace policies', 0, NULL, '2026-03-18 16:46:06'),
(46, 16, NULL, 'Employment Compliance', 'high', 'Probationary Period Exceeded', 'Employee exceeded probationary period without review', 0, NULL, '2026-03-18 16:46:06'),
(47, 17, NULL, 'Workplace Protection', 'critical', 'Safety Violation', 'Employee did not follow mandatory safety procedures', 0, NULL, '2026-03-18 16:46:06');

-- --------------------------------------------------------

--
-- Table structure for table `salary_structures`
--

CREATE TABLE `salary_structures` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `basic_salary` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `salary_structures`
--

INSERT INTO `salary_structures` (`id`, `name`, `basic_salary`) VALUES
(1, 'Instructor Level 1', 30000.00),
(2, 'Professor Level 2', 40000.00),
(3, 'Registrar Level 1', 22000.00),
(4, 'Cashier Contractual', 18000.00),
(5, 'Janitor Level 1', 15000.00);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_name` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_name`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'company_name', 'BESTLINK HR', '2026-02-11 13:55:05', '2026-02-11 13:55:05'),
(2, 'timezone', 'Asia/Manila', '2026-02-11 13:55:05', '2026-02-11 13:55:05'),
(3, 'date_format', 'Y-m-d', '2026-02-11 13:55:05', '2026-02-11 13:55:05'),
(4, 'email_notifications', '1', '2026-02-11 13:55:05', '2026-02-11 13:55:05'),
(5, 'leave_request_alerts', '1', '2026-02-11 13:55:05', '2026-02-11 13:55:05');

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` int(11) NOT NULL,
  `shift_name` varchar(50) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `skill_name` varchar(100) DEFAULT NULL,
  `proficiency_level` enum('Beginner','Intermediate','Advanced','Expert') DEFAULT 'Intermediate',
  `years_of_experience` decimal(3,1) DEFAULT 0.0,
  `certification` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `statutory_contributions`
--

CREATE TABLE `statutory_contributions` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `employee_share` decimal(10,2) DEFAULT NULL,
  `employer_share` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `statutory_contributions`
--

INSERT INTO `statutory_contributions` (`id`, `name`, `employee_share`, `employer_share`) VALUES
(1, 'SSS', 1125.00, 2000.00),
(2, 'PhilHealth', 450.00, 900.00),
(3, 'Pag-IBIG', 200.00, 200.00);

-- --------------------------------------------------------

--
-- Table structure for table `tax_tables`
--

CREATE TABLE `tax_tables` (
  `id` int(11) NOT NULL,
  `min_income` decimal(10,2) DEFAULT NULL,
  `max_income` decimal(10,2) DEFAULT NULL,
  `tax_rate` decimal(5,2) DEFAULT NULL,
  `fixed_tax` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `theme` enum('light','dark') DEFAULT 'light',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `theme`, `created_at`) VALUES
(1, 'admin', '$2y$10$GF34eDR6uEqpxNIovwKmRu2A6u3ALXgmMkn8zBdoREYLb1Em0euAK', 'Administrator', 'admin', 'light', '2026-01-28 15:21:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `allowances`
--
ALTER TABLE `allowances`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_module` (`module`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `compliance_categories`
--
ALTER TABLE `compliance_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `compliance_checks`
--
ALTER TABLE `compliance_checks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_law_type` (`law_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `compliance_items`
--
ALTER TABLE `compliance_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_department` (`department`),
  ADD KEY `idx_due_date` (`due_date`);

--
-- Indexes for table `compliance_logs`
--
ALTER TABLE `compliance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `compliance_records`
--
ALTER TABLE `compliance_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_law_type` (`law_type`),
  ADD KEY `idx_status` (`compliance_status`);

--
-- Indexes for table `compliance_reports`
--
ALTER TABLE `compliance_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_report_type` (`report_type`),
  ADD KEY `idx_generated_by` (`generated_by`);

--
-- Indexes for table `compliance_results`
--
ALTER TABLE `compliance_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee` (`employee_id`),
  ADD KEY `idx_rule` (`rule_id`),
  ADD KEY `idx_result` (`result`);

--
-- Indexes for table `compliance_rules`
--
ALTER TABLE `compliance_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `compliance_summary`
--
ALTER TABLE `compliance_summary`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD KEY `idx_employee` (`employee_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `compliance_tasks`
--
ALTER TABLE `compliance_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_compliance` (`compliance_id`),
  ADD KEY `idx_assigned` (`assigned_to`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_deadline` (`deadline`);

--
-- Indexes for table `data_privacy_consents`
--
ALTER TABLE `data_privacy_consents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_id` (`employee_id`);

--
-- Indexes for table `deductions`
--
ALTER TABLE `deductions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department_roles`
--
ALTER TABLE `department_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `department_name` (`department_name`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_adjustments`
--
ALTER TABLE `employee_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `payroll_period_id` (`payroll_period_id`);

--
-- Indexes for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `allowance_id` (`allowance_id`);

--
-- Indexes for table `employee_categories`
--
ALTER TABLE `employee_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `deduction_id` (`deduction_id`);

--
-- Indexes for table `employee_leave_eligibility`
--
ALTER TABLE `employee_leave_eligibility`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee` (`employee_id`),
  ADD KEY `idx_leave_type` (`leave_type`),
  ADD KEY `idx_status` (`eligibility_status`);

--
-- Indexes for table `employee_salary`
--
ALTER TABLE `employee_salary`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `salary_structure_id` (`salary_structure_id`);

--
-- Indexes for table `employee_shifts`
--
ALTER TABLE `employee_shifts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `shift_id` (`shift_id`);

--
-- Indexes for table `employment_details`
--
ALTER TABLE `employment_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`);

--
-- Indexes for table `employment_history`
--
ALTER TABLE `employment_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `position_id` (`position_id`);

--
-- Indexes for table `employment_types`
--
ALTER TABLE `employment_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `government_contributions`
--
ALTER TABLE `government_contributions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_type` (`contribution_type`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `incidents`
--
ALTER TABLE `incidents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `incident_id` (`incident_id`),
  ADD KEY `idx_reporter_id` (`reporter_id`),
  ADD KEY `idx_type` (`incident_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_incidents_status` (`status`),
  ADD KEY `idx_incidents_severity` (`severity`),
  ADD KEY `idx_incidents_incident_id` (`incident_id`),
  ADD KEY `idx_incidents_assigned_hr` (`assigned_hr_id`),
  ADD KEY `idx_incidents_sla_deadline` (`sla_deadline`);

--
-- Indexes for table `incident_audit_log`
--
ALTER TABLE `incident_audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_incident` (`incident_id`),
  ADD KEY `idx_audit_created` (`created_at`);

--
-- Indexes for table `incident_evidence`
--
ALTER TABLE `incident_evidence`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_incident_id` (`incident_id`);

--
-- Indexes for table `incident_memos`
--
ALTER TABLE `incident_memos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `incident_memo_delivery`
--
ALTER TABLE `incident_memo_delivery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `incident_notes`
--
ALTER TABLE `incident_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_incident_id` (`incident_id`);

--
-- Indexes for table `incident_notifications`
--
ALTER TABLE `incident_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_incident` (`incident_id`),
  ADD KEY `idx_notif_user` (`user_id`),
  ADD KEY `idx_notif_read` (`is_read`);

--
-- Indexes for table `leave_approvals`
--
ALTER TABLE `leave_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_leave` (`leave_application_id`),
  ADD KEY `idx_approver` (`approver_type`,`approver_id`);

--
-- Indexes for table `leave_attachments`
--
ALTER TABLE `leave_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_leave_app` (`leave_application_id`);

--
-- Indexes for table `leave_balances`
--
ALTER TABLE `leave_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_employee_leave_year` (`employee_id`,`leave_type`,`year`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_year` (`year`);

--
-- Indexes for table `leave_documents`
--
ALTER TABLE `leave_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_history`
--
ALTER TABLE `leave_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_type_id` (`leave_type_id`),
  ADD KEY `leave_application_id` (`leave_application_id`),
  ADD KEY `idx_employee` (`employee_id`),
  ADD KEY `idx_action` (`action_type`),
  ADD KEY `idx_performed_at` (`performed_at`);

--
-- Indexes for table `leave_notifications`
--
ALTER TABLE `leave_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_application_id` (`leave_application_id`),
  ADD KEY `idx_employee` (`employee_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_type` (`notification_type`);

--
-- Indexes for table `leave_payroll_integrations`
--
ALTER TABLE `leave_payroll_integrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_application_id` (`leave_application_id`),
  ADD KEY `idx_payroll_run` (`payroll_run_id`),
  ADD KEY `idx_employee` (`employee_id`);

--
-- Indexes for table `leave_policy_configurations`
--
ALTER TABLE `leave_policy_configurations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_leave_type` (`leave_type_id`),
  ADD KEY `idx_category` (`category_id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_leave_type` (`leave_type`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `overtime`
--
ALTER TABLE `overtime`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `part_time_hours`
--
ALTER TABLE `part_time_hours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `payroll_period_id` (`payroll_period_id`);

--
-- Indexes for table `payroll_periods`
--
ALTER TABLE `payroll_periods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll_runs`
--
ALTER TABLE `payroll_runs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_period_id` (`payroll_period_id`);

--
-- Indexes for table `payslips`
--
ALTER TABLE `payslips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_run_id` (`payroll_run_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `payslip_items`
--
ALTER TABLE `payslip_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payslip_id` (`payslip_id`);

--
-- Indexes for table `philippine_laws`
--
ALTER TABLE `philippine_laws`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `policies`
--
ALTER TABLE `policies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_approved_by` (`approved_by`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_published_at` (`published_at`),
  ADD KEY `idx_is_mandatory` (`is_mandatory`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `policy_acknowledgments`
--
ALTER TABLE `policy_acknowledgments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_employee_policy` (`employee_id`,`policy_id`),
  ADD UNIQUE KEY `unique_acknowledgment` (`employee_id`,`policy_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_policy_id` (`policy_id`),
  ADD KEY `idx_acknowledged_at` (`acknowledged_at`);

--
-- Indexes for table `policy_approvals`
--
ALTER TABLE `policy_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_policy_id` (`policy_id`),
  ADD KEY `idx_approved_by` (`approved_by`),
  ADD KEY `idx_action` (`action`);

--
-- Indexes for table `policy_categories`
--
ALTER TABLE `policy_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `policy_notifications`
--
ALTER TABLE `policy_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_policy_id` (`policy_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_notification_type` (`notification_type`);

--
-- Indexes for table `policy_versions`
--
ALTER TABLE `policy_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_policy_id` (`policy_id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `report_notes`
--
ALTER TABLE `report_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_period_id` (`payroll_period_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `risks`
--
ALTER TABLE `risks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_severity` (`severity`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `risk_alerts`
--
ALTER TABLE `risk_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_risk_id` (`risk_id`),
  ADD KEY `idx_is_read` (`is_read`);

--
-- Indexes for table `risk_flags`
--
ALTER TABLE `risk_flags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rule_id` (`rule_id`),
  ADD KEY `idx_employee` (`employee_id`),
  ADD KEY `idx_severity` (`severity`),
  ADD KEY `idx_is_resolved` (`is_resolved`);

--
-- Indexes for table `salary_structures`
--
ALTER TABLE `salary_structures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_name` (`setting_name`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `statutory_contributions`
--
ALTER TABLE `statutory_contributions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tax_tables`
--
ALTER TABLE `tax_tables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `allowances`
--
ALTER TABLE `allowances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `compliance_categories`
--
ALTER TABLE `compliance_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `compliance_checks`
--
ALTER TABLE `compliance_checks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `compliance_items`
--
ALTER TABLE `compliance_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `compliance_logs`
--
ALTER TABLE `compliance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `compliance_records`
--
ALTER TABLE `compliance_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `compliance_reports`
--
ALTER TABLE `compliance_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `compliance_results`
--
ALTER TABLE `compliance_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `compliance_rules`
--
ALTER TABLE `compliance_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `compliance_summary`
--
ALTER TABLE `compliance_summary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `compliance_tasks`
--
ALTER TABLE `compliance_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `data_privacy_consents`
--
ALTER TABLE `data_privacy_consents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deductions`
--
ALTER TABLE `deductions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `department_roles`
--
ALTER TABLE `department_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=177;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `employee_adjustments`
--
ALTER TABLE `employee_adjustments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employee_categories`
--
ALTER TABLE `employee_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `employee_leave_eligibility`
--
ALTER TABLE `employee_leave_eligibility`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_salary`
--
ALTER TABLE `employee_salary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `employee_shifts`
--
ALTER TABLE `employee_shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employment_history`
--
ALTER TABLE `employment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employment_types`
--
ALTER TABLE `employment_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `government_contributions`
--
ALTER TABLE `government_contributions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `incident_audit_log`
--
ALTER TABLE `incident_audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `incident_evidence`
--
ALTER TABLE `incident_evidence`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incident_memos`
--
ALTER TABLE `incident_memos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `incident_memo_delivery`
--
ALTER TABLE `incident_memo_delivery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `incident_notes`
--
ALTER TABLE `incident_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incident_notifications`
--
ALTER TABLE `incident_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_approvals`
--
ALTER TABLE `leave_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_attachments`
--
ALTER TABLE `leave_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_balances`
--
ALTER TABLE `leave_balances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_documents`
--
ALTER TABLE `leave_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `leave_history`
--
ALTER TABLE `leave_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_notifications`
--
ALTER TABLE `leave_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_payroll_integrations`
--
ALTER TABLE `leave_payroll_integrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_policy_configurations`
--
ALTER TABLE `leave_policy_configurations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `overtime`
--
ALTER TABLE `overtime`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `part_time_hours`
--
ALTER TABLE `part_time_hours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payroll_periods`
--
ALTER TABLE `payroll_periods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `payroll_runs`
--
ALTER TABLE `payroll_runs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `payslips`
--
ALTER TABLE `payslips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `payslip_items`
--
ALTER TABLE `payslip_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `philippine_laws`
--
ALTER TABLE `philippine_laws`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `policies`
--
ALTER TABLE `policies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `policy_acknowledgments`
--
ALTER TABLE `policy_acknowledgments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `policy_approvals`
--
ALTER TABLE `policy_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `policy_categories`
--
ALTER TABLE `policy_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `policy_notifications`
--
ALTER TABLE `policy_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `policy_versions`
--
ALTER TABLE `policy_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `report_notes`
--
ALTER TABLE `report_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `risks`
--
ALTER TABLE `risks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `risk_alerts`
--
ALTER TABLE `risk_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `risk_flags`
--
ALTER TABLE `risk_flags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `salary_structures`
--
ALTER TABLE `salary_structures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `statutory_contributions`
--
ALTER TABLE `statutory_contributions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tax_tables`
--
ALTER TABLE `tax_tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `compliance_checks`
--
ALTER TABLE `compliance_checks`
  ADD CONSTRAINT `compliance_checks_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `compliance_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `compliance_results`
--
ALTER TABLE `compliance_results`
  ADD CONSTRAINT `compliance_results_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `compliance_results_ibfk_2` FOREIGN KEY (`rule_id`) REFERENCES `compliance_rules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `compliance_rules`
--
ALTER TABLE `compliance_rules`
  ADD CONSTRAINT `compliance_rules_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `compliance_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `compliance_summary`
--
ALTER TABLE `compliance_summary`
  ADD CONSTRAINT `compliance_summary_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `compliance_tasks`
--
ALTER TABLE `compliance_tasks`
  ADD CONSTRAINT `compliance_tasks_ibfk_1` FOREIGN KEY (`compliance_id`) REFERENCES `compliance_items` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_adjustments`
--
ALTER TABLE `employee_adjustments`
  ADD CONSTRAINT `employee_adjustments_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `employee_adjustments_ibfk_2` FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`);

--
-- Constraints for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  ADD CONSTRAINT `employee_allowances_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `employee_allowances_ibfk_2` FOREIGN KEY (`allowance_id`) REFERENCES `allowances` (`id`);

--
-- Constraints for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  ADD CONSTRAINT `employee_deductions_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `employee_deductions_ibfk_2` FOREIGN KEY (`deduction_id`) REFERENCES `deductions` (`id`);

--
-- Constraints for table `employee_salary`
--
ALTER TABLE `employee_salary`
  ADD CONSTRAINT `employee_salary_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `employee_salary_ibfk_2` FOREIGN KEY (`salary_structure_id`) REFERENCES `salary_structures` (`id`);

--
-- Constraints for table `employee_shifts`
--
ALTER TABLE `employee_shifts`
  ADD CONSTRAINT `employee_shifts_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `employee_shifts_ibfk_2` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`);

--
-- Constraints for table `employment_history`
--
ALTER TABLE `employment_history`
  ADD CONSTRAINT `employment_history_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `employment_history_ibfk_2` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`);

--
-- Constraints for table `leave_approvals`
--
ALTER TABLE `leave_approvals`
  ADD CONSTRAINT `leave_approvals_ibfk_1` FOREIGN KEY (`leave_application_id`) REFERENCES `leave_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_attachments`
--
ALTER TABLE `leave_attachments`
  ADD CONSTRAINT `leave_attachments_ibfk_1` FOREIGN KEY (`leave_application_id`) REFERENCES `leave_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_history`
--
ALTER TABLE `leave_history`
  ADD CONSTRAINT `leave_history_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_history_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_history_ibfk_3` FOREIGN KEY (`leave_application_id`) REFERENCES `leave_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_notifications`
--
ALTER TABLE `leave_notifications`
  ADD CONSTRAINT `leave_notifications_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_notifications_ibfk_2` FOREIGN KEY (`leave_application_id`) REFERENCES `leave_requests` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `leave_payroll_integrations`
--
ALTER TABLE `leave_payroll_integrations`
  ADD CONSTRAINT `leave_payroll_integrations_ibfk_1` FOREIGN KEY (`leave_application_id`) REFERENCES `leave_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_payroll_integrations_ibfk_2` FOREIGN KEY (`payroll_run_id`) REFERENCES `payroll_runs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `leave_policy_configurations`
--
ALTER TABLE `leave_policy_configurations`
  ADD CONSTRAINT `leave_policy_configurations_ibfk_1` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `overtime`
--
ALTER TABLE `overtime`
  ADD CONSTRAINT `overtime_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `part_time_hours`
--
ALTER TABLE `part_time_hours`
  ADD CONSTRAINT `part_time_hours_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `part_time_hours_ibfk_2` FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`);

--
-- Constraints for table `payroll_runs`
--
ALTER TABLE `payroll_runs`
  ADD CONSTRAINT `payroll_runs_ibfk_1` FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`);

--
-- Constraints for table `payslips`
--
ALTER TABLE `payslips`
  ADD CONSTRAINT `payslips_ibfk_1` FOREIGN KEY (`payroll_run_id`) REFERENCES `payroll_runs` (`id`),
  ADD CONSTRAINT `payslips_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `payslip_items`
--
ALTER TABLE `payslip_items`
  ADD CONSTRAINT `payslip_items_ibfk_1` FOREIGN KEY (`payslip_id`) REFERENCES `payslips` (`id`);

--
-- Constraints for table `policies`
--
ALTER TABLE `policies`
  ADD CONSTRAINT `policies_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `policy_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `policy_acknowledgments`
--
ALTER TABLE `policy_acknowledgments`
  ADD CONSTRAINT `policy_acknowledgments_ibfk_1` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `policy_approvals`
--
ALTER TABLE `policy_approvals`
  ADD CONSTRAINT `policy_approvals_ibfk_1` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `policy_notifications`
--
ALTER TABLE `policy_notifications`
  ADD CONSTRAINT `policy_notifications_ibfk_1` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `policy_versions`
--
ALTER TABLE `policy_versions`
  ADD CONSTRAINT `policy_versions_ibfk_1` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `positions_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `employee_categories` (`id`);

--
-- Constraints for table `report_notes`
--
ALTER TABLE `report_notes`
  ADD CONSTRAINT `report_notes_ibfk_1` FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`),
  ADD CONSTRAINT `report_notes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `risk_flags`
--
ALTER TABLE `risk_flags`
  ADD CONSTRAINT `risk_flags_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `risk_flags_ibfk_2` FOREIGN KEY (`rule_id`) REFERENCES `compliance_rules` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
