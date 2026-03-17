-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 17, 2026 at 11:22 PM
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
-- Database: `hr_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `career_paths`
--

CREATE TABLE `career_paths` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` longtext DEFAULT NULL,
  `target_position` varchar(100) DEFAULT NULL,
  `prerequisites` varchar(255) DEFAULT NULL,
  `skills_required` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`skills_required`)),
  `duration_months` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cover_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `career_paths`
--

INSERT INTO `career_paths` (`id`, `name`, `description`, `target_position`, `prerequisites`, `skills_required`, `duration_months`, `status`, `created_by`, `created_at`, `updated_at`, `cover_photo`) VALUES
(61, 'Senior Manager Track', 'Path to becoming a senior manager with focus on leadership and strategic thinking', 'Senior Manager', '3+ years management experience', '[\"Strategic Planning\", \"Leadership\", \"Budget Management\"]', 18, 'active', NULL, '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-1.gif'),
(62, 'Technical Expert Track', 'Career progression for technical professionals aiming for specialist roles', 'Technical Specialist', '5+ years technical experience', '[\"Deep Technical Knowledge\", \"Problem Solving\", \"Mentoring\"]', 24, 'active', NULL, '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-2.gif'),
(63, 'Project Manager Track', 'Dedicated path for aspiring and growing project managers', 'Senior Project Manager', '2+ years project coordination', '[\"Project Management\", \"Team Leadership\", \"Stakeholder Management\"]', 12, 'active', NULL, '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-3.gif'),
(64, 'Sales Executive Track', 'Career development for sales professionals targeting executive positions', 'Sales Director', '4+ years sales experience', '[\"Sales Strategy\", \"Leadership\", \"Client Relations\"]', 20, 'active', NULL, '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-4.gif'),
(65, 'Human Resources Specialist', 'HR career development focusing on talent management and organizational development', 'Senior HR Manager', '2+ years HR experience', '[\"Talent Management\", \"Compensation\", \"Employee Relations\"]', 15, 'active', NULL, '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-5.gif'),
(66, 'Financial Analyst Track', 'Career path for finance professionals aiming for senior analyst positions', 'Senior Financial Analyst', '3+ years accounting experience', '[\"Financial Analysis\", \"Reporting\", \"Risk Assessment\"]', 16, 'active', NULL, '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-6.gif'),
(67, 'Data Scientist Career Path', 'Career progression in data science and advanced analytics', 'Lead Data Scientist', '2+ years data analysis experience', '[\"Machine Learning\", \"Data Visualization\", \"Programming\"]', 18, 'active', NULL, '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-7.gif'),
(68, 'Cloud Architect Track', 'Path to becoming a cloud infrastructure architect', 'Cloud Solutions Architect', '4+ years cloud experience', '[\"Cloud Architecture\", \"System Design\", \"Security\"]', 20, 'active', NULL, '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-8.gif'),
(69, 'Business Analyst Track', 'Career development for business analysis and consulting roles', 'Senior Business Analyst', '2+ years BA experience', '[\"Requirements Analysis\", \"Process Improvement\", \"Communication\"]', 14, 'active', NULL, '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-9.gif'),
(70, 'Quality Assurance Lead', 'Leadership track for QA and quality management professionals', 'QA Manager', '3+ years QA experience', '[\"Quality Management\", \"Team Leadership\", \"Process Improvement\"]', 15, 'active', NULL, '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-10.gif'),
(71, 'Operations Manager Track', 'Career development for operations professionals', 'Operations Director', '3+ years operations experience', '[\"Process Optimization\", \"Cost Control\", \"Team Management\"]', 16, 'active', NULL, '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-11.gif'),
(72, 'Marketing Specialist Track', 'Career progression in marketing and brand management', 'Marketing Manager', '3+ years marketing experience', '[\"Campaign Management\", \"Brand Strategy\", \"Analytics\"]', 14, 'active', NULL, '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-12.gif'),
(73, 'Compliance Officer Track', 'Specialized track for compliance and regulatory professionals', 'Senior Compliance Officer', '2+ years compliance experience', '[\"Regulatory Knowledge\", \"Risk Management\", \"Audit\"]', 12, 'active', NULL, '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-13.gif'),
(74, 'Software Architect Track', 'Path for senior software engineers to become architects', 'Software Architect', '6+ years development experience', '[\"System Design\", \"Architecture Patterns\", \"Technical Leadership\"]', 18, 'active', NULL, '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-14.gif'),
(75, 'Customer Success Manager', 'Career development for customer-facing professionals', 'Customer Success Director', '2+ years customer service experience', '[\"Account Management\", \"Customer Relations\", \"Problem Solving\"]', 13, 'active', NULL, '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-15.gif');

-- --------------------------------------------------------

--
-- Table structure for table `competencies`
--

CREATE TABLE `competencies` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` longtext DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `proficiency_levels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`proficiency_levels`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `competencies`
--

INSERT INTO `competencies` (`id`, `name`, `description`, `category`, `proficiency_levels`, `created_at`) VALUES
(17, 'Leadership', 'Ability to lead teams and make strategic decisions', 'Management', '[\"Beginner\", \"Intermediate\", \"Advanced\", \"Expert\"]', '2026-03-17 21:25:08'),
(18, 'Communication', 'Effective written and verbal communication', 'Soft Skills', '[\"Beginner\", \"Intermediate\", \"Advanced\", \"Fluent\"]', '2026-03-17 21:25:08'),
(19, 'Technical Expertise', 'Deep technical knowledge and problem-solving', 'Technical', '[\"Beginner\", \"Intermediate\", \"Advanced\", \"Master\"]', '2026-03-17 21:25:08'),
(20, 'Project Management', 'Planning, execution and delivery of projects', 'Management', '[\"Beginner\", \"Intermediate\", \"Advanced\", \"Expert\"]', '2026-03-17 21:25:08'),
(21, 'Problem Solving', 'Ability to analyze and resolve complex issues', 'Core', '[\"Basic\", \"Intermediate\", \"Advanced\", \"Expert\"]', '2026-03-17 21:25:08'),
(22, 'Customer Relations', 'Building and maintaining customer relationships', 'Soft Skills', '[\"Developing\", \"Proficient\", \"Excellent\", \"Exceptional\"]', '2026-03-17 21:25:08'),
(23, 'Financial Analysis', 'Analyzing financial data and metrics', 'Technical', '[\"Basic\", \"Intermediate\", \"Advanced\", \"Expert\"]', '2026-03-17 21:25:08'),
(24, 'Strategic Planning', 'Long-term planning and strategy development', 'Management', '[\"Beginner\", \"Intermediate\", \"Advanced\", \"Expert\"]', '2026-03-17 21:25:08');

-- --------------------------------------------------------

--
-- Table structure for table `compliance_assignments`
--

CREATE TABLE `compliance_assignments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `compliance_training_id` int(11) NOT NULL,
  `assigned_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `due_date` date NOT NULL,
  `completion_date` timestamp NULL DEFAULT NULL,
  `status` enum('assigned','in_progress','completed','overdue') DEFAULT 'assigned',
  `acknowledgment_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `compliance_assignments`
--

INSERT INTO `compliance_assignments` (`id`, `user_id`, `compliance_training_id`, `assigned_date`, `due_date`, `completion_date`, `status`, `acknowledgment_date`) VALUES
(1, 1, 1, '2026-03-17 21:25:09', '2026-06-15', NULL, 'assigned', NULL),
(2, 1, 2, '2026-03-17 21:25:09', '2026-06-15', NULL, 'assigned', NULL),
(3, 1, 3, '2026-03-17 21:25:09', '2026-09-13', NULL, 'assigned', NULL),
(4, 1, 5, '2026-03-17 21:25:09', '2026-06-15', '2026-03-07 22:25:09', 'completed', NULL),
(5, 2, 1, '2026-03-17 21:25:09', '2026-06-15', '2026-01-26 22:25:09', 'completed', NULL),
(6, 2, 2, '2026-03-17 21:25:09', '2026-06-15', NULL, 'in_progress', NULL),
(7, 2, 4, '2026-03-17 21:25:09', '2026-06-15', NULL, 'assigned', NULL),
(8, 2, 5, '2026-03-17 21:25:09', '2026-06-15', '2026-03-12 21:25:09', 'completed', NULL),
(9, 3, 1, '2026-03-17 21:25:09', '2026-06-15', '2026-01-31 22:25:09', 'completed', NULL),
(10, 3, 2, '2026-03-17 21:25:09', '2026-06-15', '2026-02-15 22:25:09', 'completed', NULL),
(11, 3, 5, '2026-03-17 21:25:09', '2026-06-15', NULL, 'assigned', NULL),
(12, 4, 1, '2026-03-17 21:25:09', '2026-06-15', NULL, 'assigned', NULL),
(13, 4, 2, '2026-03-17 21:25:09', '2026-06-15', NULL, 'assigned', NULL),
(14, 4, 5, '2026-03-17 21:25:09', '2026-06-15', NULL, 'assigned', NULL),
(15, 5, 1, '2026-03-17 21:25:09', '2026-06-15', NULL, 'in_progress', NULL),
(16, 5, 3, '2026-03-17 21:25:09', '2026-09-13', NULL, 'assigned', NULL),
(17, 5, 5, '2026-03-17 21:25:09', '2026-06-15', NULL, 'assigned', NULL),
(18, 6, 1, '2026-03-17 21:25:09', '2026-06-15', NULL, 'assigned', NULL),
(19, 6, 2, '2026-03-17 21:25:09', '2026-06-15', NULL, 'assigned', NULL),
(20, 6, 4, '2026-03-17 21:25:09', '2026-06-15', NULL, 'assigned', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `compliance_trainings`
--

CREATE TABLE `compliance_trainings` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` longtext DEFAULT NULL,
  `compliance_type` varchar(100) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `frequency` varchar(50) DEFAULT NULL,
  `mandatory` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cover_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `compliance_trainings`
--

