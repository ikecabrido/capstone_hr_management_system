USE `hr_management`;

CREATE TABLE IF NOT EXISTS `ld_training_requests` (
  `ld_request_id` int(11) NOT NULL AUTO_INCREMENT,
  `performance_request_id` int(11) DEFAULT NULL,
  `employee_user_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `goal_id` int(11) DEFAULT NULL,
  `kpi_name` varchar(255) DEFAULT NULL,
  `request_reason` text NOT NULL,
  `requested_program` varchar(255) DEFAULT NULL,
  `requested_course` varchar(255) DEFAULT NULL,
  `ld_training_program_id` int(11) DEFAULT NULL,
  `ld_course_id` int(11) DEFAULT NULL,
  `request_status` enum('New','Received','Approved','Rejected','Completed') NOT NULL DEFAULT 'New',
  `received_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ld_request_id`),
  KEY `idx_performance_request_id` (`performance_request_id`),
  KEY `idx_employee_user_id` (`employee_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
