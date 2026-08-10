-- Migration: create ta_shift_weekday_times and drop legacy ta_custom_shifts tables
-- BACKUP recommended BEFORE running. Run these on your DB (phpMyAdmin or mysql CLI).

START TRANSACTION;

-- Create weekday times table for shift templates
CREATE TABLE IF NOT EXISTS `ta_shift_weekday_times` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `shift_id` INT NOT NULL,
  `weekday` TINYINT NOT NULL COMMENT '0=Sunday,1=Monday...6=Saturday',
  `start_time` TIME NULL,
  `end_time` TIME NULL,
  `break_start` TIME NULL,
  `break_end` TIME NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (`shift_id`),
  INDEX (`weekday`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- OPTIONAL: Backup old tables before dropping (uncomment to perform backup)
-- RENAME TABLE `ta_custom_shifts` TO `ta_custom_shifts_bak_20260810`;
-- RENAME TABLE `ta_custom_shift_times` TO `ta_custom_shift_times_bak_20260810`;

-- DROP legacy custom shift tables
DROP TABLE IF EXISTS `ta_custom_shift_times`;
DROP TABLE IF EXISTS `ta_custom_shifts`;

COMMIT;
