-- =============================================================
-- SHIFT MANAGEMENT - QUICK SQL SCRIPT
-- =============================================================
-- Copy and paste this entire script into phpMyAdmin SQL tab
-- or execute via MySQL command line
-- =============================================================

-- =====================================================
-- 1. CREATE SHIFTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `shifts` (
  `shift_id` int(11) NOT NULL AUTO_INCREMENT,
  `shift_name` varchar(100) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `break_duration` int(11) DEFAULT 60 COMMENT 'Break duration in minutes',
  `description` text,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`shift_id`),
  UNIQUE KEY `unique_shift_name` (`shift_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 2. CREATE EMPLOYEE_SHIFTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `employee_shifts` (
  `employee_shift_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`employee_shift_id`),
  FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE,
  FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`shift_id`) ON DELETE CASCADE,
  KEY `idx_employee_id` (`employee_id`),
  KEY `idx_shift_id` (`shift_id`),
  KEY `idx_effective_from` (`effective_from`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 3. INSERT SAMPLE SHIFTS
-- =====================================================
INSERT INTO `shifts` (`shift_name`, `start_time`, `end_time`, `break_duration`, `description`, `is_active`) VALUES
('Morning Shift', '08:00:00', '17:00:00', 60, 'Standard morning shift from 8 AM to 5 PM', 1),
('Afternoon Shift', '14:00:00', '23:00:00', 60, 'Afternoon shift from 2 PM to 11 PM', 1),
('Night Shift', '23:00:00', '08:00:00', 60, 'Night shift from 11 PM to 8 AM', 1),
('Flexible Morning', '08:00:00', '16:00:00', 60, 'Flexible morning shift 8 AM to 4 PM', 1),
('Flexible Evening', '16:00:00', '00:00:00', 60, 'Flexible evening shift 4 PM to 12 AM', 1);

-- =====================================================
-- 4. ALTER ATTENDANCE TABLE
-- =====================================================
ALTER TABLE `attendance` ADD COLUMN `shift_id` int(11) DEFAULT NULL AFTER `employee_id`;
ALTER TABLE `attendance` ADD COLUMN `is_within_shift_hours` tinyint(1) DEFAULT 1 AFTER `is_within_timeout_window`;
ALTER TABLE `attendance` ADD FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`shift_id`) ON DELETE SET NULL;

-- =====================================================
-- 5. CREATE INDEXES FOR PERFORMANCE
-- =====================================================
CREATE INDEX `idx_shift_id` ON `attendance` (`shift_id`);
CREATE INDEX `idx_attendance_date_shift` ON `attendance` (`attendance_date`, `shift_id`);

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================
-- Run these after migration to verify success:

-- Check shifts table
-- SELECT * FROM shifts;

-- Check employee_shifts table
-- SELECT * FROM employee_shifts;

-- Check attendance table structure
-- DESCRIBE attendance;

-- =============================================================
-- MIGRATION COMPLETE!
-- =============================================================
-- Tables created:
-- - shifts (5 sample shifts inserted)
-- - employee_shifts
-- 
-- Table modified:
-- - attendance (2 new columns added)
--
-- You can now access the Shift Management interface
-- at: http://localhost/Time_and_Attendance/public/shifts.php
-- =============================================================
