-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 15, 2026 at 05:24 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `created_by`, `created_at`) VALUES
(1, 'Welcome to EER System', 'Welcome to our new Employee Engagement and Relations system. This platform will help us build a stronger, more connected workplace.', 2, '2026-02-04 23:35:52'),
(2, 'Annual Company Outing - Save the Date', 'Please mark your calendars for our upcoming annual company outing on March 15, 2026. More details will be shared soon!', 2, '2026-02-01 23:35:52'),
(3, 'New Office Facilities Opening', 'We are excited to announce the opening of our new office facilities with modern amenities including a gym and cafeteria.', 2, '2026-01-28 23:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `announcement_reads`
--

CREATE TABLE `announcement_reads` (
  `id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `read_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement_reads`
--

INSERT INTO `announcement_reads` (`id`, `announcement_id`, `employee_id`, `read_at`) VALUES
(1, 1, 3, '2026-02-03 23:35:52'),
(2, 1, 4, '2026-02-04 21:35:52'),
(3, 2, 5, '2026-02-02 23:35:52'),
(4, 2, 6, '2026-02-04 20:35:52'),
(5, 1, 1, '2026-02-05 07:36:36'),
(6, 3, 1, '2026-02-05 07:36:36'),
(7, 2, 1, '2026-02-05 07:36:36'),
(8, 2, 2, '2026-02-05 07:43:03'),
(9, 3, 2, '2026-02-05 07:43:03'),
(10, 1, 2, '2026-02-05 07:43:03'),
(11, 3, 3, '2026-02-05 07:56:49'),
(12, 2, 3, '2026-02-05 07:56:49');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `performed_by` int(11) DEFAULT NULL,
  `target_type` varchar(100) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `performed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `action`, `performed_by`, `target_type`, `target_id`, `details`, `performed_at`) VALUES
(1, 'LOGIN', 2, 'employees', 2, 'HR Manager logged in', '2026-02-04 22:35:52'),
(2, 'VIEW_ANNOUNCEMENTS', 3, 'announcements', NULL, 'Employee viewed announcements', '2026-02-04 21:35:52'),
(3, 'FILE_GRIEVANCE', 5, 'grievances', 1, 'Employee filed new grievance', '2026-01-27 23:35:52'),
(4, 'UPDATE_GRIEVANCE', 8, 'grievances', 2, 'HR updated grievance status', '2026-02-01 23:35:52'),
(5, 'LOGIN', 3, 'employees', 3, 'Employee logged in', '2026-02-04 23:05:52'),
(6, 'REGISTER_EVENT', 3, 'event_registrations', 1, 'Employee registered for event', '2026-02-02 23:35:52'),
(7, 'SUBMIT_FEEDBACK', 4, 'feedback', 3, 'Employee submitted feedback', '2026-02-02 23:35:52'),
(8, 'LOGIN', 8, 'employees', 8, 'HR Manager logged in', '2026-02-04 21:35:52'),
(9, 'LOGIN', 1, 'employees', 1, 'User logged in successfully', '2026-03-15 05:22:02'),
(10, 'LOGIN', 1, 'employees', 1, 'User logged in successfully', '2026-03-15 05:32:33'),
(11, 'LOGIN', 1, 'employees', 1, 'User logged in successfully', '2026-03-15 05:33:52'),
(12, 'LOGIN', 1, 'employees', 1, 'User logged in successfully', '2026-03-15 05:36:23'),
(13, 'LOGIN', 1, 'employees', 1, 'User logged in successfully', '2026-03-15 05:40:59'),
(14, 'LOGIN', 1, 'employees', 1, 'User logged in successfully', '2026-03-15 09:45:53'),
(15, 'LOGIN', 1, 'employees', 1, 'User logged in successfully', '2026-03-15 09:46:37');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`) VALUES
(1, 'Human Resources'),
(2, 'Information Technology'),
(3, 'Operations'),
(4, 'Sales & Marketing'),
(5, 'Finance');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('employee','manager','hr','admin') NOT NULL DEFAULT 'employee',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `theme` enum('light','dark') NOT NULL DEFAULT 'light'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `user_id`, `username`, `name`, `department_id`, `email`, `password`, `role`, `status`, `theme`) VALUES
(1, NULL, 'admin', 'Francisco', NULL, 'admin@company.com', 'admin123', 'admin', 'active', 'light'),
(2, NULL, 'marissa.garcia@company.com', 'Marissa Garcia', 1, 'marissa.garcia@company.com', 'password', 'hr', 'active', 'light'),
(3, NULL, 'juan.delacruz@company.com', 'Juan Dela Cruz', 2, 'juan.delacruz@company.com', 'password', 'employee', 'active', 'light'),
(4, NULL, 'maria.santos@company.com', 'Maria Santos', 3, 'maria.santos@company.com', 'password', 'employee', 'active', 'light'),
(5, NULL, 'carlos.rodriguez@company.com', 'Carlos Rodriguez', 2, 'carlos.rodriguez@company.com', 'password', 'employee', 'active', 'light'),
(6, NULL, 'ana.luna@company.com', 'Ana Luna', 4, 'ana.luna@company.com', 'password', 'employee', 'active', 'light'),
(7, NULL, 'roberto.cruz@company.com', 'Roberto Cruz', 5, 'roberto.cruz@company.com', 'password', 'employee', 'active', 'light'),
(8, NULL, 'patricia.reyes@company.com', 'Patricia Reyes', 1, 'patricia.reyes@company.com', 'password', 'hr', 'active', 'light'),
(9, NULL, 'miguel.santos@company.com', 'Miguel Santos', 3, 'miguel.santos@company.com', 'password', 'employee', 'active', 'light'),
(10, NULL, 'sofia.mendoza@company.com', 'Sofia Mendoza', 4, 'sofia.mendoza@company.com', 'password', 'employee', 'active', 'light');

-- --------------------------------------------------------

--
-- Table structure for table `engagement_surveys`
--

CREATE TABLE `engagement_surveys` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `engagement_surveys`
--

INSERT INTO `engagement_surveys` (`id`, `title`, `description`, `created_by`, `created_at`) VALUES
(1, 'Employee Satisfaction Survey 2026', 'Help us understand your level of satisfaction with your work environment, management, and career development opportunities.', 2, '2026-02-04 23:35:52'),
(2, 'Workplace Culture Assessment', 'We value your feedback on our workplace culture and team dynamics. Your honest responses will help us improve.', 2, '2026-01-30 23:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `location`, `created_by`, `created_at`) VALUES
(1, 'Team Building Activity', 'Fun team building games and activities to strengthen our team bonds.', '2026-02-20', 'Main Conference Hall', 2, '2026-02-04 23:35:52'),
(2, 'Wellness Workshop', 'Learn about mental health and wellness from industry experts.', '2026-03-05', 'Training Room A', 2, '2026-02-04 23:35:52'),
(3, 'Annual Awards Ceremony', 'Celebrate outstanding achievements and recognize top performers.', '2026-03-20', 'Grand Ballroom', 2, '2026-02-04 23:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `event_registrations`
--

CREATE TABLE `event_registrations` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `registered_at` datetime DEFAULT current_timestamp(),
  `attended` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_registrations`
--

INSERT INTO `event_registrations` (`id`, `event_id`, `employee_id`, `registered_at`, `attended`) VALUES
(1, 1, 3, '2026-02-02 23:35:52', 0),
(2, 1, 4, '2026-02-02 23:35:52', 0),
(3, 1, 5, '2026-02-03 23:35:52', 0),
(4, 2, 6, '2026-02-03 23:35:52', 0),
(5, 3, 3, '2026-02-04 23:35:52', 0),
(6, 1, 1, '2026-02-05 17:06:51', 0);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `feedback_text` text NOT NULL,
  `is_anonymous` tinyint(1) DEFAULT 0,
  `status` enum('new','reviewed','responded') DEFAULT 'new',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `employee_id`, `feedback_text`, `is_anonymous`, `status`, `created_at`) VALUES
