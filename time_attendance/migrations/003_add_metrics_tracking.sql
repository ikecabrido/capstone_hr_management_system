-- ============================================================================
-- TIME & ATTENDANCE: Enhanced Metrics Tracking System
-- ============================================================================
-- This migration adds the necessary columns and tables to support:
-- - Late minutes tracking (actual minutes employee was late)
-- - Punctuality score (calculated based on lateness frequency and severity)
-- - Overtime frequency tracking (how often overtime occurs)
-- - Detailed metrics for analytics and reporting
-- ============================================================================

-- Add new columns to ta_attendance table for detailed metrics tracking
ALTER TABLE `ta_attendance` ADD COLUMN IF NOT EXISTS `late_minutes` INT DEFAULT 0 COMMENT 'Number of minutes employee was late (0 if on time)';
ALTER TABLE `ta_attendance` ADD COLUMN IF NOT EXISTS `early_out_minutes` INT DEFAULT 0 COMMENT 'Number of minutes employee left early (0 if on time)';
ALTER TABLE `ta_attendance` ADD COLUMN IF NOT EXISTS `shift_minutes` INT DEFAULT 0 COMMENT 'Expected shift duration in minutes';

-- Table to track monthly punctuality scores
CREATE TABLE IF NOT EXISTS `ta_punctuality_scores` (
    `score_id` INT PRIMARY KEY AUTO_INCREMENT,
    `employee_id` VARCHAR(50) NOT NULL,
    `month_year` VARCHAR(7) NOT NULL,
    `total_late_incidents` INT DEFAULT 0,
    `total_late_minutes` INT DEFAULT 0,
    `average_late_minutes` DECIMAL(5,2) DEFAULT 0,
    `punctuality_score` DECIMAL(5,2) DEFAULT 100,
    `punctuality_grade` ENUM('A', 'B', 'C', 'D', 'F') DEFAULT 'A',
    `score_breakdown` JSON,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`employee_id`) REFERENCES `employees`(`employee_id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_employee_month` (`employee_id`, `month_year`),
    KEY `idx_punctuality_score` (`punctuality_score`),
    KEY `idx_punctuality_grade` (`punctuality_grade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table to track overtime frequency and patterns
CREATE TABLE IF NOT EXISTS `ta_overtime_tracking` (
    `tracking_id` INT PRIMARY KEY AUTO_INCREMENT,
    `employee_id` VARCHAR(50) NOT NULL,
    `attendance_id` INT,
    `overtime_date` DATE NOT NULL,
    `overtime_hours` DECIMAL(5,2) NOT NULL,
    `overtime_minutes` INT NOT NULL,
    `reason_category` ENUM('PROJECT_DEADLINE', 'STAFFING_SHORTAGE', 'WORKLOAD_HEAVY', 'SHIFT_CHANGE', 'VOLUNTARY', 'OTHER') DEFAULT 'OTHER',
    `reason_notes` TEXT,
    `approved` BOOLEAN DEFAULT FALSE,
    `approved_by` INT,
    `approval_date` DATETIME,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`employee_id`) REFERENCES `employees`(`employee_id`) ON DELETE CASCADE,
    KEY `idx_employee_date` (`employee_id`, `overtime_date`),
    KEY `idx_overtime_approved` (`approved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for monthly overtime frequency summary
CREATE TABLE IF NOT EXISTS `ta_overtime_frequency` (
    `frequency_id` INT PRIMARY KEY AUTO_INCREMENT,
    `employee_id` VARCHAR(50) NOT NULL,
    `month_year` VARCHAR(7) NOT NULL,
    `overtime_instances` INT DEFAULT 0,
    `total_overtime_hours` DECIMAL(8,2) DEFAULT 0,
    `average_overtime_per_instance` DECIMAL(5,2) DEFAULT 0,
    `max_overtime_in_single_day` DECIMAL(5,2) DEFAULT 0,
    `overtime_frequency_rating` ENUM('LOW', 'MODERATE', 'HIGH', 'CRITICAL') DEFAULT 'LOW',
    `approved_overtime_hours` DECIMAL(8,2) DEFAULT 0,
    `unapproved_overtime_hours` DECIMAL(8,2) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`employee_id`) REFERENCES `employees`(`employee_id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_employee_month` (`employee_id`, `month_year`),
    KEY `idx_frequency_rating` (`overtime_frequency_rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for attendance metrics aggregation
CREATE TABLE IF NOT EXISTS `ta_attendance_metrics` (
    `metric_id` INT PRIMARY KEY AUTO_INCREMENT,
    `employee_id` VARCHAR(50) NOT NULL,
    `month_year` VARCHAR(7) NOT NULL,
    `attendance_rate` DECIMAL(5,2) DEFAULT 0,
    `absence_rate` DECIMAL(5,2) DEFAULT 0,
    `punctuality_score` DECIMAL(5,2) DEFAULT 100,
    `overtime_frequency_rating` VARCHAR(20),
    `overall_performance_score` DECIMAL(5,2) DEFAULT 0,
    `total_present_days` INT DEFAULT 0,
    `total_absent_days` INT DEFAULT 0,
    `total_late_incidents` INT DEFAULT 0,
    `total_overtime_hours` DECIMAL(8,2) DEFAULT 0,
    `status_summary` JSON,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`employee_id`) REFERENCES `employees`(`employee_id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_employee_month` (`employee_id`, `month_year`),
    KEY `idx_overall_performance` (`overall_performance_score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add index on ta_absence_late_records for late minutes calculation
ALTER TABLE `ta_absence_late_records` ADD COLUMN IF NOT EXISTS `late_minutes` INT DEFAULT 0 COMMENT 'Minutes late for this incident';

-- Verification Query - Check tables exist and record counts
SELECT 'ta_attendance (columns)' as item, 'late_minutes, early_out_minutes, shift_minutes' as description
UNION ALL
SELECT 'ta_punctuality_scores' as item, CONCAT('Records: ', COALESCE(COUNT(*), 0)) as description FROM ta_punctuality_scores
UNION ALL
SELECT 'ta_overtime_tracking' as item, CONCAT('Records: ', COALESCE(COUNT(*), 0)) as description FROM ta_overtime_tracking
UNION ALL
SELECT 'ta_overtime_frequency' as item, CONCAT('Records: ', COALESCE(COUNT(*), 0)) as description FROM ta_overtime_frequency
UNION ALL
SELECT 'ta_attendance_metrics' as item, CONCAT('Records: ', COALESCE(COUNT(*), 0)) as description FROM ta_attendance_metrics;
