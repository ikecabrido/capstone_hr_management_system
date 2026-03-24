-- =====================================================
-- INCIDENT DISCIPLINARY WORKFLOW SYSTEM - MIGRATION
-- Add to sample_hr database
-- =====================================================

-- =====================================================
-- 1. UPDATE INCIDENTS TABLE - Add workflow columns
-- =====================================================
ALTER TABLE `incidents` 
    ADD COLUMN `respondent_id` INT(11) DEFAULT NULL AFTER `respondent_name`,
    ADD COLUMN `reporter_name` VARCHAR(100) DEFAULT NULL AFTER `reporter_id`,
    ADD COLUMN `current_workflow_step` ENUM('submitted', 'under_review', 'nte_issued', 'explanation_received', 'hr_evaluation', 'decision_made', 'final_action', 'closed') DEFAULT 'submitted' AFTER `status`,
    ADD COLUMN `nte_deadline` DATE DEFAULT NULL AFTER `current_workflow_step`,
    ADD COLUMN `explanation_deadline` DATE DEFAULT NULL AFTER `nte_deadline`,
    ADD COLUMN `final_decision` VARCHAR(100) DEFAULT NULL AFTER `respondent_id`,
    ADD COLUMN `final_decision_date` DATE DEFAULT NULL AFTER `final_decision`,
    ADD COLUMN `closed_at` TIMESTAMP NULL DEFAULT NULL AFTER `final_decision_date`,
    ADD COLUMN `closure_reason` TEXT DEFAULT NULL AFTER `closed_at`,
    ADD COLUMN `created_by` INT(11) DEFAULT NULL AFTER `closure_reason`;

-- Update status to include new workflow statuses
ALTER TABLE `incidents` 
    MODIFY COLUMN `status` ENUM('open', 'under_review', 'in_progress', 'pending_approval', 'resolved', 'rejected', 'escalated', 'closed_no_violation', 'submitted', 'nte_issued', 'explanation_received', 'hr_evaluation', 'decision_made', 'final_action', 'closed') DEFAULT 'submitted';

-- Create indexes
CREATE INDEX idx_incidents_workflow ON incidents(current_workflow_step);
CREATE INDEX idx_incidents_respondent ON incidents(respondent_id);
CREATE INDEX idx_incidents_nte_deadline ON incidents(nte_deadline);
CREATE INDEX idx_incidents_explanation_deadline ON incidents(explanation_deadline);

