-- =====================================================
-- VIOLATION WORKFLOW PROCESS - DATABASE MIGRATION
-- HR Compliance Management System
-- =====================================================

-- Add new columns to incidents table for comprehensive workflow
ALTER TABLE `incidents` 
    ADD COLUMN `incident_id` VARCHAR(20) NULL AFTER `id`,
    ADD COLUMN `incident_time` TIME NULL AFTER `incident_date`,
    ADD COLUMN `severity` ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium' AFTER `incident_type`,
    ADD COLUMN `category` VARCHAR(100) NULL AFTER `severity`,
    ADD COLUMN `violation_type` ENUM('minor', 'major') DEFAULT 'minor' AFTER `category`,
    ADD COLUMN `complainant_name` VARCHAR(100) NULL AFTER `location`,
    ADD COLUMN `respondent_name` VARCHAR(100) NULL AFTER `complainant_name`,
    ADD COLUMN `witnesses` TEXT NULL AFTER `respondent_name`,
    ADD COLUMN `reported_by` VARCHAR(50) DEFAULT 'Employee' AFTER `witnesses`,
    ADD COLUMN `assigned_hr_id` INT(11) NULL AFTER `assigned_to`,
    ADD COLUMN `decision` VARCHAR(100) NULL AFTER `resolution_notes`,
    ADD COLUMN `approved_by` INT(11) NULL AFTER `decision`,
    ADD COLUMN `approved_at` TIMESTAMP NULL AFTER `approved_by`,
    ADD COLUMN `rejection_reason` TEXT NULL AFTER `approved_at`,
    ADD COLUMN `request_info_notes` TEXT NULL AFTER `rejection_reason`,
    ADD COLUMN `sla_deadline` TIMESTAMP NULL AFTER `request_info_notes`,
    ADD COLUMN `escalation_level` INT(1) DEFAULT 0 AFTER `sla_deadline`,
    ADD COLUMN `repeat_offender` TINYINT(1) DEFAULT 0 AFTER `escalation_level`,
    ADD COLUMN `violation_count` INT(11) DEFAULT 0 AFTER `repeat_offender`,
    ADD COLUMN `remarks` TEXT NULL AFTER `violation_count`,
    ADD COLUMN `previous_status` VARCHAR(20) NULL AFTER `remarks`,
    ADD COLUMN `status_changed_at` TIMESTAMP NULL AFTER `previous_status`;

-- Update status enum to include new workflow statuses
ALTER TABLE `incidents` 
    MODIFY COLUMN `status` ENUM('open', 'under_review', 'in_progress', 'pending_approval', 'resolved', 'rejected', 'escalated', 'closed_no_violation') DEFAULT 'open';

-- Create index for faster queries
CREATE INDEX idx_incidents_status ON incidents(status);
CREATE INDEX idx_incidents_severity ON incidents(severity);
CREATE INDEX idx_incidents_incident_id ON incidents(incident_id);
CREATE INDEX idx_incidents_assigned_hr ON incidents(assigned_hr_id);
CREATE INDEX idx_incidents_sla_deadline ON incidents(sla_deadline);

-- =====================================================
-- INCIDENT AUDIT LOG TABLE
-- For complete traceability and audit trail
-- =====================================================
CREATE TABLE IF NOT EXISTS `incident_audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `incident_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `old_status` varchar(20) DEFAULT NULL,
  `new_status` varchar(20) DEFAULT NULL,
  `performed_by` int(11) NOT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  INDEX idx_audit_incident (`incident_id`),
  INDEX idx_audit_created (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- INCIDENT NOTIFICATIONS TABLE
-- For in-system notifications
-- =====================================================
CREATE TABLE IF NOT EXISTS `incident_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `incident_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  INDEX idx_notif_incident (`incident_id`),
  INDEX idx_notif_user (`user_id`),
  INDEX idx_notif_read (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- UPDATE EXISTING DATA
-- Convert old status values to new format
-- =====================================================
UPDATE incidents SET status = 'open' WHERE status = 'filed';
UPDATE incidents SET status = 'in_progress' WHERE status = 'investigating';
