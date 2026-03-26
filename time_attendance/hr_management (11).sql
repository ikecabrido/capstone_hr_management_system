-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 19, 2026 at 07:37 PM
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
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$x/mCwvbQhChJHA29F80iyuoz/t3PDrEDuOX156Ya.9A6UwAPmT1gq'),
(1, 'admin', '$2y$10$x/mCwvbQhChJHA29F80iyuoz/t3PDrEDuOX156Ya.9A6UwAPmT1gq');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `cover_letter` text DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `job_id`, `first_name`, `last_name`, `email`, `phone`, `address`, `summary`, `cover_letter`, `resume`, `created_at`) VALUES
(1, 6, 'Jhon Carlo', 'Garcia', 'jhoncarlogarcia30@gmail.com', '09916117933', 'Brgy.Assumption', '', '', 'uploads/resumes/1770220499_E-3-RENEWAL-FORM-NEW-2-IN-1-revised-new.pdf', '2026-02-04 15:54:59'),
(2, 6, 'Jhon Carlo', 'Garcia', 'jhoncarlogarcia40@gmail.com', '09916117933', 'Brgy.Assumption', '', '', 'uploads/resumes/1770220748_E-3-RENEWAL-FORM-NEW-2-IN-1-revised-new.pdf', '2026-02-04 15:59:08'),
(3, 6, 'Jhon Carlo', 'Garcia', 'jhoncarlogarcia50@gmail.com', '09278332466', 'Brgy.Assumption', '', '', 'uploads/resumes/1770220909_E-3-RENEWAL-FORM-NEW-2-IN-1-revised-new.pdf', '2026-02-04 16:01:49'),
(4, 6, 'Richard John', 'Capalad', 'admin@gmail.com', '09085795733', 'Caloocan', '', '', 'uploads/resumes/1770221521_E-3-RENEWAL-FORM-NEW-2-IN-1-revised-new.pdf', '2026-02-04 16:12:01'),
(5, 6, 'Mary Grace', 'Braulio', 'jhoncarlogarcia40@gmail.com', '09916117933', 'Brgy.Assumption', '', '', 'uploads/resumes/1770252421_E-3-RENEWAL-FORM-NEW-2-IN-1-revised-new.pdf', '2026-02-05 00:47:01'),
(6, 6, 'Rabi ', 'Braulio', 'rabi15braulio@gmail.com', '09916117933', 'Brgy.Assumption', '', 'DETAILS TEST 1\r\n', 'uploads/resumes/1770254017_E-3-RENEWAL-FORM-NEW-2-IN-1-revised-new.pdf', '2026-02-05 01:13:37'),
(7, 5, 'Matt Ryan', 'Garcia', 'admin@gmail.com', '09916117933', 'Brgy.Assumption', '', 'sadwa assadaw', 'uploads/resumes/1770257399_E-3-RENEWAL-FORM-NEW-2-IN-1-revised-new.pdf', '2026-02-05 02:09:59'),
(8, 1, 'Radiant', 'Fabellion', 'admin@gmail.com', '09916117933', 'Kurba', '', 'TEST 3', 'uploads/resumes/1770270562_E-3-RENEWAL-FORM-NEW-2-IN-1-revised-new.pdf', '2026-02-05 05:49:22'),
(9, 7, 'Jenny Vie', 'Garcia', 'admin@gmail.com', '09278332466', 'Brgy.Assumption', '', 'adwadasf TEST', 'uploads/resumes/1770780444_Team_Roster.docx', '2026-02-11 03:27:24'),
(10, 10, 'Angel ', 'Fabellion', 'Angelfabellion@gmail.com', '09278332466', 'Kurba', '', 'adwda TEST', 'uploads/resumes/1773231664_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-03-11 12:21:04'),
(17, 10, 'dawd', 'asdw', 'danmark1201@gmail.com', '09556683080', 'dawd', '', 'asdawd', 'uploads/resumes/1773730051_1770780444_Team_Roster (8).docx', '2026-03-17 06:47:31'),
(18, 10, 'dawdsd', 'awdsd', 'jhoncarlogarcia30@gmail.com', '09556683080', 'adwd', '', 'adwdsd', 'uploads/resumes/1773730210_1770780444_Team_Roster (8).docx', '2026-03-17 06:50:10'),
(19, 10, 'andrei', 'balbuena', 'andrie.elbambuena1221@gmail.com', '09278332466', 'dawds', '', 'dadawd', 'uploads/resumes/1773734955_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-03-17 08:09:15'),
(20, 10, 'Charmen', 'Caril', 'carilcharmen@gmail.com', '09916117933', 'blk1 lot43', '', 'adweaeasd', 'uploads/resumes/1773748469_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-03-17 11:54:29'),
(21, 10, 'Jhon Carlo', 'Garcia', 'asdadwadsadasd@gmail.com', '09556683080', 'Brgy.Assumption', '', 'daw', 'uploads/resumes/1773754183_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-03-17 13:29:43'),
(22, 10, 'Jhon Carlo', 'Garcia', 'dawdsafwasdwd30@gmail.com', '09556683080', 'Brgy.Assumption', '', 'asdwasd', 'uploads/resumes/1773754567_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-03-17 13:36:07'),
(23, 10, 'Rabi', 'Braulio', 'rabi15braulio@gmail.com', '09556683080', 'Brgy.Assumption', '', 'test', 'uploads/resumes/1773795872_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-03-18 01:04:32'),
(24, 10, 'Richard John', 'Capalad', 'capaladrichard@gmail.com', '09853926174', '1401 Caloocan City', '', '', 'uploads/resumes/1773804138_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-03-18 03:22:18'),
(25, 10, 'radiant', 'bhada', 'terraipanger@gmail.com', '911', '1401', '', '', 'uploads/resumes/1773804332_1770780444_Team_Roster (8).docx', '2026-03-18 03:25:32'),
(26, 10, 'danmark', 'baldonido', 'danmark1201@gmail.com', '09556683080', 'Brgy.Assumption', '', 'asdwad', 'uploads/resumes/1773804943_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-03-18 03:35:43'),
(27, 10, 'Jhon Carlo', 'Garcia', 'jhoncarlogarcia40@gmail.com', '09556683080', 'Brgy.Assumption', '', 'dwasfwad', 'uploads/resumes/1773806338_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-03-18 03:58:58'),
(28, 10, 'Richard John', 'baldonido', 'jhoncarlogarcia50@gmail.com', '09853926174', 'awdsad', '', 'awdasdw', 'uploads/resumes/1773806834_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-03-18 04:07:14'),
(29, 10, 'Mary Grace', 'Braulio', 'jhoncarlogarcia40@gmail.com', '09556683080', 'Brgy.Assumption', '', 'wadsdw', 'uploads/resumes/1773806946_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-03-18 04:09:06'),
(30, 10, 'Jhon Carlo', 'Garcia', 'jhoncarlogarcia30@gmail.com', '09085795733', 'blk1 lot43', '', 'dawtasdawe', 'uploads/resumes/1773807280_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-03-18 04:14:40'),
(31, 10, 'Jhon Carlo', 'Garcia', 'palcobryan1104@gmail.com', '09085795733', 'blk1 lot43', '', 'dawtasdawe', 'uploads/resumes/1773807440_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-03-18 04:17:20'),
(32, 10, 'Jhon Carlodasd', 'Garcia', 'palcobryan1104@gmail.com', '09085795733', 'blk1 lot43', '', 'dawtasdawe', 'uploads/resumes/1773807772_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-03-18 04:22:52'),
(33, 10, 'Richard John', 'Garcia', 'jhoncarlogarcia30@gmail.com', '09556683080', 'Kurba', '', 'tsetsdfe', 'uploads/resumes/1773808019_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-03-18 04:26:59'),
(34, 10, 'Richard John', 'Garcia', 'jhoncarlogarcia30@gmail.com', '09556683080', 'Kurba', '', 'tsetsdfe', 'uploads/resumes/1773808447_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-03-18 04:34:07'),
(35, 10, 'asd', 'asd', 'danmark1201@gmail.com', 'asd', 'asd', '', 'asd', 'uploads/resumes/1773808860_1770780444_Team_Roster (8).docx', '2026-03-18 04:41:00'),
(36, 10, 'asd', 'asd', 'danmark1201@gmail.com', 'asd', 'asd', '', 'asd', 'uploads/resumes/1773809199_1770780444_Team_Roster (8).docx', '2026-03-18 04:46:39'),
(37, 10, 'Jhon Carlo', 'Garcia', 'danmark1201@gmail.com', '09556683080', 'Brgy.Assumption', '', 'zcszcx', 'uploads/resumes/1773813274_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-03-18 05:54:34'),
(38, 10, 'Jhon Carlo', 'Braulio', 'danmark1201@gmail.com', '09556683080', 'Kurba', '', 'awdasdw', 'uploads/resumes/1773814167_15_2018_EditorialArticle_PharmAnalActa_SudhaunshuPurohit.pdf', '2026-03-18 06:09:27');

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
(1, 'Technical Lead', 'Progress from senior engineer to technical leadership and team management', 'Technical Lead / Engineering Manager', '5+ years professional experience, proven technical expertise', '[\"Leadership\", \"Communication\", \"System Design\", \"Mentoring\", \"Project Management\"]', 18, 'active', 1, '2026-03-15 17:19:01', '2026-03-15 17:19:01', NULL),
(2, 'Project Manager', 'Develop skills to transition into project management and delivery leadership', 'Project Manager', '3+ years in any professional role', '[\"Planning\", \"Stakeholder Management\", \"Risk Management\", \"Agile Methodologies\", \"Communication\"]', 12, 'active', 1, '2026-03-15 17:19:01', '2026-03-15 17:19:01', NULL),
(3, 'Product Manager', 'Transition into product management with focus on strategy and customer success', 'Product Manager', 'Experience with product or customer interaction', '[\"Product Strategy\", \"Data Analysis\", \"User Research\", \"Roadmap Planning\", \"Cross-functional Leadership\"]', 15, 'active', 1, '2026-03-15 17:19:01', '2026-03-15 17:19:01', NULL),
(4, 'Subject Matter Expert', 'Develop deep expertise in a specific domain and become the go-to specialist', 'Senior Specialist / Subject Matter Expert', '2+ years experience in your domain', '[\"Deep Technical Knowledge\", \"Documentation\", \"Research\", \"Mentoring\", \"Innovation\"]', 24, 'active', 1, '2026-03-15 17:19:01', '2026-03-15 17:19:01', NULL),
(6, 'sda', 'sadasdasdasdad', 'das', 'das', '[\"ads\"]', 12, 'active', 7, '2026-03-16 16:16:44', '2026-03-16 16:16:44', NULL);

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
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `school` varchar(255) DEFAULT NULL,
  `field_of_study` varchar(255) DEFAULT NULL,
  `degree` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`id`, `application_id`, `school`, `field_of_study`, `degree`, `start_date`, `end_date`, `created_at`) VALUES
