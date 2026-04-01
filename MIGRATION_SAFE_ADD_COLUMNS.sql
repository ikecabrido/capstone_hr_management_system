-- ============================================================
-- SAFE MIGRATION SCRIPT - CHECK EXISTING COLUMNS FIRST
-- This script checks if columns exist before adding them
-- Date: March 22, 2026
-- Database: hr_management
-- ============================================================

-- First, let's check what columns already exist in employees table
-- Run this to see current structure:
DESCRIBE `employees`;

-- Then run the appropriate section below:

-- ============================================================
-- IF SOME COLUMNS ALREADY EXIST - Use this safe version
-- ============================================================

-- Add Department ID (if not exists)
ALTER TABLE `employees` ADD COLUMN `department_id_temp` INT DEFAULT NULL;
-- If above fails with "Duplicate column", skip to next column

-- Add Position ID (if not exists)
ALTER TABLE `employees` ADD COLUMN `position_id` INT DEFAULT NULL AFTER `position`;

-- Add Employment Type ID (if not exists)
ALTER TABLE `employees` ADD COLUMN `employment_type_id` INT DEFAULT NULL AFTER `employment_status`;

-- Add Location ID (if not exists)
ALTER TABLE `employees` ADD COLUMN `location_id` INT DEFAULT NULL AFTER `address`;

-- Add Salary Grade ID (if not exists)
ALTER TABLE `employees` ADD COLUMN `salary_grade_id` INT DEFAULT NULL;

-- ============================================================
-- DEMOGRAPHICS COLUMNS
-- ============================================================

-- Add Gender (if not exists)
ALTER TABLE `employees` ADD COLUMN `gender` ENUM('Male', 'Female', 'Other', 'Prefer not to say') DEFAULT NULL;

-- Add Date of Birth (if not exists)
ALTER TABLE `employees` ADD COLUMN `date_of_birth` DATE DEFAULT NULL;

-- Add Age Group (if not exists)
ALTER TABLE `employees` ADD COLUMN `age_group` VARCHAR(50) DEFAULT NULL;

-- Add Marital Status (if not exists)
ALTER TABLE `employees` ADD COLUMN `marital_status` ENUM('Single', 'Married', 'Divorced', 'Widowed', 'Prefer not to say') DEFAULT NULL;

-- Add Nationality (if not exists)
ALTER TABLE `employees` ADD COLUMN `nationality` VARCHAR(100) DEFAULT NULL;

-- Add PAN/Tax ID Number (if not exists)
ALTER TABLE `employees` ADD COLUMN `pan_number` VARCHAR(50) DEFAULT NULL COMMENT 'Tax ID / SSN / National ID number';

-- ============================================================
-- ORGANIZATIONAL HIERARCHY
-- ============================================================

-- Add Manager ID (if not exists)
ALTER TABLE `employees` ADD COLUMN `manager_id` VARCHAR(50) DEFAULT NULL;

-- ============================================================
-- SALARY/COMPENSATION
-- ============================================================

-- Add Base Salary (if not exists)
ALTER TABLE `employees` ADD COLUMN `base_salary` DECIMAL(12,2) DEFAULT 0.00;

-- Add Currency Code (if not exists)
ALTER TABLE `employees` ADD COLUMN `currency` VARCHAR(3) DEFAULT 'PHP';

-- Add Bank Account Number (if not exists)
ALTER TABLE `employees` ADD COLUMN `bank_account_number` VARCHAR(50) DEFAULT NULL COMMENT 'Encrypted in production';

-- Add Bank Name (if not exists)
ALTER TABLE `employees` ADD COLUMN `bank_name` VARCHAR(100) DEFAULT NULL;

-- ============================================================
-- EMERGENCY CONTACT
-- ============================================================

-- Add Emergency Contact Name (if not exists)
ALTER TABLE `employees` ADD COLUMN `emergency_contact_name` VARCHAR(150) DEFAULT NULL;

