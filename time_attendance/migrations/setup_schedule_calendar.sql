-- ========================================================================
-- Schedule Calendar Feature - Database Setup
-- Time & Attendance Management System
-- ========================================================================
-- This script creates the necessary tables for the Schedule Calendar feature
-- Run this in phpMyAdmin or MySQL command line to set up the database
-- ========================================================================

USE hr_management;

-- ========================================================================
-- Table: custom_shifts
-- Purpose: Store day-specific shift overrides for employees
-- ========================================================================
CREATE TABLE IF NOT EXISTS `custom_shifts` (
  `custom_shift_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier for custom shift',
  `employee_id` int(11) NOT NULL COMMENT 'Reference to employee',
  `shift_date` date NOT NULL COMMENT 'Date of the custom shift',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record update timestamp',
  
  PRIMARY KEY (`custom_shift_id`),
  UNIQUE KEY `unique_employee_date` (`employee_id`, `shift_date`),
  KEY `idx_employee_date` (`employee_id`, `shift_date`),
  KEY `idx_shift_date` (`shift_date`),
  
  CONSTRAINT `fk_custom_shifts_employee` FOREIGN KEY (`employee_id`) 
    REFERENCES `employees` (`employee_id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE
    
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci 
  COMMENT='Stores day-specific shift assignments for employees';

-- ========================================================================
-- Table: custom_shift_times
-- Purpose: Store the individual shift times for custom shifts
-- ========================================================================
CREATE TABLE IF NOT EXISTS `custom_shift_times` (
  `custom_shift_time_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier for shift time',
  `custom_shift_id` int(11) NOT NULL COMMENT 'Reference to custom shift',
  `start_time` datetime NOT NULL COMMENT 'Shift start time',
  `end_time` datetime NOT NULL COMMENT 'Shift end time',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp',
  
  PRIMARY KEY (`custom_shift_time_id`),
  KEY `idx_custom_shift_id` (`custom_shift_id`),
  
  CONSTRAINT `fk_custom_shift_times_shift` FOREIGN KEY (`custom_shift_id`) 
    REFERENCES `custom_shifts` (`custom_shift_id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE
    
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci 
  COMMENT='Stores individual shift time blocks for custom shifts';

-- ========================================================================
-- Verification Queries
-- ========================================================================
-- Run these after setup to verify everything is working

-- Show table structure
-- DESCRIBE custom_shifts;
-- DESCRIBE custom_shift_times;

-- Show created tables
-- SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'time_and_attendance';

-- Check foreign key relationships
-- SELECT CONSTRAINT_NAME, TABLE_NAME, REFERENCED_TABLE_NAME 
-- FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
-- WHERE TABLE_SCHEMA = 'time_and_attendance' AND REFERENCED_TABLE_NAME IS NOT NULL;

-- ========================================================================
-- Sample Data (Optional - uncomment to load test data)
-- ========================================================================

-- -- Insert sample custom shift for testing
-- INSERT INTO `custom_shifts` (`employee_id`, `shift_date`, `created_at`, `updated_at`)
-- VALUES 
--   (1, '2026-03-16', NOW(), NOW()),
--   (2, '2026-03-17', NOW(), NOW());

-- -- Insert sample shift times
-- INSERT INTO `custom_shift_times` (`custom_shift_id`, `start_time`, `end_time`, `created_at`)
-- VALUES 
--   (1, '2026-03-16 09:00:00', '2026-03-16 17:00:00', NOW()),
--   (2, '2026-03-17 10:00:00', '2026-03-17 18:00:00', NOW());

-- ========================================================================
-- End of Setup Script
-- ========================================================================
-- Setup completed. You can now use the Schedule Calendar feature.
-- Access it from: Time & Attendance → Schedule Calendar tab
-- ========================================================================