(1, 3, 'The new office layout is great! It promotes better collaboration.', 0, 'reviewed', '2026-01-30 23:35:52'),
(2, 5, 'I would appreciate more professional development opportunities.', 1, 'new', '2026-02-01 23:35:52'),
(3, 4, 'Communication between departments needs improvement.', 1, 'responded', '2026-02-02 23:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `grievances`
--

CREATE TABLE `grievances` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('open','in-progress','resolved','closed') DEFAULT 'open',
  `priority` enum('low','normal','high','critical') DEFAULT 'normal',
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grievances`
--

INSERT INTO `grievances` (`id`, `employee_id`, `subject`, `description`, `status`, `priority`, `assigned_to`, `created_at`) VALUES
(1, 5, 'Overtime Pay Discrepancy', 'I believe my overtime payment for January was incorrect. I worked 20 hours of overtime but was only paid for 15.', 'open', 'high', 2, '2026-01-27 23:35:52'),
(2, 7, 'Unfair Shift Assignment', 'The shift scheduling seems biased. I have been assigned the night shift for the past month without consideration for my request.', 'in-progress', 'normal', 8, '2026-01-30 23:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `grievance_actions`
--

CREATE TABLE `grievance_actions` (
  `id` int(11) NOT NULL,
  `grievance_id` int(11) NOT NULL,
  `action_taken` text NOT NULL,
  `action_by` int(11) NOT NULL,
  `action_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grievance_actions`
--

INSERT INTO `grievance_actions` (`id`, `grievance_id`, `action_taken`, `action_by`, `action_date`) VALUES
(1, 2, 'Reviewed the grievance with the employee. Confirmed the shift assignment issue. Will investigate scheduling practices.', 8, '2026-02-01 23:35:52'),
(2, 2, 'Spoke with the operations manager. New fair shift rotation policy will be implemented starting next month.', 8, '2026-02-03 23:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `recognitions`
--

CREATE TABLE `recognitions` (
  `id` int(11) NOT NULL,
  `from_employee_id` int(11) NOT NULL,
  `to_employee_id` int(11) NOT NULL,
  `type` enum('peer','manager') NOT NULL,
  `message` text DEFAULT NULL,
  `reward_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recognitions`
--

INSERT INTO `recognitions` (`id`, `from_employee_id`, `to_employee_id`, `type`, `message`, `reward_id`, `created_at`) VALUES
(1, 3, 5, 'peer', 'Carlos did an amazing job helping me troubleshoot the database issue. His technical expertise and patience were invaluable!', 1, '2026-01-31 23:35:52'),
(2, 4, 6, 'peer', 'Ana went above and beyond in the client presentation. Her confidence and knowledge impressed everyone!', 2, '2026-02-02 23:35:52'),
(3, 2, 3, 'manager', 'Juan has shown exceptional leadership this quarter. His innovative approach to problem-solving has benefited the entire team.', 3, '2026-02-04 23:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `rewards`
--

CREATE TABLE `rewards` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `points` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rewards`
--

INSERT INTO `rewards` (`id`, `name`, `description`, `points`) VALUES
(1, 'Coffee Voucher', 'Free coffee at the company cafeteria', 10),
(2, 'Movie Ticket', 'Two movie tickets to any cinema', 25),
(3, 'Extra Day Off', 'One extra paid day off', 50),
(4, 'Gadget Prize', 'Premium tech gadget of choice', 100);

-- --------------------------------------------------------

--
-- Table structure for table `suggestions`
--

CREATE TABLE `suggestions` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `suggestion_text` text NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suggestions`
--

INSERT INTO `suggestions` (`id`, `employee_id`, `suggestion_text`, `status`, `created_at`) VALUES
(1, 6, 'Implement a mentorship program for junior employees.', 'pending', '2026-01-31 23:35:52'),
(2, 7, 'Create a company newsletter to improve internal communication.', 'accepted', '2026-01-29 23:35:52'),
(3, 3, 'Establish a flexible work-from-home policy.', 'pending', '2026-02-03 23:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `survey_answers`
--

CREATE TABLE `survey_answers` (
  `id` int(11) NOT NULL,
  `response_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `survey_answers`
--

INSERT INTO `survey_answers` (`id`, `response_id`, `question_id`, `answer`) VALUES
(1, 1, 1, '4'),
(2, 1, 2, '4'),
(3, 1, 3, 'Better work-life balance and more career development opportunities'),
(4, 2, 1, '3'),
(5, 2, 2, '3'),
(6, 2, 3, 'More flexible working arrangements'),
(7, 3, 4, '4'),
(8, 3, 5, 'Regular team building activities and clearer communication channels');

-- --------------------------------------------------------

--
-- Table structure for table `survey_questions`
--

CREATE TABLE `survey_questions` (
  `id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('scale','text','choice') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `survey_questions`
--

INSERT INTO `survey_questions` (`id`, `survey_id`, `question_text`, `question_type`) VALUES
(1, 1, 'How satisfied are you with your current role?', 'scale'),
(2, 1, 'Do you feel valued by management?', 'scale'),
(3, 1, 'What aspects of your job would you like to improve?', 'text'),
(4, 2, 'How would you rate our workplace culture?', 'scale'),
(5, 2, 'What changes would you suggest for team collaboration?', 'text');

-- --------------------------------------------------------

--
-- Table structure for table `survey_responses`
--

CREATE TABLE `survey_responses` (
  `id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `survey_responses`
--

INSERT INTO `survey_responses` (`id`, `survey_id`, `employee_id`, `submitted_at`) VALUES
(1, 1, 3, '2026-02-02 23:35:52'),
(2, 1, 5, '2026-02-03 23:35:52'),
(3, 2, 4, '2026-02-03 23:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('recruitment','payroll','time','compliance','workforce','employee','learning','performance','engagement_relations','exit','clinic') NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `theme` enum('light','dark') DEFAULT 'light',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `employee_id`, `username`, `email`, `password`, `full_name`, `role`, `status`, `theme`, `created_at`) VALUES
(1, NULL, 'hr_payroll', 'hr_payroll@company.com', '$2y$10$lGdMJAD4KbQVmadxptk7xebMGEdpG6YsTk2UTvzB8yrgZ4T/m7.Ay', 'Russell Ike', 'payroll', 'active', 'light', '2026-03-06 21:13:06'),
(2, NULL, 'hr_recruitment', 'hr_recruitment@company.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'Administrator', 'recruitment', 'active', 'light', '2026-03-07 02:46:33'),
(3, NULL, 'hr_time', 'hr_time@company.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'Admin', 'time', 'active', 'light', '2026-03-07 02:47:07'),
(4, NULL, 'hr_employee', 'hr_employee@company.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'someone', 'employee', 'active', 'light', '2026-03-07 02:47:55'),
(5, NULL, 'hr_compliance', 'hr_compliance@company.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'comply', 'compliance', 'active', 'light', '2026-03-07 02:48:19'),
(6, NULL, 'hr_workforce', 'hr_workforce@company.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'force', 'workforce', 'active', 'light', '2026-03-07 02:48:43'),
(7, NULL, 'hr_learning', 'hr_learning@company.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'learn', 'learning', 'active', 'light', '2026-03-07 02:49:22'),
(8, NULL, 'hr_performance', 'hr_performance@company.com', '$2y$10$/Q0HsL9Cy/IlnwROoGHaeOcKQ.0wFpu43/.Zi01cfJ81fUO1t9vu2', 'Perform', 'performance', 'active', 'light', '2026-03-07 02:49:46'),
(9, NULL, 'hr_engagement', 'hr_engagement@company.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'engage', 'engagement_relations', 'active', 'light', '2026-03-07 02:50:37'),
(10, NULL, 'hr_exit', 'hr_exit@company.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'exit', 'exit', 'active', 'light', '2026-03-07 02:51:04'),
(11, NULL, 'hr_clinic', 'hr_clinic@company.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'clinic', 'clinic', 'active', 'light', '2026-03-12 08:20:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcement_id` (`announcement_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performed_by` (`performed_by`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `engagement_surveys`
--
ALTER TABLE `engagement_surveys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `grievances`
--
ALTER TABLE `grievances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `grievance_actions`
--
ALTER TABLE `grievance_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grievance_id` (`grievance_id`),
  ADD KEY `action_by` (`action_by`);

--
-- Indexes for table `recognitions`
--
ALTER TABLE `recognitions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `from_employee_id` (`from_employee_id`),
  ADD KEY `to_employee_id` (`to_employee_id`),
  ADD KEY `reward_id` (`reward_id`);

--
-- Indexes for table `rewards`
--
ALTER TABLE `rewards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suggestions`
--
ALTER TABLE `suggestions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `survey_answers`
--
ALTER TABLE `survey_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `response_id` (`response_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `survey_questions`
--
ALTER TABLE `survey_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_id` (`survey_id`);

--
-- Indexes for table `survey_responses`
--
ALTER TABLE `survey_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_id` (`survey_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `employee_id` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `engagement_surveys`
--
ALTER TABLE `engagement_surveys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `event_registrations`
--
ALTER TABLE `event_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `grievances`
--
ALTER TABLE `grievances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `grievance_actions`
--
ALTER TABLE `grievance_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `recognitions`
--
ALTER TABLE `recognitions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rewards`
--
ALTER TABLE `rewards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `suggestions`
--
ALTER TABLE `suggestions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `survey_answers`
--
ALTER TABLE `survey_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `survey_questions`
--
ALTER TABLE `survey_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `survey_responses`
--
ALTER TABLE `survey_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`id`);

--
-- Constraints for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  ADD CONSTRAINT `announcement_reads_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`),
  ADD CONSTRAINT `announcement_reads_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`performed_by`) REFERENCES `employees` (`id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `engagement_surveys`
--
ALTER TABLE `engagement_surveys`
  ADD CONSTRAINT `engagement_surveys_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`id`);

--
-- Constraints for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD CONSTRAINT `event_registrations_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`),
  ADD CONSTRAINT `event_registrations_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `grievances`
--
ALTER TABLE `grievances`
  ADD CONSTRAINT `grievances_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `grievances_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `employees` (`id`);

--
-- Constraints for table `grievance_actions`
--
ALTER TABLE `grievance_actions`
  ADD CONSTRAINT `grievance_actions_ibfk_1` FOREIGN KEY (`grievance_id`) REFERENCES `grievances` (`id`),
  ADD CONSTRAINT `grievance_actions_ibfk_2` FOREIGN KEY (`action_by`) REFERENCES `employees` (`id`);

--
-- Constraints for table `recognitions`
--
ALTER TABLE `recognitions`
  ADD CONSTRAINT `recognitions_ibfk_1` FOREIGN KEY (`from_employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `recognitions_ibfk_2` FOREIGN KEY (`to_employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `recognitions_ibfk_3` FOREIGN KEY (`reward_id`) REFERENCES `rewards` (`id`);

--
-- Constraints for table `suggestions`
--
ALTER TABLE `suggestions`
  ADD CONSTRAINT `suggestions_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `employees` joined users
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Trigger to keep users/employees in sync after employee update
--
DELIMITER $$
CREATE TRIGGER `employees_after_update_sync_users`
AFTER UPDATE ON `employees`
FOR EACH ROW
BEGIN
  UPDATE `users`
  SET
    `full_name` = NEW.name,
    `username` = COALESCE(NEW.username, NEW.email),
    `email` = NEW.email,
    `status` = NEW.status,
    `role` = NEW.role,
    `theme` = NEW.theme,
    `employee_id` = NEW.id
  WHERE `users`.`id` = NEW.user_id OR `users`.`employee_id` = NEW.id;
END$$
DELIMITER ;

--
-- Constraints for table `survey_answers`
--
ALTER TABLE `survey_answers`
  ADD CONSTRAINT `survey_answers_ibfk_1` FOREIGN KEY (`response_id`) REFERENCES `survey_responses` (`id`),
  ADD CONSTRAINT `survey_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `survey_questions` (`id`);

--
-- Constraints for table `survey_questions`
--
ALTER TABLE `survey_questions`
  ADD CONSTRAINT `survey_questions_ibfk_1` FOREIGN KEY (`survey_id`) REFERENCES `engagement_surveys` (`id`);

--
-- Constraints for table `survey_responses`
--
ALTER TABLE `survey_responses`
  ADD CONSTRAINT `survey_responses_ibfk_1` FOREIGN KEY (`survey_id`) REFERENCES `engagement_surveys` (`id`),
  ADD CONSTRAINT `survey_responses_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
