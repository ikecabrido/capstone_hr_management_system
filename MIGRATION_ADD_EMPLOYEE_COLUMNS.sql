-- ============================================================
-- EMPLOYEES TABLE MIGRATION
-- Add Master Table Foreign Keys and Employee Enhancement Columns
-- Date: March 22, 2026
-- Database: hr_management
-- ============================================================

-- ============================================================
-- STEP 1: ADD FOREIGN KEY COLUMNS (linking to master tables)
-- ============================================================

-- Add Department ID (foreign key)
ALTER TABLE `employees` ADD COLUMN `department_id` INT DEFAULT NULL AFTER `department`;
ALTER TABLE `employees` ADD KEY `fk_emp_department_id` (`department_id`);

-- Add Position ID (foreign key)
ALTER TABLE `employees` ADD COLUMN `position_id` INT DEFAULT NULL AFTER `position`;
ALTER TABLE `employees` ADD KEY `fk_emp_position_id` (`position_id`);

-- Add Employment Type ID (foreign key)
ALTER TABLE `employees` ADD COLUMN `employment_type_id` INT DEFAULT NULL AFTER `employment_status`;
ALTER TABLE `employees` ADD KEY `fk_emp_employment_type_id` (`employment_type_id`);

-- Add Location ID (foreign key)
ALTER TABLE `employees` ADD COLUMN `location_id` INT DEFAULT NULL AFTER `address`;
ALTER TABLE `employees` ADD KEY `fk_emp_location_id` (`location_id`);

-- Add Salary Grade ID (foreign key)
ALTER TABLE `employees` ADD COLUMN `salary_grade_id` INT DEFAULT NULL;
ALTER TABLE `employees` ADD KEY `fk_emp_salary_grade_id` (`salary_grade_id`);

-- ============================================================
-- STEP 2: ADD EMPLOYEE DEMOGRAPHIC COLUMNS
-- ============================================================

-- Add Gender
ALTER TABLE `employees` ADD COLUMN `gender` ENUM('Male', 'Female', 'Other', 'Prefer not to say') DEFAULT NULL;
ALTER TABLE `employees` ADD KEY `idx_emp_gender` (`gender`);

-- Add Date of Birth
ALTER TABLE `employees` ADD COLUMN `date_of_birth` DATE DEFAULT NULL;
ALTER TABLE `employees` ADD KEY `idx_emp_dob` (`date_of_birth`);

-- Add Age Group (Stored column - will be updated by triggers)
-- Note: Cannot use GENERATED ALWAYS with CURDATE(), so using stored column with triggers
ALTER TABLE `employees` ADD COLUMN `age_group` VARCHAR(50) DEFAULT NULL;
ALTER TABLE `employees` ADD KEY `idx_emp_age_group` (`age_group`);

-- Add Marital Status
ALTER TABLE `employees` ADD COLUMN `marital_status` ENUM('Single', 'Married', 'Divorced', 'Widowed', 'Prefer not to say') DEFAULT NULL;

-- Add Nationality
ALTER TABLE `employees` ADD COLUMN `nationality` VARCHAR(100) DEFAULT NULL;

-- Add PAN/Tax ID Number
ALTER TABLE `employees` ADD COLUMN `pan_number` VARCHAR(50) DEFAULT NULL COMMENT 'Tax ID / SSN / National ID number';
ALTER TABLE `employees` ADD KEY `idx_emp_pan_number` (`pan_number`);

-- ============================================================
-- STEP 3: ADD ORGANIZATIONAL HIERARCHY COLUMNS
-- ============================================================

-- Add Manager ID (self-reference for reporting hierarchy)
ALTER TABLE `employees` ADD COLUMN `manager_id` VARCHAR(50) DEFAULT NULL;
ALTER TABLE `employees` ADD KEY `fk_emp_manager_id` (`manager_id`);

-- ============================================================
-- STEP 4: ADD SALARY/COMPENSATION COLUMNS
-- ============================================================

-- Add Base Salary
ALTER TABLE `employees` ADD COLUMN `base_salary` DECIMAL(12,2) DEFAULT 0.00;
ALTER TABLE `employees` ADD KEY `idx_emp_base_salary` (`base_salary`);

-- Add Currency Code
ALTER TABLE `employees` ADD COLUMN `currency` VARCHAR(3) DEFAULT 'PHP';