(1, 5, 'National Trade School', 'Doctor', 'test 1', '0000-00-00', '0000-00-00', '2026-02-05 00:47:01'),
(2, 6, 'National Trade School', 'Doctor', 'test 1', '2026-01-01', '2026-02-01', '2026-02-05 01:13:37'),
(3, 7, 'National ', 'Pulis', 'test 2', '2025-01-01', '2026-02-01', '2026-02-05 02:09:59'),
(4, 8, 'BCP', 'IS', 'TEST 3', '2024-02-01', '2026-02-01', '2026-02-05 05:49:22'),
(5, 9, 'National Trade School', 'Pulis', 'test 1', '2024-01-01', '2026-02-01', '2026-02-11 03:27:24');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `full_name`, `address`, `contact_number`, `email`, `department`, `position`, `date_hired`, `employment_status`, `created_at`, `updated_at`, `user_id`) VALUES
('EMP001', 'John Doe', '123 Main St', '123-456-7890', 'john.doe@example.com', 'IT', 'Software Engineer', '2023-01-01', 'Active', '2026-03-17 14:25:13', '2026-03-17 17:12:26', 11),
('EMP002', 'Jane Smith', '456 Oak Ave', '098-765-4321', 'jane.smith@example.com', 'HR', 'HR Manager', '2023-02-15', 'Active', '2026-03-17 14:25:13', '2026-03-17 14:25:13', NULL),
('EMP003', 'Mike Johnson', '789 Pine Rd', '555-123-4567', 'mike.johnson@example.com', 'Finance', 'Accountant', '2023-03-10', 'Active', '2026-03-17 14:25:13', '2026-03-17 14:25:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employees1`
--

CREATE TABLE `employees1` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `hired_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees1`
--

INSERT INTO `employees1` (`id`, `application_id`, `first_name`, `last_name`, `email`, `phone`, `hired_on`) VALUES
(1, 32, 'Jhon Carlodasd', 'Garcia', 'palcobryan1104@gmail.com', '09085795733', '2026-03-18 14:11:27');

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
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `holiday_id` int(11) NOT NULL,
  `holiday_name` varchar(100) NOT NULL,
  `holiday_date` date NOT NULL,
  `is_recurring` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 7, 6, '2026-03-16', '2027-03-16', 'dsadas', '[\"asda\"]', 'active', '2026-03-16 16:17:38', '2026-03-16 16:17:38', 7);

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
(1, 4, 1, '2026-03-15 17:19:02', NULL, NULL, NULL, 'in_progress', NULL, 0, 0, NULL),
(2, 7, 2, '2026-03-15 17:19:02', NULL, NULL, NULL, 'completed', NULL, 0, 0, NULL),
(3, 7, 3, '2026-03-16 17:16:37', NULL, NULL, NULL, 'pending', NULL, 0, 0, NULL);

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
(1, 'Executive Leadership', 'Advanced leadership for senior managers', 'Executive', 'Strategic Leadership', 8, 'Senior Managers', '[\"Strategic Thinking\", \"Change Management\"]', 1, 'active', '2026-03-15 17:19:01', '2026-03-15 17:19:01', NULL),
(2, 'Team Leadership Workshop', 'Building effective teams', 'Mid-Level', 'Team Management', 4, 'Team Leads', '[\"Communication\", \"Conflict Resolution\"]', 1, 'active', '2026-03-15 17:19:01', '2026-03-15 17:19:01', NULL),
(3, 'dsadas', 'dasdsadsa', 'Foundation', '2ad', 2, 'asdasd', '[\"asdasd\"]', 7, 'active', '2026-03-16 15:55:38', '2026-03-16 15:55:38', NULL);

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
-- Table structure for table `overtime_requests`
--

CREATE TABLE `overtime_requests` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `hours` decimal(4,2) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `request_type_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected','Cancelled','Completed') DEFAULT 'Pending',
  `admin_remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `employee_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `request_types`
--

CREATE TABLE `request_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `requires_attachment` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request_types`
--

INSERT INTO `request_types` (`id`, `name`, `description`, `icon`, `requires_attachment`, `is_active`, `created_at`) VALUES
(1, 'Leave Request', 'Request for vacation leave, sick leave, or emergency leave.', 'fa-calendar-days', 1, 1, '2026-03-01 00:54:25'),
(2, 'Training Request', 'Request approval to attend seminars, workshops, or professional training.', 'fa-graduation-cap', 1, 1, '2026-03-01 00:54:25'),
(3, 'Overtime Request', 'Request approval for overtime work beyond regular schedule.', 'fa-clock', 0, 1, '2026-03-01 00:54:25'),
(4, 'Certificate of Employment', 'Request issuance of Certificate of Employment (COE).', 'fa-file-lines', 0, 1, '2026-03-01 00:54:25'),
(5, 'Schedule Adjustment', 'Request change of teaching or working schedule.', 'fa-calendar-check', 0, 1, '2026-03-01 00:54:25'),
(7, 'sample_3', 'nigga nigga', 'fa-users', 1, 1, '2026-03-01 07:57:44');

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
-- Table structure for table `ta_attendance`
--

CREATE TABLE `ta_attendance` (
  `attendance_id` int(11) NOT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL,
  `recorded_by` enum('MANUAL','QR','SYSTEM') NOT NULL DEFAULT 'MANUAL',
  `status` enum('PRESENT','ABSENT','LATE','EARLY_OUT','PENDING_APPROVAL') NOT NULL DEFAULT 'PENDING_APPROVAL',
  `is_approved` tinyint(1) DEFAULT 0,
  `approved_by` int(11) DEFAULT NULL,
  `approval_remarks` varchar(255) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `total_hours_worked` decimal(5,2) DEFAULT NULL,
  `regular_hours` decimal(5,2) DEFAULT NULL,
  `overtime_hours` decimal(5,2) DEFAULT NULL,
  `is_within_time_window` tinyint(1) DEFAULT 1,
  `is_within_timeout_window` tinyint(1) DEFAULT 1,
  `is_within_shift_hours` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ta_attendance_tokens`
--

