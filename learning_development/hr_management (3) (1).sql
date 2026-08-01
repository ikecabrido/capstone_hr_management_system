-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 04, 2026 at 09:02 AM
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
(8, 36, 'Jessica Martinez', '258 Pine Ave', '555-987-6543', 'jessica.martinez@example.com', 'Operations', 'Staff Coordinator', '2023-08-20', 'Active', '2026-03-27 11:25:55', '2026-03-27 13:13:07'),
(9, 37, 'Lisa Anderson', '369 Birch St', '555-111-2222', 'lisa.anderson@example.com', 'Marketing', 'Marketing Specialist', '2023-09-01', 'Active', '2026-04-01 19:08:52', '2026-04-01 19:08:52'),
(10, 38, 'Kevin Thompson', '741 Maple Ave', '555-333-4444', 'kevin.thompson@example.com', 'IT', 'System Administrator', '2023-10-15', 'Active', '2026-04-01 19:08:52', '2026-04-01 19:08:52'),
(11, 39, 'Rachel Garcia', '852 Oak Ln', '555-555-6666', 'rachel.garcia@example.com', 'Finance', 'Senior Accountant', '2023-11-01', 'Active', '2026-04-01 19:08:52', '2026-04-01 19:08:52'),
(12, 40, 'Michael Brown', '963 Pine Rd', '555-777-8888', 'michael.brown@example.com', 'Operations', 'Project Manager', '2023-12-01', 'Active', '2026-04-01 19:08:52', '2026-04-01 19:08:52'),
(13, 41, 'Amanda Wilson', '147 Cedar St', '555-999-0000', 'amanda.wilson@example.com', 'HR', 'Recruitment Specialist', '2024-01-15', 'Active', '2026-04-01 19:08:52', '2026-04-01 19:08:52'),
(14, 42, 'Christopher Lee', '258 Elm Ave', '555-222-3333', 'christopher.lee@example.com', 'IT', 'DevOps Engineer', '2024-02-01', 'Active', '2026-04-01 19:08:52', '2026-04-01 19:08:52'),
(15, 43, 'Jennifer Taylor', '369 Spruce St', '555-444-5555', 'jennifer.taylor@example.com', 'Marketing', 'Content Manager', '2024-03-01', 'Active', '2026-04-01 19:08:52', '2026-04-01 19:08:52'),
(16, 44, 'Daniel Martinez', '741 Willow Ln', '555-666-7777', 'daniel.martinez@example.com', 'Finance', 'Budget Analyst', '2024-04-01', 'Active', '2026-04-01 19:08:52', '2026-04-01 19:08:52'),
(17, 45, 'Nicole Rodriguez', '852 Poplar Ave', '555-888-9999', 'nicole.rodriguez@example.com', 'Operations', 'Quality Assurance Lead', '2024-05-01', 'Active', '2026-04-01 19:08:52', '2026-04-01 19:08:52'),
(18, 46, 'Steven Clark', '963 Ash St', '555-000-1111', 'steven.clark@example.com', 'HR', 'Training Coordinator', '2024-06-01', 'Active', '2026-04-01 19:08:52', '2026-04-01 19:08:52'),
(19, 47, 'Michelle Lewis', '147 Hickory Ln', '555-222-4444', 'michelle.lewis@example.com', 'IT', 'Security Analyst', '2024-07-01', 'Active', '2026-04-01 19:08:52', '2026-04-01 19:08:52'),
(20, 48, 'Brian Walker', '258 Magnolia Ave', '555-555-7777', 'brian.walker@example.com', 'Marketing', 'Digital Marketing Manager', '2024-08-01', 'Active', '2026-04-01 19:08:52', '2026-04-01 19:08:52'),
(21, 49, 'Alexander Wright', '159 Maple St, Springfield', '555-111-1111', 'alexander.wright@example.com', 'IT', 'Software Developer', '2022-01-15', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(22, 50, 'Sophia Martinez', '268 Oak Ave, Springfield', '555-222-2222', 'sophia.martinez@example.com', 'HR', 'HR Coordinator', '2022-02-20', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(23, 51, 'Ethan Johnson', '377 Pine Rd, Springfield', '555-333-3333', 'ethan.johnson@example.com', 'Finance', 'Financial Analyst', '2022-03-10', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(24, 52, 'Olivia Davis', '486 Cedar Ln, Springfield', '555-444-4444', 'olivia.davis@example.com', 'Marketing', 'Marketing Coordinator', '2022-04-05', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(25, 53, 'Mason Wilson', '595 Birch St, Springfield', '555-555-5555', 'mason.wilson@example.com', 'Operations', 'Operations Analyst', '2022-05-12', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(26, 54, 'Ava Garcia', '604 Elm Ave, Springfield', '555-666-6666', 'ava.garcia@example.com', 'IT', 'UI/UX Designer', '2022-06-18', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(27, 55, 'Logan Brown', '713 Spruce St, Springfield', '555-777-7777', 'logan.brown@example.com', 'HR', 'Recruitment Assistant', '2022-07-22', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(28, 56, 'Isabella Lee', '822 Willow Ln, Springfield', '555-888-8888', 'isabella.lee@example.com', 'Finance', 'Accountant', '2022-08-14', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(29, 57, 'Lucas Taylor', '931 Poplar Ave, Springfield', '555-999-9999', 'lucas.taylor@example.com', 'Marketing', 'Content Writer', '2022-09-08', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(30, 58, 'Mia Anderson', '140 Hickory Rd, Springfield', '555-000-1111', 'mia.anderson@example.com', 'Operations', 'Project Coordinator', '2022-10-03', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(31, 59, 'Jackson White', '259 Magnolia St, Springfield', '555-111-2222', 'jackson.white@example.com', 'IT', 'Database Administrator', '2022-11-16', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(32, 60, 'Charlotte Harris', '368 Ash Ave, Springfield', '555-222-3333', 'charlotte.harris@example.com', 'HR', 'Employee Relations Specialist', '2022-12-09', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(33, 61, 'Aiden Clark', '477 Juniper Ln, Springfield', '555-333-4444', 'aiden.clark@example.com', 'Finance', 'Budget Coordinator', '2023-01-25', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(34, 62, 'Harper Lewis', '586 Cypress Rd, Springfield', '555-444-5555', 'harper.lewis@example.com', 'Marketing', 'Social Media Manager', '2023-02-14', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(35, 63, 'Elijah Robinson', '695 Redwood St, Springfield', '555-555-6666', 'elijah.robinson@example.com', 'Operations', 'Supply Chain Analyst', '2023-03-07', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(36, 64, 'Amelia Walker', '704 Sequoia Ave, Springfield', '555-666-7777', 'amelia.walker@example.com', 'IT', 'Network Engineer', '2023-04-19', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(37, 65, 'Benjamin Hall', '813 Palm Ln, Springfield', '555-777-8888', 'benjamin.hall@example.com', 'HR', 'Training Specialist', '2023-05-11', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(38, 66, 'Evelyn Young', '922 Eucalyptus Rd, Springfield', '555-888-9999', 'evelyn.young@example.com', 'Finance', 'Tax Specialist', '2023-06-28', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(39, 67, 'Sebastian King', '131 Fir St, Springfield', '555-999-0000', 'sebastian.king@example.com', 'Marketing', 'Brand Manager', '2023-07-15', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(40, 68, 'Abigail Wright', '240 Chestnut Ave, Springfield', '555-000-1111', 'abigail.wright@example.com', 'Operations', 'Quality Control Inspector', '2023-08-22', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(41, 69, 'Matthew Lopez', '349 Dogwood Ln, Springfield', '555-111-3333', 'matthew.lopez@example.com', 'IT', 'Cybersecurity Analyst', '2023-09-04', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(42, 70, 'Ella Hill', '458 Hawthorn Rd, Springfield', '555-222-4444', 'ella.hill@example.com', 'HR', 'Benefits Administrator', '2023-10-17', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(43, 71, 'David Green', '567 Sycamore St, Springfield', '555-333-5555', 'david.green@example.com', 'Finance', 'Investment Analyst', '2023-11-09', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(44, 72, 'Sofia Adams', '676 Linden Ave, Springfield', '555-444-6666', 'sofia.adams@example.com', 'Marketing', 'Public Relations Specialist', '2023-12-01', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(45, 73, 'Joseph Baker', '785 Acacia Ln, Springfield', '555-555-7777', 'joseph.baker@example.com', 'Operations', 'Warehouse Manager', '2024-01-18', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(46, 74, 'Grace Nelson', '894 Beech Rd, Springfield', '555-666-8888', 'grace.nelson@example.com', 'IT', 'Technical Support Specialist', '2024-02-12', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(47, 75, 'Samuel Carter', '903 Walnut St, Springfield', '555-777-9999', 'samuel.carter@example.com', 'HR', 'Compensation Analyst', '2024-03-26', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(48, 76, 'Chloe Mitchell', '112 Holly Ave, Springfield', '555-888-0000', 'chloe.mitchell@example.com', 'Finance', 'Audit Assistant', '2024-04-09', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(49, 77, 'Henry Perez', '221 Laurel Ln, Springfield', '555-999-1111', 'henry.perez@example.com', 'Marketing', 'Event Coordinator', '2024-05-21', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(50, 78, 'Lily Roberts', '330 Juniper Rd, Springfield', '555-000-2222', 'lily.roberts@example.com', 'Operations', 'Customer Service Manager', '2024-06-14', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(51, 79, 'Jack Turner', '439 Cypress St, Springfield', '555-111-4444', 'jack.turner@example.com', 'IT', 'DevOps Engineer', '2024-07-08', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(52, 80, 'Zoe Phillips', '548 Redwood Ave, Springfield', '555-222-5555', 'zoe.phillips@example.com', 'HR', 'Diversity Officer', '2024-08-19', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(53, 81, 'Owen Campbell', '657 Sequoia Ln, Springfield', '555-333-6666', 'owen.campbell@example.com', 'Finance', 'Risk Analyst', '2024-09-11', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(54, 82, 'Nora Parker', '766 Palm Rd, Springfield', '555-444-7777', 'nora.parker@example.com', 'Marketing', 'SEO Specialist', '2024-10-03', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(55, 83, 'Wyatt Evans', '875 Eucalyptus St, Springfield', '555-555-8888', 'wyatt.evans@example.com', 'Operations', 'Logistics Coordinator', '2024-11-25', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(56, 84, 'Hannah Edwards', '984 Fir Ave, Springfield', '555-666-9999', 'hannah.edwards@example.com', 'IT', 'Mobile App Developer', '2024-12-07', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(57, 85, 'Carter Collins', '193 Chestnut Ln, Springfield', '555-777-0000', 'carter.collins@example.com', 'HR', 'Labor Relations Specialist', '2025-01-29', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(58, 86, 'Madison Stewart', '302 Dogwood Rd, Springfield', '555-888-1111', 'madison.stewart@example.com', 'Finance', 'Financial Planner', '2025-02-18', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(59, 87, 'Jayden Sanchez', '411 Hawthorn St, Springfield', '555-999-2222', 'jayden.sanchez@example.com', 'Marketing', 'Copywriter', '2025-03-12', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(60, 88, 'Victoria Morris', '520 Sycamore Ave, Springfield', '555-000-3333', 'victoria.morris@example.com', 'Operations', 'Procurement Specialist', '2025-04-24', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(61, 89, 'Dylan Rogers', '629 Linden Ln, Springfield', '555-111-5555', 'dylan.rogers@example.com', 'IT', 'Cloud Architect', '2025-05-16', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(62, 90, 'Penelope Reed', '738 Acacia Rd, Springfield', '555-222-6666', 'penelope.reed@example.com', 'HR', 'Organizational Development Consultant', '2025-06-08', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(63, 91, 'Grayson Cook', '847 Beech St, Springfield', '555-333-7777', 'grayson.cook@example.com', 'Finance', 'Corporate Treasurer', '2025-07-30', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(64, 92, 'Riley Morgan', '956 Walnut Ave, Springfield', '555-444-8888', 'riley.morgan@example.com', 'Marketing', 'Advertising Manager', '2025-08-21', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(65, 93, 'Leah Bell', '165 Holly Ln, Springfield', '555-555-9999', 'leah.bell@example.com', 'Operations', 'Facilities Manager', '2025-09-13', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(66, 94, 'Julian Murphy', '274 Laurel Rd, Springfield', '555-666-0000', 'julian.murphy@example.com', 'IT', 'AI/ML Engineer', '2025-10-05', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(67, 95, 'Savannah Bailey', '383 Juniper St, Springfield', '555-777-1111', 'savannah.bailey@example.com', 'HR', 'Workforce Planning Specialist', '2025-11-27', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(68, 96, 'Levi Rivera', '492 Cypress Ave, Springfield', '555-888-2222', 'levi.rivera@example.com', 'Finance', 'Forensic Accountant', '2025-12-19', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(69, 97, 'Brooklyn Cooper', '601 Redwood Ln, Springfield', '555-999-3333', 'brooklyn.cooper@example.com', 'Marketing', 'Influencer Marketing Specialist', '2026-01-10', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12'),
(70, 98, 'Nathan Richardson', '710 Sequoia Rd, Springfield', '555-000-4444', 'nathan.richardson@example.com', 'Operations', 'Business Process Analyst', '2026-02-02', 'Active', '2026-04-01 19:14:12', '2026-04-01 19:14:12');

-- --------------------------------------------------------

--
-- Table structure for table `employee_contributions`
--

CREATE TABLE `employee_contributions` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `contribution_type` enum('sss','pagibig','philhealth') NOT NULL,
  `status` enum('submitted','pending') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `contribution_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_contributions`
--

INSERT INTO `employee_contributions` (`id`, `employee_id`, `contribution_type`, `status`, `notes`, `created_at`, `updated_at`, `contribution_number`) VALUES
(1, 1, 'sss', 'submitted', NULL, '2026-03-29 21:03:57', '2026-03-29 21:03:57', '26-5464544-8'),
(2, 1, 'pagibig', 'submitted', NULL, '2026-03-29 21:03:57', '2026-03-29 21:03:57', '2849-4926-6162'),
(3, 1, 'philhealth', 'submitted', NULL, '2026-03-29 21:03:57', '2026-03-29 21:03:57', '2162-6265-1651'),
(4, 6, 'sss', 'pending', NULL, '2026-03-29 21:09:39', '2026-03-29 21:09:39', ''),
(5, 6, 'pagibig', 'submitted', NULL, '2026-03-29 21:09:39', '2026-03-29 21:09:39', '6241-9265-9592'),
(6, 6, 'philhealth', 'pending', NULL, '2026-03-29 21:09:39', '2026-03-29 21:09:39', ''),
(7, 3, 'sss', 'submitted', NULL, '2026-03-29 21:09:52', '2026-03-29 21:09:52', '98-5959126-2'),
(8, 3, 'philhealth', 'pending', NULL, '2026-03-29 21:09:52', '2026-03-29 21:09:52', ''),
(9, 3, 'pagibig', 'pending', NULL, '2026-03-29 21:09:52', '2026-03-29 21:09:52', ''),
(10, 4, 'sss', 'pending', NULL, '2026-03-29 21:10:08', '2026-03-29 21:10:08', ''),
(11, 4, 'pagibig', 'pending', NULL, '2026-03-29 21:10:08', '2026-03-29 21:10:08', ''),
(12, 4, 'philhealth', 'submitted', NULL, '2026-03-29 21:10:08', '2026-03-29 21:10:08', '2965-9595-8929'),
(13, 2, 'sss', 'pending', NULL, '2026-03-29 21:10:21', '2026-03-29 21:10:21', ''),
(14, 2, 'pagibig', 'submitted', NULL, '2026-03-29 21:10:21', '2026-03-29 21:10:21', '9829-5989-5262'),
(15, 2, 'philhealth', 'pending', NULL, '2026-03-29 21:10:21', '2026-03-29 21:10:21', ''),
(16, 5, 'sss', 'submitted', NULL, '2026-03-29 21:10:35', '2026-03-29 21:10:35', '59-8949256-1'),
(17, 5, 'pagibig', 'pending', NULL, '2026-03-29 21:10:35', '2026-03-29 21:10:35', ''),
(18, 5, 'philhealth', 'pending', NULL, '2026-03-29 21:10:35', '2026-03-29 21:10:35', '');

-- --------------------------------------------------------

--
-- Table structure for table `ep_employee_documents`
--

CREATE TABLE `ep_employee_documents` (
  `approval_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `remarks` text DEFAULT NULL,
  `submitted_on` datetime NOT NULL DEFAULT current_timestamp(),
  `submit_by` int(10) UNSIGNED DEFAULT NULL,
  `department` int(10) UNSIGNED DEFAULT NULL,
  `approver_id` int(10) UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `decision` enum('Approved','Rejected','Pending') DEFAULT 'Pending',
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ep_online_meetings`
--

CREATE TABLE `ep_online_meetings` (
  `meetings_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `meeting_link` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exit_archive`
--

CREATE TABLE `exit_archive` (
  `id` int(11) NOT NULL,
  `archive_type` enum('resignation','settlement','interview','document','survey','transfer_plan','transfer_item') NOT NULL,
  `original_id` int(11) NOT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `original_created_by` int(11) DEFAULT NULL,
  `archived_by` int(11) NOT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archive_reason` varchar(255) DEFAULT NULL,
  `archive_data` longtext DEFAULT NULL COMMENT 'JSON data of the original record',
  `restored` tinyint(1) DEFAULT 0,
  `restored_by` int(11) DEFAULT NULL,
  `restored_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `exit_employee_settlements`
--

CREATE TABLE `exit_employee_settlements` (
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
-- Dumping data for table `exit_employee_settlements`
--

INSERT INTO `exit_employee_settlements` (`id`, `employee_id`, `resignation_id`, `basic_salary`, `hra`, `conveyance`, `lta`, `medical_allowance`, `other_allowances`, `provident_fund`, `gratuity`, `notice_pay`, `outstanding_loans`, `other_deductions`, `net_payable`, `settlement_date`, `status`, `approved_by`, `approved_at`, `created_by`, `created_at`, `updated_at`) VALUES
(0, '37', 3, 25000.00, 10000.00, 19200.00, 30000.00, 5000.00, 3000.00, 3000.00, 12000.00, 0.00, 0.00, 0.00, 77200.00, '2026-04-03', 'draft', NULL, NULL, 10, '2026-04-01 19:51:24', '2026-04-01 19:51:24'),
(1, 'EMP002', 8, 123.00, 12.00, 12.00, 3.00, 3.00, 3.00, 5.00, 0.00, 5.00, 111.00, 55.00, -20.00, '2026-03-18', 'draft', NULL, NULL, NULL, '2026-03-17 09:04:17', '2026-03-17 09:04:17'),
(2, 'EMP001', 5, 321.00, 1.00, 42342.00, 13.00, 43.00, 453534.00, 3454.00, 5435.00, 435.00, 435.00, 435.00, 486060.00, '2026-03-20', 'draft', NULL, NULL, 10, '2026-03-17 12:55:11', '2026-03-17 12:55:11');

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
(0, '26', 10, '2026-04-17', '07:43:00', 'Virtual', 'v', 'completed', NULL, '2026-04-01 19:43:07', '2026-04-01 20:43:49'),
(1, 'EMP002', 0, '2026-03-20', '03:23:00', 'Virtual', 'dsaa', 'scheduled', NULL, '2026-03-17 07:24:03', '2026-03-17 07:24:14'),
(2, 'EMP002', 0, '2026-03-11', '05:03:00', 'Virtual', '456456', 'scheduled', NULL, '2026-03-17 09:03:18', '2026-03-17 09:03:18'),
(3, 'EMP003', 0, '2026-03-11', '16:23:00', 'Virtual', 'jhtuoyikuioy', 'scheduled', NULL, '2026-03-17 09:21:28', '2026-03-17 09:21:28');

-- --------------------------------------------------------

--
-- Table structure for table `exit_knowledge_transfer_items`
--

CREATE TABLE `exit_knowledge_transfer_items` (
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
-- Dumping data for table `exit_knowledge_transfer_items`
--

INSERT INTO `exit_knowledge_transfer_items` (`id`, `plan_id`, `item_type`, `title`, `description`, `priority`, `status`, `completed_at`, `created_at`) VALUES
(1, 1, 'process', 'asd', 'das', 'medium', 'pending', NULL, '2026-03-17 06:07:27'),
(2, 2, 'process', 'ghjhgj', 'jhjjhj', 'medium', 'pending', NULL, '2026-03-17 08:48:34'),
(3, 3, 'process', 'asd', 'iyggyi', 'medium', 'pending', NULL, '2026-03-17 08:55:48'),
(4, 4, 'contact', 'ghjhgj', 'f j;dhk;jhgrl;hjfgb;jgvl;bj', 'low', 'pending', NULL, '2026-03-17 08:59:31'),
(5, 5, 'system', 'sadda', 'das', 'medium', 'pending', NULL, '2026-03-17 09:09:40'),
(6, 6, 'system', 'sadda', 'das', 'medium', 'pending', NULL, '2026-03-17 09:09:51'),
(10, 10, 'system', 'ads', 'asdasdas', 'medium', 'pending', NULL, '2026-03-17 12:41:47');

-- --------------------------------------------------------

--
-- Table structure for table `exit_knowledge_transfer_plans`
--

CREATE TABLE `exit_knowledge_transfer_plans` (
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
-- Dumping data for table `exit_knowledge_transfer_plans`
--

INSERT INTO `exit_knowledge_transfer_plans` (`id`, `employee_id`, `successor_id`, `start_date`, `end_date`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(0, '37', '21', '2026-04-13', '2026-04-28', 'active', 10, '2026-04-01 19:50:49', '2026-04-01 19:50:49'),
(1, 'EMP002', 'EMP001', '2026-03-16', '2026-03-17', 'active', 10, '2026-03-17 06:07:26', '2026-03-17 06:07:26'),
(2, 'EMP002', 'EMP001', '2026-03-12', '2026-03-18', 'active', NULL, '2026-03-17 08:48:34', '2026-03-17 08:48:34'),
(3, 'EMP003', 'EMP001', '2026-03-19', '2026-03-19', 'active', NULL, '2026-03-17 08:55:48', '2026-03-17 08:55:48'),
(4, 'EMP002', 'EMP001', '2026-03-19', '2026-03-11', 'active', NULL, '2026-03-17 08:59:31', '2026-03-17 08:59:31'),
(5, 'EMP002', 'EMP003', '2026-03-18', '2026-03-26', 'active', NULL, '2026-03-17 09:09:39', '2026-03-17 09:09:39'),
(6, 'EMP002', 'EMP003', '2026-03-18', '2026-03-26', 'active', NULL, '2026-03-17 09:09:51', '2026-03-17 09:09:51'),
(10, 'EMP002', 'EMP003', '2026-03-10', '2026-03-18', 'active', 10, '2026-03-17 12:41:47', '2026-03-17 12:41:47');

-- --------------------------------------------------------

--
-- Table structure for table `exit_resignations`
--

CREATE TABLE `exit_resignations` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `resignation_type` enum('voluntary','involuntary') NOT NULL,
  `reason` text NOT NULL,
  `notice_date` date NOT NULL,
  `last_working_date` date NOT NULL,
  `comments` text DEFAULT NULL,
  `submitted_by` int(11) DEFAULT NULL,
  `preclearance_desk_person` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected','withdrawn') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `archived_from_status` enum('pending','approved','rejected','withdrawn') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exit_resignations`
--

INSERT INTO `exit_resignations` (`id`, `employee_id`, `resignation_type`, `reason`, `notice_date`, `last_working_date`, `comments`, `submitted_by`, `preclearance_desk_person`, `status`, `approved_by`, `approved_at`, `created_at`, `updated_at`, `archived_from_status`) VALUES
(1, '5', 'voluntary', 'Career Advancement', '2026-03-15', '2026-03-31', 'Accepted a senior position at another company', 10, NULL, 'approved', NULL, NULL, '2026-04-01 19:27:17', '2026-04-01 19:27:17', NULL),
(2, '12', 'voluntary', 'Personal Reasons', '2026-03-20', '2026-04-15', 'Family relocation to another city', 10, NULL, 'approved', NULL, NULL, '2026-04-01 19:27:17', '2026-04-01 19:27:17', NULL),
(3, '18', 'voluntary', 'Health Issues', '2026-03-25', '2026-04-10', 'Medical leave extended, pursuing different career path', 10, NULL, 'approved', NULL, NULL, '2026-04-01 19:27:17', '2026-04-01 19:27:17', NULL),
(4, '23', 'voluntary', 'Higher Salary', '2026-04-01', '2026-04-30', 'Received better compensation package elsewhere', 10, NULL, 'pending', NULL, NULL, '2026-04-01 19:27:17', '2026-04-01 19:27:17', NULL),
(5, '31', 'voluntary', 'Work Environment', '2026-04-05', '2026-05-15', 'Seeking more collaborative work culture', 10, NULL, 'approved', NULL, NULL, '2026-04-01 19:27:17', '2026-04-01 19:27:17', NULL),
(6, '42', 'voluntary', 'Career Change', '2026-04-10', '2026-05-01', 'Pursuing entrepreneurship', 10, NULL, 'pending', NULL, NULL, '2026-04-01 19:27:17', '2026-04-01 19:27:17', NULL),
(7, '56', 'voluntary', 'Education', '2026-04-12', '2026-05-20', 'Returning to school for advanced degree', 10, NULL, 'approved', NULL, NULL, '2026-04-01 19:27:17', '2026-04-01 19:27:17', NULL),
(8, '67', 'voluntary', 'Retirement', '2026-04-15', '2026-05-10', 'Early retirement planning', 10, NULL, 'pending', NULL, NULL, '2026-04-01 19:27:17', '2026-04-01 19:27:17', NULL),
(9, '3', 'voluntary', 'Job Dissatisfaction', '2026-04-18', '2026-05-25', 'Role not meeting expectations', 10, NULL, 'approved', NULL, NULL, '2026-04-01 19:27:17', '2026-04-01 19:27:17', NULL),
(10, '28', 'involuntary', 'Company Restructuring', '2026-04-20', '2026-06-01', 'Position being eliminated', 10, NULL, 'approved', NULL, NULL, '2026-04-01 19:27:17', '2026-04-01 19:27:17', NULL),
(11, '45', 'voluntary', 'Remote Work', '2026-04-22', '2026-05-30', 'Company moving to office-only policy', 10, NULL, 'pending', NULL, NULL, '2026-04-01 19:27:17', '2026-04-01 19:27:17', NULL),
(12, '61', 'voluntary', 'Burnout', '2026-04-25', '2026-06-15', 'Need for work-life balance', 10, NULL, 'approved', NULL, NULL, '2026-04-01 19:27:18', '2026-04-01 19:27:18', NULL),
(13, '2', 'voluntary', 'Family Care', '2026-04-28', '2026-06-10', 'Caring for elderly family member', 10, NULL, 'pending', NULL, NULL, '2026-04-01 19:27:18', '2026-04-01 19:27:18', NULL),
(14, '34', 'voluntary', 'Travel Opportunities', '2026-05-01', '2026-06-20', 'Partner job requires relocation', 10, NULL, 'approved', NULL, NULL, '2026-04-01 19:27:18', '2026-04-01 19:27:18', NULL),
(15, '52', 'voluntary', 'Contract End', '2026-05-05', '2026-06-25', 'Fixed-term contract completion', 10, NULL, 'approved', NULL, NULL, '2026-04-01 19:27:18', '2026-04-01 19:27:18', NULL),
(16, '1', 'voluntary', 'Better Opportunity', '2026-02-10', '2026-03-15', 'Found better position elsewhere', 10, NULL, 'pending', NULL, NULL, '2026-04-01 19:27:18', '2026-04-01 19:27:18', NULL),
(17, '15', 'voluntary', 'Salary Expectations', '2026-02-18', '2026-03-25', 'Current salary does not meet expectations', 10, NULL, 'pending', NULL, NULL, '2026-04-01 19:27:18', '2026-04-01 19:27:18', NULL),
(18, '22', 'voluntary', 'Relocation', '2026-02-25', '2026-03-31', 'Moving to another state', 10, NULL, 'approved', NULL, NULL, '2026-04-01 19:27:18', '2026-04-01 19:27:18', NULL),
(19, '39', 'involuntary', 'Performance Issues', '2026-03-05', '2026-04-05', 'Unable to meet performance standards', 10, NULL, 'approved', NULL, NULL, '2026-04-01 19:27:18', '2026-04-01 19:27:18', NULL),
(20, '51', 'voluntary', 'Start Own Business', '2026-03-12', '2026-04-25', 'Pursuing personal entrepreneurial venture', 10, NULL, 'pending', NULL, NULL, '2026-04-01 19:27:18', '2026-04-01 19:27:18', NULL),
(21, '7', 'voluntary', 'Seeking Leadership Role', '2026-03-08', '2026-04-20', 'Looking for management positions', 10, NULL, 'pending', NULL, NULL, '2026-04-01 19:27:18', '2026-04-01 19:27:18', NULL),
(22, '19', 'involuntary', 'Layoff', '2026-03-22', '2026-04-22', 'Part of department restructuring', 10, NULL, 'approved', NULL, NULL, '2026-04-01 19:27:18', '2026-04-01 19:27:18', NULL),
(23, '26', 'voluntary', 'Work-Life Balance', '2026-03-28', '2026-05-05', 'Need more time for personal pursuits', 10, NULL, 'pending', NULL, NULL, '2026-04-01 19:27:18', '2026-04-01 19:27:18', NULL),
(24, '37', 'voluntary', 'Commute Concerns', '2026-04-03', '2026-05-12', 'Remote work not available, long commute', 10, NULL, 'approved', NULL, NULL, '2026-04-01 19:27:18', '2026-04-01 19:27:18', NULL),
(25, '48', 'voluntary', 'Back to School', '2026-04-08', '2026-05-18', 'Pursuing postgraduate studies', 10, NULL, 'pending', NULL, NULL, '2026-04-01 19:27:18', '2026-04-01 19:27:18', NULL),
(27, '33', 'voluntary', 'DFSAD', '2026-04-15', '2026-04-08', 'DASD', 10, 12, 'pending', NULL, NULL, '2026-04-01 20:43:40', '2026-04-01 20:43:40', NULL);

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
(1, 'casedca', 'dcasdcas', 'voluntary', '2026-03-01', '2026-12-31', 'active', NULL, '2026-03-17 09:05:00', '2026-04-01 20:24:32'),
(2, 'casedca', 'dcasdcas', 'voluntary', '2026-03-01', '2026-12-31', 'active', NULL, '2026-03-17 09:05:23', '2026-04-01 20:24:32'),
(3, 'dsadsaadsda', 'adsdasadsadsasd', 'all', '2026-03-18', '2026-12-31', 'active', 10, '2026-03-17 14:01:22', '2026-04-01 20:24:32'),
(4, 'dasdasd', 'sadasdasd', 'all', '2026-03-25', '2026-12-31', 'active', 10, '2026-03-17 15:34:26', '2026-04-01 20:24:32');

-- --------------------------------------------------------

--
-- Table structure for table `exit_survey_answers`
--

CREATE TABLE `exit_survey_answers` (
  `id` int(11) NOT NULL,
  `response_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text DEFAULT NULL,
  `answer_value` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exit_survey_answers`
--

INSERT INTO `exit_survey_answers` (`id`, `response_id`, `question_id`, `answer_text`, `answer_value`, `created_at`) VALUES
(1, 5, 5, '[\"hi\",\"hello\"]', 'hi, hello', '2026-04-01 20:27:20'),
(2, 5, 6, '4', '4', '2026-04-01 20:27:20'),
(3, 5, 7, 'das', 'das', '2026-04-01 20:27:20'),
(4, 5, 8, 'test answer', 'test answer', '2026-04-01 20:27:20');

-- --------------------------------------------------------

--
-- Table structure for table `exit_survey_questions`
--

CREATE TABLE `exit_survey_questions` (
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
-- Dumping data for table `exit_survey_questions`
--

INSERT INTO `exit_survey_questions` (`id`, `survey_id`, `question_text`, `question_type`, `options`, `required`, `order_num`, `created_at`) VALUES
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
-- Table structure for table `exit_survey_responses`
--

CREATE TABLE `exit_survey_responses` (
  `id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `employee_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `responses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`responses`)),
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exit_survey_responses`
--

INSERT INTO `exit_survey_responses` (`id`, `survey_id`, `employee_id`, `responses`, `submitted_at`) VALUES
(2, 4, '1', NULL, '2026-04-01 20:26:24'),
(5, 4, '1', NULL, '2026-04-01 20:27:20');

-- --------------------------------------------------------

--
-- Table structure for table `ld_archive`
--

CREATE TABLE `ld_archive` (
  `id` int(11) NOT NULL,
  `archive_type` enum('course','program','certification') NOT NULL,
  `original_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `original_created_by` int(11) DEFAULT NULL,
  `archived_by` int(11) NOT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archive_reason` varchar(255) DEFAULT NULL,
  `archive_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`archive_data`)),
  `restored` tinyint(1) DEFAULT 0,
  `restored_by` int(11) DEFAULT NULL,
  `restored_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ld_archive`
--

INSERT INTO `ld_archive` (`id`, `archive_type`, `original_id`, `title`, `description`, `content`, `original_created_by`, `archived_by`, `archived_at`, `archive_reason`, `archive_data`, `restored`, `restored_by`, `restored_at`) VALUES
(1, 'certification', 1, 'Effective Communication Certificate - someone', 'Certification issued to someone for ', NULL, 7, 7, '2026-03-23 05:27:06', NULL, '{\"id\":1,\"employee_id\":4,\"course_id\":1,\"certification_name\":\"Effective Communication Certificate\",\"issued_date\":\"2024-03-15\",\"expiry_date\":null,\"issued_by\":7,\"status\":\"active\",\"created_at\":\"2026-03-22 13:56:59\",\"employee_name\":\"someone\",\"course_title\":\"\",\"issued_by_name\":\"learn\"}', 1, 7, '2026-03-23 05:27:12'),
(2, 'certification', 5, 'Someome - someone', 'Certification issued to someone for ', NULL, 7, 7, '2026-03-23 05:27:21', NULL, '{\"id\":5,\"employee_id\":4,\"course_id\":1,\"certification_name\":\"Someome\",\"issued_date\":\"2026-03-23\",\"expiry_date\":\"2030-03-23\",\"issued_by\":7,\"status\":\"active\",\"created_at\":\"2026-03-23 11:37:09\",\"employee_name\":\"someone\",\"course_title\":\"\",\"issued_by_name\":\"learn\"}', 1, 7, '2026-03-23 05:27:26'),
(3, 'course', 3, 'Yellowstone ', 'Lorem Ipisum', NULL, 7, 7, '2026-03-23 09:12:55', NULL, '{\"id\":3,\"title\":\"Yellowstone \",\"description\":\"Lorem Ipisum\",\"instructor\":\"Ipisim Lorme\",\"duration_hours\":32,\"training_program_id\":2,\"content_type\":\"hybrid\",\"status\":\"active\",\"created_by\":7,\"created_at\":\"2026-03-23 11:44:55\",\"updated_at\":\"2026-03-23 11:44:55\"}', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ld_certification`
--

CREATE TABLE `ld_certification` (
  `ld_certification_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `ld_courses_id` int(11) NOT NULL,
  `certification_name` varchar(255) NOT NULL,
  `issued_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `issued_by_user_id` int(11) NOT NULL,
  `status` enum('active','revoked','expired') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ld_certification`
--

INSERT INTO `ld_certification` (`ld_certification_id`, `employee_id`, `ld_courses_id`, `certification_name`, `issued_date`, `expiry_date`, `issued_by_user_id`, `status`, `created_at`) VALUES
(1, 4, 1, 'Effective Communication Certificate', '2024-03-15', NULL, 7, 'active', '2026-03-22 05:56:59'),
(4, 1, 1, '213', '2026-03-27', '2026-03-18', 7, 'revoked', '2026-03-22 06:23:12'),
(5, 3, 3, 'Someomedadad', '2026-03-23', '2030-03-23', 7, 'active', '2026-03-23 03:37:09'),
(6, 2, 2, 'dsa', '2026-03-23', '2026-03-23', 7, 'revoked', '2026-03-23 03:39:00'),
(7, 4, 5, 'baawew', '2026-03-23', '2031-11-20', 7, 'active', '2026-03-23 13:19:22'),
(8, 1, 1, 'Strategic Leadership Certificate', '2026-03-15', '2027-03-15', 7, 'active', '2026-04-02 17:57:09'),
(9, 1, 6, 'Social Media Marketing Certificate', '2026-03-20', '2027-03-20', 7, 'active', '2026-04-02 17:57:09'),
(10, 2, 2, 'Team Dynamics Certificate', '2026-03-01', '2027-03-01', 7, 'active', '2026-04-02 17:57:09'),
(11, 2, 8, 'Project Planning Certificate', '2026-03-18', '2027-03-18', 7, 'active', '2026-04-02 17:57:09'),
(12, 3, 3, 'Change Management Certificate', '2026-02-28', '2027-02-28', 7, 'active', '2026-04-02 17:57:09'),
(13, 3, 10, 'Risk Management Certificate', '2026-03-25', '2027-03-25', 7, 'active', '2026-04-02 17:57:09'),
(14, 4, 4, 'SEO Fundamentals Certificate', '2026-03-12', '2027-03-12', 7, 'active', '2026-04-02 17:57:09'),
(15, 4, 11, 'Python for Data Analysis Certificate', '2026-03-18', '2027-03-18', 7, 'active', '2026-04-02 17:57:09'),
(16, 5, 5, 'Content Marketing Certificate', '2026-03-08', '2027-03-08', 7, 'active', '2026-04-02 17:57:09'),
(17, 5, 13, 'Data Visualization Certificate', '2026-03-15', '2027-03-15', 7, 'active', '2026-04-02 17:57:09'),
(18, 6, 7, 'Security Best Practices Certificate', '2026-03-10', '2027-03-10', 7, 'active', '2026-04-02 17:57:09'),
(19, 6, 14, 'Statistical Analysis Certificate', '2026-03-22', '2027-03-22', 7, 'active', '2026-04-02 17:57:09'),
(20, 7, 9, 'Project Initiation Certificate', '2026-03-05', '2027-03-05', 7, 'active', '2026-04-02 17:57:09'),
(21, 7, 16, 'Customer Satisfaction Certificate', '2026-03-20', '2027-03-20', 7, 'active', '2026-04-02 17:57:09'),
(22, 8, 17, 'Budgeting Fundamentals Certificate', '2026-03-14', '2027-03-14', 7, 'active', '2026-04-02 17:57:09'),
(23, 8, 24, 'Business Writing Certificate', '2026-03-30', '2027-03-30', 7, 'active', '2026-04-02 17:57:09'),
(24, 9, 20, 'Scrum Framework Certificate', '2026-03-18', '2027-03-18', 7, 'active', '2026-04-02 17:57:09'),
(25, 9, 26, 'Public Speaking Certificate', '2026-04-01', '2027-04-01', 7, 'active', '2026-04-02 17:57:09'),
(26, 10, 21, 'Kanban Method Certificate', '2026-03-16', '2027-03-16', 7, 'active', '2026-04-02 17:57:09'),
(27, 10, 27, 'ISO 9001 Standards Certificate', '2026-04-02', '2027-04-02', 7, 'active', '2026-04-02 17:57:09'),
(28, 1, 6, 'Old Social Media Certificate', '2025-03-20', '2026-03-20', 7, 'expired', '2026-04-02 17:57:09'),
(29, 2, 8, 'Old Project Planning Certificate', '2025-03-18', '2026-03-18', 7, 'expired', '2026-04-02 17:57:09'),
(30, 3, 10, 'Revoked Risk Management Certificate', '2026-02-15', '2027-02-15', 7, 'revoked', '2026-04-02 17:57:09'),
(31, 4, 11, 'Revoked Python Certificate', '2026-02-20', '2027-02-20', 7, 'revoked', '2026-04-02 17:57:09');

-- --------------------------------------------------------

--
-- Table structure for table `ld_courses`
--

CREATE TABLE `ld_courses` (
  `ld_courses_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `instructor` varchar(255) NOT NULL,
  `duration_hours` int(11) NOT NULL,
  `ld_training_programs_id` int(11) DEFAULT NULL,
  `content_type` enum('in-person','online','hybrid') DEFAULT 'in-person',
  `status` enum('active','inactive','completed') DEFAULT 'active',
  `created_by_user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ld_courses`
--

INSERT INTO `ld_courses` (`ld_courses_id`, `title`, `description`, `instructor`, `duration_hours`, `ld_training_programs_id`, `content_type`, `status`, `created_by_user_id`, `created_at`, `updated_at`) VALUES
(1, 'Python Basics', NULL, 'John Doe', 0, 1, 'in-person', 'active', 7, '2026-03-22 05:56:58', '2026-04-02 17:46:12'),
(2, 'Advanced Python', NULL, 'John Doe', 0, 1, 'in-person', 'active', 7, '2026-03-22 05:56:58', '2026-04-02 17:46:12'),
(3, '', NULL, '', 0, NULL, NULL, 'inactive', 7, '2026-03-23 03:44:55', '2026-03-23 09:12:55'),
(4, 'G', 'G', 'G', 23, 2, 'online', 'active', 7, '2026-03-23 09:13:13', '2026-03-23 09:13:13'),
(5, 'Whie', 'Waoragvd', 'dfasdwet4', 54, 3, 'in-person', 'active', 7, '2026-03-23 13:18:44', '2026-03-23 13:18:44'),
(6, 'Strategic Leadership', 'Learn strategic thinking and long-term planning skills', 'Dr. Sarah Johnson', 8, 4, 'in-person', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(7, 'Team Dynamics & Management', 'Understanding team behavior and effective management techniques', 'Dr. Sarah Johnson', 6, 4, 'online', 'inactive', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(8, 'Change Management', 'Leading organizational change and transformation', 'Dr. Sarah Johnson', 8, 4, 'hybrid', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(9, 'SEO Fundamentals', 'Search engine optimization basics and advanced techniques', 'Mike Chen', 6, 5, 'online', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(10, 'Social Media Marketing', 'Building brand presence on social platforms', 'Mike Chen', 8, 5, 'online', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(11, 'Content Marketing Strategy', 'Creating and distributing valuable content', 'Mike Chen', 6, 5, 'hybrid', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(12, 'Data Protection Basics', 'Understanding data privacy and protection laws', 'Lisa Rodriguez', 4, 6, 'online', 'inactive', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(13, 'Phishing Awareness', 'Recognizing and preventing phishing attacks', 'Lisa Rodriguez', 3, 6, 'online', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(14, 'Security Best Practices', 'Daily security habits and procedures', 'Lisa Rodriguez', 5, 6, 'in-person', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(15, 'Project Initiation', 'Project charter and stakeholder analysis', 'David Thompson', 6, 7, 'hybrid', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(16, 'Project Planning', 'Creating comprehensive project plans', 'David Thompson', 8, 7, 'in-person', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(17, 'Risk Management', 'Identifying and managing project risks', 'David Thompson', 6, 7, 'online', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(18, 'Python for Data Analysis', 'Python programming fundamentals for data work', 'Dr. Emily Wang', 10, 8, 'online', 'inactive', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(19, 'Data Visualization', 'Creating effective data visualizations', 'Dr. Emily Wang', 6, 8, 'hybrid', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(20, 'Statistical Analysis', 'Statistical methods and hypothesis testing', 'Dr. Emily Wang', 8, 8, 'in-person', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(21, 'Communication Skills', 'Effective verbal and written communication', 'Jennifer Martinez', 4, 9, 'online', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(22, 'Conflict Resolution', 'Handling difficult customer situations', 'Jennifer Martinez', 6, 9, 'in-person', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(23, 'Customer Satisfaction', 'Measuring and improving customer experience', 'Jennifer Martinez', 4, 9, 'hybrid', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(24, 'Budgeting Fundamentals', 'Creating and managing budgets', 'Robert Kim', 6, 10, 'hybrid', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(25, 'Financial Forecasting', 'Predicting future financial performance', 'Robert Kim', 8, 10, 'in-person', 'inactive', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(26, 'Investment Analysis', 'Evaluating investment opportunities', 'Robert Kim', 6, 10, 'online', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(27, 'Scrum Framework', 'Understanding Scrum principles and practices', 'Tom Anderson', 6, 11, 'online', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(28, 'Kanban Method', 'Visualizing workflow and limiting work in progress', 'Tom Anderson', 4, 11, 'hybrid', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(29, 'Agile Planning', 'Sprint planning and backlog management', 'Tom Anderson', 6, 11, 'in-person', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(30, 'Public Speaking', 'Overcoming fear and delivering effective presentations', 'Maria Garcia', 6, 12, 'in-person', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(31, 'Presentation Design', 'Creating visually appealing presentations', 'Maria Garcia', 4, 12, 'online', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(32, 'Business Writing', 'Writing clear and professional business documents', 'Maria Garcia', 5, 12, 'hybrid', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(33, 'ISO 9001 Standards', 'Understanding quality management systems', 'James Wilson', 8, 13, 'hybrid', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(34, 'Process Improvement', 'Identifying and implementing process improvements', 'James Wilson', 6, 13, 'in-person', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09'),
(35, 'Quality Assurance', 'Quality control and assurance techniques', 'James Wilson', 7, 13, 'online', 'active', 7, '2026-04-02 17:57:09', '2026-04-02 17:57:09');

-- --------------------------------------------------------

--
-- Table structure for table `ld_elearning_modules`
--

CREATE TABLE `ld_elearning_modules` (
  `ld_elearning_modules_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `content_url` varchar(500) DEFAULT NULL,
  `ld_courses_id` int(11) NOT NULL,
  `module_order` int(11) DEFAULT 0,
  `duration_minutes` int(11) DEFAULT 0,
  `created_by_user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ld_enrollments`
--

CREATE TABLE `ld_enrollments` (
  `ld_enrollment_id` int(11) NOT NULL,
  `employee_user_id` int(11) NOT NULL,
  `ld_courses_id` int(11) NOT NULL,
  `status` enum('enrolled','in-progress','completed','dropped') DEFAULT 'enrolled',
  `progress_percentage` decimal(5,2) DEFAULT 0.00,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ld_enrollments`
--

INSERT INTO `ld_enrollments` (`ld_enrollment_id`, `employee_user_id`, `ld_courses_id`, `status`, `progress_percentage`, `enrolled_at`, `completed_at`) VALUES
(1, 4, 1, 'in-progress', 50.00, '2026-03-22 05:56:59', NULL),
(2, 4, 2, 'enrolled', 0.00, '2026-03-22 05:56:59', NULL),
(3, 1, 1, 'completed', 100.00, '2026-03-01 01:00:00', NULL),
(4, 1, 6, 'in-progress', 75.00, '2026-03-15 02:30:00', NULL),
(5, 1, 12, 'enrolled', 0.00, '2026-04-01 06:00:00', NULL),
(6, 2, 2, 'completed', 100.00, '2026-02-20 03:00:00', NULL),
(7, 2, 8, 'completed', 100.00, '2026-03-10 05:15:00', NULL),
(8, 2, 15, 'in-progress', 60.00, '2026-03-25 08:45:00', NULL),
(9, 3, 3, 'completed', 100.00, '2026-02-15 00:30:00', NULL),
(10, 3, 10, 'in-progress', 45.00, '2026-03-20 04:00:00', NULL),
(11, 3, 18, 'enrolled', 0.00, '2026-04-02 01:15:00', NULL),
(12, 4, 4, 'completed', 100.00, '2026-03-05 06:20:00', NULL),
(13, 4, 11, 'in-progress', 80.00, '2026-03-12 03:30:00', NULL),
(14, 4, 22, 'enrolled', 0.00, '2026-04-03 02:00:00', NULL),
(15, 5, 5, 'completed', 100.00, '2026-02-28 07:45:00', NULL),
(16, 5, 13, 'completed', 100.00, '2026-03-08 01:30:00', NULL),
(17, 5, 19, 'in-progress', 30.00, '2026-03-28 05:20:00', NULL),
(18, 6, 7, 'completed', 100.00, '2026-03-03 02:15:00', NULL),
(19, 6, 14, 'in-progress', 55.00, '2026-03-18 06:30:00', NULL),
(20, 6, 25, 'enrolled', 0.00, '2026-04-01 03:45:00', NULL),
(21, 7, 9, 'completed', 100.00, '2026-02-25 04:00:00', NULL),
(22, 7, 16, 'completed', 100.00, '2026-03-14 00:45:00', NULL),
(23, 7, 23, 'in-progress', 90.00, '2026-03-22 07:30:00', NULL),
(24, 8, 17, 'completed', 100.00, '2026-03-07 05:00:00', NULL),
(25, 8, 24, 'in-progress', 70.00, '2026-03-26 02:15:00', NULL),
(26, 8, 28, 'enrolled', 0.00, '2026-04-02 06:30:00', NULL),
(27, 9, 20, 'completed', 100.00, '2026-03-11 01:20:00', NULL),
(28, 9, 26, 'in-progress', 40.00, '2026-03-29 03:00:00', NULL),
(29, 9, 30, 'enrolled', 0.00, '2026-04-03 00:30:00', NULL),
(30, 10, 21, 'completed', 100.00, '2026-03-09 06:45:00', NULL),
(31, 10, 27, 'in-progress', 25.00, '2026-03-30 04:15:00', NULL),
(32, 10, 31, 'enrolled', 0.00, '2026-04-01 08:00:00', NULL),
(33, 4, 6, 'enrolled', 0.00, '2026-04-02 18:44:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ld_training_programs`
--

CREATE TABLE `ld_training_programs` (
  `ld_training_programs_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `trainer` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `max_participants` int(11) DEFAULT 0,
  `status` enum('active','inactive','completed') DEFAULT 'active',
  `created_by_user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ld_training_programs`
--

INSERT INTO `ld_training_programs` (`ld_training_programs_id`, `title`, `description`, `trainer`, `start_date`, `end_date`, `max_participants`, `status`, `created_by_user_id`, `created_at`, `updated_at`) VALUES
(1, 'Yehs', 'dsadce', 'dsadacdafsdg', '2026-03-25', '2026-03-20', 23, 'active', 7, '2026-03-22 05:56:58', '2026-03-23 05:26:41'),
(2, 'Hello World Printing', 'A training program to learn various ways to print Hello World in different programming languages.', 'Traneroz', '2026-03-25', '2026-03-26', 50, 'active', 7, '2026-03-22 13:06:06', '2026-03-23 03:32:23'),
(3, 'Testing How', 'Mellownya', 'Chickem', '2026-03-23', '2026-03-23', 55, 'active', 7, '2026-03-23 13:15:35', '2026-03-23 13:15:35'),
(4, 'Advanced Leadership Development', 'Comprehensive leadership training for senior managers covering strategic thinking, team management, and organizational change.', 'Dr. Sarah Johnson', '2026-05-01', '2026-05-15', 25, 'active', 7, '2026-04-02 17:57:08', '2026-04-02 17:57:08'),
(5, 'Digital Marketing Mastery', 'Complete digital marketing course covering SEO, social media marketing, content strategy, and analytics.', 'Mike Chen', '2026-04-15', '2026-04-30', 30, 'active', 7, '2026-04-02 17:57:08', '2026-04-02 17:57:08'),
(6, 'Cybersecurity Fundamentals', 'Essential cybersecurity training for all employees covering data protection, phishing awareness, and security best practices.', 'Lisa Rodriguez', '2026-06-01', '2026-06-10', 50, 'active', 7, '2026-04-02 17:57:08', '2026-04-02 17:57:08'),
(7, 'Project Management Professional', 'PMP certification preparation course covering project lifecycle, risk management, and stakeholder communication.', 'David Thompson', '2026-07-01', '2026-07-20', 20, 'active', 7, '2026-04-02 17:57:08', '2026-04-02 17:57:08'),
(8, 'Data Analytics with Python', 'Hands-on data analytics training using Python, pandas, and visualization tools.', 'Dr. Emily Wang', '2026-05-20', '2026-06-05', 35, 'active', 7, '2026-04-02 17:57:08', '2026-04-02 17:57:08'),
(9, 'Customer Service Excellence', 'Advanced customer service training focusing on communication skills, conflict resolution, and customer satisfaction.', 'Jennifer Martinez', '2026-04-20', '2026-04-25', 40, 'active', 7, '2026-04-02 17:57:08', '2026-04-02 17:57:08'),
(10, 'Financial Planning for Managers', 'Financial literacy training for managers covering budgeting, forecasting, and financial decision making.', 'Robert Kim', '2026-08-01', '2026-08-10', 25, 'active', 7, '2026-04-02 17:57:08', '2026-04-02 17:57:08'),
(11, 'Agile Methodology Workshop', 'Practical agile training covering Scrum, Kanban, and agile project management techniques.', 'Tom Anderson', '2026-06-15', '2026-06-20', 30, 'active', 7, '2026-04-02 17:57:08', '2026-04-02 17:57:08'),
(12, 'Communication Skills Masterclass', 'Advanced communication training covering public speaking, presentation skills, and effective writing.', 'Maria Garcia', '2026-05-10', '2026-05-17', 45, 'active', 7, '2026-04-02 17:57:08', '2026-04-02 17:57:08'),
(13, 'Quality Management Systems', 'ISO 9001 and quality management training for process improvement and quality assurance.', 'James Wilson', '2026-09-01', '2026-09-15', 28, 'active', 7, '2026-04-02 17:57:08', '2026-04-02 17:57:08');

-- --------------------------------------------------------

--
-- Table structure for table `ld_virtual_sessions`
--

CREATE TABLE `ld_virtual_sessions` (
  `ld_virtual_sessions_id` int(11) NOT NULL,
  `ld_courses_id` int(11) NOT NULL,
  `session_url` varchar(500) NOT NULL,
  `scheduled_at` datetime NOT NULL,
  `duration_minutes` int(11) DEFAULT 60,
  `status` enum('scheduled','ongoing','completed','cancelled') DEFAULT 'scheduled',
  `created_by_user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pm_360_feedback`
--

CREATE TABLE `pm_360_feedback` (
  `feedback_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `evaluator_type` enum('Manager','Peer','Subordinate','Self') NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `category` enum('Communication','Teamwork','Leadership','Performance') NOT NULL,
  `comments` text DEFAULT NULL,
  `is_anonymous` tinyint(1) DEFAULT 0,
  `evaluation_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pm_appraisals`
--

CREATE TABLE `pm_appraisals` (
  `appraisal_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `review_period` enum('Quarterly','Annual','Mid-Year') NOT NULL,
  `goals_kpis` text NOT NULL,
  `performance_ratings` text NOT NULL,
  `manager_evaluation` text NOT NULL,
  `overall_score` decimal(5,2) NOT NULL,
  `comments` text DEFAULT NULL,
  `review_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pm_goals`
--

CREATE TABLE `pm_goals` (
  `goal_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `goal_title` varchar(255) NOT NULL,
  `kpi_name` varchar(255) NOT NULL,
  `target_value` decimal(10,2) NOT NULL DEFAULT 100.00,
  `current_progress` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('On Track','Delayed','Completed') NOT NULL DEFAULT 'On Track',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pm_reports`
--

CREATE TABLE `pm_reports` (
  `report_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `evaluation_period` enum('monthly','quarterly','yearly') NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `kpi_score` decimal(5,2) NOT NULL,
  `attendance_score` decimal(5,2) NOT NULL,
  `attendance_impact_notes` text DEFAULT NULL,
  `overall_rating_percent` decimal(5,2) NOT NULL,
  `overall_rating_5` tinyint(1) NOT NULL,
  `final_rating_percent` decimal(5,2) NOT NULL,
  `final_grade` varchar(50) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pm_training_recommendations`
--

CREATE TABLE `pm_training_recommendations` (
  `recommendation_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `skill_gaps` text NOT NULL,
  `training_program` varchar(255) NOT NULL,
  `training_type` enum('Online Course','Workshop','Seminar','Internal Training') NOT NULL,
  `priority_level` enum('High','Medium','Low') NOT NULL,
  `suggested_completion_date` date NOT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('Proposed','In Progress','Completed','Cancelled') NOT NULL DEFAULT 'Proposed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `employee_id` int(11) DEFAULT NULL,
  `payroll_period_id` int(11) NOT NULL,
  `type` enum('allowance','deduction') NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pr_employee_benefits`
--

CREATE TABLE `pr_employee_benefits` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `has_sss` tinyint(1) DEFAULT 1 COMMENT 'SSS Contribution enrollment',
  `has_philhealth` tinyint(1) DEFAULT 1 COMMENT 'PhilHealth enrollment',
  `has_pagibig` tinyint(1) DEFAULT 1 COMMENT 'Pag-IBIG enrollment',
  `sss_amount_override` decimal(10,2) DEFAULT NULL COMMENT 'Manual override if needed',
  `philhealth_amount_override` decimal(10,2) DEFAULT NULL,
  `pagibig_amount_override` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pr_employee_deductions`
--

CREATE TABLE `pr_employee_deductions` (
  `employee_deduction_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `deduction_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pr_employee_details`
--

CREATE TABLE `pr_employee_details` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `base_salary` decimal(12,2) NOT NULL COMMENT 'Monthly base salary - from Legal & Compliance',
  `position_type` enum('Admin','Teacher','Other') DEFAULT 'Admin' COMMENT 'Determines calculation method',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pr_employee_details`
--

INSERT INTO `pr_employee_details` (`id`, `employee_id`, `base_salary`, `position_type`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 30000.00, 'Admin', 1, '2026-03-29 08:58:44', '2026-03-29 08:58:44'),
(2, 2, 35000.00, 'Admin', 1, '2026-03-29 08:58:44', '2026-03-29 08:58:44'),
(3, 3, 32000.00, 'Admin', 1, '2026-03-29 08:58:44', '2026-03-29 08:58:44');

-- --------------------------------------------------------

--
-- Table structure for table `pr_payslips`
--

CREATE TABLE `pr_payslips` (
  `payslip_id` int(11) NOT NULL,
  `payroll_run_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
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
(0, 'Mar 1-15, 2026', '2026-01-01', '2026-01-15', '2026-03-20', 'open');

-- --------------------------------------------------------

--
-- Table structure for table `pr_position_deduction_rates`
--

CREATE TABLE `pr_position_deduction_rates` (
  `id` int(11) NOT NULL,
  `position_type` enum('Admin','Teacher','Other') NOT NULL,
  `absence_deduction_amount` decimal(10,2) NOT NULL COMMENT 'Per absence deduction',
  `late_per_minute_rate` decimal(5,2) DEFAULT 2.00 COMMENT 'Deduction per minute late',
  `late_per_hour_rate` decimal(5,2) DEFAULT 120.00 COMMENT 'Deduction per hour late',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pr_position_deduction_rates`
--

INSERT INTO `pr_position_deduction_rates` (`id`, `position_type`, `absence_deduction_amount`, `late_per_minute_rate`, `late_per_hour_rate`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 1020.00, 2.00, 120.00, 1, '2026-03-29 08:58:43', '2026-03-29 08:58:43'),
(2, 'Teacher', 1536.00, 2.00, 120.00, 1, '2026-03-29 08:58:43', '2026-03-29 08:58:43'),
(3, 'Other', 1000.00, 2.00, 120.00, 1, '2026-03-29 08:58:43', '2026-03-29 08:58:43');

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
-- Table structure for table `pr_teacher_loads`
--

CREATE TABLE `pr_teacher_loads` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `academic_year` varchar(10) NOT NULL COMMENT 'e.g., 2025-2026',
  `semester` enum('1st','2nd','Summer') NOT NULL,
  `qualification` enum('ProfEd','LPT','Masteral') NOT NULL COMMENT 'Teacher qualification',
  `total_units` decimal(5,2) NOT NULL COMMENT 'Total teaching units (per semester)',
  `created_by` varchar(100) DEFAULT NULL,
  `approved_by` varchar(100) DEFAULT NULL COMMENT 'College Coordinator approval',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Teacher course assignments and units - managed by College Coordinator';

--
-- Dumping data for table `pr_teacher_loads`
--

INSERT INTO `pr_teacher_loads` (`id`, `employee_id`, `academic_year`, `semester`, `qualification`, `total_units`, `created_by`, `approved_by`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-2026', '1st', 'Masteral', 30.00, 'college_coordinator', NULL, '2026-03-29 08:58:44', '2026-03-29 08:58:44'),
(2, 1, '2025-2026', '2nd', 'Masteral', 28.00, 'college_coordinator', NULL, '2026-03-29 08:58:44', '2026-03-29 08:58:44'),
(3, 3, '2025-2026', '1st', 'ProfEd', 18.00, 'rustom', NULL, '2026-03-29 09:04:36', '2026-03-29 09:04:36');

-- --------------------------------------------------------

--
-- Table structure for table `pr_teacher_qualification_rates`
--

CREATE TABLE `pr_teacher_qualification_rates` (
  `id` int(11) NOT NULL,
  `qualification` enum('ProfEd','LPT','Masteral') NOT NULL,
  `pay_per_unit` decimal(10,2) NOT NULL COMMENT 'PHP per unit',
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pr_teacher_qualification_rates`
--

INSERT INTO `pr_teacher_qualification_rates` (`id`, `qualification`, `pay_per_unit`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'ProfEd', 128.00, 'ProfEd/Normal Teacher - Default', 1, '2026-03-29 08:58:43', '2026-03-29 08:58:43'),
(2, 'LPT', 130.00, 'Licensed Professional Teacher', 1, '2026-03-29 08:58:43', '2026-03-29 08:58:43'),
(3, 'Masteral', 250.00, 'Teachers with Masteral Degree', 1, '2026-03-29 08:58:43', '2026-03-29 08:58:43');

-- --------------------------------------------------------

--
-- Table structure for table `skill_gap_analyses`
--

CREATE TABLE `skill_gap_analyses` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `required_skill` varchar(255) NOT NULL,
  `current_level` int(11) DEFAULT 1,
  `required_level` int(11) DEFAULT 5,
  `gap_description` text DEFAULT NULL,
  `recommendations` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 3, NULL, '2026-03-27', '0000-00-00 00:00:00', NULL, 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-27 07:17:19', '2026-03-27 08:23:29', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(2, 3, NULL, '2026-03-28', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-27 16:46:35', '2026-03-27 16:55:21', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(3, 1, NULL, '2026-03-28', '2026-03-28 09:03:23', '2026-03-28 09:03:31', 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-27 17:01:53', '2026-03-27 17:03:31', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(4, 2, NULL, '2026-03-28', '2026-03-28 09:04:55', '2026-03-28 09:16:05', 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-27 17:04:55', '2026-03-27 17:16:05', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(5, 6, NULL, '2026-03-28', '2026-03-28 10:59:33', NULL, 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-27 18:59:33', '2026-03-27 18:59:33', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(6, 7, NULL, '2026-03-28', '2026-03-28 11:56:06', NULL, 'MANUAL', 'PRESENT', NULL, 0, NULL, NULL, NULL, '2026-03-27 19:56:06', '2026-03-27 19:56:06', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(7, 8, NULL, '2026-03-28', '2026-03-28 17:36:20', '2026-03-28 17:37:39', 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-28 01:36:20', '2026-03-28 01:37:39', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(8, 4, NULL, '2026-03-28', '2026-03-28 15:44:30', NULL, 'QR', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-28 06:44:30', '2026-03-28 06:44:30', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL),
(9, 5, NULL, '2026-03-28', '2026-03-28 23:13:41', NULL, 'MANUAL', 'PENDING_APPROVAL', NULL, 0, NULL, NULL, NULL, '2026-03-28 07:13:41', '2026-03-28 07:13:41', NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('recruitment','payroll','time','compliance','workforce','employee','learning','performance','engagement_relations','exit','clinic') NOT NULL,
  `theme` enum('light','dark') DEFAULT 'light',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `theme`, `created_at`) VALUES
(1, 'hr_payroll', '$2y$10$lGdMJAD4KbQVmadxptk7xebMGEdpG6YsTk2UTvzB8yrgZ4T/m7.Ay', 'Russell Ike', 'payroll', 'light', '2026-03-06 21:13:06'),
(2, 'hr_recruitment', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'Administrator', 'recruitment', 'light', '2026-03-07 02:46:33'),
(3, 'hr_time', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'Admin', 'time', 'light', '2026-03-07 02:47:07'),
(4, 'hr_employee', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'someone', 'employee', 'light', '2026-03-07 02:47:55'),
(5, 'hr_compliance', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'comply', 'compliance', 'light', '2026-03-07 02:48:19'),
(6, 'hr_workforce', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'force', 'workforce', 'light', '2026-03-07 02:48:43'),
(7, 'hr_learning', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'HR Learning Admin', 'learning', 'light', '2026-03-07 02:49:22'),
(8, 'hr_performance', '$2y$10$/Q0HsL9Cy/IlnwROoGHaeOcKQ.0wFpu43/.Zi01cfJ81fUO1t9vu2', 'Perform', 'performance', 'light', '2026-03-07 02:49:46'),
(9, 'hr_engagement', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'engage', 'engagement_relations', 'light', '2026-03-07 02:50:37'),
(10, 'hr_exit', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'exit', 'exit', 'light', '2026-03-07 02:51:04'),
(11, 'hr_clinic', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'clinic', 'clinic', 'light', '2026-03-12 08:20:55'),
(12, 'learning_admin', '\\.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Learning Administrator', 'learning', 'light', '2026-03-23 12:35:30'),
(37, 'lisa.anderson', '$2y$10$hashedpassword1', 'Lisa Anderson', 'employee', 'light', '2026-04-01 19:10:44'),
(38, 'kevin.thompson', '$2y$10$hashedpassword2', 'Kevin Thompson', 'employee', 'light', '2026-04-01 19:10:44'),
(39, 'rachel.garcia', '$2y$10$hashedpassword3', 'Rachel Garcia', 'employee', 'light', '2026-04-01 19:10:44'),
(40, 'michael.brown', '$2y$10$hashedpassword4', 'Michael Brown', 'employee', 'light', '2026-04-01 19:10:44'),
(41, 'amanda.wilson', '$2y$10$hashedpassword5', 'Amanda Wilson', 'employee', 'light', '2026-04-01 19:10:44'),
(42, 'christopher.lee', '$2y$10$hashedpassword6', 'Christopher Lee', 'employee', 'light', '2026-04-01 19:10:44'),
(43, 'jennifer.taylor', '$2y$10$hashedpassword7', 'Jennifer Taylor', 'employee', 'light', '2026-04-01 19:10:44'),
(44, 'daniel.martinez', '$2y$10$hashedpassword8', 'Daniel Martinez', 'employee', 'light', '2026-04-01 19:10:44'),
(45, 'nicole.rodriguez', '$2y$10$hashedpassword9', 'Nicole Rodriguez', 'employee', 'light', '2026-04-01 19:10:44'),
(46, 'steven.clark', '$2y$10$hashedpassword10', 'Steven Clark', 'employee', 'light', '2026-04-01 19:10:44'),
(47, 'michelle.lewis', '$2y$10$hashedpassword11', 'Michelle Lewis', 'employee', 'light', '2026-04-01 19:10:44'),
(48, 'brian.walker', '$2y$10$hashedpassword12', 'Brian Walker', 'employee', 'light', '2026-04-01 19:10:44'),
(49, 'alexander.wright', '$2y$10$hashedpassword13', 'Alexander Wright', 'employee', 'light', '2026-04-01 19:14:12'),
(50, 'sophia.martinez', '$2y$10$hashedpassword14', 'Sophia Martinez', 'employee', 'light', '2026-04-01 19:14:12'),
(51, 'ethan.johnson', '$2y$10$hashedpassword15', 'Ethan Johnson', 'employee', 'light', '2026-04-01 19:14:12'),
(52, 'olivia.davis', '$2y$10$hashedpassword16', 'Olivia Davis', 'employee', 'light', '2026-04-01 19:14:12'),
(53, 'mason.wilson', '$2y$10$hashedpassword17', 'Mason Wilson', 'employee', 'light', '2026-04-01 19:14:12'),
(54, 'ava.garcia', '$2y$10$hashedpassword18', 'Ava Garcia', 'employee', 'light', '2026-04-01 19:14:12'),
(55, 'logan.brown', '$2y$10$hashedpassword19', 'Logan Brown', 'employee', 'light', '2026-04-01 19:14:12'),
(56, 'isabella.lee', '$2y$10$hashedpassword20', 'Isabella Lee', 'employee', 'light', '2026-04-01 19:14:12'),
(57, 'lucas.taylor', '$2y$10$hashedpassword21', 'Lucas Taylor', 'employee', 'light', '2026-04-01 19:14:12'),
(58, 'mia.anderson', '$2y$10$hashedpassword22', 'Mia Anderson', 'employee', 'light', '2026-04-01 19:14:12'),
(59, 'jackson.white', '$2y$10$hashedpassword23', 'Jackson White', 'employee', 'light', '2026-04-01 19:14:12'),
(60, 'charlotte.harris', '$2y$10$hashedpassword24', 'Charlotte Harris', 'employee', 'light', '2026-04-01 19:14:12'),
(61, 'aiden.clark', '$2y$10$hashedpassword25', 'Aiden Clark', 'employee', 'light', '2026-04-01 19:14:12'),
(62, 'harper.lewis', '$2y$10$hashedpassword26', 'Harper Lewis', 'employee', 'light', '2026-04-01 19:14:12'),
(63, 'elijah.robinson', '$2y$10$hashedpassword27', 'Elijah Robinson', 'employee', 'light', '2026-04-01 19:14:12'),
(64, 'amelia.walker', '$2y$10$hashedpassword28', 'Amelia Walker', 'employee', 'light', '2026-04-01 19:14:12'),
(65, 'benjamin.hall', '$2y$10$hashedpassword29', 'Benjamin Hall', 'employee', 'light', '2026-04-01 19:14:12'),
(66, 'evelyn.young', '$2y$10$hashedpassword30', 'Evelyn Young', 'employee', 'light', '2026-04-01 19:14:12'),
(67, 'sebastian.king', '$2y$10$hashedpassword31', 'Sebastian King', 'employee', 'light', '2026-04-01 19:14:12'),
(68, 'abigail.wright', '$2y$10$hashedpassword32', 'Abigail Wright', 'employee', 'light', '2026-04-01 19:14:12'),
(69, 'matthew.lopez', '$2y$10$hashedpassword33', 'Matthew Lopez', 'employee', 'light', '2026-04-01 19:14:12'),
(70, 'ella.hill', '$2y$10$hashedpassword34', 'Ella Hill', 'employee', 'light', '2026-04-01 19:14:12'),
(71, 'david.green', '$2y$10$hashedpassword35', 'David Green', 'employee', 'light', '2026-04-01 19:14:12'),
(72, 'sofia.adams', '$2y$10$hashedpassword36', 'Sofia Adams', 'employee', 'light', '2026-04-01 19:14:12'),
(73, 'joseph.baker', '$2y$10$hashedpassword37', 'Joseph Baker', 'employee', 'light', '2026-04-01 19:14:12'),
(74, 'grace.nelson', '$2y$10$hashedpassword38', 'Grace Nelson', 'employee', 'light', '2026-04-01 19:14:12'),
(75, 'samuel.carter', '$2y$10$hashedpassword39', 'Samuel Carter', 'employee', 'light', '2026-04-01 19:14:12'),
(76, 'chloe.mitchell', '$2y$10$hashedpassword40', 'Chloe Mitchell', 'employee', 'light', '2026-04-01 19:14:12'),
(77, 'henry.perez', '$2y$10$hashedpassword41', 'Henry Perez', 'employee', 'light', '2026-04-01 19:14:12'),
(78, 'lily.roberts', '$2y$10$hashedpassword42', 'Lily Roberts', 'employee', 'light', '2026-04-01 19:14:12'),
(79, 'jack.turner', '$2y$10$hashedpassword43', 'Jack Turner', 'employee', 'light', '2026-04-01 19:14:12'),
(80, 'zoe.phillips', '$2y$10$hashedpassword44', 'Zoe Phillips', 'employee', 'light', '2026-04-01 19:14:12'),
(81, 'owen.campbell', '$2y$10$hashedpassword45', 'Owen Campbell', 'employee', 'light', '2026-04-01 19:14:12'),
(82, 'nora.parker', '$2y$10$hashedpassword46', 'Nora Parker', 'employee', 'light', '2026-04-01 19:14:12'),
(83, 'wyatt.evans', '$2y$10$hashedpassword47', 'Wyatt Evans', 'employee', 'light', '2026-04-01 19:14:12'),
(84, 'hannah.edwards', '$2y$10$hashedpassword48', 'Hannah Edwards', 'employee', 'light', '2026-04-01 19:14:12'),
(85, 'carter.collins', '$2y$10$hashedpassword49', 'Carter Collins', 'employee', 'light', '2026-04-01 19:14:12'),
(86, 'madison.stewart', '$2y$10$hashedpassword50', 'Madison Stewart', 'employee', 'light', '2026-04-01 19:14:12'),
(87, 'jayden.sanchez', '$2y$10$hashedpassword51', 'Jayden Sanchez', 'employee', 'light', '2026-04-01 19:14:12'),
(88, 'victoria.morris', '$2y$10$hashedpassword52', 'Victoria Morris', 'employee', 'light', '2026-04-01 19:14:12'),
(89, 'dylan.rogers', '$2y$10$hashedpassword53', 'Dylan Rogers', 'employee', 'light', '2026-04-01 19:14:12'),
(90, 'penelope.reed', '$2y$10$hashedpassword54', 'Penelope Reed', 'employee', 'light', '2026-04-01 19:14:12'),
(91, 'grayson.cook', '$2y$10$hashedpassword55', 'Grayson Cook', 'employee', 'light', '2026-04-01 19:14:12'),
(92, 'riley.morgan', '$2y$10$hashedpassword56', 'Riley Morgan', 'employee', 'light', '2026-04-01 19:14:12'),
(93, 'leah.bell', '$2y$10$hashedpassword57', 'Leah Bell', 'employee', 'light', '2026-04-01 19:14:12'),
(94, 'julian.murphy', '$2y$10$hashedpassword58', 'Julian Murphy', 'employee', 'light', '2026-04-01 19:14:12'),
(95, 'savannah.bailey', '$2y$10$hashedpassword59', 'Savannah Bailey', 'employee', 'light', '2026-04-01 19:14:12'),
(96, 'levi.rivera', '$2y$10$hashedpassword60', 'Levi Rivera', 'employee', 'light', '2026-04-01 19:14:12'),
(97, 'brooklyn.cooper', '$2y$10$hashedpassword61', 'Brooklyn Cooper', 'employee', 'light', '2026-04-01 19:14:12'),
(98, 'nathan.richardson', '$2y$10$hashedpassword62', 'Nathan Richardson', 'employee', 'light', '2026-04-01 19:14:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `employee_contributions`
--
ALTER TABLE `employee_contributions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_employee_contribution` (`employee_id`,`contribution_type`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `ep_employee_documents`
--
ALTER TABLE `ep_employee_documents`
  ADD PRIMARY KEY (`approval_id`),
  ADD KEY `approver_id` (`approver_id`),
  ADD KEY `department` (`department`),
  ADD KEY `submit_by` (`submit_by`);

--
-- Indexes for table `ep_online_meetings`
--
ALTER TABLE `ep_online_meetings`
  ADD PRIMARY KEY (`meetings_id`);

--
-- Indexes for table `exit_archive`
--
ALTER TABLE `exit_archive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_archive_type` (`archive_type`),
  ADD KEY `idx_original_id` (`original_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_archived_by` (`archived_by`),
  ADD KEY `idx_archived_at` (`archived_at`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_restored` (`restored`);

--
-- Indexes for table `exit_documents`
--
ALTER TABLE `exit_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_document_employee` (`employee_id`),
  ADD KEY `fk_document_uploaded_by` (`uploaded_by`);

--
-- Indexes for table `exit_employee_settlements`
--
ALTER TABLE `exit_employee_settlements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_settlement_employee` (`employee_id`),
  ADD KEY `fk_settlement_resignation` (`resignation_id`),
  ADD KEY `fk_settlement_approved_by` (`approved_by`),
  ADD KEY `fk_settlement_created_by` (`created_by`);

--
-- Indexes for table `exit_interviews`
--
ALTER TABLE `exit_interviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_interview_employee` (`employee_id`),
  ADD KEY `fk_interview_interviewer` (`interviewer_id`);

--
-- Indexes for table `exit_knowledge_transfer_items`
--
ALTER TABLE `exit_knowledge_transfer_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_item_plan` (`plan_id`);

--
-- Indexes for table `exit_knowledge_transfer_plans`
--
ALTER TABLE `exit_knowledge_transfer_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_transfer_employee` (`employee_id`),
  ADD KEY `fk_transfer_successor` (`successor_id`),
  ADD KEY `fk_transfer_created_by` (`created_by`);

--
-- Indexes for table `exit_resignations`
--
ALTER TABLE `exit_resignations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_resignation_employee` (`employee_id`),
  ADD KEY `fk_resignation_submitted_by` (`submitted_by`),
  ADD KEY `fk_resignation_approved_by` (`approved_by`),
  ADD KEY `fk_resignation_preclearance_desk` (`preclearance_desk_person`);

--
-- Indexes for table `exit_surveys`
--
ALTER TABLE `exit_surveys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_survey_created_by` (`created_by`);

--
-- Indexes for table `exit_survey_answers`
--
ALTER TABLE `exit_survey_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_answer_response` (`response_id`),
  ADD KEY `fk_answer_question` (`question_id`);

--
-- Indexes for table `exit_survey_questions`
--
ALTER TABLE `exit_survey_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_question_survey` (`survey_id`);

--
-- Indexes for table `exit_survey_responses`
--
ALTER TABLE `exit_survey_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_response_survey` (`survey_id`),
  ADD KEY `fk_response_employee` (`employee_id`);

--
-- Indexes for table `ld_archive`
--
ALTER TABLE `ld_archive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `archived_by` (`archived_by`),
  ADD KEY `restored_by` (`restored_by`),
  ADD KEY `original_created_by` (`original_created_by`),
  ADD KEY `archive_type` (`archive_type`),
  ADD KEY `archived_at` (`archived_at`),
  ADD KEY `restored` (`restored`),
  ADD KEY `idx_restored` (`restored`);

--
-- Indexes for table `ld_certification`
--
ALTER TABLE `ld_certification`
  ADD PRIMARY KEY (`ld_certification_id`),
  ADD KEY `ld_certification_ibfk_1` (`employee_id`),
  ADD KEY `ld_certification_ibfk_2` (`ld_courses_id`),
  ADD KEY `ld_certification_ibfk_3` (`issued_by_user_id`);

--
-- Indexes for table `ld_courses`
--
ALTER TABLE `ld_courses`
  ADD PRIMARY KEY (`ld_courses_id`),
  ADD KEY `ld_courses_ibfk_1` (`ld_training_programs_id`),
  ADD KEY `ld_courses_ibfk_2` (`created_by_user_id`);

--
-- Indexes for table `ld_elearning_modules`
--
ALTER TABLE `ld_elearning_modules`
  ADD PRIMARY KEY (`ld_elearning_modules_id`),
  ADD KEY `ld_elearning_modules_ibfk_1` (`ld_courses_id`),
  ADD KEY `ld_elearning_modules_ibfk_2` (`created_by_user_id`);

--
-- Indexes for table `ld_enrollments`
--
ALTER TABLE `ld_enrollments`
  ADD PRIMARY KEY (`ld_enrollment_id`),
  ADD UNIQUE KEY `unique_enrollment` (`employee_user_id`,`ld_courses_id`),
  ADD KEY `ld_enrollments_ibfk_2` (`ld_courses_id`);

--
-- Indexes for table `ld_training_programs`
--
ALTER TABLE `ld_training_programs`
  ADD PRIMARY KEY (`ld_training_programs_id`),
  ADD KEY `created_by` (`created_by_user_id`);

--
-- Indexes for table `ld_virtual_sessions`
--
ALTER TABLE `ld_virtual_sessions`
  ADD PRIMARY KEY (`ld_virtual_sessions_id`),
  ADD KEY `ld_virtual_sessions_ibfk_1` (`ld_courses_id`),
  ADD KEY `ld_virtual_sessions_ibfk_2` (`created_by_user_id`);

--
-- Indexes for table `pm_360_feedback`
--
ALTER TABLE `pm_360_feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `pm_appraisals`
--
ALTER TABLE `pm_appraisals`
  ADD PRIMARY KEY (`appraisal_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `pm_goals`
--
ALTER TABLE `pm_goals`
  ADD PRIMARY KEY (`goal_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `pm_reports`
--
ALTER TABLE `pm_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `pm_training_recommendations`
--
ALTER TABLE `pm_training_recommendations`
  ADD PRIMARY KEY (`recommendation_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `pr_deductions`
--
ALTER TABLE `pr_deductions`
  ADD PRIMARY KEY (`deduction_id`);

--
-- Indexes for table `pr_employee_adjustments`
--
ALTER TABLE `pr_employee_adjustments`
  ADD PRIMARY KEY (`adjustment_id`),
  ADD KEY `fk_emp_adj_employee` (`employee_id`);

--
-- Indexes for table `pr_employee_benefits`
--
ALTER TABLE `pr_employee_benefits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD KEY `idx_employee_id` (`employee_id`);

--
-- Indexes for table `pr_employee_deductions`
--
ALTER TABLE `pr_employee_deductions`
  ADD PRIMARY KEY (`employee_deduction_id`),
  ADD KEY `fk_emp_ded_employee` (`employee_id`);

--
-- Indexes for table `pr_employee_details`
--
ALTER TABLE `pr_employee_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_position_type` (`position_type`);

--
-- Indexes for table `pr_payslips`
--
ALTER TABLE `pr_payslips`
  ADD PRIMARY KEY (`payslip_id`),
  ADD KEY `fk_payslips_employee` (`employee_id`);

--
-- Indexes for table `pr_payslip_items`
--
ALTER TABLE `pr_payslip_items`
  ADD PRIMARY KEY (`payslip_item_id`);

--
-- Indexes for table `pr_periods`
--
ALTER TABLE `pr_periods`
  ADD PRIMARY KEY (`period_id`);

--
-- Indexes for table `pr_position_deduction_rates`
--
ALTER TABLE `pr_position_deduction_rates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `position_type` (`position_type`),
  ADD KEY `idx_position_type` (`position_type`);

--
-- Indexes for table `pr_runs`
--
ALTER TABLE `pr_runs`
  ADD PRIMARY KEY (`run_id`);

--
-- Indexes for table `pr_statutory_contributions`
--
ALTER TABLE `pr_statutory_contributions`
  ADD PRIMARY KEY (`contribution_id`);

--
-- Indexes for table `pr_tax_tables`
--
ALTER TABLE `pr_tax_tables`
  ADD PRIMARY KEY (`tax_id`);

--
-- Indexes for table `pr_teacher_loads`
--
ALTER TABLE `pr_teacher_loads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_teacher_semester` (`employee_id`,`academic_year`,`semester`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_academic_year` (`academic_year`);

--
-- Indexes for table `pr_teacher_qualification_rates`
--
ALTER TABLE `pr_teacher_qualification_rates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `qualification` (`qualification`),
  ADD KEY `idx_qualification` (`qualification`);

--
-- Indexes for table `skill_gap_analyses`
--
ALTER TABLE `skill_gap_analyses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `created_by` (`created_by`);

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
-- AUTO_INCREMENT for table `ep_employee_documents`
--
ALTER TABLE `ep_employee_documents`
  MODIFY `approval_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ep_online_meetings`
--
ALTER TABLE `ep_online_meetings`
  MODIFY `meetings_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exit_archive`
--
ALTER TABLE `exit_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exit_resignations`
--
ALTER TABLE `exit_resignations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `exit_survey_answers`
--
ALTER TABLE `exit_survey_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `exit_survey_responses`
--
ALTER TABLE `exit_survey_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ld_archive`
--
ALTER TABLE `ld_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ld_certification`
--
ALTER TABLE `ld_certification`
  MODIFY `ld_certification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `ld_courses`
--
ALTER TABLE `ld_courses`
  MODIFY `ld_courses_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `ld_elearning_modules`
--
ALTER TABLE `ld_elearning_modules`
  MODIFY `ld_elearning_modules_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ld_enrollments`
--
ALTER TABLE `ld_enrollments`
  MODIFY `ld_enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `ld_training_programs`
--
ALTER TABLE `ld_training_programs`
  MODIFY `ld_training_programs_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `ld_virtual_sessions`
--
ALTER TABLE `ld_virtual_sessions`
  MODIFY `ld_virtual_sessions_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pm_360_feedback`
--
ALTER TABLE `pm_360_feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pm_appraisals`
--
ALTER TABLE `pm_appraisals`
  MODIFY `appraisal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pm_goals`
--
ALTER TABLE `pm_goals`
  MODIFY `goal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pm_reports`
--
ALTER TABLE `pm_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pm_training_recommendations`
--
ALTER TABLE `pm_training_recommendations`
  MODIFY `recommendation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `skill_gap_analyses`
--
ALTER TABLE `skill_gap_analyses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `exit_resignations`
--
ALTER TABLE `exit_resignations`
  ADD CONSTRAINT `fk_resignation_preclearance_desk` FOREIGN KEY (`preclearance_desk_person`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ld_archive`
--
ALTER TABLE `ld_archive`
  ADD CONSTRAINT `ld_archive_ibfk_2` FOREIGN KEY (`restored_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ld_archive_ibfk_3` FOREIGN KEY (`original_created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ld_certification`
--
ALTER TABLE `ld_certification`
  ADD CONSTRAINT `ld_certification_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ld_certification_ibfk_2` FOREIGN KEY (`ld_courses_id`) REFERENCES `ld_courses` (`ld_courses_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ld_certification_ibfk_3` FOREIGN KEY (`issued_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ld_courses`
--
ALTER TABLE `ld_courses`
  ADD CONSTRAINT `ld_courses_ibfk_1` FOREIGN KEY (`ld_training_programs_id`) REFERENCES `ld_training_programs` (`ld_training_programs_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ld_courses_ibfk_2` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ld_elearning_modules`
--
ALTER TABLE `ld_elearning_modules`
  ADD CONSTRAINT `ld_elearning_modules_ibfk_1` FOREIGN KEY (`ld_courses_id`) REFERENCES `ld_courses` (`ld_courses_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ld_elearning_modules_ibfk_2` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ld_enrollments`
--
ALTER TABLE `ld_enrollments`
  ADD CONSTRAINT `ld_enrollments_ibfk_1` FOREIGN KEY (`employee_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ld_enrollments_ibfk_2` FOREIGN KEY (`ld_courses_id`) REFERENCES `ld_courses` (`ld_courses_id`) ON DELETE CASCADE;

--
-- Constraints for table `ld_training_programs`
--
ALTER TABLE `ld_training_programs`
  ADD CONSTRAINT `ld_training_programs_ibfk_1` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ld_virtual_sessions`
--
ALTER TABLE `ld_virtual_sessions`
  ADD CONSTRAINT `ld_virtual_sessions_ibfk_1` FOREIGN KEY (`ld_courses_id`) REFERENCES `ld_courses` (`ld_courses_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ld_virtual_sessions_ibfk_2` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pm_360_feedback`
--
ALTER TABLE `pm_360_feedback`
  ADD CONSTRAINT `pm_360_feedback_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `pm_appraisals`
--
ALTER TABLE `pm_appraisals`
  ADD CONSTRAINT `pm_appraisals_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `pm_goals`
--
ALTER TABLE `pm_goals`
  ADD CONSTRAINT `pm_goals_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `pm_reports`
--
ALTER TABLE `pm_reports`
  ADD CONSTRAINT `performance_reports_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `pm_training_recommendations`
--
ALTER TABLE `pm_training_recommendations`
  ADD CONSTRAINT `pm_training_recommendations_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `skill_gap_analyses`
--
ALTER TABLE `skill_gap_analyses`
  ADD CONSTRAINT `skill_gap_analyses_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `skill_gap_analyses_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
