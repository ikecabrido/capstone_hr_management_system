-- Migration: Add Custom Shifts Tables for Schedule Calendar
-- Purpose: Allow day-specific shift overrides and custom shift times

CREATE TABLE IF NOT EXISTS `custom_shifts` (
  `custom_shift_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `shift_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`custom_shift_id`),
  UNIQUE KEY `unique_employee_date` (`employee_id`, `shift_date`),
  FOREIGN KEY (`employee_id`) REFERENCES `employees`(`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `custom_shift_times` (
  `custom_shift_time_id` int(11) NOT NULL AUTO_INCREMENT,
  `custom_shift_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`custom_shift_time_id`),
  KEY `custom_shift_id` (`custom_shift_id`),
  FOREIGN KEY (`custom_shift_id`) REFERENCES `custom_shifts`(`custom_shift_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create index for quick lookups
CREATE INDEX idx_employee_date ON `custom_shifts` (`employee_id`, `shift_date`);
