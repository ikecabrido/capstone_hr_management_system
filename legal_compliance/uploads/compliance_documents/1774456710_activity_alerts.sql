-- =====================================================
-- HR LEGAL & COMPLIANCE ACTIVITY/ALERTS SYSTEM
-- Tracks HR compliance incidents, risk events, and legal compliance activities
-- =====================================================

-- --------------------------------------------------------
--
-- Table structure for table `activity_alerts`
--

CREATE TABLE `activity_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alert_type` enum('incident','risk','compliance','general') NOT NULL DEFAULT 'general',
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `severity_level` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `created_by` int(11) DEFAULT NULL COMMENT 'Foreign key to employees table',
  `assigned_to` int(11) DEFAULT NULL COMMENT 'Foreign key to employees table',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL,
  `related_entity_type` varchar(50) DEFAULT NULL COMMENT 'Polymorphic: incidents, risks, compliance_items',
  `related_entity_id` int(11) DEFAULT NULL COMMENT 'ID of related entity',
  `is_read` tinyint(1) DEFAULT 0 COMMENT 'Whether alert has been read',
  `is_notified` tinyint(1) DEFAULT 0 COMMENT 'Whether notification sent',
  `notes` text DEFAULT NULL COMMENT 'Additional notes or comments',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Indexes for frequently queried columns
--

ALTER TABLE `activity_alerts`
  ADD KEY `idx_alert_type` (`alert_type`),
  ADD KEY `idx_severity_level` (`severity_level`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_assigned_to` (`assigned_to`),
  ADD KEY `idx_related_entity` (`related_entity_type`,`related_entity_id`),
  ADD KEY `idx_is_read` (`is_read`);

-- --------------------------------------------------------
--
-- Foreign key constraints with ON DELETE SET NULL
--

ALTER TABLE `activity_alerts`
  ADD CONSTRAINT `fk_activity_alerts_created_by` 
    FOREIGN KEY (`created_by`) REFERENCES `employees` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_activity_alerts_assigned_to` 
    FOREIGN KEY (`assigned_to`) REFERENCES `employees` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE;

-- --------------------------------------------------------
--
-- Sample data for testing
--

INSERT INTO `activity_alerts` 
(`alert_type`, `title`, `description`, `severity_level`, `status`, `created_by`, `assigned_to`, `related_entity_type`, `related_entity_id`, `created_at`) VALUES
('incident', 'Workplace Safety Incident Reported', 'A workplace safety incident has been reported in the Operations department requiring immediate attention', 'high', 'open', 1, 2, 'incidents', 1, NOW()),
('risk', 'Compliance Deadline Approaching', 'Maternity Leave Compliance deadline is approaching within 7 days', 'medium', 'in_progress', 1, 1, 'compliance_items', 1, NOW()),
('compliance', 'Policy Update Required', 'Employee Manual requires annual review and update', 'low', 'open', 1, 1, 'compliance_items', 12, NOW()),
('general', 'System Maintenance', 'Scheduled system maintenance for compliance tracking module', 'low', 'resolved', 1, NULL, NULL, NULL, NOW()),
('incident', 'Harassment Investigation', 'Harassment complaint investigation in progress', 'critical', 'in_progress', 1, 2, 'incidents', 2, NOW());

-- --------------------------------------------------------
--
-- Create activity_log table for detailed audit trail
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'Foreign key to employees - who performed the action',
  `action` varchar(100) NOT NULL COMMENT 'Action type: created, updated, deleted, status_changed',
  `entity_type` varchar(50) NOT NULL COMMENT 'Table name: incidents, risks, compliance_items, etc.',
  `entity_id` int(11) NOT NULL COMMENT 'ID of affected record',
  `old_values` json DEFAULT NULL COMMENT 'JSON of previous values',
  `new_values` json DEFAULT NULL COMMENT 'JSON of new values',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Indexes for activity_log
ALTER TABLE `activity_log`
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_entity` (`entity_type`, `entity_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_action` (`action`);

-- Foreign key for activity_log
ALTER TABLE `activity_log`
  ADD CONSTRAINT `fk_activity_log_user` 
    FOREIGN KEY (`user_id`) REFERENCES `employees` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE;

-- --------------------------------------------------------
--
-- Sample activity log entries
--

INSERT INTO `activity_log` 
(`user_id`, `action`, `entity_type`, `entity_id`, `old_values`, `new_values`, `created_at`) VALUES
(1, 'created', 'incidents', 1, NULL, '{"title":"Workplace Safety Incident Reported","severity":"high","status":"open"}', NOW()),
(1, 'status_changed', 'incidents', 1, '{"status":"open"}', '{"status":"in_progress"}', NOW()),
(2, 'created', 'compliance_items', 1, NULL, '{"name":"Maternity Leave Compliance","status":"Compliant"}', NOW()),
(1, 'created', 'risks', 1, NULL, '{"title":"Data Privacy Risk","severity":"medium","status":"identified"}', NOW());

-- =====================================================
-- END OF SQL
-- =====================================================