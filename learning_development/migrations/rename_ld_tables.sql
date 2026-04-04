-- Learning & Development Table Rename Migration
-- Renames LD tables with ld_ prefix and updates ID column names

SET FOREIGN_KEY_CHECKS=0;

-- Step 1: Drop all foreign key constraints
ALTER TABLE `ld_archive` DROP FOREIGN KEY `ld_archive_ibfk_1`;
ALTER TABLE `certifications` DROP FOREIGN KEY `certifications_ibfk_1`;
ALTER TABLE `certifications` DROP FOREIGN KEY `certifications_ibfk_2`;
ALTER TABLE `certifications` DROP FOREIGN KEY `certifications_ibfk_3`;
ALTER TABLE `courses` DROP FOREIGN KEY `courses_ibfk_1`;
ALTER TABLE `courses` DROP FOREIGN KEY `courses_ibfk_2`;
ALTER TABLE `elearning_modules` DROP FOREIGN KEY `elearning_modules_ibfk_1`;
ALTER TABLE `elearning_modules` DROP FOREIGN KEY `elearning_modules_ibfk_2`;
ALTER TABLE `enrollments` DROP FOREIGN KEY `enrollments_ibfk_1`;
ALTER TABLE `enrollments` DROP FOREIGN KEY `enrollments_ibfk_2`;
ALTER TABLE `virtual_sessions` DROP FOREIGN KEY `virtual_sessions_ibfk_1`;
ALTER TABLE `virtual_sessions` DROP FOREIGN KEY `virtual_sessions_ibfk_2`;

-- Step 2: Rename tables
RENAME TABLE `certifications` TO `ld_certification`;
RENAME TABLE `courses` TO `ld_courses`;
RENAME TABLE `elearning_modules` TO `ld_elearning_modules`;
RENAME TABLE `enrollments` TO `ld_enrollments`;
RENAME TABLE `training_programs` TO `ld_training_programs`;
RENAME TABLE `virtual_sessions` TO `ld_virtual_sessions`;

-- Step 3: Rename primary key columns in each table
ALTER TABLE `ld_certification` CHANGE COLUMN `id` `ld_certification_id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `ld_courses` CHANGE COLUMN `id` `ld_courses_id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `ld_elearning_modules` CHANGE COLUMN `id` `ld_elearning_modules_id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `ld_enrollments` CHANGE COLUMN `id` `ld_enrollment_id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `ld_training_programs` CHANGE COLUMN `id` `ld_training_programs_id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `ld_virtual_sessions` CHANGE COLUMN `id` `ld_virtual_sessions_id` INT(11) NOT NULL AUTO_INCREMENT;

-- Step 4: Update foreign key column references
ALTER TABLE `ld_certification` CHANGE COLUMN `course_id` `ld_courses_id` INT(11) NOT NULL;
ALTER TABLE `ld_certification` CHANGE COLUMN `issued_by` `issued_by_user_id` INT(11) NOT NULL;

ALTER TABLE `ld_courses` CHANGE COLUMN `training_program_id` `ld_training_programs_id` INT(11) DEFAULT NULL;
ALTER TABLE `ld_courses` CHANGE COLUMN `created_by` `created_by_user_id` INT(11) NOT NULL;

ALTER TABLE `ld_elearning_modules` CHANGE COLUMN `course_id` `ld_courses_id` INT(11) NOT NULL;
ALTER TABLE `ld_elearning_modules` CHANGE COLUMN `created_by` `created_by_user_id` INT(11) NOT NULL;

ALTER TABLE `ld_enrollments` CHANGE COLUMN `employee_id` `employee_user_id` INT(11) NOT NULL;
ALTER TABLE `ld_enrollments` CHANGE COLUMN `course_id` `ld_courses_id` INT(11) NOT NULL;

ALTER TABLE `ld_training_programs` CHANGE COLUMN `created_by` `created_by_user_id` INT(11) NOT NULL;

ALTER TABLE `ld_virtual_sessions` CHANGE COLUMN `course_id` `ld_courses_id` INT(11) NOT NULL;
ALTER TABLE `ld_virtual_sessions` CHANGE COLUMN `created_by` `created_by_user_id` INT(11) NOT NULL;

-- Step 5: Re-create foreign key constraints
ALTER TABLE `ld_certification`
  ADD CONSTRAINT `ld_certification_ibfk_1` FOREIGN KEY (`employee_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ld_certification_ibfk_2` FOREIGN KEY (`ld_courses_id`) REFERENCES `ld_courses` (`ld_courses_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ld_certification_ibfk_3` FOREIGN KEY (`issued_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `ld_courses`
  ADD CONSTRAINT `ld_courses_ibfk_1` FOREIGN KEY (`ld_training_programs_id`) REFERENCES `ld_training_programs` (`ld_training_programs_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ld_courses_ibfk_2` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `ld_elearning_modules`
  ADD CONSTRAINT `ld_elearning_modules_ibfk_1` FOREIGN KEY (`ld_courses_id`) REFERENCES `ld_courses` (`ld_courses_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ld_elearning_modules_ibfk_2` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `ld_enrollments`
  ADD CONSTRAINT `ld_enrollments_ibfk_1` FOREIGN KEY (`employee_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ld_enrollments_ibfk_2` FOREIGN KEY (`ld_courses_id`) REFERENCES `ld_courses` (`ld_courses_id`) ON DELETE CASCADE;

ALTER TABLE `ld_virtual_sessions`
  ADD CONSTRAINT `ld_virtual_sessions_ibfk_1` FOREIGN KEY (`ld_courses_id`) REFERENCES `ld_courses` (`ld_courses_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ld_virtual_sessions_ibfk_2` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

SET FOREIGN_KEY_CHECKS=1;

-- Migration complete
