-- Goal Setting Module - goals table
-- Run this SQL in phpMyAdmin if you need to add the table to an existing hr_management database

USE `hr_management`;

CREATE TABLE IF NOT EXISTS `goals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `goal_title` varchar(255) NOT NULL,
  `goal_description` text DEFAULT NULL,
  `target_date` date NOT NULL,
  `priority_level` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `status` enum('pending','ongoing','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `goals_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `performance_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `performance_reports_employee_fk`
    FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `goals`
--
CREATE TABLE `goals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `goal_title` varchar(255) NOT NULL,
  `goal_description` text DEFAULT NULL,
  `target_date` date NOT NULL,
  `priority_level` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `status` enum('pending','ongoing','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `goals_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `performance_evaluations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `criteria_json` text NOT NULL,
  `rating_percent` decimal(5,2) NOT NULL,
  `rating_5` tinyint(4) NOT NULL,
  `rating_type` enum('percent','scale5') NOT NULL DEFAULT 'percent',
  `reviewer_name` varchar(120) NOT NULL,
  `comments` text DEFAULT NULL,
  `review_date` date NOT NULL,
  `final_result` enum('Excellent','Good','Needs Improvement') NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `performance_evaluations_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `feedback_360`
--

CREATE TABLE IF NOT EXISTS `feedback_360` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `provider_name` varchar(150) NOT NULL,
  `provider_role` enum('Manager','Peer','Self','Subordinate','Client') NOT NULL,
  `evaluation_criteria` varchar(255) NOT NULL,
  `rating_score` tinyint(1) NOT NULL,
  `comments` text DEFAULT NULL,
  `review_date` date NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `feedback_360_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `training_recommendations`
--

CREATE TABLE IF NOT EXISTS `training_recommendations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `position` varchar(100) NOT NULL,
  `skill_gap` varchar(150) NOT NULL,
  `recommended_program` varchar(255) NOT NULL,
  `training_provider` varchar(150) NOT NULL,
  `training_schedule` varchar(150) NOT NULL,
  `expected_outcome` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `training_recommendations_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `performance_evaluations`
--

CREATE TABLE `performance_evaluations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `criteria_json` text NOT NULL,
  `rating_percent` decimal(5,2) NOT NULL,
  `rating_5` tinyint(4) NOT NULL,
  `rating_type` enum('percent','scale5') NOT NULL DEFAULT 'percent',
  `reviewer_name` varchar(120) NOT NULL,
  `comments` text DEFAULT NULL,
  `review_date` date NOT NULL,
  `final_result` enum('Excellent','Good','Needs Improvement') NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `performance_evaluations_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