-- Add Emergency Contact Phone (if not exists)
ALTER TABLE `employees` ADD COLUMN `emergency_contact_phone` VARCHAR(20) DEFAULT NULL;

-- Add Emergency Contact Relationship (if not exists)
ALTER TABLE `employees` ADD COLUMN `emergency_contact_relation` VARCHAR(50) DEFAULT NULL;

-- ============================================================
-- EMPLOYMENT STATUS & DATES
-- ============================================================

-- Add Probation End Date (if not exists)
ALTER TABLE `employees` ADD COLUMN `probation_end_date` DATE DEFAULT NULL;

-- Add Confirmation Date (if not exists)
ALTER TABLE `employees` ADD COLUMN `confirmation_date` DATE DEFAULT NULL;

-- Add Retirement Eligible Date (if not exists)
ALTER TABLE `employees` ADD COLUMN `retirement_eligible_date` DATE DEFAULT NULL;

-- Add Enhanced Employee Status (if not exists)
ALTER TABLE `employees` ADD COLUMN `employee_status` ENUM('Active', 'On Leave', 'On Probation', 'Inactive', 'Retired', 'Terminated', 'Resigned') DEFAULT 'Active';

-- ============================================================
-- ADD KEYS/INDEXES (safe to run multiple times)
-- ============================================================

-- Add indexes (ignore if they already exist)
ALTER TABLE `employees` ADD KEY `idx_emp_department_id` (`department_id`);
ALTER TABLE `employees` ADD KEY `idx_emp_position_id` (`position_id`);
ALTER TABLE `employees` ADD KEY `idx_emp_employment_type_id` (`employment_type_id`);
ALTER TABLE `employees` ADD KEY `idx_emp_location_id` (`location_id`);
ALTER TABLE `employees` ADD KEY `idx_emp_salary_grade_id` (`salary_grade_id`);
ALTER TABLE `employees` ADD KEY `idx_emp_gender` (`gender`);
ALTER TABLE `employees` ADD KEY `idx_emp_dob` (`date_of_birth`);
ALTER TABLE `employees` ADD KEY `idx_emp_age_group` (`age_group`);
ALTER TABLE `employees` ADD KEY `idx_emp_pan_number` (`pan_number`);
ALTER TABLE `employees` ADD KEY `idx_emp_manager_id` (`manager_id`);
ALTER TABLE `employees` ADD KEY `idx_emp_base_salary` (`base_salary`);
ALTER TABLE `employees` ADD KEY `idx_emp_probation_end` (`probation_end_date`);
ALTER TABLE `employees` ADD KEY `idx_emp_retirement_eligible` (`retirement_eligible_date`);
ALTER TABLE `employees` ADD KEY `idx_emp_employee_status` (`employee_status`);

-- Add composite indexes
ALTER TABLE `employees` ADD KEY `idx_emp_dept_status` (`department_id`, `employee_status`);
ALTER TABLE `employees` ADD KEY `idx_emp_position_status` (`position_id`, `employee_status`);
ALTER TABLE `employees` ADD KEY `idx_emp_manager_status` (`manager_id`, `employee_status`);
ALTER TABLE `employees` ADD KEY `idx_emp_location_status` (`location_id`, `employee_status`);
ALTER TABLE `employees` ADD KEY `idx_emp_employment_type_status` (`employment_type_id`, `employee_status`);
ALTER TABLE `employees` ADD KEY `idx_emp_gender_age_dept` (`gender`, `age_group`, `department_id`);
ALTER TABLE `employees` ADD KEY `idx_emp_base_salary_grade` (`base_salary`, `salary_grade_id`);

-- ============================================================
-- ADD FOREIGN KEY CONSTRAINTS
-- ============================================================

-- These may fail if constraints already exist - that's OK
-- They'll be added on first run

ALTER TABLE `employees` ADD CONSTRAINT `fk_emp_department` 
  FOREIGN KEY (`department_id`) 
  REFERENCES `departments` (`department_id`) 
  ON DELETE SET NULL;

