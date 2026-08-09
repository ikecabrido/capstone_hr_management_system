-- Migration: create ta_custom_shifts and ta_custom_shift_times (with break columns)
-- Run this on your DB (MySQL) to add tables used by the fixed-schedule generator

CREATE TABLE IF NOT EXISTS `ta_custom_shifts` (
  `custom_shift_id` INT(11) NOT NULL AUTO_INCREMENT,
  `employee_id` INT(11) NOT NULL,
  `shift_date` DATE NOT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`custom_shift_id`),
  UNIQUE KEY `uniq_employee_date` (`employee_id`,`shift_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ta_custom_shift_times` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `custom_shift_id` INT(11) NOT NULL,
  `start_time` DATETIME NOT NULL,
  `end_time` DATETIME NOT NULL,
  `break_start` TIME DEFAULT NULL,
  `break_end` TIME DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_custom_shift` (`custom_shift_id`),
  CONSTRAINT `fk_custom_shift_times_custom_shift` FOREIGN KEY (`custom_shift_id`) REFERENCES `ta_custom_shifts` (`custom_shift_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
