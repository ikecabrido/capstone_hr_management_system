-- Goal Setting Module - goals table
-- Run this SQL in phpMyAdmin if you need to add the table to an existing hr_management database

USE `hr_management`;

CREATE TABLE IF NOT EXISTS `goals` (
  `goal_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `goal_title` varchar(255) NOT NULL,
  `goal_description` text DEFAULT NULL,
  `target_date` date NOT NULL,
  `priority_level` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
<<<<<<< HEAD
  `status` enum('not_started','in_progress','completed') NOT NULL DEFAULT 'not_started',
  `kpi_target` decimal(10,2) DEFAULT NULL,
  `kpi_current` decimal(10,2) DEFAULT 0.00,
  `kpi_unit` varchar(50) DEFAULT NULL,
=======
  `status` enum('pending','ongoing','completed') NOT NULL DEFAULT 'pending',
>>>>>>> 8942cf7 (Clinic update)
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`goal_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `goals_employee_fk` 
    FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`report_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `performance_reports_employee_fk`
    FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;






CREATE TABLE IF NOT EXISTS `pm_evaluations` (
  `evaluation_id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`evaluation_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `performance_evaluations_employee_fk` 
    FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `feedback_360`
--

CREATE TABLE IF NOT EXISTS `fb_360` (
  `fb_id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`fb_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `feedback_360_employee_fk` 
    FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `tr_recommendations` (
  `training_id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`training_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `training_recommendations_employee_fk` 
    FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `performance_evaluations`
--

CREATE TABLE `pm_evaluations` (
  `evaluation_id` int(11) NOT NULL AUTO_INCREMENT,
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
<<<<<<< HEAD
  PRIMARY KEY (`evaluation_id`),
=======
  PRIMARY KEY (`id`),
>>>>>>> 8942cf7 (Clinic update)
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `performance_evaluations_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 


CREATE TABLE IF NOT EXISTS employees (
    employee_id VARCHAR(50) PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    address TEXT,
    contact_number VARCHAR(20),
    email VARCHAR(255),
    department VARCHAR(100),
    position VARCHAR(100),
    date_hired DATE,
    employment_status VARCHAR(50) DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO employees (employee_id, full_name, address, contact_number, email, department, position, date_hired, employment_status)
VALUES 
('EMP001', 'John Doe', '123 Main St', '123-456-7890', 'john.doe@example.com', 'IT', 'Software Engineer', '2023-01-01', 'Active'),
('EMP002', 'Jane Smith', '456 Oak Ave', '098-765-4321', 'jane.smith@example.com', 'HR', 'HR Manager', '2023-02-15', 'Active'),
('EMP003', 'Mike Johnson', '789 Pine Rd', '555-123-4567', 'mike.johnson@example.com', 'Finance', 'Accountant', '2023-03-10', 'Active');







