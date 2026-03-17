-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 17, 2026 at 09:57 PM
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
(27, 'dsadas', 'dasdasasdadasda', '421asd', '321', '[\"dasasd\"]', 12, 'active', 7, '2026-03-17 20:07:27', '2026-03-17 20:07:27', 'uploads/career/img_69b9b47fead3a1.50399250.jpg');

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
(1, 'Communication', 'Effective verbal and written communication', 'Soft Skills', '[\"Basic\", \"Intermediate\", \"Advanced\", \"Expert\"]', '2026-03-15 17:19:02'),
(2, 'Technical Skills', 'Programming and technical expertise', 'Technical', '[\"Beginner\", \"Intermediate\", \"Advanced\", \"Expert\"]', '2026-03-15 17:19:02');

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
(1, 'Code of Conduct Training', 'Annual code of conduct review', 'Ethics', NULL, NULL, 1, 1, '2026-03-15 17:19:01', NULL),
(2, 'Data Privacy Compliance', 'GDPR and data protection training', 'Privacy', NULL, NULL, 1, 1, '2026-03-15 17:19:01', NULL),
(3, 'sadasd', 'dasadsdsa', 'dasdasd', '2026-03-01', NULL, 1, 7, '2026-03-16 14:42:43', NULL);

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
(1, 4, 1, 'manager', 4.50, 'Strong leadership skills', '2026-03-15 17:19:02'),
(2, 4, 7, 'peer', 4.00, 'Collaborative team player', '2026-03-15 17:19:02');

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
(33, 7, 27, '0000-00-00', '0000-00-00', NULL, NULL, '', '2026-03-17 20:14:03', '2026-03-17 20:14:03', NULL);

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
(39, 7, 34, '2026-03-17 20:13:56', NULL, NULL, NULL, 'pending', NULL, 0, 0, NULL);

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
(33, 'dsadasdas', 'dadasdasd', 'Intermediate', 'dsadsad', 213, 'dsadasdas', '[\"dasdsad\"]', 7, 'active', '2026-03-17 20:07:52', '2026-03-17 20:07:52', 'uploads/leadership/img_69b9b4986f7d74.26893916.jpg'),
(34, '2345678vstvserfgthjkkkkkkkkkkkkkkk', 'dcawdcasf', 'Foundation', 'ecawceaw', 43, 'ecawec', '[\"cawecawec\",\"awve\",\"waeva\",\"retbr\"]', 7, 'active', '2026-03-17 20:13:49', '2026-03-17 20:13:49', 'uploads/leadership/img_69b9b5fd8c7597.91735192.jpg');

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
(1, 'Introduction to Leadership', 'Basic leadership skills course', 'Leadership', 1, NULL, NULL, 'published', '2026-03-15 17:19:01', '2026-03-15 17:19:01'),
(2, 'Communication Skills', 'Effective communication techniques', 'Professional Development', 1, NULL, NULL, 'published', '2026-03-15 17:19:01', '2026-03-15 17:19:01');

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
(1, 4, 1, '2026-03-15 17:19:01', NULL, 50, NULL, 'in_progress'),
(2, 7, 2, '2026-03-15 17:19:01', NULL, 100, 95.00, 'completed');

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
(1, 4, 1, '2026-01-01', '2026-03-01', 4.50, 'Excellent performance in Q1', '2026-03-15 17:19:02', 'completed', '2026-03-15 17:19:02', NULL),
(2, 7, 1, '2026-01-01', '2026-03-01', 4.00, 'Good progress, needs improvement in communication', '2026-03-15 17:19:02', 'completed', '2026-03-15 17:19:02', NULL);

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
(21, 'dsadasd', 'dasdadsa', '2026-03-17', 'asdd', 7, 321.00, 214, 'planned', '2026-03-17 20:08:12', NULL),
(22, 'tatat', 'atatat', '2026-03-18', 'dasdsa', 7, 2134.00, 123, 'planned', '2026-03-17 20:12:39', NULL),
(23, 'Annual Team Building Retreat', 'Full-day team building and strategic planning session for all departments', '2026-04-16', 'All Departments', NULL, 5000.00, 12, 'planned', '2026-03-17 20:18:42', NULL),
(24, 'Cross-Functional Collaboration Workshop', 'Workshop to improve communication and collaboration across teams', '2026-04-11', 'HR & Operations', NULL, 2500.00, 8, 'planned', '2026-03-17 20:18:42', NULL),
(25, 'Innovation Hackathon 2026', 'Interactive hackathon to generate innovative ideas for organizational improvements', '2026-05-01', 'Technology', NULL, 3500.00, 15, 'planned', '2026-03-17 20:18:42', NULL),
(26, 'Diversity & Inclusion Initiative', 'Program promoting diversity and inclusive workplace culture', '2026-04-01', 'HR', NULL, 1500.00, 20, 'planned', '2026-03-17 20:18:42', NULL),
(27, 'Wellness & Work-Life Balance Program', 'Comprehensive wellness initiative focusing on employee well-being', '2026-04-06', 'HR', NULL, 3000.00, 35, 'planned', '2026-03-17 20:18:42', NULL),
(28, 'Mentoring Program Launch', 'Formal mentoring program connecting senior and junior staff', '2026-04-21', 'All Departments', NULL, 1000.00, 18, 'planned', '2026-03-17 20:18:42', NULL),
(29, 'Quarterly Knowledge Sharing Session', 'Monthly sessions where employees share expertise and best practices', '2026-03-27', 'Organization', NULL, 500.00, 25, 'planned', '2026-03-17 20:18:42', NULL),
(30, 'Community Outreach Initiative', 'Corporate social responsibility program for community involvement', '2026-05-06', 'CSR', NULL, 2000.00, 10, 'planned', '2026-03-17 20:18:42', NULL),
(31, 'Environmental Sustainability Project', 'Go-green initiative to promote environmental responsibility', '2026-04-26', 'Operations', NULL, 4000.00, 22, 'planned', '2026-03-17 20:18:42', NULL),
(32, 'Leadership Development Circle', 'Monthly discussion group for emerging leaders to develop professionally', '2026-03-22', 'Management', NULL, 800.00, 14, 'planned', '2026-03-17 20:18:42', NULL),
(33, 'Skills Development Workshop Series', 'Multi-week workshop covering essential professional skills', '2026-04-08', 'Training', NULL, 3500.00, 30, 'planned', '2026-03-17 20:18:42', NULL),
(34, 'Employee Recognition Program', 'Celebration and recognition of outstanding employee contributions', '2026-04-04', 'HR', NULL, 1200.00, 50, 'planned', '2026-03-17 20:18:42', NULL),
(35, 'Tech Innovation Lab', 'Collaborative space for exploring emerging technologies', '2026-05-16', 'Technology', NULL, 5500.00, 16, 'planned', '2026-03-17 20:18:42', NULL),
(36, 'Customer Experience Improvement', 'Initiative to enhance customer satisfaction and loyalty', '2026-04-14', 'Operations', NULL, 2200.00, 19, 'planned', '2026-03-17 20:18:42', NULL),
(37, 'Process Improvement Kaizen', 'Continuous improvement methodology implementation sessions', '2026-04-18', 'Operations', NULL, 1800.00, 24, 'planned', '2026-03-17 20:18:42', NULL),
(38, 'Health & Safety Awareness Campaign', 'Comprehensive health, safety, and wellness awareness program', '2026-03-29', 'Safety', NULL, 2500.00, 28, 'planned', '2026-03-17 20:18:42', NULL),
(39, 'Digital Transformation Roadshow', 'Series of sessions introducing digital tools and processes', '2026-04-24', 'Technology', NULL, 3200.00, 32, 'planned', '2026-03-17 20:18:42', NULL),
(40, 'Team Sports & Recreation', 'Organized sports activities and recreation for team bonding', '2026-03-25', 'HR', NULL, 2000.00, 40, 'planned', '2026-03-17 20:18:42', NULL),
(41, 'Performance Excellence Program', 'Program focusing on highest performance standards and metrics', '2026-04-28', 'Management', NULL, 2800.00, 21, 'planned', '2026-03-17 20:18:42', NULL),
(42, 'Social Responsibility Week', 'Week-long initiative for various charitable and social causes', '2026-05-11', 'CSR', NULL, 3300.00, 45, 'planned', '2026-03-17 20:18:42', NULL);

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
(11, 1, 21, 'confirmed', '2026-02-25 21:19:19'),
(12, 1, 25, 'confirmed', '2026-03-02 21:19:19'),
(13, 2, 22, 'confirmed', '2026-02-27 21:19:19'),
(14, 2, 27, 'confirmed', '2026-03-05 21:19:19'),
(15, 3, 23, 'confirmed', '2026-02-20 21:19:19'),
(16, 3, 31, 'confirmed', '2026-03-07 21:19:19'),
(17, 4, 24, 'confirmed', '2026-02-23 21:19:19'),
(18, 4, 29, 'confirmed', '2026-03-09 20:19:19'),
(19, 5, 26, 'confirmed', '2026-02-15 21:19:19'),
(20, 5, 33, 'confirmed', '2026-03-12 20:19:19'),
(21, 6, 28, 'confirmed', '2026-02-17 21:19:19'),
(22, 1, 30, 'confirmed', '2026-03-01 21:19:19'),
(23, 2, 34, 'confirmed', '2026-03-03 21:19:19'),
(24, 3, 26, 'confirmed', '2026-02-25 21:19:19'),
(25, 4, 32, 'confirmed', '2026-02-26 21:19:19'),
(26, 7, 21, 'confirmed', '2026-03-17 20:20:02');

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
(136, 1, 77, 'pending', '2026-03-02 20:33:14', NULL, NULL, 35, 0, NULL),
(137, 1, 78, 'pending', '2026-03-07 20:33:14', NULL, NULL, 50, 0, NULL),
(138, 1, 79, 'completed', '2026-02-15 20:33:14', NULL, NULL, 100, 0, NULL),
(139, 2, 77, 'approved', '2026-02-25 20:33:14', NULL, NULL, 60, 0, NULL),
(140, 2, 80, 'pending', '2026-03-09 19:33:14', NULL, NULL, 30, 0, NULL),
(141, 3, 78, 'pending', '2026-03-05 20:33:14', NULL, NULL, 45, 0, NULL),
(142, 3, 81, 'approved', '2026-03-02 20:33:14', NULL, NULL, 70, 0, NULL),
(143, 4, 77, 'pending', '2026-03-12 19:33:14', NULL, NULL, 10, 0, NULL),
(144, 4, 79, 'pending', '2026-02-27 20:33:14', NULL, NULL, 25, 0, NULL),
(145, 5, 78, 'completed', '2026-01-16 20:33:14', NULL, NULL, 100, 0, NULL),
(146, 5, 80, 'pending', '2026-03-10 19:33:14', NULL, NULL, 55, 0, NULL),
(147, 6, 81, '', '2026-03-03 20:33:14', NULL, NULL, 40, 0, NULL),
(148, 7, 82, 'pending', '2026-03-17 19:34:24', NULL, NULL, 0, 0, NULL),
(149, 7, 83, 'pending', '2026-03-17 19:52:33', NULL, NULL, 0, 0, NULL),
(150, 7, 84, 'pending', '2026-03-17 19:52:34', NULL, NULL, 0, 0, NULL);

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
(77, 'Leadership Bootcamp', 'Develop leadership skills for modern managers', 'Leadership', 'Workshop', 5, NULL, 'Active', '2026-03-17 19:32:11', '2026-03-17 19:32:11', NULL),
(78, 'Technical Skills Training', 'Advanced technical knowledge and skills', 'Technical', 'Course', 8, NULL, 'Active', '2026-03-17 19:32:11', '2026-03-17 19:32:11', NULL),
(79, 'Communication Excellence', 'Enhance workplace communication abilities', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 19:32:11', '2026-03-17 19:32:11', NULL),
(80, 'Project Management Essentials', 'Learn project management methodologies', 'Management', 'Course', 6, NULL, 'Active', '2026-03-17 19:32:11', '2026-03-17 19:32:11', NULL),
(81, 'Customer Service Training', 'Develop exceptional customer service skills', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 19:32:11', '2026-03-17 19:32:11', NULL),
(82, 'Leadership Bootcamp', 'Develop leadership skills for modern managers', 'Leadership', 'Workshop', 5, NULL, 'Active', '2026-03-17 19:32:47', '2026-03-17 19:32:47', NULL),
(83, 'Technical Skills Training', 'Advanced technical knowledge and skills', 'Technical', 'Course', 8, NULL, 'Active', '2026-03-17 19:32:47', '2026-03-17 19:32:47', NULL),
(84, 'Communication Excellence', 'Enhance workplace communication abilities', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 19:32:47', '2026-03-17 19:32:47', NULL),
(85, 'Project Management Essentials', 'Learn project management methodologies', 'Management', 'Course', 6, NULL, 'Active', '2026-03-17 19:32:47', '2026-03-17 19:32:47', NULL),
(86, 'Customer Service Training', 'Develop exceptional customer service skills', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 19:32:47', '2026-03-17 19:32:47', NULL),
(87, 'Advanced Leadership Skills', 'Learn advanced leadership techniques and management strategies to lead teams effectively', 'Leadership', 'Workshop', 5, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(88, 'Technical Project Management', 'Master the fundamentals of project management using industry-standard methodologies', 'Management', 'Course', 8, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(89, 'Communication Excellence', 'Enhance your communication skills for better professional interactions', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(90, 'Data Analysis & Excel Mastery', 'Comprehensive training on data analysis tools and advanced Excel functions', 'Technical', 'Course', 6, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(91, 'Customer Service Excellence', 'Develop exceptional customer service skills to improve client satisfaction', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(92, 'Digital Marketing Fundamentals', 'Learn the basics of digital marketing and social media strategies', 'Marketing', 'Course', 7, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(93, 'Financial Analysis for Non-Finance', 'Understanding financial statements and business metrics for non-finance professionals', 'Finance', 'Course', 5, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(94, 'Effective Negotiation Tactics', 'Master negotiation techniques for better business outcomes', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(95, 'Time Management & Productivity', 'Improve productivity and manage time effectively in a fast-paced environment', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(96, 'Creativity & Innovation in Business', 'Unlock your creative potential and drive innovation in your organization', 'Soft Skills', 'Workshop', 4, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(97, 'Six Sigma Green Belt', 'Process improvement certification focusing on quality management', 'Operations', 'Course', 10, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(98, 'Agile Methodology Bootcamp', 'Learn Agile principles and practices for software development teams', 'Technical', 'Bootcamp', 6, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(99, 'Strategic Business Planning', 'Develop skills in strategic planning and competitive analysis', 'Management', 'Course', 7, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(100, 'Supply Chain Management', 'Comprehensive overview of supply chain operations and optimization', 'Operations', 'Course', 8, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(101, 'Quality Management Systems', 'ISO 9001 and quality management fundamentals', 'Operations', 'Workshop', 4, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(102, 'Business Ethics & Compliance', 'Understanding ethical practices and regulatory compliance requirements', 'Compliance', 'Course', 3, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(103, 'Advanced Public Speaking', 'Master the art of public speaking and presentation skills', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(104, 'Conflict Resolution Workshop', 'Learn techniques to resolve workplace conflicts effectively', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(105, 'Cloud Computing Essentials', 'Introduction to cloud platforms and cloud-based solutions', 'Technical', 'Course', 6, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(106, 'Cybersecurity Fundamentals', 'Protect your organization from cyber threats and security breaches', 'Technical', 'Course', 5, NULL, 'Active', '2026-03-17 20:02:28', '2026-03-17 20:02:28', NULL),
(107, 'Advanced Leadership Skills', 'Learn advanced leadership techniques and management strategies to lead teams effectively', 'Leadership', 'Workshop', 5, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(108, 'Technical Project Management', 'Master the fundamentals of project management using industry-standard methodologies', 'Management', 'Course', 8, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(109, 'Communication Excellence', 'Enhance your communication skills for better professional interactions', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(110, 'Data Analysis & Excel Mastery', 'Comprehensive training on data analysis tools and advanced Excel functions', 'Technical', 'Course', 6, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(111, 'Customer Service Excellence', 'Develop exceptional customer service skills to improve client satisfaction', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(112, 'Digital Marketing Fundamentals', 'Learn the basics of digital marketing and social media strategies', 'Marketing', 'Course', 7, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(113, 'Financial Analysis for Non-Finance', 'Understanding financial statements and business metrics for non-finance professionals', 'Finance', 'Course', 5, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(114, 'Effective Negotiation Tactics', 'Master negotiation techniques for better business outcomes', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(115, 'Time Management & Productivity', 'Improve productivity and manage time effectively in a fast-paced environment', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(116, 'Creativity & Innovation in Business', 'Unlock your creative potential and drive innovation in your organization', 'Soft Skills', 'Workshop', 4, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(117, 'Six Sigma Green Belt', 'Process improvement certification focusing on quality management', 'Operations', 'Course', 10, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(118, 'Agile Methodology Bootcamp', 'Learn Agile principles and practices for software development teams', 'Technical', 'Bootcamp', 6, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(119, 'Strategic Business Planning', 'Develop skills in strategic planning and competitive analysis', 'Management', 'Course', 7, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(120, 'Supply Chain Management', 'Comprehensive overview of supply chain operations and optimization', 'Operations', 'Course', 8, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(121, 'Quality Management Systems', 'ISO 9001 and quality management fundamentals', 'Operations', 'Workshop', 4, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(122, 'Business Ethics & Compliance', 'Understanding ethical practices and regulatory compliance requirements', 'Compliance', 'Course', 3, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(123, 'Advanced Public Speaking', 'Master the art of public speaking and presentation skills', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(124, 'Conflict Resolution Workshop', 'Learn techniques to resolve workplace conflicts effectively', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(125, 'Cloud Computing Essentials', 'Introduction to cloud platforms and cloud-based solutions', 'Technical', 'Course', 6, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(126, 'Cybersecurity Fundamentals', 'Protect your organization from cyber threats and security breaches', 'Technical', 'Course', 5, NULL, 'Active', '2026-03-17 20:09:26', '2026-03-17 20:09:26', NULL),
(127, 'Advanced Leadership Skills', 'Learn advanced leadership techniques and management strategies to lead teams effectively', 'Leadership', 'Workshop', 5, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(128, 'Technical Project Management', 'Master the fundamentals of project management using industry-standard methodologies', 'Management', 'Course', 8, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(129, 'Communication Excellence', 'Enhance your communication skills for better professional interactions', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(130, 'Data Analysis & Excel Mastery', 'Comprehensive training on data analysis tools and advanced Excel functions', 'Technical', 'Course', 6, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(131, 'Customer Service Excellence', 'Develop exceptional customer service skills to improve client satisfaction', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(132, 'Digital Marketing Fundamentals', 'Learn the basics of digital marketing and social media strategies', 'Marketing', 'Course', 7, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(133, 'Financial Analysis for Non-Finance', 'Understanding financial statements and business metrics for non-finance professionals', 'Finance', 'Course', 5, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(134, 'Effective Negotiation Tactics', 'Master negotiation techniques for better business outcomes', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(135, 'Time Management & Productivity', 'Improve productivity and manage time effectively in a fast-paced environment', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(136, 'Creativity & Innovation in Business', 'Unlock your creative potential and drive innovation in your organization', 'Soft Skills', 'Workshop', 4, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(137, 'Six Sigma Green Belt', 'Process improvement certification focusing on quality management', 'Operations', 'Course', 10, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(138, 'Agile Methodology Bootcamp', 'Learn Agile principles and practices for software development teams', 'Technical', 'Bootcamp', 6, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(139, 'Strategic Business Planning', 'Develop skills in strategic planning and competitive analysis', 'Management', 'Course', 7, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(140, 'Supply Chain Management', 'Comprehensive overview of supply chain operations and optimization', 'Operations', 'Course', 8, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(141, 'Quality Management Systems', 'ISO 9001 and quality management fundamentals', 'Operations', 'Workshop', 4, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(142, 'Business Ethics & Compliance', 'Understanding ethical practices and regulatory compliance requirements', 'Compliance', 'Course', 3, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(143, 'Advanced Public Speaking', 'Master the art of public speaking and presentation skills', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(144, 'Conflict Resolution Workshop', 'Learn techniques to resolve workplace conflicts effectively', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(145, 'Cloud Computing Essentials', 'Introduction to cloud platforms and cloud-based solutions', 'Technical', 'Course', 6, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(146, 'Cybersecurity Fundamentals', 'Protect your organization from cyber threats and security breaches', 'Technical', 'Course', 5, NULL, 'Active', '2026-03-17 20:12:16', '2026-03-17 20:12:16', NULL),
(147, 'Advanced Leadership Skills', 'Learn advanced leadership techniques and management strategies to lead teams effectively', 'Leadership', 'Workshop', 5, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(148, 'Technical Project Management', 'Master the fundamentals of project management using industry-standard methodologies', 'Management', 'Course', 8, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(149, 'Communication Excellence', 'Enhance your communication skills for better professional interactions', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(150, 'Data Analysis & Excel Mastery', 'Comprehensive training on data analysis tools and advanced Excel functions', 'Technical', 'Course', 6, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(151, 'Customer Service Excellence', 'Develop exceptional customer service skills to improve client satisfaction', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(152, 'Digital Marketing Fundamentals', 'Learn the basics of digital marketing and social media strategies', 'Marketing', 'Course', 7, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(153, 'Financial Analysis for Non-Finance', 'Understanding financial statements and business metrics for non-finance professionals', 'Finance', 'Course', 5, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(154, 'Effective Negotiation Tactics', 'Master negotiation techniques for better business outcomes', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(155, 'Time Management & Productivity', 'Improve productivity and manage time effectively in a fast-paced environment', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(156, 'Creativity & Innovation in Business', 'Unlock your creative potential and drive innovation in your organization', 'Soft Skills', 'Workshop', 4, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(157, 'Six Sigma Green Belt', 'Process improvement certification focusing on quality management', 'Operations', 'Course', 10, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(158, 'Agile Methodology Bootcamp', 'Learn Agile principles and practices for software development teams', 'Technical', 'Bootcamp', 6, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(159, 'Strategic Business Planning', 'Develop skills in strategic planning and competitive analysis', 'Management', 'Course', 7, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(160, 'Supply Chain Management', 'Comprehensive overview of supply chain operations and optimization', 'Operations', 'Course', 8, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(161, 'Quality Management Systems', 'ISO 9001 and quality management fundamentals', 'Operations', 'Workshop', 4, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(162, 'Business Ethics & Compliance', 'Understanding ethical practices and regulatory compliance requirements', 'Compliance', 'Course', 3, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(163, 'Advanced Public Speaking', 'Master the art of public speaking and presentation skills', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(164, 'Conflict Resolution Workshop', 'Learn techniques to resolve workplace conflicts effectively', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(165, 'Cloud Computing Essentials', 'Introduction to cloud platforms and cloud-based solutions', 'Technical', 'Course', 6, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(166, 'Cybersecurity Fundamentals', 'Protect your organization from cyber threats and security breaches', 'Technical', 'Course', 5, NULL, 'Active', '2026-03-17 20:18:03', '2026-03-17 20:18:03', NULL),
(167, 'Advanced Leadership Skills', 'Learn advanced leadership techniques and management strategies to lead teams effectively', 'Leadership', 'Workshop', 5, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-1.gif'),
(168, 'Technical Project Management', 'Master the fundamentals of project management using industry-standard methodologies', 'Management', 'Course', 8, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-2.gif'),
(169, 'Communication Excellence', 'Enhance your communication skills for better professional interactions', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-3.gif'),
(170, 'Data Analysis & Excel Mastery', 'Comprehensive training on data analysis tools and advanced Excel functions', 'Technical', 'Course', 6, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-4.gif'),
(171, 'Customer Service Excellence', 'Develop exceptional customer service skills to improve client satisfaction', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-5.gif'),
(172, 'Digital Marketing Fundamentals', 'Learn the basics of digital marketing and social media strategies', 'Marketing', 'Course', 7, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-6.gif'),
(173, 'Financial Analysis for Non-Finance', 'Understanding financial statements and business metrics for non-finance professionals', 'Finance', 'Course', 5, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-7.gif'),
(174, 'Effective Negotiation Tactics', 'Master negotiation techniques for better business outcomes', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-8.gif'),
(175, 'Time Management & Productivity', 'Improve productivity and manage time effectively in a fast-paced environment', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-9.gif'),
(176, 'Creativity & Innovation in Business', 'Unlock your creative potential and drive innovation in your organization', 'Soft Skills', 'Workshop', 4, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-10.gif'),
(177, 'Six Sigma Green Belt', 'Process improvement certification focusing on quality management', 'Operations', 'Course', 10, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-11.gif'),
(178, 'Agile Methodology Bootcamp', 'Learn Agile principles and practices for software development teams', 'Technical', 'Bootcamp', 6, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-12.gif'),
(179, 'Strategic Business Planning', 'Develop skills in strategic planning and competitive analysis', 'Management', 'Course', 7, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-13.gif'),
(180, 'Supply Chain Management', 'Comprehensive overview of supply chain operations and optimization', 'Operations', 'Course', 8, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-14.gif'),
(181, 'Quality Management Systems', 'ISO 9001 and quality management fundamentals', 'Operations', 'Workshop', 4, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-15.gif'),
(182, 'Business Ethics & Compliance', 'Understanding ethical practices and regulatory compliance requirements', 'Compliance', 'Course', 3, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-16.gif'),
(183, 'Advanced Public Speaking', 'Master the art of public speaking and presentation skills', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-17.gif'),
(184, 'Conflict Resolution Workshop', 'Learn techniques to resolve workplace conflicts effectively', 'Soft Skills', 'Workshop', 2, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-18.gif'),
(185, 'Cloud Computing Essentials', 'Introduction to cloud platforms and cloud-based solutions', 'Technical', 'Course', 6, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-19.gif'),
(186, 'Cybersecurity Fundamentals', 'Protect your organization from cyber threats and security breaches', 'Technical', 'Course', 5, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-20.gif'),
(187, 'DevOps Best Practices', 'Learn containerization, CI/CD pipelines, and infrastructure as code', 'Technical', 'Bootcamp', 8, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-21.gif'),
(188, 'Machine Learning Fundamentals', 'Introduction to machine learning algorithms and practical applications', 'Technical', 'Course', 9, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-1.gif'),
(189, 'React & Modern JavaScript', 'Build dynamic web applications using React and ES6+ JavaScript', 'Technical', 'Course', 7, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-2.gif'),
(190, 'User Experience Design', 'Create intuitive and engaging user interfaces with UX principles', 'Design', 'Course', 6, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-3.gif'),
(191, 'Database Design & SQL', 'Master relational databases, queries, and optimization techniques', 'Technical', 'Course', 8, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-4.gif'),
(192, 'API Development & Integration', 'Build and integrate RESTful APIs and microservices', 'Technical', 'Course', 6, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-5.gif'),
(193, 'Advanced Excel for Analytics', 'Pivot tables, VLOOKUP, macros, and data visualization in Excel', 'Technical', 'Workshop', 4, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-6.gif'),
(194, 'Power BI & Data Visualization', 'Create interactive dashboards and reports with Power BI', 'Technical', 'Course', 5, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-7.gif'),
(195, 'Emotional Intelligence at Work', 'Develop emotional awareness and interpersonal effectiveness', 'Soft Skills', 'Workshop', 3, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-8.gif'),
(196, 'Team Collaboration & Synergy', 'Build high-performing teams through effective collaboration', 'Management', 'Workshop', 3, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-9.gif'),
(197, 'Presentation Skills Mastery', 'Create compelling presentations and deliver impactful messages', 'Soft Skills', 'Workshop', 4, NULL, 'Active', '2026-03-17 20:56:15', '2026-03-17 20:56:15', 'modules/img/gifholder/gifholder-10.gif');

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
(1, 4, 1, 'Advanced', 'Expert', '2026-03-01 00:00:00', 1),
(2, 7, 2, 'Intermediate', 'Advanced', '2026-03-01 00:00:00', 1);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `competencies`
--
ALTER TABLE `competencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `compliance_assignments`
--
ALTER TABLE `compliance_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `compliance_trainings`
--
ALTER TABLE `compliance_trainings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `individual_development_plans`
--
ALTER TABLE `individual_development_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `leadership_programs`
--
ALTER TABLE `leadership_programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `lms_courses`
--
ALTER TABLE `lms_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lms_enrollments`
--
ALTER TABLE `lms_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `performance_reviews`
--
ALTER TABLE `performance_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `team_activity_participants`
--
ALTER TABLE `team_activity_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `training_enrollments`
--
ALTER TABLE `training_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=298;

--
-- AUTO_INCREMENT for table `training_logs`
--
ALTER TABLE `training_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `training_programs`
--
ALTER TABLE `training_programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=198;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_competencies`
--
ALTER TABLE `user_competencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
