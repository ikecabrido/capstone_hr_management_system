-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2026 at 05:11 PM
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
  `employee_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
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

INSERT INTO `employees` (`employee_id`, `user_id`, `full_name`, `address`, `contact_number`, `email`, `department`, `position`, `date_hired`, `employment_status`, `created_at`, `updated_at`) VALUES
(1, 29, 'John Doe', '123 Main St', '123-456-7890', 'john.doe@example.com', 'IT', 'Software Engineer', '2023-01-01', 'Active', '2026-03-22 07:51:30', '2026-03-27 13:09:28'),
(2, 30, 'Jane Smith', '456 Oak Ave', '098-765-4321', 'jane.smith@example.com', 'HR', 'HR Manager', '2023-02-15', 'Active', '2026-03-22 07:51:30', '2026-03-27 13:13:07'),
(3, 31, 'Mike Johnson', '789 Pine Rd', '555-123-4567', 'mike.johnson@example.com', 'Finance', 'Accountant', '2023-03-10', 'Active', '2026-03-22 07:51:30', '2026-03-27 13:13:07'),
(4, 32, 'Sarah Williams', '321 Elm St', '555-789-0123', 'sarah.williams@example.com', 'Operations', 'Operations Manager', '2023-04-01', 'Active', '2026-03-27 11:25:55', '2026-03-27 13:13:07'),
(5, 33, 'David Brown', '654 Maple Dr', '555-456-7890', 'david.brown@example.com', 'IT', 'Junior Developer', '2023-05-15', 'Active', '2026-03-27 11:25:55', '2026-03-27 13:13:07'),
(6, 34, 'Emily Davis', '987 Cedar Ln', '555-321-6543', 'emily.davis@example.com', 'HR', 'HR Specialist', '2023-06-01', 'Active', '2026-03-27 11:25:55', '2026-03-27 13:13:07'),
(7, 35, 'Robert Wilson', '147 Oak St', '555-654-3210', 'robert.wilson@example.com', 'Finance', 'Financial Analyst', '2023-07-10', 'Active', '2026-03-27 11:25:55', '2026-03-27 13:13:07'),
(8, 36, 'Jessica Martinez', '258 Pine Ave', '555-987-6543', 'jessica.martinez@example.com', 'Operations', 'Staff Coordinator', '2023-08-20', 'Active', '2026-03-27 11:25:55', '2026-03-27 13:13:07');

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
-- Table structure for table `employees_backup`
--

CREATE TABLE `employees_backup` (
  `employee_id` varchar(50) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `employment_status` varchar(50) DEFAULT 'Active',
  `employment_type_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `salary_grade_id` int(11) DEFAULT NULL,
  `gender` enum('Male','Female','Other','Prefer not to say') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `marital_status` enum('Single','Married','Divorced','Widowed','Prefer not to say') DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `pan_number` varchar(50) DEFAULT NULL COMMENT 'Tax ID / SSN / National ID number',
  `manager_id` varchar(50) DEFAULT NULL,
  `base_salary` decimal(12,2) DEFAULT 0.00,
  `currency` varchar(3) DEFAULT 'PHP',
  `bank_account_number` varchar(50) DEFAULT NULL COMMENT 'Encrypted in production',
  `bank_name` varchar(100) DEFAULT NULL,
  `emergency_contact_name` varchar(150) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `emergency_contact_relation` varchar(50) DEFAULT NULL,
  `probation_end_date` date DEFAULT NULL,
  `confirmation_date` date DEFAULT NULL,
  `retirement_eligible_date` date DEFAULT NULL,
  `employee_status` enum('Active','On Leave','On Probation','Inactive','Retired','Terminated','Resigned') DEFAULT 'Active',
  `age_group` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees_backup`
--

INSERT INTO `employees_backup` (`employee_id`, `full_name`, `address`, `location_id`, `contact_number`, `email`, `department`, `department_id`, `position`, `position_id`, `date_hired`, `employment_status`, `employment_type_id`, `created_at`, `updated_at`, `user_id`, `salary_grade_id`, `gender`, `date_of_birth`, `marital_status`, `nationality`, `pan_number`, `manager_id`, `base_salary`, `currency`, `bank_account_number`, `bank_name`, `emergency_contact_name`, `emergency_contact_phone`, `emergency_contact_relation`, `probation_end_date`, `confirmation_date`, `retirement_eligible_date`, `employee_status`, `age_group`) VALUES
('EMP001', 'John Doe', '123 Main St', NULL, '123-456-7890', 'john.doe@example.com', 'IT', NULL, 'Software Engineer', NULL, '2023-01-01', 'Active', NULL, '2026-03-17 14:25:13', '2026-03-22 06:40:43', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 'PHP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Active', NULL),
('EMP002', 'Jane Smith', '456 Oak Ave', NULL, '098-765-4321', 'jane.smith@example.com', 'HR', NULL, 'HR Manager', NULL, '2023-02-15', 'Active', NULL, '2026-03-17 14:25:13', '2026-03-17 14:25:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 'PHP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Active', NULL),
('EMP003', 'Mike Johnson', '789 Pine Rd', NULL, '555-123-4567', 'mike.johnson@example.com', 'Finance', NULL, 'Accountant', NULL, '2023-03-10', 'Active', NULL, '2026-03-17 14:25:13', '2026-03-17 14:25:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 'PHP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employees_new`
--

