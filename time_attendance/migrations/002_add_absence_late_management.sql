-- ============================================================================
-- TIME & ATTENDANCE: Absence & Late Management System
-- ============================================================================
-- This migration adds the necessary table and columns to support:
-- - Tracking excuse status (excused/unexcused)
-- - Recording reasons and notes
-- - Managing absence/late incidents
-- ============================================================================

-- Table to track absence and late arrivals with excuse status
CREATE TABLE IF NOT EXISTS `ta_absence_late_records` (
    `record_id` INT PRIMARY KEY AUTO_INCREMENT,
    `attendance_id` INT,
    `employee_id` VARCHAR(50) NOT NULL,
    `absence_date` DATE NOT NULL,
    `type` ENUM('ABSENT', 'LATE') NOT NULL,
    `is_excused` BOOLEAN DEFAULT FALSE,
    `excuse_status` ENUM('PENDING', 'APPROVED', 'REJECTED', 'AWAITING_DOCUMENTS') DEFAULT 'PENDING',
    `excuse_type` ENUM('MANUAL_APPEAL', 'APPROVED_LEAVE') DEFAULT 'MANUAL_APPEAL',
    `leave_request_id` INT,
    `reason` TEXT,
    `notes` TEXT,
    `supporting_document_url` VARCHAR(255),
    `submitted_by` INT,
    `submitted_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `reviewed_by` INT,
    `reviewed_date` DATETIME,
    `approval_notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`employee_id`) REFERENCES `employees`(`employee_id`) ON DELETE CASCADE,
    KEY `idx_employee_date` (`employee_id`, `absence_date`),
    KEY `idx_excuse_status` (`excuse_status`),
    KEY `idx_type` (`type`),
    KEY `idx_leave_request` (`leave_request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table to track absence/late thresholds and warnings
CREATE TABLE IF NOT EXISTS `ta_absence_late_thresholds` (
    `threshold_id` INT PRIMARY KEY AUTO_INCREMENT,
    `employee_id` VARCHAR(50) NOT NULL,
    `month_year` VARCHAR(7) NOT NULL,
    `absent_count` INT DEFAULT 0,
    `late_count` INT DEFAULT 0,
    `excused_absent_count` INT DEFAULT 0,
    `excused_late_count` INT DEFAULT 0,
    `warning_level` ENUM('NONE', 'LEVEL_1', 'LEVEL_2', 'LEVEL_3') DEFAULT 'NONE',
    `warning_date` DATETIME,
    `last_action_taken` VARCHAR(100),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`employee_id`) REFERENCES `employees`(`employee_id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_employee_month` (`employee_id`, `month_year`),
    KEY `idx_warning_level` (`warning_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for absence/late policies and rules
CREATE TABLE IF NOT EXISTS `ta_absence_late_policies` (
    `policy_id` INT PRIMARY KEY AUTO_INCREMENT,
    `policy_name` VARCHAR(100) NOT NULL,
    `max_late_per_month` INT DEFAULT 3,
    `max_absent_per_month` INT DEFAULT 2,
    `max_excused_absent_per_month` INT DEFAULT 2,
    `max_excused_late_per_month` INT DEFAULT 5,
    `warning_after_late_count` INT DEFAULT 5,
    `warning_after_absent_count` INT DEFAULT 3,
    `late_threshold_minutes` INT DEFAULT 15,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default policy
INSERT INTO `ta_absence_late_policies` 
(policy_name, max_late_per_month, max_absent_per_month, max_excused_absent_per_month, max_excused_late_per_month, warning_after_late_count, warning_after_absent_count, late_threshold_minutes)
VALUES 
('Default Company Policy', 3, 2, 2, 5, 5, 3, 15)
ON DUPLICATE KEY UPDATE policy_id=policy_id;

-- Verification Query - Check tables exist and record counts
SELECT 'ta_absence_late_records' as table_name, COUNT(*) as record_count FROM ta_absence_late_records
UNION ALL
SELECT 'ta_absence_late_thresholds', COUNT(*) FROM ta_absence_late_thresholds
UNION ALL
SELECT 'ta_absence_late_policies', COUNT(*) FROM ta_absence_late_policies;
