-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2026 at 12:02 PM
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `career_paths`
--

INSERT INTO `career_paths` (`id`, `name`, `description`, `target_position`, `prerequisites`, `skills_required`, `duration_months`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Technical Lead', 'Progress from senior engineer to technical leadership and team management', 'Technical Lead / Engineering Manager', '5+ years professional experience, proven technical expertise', '[\"Leadership\", \"Communication\", \"System Design\", \"Mentoring\", \"Project Management\"]', 18, 'active', 1, '2026-03-15 17:19:01', '2026-03-15 17:19:01'),
(2, 'Project Manager', 'Develop skills to transition into project management and delivery leadership', 'Project Manager', '3+ years in any professional role', '[\"Planning\", \"Stakeholder Management\", \"Risk Management\", \"Agile Methodologies\", \"Communication\"]', 12, 'active', 1, '2026-03-15 17:19:01', '2026-03-15 17:19:01'),
(3, 'Product Manager', 'Transition into product management with focus on strategy and customer success', 'Product Manager', 'Experience with product or customer interaction', '[\"Product Strategy\", \"Data Analysis\", \"User Research\", \"Roadmap Planning\", \"Cross-functional Leadership\"]', 15, 'active', 1, '2026-03-15 17:19:01', '2026-03-15 17:19:01'),
(4, 'Subject Matter Expert', 'Develop deep expertise in a specific domain and become the go-to specialist', 'Senior Specialist / Subject Matter Expert', '2+ years experience in your domain', '[\"Deep Technical Knowledge\", \"Documentation\", \"Research\", \"Mentoring\", \"Innovation\"]', 24, 'active', 1, '2026-03-15 17:19:01', '2026-03-15 17:19:01');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `compliance_trainings`
--

INSERT INTO `compliance_trainings` (`id`, `title`, `description`, `compliance_type`, `due_date`, `frequency`, `mandatory`, `created_by`, `created_at`) VALUES
(1, 'Code of Conduct Training', 'Annual code of conduct review', 'Ethics', NULL, NULL, 1, 1, '2026-03-15 17:19:01'),
(2, 'Data Privacy Compliance', 'GDPR and data protection training', 'Privacy', NULL, NULL, 1, 1, '2026-03-15 17:19:01');

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
  `feedback` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leadership_enrollments`
--

INSERT INTO `leadership_enrollments` (`id`, `user_id`, `program_id`, `enrollment_date`, `start_date`, `end_date`, `completion_date`, `status`, `feedback`) VALUES
(1, 4, 1, '2026-03-15 17:19:02', NULL, NULL, NULL, 'in_progress', NULL),
(2, 7, 2, '2026-03-15 17:19:02', NULL, NULL, NULL, 'completed', NULL);

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leadership_programs`
--

INSERT INTO `leadership_programs` (`id`, `name`, `description`, `level`, `focus_area`, `duration_weeks`, `target_audience`, `outcomes`, `created_by`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Executive Leadership', 'Advanced leadership for senior managers', 'Executive', 'Strategic Leadership', 8, 'Senior Managers', '[\"Strategic Thinking\", \"Change Management\"]', 1, 'active', '2026-03-15 17:19:01', '2026-03-15 17:19:01'),
(2, 'Team Leadership Workshop', 'Building effective teams', 'Mid-Level', 'Team Management', 4, 'Team Leads', '[\"Communication\", \"Conflict Resolution\"]', 1, 'active', '2026-03-15 17:19:01', '2026-03-15 17:19:01');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performance_reviews`
--

INSERT INTO `performance_reviews` (`id`, `employee_id`, `reviewer_id`, `review_period_start`, `review_period_end`, `rating`, `comments`, `reviewed_date`, `status`, `created_at`) VALUES
(1, 4, 1, '2026-01-01', '2026-03-01', 4.50, 'Excellent performance in Q1', '2026-03-15 17:19:02', 'completed', '2026-03-15 17:19:02'),
(2, 7, 1, '2026-01-01', '2026-03-01', 4.00, 'Good progress, needs improvement in communication', '2026-03-15 17:19:02', 'completed', '2026-03-15 17:19:02');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `team_activities`
--

INSERT INTO `team_activities` (`id`, `name`, `description`, `activity_date`, `department`, `organizer_id`, `budget`, `participant_count`, `status`, `created_at`) VALUES
(1, 'Team Building Retreat', 'Annual team building event', '2026-04-15', 'All', 1, 5000.00, 50, 'planned', '2026-03-15 17:19:02'),
(2, 'Diversity Workshop', 'Promoting inclusivity', '2026-05-10', 'HR', 1, 2000.00, 20, 'completed', '2026-03-15 17:19:02');

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
  `feedback` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `training_programs`
--

INSERT INTO `training_programs` (`id`, `name`, `description`, `category`, `type`, `duration`, `created_by`, `status`, `created_at`, `updated_at`) VALUES
(1, 'dsa', '435ewr', 'General', 'Workshop', 13, 7, 'Active', '2026-03-15 18:33:09', '2026-03-15 18:33:09');

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
-- Indexes for table `succession_plans`
--
ALTER TABLE `succession_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `current_holder_id` (`current_holder_id`),
  ADD KEY `successor_id` (`successor_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `team_activities`
--
ALTER TABLE `team_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organizer_id` (`organizer_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_activity_date` (`activity_date`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `feedback_360`
--
ALTER TABLE `feedback_360`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `individual_development_plans`
--
ALTER TABLE `individual_development_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leadership_enrollments`
--
ALTER TABLE `leadership_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `leadership_programs`
--
ALTER TABLE `leadership_programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT for table `succession_plans`
--
ALTER TABLE `succession_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `team_activities`
--
ALTER TABLE `team_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `training_enrollments`
--
ALTER TABLE `training_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `training_logs`
--
ALTER TABLE `training_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `training_programs`
--
ALTER TABLE `training_programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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

--
-- Table structure for table `resignations`
--

CREATE TABLE `resignations` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `resignation_type` enum('voluntary','involuntary') DEFAULT 'voluntary',
  `reason` text,
  `notice_date` date NOT NULL,
  `last_working_date` date NOT NULL,
  `comments` text,
  `submitted_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected','withdrawn') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `exit_interviews`
--

CREATE TABLE `exit_interviews` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `interviewer_id` int(11) NOT NULL,
  `scheduled_date` date NOT NULL,
  `scheduled_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT 'Virtual',
  `notes` text,
  `feedback` longtext,
  `rating` decimal(3,2) DEFAULT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `knowledge_transfer_plans`
--

CREATE TABLE `knowledge_transfer_plans` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `successor_id` int(11) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `knowledge_transfer_items`
--

CREATE TABLE `knowledge_transfer_items` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `item_type` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `assigned_to` int(11) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `completed_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `employee_settlements`
--

CREATE TABLE `employee_settlements` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
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
  `payment_date` date DEFAULT NULL,
  `status` enum('draft','approved','paid','cancelled') DEFAULT 'draft',
  `created_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `exit_documents`
--

CREATE TABLE `exit_documents` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `status` enum('active','archived','deleted') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `exit_surveys`
--

CREATE TABLE `exit_surveys` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `target_audience` varchar(100) DEFAULT 'all',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','inactive','completed') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `order_num` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `survey_responses`
--

CREATE TABLE `survey_responses` (
  `id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `response` text,
  `rating_value` int(11) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `survey_answers`
--

CREATE TABLE `survey_answers` (
  `id` int(11) NOT NULL,
  `response_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text,
  `answer_value` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for exit management tables
--

--
-- Indexes for table `resignations`
--
ALTER TABLE `resignations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_submitted_by` (`submitted_by`),
  ADD KEY `idx_approved_by` (`approved_by`);

--
-- Indexes for table `exit_interviews`
--
ALTER TABLE `exit_interviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_interviewer_id` (`interviewer_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_scheduled_date` (`scheduled_date`);

--
-- Indexes for table `knowledge_transfer_plans`
--
ALTER TABLE `knowledge_transfer_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_successor_id` (`successor_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_by` (`created_by`);

--
-- Indexes for table `knowledge_transfer_items`
--
ALTER TABLE `knowledge_transfer_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_plan_id` (`plan_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_assigned_to` (`assigned_to`),
  ADD KEY `idx_due_date` (`due_date`);

--
-- Indexes for table `employee_settlements`
--
ALTER TABLE `employee_settlements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_resignation_id` (`resignation_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_approved_by` (`approved_by`);

--
-- Indexes for table `exit_documents`
--
ALTER TABLE `exit_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_document_type` (`document_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_uploaded_by` (`uploaded_by`);

--
-- Indexes for table `exit_surveys`
--
ALTER TABLE `exit_surveys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_start_date` (`start_date`),
  ADD KEY `idx_end_date` (`end_date`);

--
-- Indexes for table `survey_questions`
--
ALTER TABLE `survey_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_survey_id` (`survey_id`),
  ADD KEY `idx_order_num` (`order_num`);

--
-- Indexes for table `survey_responses`
--
ALTER TABLE `survey_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_survey_id` (`survey_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_question_id` (`question_id`);

--
-- Indexes for table `survey_answers`
--
ALTER TABLE `survey_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_response_id` (`response_id`),
  ADD KEY `idx_question_id` (`question_id`);

--
-- AUTO_INCREMENT for exit management tables
--

--
-- AUTO_INCREMENT for table `resignations`
--
ALTER TABLE `resignations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exit_interviews`
--
ALTER TABLE `exit_interviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `knowledge_transfer_plans`
--
ALTER TABLE `knowledge_transfer_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `knowledge_transfer_items`
--
ALTER TABLE `knowledge_transfer_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_settlements`
--
ALTER TABLE `employee_settlements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exit_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exit_surveys`
--
ALTER TABLE `exit_surveys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `survey_questions`
--
ALTER TABLE `survey_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `survey_responses`
--
ALTER TABLE `survey_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `survey_answers`
--
ALTER TABLE `survey_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for exit management tables
--

--
-- Constraints for table `resignations`
--
ALTER TABLE `resignations`
  ADD CONSTRAINT `fk_resignations_employee` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_resignations_submitted_by` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_resignations_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exit_interviews`
--
ALTER TABLE `exit_interviews`
  ADD CONSTRAINT `fk_exit_interviews_employee` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_exit_interviews_interviewer` FOREIGN KEY (`interviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `knowledge_transfer_plans`
--
ALTER TABLE `knowledge_transfer_plans`
  ADD CONSTRAINT `fk_kt_plans_employee` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kt_plans_successor` FOREIGN KEY (`successor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_kt_plans_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `knowledge_transfer_items`
--
ALTER TABLE `knowledge_transfer_items`
  ADD CONSTRAINT `fk_kt_items_plan` FOREIGN KEY (`plan_id`) REFERENCES `knowledge_transfer_plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kt_items_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_settlements`
--
ALTER TABLE `employee_settlements`
  ADD CONSTRAINT `fk_settlements_employee` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_settlements_resignation` FOREIGN KEY (`resignation_id`) REFERENCES `resignations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_settlements_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_settlements_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exit_documents`
--
ALTER TABLE `exit_documents`
  ADD CONSTRAINT `fk_exit_documents_employee` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_exit_documents_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exit_surveys`
--
ALTER TABLE `exit_surveys`
  ADD CONSTRAINT `fk_exit_surveys_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `survey_questions`
--
ALTER TABLE `survey_questions`
  ADD CONSTRAINT `fk_survey_questions_survey` FOREIGN KEY (`survey_id`) REFERENCES `exit_surveys` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `survey_responses`
--
ALTER TABLE `survey_responses`
  ADD CONSTRAINT `fk_survey_responses_survey` FOREIGN KEY (`survey_id`) REFERENCES `exit_surveys` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_survey_responses_employee` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_survey_responses_question` FOREIGN KEY (`question_id`) REFERENCES `survey_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `survey_answers`
--
ALTER TABLE `survey_answers`
  ADD CONSTRAINT `fk_survey_answers_response` FOREIGN KEY (`response_id`) REFERENCES `survey_responses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_survey_answers_question` FOREIGN KEY (`question_id`) REFERENCES `survey_questions` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