INSERT INTO `compliance_trainings` (`id`, `title`, `description`, `compliance_type`, `due_date`, `frequency`, `mandatory`, `created_by`, `created_at`, `cover_photo`) VALUES
(9, 'Annual Safety Training', 'Mandatory safety protocols and emergency procedures', 'Health & Safety', NULL, 'Yearly', 1, NULL, '2026-03-17 21:25:09', NULL),
(10, 'Data Privacy & GDPR Compliance', 'Understanding data protection and privacy regulations', 'Data Privacy', NULL, 'Yearly', 1, NULL, '2026-03-17 21:25:09', NULL),
(11, 'Anti-Harassment & Discrimination Policy', 'Workplace conduct and anti-harassment training', 'HR Compliance', NULL, 'Every 2 Years', 1, NULL, '2026-03-17 21:25:09', NULL),
(12, 'Information Security Awareness', 'Protecting company and customer information', 'Cybersecurity', NULL, 'Yearly', 1, NULL, '2026-03-17 21:25:09', NULL),
(13, 'Code of Conduct Training', 'Company policies and ethical standards', 'Business Ethics', NULL, 'Yearly', 1, NULL, '2026-03-17 21:25:09', NULL),
(14, 'Export Control & Trade Compliance', 'Regulations for international trade and exports', 'Legal Compliance', NULL, 'Yearly', 0, NULL, '2026-03-17 21:25:09', NULL),
(15, 'Environmental Compliance', 'Environmental laws and sustainability practices', 'Environmental', NULL, 'Every 2 Years', 0, NULL, '2026-03-17 21:25:09', NULL),
(16, 'Quality & ISO Standards', 'ISO standards and quality management', 'Quality', NULL, 'Yearly', 0, NULL, '2026-03-17 21:25:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` varchar(50) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `employment_status` varchar(50) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `full_name`, `address`, `contact_number`, `email`, `department`, `position`, `date_hired`, `employment_status`, `created_at`, `updated_at`) VALUES
('EMP001', 'John Doe', '123 Main St', '123-456-7890', 'john.doe@example.com', 'IT', 'Software Engineer', '2023-01-01', 'Active', '2026-03-16 18:02:12', '2026-03-16 18:02:12'),
('EMP002', 'Jane Smith', '456 Oak Ave', '098-765-4321', 'jane.smith@example.com', 'HR', 'HR Manager', '2023-02-15', 'Active', '2026-03-16 18:02:12', '2026-03-16 18:02:12'),
('EMP003', 'Mike Johnson', '789 Pine Rd', '555-123-4567', 'mike.johnson@example.com', 'Finance', 'Accountant', '2023-03-10', 'Active', '2026-03-16 18:02:12', '2026-03-16 18:02:12');

-- --------------------------------------------------------

--
-- Table structure for table `employee_settlements`
--

CREATE TABLE `employee_settlements` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `resignation_id` int(11) DEFAULT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `hra` decimal(10,2) DEFAULT 0.00,
  `conveyance` decimal(10,2) DEFAULT 0.00,
  `lta` decimal(10,2) DEFAULT 0.00,
  `medical_allowance` decimal(10,2) DEFAULT 0.00,
  `other_allowances` decimal(10,2) DEFAULT 0.00,
  `provident_fund` decimal(10,2) DEFAULT 0.00,
  `gratuity` decimal(10,2) DEFAULT 0.00,
  `notice_pay` decimal(10,2) DEFAULT 0.00,
  `outstanding_loans` decimal(10,2) DEFAULT 0.00,
  `other_deductions` decimal(10,2) DEFAULT 0.00,
  `net_payable` decimal(10,2) NOT NULL,
  `settlement_date` date NOT NULL,
  `status` enum('draft','approved','paid') DEFAULT 'draft',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_settlements`
--

INSERT INTO `employee_settlements` (`id`, `employee_id`, `resignation_id`, `basic_salary`, `hra`, `conveyance`, `lta`, `medical_allowance`, `other_allowances`, `provident_fund`, `gratuity`, `notice_pay`, `outstanding_loans`, `other_deductions`, `net_payable`, `settlement_date`, `status`, `approved_by`, `approved_at`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'EMP002', 8, 123.00, 12.00, 12.00, 3.00, 3.00, 3.00, 5.00, 0.00, 5.00, 111.00, 55.00, -20.00, '2026-03-18', 'draft', NULL, NULL, NULL, '2026-03-17 09:04:17', '2026-03-17 09:04:17'),
(2, 'EMP001', 5, 321.00, 1.00, 42342.00, 13.00, 43.00, 453534.00, 3454.00, 5435.00, 435.00, 435.00, 435.00, 486060.00, '2026-03-20', 'draft', NULL, NULL, 10, '2026-03-17 12:55:11', '2026-03-17 12:55:11');

-- --------------------------------------------------------

--
-- Table structure for table `exit_documents`
--

CREATE TABLE `exit_documents` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `document_type` enum('resignation_letter','clearance_form','handover_document','certificate','other') NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `status` enum('active','archived') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exit_documents`
--

INSERT INTO `exit_documents` (`id`, `employee_id`, `document_type`, `title`, `file_path`, `uploaded_by`, `status`, `created_at`) VALUES
(1, 'EMP002', '', 'sdadasd', 'uploads/documents/1773752497_sir mark docu.docx', 10, 'active', '2026-03-17 13:01:37'),
(2, '1', 'clearance_form', 'dasdasd', 'uploads/documents/1773755616_sir mark docu.docx', 10, 'active', '2026-03-17 13:53:36');

-- --------------------------------------------------------

--
-- Table structure for table `exit_interviews`
--

CREATE TABLE `exit_interviews` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `interviewer_id` int(11) DEFAULT NULL,
  `scheduled_date` date NOT NULL,
  `scheduled_time` time NOT NULL,
  `location` varchar(255) DEFAULT 'Virtual',
  `notes` text DEFAULT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exit_interviews`
--

INSERT INTO `exit_interviews` (`id`, `employee_id`, `interviewer_id`, `scheduled_date`, `scheduled_time`, `location`, `notes`, `status`, `feedback`, `created_at`, `updated_at`) VALUES
(1, 'EMP002', 0, '2026-03-20', '03:23:00', 'Virtual', 'dsaa', 'scheduled', NULL, '2026-03-17 07:24:03', '2026-03-17 07:24:14'),
(2, 'EMP002', 0, '2026-03-11', '05:03:00', 'Virtual', '456456', 'scheduled', NULL, '2026-03-17 09:03:18', '2026-03-17 09:03:18'),
(3, 'EMP003', 0, '2026-03-11', '16:23:00', 'Virtual', 'jhtuoyikuioy', 'scheduled', NULL, '2026-03-17 09:21:28', '2026-03-17 09:21:28');

-- --------------------------------------------------------

--
-- Table structure for table `exit_surveys`
--

CREATE TABLE `exit_surveys` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `target_audience` enum('all','voluntary','involuntary') DEFAULT 'all',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','inactive','completed') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exit_surveys`
--

INSERT INTO `exit_surveys` (`id`, `title`, `description`, `target_audience`, `start_date`, `end_date`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'casedca', 'dcasdcas', 'voluntary', '2026-03-01', '2026-03-31', 'active', NULL, '2026-03-17 09:05:00', '2026-03-17 09:05:00'),
(2, 'casedca', 'dcasdcas', 'voluntary', '2026-03-01', '2026-03-31', 'active', NULL, '2026-03-17 09:05:23', '2026-03-17 09:05:23'),
(3, 'dsadsaadsda', 'adsdasadsadsasd', 'all', '2026-03-18', '2026-03-18', 'active', 10, '2026-03-17 14:01:22', '2026-03-17 14:01:22'),
(4, 'dasdasd', 'sadasdasd', 'all', '2026-03-25', '2026-03-25', 'active', 10, '2026-03-17 15:34:26', '2026-03-17 15:34:26');

-- --------------------------------------------------------

--
-- Table structure for table `feedback_360`
--

CREATE TABLE `feedback_360` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `reviewer_type` enum('manager','peer','subordinate','external') DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `comments` longtext DEFAULT NULL,
  `feedback_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedback_360`
--

INSERT INTO `feedback_360` (`id`, `employee_id`, `reviewer_id`, `reviewer_type`, `rating`, `comments`, `feedback_date`) VALUES
(29, 1, 2, 'manager', 4.60, 'Excellent strategic thinking and team leadership', '2026-01-16 22:25:08'),
(30, 1, 3, 'peer', 4.40, 'Great collaboration skills and always helpful', '2026-01-16 22:25:08'),
(31, 1, 4, 'subordinate', 4.70, 'Inspires and motivates the team effectively', '2026-01-16 22:25:08'),
(32, 2, 1, 'manager', 4.20, 'Strong technical abilities, needs to develop leadership', '2026-01-16 22:25:08'),
(33, 2, 4, 'peer', 4.30, 'Reliable team member with good technical expertise', '2026-01-16 22:25:08'),
(34, 2, 5, 'subordinate', 4.00, 'Provides good technical guidance', '2026-01-16 22:25:08'),
(35, 3, 2, 'manager', 4.80, 'Exceptional performer and natural leader', '2026-01-16 22:25:08'),
(36, 3, 1, 'peer', 4.70, 'Excellent mentor and supportive colleague', '2026-01-16 22:25:08'),
(37, 3, 5, 'subordinate', 4.90, 'Outstanding mentor who helped my growth significantly', '2026-01-16 22:25:08'),
(38, 4, 3, 'manager', 3.90, 'Competent but needs improvement in initiative', '2026-01-16 22:25:08'),
(39, 4, 1, 'peer', 4.10, 'Cooperative team member', '2026-01-16 22:25:08'),
(40, 4, 6, 'subordinate', 3.80, 'Adequate guidance provided', '2026-01-16 22:25:08'),
(41, 5, 2, 'manager', 4.50, 'Strong strategic capabilities and initiative', '2026-01-16 22:25:08'),
(42, 6, 1, 'manager', 4.20, 'Good performance with growth potential', '2026-01-16 22:25:08');

-- --------------------------------------------------------

--
-- Table structure for table `individual_development_plans`
--

CREATE TABLE `individual_development_plans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `career_path_id` int(11) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `objectives` longtext DEFAULT NULL,
  `milestones` longtext DEFAULT NULL,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `individual_development_plans`
--

INSERT INTO `individual_development_plans` (`id`, `user_id`, `career_path_id`, `start_date`, `end_date`, `objectives`, `milestones`, `status`, `created_at`, `updated_at`, `created_by`) VALUES
(65, 1, 1, '2026-01-31', '2027-01-26', 'Develop strategic leadership capabilities and team management skills', '[\"Complete leadership assessment\", \"Attend executive coaching\", \"Lead cross-functional project\"]', 'active', '2026-01-31 22:25:07', '2026-03-17 21:25:07', 1),
(66, 2, 2, '2026-02-15', '2028-02-05', 'Become technical expert in cloud architecture', '[\"Obtain cloud certification\", \"Mentor junior developers\", \"Complete 3 architecture projects\"]', 'active', '2026-02-15 22:25:07', '2026-03-17 21:25:07', 1),
(67, 3, 3, '2026-01-16', '2027-01-11', 'Master project management methodologies', '[\"PMP certification\", \"Manage 2 large projects\", \"Stakeholder training\"]', 'active', '2026-01-16 22:25:07', '2026-03-17 21:25:07', 1),
(68, 4, 1, '2026-02-25', '2027-08-19', 'Progress to senior management level', '[\"Leadership training\", \"Budget responsibility\", \"Team building initiatives\"]', 'active', '2026-02-25 22:25:07', '2026-03-17 21:25:07', 1),
(69, 5, 4, '2026-01-26', '2027-09-18', 'Develop sales executive capabilities', '[\"Sales strategy course\", \"Client portfolio growth\", \"Team leadership\"]', 'active', '2026-01-26 22:25:07', '2026-03-17 21:25:07', 1),
(70, 6, 5, '2025-12-17', '2026-03-16', 'Complete HR specialist development', '[\"SHRM certification\", \"Compensation expertise\", \"Employee relations mastery\"]', 'completed', '2025-12-17 22:25:07', '2026-03-17 21:25:07', 1),
(71, 1, 7, '2026-02-10', '2027-09-13', 'Transition to data science track', '[\"Machine learning courses\", \"Python mastery\", \"Data project completion\"]', 'active', '2026-02-10 22:25:07', '2026-03-17 21:25:07', 1),
(72, 2, 8, '2026-01-21', '2027-09-13', 'Develop cloud architecture expertise', '[\"AWS certification\", \"Azure training\", \"Architecture patterns\"]', 'active', '2026-01-21 22:25:07', '2026-03-17 21:25:07', 1),
(73, 3, 9, '2026-02-20', '2027-04-16', 'Master business analysis', '[\"Requirements training\", \"Process mapping\", \"Stakeholder analysis\"]', 'active', '2026-02-20 22:25:07', '2026-03-17 21:25:07', 1),
(74, 4, 10, '2026-03-12', '2027-06-05', 'Develop QA leadership', '[\"Quality frameworks\", \"Team management\", \"Process improvement\"]', 'active', '2026-03-12 21:25:07', '2026-03-17 21:25:07', 1),
(75, 5, 11, '2026-02-05', '2027-05-31', 'Build operations management expertise', '[\"Lean methodology\", \"Process automation\", \"Cost reduction initiatives\"]', 'active', '2026-02-05 22:25:07', '2026-03-17 21:25:07', 1),
(76, 6, 6, '2026-03-02', '2027-06-25', 'Advance in financial analysis', '[\"Financial modeling\", \"Treasury management\", \"Risk assessment\"]', 'active', '2026-03-02 22:25:07', '2026-03-17 21:25:07', 1),
(77, 1, 12, '2026-03-09', '2027-04-27', 'Develop marketing excellence', '[\"Digital marketing certification\", \"Campaign analytics\", \"Brand strategy\"]', '', '2026-03-09 21:25:07', '2026-03-17 21:25:07', 1),
(78, 2, 13, '2026-02-23', '2027-02-18', 'Become compliance expert', '[\"Advanced compliance training\", \"Audit certification\", \"Risk framework implementation\"]', 'active', '2026-02-23 22:25:07', '2026-03-17 21:25:07', 1),
(79, 3, 14, '2026-02-27', '2027-08-21', 'Progress to software architect', '[\"System design patterns\", \"Architecture workshops\", \"Lead technical design\"]', 'active', '2026-02-27 22:25:07', '2026-03-17 21:25:07', 1),
(80, 4, 15, '2026-03-05', '2027-03-30', 'Achieve customer success leadership', '[\"Executive communication\", \"Strategic account management\", \"Team leadership\"]', '', '2026-03-05 22:25:07', '2026-03-17 21:25:07', 2);

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_transfer_items`
--

CREATE TABLE `knowledge_transfer_items` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `item_type` enum('document','process','contact','system','other') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `knowledge_transfer_items`
--

INSERT INTO `knowledge_transfer_items` (`id`, `plan_id`, `item_type`, `title`, `description`, `priority`, `status`, `completed_at`, `created_at`) VALUES
(1, 1, 'process', 'asd', 'das', 'medium', 'pending', NULL, '2026-03-17 06:07:27'),
(2, 2, 'process', 'ghjhgj', 'jhjjhj', 'medium', 'pending', NULL, '2026-03-17 08:48:34'),
(3, 3, 'process', 'asd', 'iyggyi', 'medium', 'pending', NULL, '2026-03-17 08:55:48'),
(4, 4, 'contact', 'ghjhgj', 'f j;dhk;jhgrl;hjfgb;jgvl;bj', 'low', 'pending', NULL, '2026-03-17 08:59:31'),
(5, 5, 'system', 'sadda', 'das', 'medium', 'pending', NULL, '2026-03-17 09:09:40'),
(6, 6, 'system', 'sadda', 'das', 'medium', 'pending', NULL, '2026-03-17 09:09:51'),
(10, 10, 'system', 'ads', 'asdasdas', 'medium', 'pending', NULL, '2026-03-17 12:41:47');

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_transfer_plans`
--

CREATE TABLE `knowledge_transfer_plans` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `successor_id` varchar(50) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `knowledge_transfer_plans`
--

INSERT INTO `knowledge_transfer_plans` (`id`, `employee_id`, `successor_id`, `start_date`, `end_date`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'EMP002', 'EMP001', '2026-03-16', '2026-03-17', 'active', 10, '2026-03-17 06:07:26', '2026-03-17 06:07:26'),
(2, 'EMP002', 'EMP001', '2026-03-12', '2026-03-18', 'active', NULL, '2026-03-17 08:48:34', '2026-03-17 08:48:34'),
(3, 'EMP003', 'EMP001', '2026-03-19', '2026-03-19', 'active', NULL, '2026-03-17 08:55:48', '2026-03-17 08:55:48'),
(4, 'EMP002', 'EMP001', '2026-03-19', '2026-03-11', 'active', NULL, '2026-03-17 08:59:31', '2026-03-17 08:59:31'),
(5, 'EMP002', 'EMP003', '2026-03-18', '2026-03-26', 'active', NULL, '2026-03-17 09:09:39', '2026-03-17 09:09:39'),
(6, 'EMP002', 'EMP003', '2026-03-18', '2026-03-26', 'active', NULL, '2026-03-17 09:09:51', '2026-03-17 09:09:51'),
(10, 'EMP002', 'EMP003', '2026-03-10', '2026-03-18', 'active', 10, '2026-03-17 12:41:47', '2026-03-17 12:41:47');

-- --------------------------------------------------------

--
-- Table structure for table `leadership_enrollments`
--

CREATE TABLE `leadership_enrollments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `completion_date` timestamp NULL DEFAULT NULL,
  `status` enum('pending','in_progress','completed','dropped') DEFAULT 'pending',
  `feedback` longtext DEFAULT NULL,
  `progress_percentage` int(11) DEFAULT 0,
  `certificate_issued` tinyint(1) DEFAULT 0,
  `certificate_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leadership_enrollments`
--

INSERT INTO `leadership_enrollments` (`id`, `user_id`, `program_id`, `enrollment_date`, `start_date`, `end_date`, `completion_date`, `status`, `feedback`, `progress_percentage`, `certificate_issued`, `certificate_url`) VALUES
(101, 1, 1, '2026-02-20 22:25:07', NULL, NULL, NULL, 'in_progress', NULL, 45, 0, NULL),
(102, 1, 5, '2026-03-07 22:25:07', NULL, NULL, NULL, 'pending', NULL, 0, 0, NULL),
(103, 1, 15, '2026-03-09 21:25:07', NULL, NULL, NULL, '', NULL, 30, 0, NULL),
(104, 2, 2, '2026-02-10 22:25:07', NULL, NULL, NULL, 'in_progress', NULL, 60, 0, NULL),
(105, 2, 4, '2026-03-12 21:25:07', NULL, NULL, NULL, 'pending', NULL, 0, 0, NULL),
(106, 2, 16, '2026-03-05 22:25:07', NULL, NULL, NULL, 'in_progress', NULL, 50, 0, NULL),
(107, 3, 3, '2026-03-02 22:25:07', NULL, NULL, NULL, 'pending', NULL, 0, 0, NULL),
(108, 3, 7, '2026-03-05 22:25:07', NULL, NULL, NULL, 'pending', NULL, 0, 0, NULL),
(109, 3, 17, '2026-02-27 22:25:07', NULL, NULL, NULL, 'in_progress', NULL, 40, 0, NULL),
(110, 4, 1, '2026-03-09 21:25:07', NULL, NULL, NULL, 'pending', NULL, 0, 0, NULL),
(111, 4, 6, '2026-02-23 22:25:07', NULL, NULL, NULL, 'pending', NULL, 0, 0, NULL),
(112, 4, 18, '2026-03-11 21:25:07', NULL, NULL, NULL, '', NULL, 35, 0, NULL),
(113, 5, 2, '2026-02-05 22:25:07', NULL, NULL, NULL, 'in_progress', NULL, 50, 0, NULL),
(114, 5, 8, '2026-03-01 22:25:07', NULL, NULL, NULL, 'pending', NULL, 0, 0, NULL),
(115, 5, 11, '2026-02-15 22:25:07', NULL, NULL, NULL, 'in_progress', NULL, 55, 0, NULL),
(116, 6, 3, '2026-02-25 22:25:07', NULL, NULL, NULL, 'in_progress', NULL, 40, 0, NULL),
(117, 6, 10, '2026-02-03 22:25:07', NULL, NULL, NULL, 'in_progress', NULL, 75, 0, NULL),
(118, 6, 12, '2026-03-07 22:25:07', NULL, NULL, NULL, 'pending', NULL, 0, 0, NULL),
(119, 1, 9, '2026-02-27 22:25:07', NULL, NULL, NULL, 'pending', NULL, 0, 0, NULL),
(120, 2, 11, '2026-02-15 22:25:07', NULL, NULL, NULL, 'in_progress', NULL, 55, 0, NULL),
(121, 3, 13, '2026-03-08 21:25:07', NULL, NULL, NULL, 'pending', NULL, 0, 0, NULL),
(122, 4, 14, '2026-03-03 22:25:07', NULL, NULL, NULL, '', NULL, 25, 0, NULL),
(123, 5, 9, '2026-02-26 22:25:07', NULL, NULL, NULL, 'in_progress', NULL, 65, 0, NULL),
(124, 6, 8, '2026-02-17 22:25:07', NULL, NULL, NULL, '', NULL, 80, 0, NULL),
(125, 1, 12, '2026-03-08 21:25:07', NULL, NULL, NULL, 'pending', NULL, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `leadership_programs`
--

CREATE TABLE `leadership_programs` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` longtext DEFAULT NULL,
  `level` varchar(50) DEFAULT NULL,
  `focus_area` varchar(100) DEFAULT NULL,
  `duration_weeks` int(11) DEFAULT NULL,
  `target_audience` varchar(255) DEFAULT NULL,
  `outcomes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`outcomes`)),
  `created_by` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cover_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leadership_programs`
--

INSERT INTO `leadership_programs` (`id`, `name`, `description`, `level`, `focus_area`, `duration_weeks`, `target_audience`, `outcomes`, `created_by`, `status`, `created_at`, `updated_at`, `cover_photo`) VALUES
(73, 'Executive Leadership Program', 'Comprehensive program for developing executive-level leaders', 'Executive', 'Strategic Leadership', 8, 'C-level Executives', '[\"Strategic Vision\", \"Executive Decision Making\", \"Organizational Strategy\"]', NULL, 'active', '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-1.gif'),
(74, 'Middle Management Excellence', 'Designed for middle managers to enhance leadership capabilities', 'Mid-Level', 'Team Management', 6, 'Middle Managers', '[\"Team Leadership\", \"Performance Management\", \"Delegation\"]', NULL, 'active', '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-2.gif'),
(75, 'Emerging Leaders Program', 'Program for high-potential employees identified as future leaders', 'Foundation', 'Leadership Foundations', 5, 'High Potential Employees', '[\"Leadership Skills\", \"Self Awareness\", \"Communication\"]', NULL, 'active', '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-3.gif'),
(76, 'Strategic Leadership Development', 'Focus on strategic thinking and long-term organizational planning', 'Executive', 'Strategic Thinking', 8, 'Senior Leaders', '[\"Strategic Planning\", \"Competitive Analysis\", \"Market Analysis\"]', NULL, 'active', '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-4.gif'),
(77, 'Leadership Communication Workshop', 'Advanced communication skills for leaders across all levels', 'Mid-Level', 'Communication', 4, 'All Leaders', '[\"Executive Communication\", \"Presentation Skills\", \"Persuasion\"]', NULL, 'active', '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-5.gif'),
(78, 'Change Management & Leadership', 'Guide organizational change as an effective leader', 'Mid-Level', 'Change Leadership', 6, 'Change Agents', '[\"Change Management\", \"Resistance Management\", \"Stakeholder Engagement\"]', NULL, 'active', '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-6.gif'),
(79, 'Emotional Intelligence for Leaders', 'Develop emotional intelligence to improve team dynamics', 'Foundation', 'Self Development', 5, 'Emerging Leaders', '[\"Self Awareness\", \"Empathy\", \"Relationship Management\"]', NULL, 'active', '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-7.gif'),
(80, 'Decision Making for Leaders', 'Learn frameworks for making strategic business decisions', 'Mid-Level', 'Critical Thinking', 5, 'Team Leads', '[\"Decision Frameworks\", \"Risk Assessment\", \"Problem Solving\"]', NULL, 'active', '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-8.gif'),
(81, 'Servant Leadership Model', 'Leadership approach focused on serving and supporting team members', 'Foundation', 'Leadership Philosophy', 4, 'New Leaders', '[\"Service Leadership\", \"Empowerment\", \"Team Development\"]', NULL, 'active', '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-9.gif'),
(82, 'Crisis Leadership & Resilience', 'Lead effectively during challenging times and organizational crises', 'Executive', 'Crisis Management', 6, 'Senior Leaders', '[\"Crisis Response\", \"Decision Making\", \"Team Stabilization\"]', NULL, 'active', '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-10.gif'),
(83, 'Diversity & Inclusion Leadership', 'Building inclusive teams and fostering diversity in leadership', 'Mid-Level', 'Diversity', 5, 'All Leaders', '[\"Inclusive Leadership\", \"Bias Awareness\", \"Diversity Strategy\"]', NULL, 'active', '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-11.gif'),
(84, 'Coaching & Mentoring Skills', 'Develop coaches and mentors for employee development', 'Mid-Level', 'Talent Development', 6, 'Managers', '[\"Coaching Skills\", \"Mentoring\", \"Employee Development\"]', NULL, 'active', '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-12.gif'),
(85, 'Vision & Mission Leadership', 'Creating and communicating organizational vision and values', 'Executive', 'Strategic Leadership', 6, 'C-level & Directors', '[\"Vision Creation\", \"Mission Alignment\", \"Cultural Leadership\"]', NULL, 'active', '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-13.gif'),
(86, 'Sustainable Leadership', 'Leadership practices for sustainable and responsible business growth', 'Executive', 'Sustainable Business', 7, 'Senior Leaders', '[\"Sustainability\", \"ESG Leadership\", \"Responsible Growth\"]', NULL, 'active', '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-14.gif'),
(87, 'Digital Leadership Transformation', 'Leading organizations through digital transformation initiatives', 'Executive', 'Digital Strategy', 8, 'Technology Leaders', '[\"Digital Vision\", \"Tech Integration\", \"Change Leadership\"]', NULL, 'active', '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-15.gif'),
(88, 'Influence Without Authority', 'Master the art of influencing without formal power', 'Mid-Level', 'Influence & Persuasion', 4, 'Project Leads', '[\"Persuasion\", \"Networking\", \"Stakeholder Management\"]', NULL, 'active', '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-16.gif'),
(89, 'Adaptive Leadership', 'Learn to lead through ambiguity and rapid change', 'Foundation', 'Adaptability', 5, 'All Levels', '[\"Agility\", \"Problem Solving\", \"Resilience\"]', NULL, 'active', '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-17.gif'),
(90, 'Global Leadership Excellence', 'Master cross-cultural leadership in global organizations', 'Executive', 'Global Strategy', 7, 'International Leaders', '[\"Cultural Awareness\", \"Global Strategy\", \"Cross-Cultural Communication\"]', NULL, 'active', '2026-03-17 21:25:07', '2026-03-17 21:25:07', 'modules/img/gifholder/gifholder-18.gif');

-- --------------------------------------------------------

--
-- Table structure for table `lms_courses`
--

CREATE TABLE `lms_courses` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` longtext DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `course_content` longtext DEFAULT NULL,
  `duration_hours` int(11) DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lms_courses`
--

INSERT INTO `lms_courses` (`id`, `title`, `description`, `category`, `instructor_id`, `course_content`, `duration_hours`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Advanced SQL Optimization', 'Learn advanced database query optimization techniques and performance tuning', 'Database', 2, 'Advanced techniques for database query optimization and performance tuning', 40, 'published', '2026-03-17 21:25:09', '2026-03-17 21:25:09'),
(2, 'Web Development with Python Flask', 'Build modern web applications using Python Flask framework', 'Web Development', 2, 'Comprehensive guide to building web applications with Python Flask', 36, 'published', '2026-03-17 21:25:09', '2026-03-17 21:25:09'),
(3, 'Mobile App Development Basics', 'Introduction to mobile app development for iOS and Android', 'Mobile Development', 3, 'Fundamental concepts and tools for developing mobile applications', 32, 'published', '2026-03-17 21:25:09', '2026-03-17 21:25:09'),
(4, 'Business Intelligence & Analytics', 'Master BI tools and analytics for data-driven decision making', 'Analytics', 5, 'BI tools and analytics methodologies for business intelligence', 30, 'published', '2026-03-17 21:25:09', '2026-03-17 21:25:09'),
(5, 'Graphic Design Essentials', 'Learn fundamental design principles and creative tools', 'Design', 6, 'Design principles and essential Adobe Creative Suite tools', 28, 'published', '2026-03-17 21:25:09', '2026-03-17 21:25:09'),
(6, 'Content Marketing Strategy', 'Develop effective content marketing campaigns and strategies', 'Marketing', 1, 'Strategies for developing and implementing content marketing campaigns', 24, 'published', '2026-03-17 21:25:09', '2026-03-17 21:25:09'),
(7, 'Advanced Excel for Business', 'Master advanced Excel functions for business analytics', 'Business Tools', 2, 'Advanced Excel functions and features for business analysis', 20, 'published', '2026-03-17 21:25:09', '2026-03-17 21:25:09'),
(8, 'Professional Writing Skills', 'Enhance business writing and documentation skills', 'Communication', 3, 'Business writing techniques and professional documentation skills', 16, 'published', '2026-03-17 21:25:09', '2026-03-17 21:25:09'),
(9, 'Project Risk Management', 'Identify, assess, and mitigate project risks', 'Project Management', 4, 'Risk identification, assessment, and mitigation strategies', 24, 'published', '2026-03-17 21:25:09', '2026-03-17 21:25:09'),
(10, 'Building Remote Teams', 'Strategies for managing and engaging remote teams effectively', 'Management', 1, 'Management strategies for remote and distributed teams', 20, 'published', '2026-03-17 21:25:09', '2026-03-17 21:25:09');

-- --------------------------------------------------------

--
-- Table structure for table `lms_enrollments`
--

CREATE TABLE `lms_enrollments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `completion_date` timestamp NULL DEFAULT NULL,
  `progress_percentage` int(11) DEFAULT 0,
  `score` decimal(5,2) DEFAULT NULL,
  `status` enum('enrolled','in_progress','completed','dropped') DEFAULT 'enrolled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lms_enrollments`
