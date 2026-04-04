-- Goal Setting Module - goals table
-- Run this SQL in phpMyAdmin if you need to add the table to an existing hr_management database

USE `hr_management`;








CREATE TABLE IF NOT EXISTS `pm_360_feedback` (
  `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `evaluator_type` enum('Manager', 'Peer', 'Subordinate', 'Self') NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `category` enum('Communication', 'Teamwork', 'Leadership', 'Performance') NOT NULL,
  `comments` text DEFAULT NULL,
  `is_anonymous` tinyint(1) DEFAULT 0,
  `evaluation_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`feedback_id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Enhanced 360-Degree Feedback Tables for Advanced Features

-- Competencies and Behavioral Indicators
CREATE TABLE IF NOT EXISTS `pm_competencies` (
  `competency_id` int(11) NOT NULL AUTO_INCREMENT,
  `competency_name` varchar(255) NOT NULL,
  `description` text,
  `category` enum('Technical', 'Leadership', 'Interpersonal', 'Strategic', 'Operational') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`competency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Behavioral Indicators for each competency
CREATE TABLE IF NOT EXISTS `pm_competency_indicators` (
  `indicator_id` int(11) NOT NULL AUTO_INCREMENT,
  `competency_id` int(11) NOT NULL,
  `indicator_text` text NOT NULL,
  `level` enum('Beginner', 'Intermediate', 'Advanced', 'Expert') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`indicator_id`),
  KEY `competency_id` (`competency_id`),
  CONSTRAINT `fk_competency_indicator` FOREIGN KEY (`competency_id`) REFERENCES `pm_competencies` (`competency_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Enhanced 360 Feedback with Competencies
CREATE TABLE IF NOT EXISTS `pm_360_competency_feedback` (
  `feedback_competency_id` int(11) NOT NULL AUTO_INCREMENT,
  `feedback_id` int(11) NOT NULL,
  `competency_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `strengths` text,
  `improvement_areas` text,
  `examples` text,
  PRIMARY KEY (`feedback_competency_id`),
  KEY `feedback_id` (`feedback_id`),
  KEY `competency_id` (`competency_id`),
  CONSTRAINT `fk_feedback_competency` FOREIGN KEY (`feedback_id`) REFERENCES `pm_360_feedback` (`feedback_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_competency_feedback` FOREIGN KEY (`competency_id`) REFERENCES `pm_competencies` (`competency_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Feedback Action Plans
CREATE TABLE IF NOT EXISTS `pm_feedback_action_plans` (
  `action_plan_id` int(11) NOT NULL AUTO_INCREMENT,
  `feedback_id` int(11) NOT NULL,
  `action_description` text NOT NULL,
  `priority` enum('High', 'Medium', 'Low') NOT NULL DEFAULT 'Medium',
  `target_date` date NOT NULL,
  `assigned_to` int(11) NOT NULL, -- employee_id of person responsible
  `status` enum('Not Started', 'In Progress', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Not Started',
  `progress_notes` text,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`action_plan_id`),
  KEY `feedback_id` (`feedback_id`),
  KEY `assigned_to` (`assigned_to`),
  CONSTRAINT `fk_action_plan_feedback` FOREIGN KEY (`feedback_id`) REFERENCES `pm_360_feedback` (`feedback_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Anonymous Feedback Verification
CREATE TABLE IF NOT EXISTS `pm_anonymous_verification` (
  `verification_id` int(11) NOT NULL AUTO_INCREMENT,
  `feedback_id` int(11) NOT NULL,
  `verification_code` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verified_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`verification_id`),
  UNIQUE KEY `unique_feedback_verification` (`feedback_id`),
  KEY `feedback_id` (`feedback_id`),
  CONSTRAINT `fk_verification_feedback` FOREIGN KEY (`feedback_id`) REFERENCES `pm_360_feedback` (`feedback_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Calibration Sessions
CREATE TABLE IF NOT EXISTS `pm_calibration_sessions` (
  `calibration_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_name` varchar(255) NOT NULL,
  `session_date` date NOT NULL,
  `facilitator_id` int(11) NOT NULL,
  `department` varchar(100),
  `status` enum('Planned', 'In Progress', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Planned',
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`calibration_id`),
  KEY `facilitator_id` (`facilitator_id`),
  CONSTRAINT `fk_calibration_facilitator` FOREIGN KEY (`facilitator_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Employees in calibration sessions
CREATE TABLE IF NOT EXISTS `pm_calibration_participants` (
  `participant_id` int(11) NOT NULL AUTO_INCREMENT,
  `calibration_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `role` enum('Participant', 'Observer', 'Facilitator') NOT NULL DEFAULT 'Participant',
  PRIMARY KEY (`participant_id`),
  KEY `calibration_id` (`calibration_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `fk_participant_calibration` FOREIGN KEY (`calibration_id`) REFERENCES `pm_calibration_sessions` (`calibration_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Feedback Analytics Cache
CREATE TABLE IF NOT EXISTS `pm_feedback_analytics` (
  `analytics_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `analysis_date` date NOT NULL,
  `overall_rating_trend` enum('Improving', 'Stable', 'Declining') NOT NULL,
  `strengths_count` int(11) DEFAULT 0,
  `improvement_areas_count` int(11) DEFAULT 0,
  `top_competencies` text, -- JSON array of competency names
  `development_needs` text, -- JSON array of development areas
  `consistency_score` decimal(5,2) DEFAULT 0.00, -- Rating consistency score
  PRIMARY KEY (`analytics_id`),
  UNIQUE KEY `unique_employee_analysis` (`employee_id`, `analysis_date`),
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Audit Log Table for tracking all changes
CREATE TABLE IF NOT EXISTS `audit_log` (
  `audit_id` int(11) NOT NULL AUTO_INCREMENT,
  `action` enum('CREATE','UPDATE','DELETE','VIEW') NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `description` text,
  `old_values` text,
  `new_values` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`audit_id`),
  KEY `user_id` (`user_id`),
  KEY `table_name` (`table_name`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Users table for authentication (if not exists)
CREATE TABLE IF NOT EXISTS `pm_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','manager','employee') NOT NULL DEFAULT 'employee',
  `theme` enum('light','dark') DEFAULT 'light',
  `employee_id` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Performance Review Templates
CREATE TABLE IF NOT EXISTS `pm_review_templates` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(255) NOT NULL,
  `description` text,
  `review_period` enum('Quarterly', 'Annual', 'Mid-Year') NOT NULL,
  `rating_categories` text NOT NULL, -- JSON array of rating categories
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`template_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `template_creator_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample review templates
INSERT IGNORE INTO `pm_review_templates` (`template_id`, `template_name`, `description`, `review_period`, `rating_categories`, `is_active`) VALUES
(1, 'Standard Performance Review', 'Comprehensive performance evaluation template', 'Annual', '["Communication Skills", "Technical Proficiency", "Teamwork", "Leadership", "Problem Solving", "Initiative", "Attendance", "Goal Achievement"]', 1),
(2, 'Mid-Year Check-in', 'Mid-year performance check-in template', 'Mid-Year', '["Goal Progress", "Skill Development", "Team Contribution", "Communication", "Adaptability"]', 1),
(3, 'Quarterly Review', 'Quarterly performance assessment template', 'Quarterly', '["Achievement of Objectives", "Quality of Work", "Productivity", "Collaboration", "Professional Development"]', 1);

INSERT IGNORE INTO `pm_competency_indicators` (`indicator_id`, `competency_id`, `indicator_text`, `level`) VALUES
-- Communication Skills
(1, 1, 'Expresses ideas clearly and concisely in writing', 'Intermediate'),
(2, 1, 'Delivers presentations that engage and inform the audience', 'Advanced'),
(3, 1, 'Actively listens and responds thoughtfully to others', 'Beginner'),
(4, 1, 'Adapts communication style to different audiences', 'Advanced'),

-- Leadership
(5, 2, 'Inspires and motivates team members to achieve goals', 'Expert'),
(6, 2, 'Provides constructive feedback that helps others grow', 'Advanced'),
(7, 2, 'Delegates tasks effectively and appropriately', 'Intermediate'),
(8, 2, 'Leads by example and demonstrates integrity', 'Advanced'),

-- Problem Solving
(9, 3, 'Breaks down complex problems into manageable components', 'Intermediate'),
(10, 3, 'Develops creative and innovative solutions', 'Advanced'),
(11, 3, 'Considers multiple perspectives when analyzing issues', 'Intermediate'),
(12, 3, 'Learns from past experiences to improve future outcomes', 'Advanced'),

-- Team Collaboration
(13, 4, 'Builds strong working relationships with colleagues', 'Beginner'),
(14, 4, 'Contributes ideas and supports team decisions', 'Intermediate'),
(15, 4, 'Resolves conflicts constructively', 'Advanced'),
(16, 4, 'Shares knowledge and resources with team members', 'Intermediate'),

-- Technical Expertise
(17, 5, 'Stays current with industry trends and best practices', 'Advanced'),
(18, 5, 'Applies technical knowledge to solve complex problems', 'Expert'),
(19, 5, 'Mentors others in technical skills and knowledge', 'Advanced'),
(20, 5, 'Demonstrates deep understanding of technical concepts', 'Expert'),

-- Project Management
(21, 6, 'Creates realistic project plans with clear milestones', 'Intermediate'),
(22, 6, 'Manages project resources effectively and efficiently', 'Advanced'),
(23, 6, 'Identifies and mitigates project risks proactively', 'Advanced'),
(24, 6, 'Delivers projects on time and within budget', 'Expert'),

-- Adaptability
(25, 7, 'Embraces change and adapts quickly to new situations', 'Intermediate'),
(26, 7, 'Learns new skills and technologies rapidly', 'Advanced'),
(27, 7, 'Maintains positive attitude during challenging times', 'Beginner'),
(28, 7, 'Finds opportunities in changing circumstances', 'Advanced'),

-- Decision Making
(29, 8, 'Gathers and analyzes relevant information before deciding', 'Intermediate'),
(30, 8, 'Makes timely decisions even with incomplete information', 'Advanced'),
(31, 8, 'Considers the impact of decisions on stakeholders', 'Advanced'),
(32, 8, 'Takes responsibility for decision outcomes', 'Expert');

CREATE TABLE IF NOT EXISTS `pm_goals` (
      `goal_id` int(11) NOT NULL AUTO_INCREMENT,
      `employee_id` int(11) NOT NULL,
      `goal_title` varchar(255) NOT NULL,
      `kpi_name` varchar(255) NOT NULL,
      `target_value` decimal(10,2) NOT NULL DEFAULT 100.00,
      `current_progress` decimal(10,2) NOT NULL DEFAULT 0.00,
      `status` enum('On Track', 'Delayed', 'Completed') NOT NULL DEFAULT 'On Track',
      `start_date` date NOT NULL,
      `end_date` date NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`goal_id`),
      KEY `employee_id` (`employee_id`)
    );

    ALTER TABLE `pm_goals` ADD COLUMN `priority` enum('Low', 'Medium', 'High') NOT NULL DEFAULT 'Medium' AFTER `status`;

    
    CREATE INDEX idx_priority ON `pm_goals` (`priority`);

   CREATE TABLE IF NOT EXISTS `pm_appraisals` (
      `appraisal_id` int(11) NOT NULL AUTO_INCREMENT,
      `employee_id` int(11) NOT NULL,
      `review_period` enum('Quarterly', 'Annual', 'Mid-Year') NOT NULL,
      `goals_kpis` text NOT NULL,
      `performance_ratings` text NOT NULL, -- Storing ratings as JSON or serialized array
      `manager_evaluation` text NOT NULL,
      `overall_score` decimal(5,2) NOT NULL,
      `comments` text DEFAULT NULL,
      `review_date` date NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`appraisal_id`),
      KEY `employee_id` (`employee_id`)
    );


CREATE TABLE IF NOT EXISTS `pm_training_recommendations` (
      `recommendation_id` int(11) NOT NULL AUTO_INCREMENT,
      `employee_id` int(11) NOT NULL,
      `skill_gaps` text NOT NULL,
      `training_program` varchar(255) NOT NULL,
      `training_type` enum('Online Course', 'Workshop', 'Seminar', 'Internal Training') NOT NULL,
      `priority_level` enum('High', 'Medium', 'Low') NOT NULL,
      `suggested_completion_date` date NOT NULL,
      `remarks` text DEFAULT NULL,
      `status` enum('Proposed', 'In Progress', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Proposed',
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`recommendation_id`),
      KEY `employee_id` (`employee_id`)
    );


CREATE TABLE `pm_reports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`report_id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add foreign key constraints after all tables are created

-- Foreign key constraints for tables referencing employees
ALTER TABLE `pm_360_feedback` ADD CONSTRAINT `pm_360_feedback_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;
ALTER TABLE `pm_goals` ADD CONSTRAINT `pm_goals_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;
ALTER TABLE `pm_appraisals` ADD CONSTRAINT `pm_appraisals_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;
ALTER TABLE `pm_training_recommendations` ADD CONSTRAINT `pm_training_recommendations_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;
ALTER TABLE `pm_reports` ADD CONSTRAINT `performance_reports_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;
ALTER TABLE `pm_feedback_action_plans` ADD CONSTRAINT `fk_action_plan_assigned` FOREIGN KEY (`assigned_to`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;
ALTER TABLE `pm_calibration_participants` ADD CONSTRAINT `fk_participant_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;
ALTER TABLE `pm_feedback_analytics` ADD CONSTRAINT `fk_analytics_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

-- Foreign key constraint for audit_log (conditional)
SET @constraint_exists = (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'audit_log'
    AND CONSTRAINT_NAME = 'audit_log_user_fk'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);

SET @sql = IF(@constraint_exists = 0,
    'ALTER TABLE `audit_log` ADD CONSTRAINT `audit_log_user_fk` FOREIGN KEY (`user_id`) REFERENCES `pm_users` (`user_id`) ON DELETE SET NULL',
    'SELECT "Foreign key constraint already exists" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;