-- Add Bank Account Number (should be encrypted in production)
ALTER TABLE `employees` ADD COLUMN `bank_account_number` VARCHAR(50) DEFAULT NULL COMMENT 'Encrypted in production';

-- Add Bank Name
ALTER TABLE `employees` ADD COLUMN `bank_name` VARCHAR(100) DEFAULT NULL;

-- ============================================================
-- STEP 5: ADD EMERGENCY CONTACT COLUMNS
-- ============================================================

-- Add Emergency Contact Name
ALTER TABLE `employees` ADD COLUMN `emergency_contact_name` VARCHAR(150) DEFAULT NULL;

-- Add Emergency Contact Phone
ALTER TABLE `employees` ADD COLUMN `emergency_contact_phone` VARCHAR(20) DEFAULT NULL;

-- Add Emergency Contact Relationship
ALTER TABLE `employees` ADD COLUMN `emergency_contact_relation` VARCHAR(50) DEFAULT NULL;

-- ============================================================
-- STEP 6: ADD EMPLOYMENT STATUS & DATE COLUMNS
-- ============================================================

-- Add Probation End Date
ALTER TABLE `employees` ADD COLUMN `probation_end_date` DATE DEFAULT NULL;
ALTER TABLE `employees` ADD KEY `idx_emp_probation_end` (`probation_end_date`);

-- Add Confirmation Date
ALTER TABLE `employees` ADD COLUMN `confirmation_date` DATE DEFAULT NULL;

-- Add Retirement Eligible Date
ALTER TABLE `employees` ADD COLUMN `retirement_eligible_date` DATE DEFAULT NULL;
ALTER TABLE `employees` ADD KEY `idx_emp_retirement_eligible` (`retirement_eligible_date`);

-- Add Enhanced Employee Status (more granular than employment_status)
ALTER TABLE `employees` ADD COLUMN `employee_status` ENUM('Active', 'On Leave', 'On Probation', 'Inactive', 'Retired', 'Terminated', 'Resigned') DEFAULT 'Active';
ALTER TABLE `employees` ADD KEY `idx_emp_employee_status` (`employee_status`);

-- ============================================================
-- STEP 7: ADD FOREIGN KEY CONSTRAINTS
-- ============================================================

-- Department Foreign Key
ALTER TABLE `employees` ADD CONSTRAINT `fk_emp_department` 
  FOREIGN KEY (`department_id`) 
  REFERENCES `departments` (`department_id`) 
  ON DELETE SET NULL;

-- Position Foreign Key
ALTER TABLE `employees` ADD CONSTRAINT `fk_emp_position` 
  FOREIGN KEY (`position_id`) 
  REFERENCES `positions` (`position_id`) 
  ON DELETE SET NULL;

-- Employment Type Foreign Key
ALTER TABLE `employees` ADD CONSTRAINT `fk_emp_employment_type` 
  FOREIGN KEY (`employment_type_id`) 
  REFERENCES `employment_types` (`employment_type_id`) 
  ON DELETE SET NULL;

-- Location Foreign Key
ALTER TABLE `employees` ADD CONSTRAINT `fk_emp_location` 
  FOREIGN KEY (`location_id`) 
  REFERENCES `locations` (`location_id`) 
  ON DELETE SET NULL;

-- Salary Grade Foreign Key
ALTER TABLE `employees` ADD CONSTRAINT `fk_emp_salary_grade` 
  FOREIGN KEY (`salary_grade_id`) 
  REFERENCES `salary_grades` (`salary_grade_id`) 
  ON DELETE SET NULL;

-- Manager Foreign Key (self-reference)
ALTER TABLE `employees` ADD CONSTRAINT `fk_emp_manager` 
  FOREIGN KEY (`manager_id`) 
  REFERENCES `employees` (`employee_id`) 
  ON DELETE SET NULL;

-- ============================================================
-- STEP 7B: CREATE TRIGGERS TO AUTO-UPDATE AGE_GROUP
-- ============================================================

-- DELIMITER to allow multi-line triggers
DELIMITER $$

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

-- Reset DELIMITER back to normal
DELIMITER ;