-- =====================================================
-- 2. INCIDENT WORKFLOW TABLE
-- Tracks step-by-step disciplinary process
-- =====================================================
CREATE TABLE IF NOT EXISTS `incident_workflow` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `incident_id` INT(11) NOT NULL,
  `step` ENUM('submitted', 'nte_issued', 'explanation_received', 'hr_evaluation', 'decision_made', 'final_action', 'closed') NOT NULL DEFAULT 'submitted',
  `step_status` ENUM('pending', 'in_progress', 'completed', 'skipped', 'rejected') DEFAULT 'pending',
  `started_at` TIMESTAMP NULL DEFAULT NULL,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  `deadline` TIMESTAMP NULL DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `performed_by` INT(11) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX idx_workflow_incident (`incident_id`),
  INDEX idx_workflow_step (`step`),
  INDEX idx_workflow_status (`step_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 3. DISCIPLINARY ACTIONS TABLE
-- Stores HR decisions and actions taken
-- =====================================================
CREATE TABLE IF NOT EXISTS `disciplinary_actions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `incident_id` INT(11) NOT NULL,
  `action_type` ENUM('verbal_warning', 'written_warning', 'suspension', 'termination', 'case_dismissed', 'counseling', 'probation') NOT NULL,
  `action_details` TEXT DEFAULT NULL,
  `action_date` DATE NOT NULL,
  `effective_date` DATE DEFAULT NULL,
  `duration_days` INT(11) DEFAULT NULL,
  `issued_by` INT(11) NOT NULL,
  `approved_by` INT(11) DEFAULT NULL,
  `approved_at` TIMESTAMP NULL DEFAULT NULL,
  `is_final` TINYINT(1) DEFAULT 0,
  `document_path` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX idx_action_incident (`incident_id`),
  INDEX idx_action_type (`action_type`),
  INDEX idx_action_date (`action_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 4. EMPLOYEE EXPLANATIONS TABLE
-- Stores employee responses to incidents/NTE
-- =====================================================
CREATE TABLE IF NOT EXISTS `explanations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `incident_id` INT(11) NOT NULL,
  `employee_id` INT(11) NOT NULL,
  `explanation_text` TEXT NOT NULL,
  `submission_method` ENUM('online', 'written', 'verbal_recorded') DEFAULT 'online',
  `submitted_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_late` TINYINT(1) DEFAULT 0,
  `late_reason` TEXT DEFAULT NULL,
  `attachments` VARCHAR(255) DEFAULT NULL,
  `reviewed_by` INT(11) DEFAULT NULL,
  `reviewed_at` TIMESTAMP NULL DEFAULT NULL,
  `review_notes` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX idx_explanation_incident (`incident_id`),
  INDEX idx_explanation_employee (`employee_id`),
  INDEX idx_explanation_submitted (`submitted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 5. NOTICE TO EXPLAIN (NTE) TABLE
-- Stores NTE details issued to employees
-- =====================================================
CREATE TABLE IF NOT EXISTS `notice_to_explain` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `incident_id` INT(11) NOT NULL,
  `nte_number` VARCHAR(50) NOT NULL UNIQUE,
  `issued_to` INT(11) NOT NULL,
  `issued_by` INT(11) NOT NULL,
  `issue_date` DATE NOT NULL,
  `deadline_date` DATE NOT NULL,
  `nte_content` TEXT NOT NULL,
  `delivery_method` ENUM('email', 'physical', 'both') DEFAULT 'email',
  `delivered_at` TIMESTAMP NULL DEFAULT NULL,
  `is_received` TINYINT(1) DEFAULT 0,
  `received_signature` VARCHAR(255) DEFAULT NULL,
  `reminder_sent` TINYINT(1) DEFAULT 0,
  `reminder_sent_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX idx_nte_incident (`incident_id`),
  INDEX idx_nte_issued_to (`issued_to`),
  INDEX idx_nte_number (`nte_number`),
  INDEX idx_nte_deadline (`deadline_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 6. EMAIL NOTIFICATIONS LOG TABLE
-- Tracks all email notifications sent
-- =====================================================
CREATE TABLE IF NOT EXISTS `incident_email_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `incident_id` INT(11) DEFAULT NULL,
  `recipient_id` INT(11) NOT NULL,
  `recipient_email` VARCHAR(255) NOT NULL,
  `email_type` ENUM('incident_submitted', 'nte_issued', 'nte_reminder', 'explanation_received', 'decision_notice', 'case_closed', 'hr_review') NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `body` TEXT NOT NULL,
  `sent_by` INT(11) NOT NULL,
  `sent_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('pending', 'sent', 'failed', 'bounced') DEFAULT 'pending',
  `error_message` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX idx_email_incident (`incident_id`),
  INDEX idx_email_recipient (`recipient_id`),
  INDEX idx_email_type (`email_type`),
  INDEX idx_email_sent (`sent_at`),
  INDEX idx_email_status (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 7. ACTIVITY LOG TABLE
-- For complete legal tracking and audit trail
-- =====================================================
CREATE TABLE IF NOT EXISTS `incident_activity_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `incident_id` INT(11) NOT NULL,
  `activity_type` VARCHAR(50) NOT NULL,
  `activity_description` TEXT NOT NULL,
  `performed_by` INT(11) NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `metadata` JSON DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX idx_activity_incident (`incident_id`),
  INDEX idx_activity_type (`activity_type`),
  INDEX idx_activity_performed (`performed_by`),
  INDEX idx_activity_created (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 8. WORKFLOW CONFIGURATION TABLE
-- Define workflow step configurations and deadlines
-- =====================================================
CREATE TABLE IF NOT EXISTS `workflow_config` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `step` VARCHAR(50) NOT NULL UNIQUE,
  `step_order` INT(11) NOT NULL,
  `display_name` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `default_deadline_hours` INT(11) DEFAULT NULL,
  `allowed_actions` JSON DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  INDEX idx_config_order (`step_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default workflow steps
INSERT INTO `workflow_config` (`step`, `step_order`, `display_name`, `description`, `default_deadline_hours`, `allowed_actions`, `is_active`) VALUES
('submitted', 1, 'Incident Submitted', 'Incident has been reported and logged', NULL, '["view", "edit"]', 1),
('under_review', 2, 'Under HR Review', 'HR is reviewing the incident', 48, '["approve", "reject", "issue_nte"]', 1),
('nte_issued', 3, 'Notice to Explain Issued', 'NTE has been issued to employee', 48, '["view_nte", "send_reminder"]', 1),
('explanation_received', 4, 'Explanation Received', 'Employee has submitted explanation', 72, '["evaluate", "request_more_info"]', 1),
('hr_evaluation', 5, 'HR Evaluation', 'HR is evaluating the case', 48, '["decide"]', 1),
('decision_made', 6, 'Decision Made', 'HR has made a decision', NULL, '["view_decision", "finalize"]', 1),
('final_action', 7, 'Final Action', 'Final disciplinary action recorded', 24, '["notify_employee", "close_case"]', 1),
('closed', 8, 'Case Closed', 'Case has been closed', NULL, '["view"]', 1);

-- =====================================================
-- 9. SYSTEM CONFIGURATION TABLE
-- Email and notification settings
-- =====================================================
CREATE TABLE IF NOT EXISTS `incident_config` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `config_key` VARCHAR(100) NOT NULL UNIQUE,
  `config_value` TEXT NOT NULL,
  `description` TEXT DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default configurations
INSERT INTO `incident_config` (`config_key`, `config_value`, `description`) VALUES
('nte_deadline_hours', '48', 'Default hours for NTE response deadline'),
('explanation_deadline_hours', '72', 'Default hours for employee explanation deadline'),
('auto_escalate_after_hours', '168', 'Hours after which unaddressed incidents are escalated'),
('email_notifications_enabled', '1', 'Enable/disable email notifications'),
('smtp_host', 'smtp.example.com', 'SMTP server hostname'),
('smtp_port', '587', 'SMTP server port'),
('smtp_username', 'hr@bestlink.edu.ph', 'SMTP username'),
('smtp_from_email', 'hr@bestlink.edu.ph', 'From email address'),
('smtp_from_name', 'HR Legal Compliance');

-- =====================================================
-- 10. UPDATE EXISTING DATA
-- =====================================================
-- Set default workflow step for existing incidents
UPDATE incidents SET current_workflow_step = 'submitted' WHERE current_workflow_step IS NULL;

-- Set created_at for incidents without it
UPDATE incidents SET created_at = NOW() WHERE created_at IS NULL;