--

INSERT INTO `lms_enrollments` (`id`, `user_id`, `course_id`, `enrollment_date`, `completion_date`, `progress_percentage`, `score`, `status`) VALUES
(1, 1, 1, '2025-12-17 22:25:09', '2026-02-15 22:25:09', 100, 92.00, 'completed'),
(2, 1, 2, '2026-01-31 22:25:09', NULL, 65, 0.00, 'in_progress'),
(3, 2, 3, '2026-03-07 22:25:09', NULL, 0, 0.00, ''),
(4, 2, 4, '2026-02-20 22:25:09', NULL, 45, 0.00, 'in_progress'),
(5, 2, 7, '2026-01-16 22:25:09', '2026-03-02 22:25:09', 100, 88.00, 'completed'),
(6, 3, 1, '2026-01-26 22:25:09', NULL, 75, 0.00, 'in_progress'),
(7, 3, 4, '2026-03-12 21:25:09', NULL, 0, 0.00, ''),
(8, 3, 8, '2026-01-01 22:25:09', '2026-02-25 22:25:09', 100, 95.00, 'completed'),
(9, 4, 2, '2026-03-09 21:25:09', NULL, 0, 0.00, ''),
(10, 4, 5, '2026-02-15 22:25:09', NULL, 35, 0.00, 'in_progress'),
(11, 4, 9, '2025-12-22 22:25:09', '2026-02-20 22:25:09', 100, 85.00, 'completed'),
(12, 5, 4, '2026-02-10 22:25:09', NULL, 55, 0.00, 'in_progress'),
(13, 5, 6, '2026-01-06 22:25:09', '2026-02-27 22:25:09', 100, 90.00, 'completed'),
(14, 5, 10, '2026-03-14 21:25:09', NULL, 0, 0.00, ''),
(15, 6, 2, '2026-02-05 22:25:09', NULL, 45, 0.00, 'in_progress'),
(16, 6, 3, '2026-03-11 21:25:09', NULL, 0, 0.00, ''),
(17, 6, 7, '2026-02-25 22:25:09', NULL, 60, 0.00, 'in_progress');