-- ============================================================
-- STEP 8: UPDATED EMPLOYEES TABLE STRUCTURE (for reference)
-- ============================================================
/*
Final employees table columns:

--- Original Columns ---
employee_id             VARCHAR(50) PRIMARY KEY
full_name              VARCHAR(255)
email                  VARCHAR(255)
contact_number         VARCHAR(20)
date_hired             DATE
created_at             TIMESTAMP
updated_at             TIMESTAMP
user_id                INT (FK to users)

--- Address/Location ---
address                TEXT
location_id            INT (FK to locations) NEW

--- Organization ---
department             VARCHAR(100) (old text field, keep for migration)
department_id          INT (FK to departments) NEW
position               VARCHAR(100) (old text field, keep for migration)
position_id            INT (FK to positions) NEW
manager_id             VARCHAR(50) (self-ref to employees) NEW

--- Employment ---
employment_status      VARCHAR(50) (old field)
employment_type_id     INT (FK to employment_types) NEW
employee_status        ENUM NEW
salary_grade_id        INT (FK to salary_grades) NEW

--- Demographics ---
gender                 ENUM NEW
date_of_birth          DATE NEW
age_group              VARCHAR(50) GENERATED STORED NEW
nationality            VARCHAR(100) NEW
marital_status         ENUM NEW
pan_number             VARCHAR(50) NEW

--- Salary/Bank ---
base_salary            DECIMAL(12,2) NEW
currency               VARCHAR(3) NEW
bank_account_number    VARCHAR(50) NEW
bank_name              VARCHAR(100) NEW

--- Emergency Contact ---
emergency_contact_name     VARCHAR(150) NEW
emergency_contact_phone    VARCHAR(20) NEW
emergency_contact_relation VARCHAR(50) NEW

--- Employment Dates ---
probation_end_date          DATE NEW
confirmation_date           DATE NEW
retirement_eligible_date    DATE NEW
*/

-- ============================================================
-- STEP 9: CREATE INDEXES FOR COMMON QUERIES
-- ============================================================

-- Index for finding employees by department
CREATE INDEX `idx_emp_dept_status` ON `employees`(`department_id`, `employee_status`);

-- Index for finding employees by position
CREATE INDEX `idx_emp_position_status` ON `employees`(`position_id`, `employee_status`);

-- Index for finding employees by manager
CREATE INDEX `idx_emp_manager_status` ON `employees`(`manager_id`, `employee_status`);

-- Index for finding employees by location
CREATE INDEX `idx_emp_location_status` ON `employees`(`location_id`, `employee_status`);

-- Index for finding employees by employment type
CREATE INDEX `idx_emp_employment_type_status` ON `employees`(`employment_type_id`, `employee_status`);

-- Composite index for diversity queries
CREATE INDEX `idx_emp_gender_age_dept` ON `employees`(`gender`, `age_group`, `department_id`);

-- Index for salary analysis
CREATE INDEX `idx_emp_base_salary_grade` ON `employees`(`base_salary`, `salary_grade_id`);

-- ============================================================
-- STEP 10: DATA MIGRATION NOTES
-- ============================================================
/*
IMPORTANT: After running this migration, you should:

1. POPULATE department_id from department text field:
   UPDATE employees e
   SET e.department_id = (
     SELECT d.department_id FROM departments d 
     WHERE LOWER(d.department_name) = LOWER(e.department)
     LIMIT 1
   )
   WHERE e.department IS NOT NULL AND e.department_id IS NULL;

2. POPULATE position_id from position text field:
   UPDATE employees e
   SET e.position_id = (
     SELECT p.position_id FROM positions p 
     WHERE LOWER(p.position_title) = LOWER(e.position)
     LIMIT 1
   )
   WHERE e.position IS NOT NULL AND e.position_id IS NULL;

3. SET employment_type_id (for existing employees, default to Full-Time):
   UPDATE employees e
   SET e.employment_type_id = (
     SELECT et.employment_type_id FROM employment_types et 
     WHERE et.employment_type_code = 'FT'
     LIMIT 1
   )
   WHERE e.employment_type_id IS NULL;

4. SET location_id (for existing employees, default to Head Office):
   UPDATE employees e
   SET e.location_id = (
     SELECT l.location_id FROM locations l 
     WHERE l.location_code = 'HQ'
     LIMIT 1
   )
   WHERE e.location_id IS NULL;

5. SET date_hired if NULL (required for age calculation):
   -- Review and update manually or use CURDATE()

6. POPULATE manager_id based on department heads:
   -- Set department heads as managers for their departments
   -- This is a manual process based on your org structure
*/

-- ============================================================
-- END OF MIGRATION SCRIPT
-- ============================================================