CREATE TABLE `employees_new` (
  `employee_id` int(11) NOT NULL,
  `employee_id_new` int(11) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `employment_status` varchar(50) DEFAULT 'Active',
  `employment_type_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `salary_grade_id` int(11) DEFAULT NULL,
  `gender` enum('Male','Female','Other','Prefer not to say') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `marital_status` enum('Single','Married','Divorced','Widowed','Prefer not to say') DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `pan_number` varchar(50) DEFAULT NULL COMMENT 'Tax ID / SSN / National ID number',
  `manager_id` int(11) DEFAULT NULL,
  `base_salary` decimal(12,2) DEFAULT 0.00,
  `currency` varchar(3) DEFAULT 'PHP',
  `bank_account_number` varchar(50) DEFAULT NULL COMMENT 'Encrypted in production',
  `bank_name` varchar(100) DEFAULT NULL,
  `emergency_contact_name` varchar(150) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `emergency_contact_relation` varchar(50) DEFAULT NULL,
  `probation_end_date` date DEFAULT NULL,
  `confirmation_date` date DEFAULT NULL,
  `retirement_eligible_date` date DEFAULT NULL,
  `employee_status` enum('Active','On Leave','On Probation','Inactive','Retired','Terminated','Resigned') DEFAULT 'Active',
  `age_group` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_accounts`
--

CREATE TABLE `employee_accounts` (
  `account_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `account_status` varchar(50) DEFAULT 'Active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_accounts`
--

INSERT INTO `employee_accounts` (`account_id`, `employee_id`, `username`, `email`, `password_hash`, `account_status`, `last_login`, `created_at`, `updated_at`) VALUES
(6, 1, 'john.doe', 'john.doe@example.com', '$2y$10$vIvUW2vWJrJ6vDzQH6/9S.PzKJ3E0d8vKvDzQH6/9S.PzKJ3E0d8v', 'Active', NULL, '2026-03-27 11:26:50', '2026-03-27 11:26:50'),
(7, 2, 'jane.smith', 'jane.smith@example.com', '$2y$10$vIvUW2vWJrJ6vDzQH6/9S.PzKJ3E0d8vKvDzQH6/9S.PzKJ3E0d8v', 'Active', NULL, '2026-03-27 11:26:50', '2026-03-27 11:26:50'),
(8, 3, 'mike.johnson', 'mike.johnson@example.com', '$2y$10$vIvUW2vWJrJ6vDzQH6/9S.PzKJ3E0d8vKvDzQH6/9S.PzKJ3E0d8v', 'Active', NULL, '2026-03-27 11:26:50', '2026-03-27 11:26:50'),
(9, 4, 'sarah.williams', 'sarah.williams@example.com', '$2y$10$vIvUW2vWJrJ6vDzQH6/9S.PzKJ3E0d8vKvDzQH6/9S.PzKJ3E0d8v', 'Active', NULL, '2026-03-27 11:26:50', '2026-03-27 11:26:50'),
(10, 5, 'david.brown', 'david.brown@example.com', '$2y$10$vIvUW2vWJrJ6vDzQH6/9S.PzKJ3E0d8vKvDzQH6/9S.PzKJ3E0d8v', 'Active', NULL, '2026-03-27 11:26:50', '2026-03-27 11:26:50'),
(11, 6, 'emily.davis', 'emily.davis@example.com', '$2y$10$vIvUW2vWJrJ6vDzQH6/9S.PzKJ3E0d8vKvDzQH6/9S.PzKJ3E0d8v', 'Active', NULL, '2026-03-27 11:26:50', '2026-03-27 11:26:50'),
(12, 7, 'robert.wilson', 'robert.wilson@example.com', '$2y$10$vIvUW2vWJrJ6vDzQH6/9S.PzKJ3E0d8vKvDzQH6/9S.PzKJ3E0d8v', 'Active', NULL, '2026-03-27 11:26:50', '2026-03-27 11:26:50'),
(13, 8, 'jessica.martinez', 'jessica.martinez@example.com', '$2y$10$vIvUW2vWJrJ6vDzQH6/9S.PzKJ3E0d8vKvDzQH6/9S.PzKJ3E0d8v', 'Active', NULL, '2026-03-27 11:26:50', '2026-03-27 11:26:50');

-- --------------------------------------------------------

--
-- Table structure for table `employee_id_mapping`
--

CREATE TABLE `employee_id_mapping` (
  `old_id` varchar(50) NOT NULL,
  `new_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_id_mapping`
--

INSERT INTO `employee_id_mapping` (`old_id`, `new_id`, `created_at`) VALUES
('EMP001', 1, '2026-03-22 07:00:20'),
('EMP002', 2, '2026-03-22 07:00:20'),
('EMP003', 3, '2026-03-22 07:00:20');

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
  `employee_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `hours` decimal(4,2) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `overtime_requests_backup`
--

CREATE TABLE `overtime_requests_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
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
-- Table structure for table `pr_deductions`
--

CREATE TABLE `pr_deductions` (
  `deduction_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `type` enum('fixed','percentage') DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `is_statutory` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pr_employee_adjustments`
--

CREATE TABLE `pr_employee_adjustments` (
  `adjustment_id` int(11) NOT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `payroll_period_id` int(11) NOT NULL,
  `type` enum('allowance','deduction') NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pr_employee_deductions`
--

CREATE TABLE `pr_employee_deductions` (
  `employee_deduction_id` int(11) NOT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `deduction_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pr_payslips`
--

CREATE TABLE `pr_payslips` (
  `payslip_id` int(11) NOT NULL,
  `payroll_run_id` int(11) DEFAULT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `gross_pay` decimal(10,2) DEFAULT NULL,
  `total_deductions` decimal(10,2) DEFAULT NULL,
  `net_pay` decimal(10,2) DEFAULT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pr_payslip_items`
--

CREATE TABLE `pr_payslip_items` (
  `payslip_item_id` int(11) NOT NULL,
  `payslip_id` int(11) DEFAULT NULL,
  `item_type` enum('earning','deduction') DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pr_periods`
--

CREATE TABLE `pr_periods` (
  `period_id` int(11) NOT NULL,
  `period_name` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `pay_date` date DEFAULT NULL,
  `status` enum('open','processing','closed') DEFAULT 'open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pr_periods`
--

INSERT INTO `pr_periods` (`period_id`, `period_name`, `start_date`, `end_date`, `pay_date`, `status`) VALUES
(0, 'Mar 1-15, 2026', '2026-03-01', '2026-03-15', '2026-03-20', 'open');

-- --------------------------------------------------------

--
-- Table structure for table `pr_runs`
--

CREATE TABLE `pr_runs` (
  `run_id` int(11) NOT NULL,
  `payroll_period_id` int(11) DEFAULT NULL,
  `processed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('draft','finalized') DEFAULT 'draft'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pr_statutory_contributions`
--

CREATE TABLE `pr_statutory_contributions` (
  `contribution_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `employee_share` decimal(10,2) DEFAULT NULL,
  `employer_share` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pr_tax_tables`
--

CREATE TABLE `pr_tax_tables` (
  `tax_id` int(11) NOT NULL,
  `min_income` decimal(10,2) DEFAULT NULL,
  `max_income` decimal(10,2) DEFAULT NULL,
  `tax_rate` decimal(5,2) DEFAULT NULL,
  `fixed_tax` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `employee_id` varchar(50) DEFAULT NULL,
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requests_backup`
--

CREATE TABLE `requests_backup` (
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
-- Table structure for table `ta_absence_late_policies`
--

CREATE TABLE `ta_absence_late_policies` (
  `policy_id` int(11) NOT NULL,
  `policy_name` varchar(100) NOT NULL,
  `max_late_per_month` int(11) DEFAULT 3,
  `max_absent_per_month` int(11) DEFAULT 2,
  `max_excused_absent_per_month` int(11) DEFAULT 2,
  `max_excused_late_per_month` int(11) DEFAULT 5,
  `warning_after_late_count` int(11) DEFAULT 5,
  `warning_after_absent_count` int(11) DEFAULT 3,
  `late_threshold_minutes` int(11) DEFAULT 15,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_absence_late_policies`
--

INSERT INTO `ta_absence_late_policies` (`policy_id`, `policy_name`, `max_late_per_month`, `max_absent_per_month`, `max_excused_absent_per_month`, `max_excused_late_per_month`, `warning_after_late_count`, `warning_after_absent_count`, `late_threshold_minutes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Default Company Policy', 3, 2, 2, 5, 5, 3, 15, 1, '2026-03-19 18:42:28', '2026-03-19 18:42:28');

-- --------------------------------------------------------

--
-- Table structure for table `ta_absence_late_records`
--

CREATE TABLE `ta_absence_late_records` (
  `record_id` int(11) NOT NULL,
  `attendance_id` int(11) DEFAULT NULL,
  `employee_id` int(11) NOT NULL,
  `absence_date` date NOT NULL,
  `type` enum('ABSENT','LATE') NOT NULL,
  `is_excused` tinyint(1) DEFAULT 0,
  `excuse_status` enum('PENDING','APPROVED','REJECTED','AWAITING_DOCUMENTS') DEFAULT 'PENDING',
  `excuse_type` enum('MANUAL_APPEAL','APPROVED_LEAVE') DEFAULT 'MANUAL_APPEAL',
  `leave_request_id` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `supporting_document_url` varchar(255) DEFAULT NULL,
  `submitted_by` int(11) DEFAULT NULL,
  `submitted_date` datetime DEFAULT current_timestamp(),
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_date` datetime DEFAULT NULL,
  `approval_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `late_minutes` int(11) DEFAULT 0 COMMENT 'Minutes late for this incident',
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ta_absence_late_thresholds`
--

CREATE TABLE `ta_absence_late_thresholds` (
  `threshold_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `month_year` varchar(7) NOT NULL,
  `absent_count` int(11) DEFAULT 0,
  `late_count` int(11) DEFAULT 0,
  `excused_absent_count` int(11) DEFAULT 0,
  `excused_late_count` int(11) DEFAULT 0,
  `warning_level` enum('NONE','LEVEL_1','LEVEL_2','LEVEL_3') DEFAULT 'NONE',
  `warning_date` datetime DEFAULT NULL,
  `last_action_taken` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ta_attendance`
--

CREATE TABLE `ta_attendance` (
  `attendance_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL,
  `recorded_by` enum('MANUAL','QR','SYSTEM') NOT NULL DEFAULT 'MANUAL',
  `status` enum('PRESENT','ABSENT','LATE','EARLY_OUT','ON_LEAVE','PENDING_APPROVAL') DEFAULT 'PENDING_APPROVAL',
  `leave_request_id` int(11) DEFAULT NULL,
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
  `is_within_shift_hours` tinyint(1) DEFAULT 1,
  `late_minutes` int(11) DEFAULT 0 COMMENT 'Number of minutes employee was late (0 if on time)',
  `early_out_minutes` int(11) DEFAULT 0 COMMENT 'Number of minutes employee left early (0 if on time)',
  `shift_minutes` int(11) DEFAULT 0 COMMENT 'Expected shift duration in minutes',
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_attendance`
--

INSERT INTO `ta_attendance` (`attendance_id`, `employee_id`, `shift_id`, `attendance_date`, `time_in`, `time_out`, `recorded_by`, `status`, `leave_request_id`, `is_approved`, `approved_by`, `approval_remarks`, `approved_at`, `created_at`, `updated_at`, `total_hours_worked`, `regular_hours`, `overtime_hours`, `is_within_time_window`, `is_within_timeout_window`, `is_within_shift_hours`, `late_minutes`, `early_out_minutes`, `shift_minutes`, `employee_id_new`) VALUES
(1, 3, NULL, '2026-03-27', '0000-00-00 00:00:00', NULL, 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-27 15:17:19', '2026-03-27 16:23:29', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(2, 3, NULL, '2026-03-28', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-28 00:46:35', '2026-03-28 00:55:21', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(3, 1, NULL, '2026-03-28', '2026-03-28 09:03:23', '2026-03-28 09:03:31', 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-28 01:01:53', '2026-03-28 01:03:31', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(4, 2, NULL, '2026-03-28', '2026-03-28 09:04:55', '2026-03-28 09:16:05', 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-28 01:04:55', '2026-03-28 01:16:05', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(5, 6, NULL, '2026-03-28', '2026-03-28 10:59:33', NULL, 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-28 02:59:33', '2026-03-28 02:59:33', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(6, 7, NULL, '2026-03-28', '2026-03-28 11:56:06', NULL, 'MANUAL', 'PRESENT', NULL, 0, NULL, NULL, NULL, '2026-03-28 03:56:06', '2026-03-28 03:56:06', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(7, 8, NULL, '2026-03-28', '2026-03-28 17:36:20', '2026-03-28 17:37:39', 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-28 09:36:20', '2026-03-28 09:37:39', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(8, 4, NULL, '2026-03-28', '2026-03-28 15:44:30', NULL, 'QR', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-28 14:44:30', '2026-03-28 14:44:30', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(9, 5, NULL, '2026-03-28', '2026-03-28 23:13:41', NULL, 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-28 15:13:41', '2026-03-28 15:13:41', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(10, 3, NULL, '2026-03-29', '2026-03-29 16:06:47', '2026-03-29 16:06:53', 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-29 08:06:47', '2026-03-29 08:06:53', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(11, 4, NULL, '2026-03-29', '2026-03-29 16:57:44', '2026-03-29 16:58:24', 'QR', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-29 08:57:44', '2026-03-29 08:58:24', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(12, 6, NULL, '2026-03-29', '2026-03-29 17:15:18', NULL, 'QR', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-29 09:15:18', '2026-03-29 09:15:18', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(13, 5, NULL, '2026-03-30', '2026-03-30 09:49:20', NULL, 'QR', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-30 01:49:20', '2026-03-30 01:49:20', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ta_attendance_backup`
--

CREATE TABLE `ta_attendance_backup` (
  `attendance_id` int(11) NOT NULL DEFAULT 0,
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
  `is_within_shift_hours` tinyint(1) DEFAULT 1,
  `late_minutes` int(11) DEFAULT 0 COMMENT 'Number of minutes employee was late (0 if on time)',
  `early_out_minutes` int(11) DEFAULT 0 COMMENT 'Number of minutes employee left early (0 if on time)',
  `shift_minutes` int(11) DEFAULT 0 COMMENT 'Expected shift duration in minutes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ta_attendance_metrics`
--

CREATE TABLE `ta_attendance_metrics` (
  `metric_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `month_year` varchar(7) NOT NULL,
  `attendance_rate` decimal(5,2) DEFAULT 0.00,
  `absence_rate` decimal(5,2) DEFAULT 0.00,
  `punctuality_score` decimal(5,2) DEFAULT 100.00,
  `overtime_frequency_rating` varchar(20) DEFAULT NULL,
  `overall_performance_score` decimal(5,2) DEFAULT 0.00,
  `total_present_days` int(11) DEFAULT 0,
  `total_absent_days` int(11) DEFAULT 0,
  `total_late_incidents` int(11) DEFAULT 0,
  `total_overtime_hours` decimal(8,2) DEFAULT 0.00,
  `status_summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`status_summary`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
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
  `used_by` int(11) DEFAULT NULL,
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
(212, '82108daf0a6a1bc5d0ae937011e8fe449566beda4b12dff11b1677f6da4060a1', 3, '2026-03-19', '2026-03-19 22:50:19', 0, NULL, NULL, '::1', '2026-03-19 14:49:19'),
(213, '6e997895322b9e471dc24c8b4d2d922be4358d5156e35c5da03d29164c100180', 3, '2026-03-20', '2026-03-20 02:46:47', 0, NULL, NULL, '10.56.5.98', '2026-03-19 18:45:47'),
(214, 'b6b9a85726bcb12fb7072bb05534845865cbb1a4566169a59ab7423f3ca09f31', 3, '2026-03-20', '2026-03-20 14:35:18', 0, NULL, NULL, '192.168.68.188', '2026-03-20 06:34:18'),
(215, 'c233b58d779a32c01a052e4a3426394edca3f069f6fd70b521643bde2eec8e4f', 3, '2026-03-20', '2026-03-20 20:49:44', 0, NULL, NULL, '10.56.5.98', '2026-03-20 12:48:44'),
(216, '8a8e3e6e33dcfe0321730662a7f298f06251b2aed26cbb3f44caa1daaf2b8756', 3, '2026-03-21', '2026-03-21 12:36:36', 0, NULL, NULL, '10.56.5.98', '2026-03-21 04:35:36'),
(217, '3a38193c83f06d9ac8d42de7274c0c36e776888112a12a6fb82a35b272f681ee', 3, '2026-03-21', '2026-03-21 12:40:29', 0, NULL, NULL, '10.56.5.98', '2026-03-21 04:39:29'),
(218, 'f3954aa87ade808b8c421f57394d61f18c20e8ac947cdf70f015b5e92a1a32a8', 3, '2026-03-21', '2026-03-21 12:40:41', 0, NULL, NULL, '10.56.5.98', '2026-03-21 04:39:41'),
(219, '0e685c4bc59a99b5232e1a89d4b948bdac44f89e5616e7ce0547ba5b10244cc7', 3, '2026-03-22', '2026-03-22 11:18:18', 0, NULL, NULL, '192.168.68.141', '2026-03-22 03:17:18'),
(220, 'b2596ae18fc7c953ca210ec44ed222f42842f8b18b3dd38211a10675ec7b77d7', 3, '2026-03-22', '2026-03-22 11:18:48', 0, NULL, NULL, '192.168.68.141', '2026-03-22 03:17:48'),
(221, 'bb8843668a888a9cbbbfb795bf7c36bce61206150809dfa94621a491666b82b4', 3, '2026-03-22', '2026-03-22 11:19:18', 0, NULL, NULL, '192.168.68.141', '2026-03-22 03:18:18'),
(222, '98d9deba9b336a965b7cfcb400cfb4173f9ee8bf9a14775206a36400d6c1e208', 3, '2026-03-22', '2026-03-22 11:19:48', 0, NULL, NULL, '192.168.68.141', '2026-03-22 03:18:48'),
(223, 'e57ee1cabbe3fa32b5615f2b4b53402f73a8945f2582b9b09c2fd9f495d1c1b9', 3, '2026-03-22', '2026-03-22 12:30:29', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:29:29'),
(224, '92ac358dafa12addf4aa871a349559c40a837dfd117425cb0ddcd15c422e5b9a', 3, '2026-03-22', '2026-03-22 12:30:59', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:29:59'),
(225, '8a246be0bcdc136d4ecf21bff80d3d1890645af44dd19a459c46f12b73c2479b', 3, '2026-03-22', '2026-03-22 12:31:29', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:30:29'),
(226, '21593df39a5fe50b67966beb96136982cfbb708fa0f85801eac9b7b69a3d51bc', 3, '2026-03-22', '2026-03-22 12:31:59', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:30:59'),
(227, '5b00c4c3ee8eee1059a6e24257780047f6f95b5aad3d1f0300f442f674eba61b', 3, '2026-03-22', '2026-03-22 12:32:29', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:31:29'),
(228, '21da5af6001c056f337466ad5564815b5e0c95a6b0591e1682f2a8a5f52a5a3f', 3, '2026-03-22', '2026-03-22 12:33:00', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:32:00'),
(229, 'f9915021289f1b47695825115e5c1095daf2ab64b11600b5a3408ceb83aff1f8', 3, '2026-03-22', '2026-03-22 12:33:50', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:32:50'),
(230, 'c4d35b3fd3dfbcd886597f130921a8c262e3fcddd077e3cff52778e9d3933e58', 3, '2026-03-22', '2026-03-22 12:34:47', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:33:47'),
(231, '5f639ee0f5e605af18950c665a2d35b52218b66d02c12d6368b8c3cc69dbc365', 3, '2026-03-22', '2026-03-22 12:35:34', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:34:34'),
(232, '1a13b473f49ed19551b3e1651d3d6d6d02fcd2ed2b3f32dee8c1a833a43fe18c', 3, '2026-03-22', '2026-03-22 12:36:05', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:35:05'),
(233, '9c8e2eb1b61a1ed4ed0b46a19acb9fa2d23fb8a717ff41e8653c01ae964cbec7', 3, '2026-03-22', '2026-03-22 12:36:35', 0, NULL, NULL, '192.168.68.141', '2026-03-22 04:35:35'),
(234, 'ee47d72e14b328ccbe439b70fca73a2f85b8de92d8eaa0c28f23137cedc4ce75', 3, '2026-03-22', '2026-03-22 13:01:12', 0, NULL, NULL, '192.168.68.141', '2026-03-22 05:00:12'),
(235, '297bed73de23faa8dcdf85c49a56e4d51d102f2d67da76a205194195d6a0e4a6', 3, '2026-03-22', '2026-03-22 13:01:45', 0, NULL, NULL, '192.168.68.141', '2026-03-22 05:00:45'),
(236, '73454468c9cfb1827e5b62cef5143e36961ef3ca52ef218853ccd6219abe62ce', 3, '2026-03-22', '2026-03-22 14:52:57', 0, NULL, NULL, '192.168.68.141', '2026-03-22 06:51:57'),
(237, '0ca499699c02b9c4d6b8865942408ac5f3cee02fe57cabd27bbc5d90b2f30ea0', 3, '2026-03-22', '2026-03-22 16:07:49', 0, NULL, NULL, '192.168.68.141', '2026-03-22 08:06:49'),
(238, '712e3822006dc40bd083072db8212fbe8bc9b53aa56a4aa448c5bb1e5659755d', 3, '2026-03-27', '2026-03-27 17:12:13', 0, NULL, NULL, '::1', '2026-03-27 09:11:13'),
(239, '902c92ffe562691b0398c06a0b1d102263d9c24e03701fb29b96164563aef93c', 3, '2026-03-27', '2026-03-27 17:12:44', 0, NULL, NULL, '::1', '2026-03-27 09:11:44'),
(240, '7cfc4b4954bb45c2e243449ec0745170ea4ae785229bdb82fe7a1e10ff726b2f', 3, '2026-03-27', '2026-03-27 17:13:15', 0, NULL, NULL, '::1', '2026-03-27 09:12:15'),
(241, 'cc7154cfb831cb019a86fb68663e6a52ce4770c492eda0cbef4658cbfe6fd7a1', 3, '2026-03-27', '2026-03-27 17:13:46', 0, NULL, NULL, '::1', '2026-03-27 09:12:46'),
(242, '7b91f61e146a2e205e74ba41f42b64c4b615249bf7fe76fc778b7405f71e4ee1', 3, '2026-03-27', '2026-03-27 17:14:26', 0, NULL, NULL, '::1', '2026-03-27 09:13:26'),
(243, 'db47cc3c95389fd01b7d8d6588698cc364fbd1a04d942486c589fff1a724e3e2', 3, '2026-03-27', '2026-03-27 17:15:01', 0, NULL, NULL, '::1', '2026-03-27 09:14:01'),
(244, '2f4118a038f60adb52cd8cbdc889c054825af94be48058c87f7f416473d376ef', 3, '2026-03-27', '2026-03-27 17:16:01', 0, NULL, NULL, '::1', '2026-03-27 09:15:01'),
(245, 'e44b53fd998721a1dbeb2bb1677489ab465dce2ed66f6c01d65d03300eca45ad', 3, '2026-03-27', '2026-03-27 17:16:32', 0, NULL, NULL, '::1', '2026-03-27 09:15:32'),
(246, 'f9d7f10ae15dcc4ef88d89ce6f397e0358e062e52ff3c91f34597249445e5d2f', 3, '2026-03-27', '2026-03-27 17:17:03', 0, NULL, NULL, '::1', '2026-03-27 09:16:03'),
(247, 'b7606c7626dc2a894f6a9f1ab68de0948a17c30b6b7ed68988a75dcec5ee19b4', 3, '2026-03-27', '2026-03-27 17:17:34', 0, NULL, NULL, '::1', '2026-03-27 09:16:34'),
(248, '372a403d250e71dfbf5d32a6ee7802c378a2c9b447b2e40fead48c32e3b9a76f', 3, '2026-03-27', '2026-03-27 17:18:05', 0, NULL, NULL, '::1', '2026-03-27 09:17:05'),
(249, 'd30e1c86dc82622030279591d483fa9677d2aefbe64f01dc0b2ccd2a1b39876c', 3, '2026-03-27', '2026-03-27 17:18:37', 0, NULL, NULL, '::1', '2026-03-27 09:17:37'),
(250, '437931e222707c5664a1a45c46614e2b5e8bd72638194e7fa8a481e3a229ae75', 3, '2026-03-27', '2026-03-27 17:19:10', 0, NULL, NULL, '::1', '2026-03-27 09:18:10'),
(251, 'ff33a40308f16cde3a5b00be65873e8b5236f5cfc4011f7bb5a666414ffd8805', 3, '2026-03-27', '2026-03-27 17:19:41', 0, NULL, NULL, '::1', '2026-03-27 09:18:41'),
(252, 'a083d2388e7aa9445e2a247619dd8c245fef86be82e5c9425c6f2fcd3d87ab7a', 3, '2026-03-27', '2026-03-27 17:20:12', 0, NULL, NULL, '::1', '2026-03-27 09:19:12'),
(253, '286c7dc5dff488e16a2b5c73479e5fe00fd6181b61e66cdec3183f7299071fc7', 3, '2026-03-27', '2026-03-27 17:21:01', 0, NULL, NULL, '::1', '2026-03-27 09:20:01'),
(254, '0b97fa93048d61ac23917daf6e815a3b8c54f769c067b7c5f92810a9d7b8c8bf', 3, '2026-03-27', '2026-03-27 17:21:33', 0, NULL, NULL, '::1', '2026-03-27 09:20:33'),
(255, 'd0884adb3ff568502dd62233a62c2a39afd2b415dd3afc903190957e1039dd93', 3, '2026-03-27', '2026-03-27 17:22:04', 0, NULL, NULL, '::1', '2026-03-27 09:21:04'),
(256, '200db7fbaa0ec4f1b7508c01e9b6d54f534c21fc2f35135fda26a4b7f1316082', 3, '2026-03-27', '2026-03-27 17:22:35', 0, NULL, NULL, '::1', '2026-03-27 09:21:35'),
(257, '25b6b3eb069285f8b98f0fe7f589adc363b2f76d326589a4de442019a23ef895', 3, '2026-03-27', '2026-03-27 17:23:06', 0, NULL, NULL, '::1', '2026-03-27 09:22:06'),
(258, '48055f3aeb6d0b7c91ea892b870fe61416101cfd51455f4a1a32f80d2ae68599', 3, '2026-03-27', '2026-03-27 17:23:37', 0, NULL, NULL, '::1', '2026-03-27 09:22:37'),
(259, '9aa40de259d2dedd1398fb44aa66c4e7beb9f9684a8e44ca42fa287c6bc5ee90', 3, '2026-03-27', '2026-03-27 17:24:09', 0, NULL, NULL, '::1', '2026-03-27 09:23:09'),
(260, 'd63cf4ade6c324ed79c08f16b6c3dc2887e9cc48f99fa9d2edf52f728b71d61f', 3, '2026-03-27', '2026-03-27 17:24:40', 0, NULL, NULL, '::1', '2026-03-27 09:23:40'),
(261, '770a3ac27b3fa32073d5e1650123c2fa52fe6188b0cf96456c75e1ef842d3814', 3, '2026-03-27', '2026-03-27 17:25:25', 0, NULL, NULL, '::1', '2026-03-27 09:24:25'),
(262, 'c8f31b96ce65ced1b67ed816ddff0bae189fc9f6a17290d646f10de601901f2e', 3, '2026-03-27', '2026-03-27 17:25:55', 0, NULL, NULL, '::1', '2026-03-27 09:24:55'),
(263, 'ba6b581a2947eed9081a73a486123fa746bbac1fb184f356e1d4c595538d3285', 3, '2026-03-27', '2026-03-27 17:26:26', 0, NULL, NULL, '::1', '2026-03-27 09:25:26'),
(264, '1ee9202edbcd5cd421fefd8fee4ae499d3bc82ff02d3bed66e8b459bc87c1b3d', 3, '2026-03-27', '2026-03-27 17:26:57', 0, NULL, NULL, '::1', '2026-03-27 09:25:57'),
(265, '76a9aaa7ba46e7e74096064ed0d870e902b95105238c03440bda95c560823c10', 3, '2026-03-27', '2026-03-27 17:27:28', 0, NULL, NULL, '::1', '2026-03-27 09:26:28'),
(266, 'b13c0d4677b400f6e472f018986c3a2ad26438a41887fd956d5eaa07b3814725', 3, '2026-03-27', '2026-03-27 17:27:59', 0, NULL, NULL, '::1', '2026-03-27 09:26:59'),
(267, 'd08df5ffccae07cb7094b3014d9698c80355dd8fcd8b1b118e3ae6049509502e', 3, '2026-03-27', '2026-03-27 17:28:30', 0, NULL, NULL, '::1', '2026-03-27 09:27:30'),
(268, '8964b237c6a1efcdc7c43445fde7000e4a68773ac6f53faa704401063bcf2c1c', 3, '2026-03-27', '2026-03-27 17:29:01', 0, NULL, NULL, '::1', '2026-03-27 09:28:01'),
(269, 'ffec74abba17a0f02baee6db5dfdede3053a66378b10a33f877e219db81915d2', 3, '2026-03-27', '2026-03-27 17:29:32', 0, NULL, NULL, '::1', '2026-03-27 09:28:32'),
(270, '9b75e5386e65b21abe49d000f36163ec1de9484d68b4561a56e628e3c157d3bf', 3, '2026-03-27', '2026-03-27 17:30:03', 0, NULL, NULL, '::1', '2026-03-27 09:29:03'),
(271, 'a9dd8541a06c42df215d5d51d00789eee180d15741e62f04d2287bc92b1647a7', 3, '2026-03-27', '2026-03-27 17:30:34', 0, NULL, NULL, '::1', '2026-03-27 09:29:34'),
(272, '1f5ad493a21e28ed219dd40b763ebf7506fb4df448adcf206e57ba037062ccca', 3, '2026-03-27', '2026-03-27 17:31:05', 0, NULL, NULL, '::1', '2026-03-27 09:30:05'),
(273, '81c4c7b54a6ce2745896480b9d4c9511f1a9f1746131c5ce6b1b1915877bca0c', 3, '2026-03-27', '2026-03-27 17:31:36', 0, NULL, NULL, '::1', '2026-03-27 09:30:36'),
(274, '0b1e30557dcbc359555b54fa171023ea73f5a3449c17b0ead7c5304f22c91b4f', 3, '2026-03-27', '2026-03-27 17:32:08', 0, NULL, NULL, '::1', '2026-03-27 09:31:08'),
(275, 'c7242d5f36fd09f6d07bf127b18a9b9aeb11a270ab6a11dafe03e1e2a19f1064', 3, '2026-03-27', '2026-03-27 17:32:39', 0, NULL, NULL, '::1', '2026-03-27 09:31:39'),
(276, '9beaa6f8dd2a79448f37fdf5d93549d8e6b40e75ec4149f7642cfafd99ffd45b', 3, '2026-03-27', '2026-03-27 17:33:11', 0, NULL, NULL, '::1', '2026-03-27 09:32:11'),
(277, '18fa50bb502b0d0b885040beb6948f9034900af8034bb93933610b96b55b28e8', 3, '2026-03-27', '2026-03-27 17:33:42', 0, NULL, NULL, '::1', '2026-03-27 09:32:42'),
(278, '05bb4cf63e3c2796d78175b7787128230ffccf4a06bd551119fbffd06d426894', 3, '2026-03-27', '2026-03-27 17:34:13', 0, NULL, NULL, '::1', '2026-03-27 09:33:13'),
(279, '653483fd66dba2912dee2635a523c32ad881ca50b88f6504e2ba0c8947208bbe', 3, '2026-03-27', '2026-03-27 17:34:44', 0, NULL, NULL, '::1', '2026-03-27 09:33:44'),
(280, '10c7516ba426f4b830ab016be86ab9ebcc898d656e4f013e197a5043aa4ce277', 3, '2026-03-27', '2026-03-27 17:35:15', 0, NULL, NULL, '::1', '2026-03-27 09:34:15'),
(281, 'b3a835543cbc88b9fce113a278ae616a486c110f6666aae8c6a090303b5cef32', 3, '2026-03-27', '2026-03-27 17:35:48', 0, NULL, NULL, '::1', '2026-03-27 09:34:48'),
(282, '76b89c468e13423e3dbcb538e1f5909ff25bb9396df9602e1d0b1d49c11f40d9', 3, '2026-03-27', '2026-03-27 17:36:19', 0, NULL, NULL, '::1', '2026-03-27 09:35:19'),
(283, '20b80c5ea534177b1fa0d66db4eb79b1b73cf2d8f9ef09acd9f4a490be716962', 3, '2026-03-27', '2026-03-27 17:36:52', 0, NULL, NULL, '::1', '2026-03-27 09:35:52'),
(284, '5343951345b41406e59494d0a70dba6af574842258434f879d90a4a30ce6f6a7', 3, '2026-03-27', '2026-03-27 17:37:25', 0, NULL, NULL, '::1', '2026-03-27 09:36:25'),
(285, '0918ee15cd40aa9a358575c57e94017dd91a4ecf21899082cdcb8b4791afdc60', 3, '2026-03-27', '2026-03-27 17:38:08', 0, NULL, NULL, '::1', '2026-03-27 09:37:08'),
(286, '3185e7cbd03a55293a6bfd0ed2cc7187bb531e37919e6e3b02500ef6b300c804', 3, '2026-03-27', '2026-03-27 17:39:07', 0, NULL, NULL, '::1', '2026-03-27 09:38:07'),
(287, 'f645afb14d07251106b92606af07107da95c6cc70c92a2e1d1a203db3930473d', 3, '2026-03-28', '2026-03-28 11:03:08', 0, NULL, NULL, '::1', '2026-03-28 03:02:08'),
(288, 'dc2c89a7bc20a548c42190e4f7071eb5bf06660a374c2de489e257a260f81bec', 3, '2026-03-28', '2026-03-28 11:03:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:02:48'),
(289, 'ce43cb70ce542193f6ff17bd698a29fc515c7ea8a974f529adaff38d160cfdc0', 3, '2026-03-28', '2026-03-28 11:04:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:03:18'),
(290, '07a5ece9c994a8c08973a5586cbc3e3d0b3b23f7b9294218663482487cc64196', 3, '2026-03-28', '2026-03-28 11:04:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:03:48'),
(291, 'd52b962e74aad9474129ffdef361ba477c4c1a0bc973ee500c90252d53293cb1', 3, '2026-03-28', '2026-03-28 11:05:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:04:18'),
(292, 'a50de43e2d9a814222663b45f8692b3bf78e166214991975db9416123144ffcf', 3, '2026-03-28', '2026-03-28 11:05:51', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:04:51'),
(293, 'b7446b0be612f0c76e01b9d742b32076601df08d583955de3dc371b495e96824', 3, '2026-03-28', '2026-03-28 11:06:22', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:05:22'),
(294, 'd85db0bcc8c9b67517bb78906e8f86a38a0707243b007445c7bed0b29de3266f', 3, '2026-03-28', '2026-03-28 11:06:52', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:05:52'),
(295, 'f139ec01ce654a0dc29c58484d2aba09c94750156cd35321cd0fed46352a6d73', 3, '2026-03-28', '2026-03-28 11:07:22', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:06:22'),
(296, '73af6cff79bf6ad1961d756d9195c0f96a8a82830fdc677339e33891556f80b7', 3, '2026-03-28', '2026-03-28 11:07:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:06:53'),
(297, 'ab9e75ecd24ff652cc88b6fea8dbfba5680b31568a0e6d6d495ea02f10a243c6', 3, '2026-03-28', '2026-03-28 11:08:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:07:24'),
(298, '013dbc81b647c9345194e3f7f8f6e2049c3eb33cec3ca8039e9e9ac852baaf35', 3, '2026-03-28', '2026-03-28 11:08:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:07:55'),
(299, 'b22276f34e44944b11985d9378c0d143560ebd5e6e71d9d8f49c48f9d913c2cd', 3, '2026-03-28', '2026-03-28 11:09:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:08:26');
INSERT INTO `ta_attendance_tokens` (`token_id`, `token`, `generated_by`, `generated_for_date`, `expires_at`, `used`, `used_by`, `used_at`, `ip_address`, `created_at`) VALUES
(300, '33ae4b7a319db6fb46f05b8664491bb6da8c83763265bfe44b53ed0be13c617d', 3, '2026-03-28', '2026-03-28 11:09:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:08:57'),
(301, '5a5897fbb7d0b696536ee53b9fc2186cc098d063f461251b85a4d7fd2e79ac5c', 3, '2026-03-28', '2026-03-28 11:10:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:09:28'),
(302, 'fc94b6a9dd6985d61699c7768b18a008dba6c68dfc8e32c2490f1a1cb83f10e3', 3, '2026-03-28', '2026-03-28 11:10:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:09:59'),
(303, '22560f2e3d465290af69e991e4740256e54e1cecab99beed99d5ec91dbde3937', 3, '2026-03-28', '2026-03-28 11:11:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:10:30'),
(304, '2d2d18dba74f01ece4c3c0b3479a95387f0db30d06e764db05d6f7139524198f', 3, '2026-03-28', '2026-03-28 11:12:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:11:01'),
(305, '504145aa9fdb08b32ae64acfa6ab01ab63e8e4908a6393b906ba5f3e06574d7f', 3, '2026-03-28', '2026-03-28 11:12:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:11:49'),
(306, 'e827c4af95c8c75c9154fbef59308d1ae2809f6b2675ffe5e4322cf6bdb8cfae', 3, '2026-03-28', '2026-03-28 11:13:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:12:48'),
(307, 'be09696382087df9710697f53b53a208eb4e723b74781c4c1d2f278c8880257c', 3, '2026-03-28', '2026-03-28 11:14:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:13:48'),
(308, 'c304f5db1ebdc0e18a2fe6d9701c06f84e2447ce7be830848a61524e2b9d0843', 3, '2026-03-28', '2026-03-28 11:15:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:14:29'),
(309, '6fc77eb07f578d1dafb00a791bc7d8bc65c55bc4092bb16168d944b97f695f52', 3, '2026-03-28', '2026-03-28 11:16:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:15:00'),
(310, 'ec97d76893a8a122cfab0f0e24db8d5e275807d025463c9b985c266ebf903d92', 3, '2026-03-28', '2026-03-28 11:16:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:15:42'),
(311, '211622a6890699aecae7049cd71b1602204f2b3877627facd2aa0d36380d5ab1', 3, '2026-03-28', '2026-03-28 11:17:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:16:24'),
(312, 'cc35ee1e59d2c9e2af57337f31b69aa8856033a5f38037e4d48e08d6083070e1', 3, '2026-03-28', '2026-03-28 11:18:11', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:17:11'),
(313, '76b2c602f43e865bb8ac3b31046d60d209fa9dfef943095a7605b0ffac518ac1', 3, '2026-03-28', '2026-03-28 11:18:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:17:44'),
(314, 'eb0413258723ab4ea052664acd055b69314fc3c9de1e30ec6d9ecf90eabc7295', 3, '2026-03-28', '2026-03-28 11:19:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:18:28'),
(315, '0b926152f0e98a6beef63a7ede13f4fd044c8b8a71668d91f3d43f35b143cedd', 3, '2026-03-28', '2026-03-28 11:20:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:19:28'),
(316, 'a142724465ad5bb601aa1d5e79c4f75e5700c39f9668ee2f230022071fc22cab', 3, '2026-03-28', '2026-03-28 11:21:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:20:00'),
(317, '1dac411625520546c92a86b3419f93dfaa82df1c45f3f740dcb3294174a65e30', 3, '2026-03-28', '2026-03-28 11:21:33', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:20:33'),
(318, '78cda73de2c9ad0cd2886235820ab24cd7db636cc6a1aad5fa58bf8490a1d831', 3, '2026-03-28', '2026-03-28 11:22:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:21:07'),
(319, 'bf77e71b474d23448d0327469f61963fd46237b6c68f1d342b9e39df9d5ae415', 3, '2026-03-28', '2026-03-28 11:22:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:21:40'),
(320, '13a273e8535a3fbe54e03dc6818cabb075eba8a12ebf2239d74379fac567f5f6', 3, '2026-03-28', '2026-03-28 11:23:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:22:14'),
(321, 'dc520b59a107b2629ba8d547b9bc825cc37b3ffb52652445c75391ed5f4e9366', 3, '2026-03-28', '2026-03-28 11:24:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:23:14'),
(322, '3b9e500137b20568a2fc06ffa23d8ec7605604a0620160f9a0d2062ceb975428', 3, '2026-03-28', '2026-03-28 11:24:46', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:23:46'),
(323, '705cf04adf09d2dbf57737ee505cf74ba00c8ac71af1c7b632497cf8073d941a', 3, '2026-03-28', '2026-03-28 11:25:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:24:17'),
(324, 'fa91eafd8d3b5355c81214937cd17870a9be18d86479423f7523459adf4479ea', 3, '2026-03-28', '2026-03-28 11:25:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:24:48'),
(325, 'd7b232b77b3efe9b2ea3a676fb496fd91b2509cc2214e77b9fa1db85651627b8', 3, '2026-03-28', '2026-03-28 11:26:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:25:19'),
(326, '6c5cb097e6f00c4c28078d71318b07002644ad90e9d9be592f6814803ba398ae', 3, '2026-03-28', '2026-03-28 11:26:50', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:25:50'),
(327, '8474a4153e1e4d97ed799006e5c9c7ef3abf11f700a57f80b5c9bb5227e48d99', 3, '2026-03-28', '2026-03-28 11:27:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:26:24'),
(328, '52183e57027bf5f7ee0c988361ff6791b58efd1a9fc46c09046683c843646df3', 3, '2026-03-28', '2026-03-28 11:27:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:26:56'),
(329, 'fde90034900b99fbf2122607a31455fc6a162ee14e847caccdca906561d69a5f', 3, '2026-03-28', '2026-03-28 11:28:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:27:54'),
(330, 'dbd9c20b962739be1ac076ebf0ce463a31422b001929147b57215022a6b7cf47', 3, '2026-03-28', '2026-03-28 11:29:33', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:28:33'),
(331, '74ba64a830e92463b38978432696b000213e66a8c2e4288915a898278e0c5311', 3, '2026-03-28', '2026-03-28 11:30:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:29:04'),
(332, '8f0ff47e98fe7a6f4bf267f8b5fb73f05c41298d4c33e8c0bb88e4e72f8fecd8', 3, '2026-03-28', '2026-03-28 11:30:35', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:29:35'),
(333, 'f7aa51627e9e0a1aedfb4be160821cba8a2165d57abbf815794fe7fa985377bb', 3, '2026-03-28', '2026-03-28 11:31:06', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:30:06'),
(334, '59607dc4b807ef6a9b5c112894302b7e2ada86cfb1f09a88deea9d9d258d8d3b', 3, '2026-03-28', '2026-03-28 11:32:06', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:31:06'),
(335, 'f5eadf979c46341d9bcbb424ca2e0e91b37aedf87d3e8dd09b2cd923a3360ed8', 3, '2026-03-28', '2026-03-28 11:33:06', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:32:06'),
(336, 'a90e46be3774189c5c18485b1a17aed3a8ab0e1d703ac9ab120b6eec8abb3c8f', 3, '2026-03-28', '2026-03-28 11:33:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:32:54'),
(337, 'd8d9f78f0dab49646cce216f38dd54c329acb42cf690ddb5c975f1520933e0f0', 3, '2026-03-28', '2026-03-28 11:34:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:33:54'),
(338, '55888512007e1dd1b45e774e09aa41b17699a0317491c359e39100fa48a4a5ea', 3, '2026-03-28', '2026-03-28 11:35:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:34:36'),
(339, '8818f7ce2800cd6f487a73039b86e6255de1b4fc5ff0d97f8477b80484c69f89', 3, '2026-03-28', '2026-03-28 11:36:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:35:36'),
(340, 'f88a6b53d3a2ac910df0eb4b1326179ba754294e1b97a14ec2e41a1d035ceb4e', 3, '2026-03-28', '2026-03-28 11:37:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:36:07'),
(341, 'ead90c53cd1c9f27a7f0bccbc57a9339dbdba982bf7342f5875b483576b2fbc4', 3, '2026-03-28', '2026-03-28 11:37:38', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:36:38'),
(342, '4b3e2493dcb126661a8bed0c00947d0938ba5eeaa5092e4d5494355950c6d9d6', 3, '2026-03-28', '2026-03-28 11:38:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:37:30'),
(343, 'f12f231c20355f0929ef3e5148d14b366a991d30190a83e4b69596e7e60be7cb', 3, '2026-03-28', '2026-03-28 11:39:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:38:01'),
(344, 'b52022497a7e880348338e9bed3c884b1b8f4201939bb3e5ccf732e4a42f7a73', 3, '2026-03-28', '2026-03-28 11:39:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:38:56'),
(345, 'abbe046b0214c7ab5a933aef885df4b056daac56888bf12c63f1bb73b5e993c4', 3, '2026-03-28', '2026-03-28 11:40:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:39:29'),
(346, '05b92f5781ec6088225b6db27ccbb5ed8c3a76967bd80506c746ce5c49613956', 3, '2026-03-28', '2026-03-28 11:41:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:40:29'),
(347, 'b7f6e9c6a954aa482b7af54d42458dc5224b58142741cf36e8cc8f4184766dda', 3, '2026-03-28', '2026-03-28 11:42:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:41:29'),
(348, '68a8b0bce07a2a71f38b783590810c121a8a535e9b8daf9948694bd883a83079', 3, '2026-03-28', '2026-03-28 11:43:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:42:00'),
(349, 'e8658a2663bb2f6a9178b2f496b8d412daa13a0fe84b6a296d57582e3c5f45e0', 3, '2026-03-28', '2026-03-28 11:43:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:42:36'),
(350, '2141edceb123f9cfe1a4ce76562970ff2077b2bf85661c4d22a9881a08778aa2', 3, '2026-03-28', '2026-03-28 11:44:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:43:23'),
(351, '6f342149551a8137473924040dc57168c6d3a7ededd87e5226092ccc0f2d8f64', 3, '2026-03-28', '2026-03-28 11:45:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:44:01'),
(352, 'be1dd0408fc4b3698cbe7b1ef4ad60eff184df323e20626b61e5b477511ea265', 3, '2026-03-28', '2026-03-28 11:45:32', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:44:32'),
(353, 'c886ceb2252e16480b08b6eee8fdb89640059e527edd74d3802608f34533d22c', 3, '2026-03-28', '2026-03-28 11:46:03', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:45:03'),
(354, 'bf0993f824600ed586395fa46bfdaff9f62a7e410dbc980584ac9452f01905f7', 3, '2026-03-28', '2026-03-28 11:47:03', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:46:03'),
(355, '16771ce947985dea8566a8bc6a2eabaed8d3a02071deccf78fcea9d5f7ab243e', 3, '2026-03-28', '2026-03-28 11:47:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:46:36'),
(356, '9b562918416704204148a8236a04b191b63803af31ea41626098bdabbd9298a5', 3, '2026-03-28', '2026-03-28 11:48:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:47:07'),
(357, '3a4644343df2b3fee370fa5c227b52e32715d19cf1d7926c9e4aea64200b33fe', 3, '2026-03-28', '2026-03-28 11:48:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:47:45'),
(358, '0b7acb3634adb6be65338d04d93ed491651f1e2773fb7be58a310c55a73f9905', 3, '2026-03-28', '2026-03-28 11:49:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:48:42'),
(359, '10609ea22404366d64f589dbdc638297a1d8116faedfd8afc3e4045ea04a0cac', 3, '2026-03-28', '2026-03-28 11:50:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:49:13'),
(360, 'e1911b20ff0911683deb72b968c4303619c1ef899aa0edcc0a2f5db4224233e3', 3, '2026-03-28', '2026-03-28 11:50:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:49:44'),
(361, '5be1b21a068c76298492f7d9d298ce1c4f82c1257adde2b3de1675ca40ac4b50', 3, '2026-03-28', '2026-03-28 11:51:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:50:44'),
(362, '17db58be1e9b0e6297e3ce5b5c88e15dd78fa6110f94021dee6a8668d0334317', 3, '2026-03-28', '2026-03-28 11:52:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:51:30'),
(363, 'dab8099dd1824df58cc7f7d6b5fa0b3c5eef0d477831791dd3f7bed704f8fe1a', 3, '2026-03-28', '2026-03-28 11:53:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:52:01'),
(364, '961ecc544a263ea6cb7e936f2340aa633ae6b8ac8ae0ff7b23e7f76ccada048d', 3, '2026-03-28', '2026-03-28 11:53:32', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:52:32'),
(365, '221bbf7a0f241c0a04c5b110fe44c961a0004c875f19aa93965c3a783d9e4251', 3, '2026-03-28', '2026-03-28 11:54:06', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:53:06'),
(366, '6eda8f43b096128b696dfaf1bdd4f6806bf024fd6cc24b41f3163d8a491d7190', 3, '2026-03-28', '2026-03-28 11:54:52', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:53:52'),
(367, '9ee14ffa151579fe38673bfba0d466d0324149062b0f83e9265b9cdc14128e15', 3, '2026-03-28', '2026-03-28 11:55:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:54:23'),
(368, 'c4d268ab2b790afbba433f90bda4e4c23eede113c95edbb46ef72697402a1a51', 3, '2026-03-28', '2026-03-28 11:55:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:54:36'),
(369, '984eee8060ef3836be059aca6255618bf8705e142f1f6bd968493e2cf3726b14', 3, '2026-03-28', '2026-03-28 11:56:06', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:55:06'),
(370, '9dd01c714b6f4350d5ff1645f649288718c85a2a344c72a98449e94b207745c2', 3, '2026-03-28', '2026-03-28 11:56:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:55:42'),
(371, '5286f2b093d26805df997dc62d67044f6ecb71e38cb3100902f40a9f8b5aba84', 3, '2026-03-28', '2026-03-28 11:57:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:56:13'),
(372, '49dcc853f3a077346b4aba97607e2720046734eb5166ceaba0245cc985ffb3e8', 3, '2026-03-28', '2026-03-28 11:57:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:56:48'),
(373, 'bdc3bc191f7821be89091a114077d5ce16cc9f77ff64585e153adee210e16117', 3, '2026-03-28', '2026-03-28 11:58:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 03:57:19'),
(374, 'b21116807a0a821a20d88fbd9b5ad233f6399e6be0aa565295ab64ed412ea15c', 3, '2026-03-28', '2026-03-28 12:16:21', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:15:21'),
(375, '8667bfba9079c8fabd7e0fecc502cb503331bbf710560659a119703957e518b4', 3, '2026-03-28', '2026-03-28 12:16:51', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:15:51'),
(376, 'c7317da10ef27eb230981504ee7d5269a73bca0ee77f04c4021f27ee80ad5836', 3, '2026-03-28', '2026-03-28 12:17:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:16:47'),
(377, '56d60e25cc42d49678fc2e03c31d0eedab3fe0b537af194efe5d5439c38d1d9a', 3, '2026-03-28', '2026-03-28 12:18:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:17:23'),
(378, '886de22b3d87afef333bc314ad5678a5f288fd80a97b3cc8dd1ffa20afb3bc89', 3, '2026-03-28', '2026-03-28 12:19:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:18:04'),
(379, 'e766dbcd96f4781ae28f4238adadbd7597d8f8845e840bfdb851f8a30a53922c', 3, '2026-03-28', '2026-03-28 12:19:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:18:34'),
(380, '4bab5861a75063fc6a4b44197e79cfd20ab7df1f46413d3642ae85f0a95a0cc1', 3, '2026-03-28', '2026-03-28 12:20:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:19:04'),
(381, '13084317f1d8cbbbf1f56b5bf1f60f0e2b869a226a12a529e22309477343df82', 3, '2026-03-28', '2026-03-28 12:20:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:19:57'),
(382, 'cfae07e6a94e3718cf1c498f7c61acf1ce9cde323a389ae107a8aa47d474dc58', 3, '2026-03-28', '2026-03-28 12:21:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:20:28'),
(383, '1b3d2236d1d5c22fbd95d2e43f27a48fd748373f4a38d448e780e5f53689546e', 3, '2026-03-28', '2026-03-28 12:21:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:20:59'),
(384, '7375fb344298886fb3e7ec9204d2fc715ccd95047fe4b1f1df2e06d5853a36bd', 3, '2026-03-28', '2026-03-28 12:22:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:21:30'),
(385, '7e05849c9f42822adbcdde0550885b8bc03a3a2c62af782a0f81f62d593ea0e2', 3, '2026-03-28', '2026-03-28 12:23:02', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:22:02'),
(386, '8ea529cfde916b7ccbf46ba8f3da1892144ed106d53d9ef9ba371bdf75b4cfe8', 3, '2026-03-28', '2026-03-28 12:23:03', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:22:03'),
(387, '67e51bf4e311806a74459d8760eb48657ef0262225b719607b4381e962847639', 3, '2026-03-28', '2026-03-28 12:23:33', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:22:33'),
(388, '3560a562af45bf88ba510aa2548b2de946a88428cb7496a56e10b44d62454574', 3, '2026-03-28', '2026-03-28 12:24:03', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:23:03'),
(389, 'ca70a2c83bfcbf992dcdd247ee8a858ecb340999a2d84155a7f469790f368d68', 3, '2026-03-28', '2026-03-28 12:24:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:23:34'),
(390, '9fa67e1babddea342f963960b313b3439de7edbdc88921e8a4dd9a80f5e8bd94', 3, '2026-03-28', '2026-03-28 12:25:05', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:24:05'),
(391, 'e0e599468b107f2571b6745c6352f59a65276b9c88895e5a5a5a7a3f1f8af33f', 3, '2026-03-28', '2026-03-28 12:25:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:24:36'),
(392, '112d7d4920f4f17df4fa791ca44394ec8e827d8f5f2230b2d5ed18eacedf59c6', 3, '2026-03-28', '2026-03-28 12:26:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:25:07'),
(393, 'fc96295886cc9d71c14e2d34f3bac327d7c0f62d1d74e9bce830f769b762cbc9', 3, '2026-03-28', '2026-03-28 12:26:38', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:25:38'),
(394, 'ec5b3644759db0449cc389c7304f5ef7104ddaa56435a2b749c89f57bb505db3', 3, '2026-03-28', '2026-03-28 12:27:09', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:26:09'),
(395, '059d20786bb8fe1ec1377c6c586b5ab0c0463f7b0aade9377b2c0829018d6991', 3, '2026-03-28', '2026-03-28 12:27:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:26:42'),
(396, '9bab5cf1129e155a99ccc8652174176d27527855a014afbd378908d8550b62fb', 3, '2026-03-28', '2026-03-28 12:28:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:27:13'),
(397, '748e1a835466856d9ea504d5f2464637913cc5f712a7fdf60680469f2c9c9653', 3, '2026-03-28', '2026-03-28 12:28:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:27:44'),
(398, 'f87930ffada867ef12a507404117537141373c19161bcd39dada2ee88e3b1802', 3, '2026-03-28', '2026-03-28 12:29:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:28:15'),
(399, 'd7fb375a21e06f8ffead41e442bc6ab8e0648e77ab64015b4b5bc23a49bf6c58', 3, '2026-03-28', '2026-03-28 12:29:46', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:28:46'),
(400, '7c5e9ea924424b7c3be97202a5a8c25aa5084ba2cc562c3e46e50b94a36aef90', 3, '2026-03-28', '2026-03-28 12:30:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:29:17'),
(401, '55402f12ea7229bad4f16486929a6d690b9b0d0ea670547c6d7010af820e55b0', 3, '2026-03-28', '2026-03-28 12:30:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:29:48'),
(402, '1df7f0f86d743c391fd36cc79b3b26381dc5b1ed88dd14618e09e89f57569570', 3, '2026-03-28', '2026-03-28 12:31:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:30:19'),
(403, '2d6f183ac8c57f3d538c2ca2065e1c685dd93b409ca4b6bb795ec8b2c1d877b5', 3, '2026-03-28', '2026-03-28 12:31:50', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:30:50'),
(404, '5fc6a63f11904b6b70e3510f1d7a7378224ad96fedda48772f13090065d4de45', 3, '2026-03-28', '2026-03-28 12:32:22', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:31:22'),
(405, '6ba46a6c8a8a17e23da7adc9173f6f99649373ea33c0079e7d7967d265332296', 3, '2026-03-28', '2026-03-28 12:32:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:31:53'),
(406, 'd5a5262492c07195e5fac0eaf7128b355aabf7c1e82cd6960183989f2aa2b306', 3, '2026-03-28', '2026-03-28 12:33:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:32:24'),
(407, 'f1d00f7ee79c0d9e8746a8ed26edd02d00cc966383943c9a6fd682ba7313bd34', 3, '2026-03-28', '2026-03-28 12:33:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:32:55'),
(408, 'bd03019b87aee0ff6d775a49dd756b9bbbf945b768f9951fff83f3693cab53c6', 3, '2026-03-28', '2026-03-28 12:34:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:33:27'),
(409, '1bec2180c32567928c7dc01c875ccf061a8be07bb717e57dcb5d02464d6d7f4f', 3, '2026-03-28', '2026-03-28 12:34:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:33:58'),
(410, '157063f0f008b573e97f40d5c137a5f2dac957229451b95252d4aefbb2bed210', 3, '2026-03-28', '2026-03-28 12:35:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:34:29'),
(411, '526617ea63dae0419a01ac8b868abf1fcf9c5710ad1c4609af5e917a62f82144', 3, '2026-03-28', '2026-03-28 12:36:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:35:00'),
(412, '9bab9ea1e00b60f8c0e8b935e24606b2c2ce5ea732a499e276f5165b4b844ca9', 3, '2026-03-28', '2026-03-28 12:36:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:35:31'),
(413, 'c2e82de0b1182ef794b1431f847f00a0c4bba4fa08486ffed68c8a09071e3fa7', 3, '2026-03-28', '2026-03-28 12:37:02', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:36:02'),
(414, 'b1c7c196457c673e278eabadde6ffc568a5b6403425294ab0a6a4b2ea5f117ca', 3, '2026-03-28', '2026-03-28 12:37:33', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:36:33'),
(415, '4fdebc072ad678c5d1a643003d9953fce3da220f2263d9937f70f9e364bba44b', 3, '2026-03-28', '2026-03-28 12:38:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:37:04'),
(416, 'b85141dd963de9bfd92073effc41d2ffbcc3725618880344d8435b0dcad1dc18', 3, '2026-03-28', '2026-03-28 12:38:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:37:34'),
(417, '1e7960169036fb85ac0d68312f27f509442d0bd2c7f471bd3ded68f725400839', 3, '2026-03-28', '2026-03-28 12:39:05', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:38:05'),
(418, '97549cf8643643e8fd2f9da328e56ffde3d34828d3f184e7a674eda0689373e6', 3, '2026-03-28', '2026-03-28 12:39:35', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:38:35'),
(419, '66b3ef558d516b969dc51dbfe47464a2d9206e1978604a0f52a144d06696a5e6', 3, '2026-03-28', '2026-03-28 12:40:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:39:07'),
(420, '7768807371a60d3444256838f5b2d333e2bdef0465c6d87d35235a42fbf22197', 3, '2026-03-28', '2026-03-28 12:40:38', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:39:38'),
(421, '813b3c486e4d500bd2dae2ab4ed8932e61592234f565dc7eef2fa84bd5fd9804', 3, '2026-03-28', '2026-03-28 12:41:09', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:40:09'),
(422, '263936a99323516e32b8d9174fbf2040960924a27323a0993e84b99a6a8d7e6c', 3, '2026-03-28', '2026-03-28 12:41:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:40:40'),
(423, 'dc7c8f667e7492c56ca2ec258e57bdf80fd6c4c0bb108c1d9972393e7757045d', 3, '2026-03-28', '2026-03-28 12:42:11', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:41:11'),
(424, '7a22bddbb5a1d10b199eb16c855732d12d3ec27b51f9b61712cd904bb71e3be2', 3, '2026-03-28', '2026-03-28 12:42:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:41:42'),
(425, '00d8e03bee8e78a4aaec0678008fd33a8bee51f6bfecd7cf9fb54f8fdceef508', 3, '2026-03-28', '2026-03-28 12:43:12', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:42:12'),
(426, '2bb1658f4a4b04154af21a54e86f2a5a7b886dcf43cc5d4d3a81aca5fc7b1b5d', 3, '2026-03-28', '2026-03-28 12:43:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:42:43'),
(427, 'c5a11ac4ff902310e96ea0cbb1306220e0e5e40a86a4a76ad9653526f23a5df7', 3, '2026-03-28', '2026-03-28 12:44:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:43:13'),
(428, '7eced952c38e10c1f0e84060f0b0d9818f031503e6a3787cb63fd464594d4549', 3, '2026-03-28', '2026-03-28 12:44:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:43:43'),
(429, 'c55f4b2212436b968f85065ef04eb448ad6b36fa9d1513e33252fdbdfa0e5208', 3, '2026-03-28', '2026-03-28 12:45:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:44:13'),
(430, 'a3102cb8e29fe54ae8dbd63441bebc8c0e48c9e68624c3461401aa3d51e2be0b', 3, '2026-03-28', '2026-03-28 12:45:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:44:43'),
(431, '8bf839d8c2f88aab018a8b92ea7a0bc5ee3bf3134949fe4cc7319cd96006ba1c', 3, '2026-03-28', '2026-03-28 12:46:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:45:13'),
(432, 'db8b73fd8a8e9ee5e0ae0aa9cbb6e9428502bf8ba31fab88222ead57b75e15b3', 3, '2026-03-28', '2026-03-28 12:46:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:45:43'),
(433, 'd98952e937f25ef4b8e9c2d3402a87af1839b6cc277446245baf183b408288cd', 3, '2026-03-28', '2026-03-28 12:47:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:46:13'),
(434, 'f14a79b5b877f82667fa54b9bad0474a8164e56d69fe851879914d9ce1b52301', 3, '2026-03-28', '2026-03-28 12:47:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:46:43'),
(435, '4f39bfcc39469b90af7bbdc350bc24d8738982c7a95cee06f3ad1d27a61c2beb', 3, '2026-03-28', '2026-03-28 12:48:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:47:13'),
(436, 'f10bf1ddfbd957383b6ac29da16aec8aa85aa4f4756b05b09fa94fc486eec37c', 3, '2026-03-28', '2026-03-28 12:48:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:47:44'),
(437, '244a029fdeee30f693859f2f017310ec3a7ab13b8c552861ad2e0502fa1d3959', 3, '2026-03-28', '2026-03-28 12:49:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:48:14'),
(438, '3ac90e9dbe1e6d4627bba33b845f8b82a467a975b6d3e6537da0a0e91d5b2ed3', 3, '2026-03-28', '2026-03-28 12:49:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:48:44'),
(439, '3ae1e4f9011dfbd2195d38d813842cb1948aad286cdb305c08b45fe604ef0ad2', 3, '2026-03-28', '2026-03-28 12:50:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:49:14'),
(440, 'cca882af8301534b64a9be57aa7b4525e6063d0fca064992b7e92fd2586cf88a', 3, '2026-03-28', '2026-03-28 12:50:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:49:44'),
(441, '8f8fb1863c8f1df6b00f1d273e687f2478c2caaa739932ad82b92997e5976dac', 3, '2026-03-28', '2026-03-28 12:51:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:50:14'),
(442, 'e770e30a80e64e78f03b10efc7c5f8af4607e0970f9d23125bf5e39d9bba7daf', 3, '2026-03-28', '2026-03-28 12:51:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:50:44'),
(443, '1605daf0db079a4b4be3acbbfb1cac89db70eca678112ed5df7ac30d4dff6743', 3, '2026-03-28', '2026-03-28 12:52:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:51:14'),
(444, 'dfa8aecafc35cfeb58bc0bb422b1f4216bd5dacaa1c76f4c1a313a8a73bc6a7d', 3, '2026-03-28', '2026-03-28 12:52:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:51:44'),
(445, '12b42fbbd70217848de38c93fcb40d893c206799015c5188dc9b4d2180812e52', 3, '2026-03-28', '2026-03-28 12:53:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:52:14'),
(446, '668f10c5cf040c9248886a76ca304828b9fb70cbdf99c59e3590804443073449', 3, '2026-03-28', '2026-03-28 12:53:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:52:44'),
(447, '1ac27c38d39cf3368ebb7e720b3d9b24e414cab4cffaa2f06c4ac3ea722eafe7', 3, '2026-03-28', '2026-03-28 12:54:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:53:14'),
(448, '2b5b4ccd697f7f0d9c96ffd6dc0ed41cd0c83a996c48cfe4c9954b161bc76c2c', 3, '2026-03-28', '2026-03-28 12:54:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:53:44'),
(449, 'efbf96e8bf0cd3bcd111d2f2089a1ee4d3ea228d72431e1fd638ccaf8fd5cb55', 3, '2026-03-28', '2026-03-28 12:55:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:54:15'),
(450, '1b368e70119a530d5abe2ee0440928b02ece0c4001abc981ba5f1c8f6990d133', 3, '2026-03-28', '2026-03-28 12:55:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:54:45'),
(451, 'f9adafed26ce0e4f41c91e16b54b2ef7f4ab479852c30043f16f61c39aa4de25', 3, '2026-03-28', '2026-03-28 12:56:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:55:15'),
(452, '3499e53491a986e131f789c65250a941669920cb9fc2617a3cab0be0b037638f', 3, '2026-03-28', '2026-03-28 12:56:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:55:45'),
(453, 'b94738d0a72d60b6d566c828863fe23ff6525aa52454f94c40e01a8a9b65dd7a', 3, '2026-03-28', '2026-03-28 12:57:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:56:15'),
(454, '8baad5d2fbd263da4d61299c1a2485003c0a791ffca0e465e2b175e3a686425d', 3, '2026-03-28', '2026-03-28 12:57:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:56:45'),
(455, '00bd9dc6d1a8aa45d66d4ef5f6d7e60a3d5d892d214eb0c80ef1aed211739ab7', 3, '2026-03-28', '2026-03-28 12:58:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:57:15'),
(456, '8dfb8260b2c10d2a77b59a26392a7756d0b9a5df54b81e25976725aaedff99fc', 3, '2026-03-28', '2026-03-28 12:58:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:57:45'),
(457, '80fb46118430c9e3942dfa6ef39935c59b134c39c8a72d57ebcf4c73687ed265', 3, '2026-03-28', '2026-03-28 12:59:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:58:15'),
(458, '5759533392b075380d0e48e36e495fc226bb7899c83cc344b3e103a5a2c8f47b', 3, '2026-03-28', '2026-03-28 12:59:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:58:45'),
(459, 'f053fcc7a250223f40eda24b815124d19d6d2836674f06d9f56c009053e7cb07', 3, '2026-03-28', '2026-03-28 13:00:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:59:15'),
(460, '9f073c6501a2ac94048b717c9ac10c726ee86c66f9833c4d3d983a528c78dd26', 3, '2026-03-28', '2026-03-28 13:00:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 04:59:45'),
(461, '6f521efbf36f8e0b015fd78e8e8a42dc1b29899e686dfebbb54c406f756088e7', 3, '2026-03-28', '2026-03-28 13:01:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:00:15'),
(462, '65630c23732953bda742bed0e45e006537878def119949b4fb254b74f31f25ca', 3, '2026-03-28', '2026-03-28 13:01:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:00:45'),
(463, '4856fdbdda9677ce1d4ca3c91e6dae136bebda8455da6fa5c8ef0257c60b2767', 3, '2026-03-28', '2026-03-28 13:02:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:01:15'),
(464, '30719598251a28d9bad46bed4cd6c7ffe3f713a053cff759104969c079cf5fdc', 3, '2026-03-28', '2026-03-28 13:02:46', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:01:46'),
(465, '363f3ac6e4ced21a904a53a5854f0737fcb5f669910e648c934454d3dd3c921b', 3, '2026-03-28', '2026-03-28 13:03:16', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:02:16'),
(466, '6b31a99bda0ce4e98fab548f30d480356afdb010bd83845372f2ed1cfde7c529', 3, '2026-03-28', '2026-03-28 13:03:46', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:02:46'),
(467, 'ab65cffebd86d760e11f5a39032b601df973b7c9c1a371716890cb6a4969afee', 3, '2026-03-28', '2026-03-28 13:04:16', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:03:16'),
(468, 'c33c2266d992d1066d3f90ee5055cae260a470e20f08e567c0bde0be4fb21de3', 3, '2026-03-28', '2026-03-28 13:04:46', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:03:46'),
(469, 'ae4be37f299f49b481ce3ba22637f134aa609a1ba12d7c6069d1fe5d36da0aed', 3, '2026-03-28', '2026-03-28 13:05:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:04:17'),
(470, '23f8669e2c5f81db39d60a086c482baa3a86d438351ddccdf38993994566faea', 3, '2026-03-28', '2026-03-28 13:05:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:04:47'),
(471, 'bc000628afa2b545b2286b470fa72a6086a6b8c93f0277fc047e5545e1885c3f', 3, '2026-03-28', '2026-03-28 13:06:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:05:17'),
(472, 'd7c493a08b00d7de2c2213d1f3ce986bc123687ddf0f6d5d77b5d6e987da174a', 3, '2026-03-28', '2026-03-28 13:06:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:05:47'),
(473, 'fa0d2e331b38810533bc59a2b05a17de9de4708dde1a167e6227f6013e791ac6', 3, '2026-03-28', '2026-03-28 13:07:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:06:17'),
(474, '81affc3dfe5f04bc0a2f06755e42c0919336fada05b9f109f2683e3b15e7c93a', 3, '2026-03-28', '2026-03-28 13:07:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:06:47'),
(475, '3d5bad96ea12952347e92f4fd9b7d917758d47afc461449b6679c0bcdf2b2ec8', 3, '2026-03-28', '2026-03-28 13:08:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:07:17'),
(476, 'b9c018528cb895a7b925ba771ce1f712585afed082eb8303520378555402b4ec', 3, '2026-03-28', '2026-03-28 13:08:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:07:47'),
(477, 'a8b1e32a1542357d72431a9cd4d2cf0a949f4a754a4e4ff9e9c56e68f9a0df7d', 3, '2026-03-28', '2026-03-28 13:09:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:08:17'),
(478, 'f81a5ca48bf6a48c3b5146877e93de5f75d504d9bd28901967e6c9a383720235', 3, '2026-03-28', '2026-03-28 13:09:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:08:47'),
(479, '479f8fd1c86d6ce37a4c14f7f073713be4d43ec2ecf639c1a492b967ff3ff357', 3, '2026-03-28', '2026-03-28 13:10:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:09:17'),
(480, '957a6bd89630cbb46b57ca2d20e73f1ced26ad2ab40d5538d2f9b7b1718d76f4', 3, '2026-03-28', '2026-03-28 13:10:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:09:47'),
(481, '673360e688576991f7a0c2d590b7177f2df0853a23d3e8bacf03b228594a055a', 3, '2026-03-28', '2026-03-28 13:11:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:10:17'),
(482, '988859343bba2e67e47680ced89adbf0829f79bf5ecc12a2c16cf52e933d9c5b', 3, '2026-03-28', '2026-03-28 13:11:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:10:48'),
(483, '84a1e5941ed8aefe4e32e3c6b9826e7b879c763ebbbbfc02d7a79d4d94221e5d', 3, '2026-03-28', '2026-03-28 13:12:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:11:18'),
(484, 'dde400e1b1d4b27bed5c4cc7d89910eb746078828686bdc5f21a48ae995b2c2a', 3, '2026-03-28', '2026-03-28 13:12:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:11:48'),
(485, '40ae32ea3abd72db35b64bf689749e65e1d9e0a42aec077c6382dcb3e954fedd', 3, '2026-03-28', '2026-03-28 13:13:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:12:18'),
(486, '04c814d9cf1c5979d5361aa59a02a17ce8f04a5ea3eac84b56525e4beed8a9f4', 3, '2026-03-28', '2026-03-28 13:13:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:12:48'),
(487, '5917e7f2a8cf63b632150ab5948de247ef9ab9ebcbc2698ff4570aa397e61ab3', 3, '2026-03-28', '2026-03-28 13:14:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:13:18'),
(488, '34d80b3f0435b342a1de10bd6fe61d3362da1fe9279ee0adb7a3e41cb4e303c1', 3, '2026-03-28', '2026-03-28 13:14:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:13:48'),
(489, 'd3209f5115a50350f993743466aef4eb040bec338699476dab88404401f5206c', 3, '2026-03-28', '2026-03-28 13:15:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:14:18'),
(490, 'a52a393f01554263087e9e1b8a2027be4fdcf36cc28aaa1aad25cced391515f8', 3, '2026-03-28', '2026-03-28 13:15:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:14:48'),
(491, '5d37904ade2914526ec9371b0cad4a997a3a060a94fdce4c3f40363da175b995', 3, '2026-03-28', '2026-03-28 13:16:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:15:18'),
(492, 'da188cf8ab6ef168c2ac8333d728ae47eee7f4517abea81129b3245b64335a48', 3, '2026-03-28', '2026-03-28 13:16:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:15:49'),
(493, '44ad706b10a8fb1d114f3944cb73d1c01118e9b2201cbb00099ffd86a582d2fc', 3, '2026-03-28', '2026-03-28 13:17:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:16:19'),
(494, '92611dc3b2109d884de78f5905aa7c790070b7785fb3ae342b69b1e8a314ba79', 3, '2026-03-28', '2026-03-28 13:17:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:16:49'),
(495, '0e0bd4bc841462af9868a7d52d7b4ea69171ea3f7513f062fbe8705647bf37d2', 3, '2026-03-28', '2026-03-28 13:18:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:17:19'),
(496, 'b6289e8ec2c37dfa116dcbc534f96507da961b89e97acf38cb9912b096d0c770', 3, '2026-03-28', '2026-03-28 13:18:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:17:49'),
(497, '8024e6a1ee4ff41502b525f4633d6308533382cd2ea59cf09db2e27953cea45c', 3, '2026-03-28', '2026-03-28 13:19:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:18:19'),
(498, 'e947c78c3a830ca9e5400dff83db1312739bf34843488558a0eae6c51ef49a8b', 3, '2026-03-28', '2026-03-28 13:19:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:18:49'),
(499, '15ec5c39fbde4ac7ad17e4fe76bd929608d2cffe7cf9b50fe3902e09c89fb89d', 3, '2026-03-28', '2026-03-28 13:20:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:19:19'),
(500, '0c5362acf826c19640a56c58f5786234a56ec0e518b3528cfd4f192ce2c03b68', 3, '2026-03-28', '2026-03-28 13:20:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:19:49'),
(501, '183d9dc58b6291f4c11157df7d493a4e2c9089b6b5041ef3802ee8b94a2c4898', 3, '2026-03-28', '2026-03-28 13:21:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:20:19'),
(502, '71c7f3fd077fc29e9aba8f77246dd9108128c1f978fb0385df1c3b1085385193', 3, '2026-03-28', '2026-03-28 13:21:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:20:49'),
(503, 'd8d0e13ea90ad3cba8df6c0dd9b973e572a44a983c3ad8e610cf56f64190a795', 3, '2026-03-28', '2026-03-28 13:22:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:21:19'),
(504, '169f684272ecc8bdfb42ee3c619309592f65226c3642a08629eeef0ea29f55d4', 3, '2026-03-28', '2026-03-28 13:22:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:21:49'),
(505, 'a63fd7ab4e0905e0094a58f540d6e4e3521b429c56553fb8775aae471b6633de', 3, '2026-03-28', '2026-03-28 13:23:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:22:19'),
(506, '4986abb152998b2ce7cb0964e7a415cf2173eab8e3fe2adec363e9ba20e82abd', 3, '2026-03-28', '2026-03-28 13:23:50', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:22:50'),
(507, '9d5651c7ac292eb11d93cfdf9c90f984809e614732d89fad36ef100b7edd0ce5', 3, '2026-03-28', '2026-03-28 13:24:20', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:23:20'),
(508, '9faad817b9b74925674a6847ac667e131e5bbddaa8fcfb625be45e12fbae95a4', 3, '2026-03-28', '2026-03-28 13:24:50', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:23:50'),
(509, '4dac6446812b5dc0c578e840da0b9da27caa3c1b990cacb179197e276b2884da', 3, '2026-03-28', '2026-03-28 13:25:21', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:24:21'),
(510, '9f7a1651a1831efe74f0a34b026342bd13d159b077bd626f91c66da3000527b3', 3, '2026-03-28', '2026-03-28 13:25:51', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:24:51'),
(511, '0e36711353fc80517e5e29474108da10efcf4edbf90daccaf7caa3a0c9a554ee', 3, '2026-03-28', '2026-03-28 13:26:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:25:23'),
(512, '95299254ffb04bf6bdcbe6ec7b21c21fa559643c232c7dc43bf25a52dfef5b5e', 3, '2026-03-28', '2026-03-28 13:27:06', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:26:06'),
(513, '61f0b0babdd29a30c55e6f7234aea5ba1f23413630626279a0c1ac7908fd6f72', 3, '2026-03-28', '2026-03-28 13:27:38', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:26:38'),
(514, '5813e7259059c8693a2d0152acb69c009cfa75865572081ab72c171181e01020', 3, '2026-03-28', '2026-03-28 13:28:09', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:27:09'),
(515, 'fc75d9d2932f8935ec51eb5552cfd507df7f19b3e8151dbcbcde15d8fb0c3cd8', 3, '2026-03-28', '2026-03-28 13:28:46', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:27:46'),
(516, '72aadbf9319bc01104a793f4d53ca31fb8b7ad99fc98659b3cbf81016b8fec3d', 3, '2026-03-28', '2026-03-28 13:29:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:28:45'),
(517, '6be46ade7391df91c924718bc757c31c5427a5736467aee483c0be2bf7fb11a4', 3, '2026-03-28', '2026-03-28 13:30:22', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:29:22'),
(518, '775c53dff0444e80d6ac5fe7407f3203913e3672f3986b83d04bf9a54c8264ea', 3, '2026-03-28', '2026-03-28 13:30:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:29:53'),
(519, 'a591dacb384fd11baa3475cd90e2fefa409cbc7a436f5b591b3a599e848bc9c6', 3, '2026-03-28', '2026-03-28 13:31:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:30:23'),
(520, '6174422f8a70205931ca2fde535c639ec0d7be4cce753de9b3307bd6a7ddd5b5', 3, '2026-03-28', '2026-03-28 13:31:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:30:53'),
(521, '6d0b7b97bb9fb67882c728eddf1f610f90324b620846041fb6022f9ae9848e7f', 3, '2026-03-28', '2026-03-28 13:32:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:31:23'),
(522, '32c59c253b8e0d17c4bab56abbc4dcf3e9432757164e91a360ae29e99cf74ecf', 3, '2026-03-28', '2026-03-28 13:32:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:31:53'),
(523, '8c583bf632d932cc9483de6c516df38f4d20a49a22f8293d15e7f0ed60d5fb92', 3, '2026-03-28', '2026-03-28 13:33:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:32:23'),
(524, 'f7031b3e6f36b8d0a15188ae222c8144e3a3ced39da4344eac435200b2f22fd7', 3, '2026-03-28', '2026-03-28 13:33:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:32:53'),
(525, '1f991ed2b7fe57a44568f8c300824c6cb8c8b8224e560f3fbbb9ff452192723a', 3, '2026-03-28', '2026-03-28 13:34:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:33:23'),
(526, 'cb9c6ee039422f2daf1d7931287173b4053464ef9db099a2099d58763304550a', 3, '2026-03-28', '2026-03-28 13:34:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:33:53'),
(527, 'fceea5c7800c6d70cbee2a77bb32d09684fc261246649aef077bff3943132dcc', 3, '2026-03-28', '2026-03-28 13:35:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:34:23'),
(528, '9c351afaefd12379a34d786ef49a0c10a065241a4502b5129052a341476a9641', 3, '2026-03-28', '2026-03-28 13:35:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:34:53'),
(529, '1b47a7d5a708c32b8012f8e04c20e692e5e791da0038a014192fdaeb02c63bd9', 3, '2026-03-28', '2026-03-28 13:36:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:35:23'),
(530, 'ae0fae14863a3d05ecacef7eba93f343ab1fa180da657f54197f449c39306873', 3, '2026-03-28', '2026-03-28 13:36:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:35:53'),
(531, '9bfeca5fef5fcb106548343a872f5aa8199eadead05448f323ba895a5460b431', 3, '2026-03-28', '2026-03-28 13:37:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:36:24'),
(532, '4e1cdd1cfae9e4700a73cc964b476cab8cf5f8d0e8d58cc5ab7b7298e8a1fff9', 3, '2026-03-28', '2026-03-28 13:37:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:36:54'),
(533, 'eea818ac3f203dbd4d95341960e4be102aeade978b2d599a07cb49ec8c7b229a', 3, '2026-03-28', '2026-03-28 13:38:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:37:24'),
(534, '44d3ee5f0f566c6fc54510b9789566a4d3d741757d26157e3284733fd945fef7', 3, '2026-03-28', '2026-03-28 13:38:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:37:54'),
(535, 'dafb8b22a19d0a9e24aa8d0dcaa8e991caf921271583cb6c1e7dfd97450caedb', 3, '2026-03-28', '2026-03-28 13:39:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:38:24'),
(536, '21302259fcabc03d09c1350622b75b2bc3fe9ab03cdcf9c157c494e788a8a0e3', 3, '2026-03-28', '2026-03-28 13:39:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:38:54'),
(537, '5774836a66ed25637d1d9cd3b946455e2acae19b2e55856cee8f92d2e1f14762', 3, '2026-03-28', '2026-03-28 13:40:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:39:24'),
(538, 'f58aadadff76640cf8e2dd4052464e368b9787af8b48333b662b066e16e1e14f', 3, '2026-03-28', '2026-03-28 13:40:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:39:54'),
(539, '3989a1df25bc3530b2427d969bd60f6e1ed5bd9d4f73764be492ab5f7671fe8c', 3, '2026-03-28', '2026-03-28 13:41:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:40:24'),
(540, '47cf121cf2e3d693a8c3c843755db5bb64227afb8fb091c8afb89c638baa6df0', 3, '2026-03-28', '2026-03-28 13:41:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:40:54'),
(541, '084851f6ec422d985f9e794ce1a14f432f37ef05d4f2c42af591f48be0b2360c', 3, '2026-03-28', '2026-03-28 13:42:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:41:24'),
(542, 'c63367b1667e7eaa3a0aab0292f8ef6569d10664441cf94a3dee76ef1b405d92', 3, '2026-03-28', '2026-03-28 13:42:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:41:54'),
(543, '58540281fa20851c4e4f800b06c98c6d46f03e122809605ced800ceba92f3665', 3, '2026-03-28', '2026-03-28 13:43:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:42:24'),
(544, '2dc520c9a434811378d9457b0504eb690d9b3965e848f5b80a2023dd54393506', 3, '2026-03-28', '2026-03-28 13:43:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:42:55'),
(545, 'dfb93f3da92917b9af6ae9cc079db9a58fef2f5426b05b96c0e42f083113e5d1', 3, '2026-03-28', '2026-03-28 13:44:25', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:43:25'),
(546, 'db6780c91cabacd59ddfd491f1c138e7bb6298223bea26977877aa260cc77f1a', 3, '2026-03-28', '2026-03-28 13:44:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:43:55'),
(547, 'bf62176e6cb9409ec165607b2e32604b17be1d28259bc53cb7e29f0d4edabcda', 3, '2026-03-28', '2026-03-28 13:45:25', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:44:25'),
(548, '435359cb4401b32fbf7f7d1e12a9344d647a9ac7d9b8b0f63684ee0c67cc455d', 3, '2026-03-28', '2026-03-28 13:45:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:44:55'),
(549, '10070fa856cf5bfc2daecd5fec762b0c8ca23090dd5e8a3bc74592547191e4bb', 3, '2026-03-28', '2026-03-28 13:46:25', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:45:25'),
(550, '2f705435752173f3c5d8753b3e9855fe31bae035fc1bf16df4994b48f1ade90e', 3, '2026-03-28', '2026-03-28 13:46:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:45:55'),
(551, '69d79b6f332143e506abaa3ddc52be257aaa5751bec7817fb033fea9447762aa', 3, '2026-03-28', '2026-03-28 13:47:25', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:46:25'),
(552, '81864e462725dc1d1e10c18360df19eac7a10fe3b20dd6d3dfa2347661ecf861', 3, '2026-03-28', '2026-03-28 13:47:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:46:55'),
(553, '621a93ed3ed3c9253868d163dd4399ae9616f99895ec4cfa875fc26949d6bcfc', 3, '2026-03-28', '2026-03-28 13:48:25', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:47:25'),
(554, '7a3bc057167821159e3026bde7e81cebd6e9e96bcae8f76113744574ca2bf29f', 3, '2026-03-28', '2026-03-28 13:48:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:47:55'),
(555, 'af2f3f6a7e4ca6ca947e31e6605ce7cc5628640d58b1d9be33d41d3ce8308123', 3, '2026-03-28', '2026-03-28 13:49:25', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:48:25'),
(556, 'dda16d80d0235f8e402d081ab0266d701d139ca52a29eb434cd602e96be1f1e7', 3, '2026-03-28', '2026-03-28 13:49:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:48:55'),
(557, '51bd14d63a3dfbb247dfb801d743fc8638a4d8b007f6d59f9a1ebab1a0de0115', 3, '2026-03-28', '2026-03-28 13:50:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:49:26'),
(558, '45f43111661efac3fbf201e84100a67c13cbfb9ca3cb1eb701efbd4dbe179acd', 3, '2026-03-28', '2026-03-28 13:50:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:49:56'),
(559, 'c30a97bdd9d9e0014b9993707ab3b70a0a2a98399ca6578df7feaebb408de308', 3, '2026-03-28', '2026-03-28 13:51:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:50:26'),
(560, '2d1c6767ae297a779a32abd4b65aabfca865a88649337e8b3a3b4c199afe55ed', 3, '2026-03-28', '2026-03-28 13:51:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:50:56'),
(561, '0fc2088754e6eda5f50c158df66848549bc4a84461d9edd415e53329d5b2d017', 3, '2026-03-28', '2026-03-28 13:52:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:51:26'),
(562, '2737b1cb1b6fe7031c066558790a3ca491b99942591428625857d4a78d5f24e1', 3, '2026-03-28', '2026-03-28 13:52:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:51:56'),
(563, '67872bce0bd07e0ca40ba7e55c5cdfa005bcc49a0da83e044361533a411aa10e', 3, '2026-03-28', '2026-03-28 13:53:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:52:26'),
(564, 'ed7842175f14dc853f82e1e0d98494ad052341d9bfc83e5aca950f7005b46f04', 3, '2026-03-28', '2026-03-28 13:53:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:52:56'),
(565, '4ec067c6d353ba9699c145a72756d31961d21e9ac2b53b6dcf2f238868fdfafd', 3, '2026-03-28', '2026-03-28 13:54:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:53:26'),
(566, 'caee1dad43d8856cf112cdf958ef8751859e481675cb57b88f2fdbc5821ca00d', 3, '2026-03-28', '2026-03-28 13:54:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:53:56'),
(567, 'edaee5076e82ab7131dbb8bbfc54f34a33fb43146ee7c43cfe68c59626fc27b2', 3, '2026-03-28', '2026-03-28 13:55:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:54:26'),
(568, '30912584e7af553efe25831de4b4e643499e87930f89317c50cff3f99cb0f2e0', 3, '2026-03-28', '2026-03-28 13:55:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:54:56'),
(569, 'b7c9f085dabcd17ff9f74500195bfed61a86064e4b8adcb2a31cc94805c3cd47', 3, '2026-03-28', '2026-03-28 13:56:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:55:26'),
(570, 'f8176b80ef8200a1360455f0ff80cf058b03a8f70ecf6bfc3968f91ce8beb52d', 3, '2026-03-28', '2026-03-28 13:56:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:55:57'),
(571, '955abc4c73c3eeb1769f00da092998dca6fcbf02027ab0b04a4a8547a3f02ca7', 3, '2026-03-28', '2026-03-28 13:57:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:56:27'),
(572, '09fdfde9686bc6b66e6241a93b56318bd5066b9a1da671ebb26580e11a8c2c35', 3, '2026-03-28', '2026-03-28 13:57:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:56:57'),
(573, 'c14f8ff702f3c8f3cf8a94d2adbe74004b1d208fa6aafa4e4e51a08c5315546b', 3, '2026-03-28', '2026-03-28 13:58:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:57:27'),
(574, '5a55bac7b84a9c74cc9b39998a8a3ef5231be8664e920cdeb4530053907dbe36', 3, '2026-03-28', '2026-03-28 13:58:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:57:57'),
(575, 'd1a99336e8eaae68cdba9fc0b3d18454f1f1973afb55c06074169a960e8fe129', 3, '2026-03-28', '2026-03-28 13:59:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:58:27'),
(576, 'a829bf959cb3f3b4b146f61290ad416a1b1acea0c766087325e38de6e9e2a9ad', 3, '2026-03-28', '2026-03-28 13:59:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:58:57'),
(577, 'db9755650b1297920effde0d734ba3158d963eba454528a007b58524cb4892ab', 3, '2026-03-28', '2026-03-28 14:00:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:59:27'),
(578, '9affc68690d7d525f4ebe94cfd6676f8e5c76f17062b7477f3837052504def46', 3, '2026-03-28', '2026-03-28 14:00:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 05:59:57'),
(579, '7786f35905dfe27803605ca32bec01642d5f1e852f5da2ee246e442980cd8bd9', 3, '2026-03-28', '2026-03-28 14:01:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:00:27'),
(580, '89fc31f6cf5966789ca07ff2eeea2e001fe690abda3a39f6f0575a2a47ce6d9d', 3, '2026-03-28', '2026-03-28 14:01:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:00:57'),
(581, '59335ae4addcc6bb6a64d7920cf52a5b01148ad4f235b1279b98a4ebb3a111dc', 3, '2026-03-28', '2026-03-28 14:02:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:01:27'),
(582, 'bf0c13cf49b4f8a58c48a012b3035d557857b53d63f82bd474beedae8c44ac0e', 3, '2026-03-28', '2026-03-28 14:02:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:01:57'),
(583, '596f78e3b1254a2435940278fd497afe1ee8e7e66862ce9bef80c07bac988964', 3, '2026-03-28', '2026-03-28 14:03:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:02:28'),
(584, '2acf7158405c08a90339695bbe98cb415a7461f452d5a9bd96454765700e8fda', 3, '2026-03-28', '2026-03-28 14:03:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:02:58'),
(585, 'cdcc646240e9e010fd5eab85650ba9789584e103b35b4ce8b99eb60a637dadba', 3, '2026-03-28', '2026-03-28 14:04:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:03:28'),
(586, 'd1611a64827df0275adc584694772921829db5ba5eb29487f1b822c37571e4e7', 3, '2026-03-28', '2026-03-28 14:04:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:03:58'),
(587, '8c4d85e565cff12b2ed861228f23ad58b880910cd5902119fc19877e65dd0733', 3, '2026-03-28', '2026-03-28 14:05:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:04:28'),
(588, '159c1fbbdcf39382c66627aced37765e2292b6e9d936ed177b12e4a1324ff126', 3, '2026-03-28', '2026-03-28 14:05:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:04:58'),
(589, 'd37666fbc8698e9e1578cd1a120f6e621fe45c95c7106f7eb71aa994486ed436', 3, '2026-03-28', '2026-03-28 14:06:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:05:28'),
(590, '1e173e47c68e4f7399e671839d1f364ad4be29e46eedb11c16deb1de628d0554', 3, '2026-03-28', '2026-03-28 14:06:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:05:58'),
(591, 'd6106c45d3990c26662019492945745df7c984aaed0767875db2d29515a785aa', 3, '2026-03-28', '2026-03-28 14:07:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:06:28'),
(592, 'bec24a7be85fb5cad12f2fb81acee557efa607a6742152a30807330f5d2dc64a', 3, '2026-03-28', '2026-03-28 14:07:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:06:58'),
(593, '1217e43e6fb4cf2d1a6511c29d9e3e9bc03ea4de15388da5bc64037cd76fcf71', 3, '2026-03-28', '2026-03-28 14:08:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:07:28'),
(594, '5e62aaa4cb20e50cade46afc2bcdc2e9b543f6d3e8c963dd08bf4975a8a13cf0', 3, '2026-03-28', '2026-03-28 14:08:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:07:58'),
(595, '4516dcf9be95fd8756cedb11d4ef5da12c127a0c7300140e19d1fafce3766063', 3, '2026-03-28', '2026-03-28 14:09:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:08:28');
INSERT INTO `ta_attendance_tokens` (`token_id`, `token`, `generated_by`, `generated_for_date`, `expires_at`, `used`, `used_by`, `used_at`, `ip_address`, `created_at`) VALUES
(596, 'df14533b19257d8b0899446077766fc1e47dd1af02b7a54b3d8b5a755e589584', 3, '2026-03-28', '2026-03-28 14:09:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:08:58'),
(597, '70ccaa9ff33acc4135b5b1dd17eb11eabcb3deb1d1933b1a2b6d3547e56fe53c', 3, '2026-03-28', '2026-03-28 14:10:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:09:28'),
(598, 'b9a4866023b5f3570ac1ee58b33f3b469d89c36979727ca4977d7045a68bb5e9', 3, '2026-03-28', '2026-03-28 14:10:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:09:59'),
(599, '63994069c1ad66e0b36040819f75d9973d154dec280090a535835fef4552ba3f', 3, '2026-03-28', '2026-03-28 14:11:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:10:29'),
(600, '7ed7ce3f3d660bc3f2b6cb084ad8603a7c8ef0160e8e539128e73b797ddd6979', 3, '2026-03-28', '2026-03-28 14:11:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:10:59'),
(601, 'a36c0e7f35bd9161c743f95c912d82f08349dd7f89e6a3486d5b9ca03e6d08bd', 3, '2026-03-28', '2026-03-28 14:12:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:11:29'),
(602, '98ebf5c161ac3126f7da3e8e91318b83b1909880d4d88d8e7a101ae2db07da68', 3, '2026-03-28', '2026-03-28 14:12:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:11:59'),
(603, '3fa62b16f66a896b6f2548319637f97b2b9b2112e814f4d39cfed1ab6e8de30f', 3, '2026-03-28', '2026-03-28 14:13:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:12:29'),
(604, '2cba03165b3864d743e6eb6ec91f9d297df3138a6d2b30e8c9cd9250af58ec4b', 3, '2026-03-28', '2026-03-28 14:13:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:12:59'),
(605, 'c2cb3228d6420bbe3f64bf6c9e98300b14d032d89ce987c987e9084ab1ad28cc', 3, '2026-03-28', '2026-03-28 14:14:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:13:29'),
(606, 'bb33bb568d8c71a5fe0a12b2e7c6ca30eee8a8acd3fcfb1dc3b52d699ec0ba6e', 3, '2026-03-28', '2026-03-28 14:14:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:13:59'),
(607, '546cb8135fac77ee449342ceb510879b4d5c5ce63aa12e5e5b412d6f82d58c23', 3, '2026-03-28', '2026-03-28 14:15:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:14:29'),
(608, '91070290af2f203d006a09d265237f9fdb7a13f2e6a1e0c91b7b3b350c20a379', 3, '2026-03-28', '2026-03-28 14:15:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:14:59'),
(609, 'a838d05d088f8aeb22fc6a2dc039e5f35c60157b49f9bf2fb18068abbc6f5b80', 3, '2026-03-28', '2026-03-28 14:16:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:15:29'),
(610, 'e0e215c538ddcb878dc22c357490bc8651dd0a061ea9525ec30c8339e18b4fbc', 3, '2026-03-28', '2026-03-28 14:16:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:15:59'),
(611, 'd24195c3c74be879b2f4c4022caec14110353ab2dba9e87a9e6e157d3b798f68', 3, '2026-03-28', '2026-03-28 14:17:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:16:29'),
(612, '5201bdcf42d1d5121abbd1e616a6e7028a47952825d6d7071b9a1927b1349eeb', 3, '2026-03-28', '2026-03-28 14:17:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:16:59'),
(613, '3ff638979082d6baf3d5c68c23277e904ffb7cf6641c8f868064c5861d8f3ddd', 3, '2026-03-28', '2026-03-28 14:18:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:17:29'),
(614, '4019c7effbf8fd14c8f69a35e70ecdf2c2e381f024a2c684488d1ba5d2361970', 3, '2026-03-28', '2026-03-28 14:19:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:18:00'),
(615, '1436a800cd8215b6e82c738be872d255889377f2dac55082be0d8160fa763513', 3, '2026-03-28', '2026-03-28 14:19:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:18:30'),
(616, '95016e6407509a0412716cd0841378ede81f2668498a3baf9b5c44c062e1c8d7', 3, '2026-03-28', '2026-03-28 14:20:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:19:00'),
(617, 'ec544844b4d93cf36a855fd5124c97717ce24962e42639187efcc9156b423d71', 3, '2026-03-28', '2026-03-28 14:20:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:19:30'),
(618, 'ddc913a6ca953fbf191654e6754186aff272eb39fa70fdbd291b07ea5430fe29', 3, '2026-03-28', '2026-03-28 14:21:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:20:00'),
(619, '51505385a7b9e49cca5593638d854557593f2f3af6219d4c6602d03e0adeffd6', 3, '2026-03-28', '2026-03-28 14:21:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:20:30'),
(620, '98db8e071317bc4ab9d572147753377d85927d1c409f3c754bb9b341c8384da3', 3, '2026-03-28', '2026-03-28 14:22:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:21:00'),
(621, 'bce0c198853b64872602ae4f671816f9713d39c50b2b48a1d6e81b6ef62d750a', 3, '2026-03-28', '2026-03-28 14:22:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:21:30'),
(622, '705217c1aac1563a95e5aab86bd93cfe646ff76112ac3088a930987030604505', 3, '2026-03-28', '2026-03-28 14:23:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:22:00'),
(623, '6c140db1a7264c05b01695dcf7807a7a5654c9bf26d57f71fb9d06e4353dff68', 3, '2026-03-28', '2026-03-28 14:23:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:22:30'),
(624, 'ca7757f4ffd0dfc5cf4bfe05e4ee898d5ad41b255f887e40de70e5c78cdbcb15', 3, '2026-03-28', '2026-03-28 14:24:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:23:00'),
(625, 'b5328c782d71b397cdace8171117b8c1dd9b19a1735ae2de8e746057de7e546c', 3, '2026-03-28', '2026-03-28 14:24:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:23:30'),
(626, '36412bef5ba4ecc1717e90cdfabf9bd937872614bc7e2b3814ba51bcb26b4e47', 3, '2026-03-28', '2026-03-28 14:25:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:24:00'),
(627, '5d3c433be0a8c1ceb70b4bede92c8e4ab6c08563bc7c3139813bfe257e40389c', 3, '2026-03-28', '2026-03-28 14:25:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:24:30'),
(628, '1304a837387cb09762302ef212a7fd18a5c43096bec0704b8bb1b9c51060ca07', 3, '2026-03-28', '2026-03-28 14:26:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:25:00'),
(629, '13e8f82c08c7ba942f2f1a60b168a42ae55aab9c40db2c3dbe3ddbb1bddee073', 3, '2026-03-28', '2026-03-28 14:26:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:25:30'),
(630, 'e1d716ab6c8cfe65d391868ebb1df2b00f6484fc8cbc4ce5a7651feb29f909af', 3, '2026-03-28', '2026-03-28 14:27:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:26:00'),
(631, '7be818030e347c76d214d7ebeaeb0507a43ce04c6cf68e7e007678fae944126b', 3, '2026-03-28', '2026-03-28 14:27:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:26:30'),
(632, 'c53ff0f48edca91a17a12c2bafde15c1965c5c9f8381efdcf75829a01ba2262d', 3, '2026-03-28', '2026-03-28 14:28:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:27:01'),
(633, '55032af8365b17b34c81c19355c5c2431478d9743f6f17dd78de0897134664c0', 3, '2026-03-28', '2026-03-28 14:28:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:27:31'),
(634, '078e70285d89e0cdb637e0c3d24ab66416891598c643c7bce30704a07bd09c18', 3, '2026-03-28', '2026-03-28 14:29:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:28:01'),
(635, '7f2a01f5497fac485cce1eebd65b0c8f4a76ab47ad8a6d33b6d3321e5e49f37e', 3, '2026-03-28', '2026-03-28 14:29:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:28:31'),
(636, '721f19728ebf8052758d1b2f53f8259bfc9b65ff0db4d5882f2b6ba9b7f0c666', 3, '2026-03-28', '2026-03-28 14:30:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:29:01'),
(637, '524466f94f843615b78e4907746598dbc562a0db4e2bad9db6f9cf6b106dfe4b', 3, '2026-03-28', '2026-03-28 14:30:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:29:31'),
(638, 'ce6f83ff1961eca3b159b95978027f4fd2f633f2b0f0f3ec4caf5eb699480d29', 3, '2026-03-28', '2026-03-28 14:31:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:30:01'),
(639, 'd9bb768654e984b83494fda29836247d2fa187512ae629197278acd6df61c744', 3, '2026-03-28', '2026-03-28 14:31:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:30:31'),
(640, '76331e47763ce3a36eb2919599a01061a5df4856c45b1996f78f2d0e4d988194', 3, '2026-03-28', '2026-03-28 14:32:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:31:01'),
(641, '87a95ad798cd4010c026160759d768218e85389cc7184b7eb073569e8f648fdb', 3, '2026-03-28', '2026-03-28 14:32:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:31:31'),
(642, '05c897ed6d15f48f955c5679f981df145c59f26d0f86220b2d595d7da6776279', 3, '2026-03-28', '2026-03-28 14:33:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 06:32:01'),
(643, 'c8231b033adf672bbcbba8a32ae706f39a501a22f60c75a1c32b14a6f4be4aef', 3, '2026-03-28', '2026-03-28 17:14:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:13:54'),
(644, 'd233d54b06ec29401b30305e5a3f03035c113ff796230e874e800a772f409923', 3, '2026-03-28', '2026-03-28 17:15:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:14:24'),
(645, 'd5b5e21c429f33fd5a5ce7fd7b57c7332186d59f38b98f2c075889302d25cd60', 3, '2026-03-28', '2026-03-28 17:15:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:14:55'),
(646, 'bcae46fe1df61012b2d98f9a90f1618a39b89673cb53311817b138135eb6503c', 3, '2026-03-28', '2026-03-28 17:16:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:15:14'),
(647, '1577105efba7b194326a30e3dc1f26dda22cf69a9c8689efe97fc9020f27a0fe', 3, '2026-03-28', '2026-03-28 17:16:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:15:45'),
(648, '709d431ce1a122524a2f2c22fd14144e18a7d50d8103cf83908da335fd148e8c', 3, '2026-03-28', '2026-03-28 17:17:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:16:15'),
(649, '06fd2dbebae3c0b096ec979cae2743b222486128773fe95fbb08a9b3a1af7bc2', 3, '2026-03-28', '2026-03-28 17:17:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:16:54'),
(650, '88f85e643e7ce5144698b1af55f3d721b10ba9300b55296c50e4e94e10729dcc', 3, '2026-03-28', '2026-03-28 17:18:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:17:27'),
(651, 'fd4bc859ea8d3ef09e16de6e82954121c7bd9248aef8aac279620f3c6e8b0a33', 3, '2026-03-28', '2026-03-28 17:18:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:17:59'),
(652, 'a7b9eb76204c8da66cd7667224fd3104cbbe852016607d94803059641cc15c67', 3, '2026-03-28', '2026-03-28 17:19:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:18:30'),
(653, 'b8b108f22b17ca323a4e241623a26b574977b57a03c99e488057db0fc0a88e9c', 3, '2026-03-28', '2026-03-28 17:20:21', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:19:21'),
(654, 'ee09d5a54e3b2e2ba4126a83207633555599ad72e83a137065958da5c831c879', 3, '2026-03-28', '2026-03-28 17:21:11', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:20:11'),
(655, '2088786fdbcfdd86d9cd63c64536631a6e68ed6beac4f6807fe7e3ad0a1ce641', 3, '2026-03-28', '2026-03-28 17:21:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:20:59'),
(656, '9dd91e00a219d5869560ca9f0dd80eaf7a53280883cfaa483f31af4be7ba8923', 3, '2026-03-28', '2026-03-28 17:22:38', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:21:38'),
(657, 'a73b68ddadf21b1dc8b42c080a8bc264f49600d3afaa64c4e1955bb2349c852b', 3, '2026-03-28', '2026-03-28 17:23:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:22:10'),
(658, '8643854eb2b97497c1faec543d37cf7bd9a840324a7695f28a0b152cf3ec463d', 3, '2026-03-28', '2026-03-28 17:23:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:22:40'),
(659, '52cfaf0d83a849083bd94a43c540fd0fccf4da1f9a6c96621158362decf15e82', 3, '2026-03-28', '2026-03-28 17:24:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:23:10'),
(660, 'db62930af414cd44727e56255fd516b250306d3777dd2559453d892657bb5b19', 3, '2026-03-28', '2026-03-28 17:24:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:23:43'),
(661, '71fc25172743386616cb54acaa403b04f42a5772f4766ad0618487d8846aa111', 3, '2026-03-28', '2026-03-28 17:25:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:24:34'),
(662, '48ceb02ac977bcba5585baa2d719e1be5588f3e8a5bf6c8c0ff8f80d88bae16c', 3, '2026-03-28', '2026-03-28 17:26:05', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:25:05'),
(663, '13ec63dfed16c9cd0ba818cf0584c1d4bdc0c23527e7a6a7ee674f3698ec7381', 3, '2026-03-28', '2026-03-28 17:26:35', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:25:35'),
(664, '33e111bbb44dc8ba8dcf13ac1d57eb4f36541bf359990f422eab41b33749ba4d', 3, '2026-03-28', '2026-03-28 17:27:05', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:26:05'),
(665, 'fb4ef73ad79409e24d058801dc539f933dd13eb06262332d6e3d0282b0530f6a', 3, '2026-03-28', '2026-03-28 17:27:35', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:26:35'),
(666, 'ee584b2d0a4fa58348ed2e9fb58728445e30eac8b93c9bfdc85d990e7a47d4de', 3, '2026-03-28', '2026-03-28 17:28:06', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:27:06'),
(667, 'db68c799baa367919c17df16f15d6611c8211ccd62fe0022519ecabcb4ba9e18', 3, '2026-03-28', '2026-03-28 17:28:39', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:27:39'),
(668, '3a706126cd4defa402099ce2366d22f2ebe010595d852a4bacfe2921365f52cf', 3, '2026-03-28', '2026-03-28 17:29:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:28:31'),
(669, '3c80d45111badd3da93ff726ab5f4b135c04b098e5e41b5f5a28a2744fa459c2', 3, '2026-03-28', '2026-03-28 17:30:02', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:29:02'),
(670, '09257ecb0735b52a174adbc5675aa69199e05f0161902f49995049f812540315', 3, '2026-03-28', '2026-03-28 17:30:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:29:58'),
(671, '28ed0e9fa2358d05532ca3a0e17a38d917e6843c9cc06193ceb9f0a2005fc053', 3, '2026-03-28', '2026-03-28 17:31:35', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:30:35'),
(672, '4dc28df029d90cfb3f630aeb6264ddadb0f43a6884cc27f4a76618f053ffb613', 3, '2026-03-28', '2026-03-28 17:32:06', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:31:06'),
(673, '10f2c83908c1fa989f82f7c85173139a765896585b06c9914928fe6c2f73f2f2', 3, '2026-03-28', '2026-03-28 17:32:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:31:40'),
(674, '8ff41acd8f02723eea8e8a0ac0432e72b21692eb344bb9de59b09f86799b9ed0', 3, '2026-03-28', '2026-03-28 17:33:11', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:32:11'),
(675, 'a337996a393bf2c75e84d0fd1e3afa9efb65b3db27c5863afb51f1e5e65d52de', 3, '2026-03-28', '2026-03-28 17:34:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:33:04'),
(676, '268c95a78c68e80ffdec8f7f39f5aa28d5cf8889e702196e8f64da803c1df63a', 3, '2026-03-28', '2026-03-28 17:34:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:33:34'),
(677, 'aa03d268155e412d5943b655a828c7b873da20ecf1adb7050f51c2d80dcc0024', 3, '2026-03-28', '2026-03-28 17:35:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:34:18'),
(678, '6e8acdd63b5c7b71aba83126827bac573930c7686cf5e5c0e8b301cb8cb32624', 3, '2026-03-28', '2026-03-28 17:35:51', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:34:51'),
(679, '67b25eaa7a5fbf1f6f13a0d9ed0c48eca699c8037f7b3fe784dfa092cf330e84', 3, '2026-03-28', '2026-03-28 17:36:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:35:42'),
(680, 'c60aeabc101583a77eaaba7a8ba27e6a8650e8272ee38c891ff39bb46077fd67', 3, '2026-03-28', '2026-03-28 17:37:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:36:14'),
(681, 'a911502ee4f1c78ba5bd73e3a8b38f6e06b0bd7922708987044367197dcb7db7', 3, '2026-03-28', '2026-03-28 17:37:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:36:45'),
(682, '42c1d881de6ccb1e8bc11ef174e43eaf073f0165c375e8e2167e314216d10504', 3, '2026-03-28', '2026-03-28 17:38:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:37:19'),
(683, 'a814298aa8948d2bceeb2faec1184576206169ea88c91d980869f8237d929f67', 3, '2026-03-28', '2026-03-28 17:38:50', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:37:50'),
(684, 'ec3beb6759f8d0fee0a3e878b03e9f06d994afd70d0bdce5f13298150eaf24bc', 3, '2026-03-28', '2026-03-28 17:40:26', 0, NULL, NULL, '::1', '2026-03-28 09:39:26'),
(685, '1c4d1fa1d48d31b16f542a242e1149d729f63722aa511e235782b74e431ddc38', 3, '2026-03-28', '2026-03-28 17:40:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:39:45'),
(686, 'e002e95c38b4d7940f1d9f58c0c83d001271574d17627eae1e1f6b27c07ef660', 3, '2026-03-28', '2026-03-28 17:41:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:40:15'),
(687, '9d1e0c46282dbabf616ad94db3de4fbc5895f4ae6fd78d6f7dde7a08a99a7d04', 3, '2026-03-28', '2026-03-28 17:41:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:40:54'),
(688, '3a69918cd8ae6be710b3edd5b4b278de9c70fdbf2511c0fd0f61a301c2b942f7', 3, '2026-03-28', '2026-03-28 17:42:25', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:41:25'),
(689, 'f2449939560711165c9fc252925c2b1412d4111c97f6e45ac700d494bc870e98', 3, '2026-03-28', '2026-03-28 17:42:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:41:56'),
(690, 'e01fbd6350e17556fe45777e936d4577098670f7bcb22a0e377b6875b1fa8cf4', 3, '2026-03-28', '2026-03-28 17:43:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:42:29'),
(691, 'e1ffb47135af8c507fcf8b012dd9f0723b2614663338892e7a4fc0f7842b21aa', 3, '2026-03-28', '2026-03-28 17:44:02', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:43:02'),
(692, '0a0f50541dd920c7207c8df1355bed6dc551fe3ae3a4db3b1cb2219d9339094d', 3, '2026-03-28', '2026-03-28 17:44:35', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:43:35'),
(693, '9cb9e3f1319b326011b419d80097bb7bbfee586b21b4a9349c273863a9e9bb6e', 3, '2026-03-28', '2026-03-28 17:45:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:44:07'),
(694, '25f6bc5f4fa46c3c722b9247e3a2fb40bf8ad483d89ccfc85b1cf1b8a2531336', 3, '2026-03-28', '2026-03-28 17:45:39', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:44:39'),
(695, 'c23be2e80a26b763d3dc05234000ff9dc4afd7990ba5b898915df2807a3281c6', 3, '2026-03-28', '2026-03-28 17:46:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:45:28'),
(696, '3236173f138309b351d1dd63a73adb7bf9d0e26f0ed87f22830a74345f6a1004', 3, '2026-03-28', '2026-03-28 17:46:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:45:58'),
(697, '7be837af9c88f98c5b1359424f1b1bf8b54e2b54a2c6c8c580f67cdd81e6cdd2', 3, '2026-03-28', '2026-03-28 17:47:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:46:29'),
(698, 'ced8c8469602d51a94cda3d390692458a9afcb1d89667caa9f7cbfe5ca3350cc', 3, '2026-03-28', '2026-03-28 17:48:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:47:00'),
(699, '5ae2e5e8f5b9515dd151a19ba08d2c2064c5e19f1abb35dda28af7a03110a68a', 3, '2026-03-28', '2026-03-28 17:48:32', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:47:32'),
(700, 'e5ed7ec50838961c361e30fc08964a8d208a86d13e719c20ad906ffac5e59151', 3, '2026-03-28', '2026-03-28 17:49:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:48:04'),
(701, 'adcdc0659a5d04177ea993b32fd7b338fec9272029a649d2fe716a3675d7e4ec', 3, '2026-03-28', '2026-03-28 17:49:37', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:48:37'),
(702, '029660e0c8144941846c9d54539cfb25ed4ee84564446a14cbab70c0aaad2494', 3, '2026-03-28', '2026-03-28 17:50:12', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:49:12'),
(703, '280d3cdb92b088c23ccee697a94d641138260c98d4eb9c805d3bd1120321890c', 3, '2026-03-28', '2026-03-28 17:50:46', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:49:46'),
(704, '96738a56b9493536405f1a82e18c3eb597e0b6a6444b08a2105c8dc5a20eedca', 3, '2026-03-28', '2026-03-28 17:51:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:50:19'),
(705, '926fd5e722258681d476d0117b6b62399fbe116ed690d264218c38013525797e', 3, '2026-03-28', '2026-03-28 17:51:50', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:50:50'),
(706, '9fee9eba48edae240d1de4cff2113bededd4a35155a1400c32dbd41250e6b4a4', 3, '2026-03-28', '2026-03-28 17:52:21', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:51:21'),
(707, '46a20aae580ee2b2aa31440754cfc37d818288db2973158177d7a3cbc0dfabef', 3, '2026-03-28', '2026-03-28 17:52:52', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:51:52'),
(708, 'eb16682699282fa0dc772ca006f8a0e2c545175a0962709f808616b6db45ceab', 3, '2026-03-28', '2026-03-28 17:53:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:52:43'),
(709, '32db6c6bf7c26c24db0758e6f6034cdd11306d8bf175092a9043e53ee40bfd29', 3, '2026-03-28', '2026-03-28 17:54:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:53:14'),
(710, 'dc803f99ca9eeddcd85cf3a641831caed59c492f549522608803a863cc022b7f', 3, '2026-03-28', '2026-03-28 17:54:52', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:53:52'),
(711, 'c0b5c1b1d51823431a09cffbfdf922227319774c134d8362e93873225c1e2cf6', 3, '2026-03-28', '2026-03-28 17:55:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:54:44'),
(712, '24fe7e5026140e1bab292562f5e927fad706efd723e4bd404d8a1b64b79e4cdd', 3, '2026-03-28', '2026-03-28 17:56:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:55:15'),
(713, '3febb19bf3b1983941c74e3b5ac1fac97fe6ef7899d9a556d10fbaec88d3422f', 3, '2026-03-28', '2026-03-28 17:57:12', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:56:12'),
(714, 'a845047efc1070c3f630c85f4d60a3667d5fa91dd42c4521678755038e4f88b0', 3, '2026-03-28', '2026-03-28 17:57:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:56:44'),
(715, '5db3cbc4798d006173caa99d01cbf8e15115be7b6c100034e52787372158b575', 3, '2026-03-28', '2026-03-28 17:58:32', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:57:32'),
(716, '712fb3a44797d902f04ecd9363ed83e23d443eacfbaea9fa062dc9e500070757', 3, '2026-03-28', '2026-03-28 17:59:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:58:04'),
(717, 'a78a1f448735f790a44b6fc5ed44ce9074c20530563df05d8ee7e59da1d8e8b8', 3, '2026-03-28', '2026-03-28 17:59:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:58:36'),
(718, 'b374932edd08688266523204c769475925f615491b06bb099c57d29e67377972', 3, '2026-03-28', '2026-03-28 18:00:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:59:07'),
(719, 'b3553404b26f8491952c8a17f1c165b84821ce07998f9f2167208e9e1cc955e7', 3, '2026-03-28', '2026-03-28 18:00:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 09:59:40'),
(720, '4f38e4f6679ca54dede9bfac930726278e9cf76ca8e79c318d484a73cfda40ce', 3, '2026-03-28', '2026-03-28 18:01:11', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:00:11'),
(721, '5556ef2485aafeb926a23c4b43d0429a7d1fee6396a03fc8911831e7511558c4', 3, '2026-03-28', '2026-03-28 18:01:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:00:44'),
(722, '0848a3433e76de07dc383ee33ba97325837ddd991b7dd9e835cb29f837792c3d', 3, '2026-03-28', '2026-03-28 18:02:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:01:30'),
(723, '32d3153347051608eb989d399d5c15e89ee64e164000ccfe2763f3d9bad2d332', 3, '2026-03-28', '2026-03-28 18:03:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:02:01'),
(724, 'bb660480ad3b0d69364e16719d8542070f86c85f5b17f152529b2fc45f5e0942', 3, '2026-03-28', '2026-03-28 18:03:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:02:34'),
(725, 'a869c3287fa2e908fcf08407f1594cc3f1e9e6754e2dbbe97779adf5971a286c', 3, '2026-03-28', '2026-03-28 18:04:05', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:03:05'),
(726, 'e67520056979e2776a4971ff05885c0affb4c61c4da16957c16de15728e0233f', 3, '2026-03-28', '2026-03-28 18:04:38', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:03:38'),
(727, '08dc6c3bec03cb2b3bd27cfc9b5ecde0fc3f7d3f6fcce9b92842cbe77be3228e', 3, '2026-03-28', '2026-03-28 18:05:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:04:19'),
(728, 'dc87bed54652384d6a5520b261c6eb732505bc248231d4fc64eee7f13b049642', 3, '2026-03-28', '2026-03-28 18:05:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:04:53'),
(729, '6a03f95a624be9cf7c563b602aed48d8d3bb2ffb0ee626fda9774a7f574279bd', 3, '2026-03-28', '2026-03-28 18:06:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:05:24'),
(730, 'e9bdb5b17ee48f73204d046e932f723103d6c27dc3b15b3c5f1e131a6d7a7f1e', 3, '2026-03-28', '2026-03-28 18:06:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:05:58'),
(731, '14506a64eb10dcd9a8acfbb39bff8b03853bfdf33af8c5d6cd88e7aa73cdd41e', 3, '2026-03-28', '2026-03-28 18:07:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:06:31'),
(732, 'ad2b90ff7bfc6d9165295807b77a1c9f0ce309192713e83257bce74e1d94456b', 3, '2026-03-28', '2026-03-28 18:08:01', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:07:01'),
(733, '249eaa79e53d044c5a977ac3008c30e3b58591cda24f81db9c159e7f6c9c08c7', 3, '2026-03-28', '2026-03-28 18:08:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:07:36'),
(734, 'bc8d9a10095f5e6a3347492e8e708b2c6d3c63dd20b68daacf505a52612479a7', 3, '2026-03-28', '2026-03-28 18:09:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:08:07'),
(735, '9950ef2c0fb58cac1c8f26dfb2cea8611a8d24736112e11eb4457c315ee1a6b1', 3, '2026-03-28', '2026-03-28 18:09:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:08:40'),
(736, '90ad0136442f1d3326ce83934452b6ad64f94c2a31ad1f66d9b833f92ad7786a', 3, '2026-03-28', '2026-03-28 18:10:11', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:09:11'),
(737, '785610c06237d27dfbc92bf22ca01300ab717d3d18a9eca3ded4a29aee845110', 3, '2026-03-28', '2026-03-28 18:10:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:09:44'),
(738, 'bc9d4644520a3bb78203caa259b02edd238881f1dda07e29d283d271dea1cebf', 3, '2026-03-28', '2026-03-28 18:11:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:10:43'),
(739, '039685c2b99e98f3246987222d654f495d9a97e4440f9ee7061c320fe2f52622', 3, '2026-03-28', '2026-03-28 18:12:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:11:14'),
(740, '7ada2e4cec085e05c12b40983d637fbc1187204e2330727ede6527fa5dfff218', 3, '2026-03-28', '2026-03-28 18:12:51', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:11:51'),
(741, 'e0c2e2c8936bd41930d4ec8f16aa90bf0a7eedc88d6e94e8f0712cc0f0a4561c', 3, '2026-03-28', '2026-03-28 18:13:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:12:23'),
(742, '981481a4aa86972d50a7c4fd08f846f3cf74ef309b51729f64942885c60357d6', 3, '2026-03-28', '2026-03-28 18:13:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:12:57'),
(743, '64b5fae111b103ca41f6edeae0297a37634689290ff73cfe366a54a74b01a99d', 3, '2026-03-28', '2026-03-28 18:14:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:13:31'),
(744, 'b120b60a51d8c684926ebed14029633f7f0bf1d76e18eff8478d529be8a13e0c', 3, '2026-03-28', '2026-03-28 18:15:02', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:14:02'),
(745, 'd0c71766d248c022fffd857e7841ee182e90b64fbf6da8c8c6b1b12cc417b9ce', 3, '2026-03-28', '2026-03-28 18:15:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:14:36'),
(746, 'a1d93fdef886b257f0abc78cf02e653413e8b4fbefd2be7593d88971519f8f37', 3, '2026-03-28', '2026-03-28 18:27:46', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:26:46'),
(747, 'a50f95d7fa28f308cd0609df7aebc6bf7e8ebfba2a456a4fc9e8b816cf8818d2', 3, '2026-03-28', '2026-03-28 18:28:16', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:27:16'),
(748, '3c691b578ee7945cf75b046d65149f8dd81866aef2d6ac06fd7c32548ff0280b', 3, '2026-03-28', '2026-03-28 18:28:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:27:49'),
(749, '9d9a268aefbc27f30618f440eb60d7e7df923090a18edc29c0bd2441e72f1777', 3, '2026-03-28', '2026-03-28 18:29:21', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:28:21'),
(750, '8b41f8bf2eddea8df057fd5ea34170112cda4871e3c67300e6f90d85546580af', 3, '2026-03-28', '2026-03-28 18:29:52', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:28:52'),
(751, '7191f7bfc45a2b468cae463c27cecda3e42dcd0b4009fc48f6240e4fd12b57b9', 3, '2026-03-28', '2026-03-28 18:30:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:29:24'),
(752, '02243013e6a60676de50c65f5e4f791a48f1f29a5840c4c8092e00981febef38', 3, '2026-03-28', '2026-03-28 18:30:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:29:56'),
(753, 'a24601225b0ed5025cccc683762fe04da39441fdbd81b7234b5eeebcf18afa66', 3, '2026-03-28', '2026-03-28 18:31:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:30:27'),
(754, '9dcd8df6c52d66d4a0ac61ca91e4fd08906dd7dfd2eb10215b0e284bbd43f448', 3, '2026-03-28', '2026-03-28 18:32:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:31:00'),
(755, 'fb7965fb19e8630293bc83e9e09c238df2952ed5ef7d92b6ea1eef5886001a78', 3, '2026-03-28', '2026-03-28 18:32:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:31:31'),
(756, '55d37d21920060bcaa72563ac4a95bc4dbffedc014daedbc192d285ff489f7fc', 3, '2026-03-28', '2026-03-28 18:33:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:32:04'),
(757, '9dc83981861952fe8d51590c229b62d9039e3ae00c812422cce913594ce42a7c', 3, '2026-03-28', '2026-03-28 18:33:35', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:32:35'),
(758, '836bb266fe32fdecfe5849930604b11c31f08f282214fee5b23e05448e21bb9e', 3, '2026-03-28', '2026-03-28 18:34:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:33:10'),
(759, 'a8e742492335659faa0d414c6aa281a839296cc01df24873a1768601c5170f80', 3, '2026-03-28', '2026-03-28 18:34:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:33:42'),
(760, '521dd9f3bf06495796c8776268ff6dd52c5998eaefdbafe1bc62d5fc95cd5f82', 3, '2026-03-28', '2026-03-28 18:35:16', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:34:16'),
(761, 'f4b414364cec4cba45e0a881f7d702ba3d066ded4785a5d24b853623650ff15d', 3, '2026-03-28', '2026-03-28 18:35:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:34:48'),
(762, 'a8a55574c54cb37061893ca22849ce103326239348cce1916b0c6f4eb8400a4d', 3, '2026-03-28', '2026-03-28 18:36:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:35:18'),
(763, '67fe5b0e9a03ae1b724e66df9e084dd56b2a879509d966a63b7749bbbed50378', 3, '2026-03-28', '2026-03-28 18:37:16', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:36:16'),
(764, '77807d307e60f08aedfaaee863a5a8d5baf131c7635350c4421bb45ef8c07cff', 3, '2026-03-28', '2026-03-28 18:37:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:36:48'),
(765, '82c8c9c391445baad49c9c9246809b02f12975ab62e227ac56d93de66cc8ca63', 3, '2026-03-28', '2026-03-28 18:38:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:37:30'),
(766, '80dcc95c7bf28a267d7aab0d6869a336a852585a2a25d7b647448dd30ed8ed4c', 3, '2026-03-28', '2026-03-28 18:39:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:38:18'),
(767, '24942c09a5c72c3998c9e8954df678187daa938fe57b7e376b43c20b50dc63ca', 3, '2026-03-28', '2026-03-28 18:40:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:39:13'),
(768, '6132e282b74ba31d9986c379684b8bbe751442b599d5add677d9abef0e2bb4ac', 3, '2026-03-28', '2026-03-28 18:41:12', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:40:12'),
(769, '5a613ed9ad95404a6639802c53071abcc60d9ac308b2e07bf9a5571f5ac04e07', 3, '2026-03-28', '2026-03-28 18:41:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:40:44'),
(770, '25f73bd85e542c7e6c720bd851f89b4ae7f0962339536837f022d71f46bc7759', 3, '2026-03-28', '2026-03-28 18:42:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:41:15'),
(771, 'a24ac0a3d6f55589911481c73e3635291037cf745e92a38eaa65ab3fa869cbe9', 3, '2026-03-28', '2026-03-28 18:42:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:41:47'),
(772, '1ed9db036c84c6b401b676b88876b67e6ed6c56779ebb17f66e6ee0aca70d87f', 3, '2026-03-28', '2026-03-28 18:43:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:42:19'),
(773, 'c6a67594053d2bee6cc628c3e7c1c29c40118f8bec455b6dc9a7529d5a5a48fd', 3, '2026-03-28', '2026-03-28 18:43:51', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:42:51'),
(774, 'dd5f3cf61aee15cf1291d1e815e7cab4c6805a2aa455b86b9a1b6297ac59a9d7', 3, '2026-03-28', '2026-03-28 18:44:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:43:42'),
(775, 'fc280caa1d8bfb3433ce45f9bb0b10d75d96c44953f5b62925e7a2f44867a827', 3, '2026-03-28', '2026-03-28 18:45:41', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:44:41'),
(776, '339b6ba172f7310328a591058fae5ddb812120da53a4c7d4a0026d0db130b0fc', 3, '2026-03-28', '2026-03-28 18:46:12', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:45:12'),
(777, '9699ed6d3df70bb017b18882d8049a0bfac4caea6b6d3ddd088b76bc3124ff88', 3, '2026-03-28', '2026-03-28 18:46:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:45:43'),
(778, '34979db04089aea473bd1462dc59ffda5052ca9d7ef23a81663849414a5ab8f5', 3, '2026-03-28', '2026-03-28 18:47:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:46:42'),
(779, '2e6771659e1133eb8521a88b2440905f9c1720f48491a8467e62270bfd34f6f3', 3, '2026-03-28', '2026-03-28 18:48:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:47:13'),
(780, 'f26706ed28525bb09be9d17a3ab69196a5b8faf16e20a02bbf46cd78b99412fe', 3, '2026-03-28', '2026-03-28 18:49:11', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:48:11'),
(781, 'd94399b3b6fb38bac4a19104ff4f0ff7c27893258e9933459125694a42b584e1', 3, '2026-03-28', '2026-03-28 18:49:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:48:43'),
(782, '6b2750e0ef9cef8c2678a51f64723e0e4bbbf602034edd1223c3b414d96127a9', 3, '2026-03-28', '2026-03-28 18:50:35', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:49:35'),
(783, 'b8e07d7c19fea68bf2b035ec5bacf8434894a0cd9ce07390d4db76db9f267509', 3, '2026-03-28', '2026-03-28 18:51:06', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:50:06'),
(784, '483e92d1e7312b30717bc7d9ad14fcd394ffd099556da05603a147f1acde70fc', 3, '2026-03-28', '2026-03-28 18:51:37', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:50:37'),
(785, '8b969ea2f0ca6747a4bfe72a296768e80281417dde398633cac2b72f8a655d10', 3, '2026-03-28', '2026-03-28 18:52:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:51:13'),
(786, 'bcbddc287062862305b9fcd20f3d3fc43b178ebcbd14ea488ad933e16f8e6f8f', 3, '2026-03-28', '2026-03-28 18:52:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:51:44'),
(787, 'da5fb2cef50f852a11e324524316dde54551fd3a6ba8504b8461dc313ab16b8c', 3, '2026-03-28', '2026-03-28 18:53:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:52:15'),
(788, 'ee2ae6866398d03be77d4cc64e0dc4976c7ec97cfd60631bf33291be2a1c5782', 3, '2026-03-28', '2026-03-28 18:54:05', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:53:05'),
(789, '4964246403bf27eab15af6f001f0bb13b08deefd908b51616fff5521c64fb6f3', 3, '2026-03-28', '2026-03-28 18:54:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:53:36'),
(790, 'b10735f3979fde8fd47fa83a74b4cf99d4382a96c11a36b885ab3e293a36fdc6', 3, '2026-03-28', '2026-03-28 18:55:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:54:07'),
(791, '4a2d4938d5b571ebfb71e69e324e1fd7bcaf58e4902d4b5d763bcbbd35c80d01', 3, '2026-03-28', '2026-03-28 18:55:38', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:54:38'),
(792, '6e1ea8f304a42742a60091d25e188ab061622bc3d3d993d6e8ebbf35a7670ef8', 3, '2026-03-28', '2026-03-28 18:56:09', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:55:09'),
(793, '32068f27eb6ce0786564472b347b4ffd407a2b5ac790771e55af544a1794d31d', 3, '2026-03-28', '2026-03-28 18:56:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:55:55'),
(794, '2eee94ebc12d7e4a3d04bff92e4cfadc390d5763f4e2048863be0a7b853b9095', 3, '2026-03-28', '2026-03-28 18:57:39', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:56:39'),
(795, '8c90f8496a4bb925e675465c43cda60c201336971386e4aa96768293e8c5ffb2', 3, '2026-03-28', '2026-03-28 18:58:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:57:10'),
(796, '2612144ba8ce51b035dafd2a1a9e8b74fc7e6f3a0dd1ee8da58c641d397a6990', 3, '2026-03-28', '2026-03-28 18:58:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:57:42'),
(797, 'f13c6c8a1ddec6dbc587f26454da1bf35e5f39d3eadd76f1267d9620df7f7027', 3, '2026-03-28', '2026-03-28 18:59:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:58:13'),
(798, '802ba3953b17a7bc8f87c469366e569cd367534d9218d6d788d8456ea958d22a', 3, '2026-03-28', '2026-03-28 18:59:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:58:47'),
(799, '588303fc50f64ec760d4b7a0834c85733dc2156ab2025433a38e464cddb6b358', 3, '2026-03-28', '2026-03-28 19:00:21', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:59:21'),
(800, '90a588cf2c678eef22b4aecd59931b99c58e74378cebd53f5102689172984746', 3, '2026-03-28', '2026-03-28 19:00:52', 0, NULL, NULL, '10.231.241.98', '2026-03-28 10:59:52'),
(801, '24e152949263b32c44751f5cf851b25e620280ad33b9ce90ad64393ee7958e85', 3, '2026-03-28', '2026-03-28 19:01:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:00:23'),
(802, '404feaf123c8c77859da89947a41b6115474b2ae0f6120f64a640f10c40d0bae', 3, '2026-03-28', '2026-03-28 19:01:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:00:55'),
(803, 'acaf9a0653313daf39d8714611791ad311e0c0f7cf4806ec92e352a92b1c5fc9', 3, '2026-03-28', '2026-03-28 19:02:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:01:28'),
(804, '3f13e45f6e6ec526b8fecf639ea763f8d44fd896d7f903d3c0c0bd6fcc2b2211', 3, '2026-03-28', '2026-03-28 19:02:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:01:59'),
(805, '936621b08fc09ee19d8351175f03976a6f4d2716eeeaa4a26bd93b56f4afbd55', 3, '2026-03-28', '2026-03-28 19:03:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:02:58'),
(806, '0e5ea344668b6ff363e9236e60b787eb7e6cde5b58819c0526f2d39a18251ade', 3, '2026-03-28', '2026-03-28 19:04:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:03:29'),
(807, '282a9457a8fb8a023c73394954160741dc26f71ae5e33dd4916ee6256022ac4d', 3, '2026-03-28', '2026-03-28 19:05:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:04:28'),
(808, '6a5fc47289877ad87d6b1f98bc8bbb84493c9866211622625180bd9a1e15a989', 3, '2026-03-28', '2026-03-28 19:06:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:05:27'),
(809, '67a85049eda7a298d43718c7671a67d981c6b73b0ced486003f769745a7da881', 3, '2026-03-28', '2026-03-28 19:06:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:05:58'),
(810, '1909e012b520f4bd34a9ce42d1539725b1868d12a62b0a2aa65803788da3c762', 3, '2026-03-28', '2026-03-28 19:07:39', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:06:39'),
(811, 'e421a10117cfee0f273e76ad5fe5993d83000d7a2c534e9e02593767a14de108', 3, '2026-03-28', '2026-03-28 19:08:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:07:27'),
(812, 'fcc10c679bd621830d3c41002a0cce461a19ac7f0e98439528c2ed1e89e647f1', 3, '2026-03-28', '2026-03-28 19:08:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:07:58'),
(813, '07a1ecf3cd78ccf72e5a6d60328906712ef392581a5707fe7c00ff4a8328b988', 3, '2026-03-28', '2026-03-28 19:09:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:08:29'),
(814, '278881c2b0025719c0da6e588a3ee57d832b1b93773aafbb434710baa1ba43d2', 3, '2026-03-28', '2026-03-28 19:10:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:09:00'),
(815, '9ca15a3976867624a79b5f95a63f2ae9fdefc5537df3cf857322303bff0832b8', 3, '2026-03-28', '2026-03-28 19:10:32', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:09:32'),
(816, 'bcba2b010b2c1a8ec0d97d8fd7a46d8506869346261b83e76db8767dd915f507', 3, '2026-03-28', '2026-03-28 19:11:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:10:18'),
(817, '4b3999d8b30d62aeae8473dcb9d6c23670c0ed5ee250a07ee73ac3c8fe7d451e', 3, '2026-03-28', '2026-03-28 19:11:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:10:49'),
(818, 'c8fbaa955371121014b7b21a0b97b8c3054a9c1d4704ab00a9ee74f1993d5bb8', 3, '2026-03-28', '2026-03-28 19:12:20', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:11:20'),
(819, 'd60a8acb82f9bb0ed4e52dd0aee38712a5944762bd3827b38bbceaf9e0076f14', 3, '2026-03-28', '2026-03-28 19:12:51', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:11:51'),
(820, '7b9b23531753b455c22d49a7b8a887d102fca7a28cec44fddc1d92dfd3d7f6c4', 3, '2026-03-28', '2026-03-28 19:13:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:12:34'),
(821, '274890dd21b0c89a9cda6fbf1ac4cd0105e864c031775236d2e2d51226fe9742', 3, '2026-03-28', '2026-03-28 19:14:05', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:13:05'),
(822, '93b25d5f45a02fcca9d1fd88db0423e076552c21c93d2eef92e29950321584fc', 3, '2026-03-28', '2026-03-28 19:14:37', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:13:37'),
(823, 'd8d3cf0c6dad9b81c49d2c86eae7d74aa02ca4a2ec26a6fa629ffa1eb66b0a1e', 3, '2026-03-28', '2026-03-28 19:15:08', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:14:08'),
(824, 'b55e6bbe8e5ea1025ac8cd8b7c7793afa985a32c5f94afd56d9d0529529ae1e0', 3, '2026-03-28', '2026-03-28 19:15:39', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:14:39'),
(825, 'c0c7e4c6b2ed64ec52f3703224225938f08d2991317333ca1af3215329259bd1', 3, '2026-03-28', '2026-03-28 19:16:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:15:17'),
(826, '2f2d071b413156128c3485754759f6f1179a47c29bd99ac65798fc5de3cdb765', 3, '2026-03-28', '2026-03-28 19:16:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:15:48'),
(827, 'ead35745a3435299c97c710151804653950f3736b08e3245a9852f7740a53830', 3, '2026-03-28', '2026-03-28 19:17:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:16:19'),
(828, '2655ebaf9e57174312ae60749bcd349b3c04af67fdd78f09211dd217a74d40b4', 3, '2026-03-28', '2026-03-28 19:17:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:16:30'),
(829, '19520f2c23c8d5a414824a131f5f1fa2f4f8f8f68f7c43effcda1294e219e707', 3, '2026-03-28', '2026-03-28 19:18:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:17:00'),
(830, '5e6ed38158073b1a7f25202a62312b05bb8de58f4659c02de097256cd87f99ba', 3, '2026-03-28', '2026-03-28 19:18:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:17:59'),
(831, '39ea4a084811fb4b6c6d70fb9465fd62ca6bf0197f233cafcb56d3aad149ca33', 3, '2026-03-28', '2026-03-28 19:19:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:18:47'),
(832, 'a88df89caa512e196f6fbaceaff3858de2cbbd04316baa70093c6dcc95c169df', 3, '2026-03-28', '2026-03-28 19:20:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:19:18'),
(833, 'b15edad6eb73f4bccf539510a51e617d552b2811d7af66bbbf72875da5dee46b', 3, '2026-03-28', '2026-03-28 19:21:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:20:00'),
(834, '4ae950d23ff252bb386880e1b49758676b24d8cc08efee7b5fdb5c76f6922d05', 3, '2026-03-28', '2026-03-28 19:21:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:20:54'),
(835, 'f110a7baa94face5fcf2c4114b4c9811ac9a1acb63a16d96f2774a93bf570e85', 3, '2026-03-28', '2026-03-28 19:22:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:21:45'),
(836, '6656f3f0f3574023d4539a47e6cccb389745b7f1b037e799d4f6039330800c80', 3, '2026-03-28', '2026-03-28 19:23:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:22:44'),
(837, 'f7ade076ac51a163569bcfee57fc4089e87a198ab07f1502ff355fb37f1b0800', 3, '2026-03-28', '2026-03-28 19:24:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:23:42'),
(838, '654b0ec8b47e7e3412a3f7758b6baccbfb252f536401ac97f0e14dde6aeeb28b', 3, '2026-03-28', '2026-03-28 19:25:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:24:13'),
(839, '8a09684d349e115d1e607a9c460d69d4fee8405717ab6b9f6f3feb1f832ee1ec', 3, '2026-03-28', '2026-03-28 19:26:12', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:25:12'),
(840, '35bb2d785d9d9e6577e5b91a16bb019586eb47ab7360394aa1335c225fd04136', 3, '2026-03-28', '2026-03-28 19:27:11', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:26:11'),
(841, 'cc33da365d52c94f9e044a20b418a05829fc071635422d175f34601020e9b212', 3, '2026-03-28', '2026-03-28 19:27:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:26:45'),
(842, '23f079484c60869b59dd20be26c4da32cc52b92f0639de7ff1aec64b31f5a2e9', 3, '2026-03-28', '2026-03-28 19:28:21', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:27:21'),
(843, 'a5b9f9b9be287503172e274ca9808dc815839d2faa6a3f97cade8eb6b5cada60', 3, '2026-03-28', '2026-03-28 19:28:52', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:27:52'),
(844, 'd5d770e65c246080e11226ef825848a1bebe39e002380df91a55b3ba5bd44429', 3, '2026-03-28', '2026-03-28 19:29:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:28:24'),
(845, 'f15e0af2c50506616d95934a496889580ff9a8cda4f810f0470f13ad7c9bed3d', 3, '2026-03-28', '2026-03-28 19:29:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:28:56'),
(846, '6b0a37a2ee5be062860c215c72bda3c1b68cd16fdb8fd22657c50ef6287fa282', 3, '2026-03-28', '2026-03-28 19:30:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:29:27'),
(847, 'ca839d8250af03908334c9ac2662500eadb287b7b0674ef9da6adf35469bc448', 3, '2026-03-28', '2026-03-28 19:31:02', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:30:02'),
(848, '8c3447409a20c8b8f35f519492df1ba1985bdddf97486fb9d784ba65155ffaf9', 3, '2026-03-28', '2026-03-28 19:31:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:30:34'),
(849, '8dcba2f932d2d148507ed00256cbbaf654556ea6e3b9b5f89ea353c9a47442c4', 3, '2026-03-28', '2026-03-28 19:32:16', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:31:16'),
(850, '8fbbf4f03c6747321e1d73278136fe90765a38a2717d07f385a6f4f3a6fc74ad', 3, '2026-03-28', '2026-03-28 19:32:51', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:31:51'),
(851, '410634f12dec6d3953319c4730e99485f2fafba10e4405f75e5a2acd24a491a7', 3, '2026-03-28', '2026-03-28 19:33:22', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:32:22'),
(852, 'd5f6f397d587b004ef2bd9d23269bf8fea1af6c37db77ce2c3fa63d41bb1e50a', 3, '2026-03-28', '2026-03-28 19:33:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:32:54'),
(853, '037b3ff919f6e6c1930af87589a6813d2125c17bb22e6f1975228bc1e6f337d4', 3, '2026-03-28', '2026-03-28 19:34:25', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:33:25'),
(854, '7b2662e5bbf072aa5d1f8d2a1e7ef51622ea9a100536d69a7d256d23b543fce0', 3, '2026-03-28', '2026-03-28 19:34:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:33:56'),
(855, '85ef773290acd02cbd68b79053f8e1d0632ecd18b29a308b62237c3f00e5e3ba', 3, '2026-03-28', '2026-03-28 19:35:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:34:27'),
(856, '51f1ef9c45b6d6826e638d9d1f69a369ba452f1b9c1a77eabb2702a757e5b0ed', 3, '2026-03-28', '2026-03-28 19:35:58', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:34:58'),
(857, '713b38dab8183be537ebe48ecd470b0cf725092492cd0909187daa326e98b3ff', 3, '2026-03-28', '2026-03-28 19:36:29', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:35:29'),
(858, '52f0acb56cf71deaaecd4a2f1023aae090c90e35687d7a5a2278921a949e2b79', 3, '2026-03-28', '2026-03-28 19:37:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:36:00'),
(859, '2d874bce87e4302ab1866aaa1d7986fb8cc6ab2c875c524d3cd9f919f1ed3061', 3, '2026-03-28', '2026-03-28 19:37:32', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:36:32'),
(860, '10300b49aa3d729982ad11d430ebd97cfed65e56876c0023a721d4d28403eee9', 3, '2026-03-28', '2026-03-28 19:38:03', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:37:03'),
(861, '17507c3f7fcc1bcc7080cf1c50c7f8c87309fdbeef8483e486303f710b58cc2a', 3, '2026-03-28', '2026-03-28 19:38:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:37:34'),
(862, '671e55c608a44a0e014c2360180fc8ef65dde6731ccc9463e42c33360e5a5e23', 3, '2026-03-28', '2026-03-28 19:39:05', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:38:05'),
(863, 'cdad2e4379ca62d337e30211c598dedcc86029f67b328a7e8a9ee9f7886bc8d9', 3, '2026-03-28', '2026-03-28 19:39:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:38:36'),
(864, '9c00f9d9a34394c54ea012281a93da92840ab03ef7506489d8ecdd2904cec211', 3, '2026-03-28', '2026-03-28 19:40:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:39:07'),
(865, '586cd037efd9b038419735835159af2e8156842393a4cd88e84f3a337a7f0f49', 3, '2026-03-28', '2026-03-28 19:40:38', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:39:38'),
(866, '1f5980bf1a2c7a6344e7faf8f964bb6ec852a3522866f3476e8cf5ff15e871d5', 3, '2026-03-28', '2026-03-28 19:41:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:40:10'),
(867, '1b488728a6abc576e2e8d7d086ad00e3302c39c7fa00f838656c31451d58760b', 3, '2026-03-28', '2026-03-28 19:41:41', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:40:41'),
(868, '169a60fd4858186006ad8ce87f2de6336028115bd1ab9603a2b2ae7459a5afd7', 3, '2026-03-28', '2026-03-28 19:42:12', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:41:12'),
(869, '4db9335748549a5f1334b48feb97188f8d1fe73b80069a44733880c988655857', 3, '2026-03-28', '2026-03-28 19:42:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:41:45'),
(870, 'b34335a5bb05dbdeb4483fdd99fbbf0f558c7264306f936e7ba442a2e0ea76e2', 3, '2026-03-28', '2026-03-28 19:43:16', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:42:16'),
(871, '1202b93ddf3597370fd2a5832573a049c8deb802f9d0b9053f8e3a99ebd18d86', 3, '2026-03-28', '2026-03-28 19:43:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:42:49'),
(872, '6b72d8cbdd75babc4d376acaf9787fbd09bbb3f0c910b06882b79ed87872906f', 3, '2026-03-28', '2026-03-28 19:44:20', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:43:20'),
(873, 'eb29f48070e8d97602bad2d7bad12e7a808939c340ed3bfbc5946f322f185b8f', 3, '2026-03-28', '2026-03-28 19:44:52', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:43:52'),
(874, '8e74d9b18607593b8c51dd475ac91aa4568a584e956c0897fb93c982e3d4268f', 3, '2026-03-28', '2026-03-28 19:45:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:44:23'),
(875, '6f1a61c9c138f735ac560f68d4decfa01e150250fbd0de908dd8b94ea8ebfa37', 3, '2026-03-28', '2026-03-28 19:45:54', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:44:54'),
(876, '572341035405d2bd8c61db5338f7869a8a832e961e592a12322b15d65b37af65', 3, '2026-03-28', '2026-03-28 19:46:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:45:24'),
(877, '9615b4001e965dba1283615464ce75041283b9ab067b5046a7419e57104cc171', 3, '2026-03-28', '2026-03-28 19:46:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:45:55'),
(878, '27e77a5a58c7089e863295acdcd2356886ea39beb4759141bb4fb7d9514f53d5', 3, '2026-03-28', '2026-03-28 19:47:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:46:28'),
(879, 'a140bbee3e0d27717ed36da6d2d91684523c52eb622e8718c79f0fa95ac3d84a', 3, '2026-03-28', '2026-03-28 19:48:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:47:00'),
(880, '8119c97fd9bf757935d986757a7b29d08e33472d7fcdec787fc77a271ba7b1d8', 3, '2026-03-28', '2026-03-28 19:48:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:47:31'),
(881, '4ff441bfde25eb698f6e4505bded07738786afbfdc75bf587bd3b0f80600b354', 3, '2026-03-28', '2026-03-28 19:49:02', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:48:02'),
(882, '3af67df3019dd0c937de723605233bdc3944642de8f14efea4271dda00494bbe', 3, '2026-03-28', '2026-03-28 19:49:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:48:34'),
(883, 'b56a4aadff6ce44302ce9d5609c800c73937af2127b58971c3055eaf015c2069', 3, '2026-03-28', '2026-03-28 19:49:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:48:40'),
(884, '26d1c3492d248ab3a1b92de5d5cdcebda873bf2dbb6dceb06ded46fb998075fd', 3, '2026-03-28', '2026-03-28 19:50:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:49:10'),
(885, '9ff2bb9465ce6028d7f7de67c096b3c660daf9764228aeb2e08319536037a623', 3, '2026-03-28', '2026-03-28 19:50:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:49:42'),
(886, '579f2a8519fe268fd80d3d39ce73baba6e0628a0002cad11636913dd3541a640', 3, '2026-03-28', '2026-03-28 19:51:14', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:50:14'),
(887, '74538688f4119a6ca7087fe8bcc77fa63ab933bbfbfa6e9ce4d4bc10d2ccbe8f', 3, '2026-03-28', '2026-03-28 19:51:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:50:45'),
(888, 'f5e9a6303f373d5b0272308e5a15077d482e5734645cdc12c8ae67221d81e0b1', 3, '2026-03-28', '2026-03-28 19:52:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:51:15'),
(889, '16afb802e6ee9138b0d2998ee37f508290ddd4f576752d97329a5c35fb185d82', 3, '2026-03-28', '2026-03-28 19:52:46', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:51:46'),
(890, 'eb4330dff10bf84fd002231cfb522a74fa9beb0d4a8d358f7209beee7462de87', 3, '2026-03-28', '2026-03-28 19:53:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:52:17'),
(891, '2ad2689447d5f1a586b28ccece7df0ef932ecd22837627f70eb1f0567caad7a2', 3, '2026-03-28', '2026-03-28 19:53:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:52:42');
INSERT INTO `ta_attendance_tokens` (`token_id`, `token`, `generated_by`, `generated_for_date`, `expires_at`, `used`, `used_by`, `used_at`, `ip_address`, `created_at`) VALUES
(892, '8b5bb4deae04084c943d0c24a5da3dede120d2760062bd21c456885f28d6875f', 3, '2026-03-28', '2026-03-28 19:54:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:53:13'),
(893, 'ecccecc9f74fe65aa41149387f20c9a1cf9dfcbdcbc7018762eee2fd92e05b05', 3, '2026-03-28', '2026-03-28 19:54:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:53:49'),
(894, 'aa953e210f39fce9b5e3a0e909237e5c9fa7c84a8698b0c23c429588a228abd4', 3, '2026-03-28', '2026-03-28 19:55:22', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:54:22'),
(895, '49dd5d1b1bf42d1f2c53d56bd33133765f43db3a363aa4b2fedfdf4b33f3f5b9', 3, '2026-03-28', '2026-03-28 19:55:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:54:53'),
(896, '26315fc25ce8fa004883fa1adb9ffd8750fdfefa03400977bcabae4731ffe8fb', 3, '2026-03-28', '2026-03-28 19:56:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:55:27'),
(897, '46ce258564eac4c13d6074e698bdb9c5b0a79182a06b5baa23d372c2ad576852', 3, '2026-03-28', '2026-03-28 19:57:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:56:00'),
(898, '3374f0909670e930316364c15d9a64fd9e82f9c489285dd62ce7f222a3899ade', 3, '2026-03-28', '2026-03-28 19:57:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:56:31'),
(899, '3f4195add379af8bf2d2424365f96564e489cabf691ba9c7e75480bb7c0deb57', 3, '2026-03-28', '2026-03-28 19:58:03', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:57:03'),
(900, '188a73ed9b225eab6df611065d89d18e73df8e55704ff6e44e0af1f84b4b3b93', 3, '2026-03-28', '2026-03-28 19:58:36', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:57:36'),
(901, 'b974159a7961fbe28cd5e81a66f7cb5fa1e37d816c172df37b8a821880dac949', 3, '2026-03-28', '2026-03-28 19:59:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:58:13'),
(902, 'e31335928eef2a66cdd493c171136f1f2162c86be5b599c11499b89aa0523854', 3, '2026-03-28', '2026-03-28 19:59:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:58:44'),
(903, 'b9feb3303435ae4ca450e1700a22409a37c940cad362bbc30b8a77df67e0fa47', 3, '2026-03-28', '2026-03-28 20:00:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:59:15'),
(904, '17d50358803fb3aae0952fc494ff090e1043ae9b0c066d556925aff818b5e534', 3, '2026-03-28', '2026-03-28 20:00:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:59:23'),
(905, '13f9d91bfb90de37bfc1f095080a2c5e05796cb57c5207a81635a65564e0ec64', 3, '2026-03-28', '2026-03-28 20:00:53', 0, NULL, NULL, '10.231.241.98', '2026-03-28 11:59:53'),
(906, '9aaa2590287ebfc0b77166936b31b085b16c73636c9df38113f799916de9ef1a', 3, '2026-03-28', '2026-03-28 20:01:24', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:00:24'),
(907, '58b7780f1910a855fea6cc2e769d58f4fcada1acf67b627b4c72be4764e12aec', 3, '2026-03-28', '2026-03-28 20:01:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:00:57'),
(908, '149728601edba7c737bf83195e89e044afcb7f795687a46282d629e2d42610ea', 3, '2026-03-28', '2026-03-28 20:02:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:01:28'),
(909, '6fff81c198e10b3f7b9b71719f394b475b68857c7c4d31178eb81e4c379e9a9b', 3, '2026-03-28', '2026-03-28 20:02:59', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:01:59'),
(910, '4b1bf3035c738ba618396ebeb4db892bbf7c505b51f919eb8e465cccc29f6721', 3, '2026-03-28', '2026-03-28 20:03:30', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:02:30'),
(911, '0847515a31952aa119d306ed3eb61fa0b452a2ec8b4163a6cb19e78ef8c46467', 3, '2026-03-28', '2026-03-28 20:04:08', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:03:08'),
(912, '2f4236c44175d964e65f2491f416b03b36b7e4d7bbb8821dbaa20d0fb1a26102', 3, '2026-03-28', '2026-03-28 20:04:39', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:03:39'),
(913, 'efe6ac6cedabfcc75d4415cc24746aa4d4b467e105b8980f6959db3867ae74cd', 3, '2026-03-28', '2026-03-28 20:05:12', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:04:12'),
(914, '4c33c4a3ea36994f7f0aa8020e9b2e44a17e2730eb787e6b543c1a668cabc834', 3, '2026-03-28', '2026-03-28 20:05:43', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:04:43'),
(915, '3661cf8878c1b845a5ea60ce1eb94855f35068b752ae54101e4d5aff6d6c2dc6', 3, '2026-03-28', '2026-03-28 20:06:20', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:05:20'),
(916, 'c624d93791fcf3501b04ae78124bda3f7e8d3aa44e3f19407e883d9c6854957e', 3, '2026-03-28', '2026-03-28 20:06:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:05:57'),
(917, 'aa87e3547e524822548162715c980ca8167fc044b973ea435fc438c9ebe44a05', 3, '2026-03-28', '2026-03-28 20:07:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:06:49'),
(918, '4660c304050e8bc0285b3fb5d7007fee5c8718bcec7c715b8a5f03c263e592c5', 3, '2026-03-28', '2026-03-28 20:08:23', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:07:23'),
(919, '550388147fe9a4e0435cc67d54503aa8cb5ffbc7c8cb7f74a0240104a28d5001', 3, '2026-03-28', '2026-03-28 20:08:55', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:07:55'),
(920, 'fed87d4ac8ab61bde338a1b286f54e539bf81e50f7ac788464f6d4382d4454a0', 3, '2026-03-28', '2026-03-28 20:09:33', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:08:33'),
(921, '26091a3b95d57125d3a6baee1f72fb90907ef9b7c20b91afbd6c3eeed6ed5c2c', 3, '2026-03-28', '2026-03-28 20:10:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:09:04'),
(922, 'c552219f5e4803bcf253df072da07ea55fdebf7df6decef33df068c8a60107bb', 3, '2026-03-28', '2026-03-28 20:10:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:09:42'),
(923, '7b4da9ecbc199da12780dc949c153d542aac030091b7de19f33acec4962f4ec2', 3, '2026-03-28', '2026-03-28 20:11:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:10:15'),
(924, 'ab414c7557ad57cbce59a02625a6080994195e1b7c5e1e8bb24db91351bc7b36', 3, '2026-03-28', '2026-03-28 20:11:50', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:10:50'),
(925, '3a414d13f39b9de7ef90dfba97f88ba4d91800356ca251556ad47cf8e076c6c0', 3, '2026-03-28', '2026-03-28 20:12:45', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:11:45'),
(926, '099200e200ae8a3554baf629160ddc22794c67212677834ea4c926963342be95', 3, '2026-03-28', '2026-03-28 20:13:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:12:17'),
(927, 'aedddcafba517b3c3dc0cf0556e31fb0a3ae98cf4f04f5d8bfe1b6c8ec971bca', 3, '2026-03-28', '2026-03-28 20:13:48', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:12:48'),
(928, 'cb7fc78c358a7a374eb34b6f538c28a63c0814cb75f7e95719c8041cb72211af', 3, '2026-03-28', '2026-03-28 20:14:19', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:13:19'),
(929, 'a21b1b0e517486fabfd129e60a85a6f2083cd2a3ad6beb0ac294ec345eb6441b', 3, '2026-03-28', '2026-03-28 20:14:50', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:13:50'),
(930, '6cf0d885700f08a9d0f8ecefbf9abe257970087124945541dd15ac081dd03a58', 3, '2026-03-28', '2026-03-28 20:15:21', 0, NULL, NULL, '10.231.241.98', '2026-03-28 12:14:21'),
(931, '6d14601bb69cffa8a0c5c81a4fe95ac420beb5a6ba324c509358d0eaf7b143ee', 3, '2026-03-28', '2026-03-28 22:43:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:42:40'),
(932, '740439890b7283ea68168673815cb9d81bcbdfa1a89b9a786fea9ddf4d8e1dc2', 3, '2026-03-28', '2026-03-28 22:44:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:43:10'),
(933, '8c2395e1cd9121c11aa365431254ca3c93cbdef5d1f391f621b5e6f9cd0be19e', 3, '2026-03-28', '2026-03-28 22:44:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:43:40'),
(934, '03c1ce612d19a0e44d2bd0affc37fac86495e9cab21151e98229a0d821585bce', 3, '2026-03-28', '2026-03-28 22:45:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:44:10'),
(935, 'c5d91500388bcf3b0c3e4dcd14c3e6256391b80bdae004d94b3320c3f7197c8b', 3, '2026-03-28', '2026-03-28 22:45:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:44:40'),
(936, '1c39b1922b3c43b0ce9d70880b975be3142cf4299b7a38c4fb10c0f73b7b3e1b', 3, '2026-03-28', '2026-03-28 22:45:47', 0, NULL, NULL, '::1', '2026-03-28 14:44:47'),
(937, '46a8696c7546143df3f1bafb308e356febca1bdc394415351994960a3067adb1', 3, '2026-03-28', '2026-03-28 22:47:29', 0, NULL, NULL, '::1', '2026-03-28 14:46:29'),
(938, '1e6e8948f991e291867caf179d51a98f241aed65139096522ef3728532b94629', 3, '2026-03-28', '2026-03-28 22:47:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:46:56'),
(939, 'fb8df7f6ffbfc657f6b771bc6fa9aa05b760c67da83e90056feffa76e80fca90', 3, '2026-03-28', '2026-03-28 22:48:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:47:26'),
(940, 'f097da2f416ecd5f3691d8d06d3a96fa0c061b804e212fa063e8b5e358b8e337', 3, '2026-03-28', '2026-03-28 22:49:07', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:48:07'),
(941, '28d474dea4f011df54c26d6784157970f61422540c16b87e7c6ea36802f3fb14', 3, '2026-03-28', '2026-03-28 22:49:38', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:48:38'),
(942, 'c47549ae6f1d9f3496919c1b1381e95ed7d59ef60584362fc7a0f1c6b5cdcc03', 3, '2026-03-28', '2026-03-28 22:50:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:49:10'),
(943, 'e3764c46e58661f42673f186aa69d2e0548c7bb56191fccb02f1a35e734b5b22', 3, '2026-03-28', '2026-03-28 22:50:41', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:49:41'),
(944, '5f2b8929be765edc6b8a2735dc390796da1a12cf3c0e167f1c9ddca9e3999c1c', 3, '2026-03-28', '2026-03-28 22:51:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:50:13'),
(945, '21a0fd33b3d436ccf0688d4eec40d62ad958708961d5a62f8a03889fa72f1bb2', 3, '2026-03-28', '2026-03-28 22:51:44', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:50:44'),
(946, '3b11ebb588a4bee482591fd9f386cf113daa2c05a3bb142c6c2502302f6e8f78', 3, '2026-03-28', '2026-03-28 22:52:15', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:51:15'),
(947, '84b5522d9068ab08cea669fb7325fe1395a59d5bc85a9a542363cc6d2d07360e', 3, '2026-03-28', '2026-03-28 22:52:56', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:51:56'),
(948, 'fc13ca274342f790b2f9bd423ef5866457edcf4c4e32d4019630bd0dbf6c1b86', 3, '2026-03-28', '2026-03-28 22:53:27', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:52:27'),
(949, '3f242baef432ab1007eab7f6a63523e4cf2de79af6b30fd3357bd0bd335dedbb', 3, '2026-03-28', '2026-03-28 22:54:00', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:53:00'),
(950, '9f7283639d6f5a8a27dacc3aa130f3c43da0746164fdd99bf1aaf2e8df110f69', 3, '2026-03-28', '2026-03-28 22:54:31', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:53:31'),
(951, '21bf736a6699c5080ec860365686340340f03e18a4c3cab24b0c1f4013486669', 3, '2026-03-28', '2026-03-28 22:55:02', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:54:02'),
(952, 'a00f3a5c2c6f0a72839e60286b00f6448c788d5dc68410ad7da7be6c3c71d8af', 3, '2026-03-28', '2026-03-28 22:55:34', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:54:34'),
(953, 'c054b4f03cf6bc0e36141309db6eb277896daba803d7c9e934f712958acb8132', 3, '2026-03-28', '2026-03-28 22:56:05', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:55:05'),
(954, 'af08e33d6c126728e371dfaae30a35381c987cdf557142acd21ac016ca6c6eac', 3, '2026-03-28', '2026-03-28 22:56:26', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:55:26'),
(955, 'd7b322e99279dde68fbce1059509a9ec5c85b13c36bd86bc4becd88168f05ab4', 3, '2026-03-28', '2026-03-28 22:56:57', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:55:57'),
(956, 'e5f2007a16c29d79cbe332983a59e397f115706ae107e256b6dd772796086fd2', 3, '2026-03-28', '2026-03-28 22:57:28', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:56:28'),
(957, 'e55ed2e5aaba23a8e3d20bab95b6d1a58b1414c805b601dddf2bec443e936887', 3, '2026-03-28', '2026-03-28 22:58:04', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:57:04'),
(958, 'aba7cf3a50a3613d7a3d524bc13efe8e296421a7c76b932bfa19b4d347f53765', 3, '2026-03-28', '2026-03-28 22:58:41', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:57:41'),
(959, '12480a5e576af6c40b7773180eacca570be5b0ee8fe34dfed07be2a5f145506a', 3, '2026-03-28', '2026-03-28 22:59:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:58:17'),
(960, 'e3ff305e334a81b6f27678fcb3449160f76bae4c4d80c2595c54ac8a0560a0a8', 3, '2026-03-28', '2026-03-28 22:59:47', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:58:47'),
(961, '9deefb7582d4111eeb0e1244b2367978613921e3e2ff841801cd0dbeee994d34', 3, '2026-03-28', '2026-03-28 23:00:18', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:59:18'),
(962, '57d2ac174011929466831410185e51ab4ea5ea007a6a28d2afc32e2cedfdb026', 3, '2026-03-28', '2026-03-28 23:00:49', 0, NULL, NULL, '10.231.241.98', '2026-03-28 14:59:49'),
(963, 'f975d57e9ac870337ea24970d63a1c41ba96bad2d700e9150a4e516638b49b10', 3, '2026-03-28', '2026-03-28 23:01:16', 0, NULL, NULL, '::1', '2026-03-28 15:00:16'),
(964, '062d2f2f0ab0dc5e99c16b2db7b8b7138e7003f9ea48485f7f3b6e449c1f116c', 3, '2026-03-28', '2026-03-28 23:13:11', 0, NULL, NULL, '::1', '2026-03-28 15:12:11'),
(965, '02229d8d2f9d6b2ce9d74ad2d83858c017952d3050b0f77f2c0bb6aa4df675e9', 3, '2026-03-28', '2026-03-28 23:13:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 15:12:40'),
(966, '11759556f2309e55466d89cabb182845539c1f5cae5077b874d2ca0bab352636', 3, '2026-03-28', '2026-03-28 23:14:10', 0, NULL, NULL, '10.231.241.98', '2026-03-28 15:13:10'),
(967, '825005020ecfdffabb6278d6d7fa2db5f0abc5aa5f65fb94b9ef4eb4687955ee', 3, '2026-03-28', '2026-03-28 23:14:40', 0, NULL, NULL, '10.231.241.98', '2026-03-28 15:13:40'),
(968, 'd9a7bc3839a5208ff6c7994e92f8bb3152564ae3d6576abe23ee2248d4ec7341', 3, '2026-03-28', '2026-03-28 23:15:11', 0, NULL, NULL, '10.231.241.98', '2026-03-28 15:14:11'),
(969, 'f01c693486c9755d9c4b489ada5f13a576fa25d8c3a1df2608162bd91d160ba6', 3, '2026-03-28', '2026-03-28 23:15:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 15:14:42'),
(970, '2722eaa5b1ca756631508e032054a94d5cd4c49eae9ca26ec8a87f1a98ffa978', 3, '2026-03-28', '2026-03-28 23:16:17', 0, NULL, NULL, '10.231.241.98', '2026-03-28 15:15:17'),
(971, 'f64dd99dba3896bd5a740ca12dfa2feedf3684f03033b52c87ad985f25481d97', 3, '2026-03-28', '2026-03-28 23:17:02', 0, NULL, NULL, '10.231.241.98', '2026-03-28 15:16:02'),
(972, '1965fbfb413c80cac199f3ac49d052449e3ffae131242523357398df8ba05644', 3, '2026-03-28', '2026-03-28 23:17:42', 0, NULL, NULL, '10.231.241.98', '2026-03-28 15:16:42'),
(973, '672a86e8d3b082c147da8ec26b4355b56fb709359f409c8eeeffbddb70d9b5cd', 3, '2026-03-28', '2026-03-28 23:18:13', 0, NULL, NULL, '10.231.241.98', '2026-03-28 15:17:13'),
(974, 'abf3c0d83f9ec98b03901fd3176465ba8717afaa9476138569ebbda8373e3fe7', 3, '2026-03-28', '2026-03-28 23:18:35', 0, NULL, NULL, '::1', '2026-03-28 15:17:35'),
(975, 'aa3cbf84089419c40246766d80720dc848ec125dd5dd3974103a22e84f84220b', 3, '2026-03-29', '2026-03-29 16:56:53', 0, NULL, NULL, '::1', '2026-03-29 08:55:53'),
(976, '79fe2d5fe67db29d7ca0b294926b48866d41fe2772817f867d7bd303914c4ae1', 3, '2026-03-29', '2026-03-29 16:57:24', 0, NULL, NULL, '::1', '2026-03-29 08:56:24'),
(977, '56564c04adc160fbb7e0d6b8f0fdb7b94ee6d6da73a16a3455dc7b068f7dfc09', 3, '2026-03-29', '2026-03-29 16:58:10', 0, NULL, NULL, '192.168.68.136', '2026-03-29 08:57:10'),
(978, '6ed96e708bea50cd55893c2db46c24cc1ffb8882ea6dbd04ad13a6e72d279d6b', 3, '2026-03-29', '2026-03-29 16:58:41', 0, NULL, NULL, '192.168.68.136', '2026-03-29 08:57:41'),
(979, 'db93de617759a1e3c2a701013cead778a9914fceb46c75f669c8232770640da1', 3, '2026-03-29', '2026-03-29 16:59:18', 0, NULL, NULL, '192.168.68.136', '2026-03-29 08:58:18'),
(980, 'f9b5cc8d60fbe3cb08bf586b96be40df125f00a16a1e4f234398ad1caaeda349', 3, '2026-03-29', '2026-03-29 16:59:48', 0, NULL, NULL, '192.168.68.136', '2026-03-29 08:58:48'),
(981, '73d99e125c68bf99d0bf335bca55535cd6ead3153ded1576b9d926ee6065fe9f', 3, '2026-03-29', '2026-03-29 17:14:02', 0, NULL, NULL, '10.231.241.98', '2026-03-29 09:13:02'),
(982, '9a05fdc304a0c4dc5eb87f27cae3cf8ba749a2bdc313c8539cc8b2f561ce6938', 32, '2026-03-29', '2026-03-29 17:14:58', 0, NULL, NULL, '192.168.68.136', '2026-03-29 09:13:58'),
(983, '6f1bb2989a4149200f460854b70b0df149fd239abb329f2761d704fed9c7953f', 32, '2026-03-29', '2026-03-29 17:15:29', 0, NULL, NULL, '192.168.68.136', '2026-03-29 09:14:29'),
(984, 'e9b843dd8adb950340017de5adfcf1a58dfe2e02283b26e03192a93ea9c832d5', 32, '2026-03-29', '2026-03-29 17:16:00', 0, NULL, NULL, '192.168.68.136', '2026-03-29 09:15:00'),
(985, 'a3b8ab0d3f844d0d7a10b14c0e2d893ba17fa7c0dea672a6291ca9696ff7b025', 32, '2026-03-29', '2026-03-29 17:16:34', 0, NULL, NULL, '192.168.68.136', '2026-03-29 09:15:34'),
(986, 'b232f8b28d9587d552f12bfec9584b96e2377fbf31bbe68d5975696f9ddbd77a', 3, '2026-03-30', '2026-03-30 09:49:23', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:48:23'),
(987, 'ca747329f251baecca39bd4dbd006cdd3514d0cfbebd8b4971984ee652eb1cb0', 3, '2026-03-30', '2026-03-30 09:49:53', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:48:53'),
(988, '9b783e0035dc500eca19b477a9ad3e043bbe1ed36b857b24956a6a872fd6c9b1', 3, '2026-03-30', '2026-03-30 09:50:23', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:49:23'),
(989, 'af6c7a7eb567349de21c9811744370000293fd6485608be666af85d3780a188e', 3, '2026-03-30', '2026-03-30 09:50:59', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:49:59'),
(990, '436385660b70de994424c84d05cdc76e9ca88b5e9a61d2d92a2dfbd6ceee79cf', 3, '2026-03-30', '2026-03-30 09:51:29', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:50:29'),
(991, '6c21eeda5eace42ac71b3d10ff800c05e9bdf0137f11bda64027018bcf8012f8', 3, '2026-03-30', '2026-03-30 09:51:59', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:50:59'),
(992, '87bd8d0b1acb0194ee99dc6217b0e735b2d6d303b38b69a56ce18b11bfecd9b6', 3, '2026-03-30', '2026-03-30 09:52:29', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:51:29'),
(993, '692aa2b63ca46e3625f3668f1905840b73b2cace5eac0ed140677cb83d0b8642', 3, '2026-03-30', '2026-03-30 09:53:29', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:52:29'),
(994, 'a3eff5bfc4e714f1bd38006d963824a157a9114ba7dcb0c8297b0b618dc99044', 3, '2026-03-30', '2026-03-30 09:54:04', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:53:04'),
(995, 'fb3725fb7a2b55d3124251299ae7f5a9464ff66404d1c4f5e91ae8fb302d44bd', 3, '2026-03-30', '2026-03-30 09:54:35', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:53:35'),
(996, '4e135279147f1cc5130da6d747ac0a9e7f2c1f83060fb729c1bfc85b489928c9', 3, '2026-03-30', '2026-03-30 09:55:09', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:54:09'),
(997, 'ee3c6afacf8528fbe803273618d96796f7da71618ae790ced2cf55d5be6ffa84', 3, '2026-03-30', '2026-03-30 09:55:42', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:54:42'),
(998, '256f90e868fb9c4ee5b931408e1ef2c48a8ea127e2b78d0aa218a410aa6a92f6', 3, '2026-03-30', '2026-03-30 09:56:13', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:55:13'),
(999, '468c5c0bb1358642b6b67677d4160a66a560876ce84b3b2ea97a3175cc98b995', 3, '2026-03-30', '2026-03-30 09:56:46', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:55:46'),
(1000, 'f8335dca872a0b8a2e383b528682ce8a5bccdcbd3db88dfc8bf93f4985fec30e', 3, '2026-03-30', '2026-03-30 09:57:16', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:56:16'),
(1001, '69d9eb60bf9602831c048075a11a0b0c90d1f7e5de64d6354e713fdac80f37f8', 3, '2026-03-30', '2026-03-30 09:58:16', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:57:16'),
(1002, '8bf62f623784ebe5dc90c1facd60a0761f3b7c221c5abcf3d5de3a7072db9543', 3, '2026-03-30', '2026-03-30 09:58:47', 0, NULL, NULL, '10.231.241.98', '2026-03-30 01:57:47');

-- --------------------------------------------------------

--
-- Table structure for table `ta_employee_shifts`
--

CREATE TABLE `ta_employee_shifts` (
  `employee_shift_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_employee_shifts`
--

INSERT INTO `ta_employee_shifts` (`employee_shift_id`, `employee_id`, `shift_id`, `effective_from`, `effective_to`, `is_active`, `created_at`, `updated_at`, `employee_id_new`) VALUES
(3, 5, 2, '2026-03-28', '2026-05-28', 1, '2026-03-28 15:01:20', '2026-03-28 15:01:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ta_flexible_schedules`
--

CREATE TABLE `ta_flexible_schedules` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
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

-- --------------------------------------------------------

--
-- Table structure for table `ta_holidays`
--

CREATE TABLE `ta_holidays` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `holiday_date` date NOT NULL,
  `is_recurring` tinyint(1) DEFAULT 0,
  `country_code` varchar(10) DEFAULT 'PH',
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT 'national',
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ta_holidays`
--

INSERT INTO `ta_holidays` (`id`, `name`, `holiday_date`, `is_recurring`, `country_code`, `description`, `category`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(37, 'New Year\'s Day', '2026-01-01', 0, 'PH', 'Bagong Taon', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(38, 'Chinese New Year', '2026-02-17', 0, 'PH', 'Chinese New Year', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(39, 'Maundy Thursday', '2026-04-02', 0, 'PH', 'Huwebes Santo', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(40, 'Good Friday', '2026-04-03', 0, 'PH', 'Biyernes Santo', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(41, 'Holy Saturday', '2026-04-04', 0, 'PH', 'Sabado de Gloria', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(42, 'Day of Valor', '2026-04-09', 0, 'PH', 'Araw ng Kagitingan', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(43, 'Labour Day', '2026-05-01', 0, 'PH', 'Araw ng Paggawa', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(44, 'Independence Day', '2026-06-12', 0, 'PH', 'Araw ng Kalayaan', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(45, 'Ninoy Aquino Day', '2026-08-21', 0, 'PH', 'Araw ng Kamatayan ni Senador Benigno Simeon \"Ninoy\" Aquino Jr.', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(46, 'National Heroes Day', '2026-08-31', 0, 'PH', 'Araw ng mga Bayani', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(47, 'All Saints\' Day Eve', '2026-10-31', 0, 'PH', 'All Saints\' Day Eve', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(48, 'All Saints\' Day', '2026-11-01', 0, 'PH', 'Araw ng mga Santo', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(49, 'Bonifacio Day', '2026-11-30', 0, 'PH', 'Araw ni Gat Andres Bonifacio', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(50, 'Feast of the Immaculate Conception of Mary', '2026-12-08', 0, 'PH', 'Kapistahan ng Immaculada Concepcion', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(51, 'Christmas Eve', '2026-12-24', 0, 'PH', 'Christmas Eve', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(52, 'Christmas Day', '2026-12-25', 0, 'PH', 'Araw ng Pasko', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(53, 'Rizal Day', '2026-12-30', 0, 'PH', 'Araw ng Kamatayan ni Dr. Jose Rizal', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(54, 'Last Day of The Year', '2026-12-31', 0, 'PH', 'Huling Araw ng Taon', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(55, 'New Year\'s Day', '2027-01-01', 0, 'PH', 'Bagong Taon', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(56, 'Chinese New Year', '2027-02-06', 0, 'PH', 'Chinese New Year', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(57, 'Maundy Thursday', '2027-03-25', 0, 'PH', 'Huwebes Santo', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(58, 'Good Friday', '2027-03-26', 0, 'PH', 'Biyernes Santo', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(59, 'Holy Saturday', '2027-03-27', 0, 'PH', 'Sabado de Gloria', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(60, 'Day of Valor', '2027-04-09', 0, 'PH', 'Araw ng Kagitingan', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(61, 'Labour Day', '2027-05-01', 0, 'PH', 'Araw ng Paggawa', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(62, 'Independence Day', '2027-06-12', 0, 'PH', 'Araw ng Kalayaan', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(63, 'Ninoy Aquino Day', '2027-08-21', 0, 'PH', 'Araw ng Kamatayan ni Senador Benigno Simeon \"Ninoy\" Aquino Jr.', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(64, 'National Heroes Day', '2027-08-30', 0, 'PH', 'Araw ng mga Bayani', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(65, 'All Saints\' Day Eve', '2027-10-31', 0, 'PH', 'All Saints\' Day Eve', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(66, 'All Saints\' Day', '2027-11-01', 0, 'PH', 'Araw ng mga Santo', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(67, 'Bonifacio Day', '2027-11-30', 0, 'PH', 'Araw ni Gat Andres Bonifacio', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(68, 'Feast of the Immaculate Conception of Mary', '2027-12-08', 0, 'PH', 'Kapistahan ng Immaculada Concepcion', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(69, 'Christmas Eve', '2027-12-24', 0, 'PH', 'Christmas Eve', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(70, 'Christmas Day', '2027-12-25', 0, 'PH', 'Araw ng Pasko', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(71, 'Rizal Day', '2027-12-30', 0, 'PH', 'Araw ng Kamatayan ni Dr. Jose Rizal', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28'),
(72, 'Last Day of The Year', '2027-12-31', 0, 'PH', 'Huling Araw ng Taon', 'national', 1, 3, '2026-03-20 07:41:28', '2026-03-20 07:41:28');

-- --------------------------------------------------------

--
-- Table structure for table `ta_holiday_sync_log`
--

CREATE TABLE `ta_holiday_sync_log` (
  `id` int(11) NOT NULL,
  `sync_date` date DEFAULT NULL,
  `total_holidays` int(11) DEFAULT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  `last_synced` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ta_holiday_sync_log`
--

INSERT INTO `ta_holiday_sync_log` (`id`, `sync_date`, `total_holidays`, `country_code`, `last_synced`) VALUES
(1, '2026-03-20', 36, 'PH', '2026-03-20 07:41:28');

-- --------------------------------------------------------

--
-- Table structure for table `ta_leave_balances`
--

CREATE TABLE `ta_leave_balances` (
  `leave_balance_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `opening_balance` decimal(5,2) DEFAULT 0.00,
  `used_balance` decimal(5,2) DEFAULT 0.00,
  `remaining_balance` decimal(5,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ta_leave_daily_records`
--

CREATE TABLE `ta_leave_daily_records` (
  `daily_record_id` int(11) NOT NULL,
  `leave_request_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_date` date NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `is_holiday` tinyint(1) DEFAULT 0,
  `balance_deducted` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ta_leave_requests`
--

CREATE TABLE `ta_leave_requests` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `date_submitted` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `details` varchar(255) DEFAULT NULL,
  `supporting_document` varchar(255) DEFAULT NULL,
  `reject_reason` varchar(255) DEFAULT NULL,
  `employee_id_new` int(11) DEFAULT NULL,
  `balance_deducted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_leave_requests`
--

INSERT INTO `ta_leave_requests` (`id`, `employee_id`, `leave_type_id`, `start_date`, `end_date`, `date_submitted`, `updated_at`, `status`, `details`, `supporting_document`, `reject_reason`, `employee_id_new`, `balance_deducted`) VALUES
(3, 1, 4, '2026-03-29', '2026-04-01', '2026-03-29 08:42:09', '2026-03-29 08:55:46', '', 'wdawdaw', NULL, 'ok', NULL, 0);

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
-- Table structure for table `ta_overtime_frequency`
--

CREATE TABLE `ta_overtime_frequency` (
  `frequency_id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `month_year` varchar(7) NOT NULL,
  `overtime_instances` int(11) DEFAULT 0,
  `total_overtime_hours` decimal(8,2) DEFAULT 0.00,
  `average_overtime_per_instance` decimal(5,2) DEFAULT 0.00,
  `max_overtime_in_single_day` decimal(5,2) DEFAULT 0.00,
  `overtime_frequency_rating` enum('LOW','MODERATE','HIGH','CRITICAL') DEFAULT 'LOW',
  `approved_overtime_hours` decimal(8,2) DEFAULT 0.00,
  `unapproved_overtime_hours` decimal(8,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ta_overtime_tracking`
--

CREATE TABLE `ta_overtime_tracking` (
  `tracking_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `attendance_id` int(11) DEFAULT NULL,
  `overtime_date` date NOT NULL,
  `overtime_hours` decimal(5,2) NOT NULL,
  `overtime_minutes` int(11) NOT NULL,
  `reason_category` enum('PROJECT_DEADLINE','STAFFING_SHORTAGE','WORKLOAD_HEAVY','SHIFT_CHANGE','VOLUNTARY','OTHER') DEFAULT 'OTHER',
  `reason_notes` text DEFAULT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `approved_by` int(11) DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ta_punctuality_scores`
--

CREATE TABLE `ta_punctuality_scores` (
  `score_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `month_year` varchar(7) NOT NULL,
  `total_late_incidents` int(11) DEFAULT 0,
  `total_late_minutes` int(11) DEFAULT 0,
  `average_late_minutes` decimal(5,2) DEFAULT 0.00,
  `punctuality_score` decimal(5,2) DEFAULT 100.00,
  `punctuality_grade` enum('A','B','C','D','F') DEFAULT 'A',
  `score_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`score_breakdown`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(2, 'Morning Shift', '06:00:00', '17:00:00', 60, '\r\n\r\n', 1, '2026-03-20 04:03:28', '2026-03-20 04:03:28');

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
(4, 'hr_employee', '$2y$10$qnK7VSrgqKp3JGa0VNEryOqXrGrV2AYrNEYpVklYEZDJy3Gq8xvEW', 'someone', 'employee', 'light', '2026-03-07 02:47:55', NULL),
(5, 'hr_compliance', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'comply', 'compliance', 'light', '2026-03-07 02:48:19', NULL),
(6, 'hr_workforce', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'force', 'workforce', 'light', '2026-03-07 02:48:43', NULL),
(7, 'hr_learning', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'learn', 'learning', 'light', '2026-03-07 02:49:22', NULL),
(8, 'hr_performance', '$2y$10$/aFKLVK.xloqiY31X4T.dOPKY2AnnkrpaME4f2z.l4LhQurY1/Zzy', 'Perform', 'performance', 'light', '2026-03-07 02:49:46', 'user_8.jpg'),
(9, 'hr_engagement', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'engage', 'engagement_relations', 'light', '2026-03-07 02:50:37', NULL),
(10, 'hr_exit', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'exit', 'exit', 'light', '2026-03-07 02:51:04', NULL),
(29, 'john.doe', '$2y$10$j49xYMvEuEk1fHANWbxRKOYYsIxmAQg9d6qN3MwBbKiDq1Yl5hpvq', 'John Doe', 'employee', 'light', '2026-03-27 11:39:00', NULL),
(30, 'jane.smith', '$2y$10$j49xYMvEuEk1fHANWbxRKOYYsIxmAQg9d6qN3MwBbKiDq1Yl5hpvq', 'Jane Smith', 'employee', 'light', '2026-03-27 11:39:00', NULL),
(31, 'mike.johnson', '$2y$10$j49xYMvEuEk1fHANWbxRKOYYsIxmAQg9d6qN3MwBbKiDq1Yl5hpvq', 'Mike Johnson', 'employee', 'light', '2026-03-27 11:39:00', NULL),
(32, 'sarah.williams', '$2y$10$j49xYMvEuEk1fHANWbxRKOYYsIxmAQg9d6qN3MwBbKiDq1Yl5hpvq', 'Sarah Williams', 'employee', 'light', '2026-03-27 11:39:00', NULL),
(33, 'david.brown', '$2y$10$j49xYMvEuEk1fHANWbxRKOYYsIxmAQg9d6qN3MwBbKiDq1Yl5hpvq', 'David Brown', 'employee', 'light', '2026-03-27 11:39:00', NULL),
(34, 'emily.davis', '$2y$10$j49xYMvEuEk1fHANWbxRKOYYsIxmAQg9d6qN3MwBbKiDq1Yl5hpvq', 'Emily Davis', 'employee', 'light', '2026-03-27 11:39:00', NULL),
(35, 'robert.wilson', '$2y$10$j49xYMvEuEk1fHANWbxRKOYYsIxmAQg9d6qN3MwBbKiDq1Yl5hpvq', 'Robert Wilson', 'employee', 'light', '2026-03-27 11:39:00', NULL),
(36, 'jessica.martinez', '$2y$10$j49xYMvEuEk1fHANWbxRKOYYsIxmAQg9d6qN3MwBbKiDq1Yl5hpvq', 'Jessica Martinez', 'employee', 'light', '2026-03-27 11:39:00', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_at_risk_employees_summary`
-- (See below for the actual view)
--
CREATE TABLE `vw_at_risk_employees_summary` (
`risk_level` enum('high','medium','low')
,`count` bigint(21)
,`avg_risk_score` decimal(6,2)
,`percentage` decimal(26,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_current_employees_by_dept`
-- (See below for the actual view)
--
CREATE TABLE `vw_current_employees_by_dept` (
`department` varchar(100)
,`employee_count` bigint(21)
,`avg_performance_score` decimal(6,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `wfa_age_distribution`
--

CREATE TABLE `wfa_age_distribution` (
  `id` int(11) NOT NULL,
  `metric_date` date NOT NULL,
  `age_group` varchar(50) NOT NULL COMMENT '18-25, 26-35, 36-45, 46-55, 56+',
  `employee_count` int(11) DEFAULT 0,
  `percentage` decimal(5,2) DEFAULT 0.00,
  `average_salary` decimal(12,2) DEFAULT 0.00,
  `average_performance_score` decimal(5,2) DEFAULT 0.00,
  `department_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`department_breakdown`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_attrition_tracking`
--

CREATE TABLE `wfa_attrition_tracking` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `separation_date` date NOT NULL,
  `separation_type` enum('resigned','retired','terminated','other') DEFAULT 'resigned',
  `department` varchar(100) DEFAULT NULL,
  `tenure_years` decimal(5,2) DEFAULT NULL,
  `reason_for_leaving` text DEFAULT NULL,
  `exit_interview_completed` tinyint(1) DEFAULT 0,
  `rehire_eligible` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_attrition_tracking_backup`
--

CREATE TABLE `wfa_attrition_tracking_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `employee_id` varchar(50) NOT NULL,
  `separation_date` date NOT NULL,
  `separation_type` enum('resigned','retired','terminated','other') DEFAULT 'resigned',
  `department` varchar(100) DEFAULT NULL,
  `tenure_years` decimal(5,2) DEFAULT NULL,
  `reason_for_leaving` text DEFAULT NULL,
  `exit_interview_completed` tinyint(1) DEFAULT 0,
  `rehire_eligible` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `wfa_at_risk_employees_summary`
-- (See below for the actual view)
--
CREATE TABLE `wfa_at_risk_employees_summary` (
`risk_level` enum('high','medium','low')
,`count` bigint(21)
,`avg_risk_score` decimal(6,2)
,`percentage` decimal(26,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `wfa_audit_log`
--

CREATE TABLE `wfa_audit_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL COMMENT 'view_report, generate_report, export_data, update_filter',
  `resource_type` varchar(50) DEFAULT NULL COMMENT 'report, filter, metric',
  `resource_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_compensation_analysis`
--

CREATE TABLE `wfa_compensation_analysis` (
  `id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `current_avg_salary` decimal(12,2) DEFAULT 0.00,
  `market_median_salary` decimal(12,2) DEFAULT 0.00,
  `salary_competitiveness_ratio` decimal(5,2) DEFAULT NULL COMMENT 'Current/Market %',
  `employee_count` int(11) DEFAULT 0,
  `salary_range_min` decimal(12,2) DEFAULT NULL,
  `salary_range_max` decimal(12,2) DEFAULT NULL,
  `recommended_adjustment` decimal(12,2) DEFAULT NULL,
  `last_market_review` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `wfa_current_employees_by_dept`
-- (See below for the actual view)
--
CREATE TABLE `wfa_current_employees_by_dept` (
`department` varchar(100)
,`employee_count` bigint(21)
,`avg_performance_score` decimal(6,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `wfa_custom_filters`
--

CREATE TABLE `wfa_custom_filters` (
  `id` int(11) NOT NULL,
  `filter_name` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `filter_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Department, employment type, date range' CHECK (json_valid(`filter_config`)),
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_department_analytics`
--

CREATE TABLE `wfa_department_analytics` (
  `id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `employee_count` int(11) DEFAULT 0,
  `average_salary` decimal(12,2) DEFAULT 0.00,
  `average_performance_score` decimal(5,2) DEFAULT 0.00,
  `headcount_target` int(11) DEFAULT NULL,
  `vacancy_count` int(11) DEFAULT 0,
  `average_tenure_years` decimal(5,2) DEFAULT 0.00,
  `metric_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `wfa_department_diversity`
-- (See below for the actual view)
--
CREATE TABLE `wfa_department_diversity` (
`metric_date` date
,`diversity_category` varchar(50)
,`category_value` varchar(100)
,`employee_count` int(11)
,`percentage` decimal(5,2)
,`average_salary` decimal(12,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `wfa_diversity_metrics`
--

CREATE TABLE `wfa_diversity_metrics` (
  `id` int(11) NOT NULL,
  `metric_date` date NOT NULL,
  `diversity_category` varchar(50) NOT NULL COMMENT 'gender, age_group, department',
  `category_value` varchar(100) NOT NULL COMMENT 'Male/Female/Other, 18-25/26-35/etc, Department Name',
  `employee_count` int(11) DEFAULT 0,
  `percentage` decimal(5,2) DEFAULT 0.00,
  `average_salary` decimal(12,2) DEFAULT 0.00,
  `average_performance` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_employee_metrics`
--

CREATE TABLE `wfa_employee_metrics` (
  `id` int(11) NOT NULL,
  `metric_date` date NOT NULL,
  `total_employees` int(11) DEFAULT 0,
  `total_teachers` int(11) DEFAULT 0,
  `total_staff` int(11) DEFAULT 0,
  `new_hires_this_year` int(11) DEFAULT 0,
  `average_salary` decimal(12,2) DEFAULT 0.00,
  `average_performance_score` decimal(5,2) DEFAULT 0.00,
  `total_departments` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_gender_distribution`
--

CREATE TABLE `wfa_gender_distribution` (
  `id` int(11) NOT NULL,
  `metric_date` date NOT NULL,
  `gender` varchar(50) NOT NULL COMMENT 'Male, Female, Other',
  `employee_count` int(11) DEFAULT 0,
  `percentage` decimal(5,2) DEFAULT 0.00,
  `average_salary` decimal(12,2) DEFAULT 0.00,
  `average_performance_score` decimal(5,2) DEFAULT 0.00,
  `department_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`department_breakdown`)),
  `position_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`position_breakdown`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_headcount_planning`
--

CREATE TABLE `wfa_headcount_planning` (
  `id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `fiscal_year` year(4) NOT NULL,
  `planned_headcount` int(11) DEFAULT 0,
  `actual_headcount` int(11) DEFAULT 0,
  `variance` int(11) DEFAULT 0,
  `planned_salary_budget` decimal(15,2) DEFAULT 0.00,
  `actual_salary_budget` decimal(15,2) DEFAULT 0.00,
  `budget_variance` decimal(15,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_insights_history`
--

CREATE TABLE `wfa_insights_history` (
  `insight_id` int(11) NOT NULL,
  `insight_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_kpis` int(11) DEFAULT NULL,
  `critical_insights` int(11) DEFAULT NULL,
  `warning_insights` int(11) DEFAULT NULL,
  `info_insights` int(11) DEFAULT NULL,
  `insights_data` longtext DEFAULT NULL,
  `action_taken` varchar(500) DEFAULT NULL,
  `resolved_by` varchar(100) DEFAULT NULL,
  `resolution_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_monthly_attrition`
--

CREATE TABLE `wfa_monthly_attrition` (
  `id` int(11) NOT NULL,
  `year_month` date NOT NULL,
  `total_separations` int(11) DEFAULT 0,
  `voluntary_separations` int(11) DEFAULT 0,
  `involuntary_separations` int(11) DEFAULT 0,
  `attrition_rate_percent` decimal(5,2) DEFAULT 0.00,
  `average_tenure_departing` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_performance_distribution`
--

CREATE TABLE `wfa_performance_distribution` (
  `id` int(11) NOT NULL,
  `metric_date` date NOT NULL,
  `performance_level` varchar(50) NOT NULL COMMENT 'Excellent, Good, Average, Below Average, Poor',
  `score_range_min` decimal(5,2) DEFAULT NULL,
  `score_range_max` decimal(5,2) DEFAULT NULL,
  `employee_count` int(11) DEFAULT 0,
  `percentage` decimal(5,2) DEFAULT 0.00,
  `department_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Department distribution' CHECK (json_valid(`department_breakdown`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_reports`
--

CREATE TABLE `wfa_reports` (
  `id` int(11) NOT NULL,
  `report_name` varchar(255) NOT NULL,
  `report_type` varchar(50) NOT NULL COMMENT 'dashboard, attrition, diversity, performance, salary, custom',
  `report_date` date NOT NULL,
  `generated_by` int(11) DEFAULT NULL,
  `filters_applied` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Department, date range, etc.' CHECK (json_valid(`filters_applied`)),
  `report_data` longtext DEFAULT NULL COMMENT 'JSON data snapshot',
  `file_path` varchar(255) DEFAULT NULL COMMENT 'Path to exported file if any',
  `export_format` varchar(20) DEFAULT NULL COMMENT 'CSV, PDF, Excel',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_report_history`
--

CREATE TABLE `wfa_report_history` (
  `report_id` int(11) NOT NULL,
  `report_type` varchar(50) NOT NULL,
  `report_title` varchar(255) DEFAULT NULL,
  `report_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(500) DEFAULT NULL,
  `file_format` varchar(20) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `is_favorite` tinyint(1) DEFAULT 0,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_report_snapshots`
--

CREATE TABLE `wfa_report_snapshots` (
  `snapshot_id` int(11) NOT NULL,
  `snapshot_type` varchar(50) NOT NULL,
  `snapshot_name` varchar(255) NOT NULL,
  `snapshot_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_employees` int(11) DEFAULT NULL,
  `active_employees` int(11) DEFAULT NULL,
  `inactive_employees` int(11) DEFAULT NULL,
  `attrition_rate` decimal(5,2) DEFAULT NULL,
  `average_tenure` decimal(5,1) DEFAULT NULL,
  `department_count` int(11) DEFAULT NULL,
  `position_count` int(11) DEFAULT NULL,
  `snapshot_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`snapshot_data`)),
  `created_by` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_risk_assessment`
--

CREATE TABLE `wfa_risk_assessment` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `risk_level` enum('high','medium','low') DEFAULT 'low',
  `risk_score` decimal(5,2) DEFAULT 0.00 COMMENT '0-100 score',
  `risk_factors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of risk factors' CHECK (json_valid(`risk_factors`)),
  `low_performance_flag` tinyint(1) DEFAULT 0 COMMENT 'Performance < 3.0',
  `high_absence_flag` tinyint(1) DEFAULT 0 COMMENT 'Absence days > 15',
  `low_engagement_flag` tinyint(1) DEFAULT 0,
  `recent_complaints_flag` tinyint(1) DEFAULT 0,
  `performance_score` decimal(5,2) DEFAULT NULL,
  `absence_days` int(11) DEFAULT 0,
  `tenure_months` int(11) DEFAULT 0,
  `last_assessment_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_risk_assessment_backup`
--

CREATE TABLE `wfa_risk_assessment_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `employee_id` varchar(50) NOT NULL,
  `risk_level` enum('high','medium','low') DEFAULT 'low',
  `risk_score` decimal(5,2) DEFAULT 0.00 COMMENT '0-100 score',
  `risk_factors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of risk factors' CHECK (json_valid(`risk_factors`)),
  `low_performance_flag` tinyint(1) DEFAULT 0 COMMENT 'Performance < 3.0',
  `high_absence_flag` tinyint(1) DEFAULT 0 COMMENT 'Absence days > 15',
  `low_engagement_flag` tinyint(1) DEFAULT 0,
  `recent_complaints_flag` tinyint(1) DEFAULT 0,
  `performance_score` decimal(5,2) DEFAULT NULL,
  `absence_days` int(11) DEFAULT 0,
  `tenure_months` int(11) DEFAULT 0,
  `last_assessment_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wfa_risk_assessment_backup`
--

INSERT INTO `wfa_risk_assessment_backup` (`id`, `employee_id`, `risk_level`, `risk_score`, `risk_factors`, `low_performance_flag`, `high_absence_flag`, `low_engagement_flag`, `recent_complaints_flag`, `performance_score`, `absence_days`, `tenure_months`, `last_assessment_date`, `notes`, `created_at`, `updated_at`, `employee_id_new`) VALUES
(1, 'EMP001', 'low', 25.50, '[\"none\"]', 0, 0, 0, 0, 4.50, 2, 36, NULL, NULL, '2026-03-21 09:23:29', '2026-03-22 07:00:21', 1),
(2, 'EMP002', 'medium', 55.75, '[\"high_absence\", \"low_engagement\"]', 0, 1, 0, 0, 3.50, 18, 24, NULL, NULL, '2026-03-21 09:23:29', '2026-03-22 07:00:21', 2),
(3, 'EMP003', 'high', 78.25, '[\"low_performance\", \"high_absence\", \"tenure\"]', 1, 1, 0, 0, 2.50, 20, 6, NULL, NULL, '2026-03-21 09:23:29', '2026-03-22 07:00:21', 3);

-- --------------------------------------------------------

--
-- Table structure for table `wfa_salary_statistics`
--

CREATE TABLE `wfa_salary_statistics` (
  `id` int(11) NOT NULL,
  `metric_date` date NOT NULL,
  `department` varchar(100) NOT NULL,
  `employee_count` int(11) DEFAULT 0,
  `min_salary` decimal(12,2) DEFAULT 0.00,
  `max_salary` decimal(12,2) DEFAULT 0.00,
  `average_salary` decimal(12,2) DEFAULT 0.00,
  `median_salary` decimal(12,2) DEFAULT 0.00,
  `total_payroll` decimal(15,2) DEFAULT 0.00,
  `salary_variance` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_skill_gap_analysis`
--

CREATE TABLE `wfa_skill_gap_analysis` (
  `id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `skill_name` varchar(255) NOT NULL,
  `required_proficiency` varchar(50) DEFAULT NULL COMMENT 'Basic, Intermediate, Advanced, Expert',
  `current_proficiency_avg` varchar(50) DEFAULT NULL,
  `employees_with_skill` int(11) DEFAULT 0,
  `employees_needing_training` int(11) DEFAULT 0,
  `skill_gap_percentage` decimal(5,2) DEFAULT 0.00,
  `priority_level` enum('critical','high','medium','low') DEFAULT 'medium',
  `training_recommendations` text DEFAULT NULL,
  `last_assessed` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wfa_tenure_analysis`
--

CREATE TABLE `wfa_tenure_analysis` (
  `id` int(11) NOT NULL,
  `metric_date` date NOT NULL,
  `tenure_bracket` varchar(50) NOT NULL COMMENT '0-1yr, 1-3yr, 3-5yr, 5-10yr, 10+ yr',
  `employee_count` int(11) DEFAULT 0,
  `percentage` decimal(5,2) DEFAULT 0.00,
  `average_salary` decimal(12,2) DEFAULT 0.00,
  `average_performance_score` decimal(5,2) DEFAULT 0.00,
  `department_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`department_breakdown`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure for view `vw_at_risk_employees_summary`
--
DROP TABLE IF EXISTS `vw_at_risk_employees_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_at_risk_employees_summary`  AS SELECT `wfa_risk_assessment`.`risk_level` AS `risk_level`, count(0) AS `count`, round(avg(`wfa_risk_assessment`.`risk_score`),2) AS `avg_risk_score`, round(count(0) * 100.0 / (select count(0) from `wfa_risk_assessment` where cast(`wfa_risk_assessment`.`updated_at` as date) = curdate()),2) AS `percentage` FROM `wfa_risk_assessment` WHERE cast(`wfa_risk_assessment`.`updated_at` as date) = curdate() GROUP BY `wfa_risk_assessment`.`risk_level` ;

-- --------------------------------------------------------

--
-- Structure for view `vw_current_employees_by_dept`
--
DROP TABLE IF EXISTS `vw_current_employees_by_dept`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_current_employees_by_dept`  AS SELECT `e`.`department` AS `department`, count(distinct `e`.`employee_id`) AS `employee_count`, round(avg(cast(`pr`.`rating` as decimal(5,2))),2) AS `avg_performance_score` FROM (`employees` `e` left join `performance_reviews` `pr` on(`e`.`employee_id` = `pr`.`employee_id`)) WHERE `e`.`employment_status` = 'Active' GROUP BY `e`.`department` ;

-- --------------------------------------------------------

--
-- Structure for view `wfa_at_risk_employees_summary`
--
DROP TABLE IF EXISTS `wfa_at_risk_employees_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `wfa_at_risk_employees_summary`  AS SELECT `wfa_risk_assessment`.`risk_level` AS `risk_level`, count(0) AS `count`, round(avg(`wfa_risk_assessment`.`risk_score`),2) AS `avg_risk_score`, round(count(0) * 100.0 / (select count(0) from `wfa_risk_assessment` where cast(`wfa_risk_assessment`.`updated_at` as date) = curdate()),2) AS `percentage` FROM `wfa_risk_assessment` WHERE cast(`wfa_risk_assessment`.`updated_at` as date) = curdate() GROUP BY `wfa_risk_assessment`.`risk_level` ;

-- --------------------------------------------------------

--
-- Structure for view `wfa_current_employees_by_dept`
--
DROP TABLE IF EXISTS `wfa_current_employees_by_dept`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `wfa_current_employees_by_dept`  AS SELECT `e`.`department` AS `department`, count(distinct `e`.`employee_id`) AS `employee_count`, round(avg(cast(`pr`.`rating` as decimal(5,2))),2) AS `avg_performance_score` FROM (`employees` `e` left join `performance_reviews` `pr` on(`e`.`employee_id` = `pr`.`employee_id`)) WHERE `e`.`employment_status` = 'Active' GROUP BY `e`.`department` ;

-- --------------------------------------------------------

--
-- Structure for view `wfa_department_diversity`
--
DROP TABLE IF EXISTS `wfa_department_diversity`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `wfa_department_diversity`  AS SELECT `wfa_diversity_metrics`.`metric_date` AS `metric_date`, `wfa_diversity_metrics`.`diversity_category` AS `diversity_category`, `wfa_diversity_metrics`.`category_value` AS `category_value`, `wfa_diversity_metrics`.`employee_count` AS `employee_count`, `wfa_diversity_metrics`.`percentage` AS `percentage`, `wfa_diversity_metrics`.`average_salary` AS `average_salary` FROM `wfa_diversity_metrics` WHERE `wfa_diversity_metrics`.`diversity_category` = 'gender' ;

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
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `employees1`
--
ALTER TABLE `employees1`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees_new`
--
ALTER TABLE `employees_new`
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD UNIQUE KEY `employee_id_2` (`employee_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `employee_id_new` (`employee_id_new`),
  ADD KEY `fk_emp_department_id` (`department_id`),
  ADD KEY `fk_emp_position_id` (`position_id`),
  ADD KEY `fk_emp_employment_type_id` (`employment_type_id`),
  ADD KEY `fk_emp_location_id` (`location_id`),
  ADD KEY `fk_emp_salary_grade_id` (`salary_grade_id`),
  ADD KEY `idx_emp_gender` (`gender`),
  ADD KEY `idx_emp_dob` (`date_of_birth`),
  ADD KEY `idx_emp_pan_number` (`pan_number`),
  ADD KEY `fk_emp_manager_id` (`manager_id`),
  ADD KEY `idx_emp_base_salary` (`base_salary`),
  ADD KEY `idx_emp_probation_end` (`probation_end_date`),
  ADD KEY `idx_emp_retirement_eligible` (`retirement_eligible_date`),
  ADD KEY `idx_emp_employee_status` (`employee_status`),
  ADD KEY `idx_emp_dept_status` (`department_id`,`employee_status`),
  ADD KEY `idx_emp_position_status` (`position_id`,`employee_status`),
  ADD KEY `idx_emp_manager_status` (`manager_id`,`employee_status`),
  ADD KEY `idx_emp_location_status` (`location_id`,`employee_status`),
  ADD KEY `idx_emp_employment_type_status` (`employment_type_id`,`employee_status`),
  ADD KEY `idx_emp_base_salary_grade` (`base_salary`,`salary_grade_id`),
  ADD KEY `idx_emp_age_group` (`age_group`),
  ADD KEY `idx_emp_gender_age_dept` (`gender`,`age_group`,`department_id`);

--
-- Indexes for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_employee_accounts_employees` (`employee_id`);

--
-- Indexes for table `employee_id_mapping`
--
ALTER TABLE `employee_id_mapping`
  ADD PRIMARY KEY (`old_id`),
  ADD UNIQUE KEY `new_id` (`new_id`);

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
-- Indexes for table `ta_absence_late_policies`
--
ALTER TABLE `ta_absence_late_policies`
  ADD PRIMARY KEY (`policy_id`);

--
-- Indexes for table `ta_absence_late_records`
--
ALTER TABLE `ta_absence_late_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `idx_employee_date` (`employee_id`,`absence_date`),
  ADD KEY `idx_excuse_status` (`excuse_status`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_leave_request` (`leave_request_id`);

--
-- Indexes for table `ta_absence_late_thresholds`
--
ALTER TABLE `ta_absence_late_thresholds`
  ADD PRIMARY KEY (`threshold_id`),
  ADD UNIQUE KEY `unique_employee_month` (`employee_id`,`month_year`),
  ADD KEY `idx_warning_level` (`warning_level`);

--
-- Indexes for table `ta_attendance`
--
ALTER TABLE `ta_attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`,`attendance_date`),
  ADD KEY `fk_attendance_leave_request` (`leave_request_id`),
  ADD KEY `idx_employee_date_status` (`employee_id`,`attendance_date`,`status`);

--
-- Indexes for table `ta_attendance_metrics`
--
ALTER TABLE `ta_attendance_metrics`
  ADD PRIMARY KEY (`metric_id`),
  ADD UNIQUE KEY `unique_employee_month` (`employee_id`,`month_year`),
  ADD KEY `idx_overall_performance` (`overall_performance_score`);

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
  ADD KEY `idx_date` (`schedule_date`),
  ADD KEY `idx_employee` (`employee_id`);

--
-- Indexes for table `ta_holidays`
--
ALTER TABLE `ta_holidays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date` (`holiday_date`),
  ADD KEY `idx_recurring` (`is_recurring`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `ta_holiday_sync_log`
--
ALTER TABLE `ta_holiday_sync_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_sync` (`sync_date`,`country_code`);

--
-- Indexes for table `ta_leave_balances`
--
ALTER TABLE `ta_leave_balances`
  ADD PRIMARY KEY (`leave_balance_id`),
  ADD UNIQUE KEY `unique_employee_leave_year` (`employee_id`,`leave_type_id`,`year`),
  ADD KEY `idx_employee_year` (`employee_id`,`year`),
  ADD KEY `idx_leave_type_year` (`leave_type_id`,`year`);

--
-- Indexes for table `ta_leave_daily_records`
--
ALTER TABLE `ta_leave_daily_records`
  ADD PRIMARY KEY (`daily_record_id`),
  ADD UNIQUE KEY `unique_daily_leave` (`leave_request_id`,`leave_date`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `leave_type_id` (`leave_type_id`);

--
-- Indexes for table `ta_leave_requests`
--
ALTER TABLE `ta_leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_leave_type` (`leave_type_id`),
  ADD KEY `fk_leave_employee` (`employee_id`);

--
-- Indexes for table `ta_leave_types`
--
ALTER TABLE `ta_leave_types`
  ADD PRIMARY KEY (`leave_type_id`);

--
-- Indexes for table `ta_overtime_frequency`
--
ALTER TABLE `ta_overtime_frequency`
  ADD PRIMARY KEY (`frequency_id`),
  ADD UNIQUE KEY `unique_employee_month` (`employee_id`,`month_year`),
  ADD KEY `idx_frequency_rating` (`overtime_frequency_rating`);

--
-- Indexes for table `ta_overtime_tracking`
--
ALTER TABLE `ta_overtime_tracking`
  ADD PRIMARY KEY (`tracking_id`),
  ADD KEY `idx_employee_date` (`employee_id`,`overtime_date`),
  ADD KEY `idx_overtime_approved` (`approved`);

--
-- Indexes for table `ta_punctuality_scores`
--
ALTER TABLE `ta_punctuality_scores`
  ADD PRIMARY KEY (`score_id`),
  ADD UNIQUE KEY `unique_employee_month` (`employee_id`,`month_year`),
  ADD KEY `idx_punctuality_score` (`punctuality_score`),
  ADD KEY `idx_punctuality_grade` (`punctuality_grade`);

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
-- Indexes for table `wfa_age_distribution`
--
ALTER TABLE `wfa_age_distribution`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_age_distribution` (`metric_date`,`age_group`),
  ADD KEY `idx_metric_date` (`metric_date`),
  ADD KEY `idx_age_group` (`age_group`);

--
-- Indexes for table `wfa_attrition_tracking`
--
ALTER TABLE `wfa_attrition_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_separation_date` (`separation_date`),
  ADD KEY `idx_separation_type` (`separation_type`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_wfa_attrition_date_type` (`separation_date`,`separation_type`);

--
-- Indexes for table `wfa_audit_log`
--
ALTER TABLE `wfa_audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `wfa_compensation_analysis`
--
ALTER TABLE `wfa_compensation_analysis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_compensation` (`department`,`position`),
  ADD KEY `idx_department` (`department`),
  ADD KEY `idx_position` (`position`);

--
-- Indexes for table `wfa_custom_filters`
--
ALTER TABLE `wfa_custom_filters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_filter_name` (`filter_name`);

--
-- Indexes for table `wfa_department_analytics`
--
ALTER TABLE `wfa_department_analytics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_dept_date` (`department`,`metric_date`),
  ADD KEY `idx_department` (`department`),
  ADD KEY `idx_metric_date` (`metric_date`),
  ADD KEY `idx_wfa_dept_metrics_date` (`metric_date`,`department`);

--
-- Indexes for table `wfa_diversity_metrics`
--
ALTER TABLE `wfa_diversity_metrics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_diversity` (`metric_date`,`diversity_category`,`category_value`),
  ADD KEY `idx_metric_date` (`metric_date`),
  ADD KEY `idx_diversity_category` (`diversity_category`);

--
-- Indexes for table `wfa_employee_metrics`
--
ALTER TABLE `wfa_employee_metrics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_metric_date` (`metric_date`),
  ADD KEY `idx_metric_date` (`metric_date`),
  ADD KEY `idx_wfa_metrics_date_dept` (`metric_date`);

--
-- Indexes for table `wfa_gender_distribution`
--
ALTER TABLE `wfa_gender_distribution`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_gender_distribution` (`metric_date`,`gender`),
  ADD KEY `idx_metric_date` (`metric_date`),
  ADD KEY `idx_gender` (`gender`);

--
-- Indexes for table `wfa_headcount_planning`
--
ALTER TABLE `wfa_headcount_planning`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_headcount_plan` (`department`,`fiscal_year`),
  ADD KEY `idx_department` (`department`),
  ADD KEY `idx_fiscal_year` (`fiscal_year`);

--
-- Indexes for table `wfa_insights_history`
--
ALTER TABLE `wfa_insights_history`
  ADD PRIMARY KEY (`insight_id`),
  ADD KEY `idx_insight_date` (`insight_date`);

--
-- Indexes for table `wfa_monthly_attrition`
--
ALTER TABLE `wfa_monthly_attrition`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_year_month` (`year_month`),
  ADD KEY `idx_year_month` (`year_month`);

--
-- Indexes for table `wfa_performance_distribution`
--
ALTER TABLE `wfa_performance_distribution`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_performance_dist` (`metric_date`,`performance_level`),
  ADD KEY `idx_metric_date` (`metric_date`),
  ADD KEY `idx_performance_level` (`performance_level`);

--
-- Indexes for table `wfa_reports`
--
ALTER TABLE `wfa_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_report_type` (`report_type`),
  ADD KEY `idx_report_date` (`report_date`),
  ADD KEY `idx_generated_by` (`generated_by`);

--
-- Indexes for table `wfa_report_history`
--
ALTER TABLE `wfa_report_history`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `idx_report_type` (`report_type`),
  ADD KEY `idx_report_date` (`report_date`);

--
-- Indexes for table `wfa_report_snapshots`
--
ALTER TABLE `wfa_report_snapshots`
  ADD PRIMARY KEY (`snapshot_id`),
  ADD KEY `idx_snapshot_type` (`snapshot_type`),
  ADD KEY `idx_snapshot_date` (`snapshot_date`);

--
-- Indexes for table `wfa_risk_assessment`
--
ALTER TABLE `wfa_risk_assessment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_risk_level` (`risk_level`),
  ADD KEY `idx_risk_score` (`risk_score`),
  ADD KEY `idx_wfa_risk_assessment_level_date` (`risk_level`,`updated_at`);

--
-- Indexes for table `wfa_salary_statistics`
--
ALTER TABLE `wfa_salary_statistics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_salary_stats` (`metric_date`,`department`),
  ADD KEY `idx_metric_date` (`metric_date`),
  ADD KEY `idx_department` (`department`);

--
-- Indexes for table `wfa_skill_gap_analysis`
--
ALTER TABLE `wfa_skill_gap_analysis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_skill_gap` (`department`,`skill_name`),
  ADD KEY `idx_department` (`department`),
  ADD KEY `idx_skill_name` (`skill_name`),
  ADD KEY `idx_priority_level` (`priority_level`);

--
-- Indexes for table `wfa_tenure_analysis`
--
ALTER TABLE `wfa_tenure_analysis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_tenure_analysis` (`metric_date`,`tenure_bracket`),
  ADD KEY `idx_metric_date` (`metric_date`),
  ADD KEY `idx_tenure_bracket` (`tenure_bracket`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `employees1`
--
ALTER TABLE `employees1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employees_new`
--
ALTER TABLE `employees_new`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `employee_id_mapping`
--
ALTER TABLE `employee_id_mapping`
  MODIFY `new_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- AUTO_INCREMENT for table `ta_absence_late_policies`
--
ALTER TABLE `ta_absence_late_policies`
  MODIFY `policy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ta_absence_late_records`
--
ALTER TABLE `ta_absence_late_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ta_absence_late_thresholds`
--
ALTER TABLE `ta_absence_late_thresholds`
  MODIFY `threshold_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ta_attendance`
--
ALTER TABLE `ta_attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `ta_attendance_metrics`
--
ALTER TABLE `ta_attendance_metrics`
  MODIFY `metric_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ta_attendance_tokens`
--
ALTER TABLE `ta_attendance_tokens`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT for table `ta_employee_shifts`
--
ALTER TABLE `ta_employee_shifts`
  MODIFY `employee_shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ta_flexible_schedules`
--
ALTER TABLE `ta_flexible_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `ta_holidays`
--
ALTER TABLE `ta_holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `ta_holiday_sync_log`
--
ALTER TABLE `ta_holiday_sync_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ta_leave_balances`
--
ALTER TABLE `ta_leave_balances`
  MODIFY `leave_balance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `ta_leave_daily_records`
--
ALTER TABLE `ta_leave_daily_records`
  MODIFY `daily_record_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ta_leave_requests`
--
ALTER TABLE `ta_leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ta_overtime_frequency`
--
ALTER TABLE `ta_overtime_frequency`
  MODIFY `frequency_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ta_overtime_tracking`
--
ALTER TABLE `ta_overtime_tracking`
  MODIFY `tracking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ta_punctuality_scores`
--
ALTER TABLE `ta_punctuality_scores`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ta_shifts`
--
ALTER TABLE `ta_shifts`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `wfa_age_distribution`
--
ALTER TABLE `wfa_age_distribution`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_attrition_tracking`
--
ALTER TABLE `wfa_attrition_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_audit_log`
--
ALTER TABLE `wfa_audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_compensation_analysis`
--
ALTER TABLE `wfa_compensation_analysis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_custom_filters`
--
ALTER TABLE `wfa_custom_filters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_department_analytics`
--
ALTER TABLE `wfa_department_analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_diversity_metrics`
--
ALTER TABLE `wfa_diversity_metrics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_employee_metrics`
--
ALTER TABLE `wfa_employee_metrics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_gender_distribution`
--
ALTER TABLE `wfa_gender_distribution`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_headcount_planning`
--
ALTER TABLE `wfa_headcount_planning`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_insights_history`
--
ALTER TABLE `wfa_insights_history`
  MODIFY `insight_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_monthly_attrition`
--
ALTER TABLE `wfa_monthly_attrition`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_performance_distribution`
--
ALTER TABLE `wfa_performance_distribution`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_reports`
--
ALTER TABLE `wfa_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_report_history`
--
ALTER TABLE `wfa_report_history`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_report_snapshots`
--
ALTER TABLE `wfa_report_snapshots`
  MODIFY `snapshot_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_risk_assessment`
--
ALTER TABLE `wfa_risk_assessment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wfa_salary_statistics`
--
ALTER TABLE `wfa_salary_statistics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_skill_gap_analysis`
--
ALTER TABLE `wfa_skill_gap_analysis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfa_tenure_analysis`
--
ALTER TABLE `wfa_tenure_analysis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  ADD CONSTRAINT `fk_employee_accounts_employees` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ta_absence_late_records`
--
ALTER TABLE `ta_absence_late_records`
  ADD CONSTRAINT `fk_absence_leave_request` FOREIGN KEY (`leave_request_id`) REFERENCES `ta_leave_requests` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `ta_attendance`
--
ALTER TABLE `ta_attendance`
  ADD CONSTRAINT `fk_attendance_leave_request` FOREIGN KEY (`leave_request_id`) REFERENCES `ta_leave_requests` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `ta_employee_shifts`
--
ALTER TABLE `ta_employee_shifts`
  ADD CONSTRAINT `fk_emp_shift_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_emp_shift_shift` FOREIGN KEY (`shift_id`) REFERENCES `ta_shifts` (`shift_id`) ON DELETE CASCADE;

--
-- Constraints for table `ta_flexible_schedules`
--
ALTER TABLE `ta_flexible_schedules`
  ADD CONSTRAINT `fk_flex_schedules_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ta_leave_balances`
--
ALTER TABLE `ta_leave_balances`
  ADD CONSTRAINT `ta_leave_balances_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `ta_leave_types` (`leave_type_id`) ON DELETE CASCADE;

--
-- Constraints for table `ta_leave_daily_records`
--
ALTER TABLE `ta_leave_daily_records`
  ADD CONSTRAINT `ta_leave_daily_records_ibfk_1` FOREIGN KEY (`leave_request_id`) REFERENCES `ta_leave_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ta_leave_daily_records_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ta_leave_daily_records_ibfk_3` FOREIGN KEY (`leave_type_id`) REFERENCES `ta_leave_types` (`leave_type_id`);

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
