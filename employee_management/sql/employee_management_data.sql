-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 15, 2026 at 03:48 AM
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
-- Database: `employee_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `status` enum('Present','Absent','Late','Half_Day','On_Leave') DEFAULT 'Present',
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(60, 'QC-COORD', 'Quezon City Coordinator', 'QC campus coordination - Led by: Erickson Castro Mulawin (Coordinator)', NULL, 1, 'Active', '2026-02-11 12:51:52', '2026-02-11 12:51:52');

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

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `employee_id` varchar(50) DEFAULT NULL COMMENT 'System-generated ID',
  `employee_number` varchar(50) DEFAULT NULL COMMENT 'HR reference code',
  `password` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `civil_status` enum('Single','Married','Divorced','Widowed','Separated') DEFAULT 'Single',
  `nationality` varchar(100) DEFAULT 'Filipino',
  `religion` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `personal_email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `mobile_number` varchar(20) DEFAULT NULL,
  `home_phone` varchar(20) DEFAULT NULL,
  `emergency_contact_name` varchar(200) DEFAULT NULL,
  `emergency_contact_number` varchar(20) DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `provincial_address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Philippines',
  `sss_number` varchar(50) DEFAULT NULL,
  `philhealth_number` varchar(50) DEFAULT NULL,
  `pagibig_number` varchar(50) DEFAULT NULL,
  `tin_number` varchar(50) DEFAULT NULL,
  `passport_number` varchar(50) DEFAULT NULL,
  `drivers_license_number` varchar(50) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive','On Leave','Terminated') DEFAULT 'Active',
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `employment_history`
--

CREATE TABLE `employment_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `salary_rate` decimal(15,2) DEFAULT 0.00,
  `employment_type` enum('Regular','Contractual','Probationary','Part-Time','Intern') DEFAULT 'Regular',
  `supervisor` varchar(200) DEFAULT NULL,
  `effective_start_date` date NOT NULL,
  `effective_end_date` date DEFAULT NULL,
  `change_reason` enum('Promotion','Transfer','Salary Adjustment','Resignation','Termination','New Hire') DEFAULT 'New Hire',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_balances`
--

CREATE TABLE `leave_balances` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `leave_type` varchar(50) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `entitlement` decimal(5,2) DEFAULT 0.00,
  `used` decimal(5,2) DEFAULT 0.00,
  `balance` decimal(5,2) GENERATED ALWAYS AS (`entitlement` - `used`) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` int(10) UNSIGNED NOT NULL,
  `position_code` varchar(20) NOT NULL,
  `position_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `level` enum('Intern','Junior','Mid-Level','Senior','Lead','Manager','Director','Executive') DEFAULT 'Junior',
  `salary_min` decimal(15,2) DEFAULT NULL,
  `salary_max` decimal(15,2) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','hr','manager','employee') DEFAULT 'employee',
  `status` enum('active','inactive','locked') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