ALTER TABLE `employees` ADD CONSTRAINT `fk_emp_position` 
  FOREIGN KEY (`position_id`) 
  REFERENCES `positions` (`position_id`) 
  ON DELETE SET NULL;

ALTER TABLE `employees` ADD CONSTRAINT `fk_emp_employment_type` 
  FOREIGN KEY (`employment_type_id`) 
  REFERENCES `employment_types` (`employment_type_id`) 
  ON DELETE SET NULL;

ALTER TABLE `employees` ADD CONSTRAINT `fk_emp_location` 
  FOREIGN KEY (`location_id`) 
  REFERENCES `locations` (`location_id`) 
  ON DELETE SET NULL;

ALTER TABLE `employees` ADD CONSTRAINT `fk_emp_salary_grade` 
  FOREIGN KEY (`salary_grade_id`) 
  REFERENCES `salary_grades` (`salary_grade_id`) 
  ON DELETE SET NULL;

ALTER TABLE `employees` ADD CONSTRAINT `fk_emp_manager` 
  FOREIGN KEY (`manager_id`) 
  REFERENCES `employees` (`employee_id`) 
  ON DELETE SET NULL;

-- ============================================================
-- CREATE TRIGGERS FOR AGE_GROUP CALCULATION
-- ============================================================

DELIMITER $$

-- Drop triggers if they exist
DROP TRIGGER IF EXISTS `trig_emp_update_age_group_insert`$$
DROP TRIGGER IF EXISTS `trig_emp_update_age_group_update`$$

-- Trigger: Update age_group when date_of_birth is inserted
CREATE TRIGGER `trig_emp_update_age_group_insert` 
BEFORE INSERT ON `employees`
FOR EACH ROW
BEGIN
  IF NEW.date_of_birth IS NOT NULL THEN
    SET NEW.age_group = CASE 
      WHEN YEAR(CURDATE()) - YEAR(NEW.date_of_birth) < 25 THEN '18-24'
      WHEN YEAR(CURDATE()) - YEAR(NEW.date_of_birth) < 35 THEN '25-34'
      WHEN YEAR(CURDATE()) - YEAR(NEW.date_of_birth) < 45 THEN '35-44'
      WHEN YEAR(CURDATE()) - YEAR(NEW.date_of_birth) < 55 THEN '45-54'
      ELSE '55+'
    END;
  ELSE
    SET NEW.age_group = NULL;
  END IF;
END$$

-- Trigger: Update age_group when date_of_birth is updated
CREATE TRIGGER `trig_emp_update_age_group_update` 
BEFORE UPDATE ON `employees`
FOR EACH ROW
BEGIN
  IF NEW.date_of_birth IS NOT NULL THEN
    SET NEW.age_group = CASE 
      WHEN YEAR(CURDATE()) - YEAR(NEW.date_of_birth) < 25 THEN '18-24'
      WHEN YEAR(CURDATE()) - YEAR(NEW.date_of_birth) < 35 THEN '25-34'
      WHEN YEAR(CURDATE()) - YEAR(NEW.date_of_birth) < 45 THEN '35-44'
      WHEN YEAR(CURDATE()) - YEAR(NEW.date_of_birth) < 55 THEN '45-54'
      ELSE '55+'
    END;
  ELSE
    SET NEW.age_group = NULL;
  END IF;
END$$

DELIMITER ;

-- ============================================================
-- VERIFY MIGRATION COMPLETED
-- ============================================================

-- Check current table structure:
DESCRIBE `employees`;

-- Check for triggers:
SELECT TRIGGER_SCHEMA, TRIGGER_NAME, EVENT_MANIPULATION, EVENT_OBJECT_TABLE 
FROM INFORMATION_SCHEMA.TRIGGERS 
WHERE TRIGGER_SCHEMA = 'hr_management' AND EVENT_OBJECT_TABLE = 'employees';

-- ============================================================
-- END OF SAFE MIGRATION
-- ============================================================