CREATE TABLE `ta_attendance_tokens` (
  `token_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `generated_by` int(11) NOT NULL,
  `generated_for_date` date NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `used_by` varchar(50) DEFAULT NULL,
  `used_at` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_attendance_tokens`
--

INSERT INTO `ta_attendance_tokens` (`token_id`, `token`, `generated_by`, `generated_for_date`, `expires_at`, `used`, `used_by`, `used_at`, `ip_address`, `created_at`) VALUES
(1, '043148d84415dd582b66a67aa2afcb3d3b168262a2a4df10a3c95a65342cd336', 3, '2026-03-18', '2026-03-18 00:08:54', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:07:54'),
(2, '7e2a4f39745c191d39b4d6e32984e646bf86f0d1af7578c0b0da05cf81c93e2a', 3, '2026-03-18', '2026-03-18 00:09:24', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:08:24'),
(3, 'a0b446e2246ddfce94c6948b2025ef93b43946341765db1959bdbe304d8d3d9c', 3, '2026-03-18', '2026-03-18 00:09:55', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:08:55'),
(4, '4f1ee473d39eb6dad7f8c6468f53694a6b89e6544b706113c8638dc40ab6e552', 3, '2026-03-18', '2026-03-18 00:10:26', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:09:26'),
(5, 'b387c4baf5bca16d9bb818c44e0e1ef712099ff2d8bd35ce396fe3adae6b181a', 3, '2026-03-18', '2026-03-18 00:11:26', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:10:26'),
(6, '46015fc415a8c0c3772f6e04bacecbc150e90b9657a5d7e9a456f2a545b8475d', 3, '2026-03-18', '2026-03-18 00:12:17', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:11:17'),
(7, 'c959c440222444b7e127a5596c93282c465f2b00f095aa7b1288cdeaf048925b', 3, '2026-03-18', '2026-03-18 00:12:48', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:11:48'),
(8, 'ae99b0b831594c8484df6bc320a475ddffceffc27d441b378529bcf18442acf7', 3, '2026-03-18', '2026-03-18 00:13:19', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:12:19'),
(9, '63af9e07a63cfc0c53ee37b156c3a33cb9b805dfee3f3c5104dcb49448054ac0', 3, '2026-03-18', '2026-03-18 00:14:18', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:13:18'),
(10, 'b20eb116fe63885f97fc8f4b4903c37bbaa3704d625061bf42321900f347a900', 3, '2026-03-18', '2026-03-18 00:14:57', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:13:57'),
(11, 'a03743d996ccc4090ea88141e75a185a548a45f1924ef56ac2202edefc9f4862', 3, '2026-03-18', '2026-03-18 00:15:28', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:14:28'),
(12, 'cd2e542a096725134eaf8f0e66f855202e5268493f781fd4d63516368a1f36d8', 3, '2026-03-18', '2026-03-18 00:16:04', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:15:04'),
(13, '90966563747b1c4dd9cd8ce201bf2a8d75e85fe98261be7e4e01f723cabce0b7', 3, '2026-03-18', '2026-03-18 00:17:03', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:16:03'),
(14, 'abd1e5752193ef8a4a1da6a0594473c5f930b5140381cf68fd8c18d4cd5c9ec4', 3, '2026-03-18', '2026-03-18 00:17:37', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:16:37'),
(15, '28c3a17452c451b1d57997b9551f45044a66b43d49897a99604a5a24bf265ea7', 3, '2026-03-18', '2026-03-18 00:18:09', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:17:09'),
(16, 'c8593078edd352ee35759965a7e9ef31891cb22c63e0c08aaf02b090ec999de0', 3, '2026-03-18', '2026-03-18 00:19:07', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:18:07'),
(17, '5a70d4d93e035aa4b5d3c52af4fb6d7ad70901f5f04d4e360ad00507d5a749ff', 3, '2026-03-18', '2026-03-18 00:19:56', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:18:56'),
(18, '817450609cf23c2f0a1cb6e3759821cc0f53ec7798f7e033cae97c717c512d1e', 3, '2026-03-18', '2026-03-18 00:20:27', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:19:27'),
(19, '3fbda365fc2b5724d45da317c914f3d4b3e7347aff9ac2029ccb91950ad90983', 3, '2026-03-18', '2026-03-18 00:21:22', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:20:22'),
(20, 'ee774c3df55f5dc2cdb340cfcc53423389fb803c1adf7b5179ed81528e3c1826', 3, '2026-03-18', '2026-03-18 00:22:17', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:21:17'),
(21, '4c55864b24a512c4ce8f355d8bddf12324e85a1813c65a4c05bb8211fb3103cc', 3, '2026-03-18', '2026-03-18 00:23:16', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:22:16'),
(22, 'b2ad857e32e14b8d9ed3bf313feb21f6df7bdee5b648881a97b3d48afdd57372', 3, '2026-03-18', '2026-03-18 00:23:52', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:22:52'),
(23, '45d06822fec3fb23106f9859d1a4e5f93c4f0ee23cfd26af3bfb109ab2aa48fe', 3, '2026-03-18', '2026-03-18 00:24:24', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:23:24'),
(24, 'ec9c6e1273bb0fbe3689424fdb8de216f767328d8086c57dadaa3a6ff9009d6b', 3, '2026-03-18', '2026-03-18 00:25:03', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:24:03'),
(25, '389701b71bddc93a6ab1b2a68e68cb4c22575f89fd40f822087558be7e0716cf', 3, '2026-03-18', '2026-03-18 00:25:45', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:24:45'),
(26, '079997bb11794ec6a9289a6142a81ad0dfb6f047290d39a7bbefb3a8ea5bb83c', 3, '2026-03-18', '2026-03-18 00:26:21', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:25:21'),
(27, '8058d8928de8fecfe2c6117392fc3d40f4082c9a1f85f0b7a80f627c2afe774c', 3, '2026-03-18', '2026-03-18 00:27:16', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:26:16'),
(28, '88bd721123be1b36c070ac5ebcfa4e1adcf659a4ed09ee2de6341c53ceceff73', 3, '2026-03-18', '2026-03-18 00:27:54', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:26:54'),
(29, '148232cb06ef4b88c1b46e40a754024de7473f6b7a8676085606a9572ab8dc3d', 3, '2026-03-18', '2026-03-18 00:28:50', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:27:50'),
(30, '7e14b4498b98660310e23b6c6b504c22dbd3d40ad566c800d61934794a42a21e', 3, '2026-03-18', '2026-03-18 00:29:21', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:28:21'),
(31, '3f73e0b5b9dcea4b6dfbd069a6118b4b92b8853c865d9924ef647f8faec28bdd', 3, '2026-03-18', '2026-03-18 00:30:02', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:29:02'),
(32, '606d45a38ce5edc63e0236e9d470833ed40554633695e8b1e983f492410ca7e7', 3, '2026-03-18', '2026-03-18 00:30:36', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:29:36'),
(33, '65acdd67c3ce519f5b502efd2b9e44c196c99a51c51ac9c3f0285dbb17aca783', 3, '2026-03-18', '2026-03-18 00:31:16', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:30:16'),
(34, '81d6601704e5faeca9fbcac7cf5349d96b0d78391265e681b5a6db5a396e1553', 3, '2026-03-18', '2026-03-18 00:32:13', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:31:13'),
(35, 'd52522d9328fc303a28cbfb6b1aa564a41a08dde6453496eae5db449e5270d15', 3, '2026-03-18', '2026-03-18 00:33:00', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:32:00'),
(36, '708bbc2e412acc78707d3c9c2a028a11ddfffe1e1f90984fa082d127ce8bf437', 3, '2026-03-18', '2026-03-18 00:33:59', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:32:59'),
(37, 'b0d00d1bbbf71f68fc9bb92605e734ea1491af6e070029ad823d079fb6f07d86', 3, '2026-03-18', '2026-03-18 00:34:49', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:33:49'),
(38, '32bf044c18ea88d4aa85acfca43062fc4b4e76b146698d02641875707757d31c', 3, '2026-03-18', '2026-03-18 00:35:20', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:34:20'),
(39, 'f0f36f196cdb3c0da5b4ab4e402f1ba81f6fea032167331b37fba9b15074054a', 3, '2026-03-18', '2026-03-18 00:36:11', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:35:11'),
(40, '19e3f15d410f6b10d8517b63c4bc561116f03dbe9fb3668c9240d89b0e223b25', 3, '2026-03-18', '2026-03-18 00:36:52', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:35:52'),
(41, 'eaaad30ce12189143d1c7052ea295aa6be46dfb9b08f4e18ac7053ac0d4e3c1c', 3, '2026-03-18', '2026-03-18 00:37:24', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:36:24'),
(42, '816b523aa6bb9d2ec016f99a22975187c5879eef9f6507b13ca8fbf1c8b20d14', 3, '2026-03-18', '2026-03-18 00:37:57', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:36:57'),
(43, '554f31ae2f3ac7d776c93793d1c2199cf738e3bcaf7d80e58323987b71a79d2a', 3, '2026-03-18', '2026-03-18 00:38:30', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:37:30'),
(44, '3ce3e458dd82430da3efa58525dad9c40fed0bd8967ecc92811f858a7446788b', 3, '2026-03-18', '2026-03-18 00:39:04', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:38:04'),
(45, '2220695d8e8f2d9bbc6ef6ec94da8482a2a08117723399928fd772e2453f7213', 3, '2026-03-18', '2026-03-18 00:40:04', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:39:04'),
(46, '849ca8046723a6d5dbf3da6aba1fba4b5442942388b438bc1bb7e7becbe80cdf', 3, '2026-03-18', '2026-03-18 00:40:35', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:39:35'),
(47, 'eab323bd3c93728af4a8f3c86d6359bb79ecf0889deeffffdd6fb1bb7837c758', 3, '2026-03-18', '2026-03-18 00:41:08', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:40:08'),
(48, 'a8d66b3f8132487f6b5c74a78c731f608ec9becab5302c436030e54a6ecc2e03', 3, '2026-03-18', '2026-03-18 00:42:08', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:41:08'),
(49, 'bab6b9ed881db0c98b00088cf8fe9d12301269040876ece48a14a79ba3b16aed', 3, '2026-03-18', '2026-03-18 00:42:51', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:41:51'),
(50, 'ea61c00946c37210004b5c8d7e0b74f43adc375b2e38f202f51a4ced33525c16', 3, '2026-03-18', '2026-03-18 00:43:23', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:42:23'),
(51, 'fca673092f98c1dc72ce6cb3e243521046fb872a9f7d008fdb9dc2309d007dcb', 3, '2026-03-18', '2026-03-18 00:44:11', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:43:11'),
(52, 'f1030c4458231f2bc6ca8b7619938a90689d67932d2bb1e04cc8b6a0ef2764f2', 3, '2026-03-18', '2026-03-18 00:45:10', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:44:10'),
(53, '0f75730acb44e136d4700fdef512b3dce74aee8646022e86d3452d68b342ca3c', 3, '2026-03-18', '2026-03-18 00:46:09', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:45:09'),
(54, 'e719a530d4d443d2924d4751d3dc9acb02d349095291928f136ef6793d3cb5aa', 3, '2026-03-18', '2026-03-18 00:46:40', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:45:40'),
(55, '2797f0fe045e032d524048bd78c027d6c8966af7a1cecd6094c1c7ec9763f9ac', 3, '2026-03-18', '2026-03-18 00:47:20', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:46:20'),
(56, '0be2d069dc08c00af54ced1d9c56b72e521ac3644f14a240c445f84779c62f3b', 3, '2026-03-18', '2026-03-18 00:48:11', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:47:11'),
(57, '0e875e312c530123d68d19ca891635dcf5c910a23b7d03e81ad43c797e952ba4', 3, '2026-03-18', '2026-03-18 00:48:42', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:47:42'),
(58, '8957f1b9a79454559a1fa54936454031a2d822cdd2c4b2dd18f04bc3d05d07ca', 3, '2026-03-18', '2026-03-18 00:49:41', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:48:41'),
(59, 'd7362d3f77aa71a48eea0c24f7e3ef2a3e2da649973b01a89341e430b99ceefa', 3, '2026-03-18', '2026-03-18 00:50:14', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:49:14'),
(60, '3ff7acd76973de4bc5b08e0a454c0d61c619a4a0b428b21a7b2951cde999b536', 3, '2026-03-18', '2026-03-18 00:50:45', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:49:45'),
(61, 'bae6bcb655d3666966cc0da543f21e738d00cc05310a5e7387fc21011ebab58c', 3, '2026-03-18', '2026-03-18 00:51:36', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:50:36'),
(62, 'f6c739b9e79aad8459fce81444bb9dba502d03fab08a0ffb4f9166ac27a131b2', 3, '2026-03-18', '2026-03-18 00:52:33', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:51:33'),
(63, 'adf9f969d38198ffead315d1b1342c6717707ff911237a5ee95b46c0a07b3797', 3, '2026-03-18', '2026-03-18 00:53:08', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:52:08'),
(64, 'a85b801200f88314fc82daf6596fb69320be51273968469dedfbf6e872a66368', 3, '2026-03-18', '2026-03-18 00:53:48', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:52:48'),
(65, '487ed6a5daf343889c8880ec3f32c8a790897b7993432f43d8ab30553a16ab5b', 3, '2026-03-18', '2026-03-18 00:54:47', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:53:47'),
(66, 'bc992edd765524c88922f985b70cd742ab9db1ce35a96969e8950dbc177cccbc', 3, '2026-03-18', '2026-03-18 00:55:47', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:54:47'),
(67, '36abd2c4cccc583c1e852b0abbc843302a82fff48b42d4008d271a51f9623c0c', 3, '2026-03-18', '2026-03-18 00:56:19', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:55:19'),
(68, '5b6837587ba1c0ca1a76d3101d05bfedda5ea56b152db8c0b88f07aee32f8c31', 3, '2026-03-18', '2026-03-18 00:57:18', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:56:18'),
(69, 'fa65f42d3d34384866f1c21c12d957096d5794c205147b2e2824df4806a201cd', 3, '2026-03-18', '2026-03-18 00:58:11', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:57:11'),
(70, 'a150e144de4404fdc10524255436c430359c7ae51e5ae4a4951b565b7d687b3c', 3, '2026-03-18', '2026-03-18 00:58:42', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:57:42'),
(71, 'e09394827617aa18827e39604f7cbd73e313deef1845aed77d3a45d675e6f002', 3, '2026-03-18', '2026-03-18 00:59:26', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:58:26'),
(72, '8199ef0966c64faa4695ce443bd5bd3a1d04a43846eabb94a82d2599738bd83d', 3, '2026-03-18', '2026-03-18 01:00:18', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:59:18'),
(73, 'cbe93704c2aeffeec28b827e1e78dc35786a6b03ca58780438934bf9e0f53589', 3, '2026-03-18', '2026-03-18 01:00:49', 0, NULL, NULL, '192.168.68.188', '2026-03-17 16:59:49'),
(74, '5297af9f4c68b7060b147c9e14390254eea02b0b1b45a28f89a8ea43ba4425e0', 3, '2026-03-18', '2026-03-18 01:01:20', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:00:20'),
(75, 'eebae7984f85fd3d62ea27ee42784a921d9584f9cbec5b20f7356a70e31c4912', 3, '2026-03-18', '2026-03-18 01:02:20', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:01:20'),
(76, 'e4963ea6f2b049684b02a0644e4691dbecdc3f8c8b1b42063d8578e7e183f7d1', 3, '2026-03-18', '2026-03-18 01:02:55', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:01:55'),
(77, '496dcba3ee591d65292e418ad60f490ac9b8f7a348f378198c14df49ebd292e6', 3, '2026-03-18', '2026-03-18 01:03:30', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:02:30'),
(78, '3872329738e7fb2bf8e62d717f17543274fc50168fcc75bea77908737b581051', 3, '2026-03-18', '2026-03-18 01:04:02', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:03:02'),
(79, '7ccdb0ca86fd28924f582fa5250a5b83334d757ddd86552a17d029ad2a08fdbc', 3, '2026-03-18', '2026-03-18 01:04:33', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:03:33'),
(80, 'f6c0e0d76efbe49ae28584ac93aba3125d1304ee1ebad55875b5c36036ad9d00', 3, '2026-03-18', '2026-03-18 01:05:06', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:04:06'),
(81, '27ece0d44190de227b11cc2dc9b1477693fea1786ea08934eff98c3671dc4e5d', 3, '2026-03-18', '2026-03-18 01:05:37', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:04:37'),
(82, 'b0fccbbf167745fccd4e7d6a0b6842894ff3e807c4707c83853c0d56f01668e4', 3, '2026-03-18', '2026-03-18 01:06:12', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:05:12'),
(83, 'eadd49baa36557b3eb7f78ee252d292b9fe2d873cfe126cb5ce674ffbca57621', 3, '2026-03-18', '2026-03-18 01:07:12', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:06:12'),
(84, 'b8ac1741ec0e4b52d74f6449aa9b4808f77d55032565dd78974fd2e91abb0e1c', 3, '2026-03-18', '2026-03-18 01:07:43', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:06:43'),
(85, 'cdc837f030d3a560b33fb10d1521fec2c5ea2d20e060821f67f41884990c214e', 3, '2026-03-18', '2026-03-18 01:08:16', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:07:16'),
(86, 'bc243312bf9c9f108f7f1c13d18a6007f8815ace4e7710ca12b0028d8c25066e', 3, '2026-03-18', '2026-03-18 01:08:47', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:07:47'),
(87, 'dddeb7ca8d7bd038d2cc4cbe9726e9d205f0a346258d79d8e5fff1ca804bedaa', 3, '2026-03-18', '2026-03-18 01:09:21', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:08:21'),
(88, '3d93f7f6eb5805200bfcd11f4ad371efbbfe370ad77dbf82a03777607d66a35e', 3, '2026-03-18', '2026-03-18 01:09:53', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:08:53'),
(89, 'aba942968a8146f8a6b10c987866088ae26064e55f6757c0c91efc9ae055b839', 3, '2026-03-18', '2026-03-18 01:10:24', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:09:24'),
(90, '52170170ddd941fb8ef8039bf2662074785f72266fefeb468d127c1ed7a80dc7', 3, '2026-03-18', '2026-03-18 01:10:55', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:09:55'),
(91, 'cfd2b6a63472471391af5bfb6f37f7457b4614a819afd0a45ee7efeef08f7338', 3, '2026-03-18', '2026-03-18 01:11:26', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:10:26'),
(92, 'b35c8eb3ebd644c316207198a1ff765d9dcf50a8ee9e233e08bc0e5aa6d2f65d', 3, '2026-03-18', '2026-03-18 01:11:57', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:10:57'),
(93, '5eef8d96dac1ce4d7a50c73fff63da36a509a5bccf9dc713e6fd252cf23bda40', 3, '2026-03-18', '2026-03-18 01:12:28', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:11:28'),
(94, '1c03546b2fc4a427a54b18405d28c87eeaf97f5de4f65f9c290d2b2de1c21cea', 3, '2026-03-18', '2026-03-18 01:13:00', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:12:00'),
(95, '70e836e15b1516b2054617ee43a6c783caae6ed1209fd327ebc818b4e9b8c5a2', 3, '2026-03-18', '2026-03-18 01:13:32', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:12:32'),
(96, '64f523be72d9ab0a595c573e96fe7a50bd2e4c223a4d49040a9828d3b8a5b430', 3, '2026-03-18', '2026-03-18 01:14:03', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:13:03'),
(97, 'd1d42f89fae652ba53f7e389bad2630820d303042e6600fb6c162b7a4f29d3e5', 3, '2026-03-18', '2026-03-18 01:14:12', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:13:12'),
(98, '2690565381a550ef952a073877ffe7e53db4b712d562ccadc2ab2aa4914daf25', 3, '2026-03-18', '2026-03-18 01:14:43', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:13:43'),
(99, '89dc4f46ab74cd399d64a741a1ea038bb4299a8b05eb35bdabaf8a351aab40e5', 3, '2026-03-18', '2026-03-18 01:15:14', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:14:14'),
(100, '657a11b148d2f7841e97c492a66893a35b0adb5c666dac7c346a855a7aad89aa', 3, '2026-03-18', '2026-03-18 01:15:45', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:14:45'),
(101, 'ed7665d201e5a5981e5136992b62f67ea00ca0a621f00e37a656d51af0eda9a5', 3, '2026-03-18', '2026-03-18 01:16:16', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:15:16'),
(102, '3df0762536c2166ebbdd75abb439514abcd27bfb8b1bd099c45bcd82febbc4ec', 3, '2026-03-18', '2026-03-18 01:16:47', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:15:47'),
(103, '5c1c12a169e10e9595aaae620ab11ef84d298a4316348406e6ef3bbec38fe831', 3, '2026-03-18', '2026-03-18 01:17:18', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:16:18'),
(104, '0c6308bd9cfaa7fcd93f89abbfe2ea5c04db0c11013b576abddaef244d0db4da', 3, '2026-03-18', '2026-03-18 01:17:49', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:16:49'),
(105, 'dfecd08b8b6617a13647c2ec1a519481dcf05ad35f3b5291551c331d6b156d48', 3, '2026-03-18', '2026-03-18 01:18:20', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:17:20'),
(106, '8379e5bd31d8485c53e3f55296ec9ef24c720d30cef90ee7eec3b7b49022a861', 3, '2026-03-18', '2026-03-18 01:18:51', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:17:51'),
(107, '33b2b987ac24efab1c2810a47be9e15cfeb645f33389a74c020c7268b4e2b92e', 3, '2026-03-18', '2026-03-18 01:19:23', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:18:23'),
(108, 'bc7c1d3a1d7345afeb404f721e6c5e68114cc47a0d1b484e7cd918f80030150e', 3, '2026-03-18', '2026-03-18 01:19:54', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:18:54'),
(109, '85f80f29a2447ae3519eec1cb15708b491f973b3e68d7c40d461bad67d6bb099', 3, '2026-03-18', '2026-03-18 01:20:25', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:19:25'),
(110, '1c2e2fa0f23ac881cf58fa67e92ee145edf64f121715848e74577f45e7a91ada', 3, '2026-03-18', '2026-03-18 01:20:56', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:19:56'),
(111, '1e1677f84d693040a6462e0d090c1a9cdec4ad623a06e1ffe2bdab9e27d8de6e', 3, '2026-03-18', '2026-03-18 01:21:27', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:20:27'),
(112, '038600c1973033937118525ce7e795518f5bf2cd6b89bf4a8578efa54281bec7', 3, '2026-03-18', '2026-03-18 01:21:58', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:20:58'),
(113, 'b3b4736c791245f3d133f2ca6ced00a41d40fa5043f5b140f874321c8f274b48', 3, '2026-03-18', '2026-03-18 01:22:29', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:21:29'),
(114, 'f588d754c4f74b642f0ecfa5f76b1bca863498fcaec114ec8ed582f32488549a', 3, '2026-03-18', '2026-03-18 01:23:00', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:22:00'),
(115, '91ed9b26d4758ddd1958ca59f5e506ce18eaf9b619f86cf11285b99fad0971e4', 3, '2026-03-18', '2026-03-18 01:23:32', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:22:32'),
(116, '811f92f2f0779cc8caf0c8b35ae9db1ea77dddd8b6183e71fddf6b8022ae89c1', 3, '2026-03-18', '2026-03-18 01:24:31', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:23:31'),
(117, '87218a896eef791df5817bdc2aff5f4a67978795faeefbe0b5f04d0d291973b0', 3, '2026-03-18', '2026-03-18 01:25:31', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:24:31'),
(118, '8af6eab8cb359d28453a911acf196bdffcc2f8199e46cdbcc8f24eeacdb60496', 3, '2026-03-18', '2026-03-18 01:26:02', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:25:02'),
(119, '1a6c3ae7209fca85e55049217c86f9f3fb3f1d92659e099e210e9a1a74209aa8', 3, '2026-03-18', '2026-03-18 01:26:33', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:25:33'),
(120, '667a129c9e92e3e7629c270cb51fab42ad666fe28af15b49fd46be859c3f81aa', 3, '2026-03-18', '2026-03-18 01:27:04', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:26:04'),
(121, '2f6f7308fc2f4229f246db4cf5fa0b894eef63c473638561544409aaa1737de4', 3, '2026-03-18', '2026-03-18 01:27:35', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:26:35'),
(122, '949c5446ff5f9ad7aa2e0d043ba095210cbe0a64d697e053e5f0ee7a23522f89', 3, '2026-03-18', '2026-03-18 01:28:06', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:27:06'),
(123, '4749e0ac198dda231d32f9bfdc3c43c9bb5170ab67f0d31871d35f97e04bd531', 3, '2026-03-18', '2026-03-18 01:28:39', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:27:39'),
(124, '926383f919fa5ff591f4c3feb77cf3f09edb98b09fc1d4dbd4e16cdcd6f980e8', 3, '2026-03-18', '2026-03-18 01:29:10', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:28:10'),
(125, '2156c3a3ff8a3ecb1b7fed15ce680c625ee4890c9582e03bb0db2a9e0fb4a50e', 3, '2026-03-18', '2026-03-18 01:29:46', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:28:46'),
(126, 'e88c8fdfb77758b275a15aef2973e5a11b91d5f658945d71a9cce706d9a18086', 3, '2026-03-18', '2026-03-18 01:30:19', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:29:19'),
(127, 'dde9be81afe6a6c6e83ff5c88baf7cc4090c77716e01373f2acbfa0c694d5932', 3, '2026-03-18', '2026-03-18 01:30:50', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:29:50'),
(128, '72905786c6c5dbc306729f1e2574675f7904c84018dc3a85becf54f88d335493', 3, '2026-03-18', '2026-03-18 01:31:21', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:30:21'),
(129, 'f3c9fdd33acde403a65cd8d05f13dcff391a3de5948b496dbe659ab293ba14c0', 3, '2026-03-18', '2026-03-18 01:31:52', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:30:52'),
(130, '895fa8452a55ab8712c99992b4d889727894ce1e1550a65628f3375b0d40d41c', 3, '2026-03-18', '2026-03-18 01:32:23', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:31:23'),
(131, 'a920f6e13f8516494c7821556cb9015a5b9f8fc1fa602f081e7550d7f71ca225', 3, '2026-03-18', '2026-03-18 01:34:23', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:33:23'),
(132, '7ff0cf5ae02f1ce10b24c143e6af6b7c123598db70f1e07dfa6e49575abaa4a9', 3, '2026-03-18', '2026-03-18 01:34:53', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:33:53'),
(133, '14e2630f3a0e152ae238d7498f7d9346785d3d3ee89985202a95da800f95c6b9', 3, '2026-03-18', '2026-03-18 01:35:24', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:34:24'),
(134, 'b8d072c4e133253341ca62222b860b3882f7b58e2739284f3f7b2b5f3576edf5', 3, '2026-03-18', '2026-03-18 01:35:54', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:34:54'),
(135, '112b485e2f8e6a1cb814b95fabd3da689d58c531b32ab96500c42d9604201647', 3, '2026-03-18', '2026-03-18 01:36:31', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:35:31'),
(136, 'ac90bd80e3f25ad4e7d6e5ff25068b64440ba6d9b21382639c067470c77fc492', 3, '2026-03-18', '2026-03-18 01:37:01', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:36:01'),
(137, '5ef123e03d684138f763139ab79947b78881ffbe5b7c1af920c243091920eddb', 3, '2026-03-18', '2026-03-18 01:37:31', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:36:31'),
(138, '205a07cbe4f93137a76c0f13afbbf0c12a71a391d2e49cd8d425690f62c9f53f', 3, '2026-03-18', '2026-03-18 01:38:01', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:37:01'),
(139, 'f54479d2b735171cf14fda0f1f8892dea7eeee7bbea3a44f279cd41f79e67c04', 3, '2026-03-18', '2026-03-18 01:38:31', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:37:31'),
(140, '4e9a01605e9e749ef04abc2575bfef6b97f8001ab9d0598ba29dbda41c132a22', 3, '2026-03-18', '2026-03-18 01:39:02', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:38:02'),
(141, '13a2ac126f095209a09375e2a934b70ed5bd4fa1b6e3e14f7ac3a5f6032c1b7f', 3, '2026-03-18', '2026-03-18 01:51:07', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:50:07'),
(142, '682468d854b9b2213361fbb68b1c9c47537ca79940de74f0241249760f4d85ad', 3, '2026-03-18', '2026-03-18 01:51:37', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:50:37'),
(143, '87a933540470114374f2356af786d95e93722e367047e276ba3c7c5e991f1991', 3, '2026-03-18', '2026-03-18 01:52:07', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:51:07'),
(144, 'a933abe3b37512fc1e1ad9495d3d048d05e1b2d04a4a5862e29f388bec60d098', 3, '2026-03-18', '2026-03-18 01:52:37', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:51:37'),
(145, 'e7cf029bb3d90d6d5fc97aad9d2d7c48ed16bdb6f04497da91483c69a935e194', 3, '2026-03-18', '2026-03-18 01:53:07', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:52:07'),
(146, '10af51399395ccb3cdbc58241606ce66b10d2cb826d5a80785a18d077d3f3c35', 3, '2026-03-18', '2026-03-18 01:53:37', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:52:37'),
(147, '2d70f01d3e6548710af985c6b32598764b35b346b9b575b7a779f90d49740628', 3, '2026-03-18', '2026-03-18 01:54:08', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:53:08'),
(148, '1074f4e4284e665b5d68232f8d4193bad93961aca53cc43e802349a44eb5ffc7', 3, '2026-03-18', '2026-03-18 01:54:39', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:53:39'),
(149, 'ab0473edef4abd3f98b87e49d8dd1014f556ada1b2c7e158c3a4bf31860a6643', 3, '2026-03-18', '2026-03-18 01:55:11', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:54:11'),
(150, 'b802fcaaf877fb71da92e733eb2ee7163dc8bf9602843872d910b0c57bb9a627', 3, '2026-03-18', '2026-03-18 01:55:42', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:54:42'),
(151, 'fada8e3be56282d6d8640abda2dab8cba88d4570ef417e0ea7542beef9bb17f3', 3, '2026-03-18', '2026-03-18 01:56:13', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:55:13'),
(152, 'e125a38e2cb3d743a6788c1c88cb7dec4d9fdeba674134ae72e396ce10e6a696', 3, '2026-03-18', '2026-03-18 01:56:44', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:55:44'),
(153, '8bfbdc36c41859a41b93b9ce7f90fd85b2079b7328d584b805be5869fa4bc551', 3, '2026-03-18', '2026-03-18 01:57:15', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:56:15'),
(154, '3d4026c6bac445e1f20e8509bd350ef0f2c6f6ab347838a9dc345a3d1fbb276c', 3, '2026-03-18', '2026-03-18 01:57:47', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:56:47'),
(155, '0fbd36e7f6775d424f5416a50dff6b3e21437c02f809583cae8350d3df2f7198', 3, '2026-03-18', '2026-03-18 01:58:18', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:57:18'),
(156, '6c3c6ca6339bd5556080fb63b2c281869de4b8d2bc99da21b6399c00f97092cf', 3, '2026-03-18', '2026-03-18 01:58:49', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:57:49'),
(157, '28dc0430dc7338b87e20c6c94658c8a936fceef8a3e89a2b6fc761b5e6f15d53', 3, '2026-03-18', '2026-03-18 01:59:20', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:58:20'),
(158, '073f96bc07d73ae211bae03239abb766da5e51e1356cb697e4ca09a28063113b', 3, '2026-03-18', '2026-03-18 01:59:51', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:58:51'),
(159, 'c0cd5a256af68e0ab3fe5e93f2d42180a001fb275bf34beeabf8fbf9668d3486', 3, '2026-03-18', '2026-03-18 02:00:22', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:59:22'),
(160, 'b5b2ce87a209fd9213b1f3bed59240b3d8f1be70f14a26b24843e38e9602701c', 3, '2026-03-18', '2026-03-18 02:00:53', 0, NULL, NULL, '192.168.68.188', '2026-03-17 17:59:53'),
(161, '852f17c5381dd9cee230301e1a5d06c24035a2f28d11facb233db4550270d95e', 3, '2026-03-18', '2026-03-18 02:01:24', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:00:24'),
(162, '614eb51410854ad466b1188568e6a67ffd2ec10722e9866838dca6a5be85e2d9', 3, '2026-03-18', '2026-03-18 02:01:55', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:00:55'),
(163, '52f81572c28e970e68d5eed53037100f55947c886699f3fc79ecf2684dfb2975', 3, '2026-03-18', '2026-03-18 02:02:26', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:01:26'),
(164, 'dd0fbd7639b05b3e241af279b56cfee2b1ba69d92842860e4620177047307758', 3, '2026-03-18', '2026-03-18 02:02:57', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:01:57'),
(165, 'ce6b19bdfdb9db4b07b4dfc2dbe7f85dadacec5d0312e425dd1ca9c27e43afe0', 3, '2026-03-18', '2026-03-18 02:03:28', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:02:28'),
(166, 'c3cd5bed7421de9be27d49b1f814b4b358bd39cc5eea74d134ac4be4f2050c88', 3, '2026-03-18', '2026-03-18 02:04:01', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:03:01'),
(167, '6c8bac5fe1cf29789289bbd403a46f25d321eda3ba58a42d14ba6637f9fe905e', 3, '2026-03-18', '2026-03-18 02:04:32', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:03:32'),
(168, '690c3679f97c696fba67f6119a5741b368e418a4d269a4d16526ff69806f4987', 3, '2026-03-18', '2026-03-18 02:05:03', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:04:03'),
(169, 'ce55dbd42bc57641943f29492efe3062d779ee496edf79d01dbc37d3321dbd71', 3, '2026-03-18', '2026-03-18 02:05:34', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:04:34'),
(170, '256cab813f1c86c1058aeb0105dd580ed806d62df8edd0c3a5784c0fe38e2fda', 3, '2026-03-18', '2026-03-18 02:06:05', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:05:05'),
(171, 'de9de89ad098059d400e10f6c5aa753390cf3a7c021f8fe92ab014096e8edf5c', 3, '2026-03-18', '2026-03-18 02:06:36', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:05:36'),
(172, 'f65a4daff0998e8013ec49b3668dc7715e93404d3dd3a8a1cc9914ee78d1f22b', 3, '2026-03-18', '2026-03-18 02:07:07', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:06:07'),
(173, '1ee53232dc767cbee51a0482244f1a82e7ac1785506ed043dd0f0482a281d27e', 3, '2026-03-18', '2026-03-18 02:07:38', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:06:38'),
(174, 'bb050367a47d75d5d44e678ab5b0a5f2c1b0974889802fc76f1d49c2ad9c6b39', 3, '2026-03-18', '2026-03-18 02:08:09', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:07:09'),
(175, 'a80502c81d43fb622775a0d68f5d73a3c922aa8030be99fbc355d765f3699e86', 3, '2026-03-18', '2026-03-18 02:08:40', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:07:40'),
(176, '51591be8b406739e29fa7253847d74a90d4c5633395630f84c5fa93b5f5dd961', 3, '2026-03-18', '2026-03-18 02:08:57', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:07:57'),
(177, '37b9cf726168bb97b8d06364496e634e41983c6e591e11f3a2ee948afd5e8126', 3, '2026-03-18', '2026-03-18 02:09:27', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:08:27'),
(178, '09641b6f031b1580b73b8c0d199fd816c3b611176f3589336c40453db7265424', 3, '2026-03-18', '2026-03-18 02:09:58', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:08:58'),
(179, 'b2b30ce6b558d56082e33c6079d25c7a7f006349e9fcfff36dfed81b8e9003f8', 3, '2026-03-18', '2026-03-18 02:10:29', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:09:29'),
(180, 'c0ed5266350eaef1e6f11889086791b53b2911e5a0f13a34360a0bb08f71d333', 3, '2026-03-18', '2026-03-18 02:11:00', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:10:00'),
(181, '35427f1a218a090ed5c801c8b1a0ebc3b3e2c9d6a1a47950cc3aec3638412526', 3, '2026-03-18', '2026-03-18 02:11:31', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:10:31'),
(182, '4cf44df1055ac1eac6d5d4e1380ec9ca94951bb08f92ea638f5f53ae3574e8e4', 3, '2026-03-18', '2026-03-18 02:12:03', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:11:03'),
(183, '4975ea74dbdb4d449383a7292a686f28c1a186701292ff81a390ad3d8d7476c2', 3, '2026-03-18', '2026-03-18 02:12:34', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:11:34'),
(184, '920cfc9d15e3de394d3fc8eaec533d1f15a5e8dd24547c2a530aab06b6513369', 3, '2026-03-18', '2026-03-18 02:13:05', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:12:05'),
(185, '45b2c1e08d103595cb2ed6a88fbdf58d5ef7b05c9d4d31006b60ed82e908837e', 3, '2026-03-18', '2026-03-18 02:13:36', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:12:36'),
(186, '58f6f8aff5e4fbf94643defa6086f7414cc2369785d89fb58b2c32158e0511a0', 3, '2026-03-18', '2026-03-18 02:14:07', 0, NULL, NULL, '192.168.68.188', '2026-03-17 18:13:07'),
(187, '9077fae58f4ad0d515eabb5b42ddbae70f09bc89ccc3e029d99b32b068935f11', 3, '2026-03-18', '2026-03-18 03:34:26', 0, NULL, NULL, '192.168.68.188', '2026-03-17 19:33:26'),
(188, '70b98603bce022580ca45e5d889a5d8d75b745b9d4f05a74aac50e9e88110132', 3, '2026-03-18', '2026-03-18 03:37:17', 0, NULL, NULL, '192.168.68.188', '2026-03-17 19:36:17'),
(189, '37f6c02f5af51bf8df9ee83198aff368fed87535ced39c0030ce300d63d29e8d', 3, '2026-03-18', '2026-03-18 03:37:30', 0, NULL, NULL, '192.168.68.188', '2026-03-17 19:36:30'),
(190, '919a94a5a39b7c553aeca17fce4d9f2b77e994b9d7503a88a92630d4a8a085e8', 3, '2026-03-18', '2026-03-18 03:54:54', 0, NULL, NULL, '192.168.68.188', '2026-03-17 19:53:54'),
(191, 'dfa362692880bb7c68b3dfee3f85e6b08c7733fd8c67debed62476524781dd46', 3, '2026-03-18', '2026-03-18 15:46:04', 0, NULL, NULL, '::1', '2026-03-18 07:45:04'),
(192, 'afd5ee5ce107eafb9b46afa8215caaab2f5970d57f253db6306ef3645b179a00', 3, '2026-03-18', '2026-03-18 15:46:34', 0, NULL, NULL, '::1', '2026-03-18 07:45:34'),
(193, '5c8b4a67cf45863a1b8ba23a9dc8fc7c34038ec97564c7af8e09ceb43c6c2a55', 3, '2026-03-18', '2026-03-18 15:47:04', 0, NULL, NULL, '::1', '2026-03-18 07:46:04'),
(194, '28c3e542c1a60539484cb738061232eec221b70566cc83944793ebb508743bee', 3, '2026-03-18', '2026-03-18 15:47:34', 0, NULL, NULL, '::1', '2026-03-18 07:46:34'),
(195, '0d2cf45691b1c5c5fe01aa7c509822114e9413a52a4200c0ad3c76a2c21581b4', 3, '2026-03-18', '2026-03-18 15:48:04', 0, NULL, NULL, '::1', '2026-03-18 07:47:04'),
(196, '6e64218395261c3c4915296c317fe14a14b96030ec6ba726ac30666dccc77de8', 3, '2026-03-18', '2026-03-18 15:48:34', 0, NULL, NULL, '::1', '2026-03-18 07:47:34'),
(197, 'ff4cb114e504a1348f8c1aa51d618a459750173c78ff1a53c7249af76410bc9e', 3, '2026-03-18', '2026-03-18 17:31:39', 0, NULL, NULL, '::1', '2026-03-18 09:30:39'),
(198, 'ad6bfe13ed8af6670798cd6c9e4987774c7fb0fe36eda280e71db8cb930bf880', 4, '2026-03-19', '2026-03-19 13:31:35', 0, NULL, NULL, '10.43.2.98', '2026-03-19 05:30:35'),
(199, '9a1b72e2a07c7d11d91a4aeaa1c908eed4443ef32135532da0e3284acb1f43b9', 4, '2026-03-19', '2026-03-19 13:32:55', 0, NULL, NULL, '10.43.2.98', '2026-03-19 05:31:55'),
(200, '674766f652531bceccabc1dc52ce381da20f24f652730162779a5c9361faaf96', 4, '2026-03-19', '2026-03-19 13:34:46', 0, NULL, NULL, '10.43.2.98', '2026-03-19 05:33:46'),
(201, 'b39a686ea13e79ccf1c69d7136997884aed78d3018b47d227748a27def620cfe', 4, '2026-03-19', '2026-03-19 13:40:29', 0, NULL, NULL, '10.43.2.98', '2026-03-19 05:39:29'),
(202, 'd1a013ea001a0f42d7daa5bb13aad3b6ea0903e54e8a632b9b4ebf4bc21e3c8d', 3, '2026-03-19', '2026-03-19 13:40:53', 0, NULL, NULL, '10.43.2.98', '2026-03-19 05:39:53'),
(203, 'bc546ce2cf8a4b67cf4023cb3d88d5d47e4123cd7487190d8c5c28560f734a20', 3, '2026-03-19', '2026-03-19 13:46:46', 0, NULL, NULL, '10.43.2.98', '2026-03-19 05:45:46'),
(204, '368da10a9a48c58e17eea11b4fcd23646a5555e04725c08156e2e2c5a66695f7', 3, '2026-03-19', '2026-03-19 13:50:16', 0, NULL, NULL, '10.43.2.98', '2026-03-19 05:49:16'),
(205, '6e9c96b545901bda2a5db1fe0057e1cb708b61d1afa48efcf0b844369d7c349f', 3, '2026-03-19', '2026-03-19 13:58:16', 0, NULL, NULL, '10.43.2.98', '2026-03-19 05:57:16'),
(206, 'ba80ae160f9175b433454e7cf562b629ce8ebe919548649939a1cb3b7c2bc3a2', 3, '2026-03-19', '2026-03-19 14:01:02', 0, NULL, NULL, '10.43.2.98', '2026-03-19 06:00:02'),
(207, 'c0501b056a9798f9add4dc72cd4318fe5105b2fa249433238c44a3f98a3110d0', 3, '2026-03-19', '2026-03-19 14:58:14', 0, NULL, NULL, '10.56.5.98', '2026-03-19 06:57:14'),
(208, 'cc9766daa571a96fc9930ed88abee4ffa031ac43a505391e5eac79b33b19989b', 3, '2026-03-19', '2026-03-19 14:58:28', 0, NULL, NULL, '10.56.5.98', '2026-03-19 06:57:28'),
(209, '5574019e018a826b43935be1072dc3fe3e37de9c998805a3758addbd4d73d3cb', 3, '2026-03-19', '2026-03-19 17:32:45', 0, NULL, NULL, '10.56.5.98', '2026-03-19 09:31:45'),
(210, '41a7f1613dbad5e3846c4bf9e0b177d6d7167d2b0b0625e8f49aebd3b3f54ab8', 3, '2026-03-19', '2026-03-19 17:33:01', 0, NULL, NULL, '10.56.5.98', '2026-03-19 09:32:01'),
(211, '6cce3b0b225320a5978e2bc75e362277d156f5e28c05c0246b36a6e4218fe906', 3, '2026-03-19', '2026-03-19 19:27:31', 0, NULL, NULL, '10.56.5.98', '2026-03-19 11:26:31'),
(212, '82108daf0a6a1bc5d0ae937011e8fe449566beda4b12dff11b1677f6da4060a1', 3, '2026-03-19', '2026-03-19 22:50:19', 0, NULL, NULL, '::1', '2026-03-19 14:49:19');

-- --------------------------------------------------------

--
-- Table structure for table `ta_employee_shifts`
--

CREATE TABLE `ta_employee_shifts` (
  `employee_shift_id` int(11) NOT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `shift_id` int(11) NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_employee_shifts`
--

INSERT INTO `ta_employee_shifts` (`employee_shift_id`, `employee_id`, `shift_id`, `effective_from`, `effective_to`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'EMP002', 1, '2026-03-18', '2026-06-20', 1, '2026-03-18 07:42:42', '2026-03-18 07:42:42');

-- --------------------------------------------------------

--
-- Table structure for table `ta_flexible_schedules`
--

CREATE TABLE `ta_flexible_schedules` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `schedule_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `day_of_week` int(11) DEFAULT NULL,
  `repeat_until` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_flexible_schedules`
--

INSERT INTO `ta_flexible_schedules` (`id`, `employee_id`, `schedule_date`, `start_time`, `end_time`, `day_of_week`, `repeat_until`, `contract_end_date`, `notes`, `created_by`, `created_at`) VALUES
(28, 'EMP001', '2026-03-26', '07:00:00', '17:00:00', 4, '2026-06-26', '2026-06-26', '', 3, '2026-03-19 13:52:39'),
(29, 'EMP001', '2026-03-24', '07:00:00', '16:00:00', 2, NULL, NULL, '', 3, '2026-03-19 14:39:26'),
(30, 'EMP001', '2026-03-24', '08:30:00', '17:30:00', 2, NULL, NULL, '', 3, '2026-03-19 16:26:13');

-- --------------------------------------------------------

--
-- Table structure for table `ta_leave_balances`
--

CREATE TABLE `ta_leave_balances` (
  `leave_balance_id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `opening_balance` decimal(5,2) DEFAULT 0.00,
  `used_balance` decimal(5,2) DEFAULT 0.00,
  `remaining_balance` decimal(5,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_leave_balances`
--

INSERT INTO `ta_leave_balances` (`leave_balance_id`, `employee_id`, `leave_type_id`, `year`, `opening_balance`, `used_balance`, `remaining_balance`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'EMP002', 1, 2026, 15.00, 0.00, 15.00, NULL, '2026-03-19 04:45:30', '2026-03-19 04:45:30'),
(2, 'EMP003', 1, 2026, 15.00, 0.00, 15.00, NULL, '2026-03-19 04:45:30', '2026-03-19 04:45:30'),
(3, 'EMP001', 1, 2026, 15.00, 0.00, 15.00, NULL, '2026-03-19 04:45:30', '2026-03-19 04:45:30'),
(4, 'EMP002', 2, 2026, 10.00, 0.00, 10.00, NULL, '2026-03-19 04:45:30', '2026-03-19 04:45:30'),
(5, 'EMP003', 2, 2026, 10.00, 0.00, 10.00, NULL, '2026-03-19 04:45:30', '2026-03-19 04:45:30'),
(6, 'EMP001', 2, 2026, 10.00, 0.00, 10.00, NULL, '2026-03-19 04:45:30', '2026-03-19 04:45:30'),
(7, 'EMP002', 3, 2026, 5.00, 0.00, 5.00, NULL, '2026-03-19 04:45:30', '2026-03-19 04:45:30'),
(8, 'EMP003', 3, 2026, 5.00, 0.00, 5.00, NULL, '2026-03-19 04:45:30', '2026-03-19 04:45:30'),
(9, 'EMP001', 3, 2026, 5.00, 0.00, 5.00, NULL, '2026-03-19 04:45:30', '2026-03-19 04:45:30'),
(10, 'EMP002', 4, 2026, 3.00, 0.00, 3.00, NULL, '2026-03-19 04:45:30', '2026-03-19 04:45:30'),
(11, 'EMP003', 4, 2026, 3.00, 0.00, 3.00, NULL, '2026-03-19 04:45:30', '2026-03-19 04:45:30'),
(12, 'EMP001', 4, 2026, 3.00, 0.00, 3.00, NULL, '2026-03-19 04:45:30', '2026-03-19 04:45:30');

-- --------------------------------------------------------

--
-- Table structure for table `ta_leave_requests`
--

CREATE TABLE `ta_leave_requests` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `date_submitted` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `details` varchar(255) DEFAULT NULL,
  `supporting_document` varchar(255) DEFAULT NULL,
  `reject_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ta_leave_types`
--

CREATE TABLE `ta_leave_types` (
  `leave_type_id` int(11) NOT NULL,
  `leave_type_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `days_per_year` int(11) NOT NULL DEFAULT 10,
  `is_deductible` tinyint(1) DEFAULT 1,
  `requires_approval` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_leave_types`
--

INSERT INTO `ta_leave_types` (`leave_type_id`, `leave_type_name`, `description`, `days_per_year`, `is_deductible`, `requires_approval`, `created_at`) VALUES
(1, 'Vacation Leave', NULL, 15, 1, 1, '2026-03-19 04:45:21'),
(2, 'Sick Leave', NULL, 10, 1, 1, '2026-03-19 04:45:21'),
(3, 'Maternity Leave', NULL, 5, 1, 1, '2026-03-19 04:45:21'),
(4, 'Emergency Leave', NULL, 3, 0, 0, '2026-03-19 04:45:21');

-- --------------------------------------------------------

--
-- Table structure for table `ta_shifts`
--

CREATE TABLE `ta_shifts` (
  `shift_id` int(11) NOT NULL,
  `shift_name` varchar(100) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `break_duration` int(11) DEFAULT 60 COMMENT 'Break duration in minutes',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_shifts`
--

INSERT INTO `ta_shifts` (`shift_id`, `shift_name`, `start_time`, `end_time`, `break_duration`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Morning Shift', '06:00:00', '17:00:00', 60, '', 1, '2026-03-18 07:41:35', '2026-03-18 07:41:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('recruitment','payroll','time','compliance','workforce','employee','learning','performance','engagement_relations','exit') NOT NULL,
  `theme` enum('light','dark') DEFAULT 'light',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `theme`, `created_at`, `profile_pic`) VALUES
(1, 'hr_payroll', '$2y$10$YSkTSwrSdqSBsF2e.pfyq.mNCCIF7ijV4h/s1pAc8Q7KlQHzbQTmq', 'Russell Ike', 'payroll', 'light', '2026-03-06 21:13:06', NULL),
(2, 'hr_recruitment', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'Administrator', 'recruitment', 'light', '2026-03-07 02:46:33', NULL),
(3, 'hr_time', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'Jose Mari', 'time', 'light', '2026-03-07 02:47:07', NULL),
(4, 'hr_employee', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'someone', 'employee', 'light', '2026-03-07 02:47:55', NULL),
(5, 'hr_compliance', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'comply', 'compliance', 'light', '2026-03-07 02:48:19', NULL),
(6, 'hr_workforce', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'force', 'workforce', 'light', '2026-03-07 02:48:43', NULL),
(7, 'hr_learning', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'learn', 'learning', 'light', '2026-03-07 02:49:22', NULL),
(8, 'hr_performance', '$2y$10$/aFKLVK.xloqiY31X4T.dOPKY2AnnkrpaME4f2z.l4LhQurY1/Zzy', 'Perform', 'performance', 'light', '2026-03-07 02:49:46', 'user_8.jpg'),
(9, 'hr_engagement', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'engage', 'engagement_relations', 'light', '2026-03-07 02:50:37', NULL),
(10, 'hr_exit', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'exit', 'exit', 'light', '2026-03-07 02:51:04', NULL),
(11, 'emp_john_doe', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'John Doe', 'employee', 'light', '2026-03-17 17:12:01', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `employees1`
--
ALTER TABLE `employees1`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`holiday_id`);

--
-- Indexes for table `overtime_requests`
--
ALTER TABLE `overtime_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD KEY `fk_requests_employee` (`employee_id`);

--
-- Indexes for table `ta_attendance`
--
ALTER TABLE `ta_attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`,`attendance_date`);

--
-- Indexes for table `ta_attendance_tokens`
--
ALTER TABLE `ta_attendance_tokens`
  ADD PRIMARY KEY (`token_id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `generated_by` (`generated_by`),
  ADD KEY `used_by` (`used_by`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_used` (`used`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `ta_employee_shifts`
--
ALTER TABLE `ta_employee_shifts`
  ADD PRIMARY KEY (`employee_shift_id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`,`shift_id`,`effective_from`),
  ADD KEY `fk_emp_shift_shift` (`shift_id`);

--
-- Indexes for table `ta_flexible_schedules`
--
ALTER TABLE `ta_flexible_schedules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_schedule` (`employee_id`,`schedule_date`,`start_time`),
  ADD KEY `idx_employee` (`employee_id`),
  ADD KEY `idx_date` (`schedule_date`);

--
-- Indexes for table `ta_leave_balances`
--
ALTER TABLE `ta_leave_balances`
  ADD PRIMARY KEY (`leave_balance_id`),
  ADD UNIQUE KEY `unique_employee_leave_year` (`employee_id`,`leave_type_id`,`year`),
  ADD KEY `idx_employee_year` (`employee_id`,`year`),
  ADD KEY `idx_leave_type_year` (`leave_type_id`,`year`);

--
-- Indexes for table `ta_leave_requests`
--
ALTER TABLE `ta_leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_leave_employee` (`employee_id`),
  ADD KEY `fk_leave_type` (`leave_type_id`);

--
-- Indexes for table `ta_leave_types`
--
ALTER TABLE `ta_leave_types`
  ADD PRIMARY KEY (`leave_type_id`);

--
-- Indexes for table `ta_shifts`
--
ALTER TABLE `ta_shifts`
  ADD PRIMARY KEY (`shift_id`),
  ADD UNIQUE KEY `shift_name` (`shift_name`);

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
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees1`
--
ALTER TABLE `employees1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `holiday_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `overtime_requests`
--
ALTER TABLE `overtime_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ta_attendance`
--
ALTER TABLE `ta_attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ta_attendance_tokens`
--
ALTER TABLE `ta_attendance_tokens`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- AUTO_INCREMENT for table `ta_employee_shifts`
--
ALTER TABLE `ta_employee_shifts`
  MODIFY `employee_shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ta_flexible_schedules`
--
ALTER TABLE `ta_flexible_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `ta_leave_balances`
--
ALTER TABLE `ta_leave_balances`
  MODIFY `leave_balance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `ta_leave_requests`
--
ALTER TABLE `ta_leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ta_shifts`
--
ALTER TABLE `ta_shifts`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_employee_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `overtime_requests`
--
ALTER TABLE `overtime_requests`
  ADD CONSTRAINT `overtime_requests_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `fk_requests_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `ta_attendance`
--
ALTER TABLE `ta_attendance`
  ADD CONSTRAINT `fk_attendance_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `ta_employee_shifts`
--
ALTER TABLE `ta_employee_shifts`
  ADD CONSTRAINT `fk_emp_shift_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_emp_shift_shift` FOREIGN KEY (`shift_id`) REFERENCES `ta_shifts` (`shift_id`) ON DELETE CASCADE;

--
-- Constraints for table `ta_leave_balances`
--
ALTER TABLE `ta_leave_balances`
  ADD CONSTRAINT `ta_leave_balances_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ta_leave_balances_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `ta_leave_types` (`leave_type_id`) ON DELETE CASCADE;

--
-- Constraints for table `ta_leave_requests`
--
ALTER TABLE `ta_leave_requests`
  ADD CONSTRAINT `fk_leave_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_leave_type` FOREIGN KEY (`leave_type_id`) REFERENCES `ta_leave_types` (`leave_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