-- --------------------------------------------------------

--
-- Table structure for table `performance_reviews`
--

CREATE TABLE `performance_reviews` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `review_period_start` date NOT NULL,
  `review_period_end` date NOT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `comments` longtext DEFAULT NULL,
  `reviewed_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('draft','submitted','completed') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cover_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performance_reviews`
--

INSERT INTO `performance_reviews` (`id`, `employee_id`, `reviewer_id`, `review_period_start`, `review_period_end`, `rating`, `comments`, `reviewed_date`, `status`, `created_at`, `cover_photo`) VALUES
(21, 1, 2, '2025-09-18', '2025-12-17', 4.50, 'Exceptional performance in project delivery and team collaboration', '2026-03-17 21:25:08', 'completed', '2025-12-22 22:25:08', NULL),
(22, 2, 3, '2025-09-18', '2025-12-17', 4.20, 'Strong technical skills with room for improvement in communication', '2026-03-17 21:25:08', 'completed', '2025-12-24 22:25:08', NULL),
(23, 3, 1, '2025-09-18', '2025-12-17', 4.80, 'Outstanding leadership and mentorship qualities', '2026-03-17 21:25:08', 'completed', '2025-12-19 22:25:08', NULL),
(24, 4, 2, '2025-09-18', '2025-12-17', 3.90, 'Solid performance with focus needed on time management', '2026-03-17 21:25:08', 'completed', '2025-12-23 22:25:08', NULL),
(25, 5, 1, '2025-09-18', '2025-12-17', 4.60, 'Excellent strategic planning and execution capabilities', '2026-03-17 21:25:08', 'completed', '2025-12-21 22:25:08', NULL),
(26, 6, 3, '2025-09-18', '2025-12-17', 4.10, 'Good performance with potential for growth in advanced skills', '2026-03-17 21:25:08', 'completed', '2025-12-25 22:25:08', NULL),
(27, 1, 2, '2025-12-17', '2026-03-17', 4.30, 'Continues to demonstrate leadership excellence and innovation', '2026-03-17 21:25:08', '', '2026-03-12 21:25:08', NULL),
(28, 2, 3, '2025-12-17', '2026-03-17', 4.00, 'Improved communication skills, strong technical performance', '2026-03-17 21:25:08', '', '2026-03-13 21:25:08', NULL),
(29, 3, 1, '2025-12-17', '2026-03-17', 4.70, 'Exceptional mentorship leading to team growth', '2026-03-17 21:25:08', '', '2026-03-15 21:25:08', NULL),
(30, 4, 2, '2025-12-17', '2026-03-17', 4.10, 'Better time management and project coordination', '2026-03-17 21:25:08', '', '2026-03-14 21:25:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `resignations`
--

CREATE TABLE `resignations` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `resignation_type` enum('voluntary','involuntary') NOT NULL,
  `reason` text NOT NULL,
  `notice_date` date NOT NULL,
  `last_working_date` date NOT NULL,
  `comments` text DEFAULT NULL,
  `submitted_by` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected','withdrawn') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resignations`
--

INSERT INTO `resignations` (`id`, `employee_id`, `resignation_type`, `reason`, `notice_date`, `last_working_date`, `comments`, `submitted_by`, `status`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(5, 'EMP002', 'voluntary', 'dasdadsa', '2026-03-17', '2026-03-17', 'dsadas', 10, 'pending', NULL, NULL, '2026-03-17 07:05:31', '2026-03-17 07:23:35'),
(8, 'EMP001', 'voluntary', 'rsdsrd', '2026-04-02', '2026-03-18', 'rsd', NULL, 'pending', NULL, NULL, '2026-03-17 08:39:24', '2026-03-17 08:39:24'),
(9, 'EMP001', 'involuntary', 'kgkvhgl;bkvlnkfgbjfvb', '2026-03-26', '2026-03-27', 'fjgkdfahgkljhfgklhnjg', 10, 'pending', NULL, NULL, '2026-03-17 08:58:09', '2026-03-17 08:58:09'),
(12, 'EMP003', 'voluntary', 'sad', '2026-03-18', '2026-03-12', 'asdasdas', 10, 'pending', NULL, NULL, '2026-03-17 16:00:02', '2026-03-17 16:00:02'),
(13, 'EMP003', 'involuntary', 'dsa', '2026-03-19', '2026-03-19', 'sad', 10, 'pending', NULL, NULL, '2026-03-17 16:15:17', '2026-03-17 16:15:17');

-- --------------------------------------------------------

--
-- Table structure for table `succession_plans`
--

CREATE TABLE `succession_plans` (
  `id` int(11) NOT NULL,
  `position_id` int(11) DEFAULT NULL,
  `position_name` varchar(150) NOT NULL,
  `current_holder_id` int(11) DEFAULT NULL,
  `successor_id` int(11) DEFAULT NULL,
  `readiness_level` varchar(50) DEFAULT NULL,
  `planned_transition_date` date DEFAULT NULL,
  `status` enum('draft','active','completed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `succession_plans`
--

INSERT INTO `succession_plans` (`id`, `position_id`, `position_name`, `current_holder_id`, `successor_id`, `readiness_level`, `planned_transition_date`, `status`, `created_at`) VALUES
(1, NULL, 'Senior Developer', 4, 7, 'High', '2026-06-01', 'active', '2026-03-15 17:19:02'),
(2, NULL, 'Project Manager', 7, 4, 'Medium', '2026-07-01', 'active', '2026-03-15 17:19:02');

-- --------------------------------------------------------

--
-- Table structure for table `survey_answers`
--

CREATE TABLE `survey_answers` (
  `id` int(11) NOT NULL,
  `response_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text DEFAULT NULL,
  `answer_value` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `survey_questions`
--

CREATE TABLE `survey_questions` (
  `id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('text','textarea','radio','checkbox','select','rating') NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `required` tinyint(1) DEFAULT 0,
  `order_num` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `survey_questions`
--

INSERT INTO `survey_questions` (`id`, `survey_id`, `question_text`, `question_type`, `options`, `required`, `order_num`, `created_at`) VALUES
(1, 1, 'casdcacd', 'textarea', NULL, 0, 1, '2026-03-17 09:05:01'),
(2, 2, 'casdcacd', 'textarea', NULL, 0, 1, '2026-03-17 09:05:23'),
(3, 2, '2323232', 'textarea', NULL, 0, 2, '2026-03-17 09:05:23'),
(4, 3, 'dsadasds', 'textarea', NULL, 0, 1, '2026-03-17 14:01:23'),
(5, 4, 'hello', 'checkbox', '\"hi\\r\\nhello\\r\\nwao\"', 0, 1, '2026-03-17 15:34:26'),
(6, 4, 'mwke', 'rating', NULL, 0, 2, '2026-03-17 15:34:27'),
(7, 4, 'asdw', 'radio', '\"das\\r\\ndasd\\r\\nweq\"', 0, 3, '2026-03-17 15:34:27'),
(8, 4, 'sad', 'textarea', NULL, 0, 4, '2026-03-17 15:34:27');

-- --------------------------------------------------------

--
-- Table structure for table `survey_responses`
--

CREATE TABLE `survey_responses` (
  `id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `employee_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `responses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`responses`)),
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team_activities`
--

CREATE TABLE `team_activities` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` longtext DEFAULT NULL,
  `activity_date` date NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `organizer_id` int(11) DEFAULT NULL,
  `budget` decimal(10,2) DEFAULT NULL,
  `participant_count` int(11) DEFAULT NULL,
  `status` enum('planned','ongoing','completed','cancelled') DEFAULT 'planned',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cover_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `team_activities`
--

INSERT INTO `team_activities` (`id`, `name`, `description`, `activity_date`, `department`, `organizer_id`, `budget`, `participant_count`, `status`, `created_at`, `cover_photo`) VALUES
(101, 'Annual Team Building Retreat', 'Full-day team building and strategic planning session for all departments', '2026-04-16', 'All Departments', NULL, 5000.00, 12, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-1.gif'),
(102, 'Cross-Functional Collaboration Workshop', 'Workshop to improve communication and collaboration across teams', '2026-04-11', 'HR & Operations', NULL, 2500.00, 8, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-2.gif'),
(103, 'Innovation Hackathon 2026', 'Interactive hackathon to generate innovative ideas for organizational improvements', '2026-05-01', 'Technology', NULL, 3500.00, 15, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-3.gif'),
(104, 'Diversity & Inclusion Initiative', 'Program promoting diversity and inclusive workplace culture', '2026-04-01', 'HR', NULL, 1500.00, 20, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-4.gif'),
(105, 'Wellness & Work-Life Balance Program', 'Comprehensive wellness initiative focusing on employee well-being', '2026-04-06', 'HR', NULL, 3000.00, 35, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-5.gif'),
(106, 'Mentoring Program Launch', 'Formal mentoring program connecting senior and junior staff', '2026-04-21', 'All Departments', NULL, 1000.00, 18, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-6.gif'),
(107, 'Quarterly Knowledge Sharing Session', 'Monthly sessions where employees share expertise and best practices', '2026-03-27', 'Organization', NULL, 500.00, 25, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-7.gif'),
(108, 'Community Outreach Initiative', 'Corporate social responsibility program for community involvement', '2026-05-06', 'CSR', NULL, 2000.00, 10, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-8.gif'),
(109, 'Environmental Sustainability Project', 'Go-green initiative to promote environmental responsibility', '2026-04-26', 'Operations', NULL, 4000.00, 22, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-9.gif'),
(110, 'Leadership Development Circle', 'Monthly discussion group for emerging leaders to develop professionally', '2026-03-22', 'Management', NULL, 800.00, 14, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-10.gif'),
(111, 'Skills Development Workshop Series', 'Multi-week workshop covering essential professional skills', '2026-04-08', 'Training', NULL, 3500.00, 30, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-11.gif'),
(112, 'Employee Recognition Program', 'Celebration and recognition of outstanding employee contributions', '2026-04-04', 'HR', NULL, 1200.00, 50, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-12.gif'),
(113, 'Tech Innovation Lab', 'Collaborative space for exploring emerging technologies', '2026-05-16', 'Technology', NULL, 5500.00, 16, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-13.gif'),
(114, 'Customer Experience Improvement', 'Initiative to enhance customer satisfaction and loyalty', '2026-04-14', 'Operations', NULL, 2200.00, 19, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-14.gif'),
(115, 'Process Improvement Kaizen', 'Continuous improvement methodology implementation sessions', '2026-04-18', 'Operations', NULL, 1800.00, 24, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-15.gif'),
(116, 'Health & Safety Awareness Campaign', 'Comprehensive health, safety, and wellness awareness program', '2026-03-29', 'Safety', NULL, 2500.00, 28, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-16.gif'),
(117, 'Digital Transformation Roadshow', 'Series of sessions introducing digital tools and processes', '2026-04-24', 'Technology', NULL, 3200.00, 32, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-17.gif'),
(118, 'Team Sports & Recreation', 'Organized sports activities and recreation for team bonding', '2026-03-25', 'HR', NULL, 2000.00, 40, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-18.gif'),
(119, 'Performance Excellence Program', 'Program focusing on highest performance standards and metrics', '2026-04-28', 'Management', NULL, 2800.00, 21, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-19.gif'),
(120, 'Social Responsibility Week', 'Week-long initiative for various charitable and social causes', '2026-05-11', 'CSR', NULL, 3300.00, 45, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-20.gif'),
(121, 'Agile Transformation Initiative', 'Implementing Agile practices across organization', '2026-05-04', 'Technology', NULL, 4200.00, 26, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-21.gif'),
(122, 'Customer Advisory Board Meeting', 'Strategic session with key customers for feedback', '2026-04-06', 'Sales', NULL, 1800.00, 12, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-1.gif'),
(123, 'Code of Conduct Training', 'Mandatory ethics and compliance training for all employees', '2026-03-24', 'Compliance', NULL, 1200.00, 60, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-2.gif'),
(125, 'Executive Strategy Summit', 'Annual gathering of leadership for strategic planning', '2026-05-21', 'Executive', NULL, 6500.00, 15, 'planned', '2026-03-17 21:25:08', 'modules/img/gifholder/gifholder-4.gif');

-- --------------------------------------------------------

--
-- Table structure for table `team_activity_participants`
--

CREATE TABLE `team_activity_participants` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'confirmed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team_activity_participants`
--

INSERT INTO `team_activity_participants` (`id`, `user_id`, `activity_id`, `status`, `created_at`) VALUES
(205, 1, 1, 'confirmed', '2026-02-25 22:25:08'),
(206, 1, 5, 'confirmed', '2026-03-02 22:25:08'),
(207, 1, 10, 'confirmed', '2026-03-01 22:25:08'),
(208, 1, 19, 'confirmed', '2026-03-13 21:25:08'),
(209, 2, 2, 'confirmed', '2026-02-27 22:25:08'),
(210, 2, 7, 'confirmed', '2026-03-05 22:25:08'),
(211, 2, 14, 'confirmed', '2026-03-03 22:25:08'),
(212, 2, 9, 'confirmed', '2026-02-28 22:25:08'),
(213, 2, 23, 'confirmed', '2026-03-15 21:25:08'),
(214, 3, 3, 'confirmed', '2026-02-20 22:25:08'),
(215, 3, 11, 'confirmed', '2026-03-07 22:25:08'),
(216, 3, 6, 'confirmed', '2026-02-25 22:25:08'),
(217, 3, 17, 'confirmed', '2026-03-04 22:25:08'),
(218, 3, 24, 'confirmed', '2026-03-16 21:25:08'),
(219, 4, 4, 'confirmed', '2026-02-23 22:25:08'),
(220, 4, 9, 'confirmed', '2026-03-09 21:25:08'),
(221, 4, 12, 'confirmed', '2026-02-26 22:25:08'),
(222, 4, 5, 'confirmed', '2026-02-21 22:25:08'),
(223, 4, 20, 'confirmed', '2026-03-14 21:25:08'),
(224, 5, 6, 'confirmed', '2026-02-15 22:25:08'),
(225, 5, 13, 'confirmed', '2026-03-12 21:25:08'),
(226, 5, 15, 'confirmed', '2026-03-06 22:25:08'),
(227, 5, 18, 'confirmed', '2026-03-11 21:25:08'),
(228, 5, 21, 'confirmed', '2026-03-09 21:25:08'),
(229, 6, 8, 'confirmed', '2026-02-17 22:25:08'),
(230, 6, 7, 'confirmed', '2026-02-22 22:25:08'),
(231, 6, 3, 'confirmed', '2026-03-10 21:25:08'),
(232, 6, 16, 'confirmed', '2026-03-08 21:25:08'),
(233, 6, 22, 'confirmed', '2026-03-12 21:25:08'),
(234, 1, 2, 'confirmed', '2026-03-06 22:25:08'),
(235, 2, 1, 'confirmed', '2026-02-26 22:25:08'),
(236, 3, 5, 'confirmed', '2026-03-01 22:25:08'),
(237, 4, 7, 'confirmed', '2026-02-24 22:25:08'),
(238, 5, 4, 'confirmed', '2026-03-04 22:25:08'),
(239, 6, 9, 'confirmed', '2026-03-07 22:25:08'),
(240, 1, 11, 'confirmed', '2026-03-03 22:25:08'),
(241, 2, 13, 'confirmed', '2026-02-23 22:25:08'),
(242, 3, 8, 'confirmed', '2026-02-19 22:25:08'),
(243, 4, 10, 'confirmed', '2026-02-27 22:25:08'),
(244, 5, 2, 'confirmed', '2026-02-18 22:25:08'),
(245, 6, 14, 'confirmed', '2026-02-22 22:25:08'),
(246, 1, 15, 'confirmed', '2026-03-10 21:25:08'),
(247, 2, 16, 'confirmed', '2026-03-11 21:25:08'),
(248, 3, 18, 'confirmed', '2026-03-02 22:25:08'),
(249, 4, 19, 'confirmed', '2026-02-25 22:25:08'),
(250, 5, 20, 'confirmed', '2026-03-08 21:25:08'),
(251, 6, 21, 'confirmed', '2026-03-05 22:25:08'),
(252, 1, 12, 'confirmed', '2026-02-20 22:25:08'),
(253, 2, 8, 'confirmed', '2026-02-26 22:25:08'),
(254, 3, 4, 'confirmed', '2026-02-28 22:25:08'),
(255, 4, 6, 'confirmed', '2026-03-05 22:25:08');

-- --------------------------------------------------------

--
-- Table structure for table `training_enrollments`
--

CREATE TABLE `training_enrollments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected','completed','banned','exempt') DEFAULT 'pending',
  `enrollment_date` timestamp NULL DEFAULT current_timestamp(),
  `completion_date` timestamp NULL DEFAULT NULL,
  `feedback` longtext DEFAULT NULL,
  `progress_percentage` int(11) DEFAULT 0,
  `certificate_issued` tinyint(1) DEFAULT 0,
  `certificate_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `training_enrollments`
--

INSERT INTO `training_enrollments` (`id`, `user_id`, `program_id`, `status`, `enrollment_date`, `completion_date`, `feedback`, `progress_percentage`, `certificate_issued`, `certificate_url`) VALUES
(283, 1, 1, 'pending', '2026-03-02 22:25:06', NULL, NULL, 35, 0, NULL),
(284, 1, 2, 'pending', '2026-03-07 22:25:06', NULL, NULL, 50, 0, NULL),
(285, 1, 3, 'completed', '2026-02-15 22:25:06', NULL, NULL, 100, 0, NULL),
(286, 1, 11, 'approved', '2026-03-09 21:25:06', NULL, NULL, 20, 0, NULL),
(287, 1, 17, 'completed', '2026-02-10 22:25:06', NULL, NULL, 100, 0, NULL),
(288, 1, 22, 'pending', '2026-03-12 21:25:06', NULL, NULL, 15, 0, NULL),
(289, 2, 2, 'pending', '2026-02-25 22:25:06', NULL, NULL, 25, 0, NULL),
(290, 2, 4, 'approved', '2026-03-09 21:25:06', NULL, NULL, 40, 0, NULL),
(291, 2, 12, 'pending', '2026-03-03 22:25:06', NULL, NULL, 30, 0, NULL),
(292, 2, 18, 'approved', '2026-03-11 21:25:06', NULL, NULL, 15, 0, NULL),
(293, 2, 23, '', '2026-03-14 21:25:06', NULL, NULL, 65, 0, NULL),
(294, 3, 6, 'pending', '2026-03-05 22:25:06', NULL, NULL, 45, 0, NULL),
(295, 3, 7, 'completed', '2026-01-31 22:25:06', NULL, NULL, 100, 0, NULL),
(296, 3, 13, 'pending', '2026-03-07 22:25:06', NULL, NULL, 60, 0, NULL),
(297, 3, 19, 'approved', '2026-03-08 21:25:06', NULL, NULL, 25, 0, NULL),
(298, 3, 24, '', '2026-03-10 21:25:06', NULL, NULL, 55, 0, NULL),
(299, 4, 1, 'pending', '2026-03-12 21:25:06', NULL, NULL, 10, 0, NULL),
(300, 4, 8, 'pending', '2026-02-27 22:25:06', NULL, NULL, 55, 0, NULL),
(301, 4, 14, 'approved', '2026-03-05 22:25:06', NULL, NULL, 35, 0, NULL),
(302, 4, 20, 'pending', '2026-03-10 21:25:06', NULL, NULL, 20, 0, NULL),
(303, 4, 25, '', '2026-03-15 21:25:06', NULL, NULL, 40, 0, NULL),
(304, 5, 2, 'completed', '2026-01-16 22:25:06', NULL, NULL, 100, 0, NULL),
(305, 5, 9, 'pending', '2026-03-10 21:25:06', NULL, NULL, 70, 0, NULL),
(306, 5, 15, 'pending', '2026-03-06 22:25:06', NULL, NULL, 40, 0, NULL),
(307, 5, 26, 'approved', '2026-03-13 21:25:06', NULL, NULL, 85, 0, NULL),
(308, 6, 3, 'pending', '2026-03-03 22:25:06', NULL, NULL, 30, 0, NULL),
(309, 6, 10, 'pending', '2026-02-25 22:25:06', NULL, NULL, 50, 0, NULL),
(310, 6, 16, 'completed', '2026-01-26 22:25:06', NULL, NULL, 100, 0, NULL),
(311, 6, 27, 'pending', '2026-03-11 21:25:06', NULL, NULL, 25, 0, NULL),
(312, 1, 5, 'pending', '2026-02-23 22:25:06', NULL, NULL, 20, 0, NULL),
(313, 2, 20, 'approved', '2026-03-13 21:25:06', NULL, NULL, 5, 0, NULL),
(314, 3, 21, '', '2026-03-08 21:25:06', NULL, NULL, 35, 0, NULL),
(315, 4, 28, 'pending', '2026-03-06 22:25:06', NULL, NULL, 28, 0, NULL),
(316, 5, 29, 'approved', '2026-03-09 21:25:06', NULL, NULL, 50, 0, NULL),
(317, 6, 30, '', '2026-03-11 21:25:06', NULL, NULL, 60, 0, NULL),
(318, 1, 4, '', '2026-03-05 22:25:06', NULL, NULL, 45, 0, NULL),
(319, 2, 6, 'pending', '2026-02-27 22:25:06', NULL, NULL, 20, 0, NULL),
(320, 3, 8, 'approved', '2026-03-01 22:25:06', NULL, NULL, 75, 0, NULL),
(321, 4, 9, '', '2026-03-03 22:25:06', NULL, NULL, 55, 0, NULL),
(322, 5, 11, 'pending', '2026-03-07 22:25:06', NULL, NULL, 30, 0, NULL),
(323, 6, 12, 'approved', '2026-03-04 22:25:06', NULL, NULL, 45, 0, NULL),
(324, 1, 13, '', '2026-02-26 22:25:06', NULL, NULL, 65, 0, NULL),
(325, 2, 14, 'pending', '2026-03-10 21:25:06', NULL, NULL, 22, 0, NULL),
(326, 3, 15, 'approved', '2026-02-24 22:25:06', NULL, NULL, 80, 0, NULL),
(327, 4, 16, '', '2026-02-28 22:25:06', NULL, NULL, 38, 0, NULL),
(328, 5, 17, 'completed', '2026-01-21 22:25:06', NULL, NULL, 100, 0, NULL),
(329, 6, 18, 'pending', '2026-03-02 22:25:06', NULL, NULL, 18, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `training_logs`
--

CREATE TABLE `training_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `action_type` varchar(50) DEFAULT NULL,
  `details` longtext DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `training_programs`
--

CREATE TABLE `training_programs` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` longtext DEFAULT NULL,
  `category` varchar(100) DEFAULT 'General',
  `type` varchar(100) DEFAULT 'Workshop',
  `duration` int(11) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `status` enum('Active','Upcoming','Inactive') DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cover_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `training_programs`
--

INSERT INTO `training_programs` (`id`, `name`, `description`, `category`, `type`, `duration`, `created_by`, `status`, `created_at`, `updated_at`, `cover_photo`) VALUES
(189, 'Advanced Leadership Skills', 'Learn advanced leadership techniques and management strategies to lead teams effectively', 'Leadership', 'Workshop', 5, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-1.gif'),
(190, 'Technical Project Management', 'Master the fundamentals of project management using industry-standard methodologies', 'Management', 'Course', 8, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-2.gif'),
(191, 'Communication Excellence', 'Enhance your communication skills for better professional interactions', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-3.gif'),
(192, 'Data Analysis & Excel Mastery', 'Comprehensive training on data analysis tools and advanced Excel functions', 'Technical', 'Course', 6, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-4.gif'),
(193, 'Customer Service Excellence', 'Develop exceptional customer service skills to improve client satisfaction', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-5.gif'),
(194, 'Digital Marketing Fundamentals', 'Learn the basics of digital marketing and social media strategies', 'Marketing', 'Course', 7, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-6.gif'),
(195, 'Financial Analysis for Non-Finance', 'Understanding financial statements and business metrics for non-finance professionals', 'Finance', 'Course', 5, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-7.gif'),
(196, 'Effective Negotiation Tactics', 'Master negotiation techniques for better business outcomes', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-8.gif'),
(197, 'Time Management & Productivity', 'Improve productivity and manage time effectively in a fast-paced environment', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-9.gif'),
(198, 'Creativity & Innovation in Business', 'Unlock your creative potential and drive innovation in your organization', 'Soft Skills', 'Workshop', 4, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-10.gif'),
(199, 'Six Sigma Green Belt', 'Process improvement certification focusing on quality management', 'Operations', 'Course', 10, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-11.gif'),
(200, 'Agile Methodology Bootcamp', 'Learn Agile principles and practices for software development teams', 'Technical', 'Bootcamp', 6, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-12.gif'),
(201, 'Strategic Business Planning', 'Develop skills in strategic planning and competitive analysis', 'Management', 'Course', 7, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-13.gif'),
(202, 'Supply Chain Management', 'Comprehensive overview of supply chain operations and optimization', 'Operations', 'Course', 8, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-14.gif'),
(203, 'Quality Management Systems', 'ISO 9001 and quality management fundamentals', 'Operations', 'Workshop', 4, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-15.gif'),
(204, 'Business Ethics & Compliance', 'Understanding ethical practices and regulatory compliance requirements', 'Compliance', 'Course', 3, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-16.gif'),
(205, 'Advanced Public Speaking', 'Master the art of public speaking and presentation skills', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-17.gif'),
(206, 'Conflict Resolution Workshop', 'Learn techniques to resolve workplace conflicts effectively', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-18.gif'),
(207, 'Cloud Computing Essentials', 'Introduction to cloud platforms and cloud-based solutions', 'Technical', 'Course', 6, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-19.gif'),
(208, 'Cybersecurity Fundamentals', 'Protect your organization from cyber threats and security breaches', 'Technical', 'Course', 5, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-20.gif'),
(209, 'DevOps Best Practices', 'Learn containerization, CI/CD pipelines, and infrastructure as code', 'Technical', 'Bootcamp', 8, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-21.gif'),
(210, 'Machine Learning Fundamentals', 'Introduction to machine learning algorithms and practical applications', 'Technical', 'Course', 9, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-1.gif'),
(211, 'React & Modern JavaScript', 'Build dynamic web applications using React and ES6+ JavaScript', 'Technical', 'Course', 7, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-2.gif'),
(212, 'User Experience Design', 'Create intuitive and engaging user interfaces with UX principles', 'Design', 'Course', 6, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-3.gif'),
(213, 'Database Design & SQL', 'Master relational databases, queries, and optimization techniques', 'Technical', 'Course', 8, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-4.gif'),
(214, 'API Development & Integration', 'Build and integrate RESTful APIs and microservices', 'Technical', 'Course', 6, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-5.gif'),
(215, 'Advanced Excel for Analytics', 'Pivot tables, VLOOKUP, macros, and data visualization in Excel', 'Technical', 'Workshop', 4, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-6.gif'),
(216, 'Power BI & Data Visualization', 'Create interactive dashboards and reports with Power BI', 'Technical', 'Course', 5, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-7.gif'),
(217, 'Emotional Intelligence at Work', 'Develop emotional awareness and interpersonal effectiveness', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-8.gif'),
(218, 'Team Collaboration & Synergy', 'Build high-performing teams through effective collaboration', 'Management', 'Workshop', 3, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-9.gif'),
(219, 'Presentation Skills Mastery', 'Create compelling presentations and deliver impactful messages', 'Soft Skills', 'Workshop', 4, NULL, 'Active', '2026-03-17 21:25:06', '2026-03-17 21:25:06', 'modules/img/gifholder/gifholder-10.gif');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('recruitment','payroll','time','compliance','workforce','employee','learning','performance','engagement_relations','exit','admin','manager','trainer') NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `theme` enum('light','dark') DEFAULT 'light',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `department`, `position`, `manager_id`, `status`, `theme`, `created_at`, `updated_at`, `last_login`, `profile_pic`) VALUES
(1, 'hr_payroll', 'hr_payroll@example.com', '$2y$10$YSkTSwrSdqSBsF2e.pfyq.mNCCIF7ijV4h/s1pAc8Q7KlQHzbQTmq', 'Russell Ike', 'admin', NULL, NULL, NULL, 'active', 'light', '2026-03-06 21:13:06', '2026-03-15 17:18:53', NULL, NULL),
(2, 'hr_recruitment', 'hr_recruitment@example.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'Administrator', 'admin', NULL, NULL, NULL, 'active', 'light', '2026-03-07 02:46:33', '2026-03-15 17:18:53', NULL, NULL),
(3, 'hr_time', 'hr_time@example.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'Admin', 'admin', NULL, NULL, NULL, 'active', 'light', '2026-03-07 02:47:07', '2026-03-15 17:18:53', NULL, NULL),
(4, 'hr_employee', 'hr_employee@example.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'someone', 'admin', NULL, NULL, NULL, 'active', 'light', '2026-03-07 02:47:55', '2026-03-15 17:18:53', NULL, NULL),
(5, 'hr_compliance', 'hr_compliance@example.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'comply', 'admin', NULL, NULL, NULL, 'active', 'light', '2026-03-07 02:48:19', '2026-03-15 17:18:53', NULL, NULL),
(6, 'hr_workforce', 'hr_workforce@example.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'force', 'admin', NULL, NULL, NULL, 'active', 'light', '2026-03-07 02:48:43', '2026-03-15 17:18:53', NULL, NULL),
(7, 'hr_learning', 'hr_learning@example.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'learn', 'learning', NULL, NULL, NULL, 'active', 'light', '2026-03-07 02:49:22', '2026-03-15 19:38:33', NULL, NULL),
(8, 'hr_performance', 'hr_performance@example.com', '$2y$10$/aFKLVK.xloqiY31X4T.dOPKY2AnnkrpaME4f2z.l4LhQurY1/Zzy', 'Perform', 'performance', NULL, NULL, NULL, 'active', 'light', '2026-03-07 02:49:46', '2026-03-15 17:18:53', NULL, 'user_8.jpg'),
(9, 'hr_engagement', 'hr_engagement@example.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'engage', 'admin', NULL, NULL, NULL, 'active', 'light', '2026-03-07 02:50:37', '2026-03-15 17:18:53', NULL, NULL),
(10, 'hr_exit', 'hr_exit@example.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'exit', 'admin', NULL, NULL, NULL, 'active', 'light', '2026-03-07 02:51:04', '2026-03-15 17:18:53', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_competencies`
--

CREATE TABLE `user_competencies` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `competency_id` int(11) NOT NULL,
  `current_level` varchar(50) DEFAULT NULL,
  `target_level` varchar(50) DEFAULT NULL,
  `assessed_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `assessed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_competencies`
--

INSERT INTO `user_competencies` (`id`, `user_id`, `competency_id`, `current_level`, `target_level`, `assessed_date`, `assessed_by`) VALUES
(35, 1, 1, 'Advanced', 'Expert', '2026-02-15 22:25:08', NULL),
(36, 1, 2, 'Advanced', 'Fluent', '2026-02-15 22:25:08', NULL),
(37, 1, 4, 'Intermediate', 'Advanced', '2026-02-15 22:25:08', NULL),
(38, 1, 8, 'Advanced', 'Expert', '2026-02-15 22:25:08', NULL),
(39, 2, 3, 'Advanced', 'Master', '2026-02-20 22:25:08', NULL),
(40, 2, 2, 'Intermediate', 'Advanced', '2026-02-20 22:25:08', NULL),
(41, 2, 5, 'Advanced', 'Expert', '2026-02-20 22:25:08', NULL),
(42, 3, 1, 'Expert', 'Expert', '2026-02-10 22:25:08', NULL),
(43, 3, 2, 'Advanced', 'Fluent', '2026-02-10 22:25:08', NULL),
(44, 3, 4, 'Advanced', 'Expert', '2026-02-10 22:25:08', NULL),
(45, 3, 6, 'Advanced', 'Exceptional', '2026-02-10 22:25:08', NULL),
(46, 4, 4, 'Intermediate', 'Advanced', '2026-02-17 22:25:08', NULL),
(47, 4, 5, 'Intermediate', 'Advanced', '2026-02-17 22:25:08', NULL),
(48, 5, 8, 'Advanced', 'Expert', '2026-02-23 22:25:08', NULL),
(49, 5, 1, 'Intermediate', 'Advanced', '2026-02-23 22:25:08', NULL),
(50, 6, 7, 'Intermediate', 'Advanced', '2026-02-27 22:25:08', NULL),
(51, 6, 2, 'Intermediate', 'Advanced', '2026-02-27 22:25:08', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `career_paths`
--
ALTER TABLE `career_paths`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `fk_career_created_by` (`created_by`);

--
-- Indexes for table `competencies`
--
ALTER TABLE `competencies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `compliance_assignments`
--
ALTER TABLE `compliance_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_compliance_assignment` (`user_id`,`compliance_training_id`),
  ADD KEY `compliance_training_id` (`compliance_training_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_due_date` (`due_date`);

--
-- Indexes for table `compliance_trainings`
--
ALTER TABLE `compliance_trainings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_compliance_type` (`compliance_type`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `employee_settlements`
--
ALTER TABLE `employee_settlements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_settlement_employee` (`employee_id`),
  ADD KEY `fk_settlement_resignation` (`resignation_id`),
  ADD KEY `fk_settlement_approved_by` (`approved_by`),
  ADD KEY `fk_settlement_created_by` (`created_by`);

--
-- Indexes for table `exit_documents`
--
ALTER TABLE `exit_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_document_employee` (`employee_id`),
  ADD KEY `fk_document_uploaded_by` (`uploaded_by`);

--
-- Indexes for table `exit_interviews`
--
ALTER TABLE `exit_interviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_interview_employee` (`employee_id`),
  ADD KEY `fk_interview_interviewer` (`interviewer_id`);

--
-- Indexes for table `exit_surveys`
--
ALTER TABLE `exit_surveys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_survey_created_by` (`created_by`);

--
-- Indexes for table `feedback_360`
--
ALTER TABLE `feedback_360`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviewer_id` (`reviewer_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_reviewer_type` (`reviewer_type`);

--
-- Indexes for table `individual_development_plans`
--
ALTER TABLE `individual_development_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_career_path` (`career_path_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `knowledge_transfer_items`
--
ALTER TABLE `knowledge_transfer_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_item_plan` (`plan_id`);

--
-- Indexes for table `knowledge_transfer_plans`
--
ALTER TABLE `knowledge_transfer_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_transfer_employee` (`employee_id`),
  ADD KEY `fk_transfer_successor` (`successor_id`),
  ADD KEY `fk_transfer_created_by` (`created_by`);

--
-- Indexes for table `leadership_enrollments`
--
ALTER TABLE `leadership_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_leadership_enrollment` (`user_id`,`program_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `leadership_programs`
--
ALTER TABLE `leadership_programs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `fk_leadership_created_by` (`created_by`);

--
-- Indexes for table `lms_courses`
--
ALTER TABLE `lms_courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `instructor_id` (`instructor_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `lms_enrollments`
--
ALTER TABLE `lms_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_lms_enrollment` (`user_id`,`course_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `performance_reviews`
--
ALTER TABLE `performance_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviewer_id` (`reviewer_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `resignations`
--
ALTER TABLE `resignations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_resignation_employee` (`employee_id`),
  ADD KEY `fk_resignation_submitted_by` (`submitted_by`),
  ADD KEY `fk_resignation_approved_by` (`approved_by`);

--
-- Indexes for table `succession_plans`
--
ALTER TABLE `succession_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `current_holder_id` (`current_holder_id`),
  ADD KEY `successor_id` (`successor_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `survey_answers`
--
ALTER TABLE `survey_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_answer_response` (`response_id`),
  ADD KEY `fk_answer_question` (`question_id`);

--
-- Indexes for table `survey_questions`
--
ALTER TABLE `survey_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_question_survey` (`survey_id`);

--
-- Indexes for table `survey_responses`
--
ALTER TABLE `survey_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_response_survey` (`survey_id`),
  ADD KEY `fk_response_employee` (`employee_id`);

--
-- Indexes for table `team_activities`
--
ALTER TABLE `team_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organizer_id` (`organizer_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_activity_date` (`activity_date`);

--
-- Indexes for table `team_activity_participants`
--
ALTER TABLE `team_activity_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_participation` (`user_id`,`activity_id`),
  ADD KEY `activity_id` (`activity_id`);

--
-- Indexes for table `training_enrollments`
--
ALTER TABLE `training_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_program` (`user_id`,`program_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `fk_enrollment_program` (`program_id`);

--
-- Indexes for table `training_logs`
--
ALTER TABLE `training_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `training_programs`
--
ALTER TABLE `training_programs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `fk_manager` (`manager_id`);

--
-- Indexes for table `user_competencies`
--
ALTER TABLE `user_competencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_competency` (`user_id`,`competency_id`),
  ADD KEY `competency_id` (`competency_id`),
  ADD KEY `assessed_by` (`assessed_by`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `career_paths`
--
ALTER TABLE `career_paths`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `competencies`
--
ALTER TABLE `competencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `compliance_assignments`
--
ALTER TABLE `compliance_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `compliance_trainings`
--
ALTER TABLE `compliance_trainings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `employee_settlements`
--
ALTER TABLE `employee_settlements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `exit_documents`
--
ALTER TABLE `exit_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `exit_interviews`
--
ALTER TABLE `exit_interviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exit_surveys`
--
ALTER TABLE `exit_surveys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `feedback_360`
--
ALTER TABLE `feedback_360`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `individual_development_plans`
--
ALTER TABLE `individual_development_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `knowledge_transfer_items`
--
ALTER TABLE `knowledge_transfer_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `knowledge_transfer_plans`
--
ALTER TABLE `knowledge_transfer_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `leadership_enrollments`
--
ALTER TABLE `leadership_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `leadership_programs`
--
ALTER TABLE `leadership_programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `lms_courses`
--
ALTER TABLE `lms_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `lms_enrollments`
--
ALTER TABLE `lms_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `performance_reviews`
--
ALTER TABLE `performance_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `resignations`
--
ALTER TABLE `resignations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `succession_plans`
--
ALTER TABLE `succession_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `survey_answers`
--
ALTER TABLE `survey_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `survey_questions`
--
ALTER TABLE `survey_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `survey_responses`
--
ALTER TABLE `survey_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `team_activities`
--
ALTER TABLE `team_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `team_activity_participants`
--
ALTER TABLE `team_activity_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=256;

--
-- AUTO_INCREMENT for table `training_enrollments`
--
ALTER TABLE `training_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=330;

--
-- AUTO_INCREMENT for table `training_logs`
--
ALTER TABLE `training_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `training_programs`
--
ALTER TABLE `training_programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_competencies`
--
ALTER TABLE `user_competencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `career_paths`
--
ALTER TABLE `career_paths`
  ADD CONSTRAINT `career_paths_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_career_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `compliance_assignments`
--
ALTER TABLE `compliance_assignments`
  ADD CONSTRAINT `compliance_assignments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `compliance_assignments_ibfk_2` FOREIGN KEY (`compliance_training_id`) REFERENCES `compliance_trainings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `compliance_trainings`
--
ALTER TABLE `compliance_trainings`
  ADD CONSTRAINT `compliance_trainings_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `feedback_360`
--
ALTER TABLE `feedback_360`
  ADD CONSTRAINT `feedback_360_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `feedback_360_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `individual_development_plans`
--
ALTER TABLE `individual_development_plans`
  ADD CONSTRAINT `fk_idp_career_path` FOREIGN KEY (`career_path_id`) REFERENCES `career_paths` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_idp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leadership_enrollments`
--
ALTER TABLE `leadership_enrollments`
  ADD CONSTRAINT `leadership_enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leadership_enrollments_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `leadership_programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leadership_programs`
--
ALTER TABLE `leadership_programs`
  ADD CONSTRAINT `fk_leadership_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leadership_programs_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `lms_courses`
--
ALTER TABLE `lms_courses`
  ADD CONSTRAINT `lms_courses_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lms_enrollments`
--
ALTER TABLE `lms_enrollments`
  ADD CONSTRAINT `lms_enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lms_enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `lms_courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `performance_reviews`
--
ALTER TABLE `performance_reviews`
  ADD CONSTRAINT `performance_reviews_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `performance_reviews_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `succession_plans`
--
ALTER TABLE `succession_plans`
  ADD CONSTRAINT `succession_plans_ibfk_1` FOREIGN KEY (`current_holder_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `succession_plans_ibfk_2` FOREIGN KEY (`successor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `team_activities`
--
ALTER TABLE `team_activities`
  ADD CONSTRAINT `team_activities_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `team_activity_participants`
--
ALTER TABLE `team_activity_participants`
  ADD CONSTRAINT `team_activity_participants_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `team_activity_participants_ibfk_2` FOREIGN KEY (`activity_id`) REFERENCES `team_activities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `training_enrollments`
--
ALTER TABLE `training_enrollments`
  ADD CONSTRAINT `fk_enrollment_program` FOREIGN KEY (`program_id`) REFERENCES `training_programs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_enrollment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `training_logs`
--
ALTER TABLE `training_logs`
  ADD CONSTRAINT `training_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `training_programs`
--
ALTER TABLE `training_programs`
  ADD CONSTRAINT `fk_training_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_manager` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_competencies`
--
ALTER TABLE `user_competencies`
  ADD CONSTRAINT `user_competencies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_competencies_ibfk_2` FOREIGN KEY (`competency_id`) REFERENCES `competencies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_competencies_ibfk_3` FOREIGN KEY (`assessed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
