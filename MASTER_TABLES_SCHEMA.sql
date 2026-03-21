-- ============================================================
-- MASTER TABLES SCHEMA
-- For: HR Management System
-- Purpose: Support employee hierarchy, positions, and organization structure
-- Date: March 22, 2026
-- ============================================================

-- ============================================================
-- 1. DEPARTMENTS TABLE
-- ============================================================
CREATE TABLE `departments` (
  `department_id` INT NOT NULL AUTO_INCREMENT,
  `department_name` VARCHAR(150) NOT NULL,
  `department_code` VARCHAR(50) UNIQUE NOT NULL COMMENT 'Department code: IT, HR, FIN, OPS, etc',
  `description` TEXT DEFAULT NULL,
  `department_head_id` VARCHAR(50) DEFAULT NULL COMMENT 'Employee ID of department head',
  `parent_department_id` INT DEFAULT NULL COMMENT 'For sub-departments/teams',
  `budget_allocated` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Annual budget for department',
  `headcount_target` INT DEFAULT 0 COMMENT 'Planned headcount',
  `location` VARCHAR(255) DEFAULT NULL COMMENT 'Physical location/office',
  `cost_center` VARCHAR(50) DEFAULT NULL COMMENT 'For accounting/finance',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`department_id`),
  UNIQUE KEY `unique_department_code` (`department_code`),
  KEY `idx_department_head` (`department_head_id`),
  KEY `idx_parent_department` (`parent_department_id`),
  KEY `idx_is_active` (`is_active`),
  
  CONSTRAINT `fk_dept_head` FOREIGN KEY (`department_head_id`) 
    REFERENCES `employees` (`employee_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. POSITIONS TABLE
-- ============================================================
CREATE TABLE `positions` (
  `position_id` INT NOT NULL AUTO_INCREMENT,
  `position_title` VARCHAR(150) NOT NULL,
  `position_code` VARCHAR(50) UNIQUE NOT NULL COMMENT 'EG: MGR001, DEV002, etc',
  `description` TEXT DEFAULT NULL,
  `department_id` INT NOT NULL,
  `job_grade` VARCHAR(50) DEFAULT NULL COMMENT 'Grade level: Grade 1, Grade 2, etc',
  `level_hierarchy` INT DEFAULT 0 COMMENT '0=Entry, 1=Mid, 2=Senior, 3=Manager, 4=Director, 5=Executive',
  `salary_grade_min` DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Minimum salary for this position',
  `salary_grade_max` DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Maximum salary for this position',
  `salary_grade_midpoint` DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Midpoint for salary ranges',
  `is_managerial` TINYINT(1) DEFAULT 0 COMMENT 'If this is a management position',
  `reports_to_position_id` INT DEFAULT NULL COMMENT 'Position this reports to',
  `required_qualifications` TEXT DEFAULT NULL COMMENT 'Education, certifications, experience',
  `key_responsibilities` TEXT DEFAULT NULL COMMENT 'Main job responsibilities',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`position_id`),
  UNIQUE KEY `unique_position_code` (`position_code`),
  KEY `idx_department_id` (`department_id`),
  KEY `idx_level_hierarchy` (`level_hierarchy`),
  KEY `idx_is_managerial` (`is_managerial`),
  KEY `idx_reports_to` (`reports_to_position_id`),
  KEY `idx_is_active` (`is_active`),
  
  CONSTRAINT `fk_position_department` FOREIGN KEY (`department_id`) 
    REFERENCES `departments` (`department_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_position_reports_to` FOREIGN KEY (`reports_to_position_id`) 
    REFERENCES `positions` (`position_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. EMPLOYMENT TYPES TABLE
-- ============================================================
CREATE TABLE `employment_types` (
  `employment_type_id` INT NOT NULL AUTO_INCREMENT,
  `employment_type_name` VARCHAR(100) NOT NULL COMMENT 'Full-time, Part-time, Contract, etc',
  `employment_type_code` VARCHAR(50) UNIQUE NOT NULL COMMENT 'FT, PT, CT, INT, etc',
  `description` TEXT DEFAULT NULL,
  `benefits_eligible` TINYINT(1) DEFAULT 1 COMMENT 'Eligible for company benefits',
  `leaves_entitled` INT DEFAULT 15 COMMENT 'Annual leave days',
  `sick_leave_entitled` INT DEFAULT 5,
  `maternity_leave_entitled` INT DEFAULT 90,
  `notice_period_days` INT DEFAULT 30 COMMENT 'Resignation notice period',
  `contract_duration_months` INT DEFAULT NULL COMMENT 'If contract position',
  `salary_type` ENUM('monthly', 'hourly', 'daily', 'weekly') DEFAULT 'monthly',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`employment_type_id`),
  UNIQUE KEY `unique_employment_code` (`employment_type_code`),
  KEY `idx_benefits_eligible` (`benefits_eligible`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. SALARY GRADES TABLE
-- ============================================================
CREATE TABLE `salary_grades` (
  `salary_grade_id` INT NOT NULL AUTO_INCREMENT,
  `grade_name` VARCHAR(50) NOT NULL COMMENT 'A, B, C, D, E or Grade 1, Grade 2, etc',
  `grade_level` INT NOT NULL,
  `min_salary` DECIMAL(12,2) NOT NULL,
  `midpoint_salary` DECIMAL(12,2) NOT NULL,
  `max_salary` DECIMAL(12,2) NOT NULL,
  `currency` VARCHAR(3) DEFAULT 'PHP' COMMENT 'PHP, USD, etc',
  `effective_from` DATE NOT NULL,
  `effective_to` DATE DEFAULT NULL COMMENT 'NULL means current',
  `description` TEXT DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`salary_grade_id`),
  UNIQUE KEY `unique_grade_level_date` (`grade_level`, `effective_from`),
  KEY `idx_grade_level` (`grade_level`),
  KEY `idx_effective_from` (`effective_from`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. QUALIFICATION/SKILLS TABLE
-- ============================================================
CREATE TABLE `qualifications` (
  `qualification_id` INT NOT NULL AUTO_INCREMENT,
  `qualification_name` VARCHAR(200) NOT NULL COMMENT 'BSc Information Technology, CCNA, PMP, etc',
  `qualification_type` ENUM('degree', 'diploma', 'certificate', 'professional_license', 'training') DEFAULT 'certificate',
  `issuing_body` VARCHAR(255) DEFAULT NULL COMMENT 'University or certification body name',
  `description` TEXT DEFAULT NULL,
  `is_mandatory` TINYINT(1) DEFAULT 0 COMMENT 'Mandatory for certain positions',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`qualification_id`),
  KEY `idx_qualification_type` (`qualification_type`),
  KEY `idx_is_mandatory` (`is_mandatory`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. EMPLOYEE QUALIFICATIONS (Link Table)
-- ============================================================
CREATE TABLE `employee_qualifications` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `employee_id` VARCHAR(50) NOT NULL,
  `qualification_id` INT NOT NULL,
  `date_obtained` DATE DEFAULT NULL,
  `expiry_date` DATE DEFAULT NULL COMMENT 'For certifications that expire',
  `certificate_number` VARCHAR(255) DEFAULT NULL COMMENT 'License/cert number',
  `issuing_body` VARCHAR(255) DEFAULT NULL,
  `attachment_path` VARCHAR(500) DEFAULT NULL COMMENT 'Path to certificate file',
  `verification_status` ENUM('pending', 'verified', 'rejected', 'expired') DEFAULT 'pending',
  `verified_by` INT DEFAULT NULL,
  `verified_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_employee_id` (`employee_id`),
  KEY `idx_qualification_id` (`qualification_id`),
  KEY `idx_verification_status` (`verification_status`),
  KEY `idx_expiry_date` (`expiry_date`),
  
  CONSTRAINT `fk_emp_qual_employee` FOREIGN KEY (`employee_id`) 
    REFERENCES `employees` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_emp_qual_qualification` FOREIGN KEY (`qualification_id`) 
    REFERENCES `qualifications` (`qualification_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_emp_qual_verified_by` FOREIGN KEY (`verified_by`) 
    REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. LOCATION/OFFICE TABLE
-- ============================================================
CREATE TABLE `locations` (
  `location_id` INT NOT NULL AUTO_INCREMENT,
  `location_name` VARCHAR(150) NOT NULL COMMENT 'Head Office, Branch - Manila, etc',
  `location_code` VARCHAR(50) UNIQUE NOT NULL COMMENT 'HQ, BR01, BR02, etc',
  `address` TEXT NOT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `state_province` VARCHAR(100) DEFAULT NULL,
  `postal_code` VARCHAR(20) DEFAULT NULL,
  `country` VARCHAR(100) DEFAULT 'Philippines',
  `phone` VARCHAR(20) DEFAULT NULL,
  `fax` VARCHAR(20) DEFAULT NULL,
  `timezone` VARCHAR(50) DEFAULT 'Asia/Manila',
  `latitude` DECIMAL(10,8) DEFAULT NULL,
  `longitude` DECIMAL(11,8) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`location_id`),
  UNIQUE KEY `unique_location_code` (`location_code`),
  KEY `idx_city` (`city`),
  KEY `idx_country` (`country`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. UPDATED EMPLOYEES TABLE (Schema Migration)
-- This shows the new columns to ADD to existing employees table
-- ============================================================
-- ALTER TABLE `employees` ADD COLUMN `department_id` INT DEFAULT NULL AFTER `department`;
-- ALTER TABLE `employees` ADD COLUMN `position_id` INT DEFAULT NULL AFTER `position`;
-- ALTER TABLE `employees` ADD COLUMN `employment_type_id` INT DEFAULT NULL AFTER `employment_status`;
-- ALTER TABLE `employees` ADD COLUMN `location_id` INT DEFAULT NULL AFTER `address`;
-- ALTER TABLE `employees` ADD COLUMN `salary_grade_id` INT DEFAULT NULL;
-- ALTER TABLE `employees` ADD COLUMN `manager_id` VARCHAR(50) DEFAULT NULL COMMENT 'Employee ID of direct manager';
-- ALTER TABLE `employees` ADD COLUMN `gender` ENUM('Male', 'Female', 'Other', 'Prefer not to say') DEFAULT NULL;
-- ALTER TABLE `employees` ADD COLUMN `date_of_birth` DATE DEFAULT NULL;
-- ALTER TABLE `employees` ADD COLUMN `age_group` VARCHAR(50) GENERATED ALWAYS AS (
--   CASE 
--     WHEN YEAR(CURDATE()) - YEAR(date_of_birth) < 25 THEN '18-24'
--     WHEN YEAR(CURDATE()) - YEAR(date_of_birth) < 35 THEN '25-34'
--     WHEN YEAR(CURDATE()) - YEAR(date_of_birth) < 45 THEN '35-44'
--     WHEN YEAR(CURDATE()) - YEAR(date_of_birth) < 55 THEN '45-54'
--     ELSE '55+'
--   END
-- ) STORED;
-- ALTER TABLE `employees` ADD COLUMN `marital_status` ENUM('Single', 'Married', 'Divorced', 'Widowed', 'Prefer not to say') DEFAULT NULL;
-- ALTER TABLE `employees` ADD COLUMN `nationality` VARCHAR(100) DEFAULT NULL;
-- ALTER TABLE `employees` ADD COLUMN `pan_number` VARCHAR(50) DEFAULT NULL COMMENT 'Tax ID / SSN equivalent';
-- ALTER TABLE `employees` ADD COLUMN `emergency_contact_name` VARCHAR(150) DEFAULT NULL;
-- ALTER TABLE `employees` ADD COLUMN `emergency_contact_phone` VARCHAR(20) DEFAULT NULL;
-- ALTER TABLE `employees` ADD COLUMN `emergency_contact_relation` VARCHAR(50) DEFAULT NULL;
-- ALTER TABLE `employees` ADD COLUMN `base_salary` DECIMAL(12,2) DEFAULT 0.00;
-- ALTER TABLE `employees` ADD COLUMN `currency` VARCHAR(3) DEFAULT 'PHP';
-- ALTER TABLE `employees` ADD COLUMN `bank_account_number` VARCHAR(50) DEFAULT NULL COMMENT 'Encrypted in production';
-- ALTER TABLE `employees` ADD COLUMN `bank_name` VARCHAR(100) DEFAULT NULL;
-- ALTER TABLE `employees` ADD COLUMN `probation_end_date` DATE DEFAULT NULL;
-- ALTER TABLE `employees` ADD COLUMN `confirmation_date` DATE DEFAULT NULL;
-- ALTER TABLE `employees` ADD COLUMN `retirement_eligible_date` DATE DEFAULT NULL;
-- ALTER TABLE `employees` ADD COLUMN `employee_status` ENUM('Active', 'On Leave', 'On Probation', 'Inactive', 'Retired') DEFAULT 'Active';
-- ALTER TABLE `employees` ADD KEY `fk_department_id` (`department_id`);
-- ALTER TABLE `employees` ADD KEY `fk_position_id` (`position_id`);
-- ALTER TABLE `employees` ADD KEY `fk_employment_type_id` (`employment_type_id`);
-- ALTER TABLE `employees` ADD KEY `fk_location_id` (`location_id`);
-- ALTER TABLE `employees` ADD KEY `fk_manager_id` (`manager_id`);
-- ALTER TABLE `employees` ADD KEY `idx_gender` (`gender`);
-- ALTER TABLE `employees` ADD KEY `idx_age_group` (`age_group`);
-- ALTER TABLE `employees` ADD CONSTRAINT `fk_emp_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE SET NULL;
-- ALTER TABLE `employees` ADD CONSTRAINT `fk_emp_position` FOREIGN KEY (`position_id`) REFERENCES `positions` (`position_id`) ON DELETE SET NULL;
-- ALTER TABLE `employees` ADD CONSTRAINT `fk_emp_employment_type` FOREIGN KEY (`employment_type_id`) REFERENCES `employment_types` (`employment_type_id`) ON DELETE SET NULL;
-- ALTER TABLE `employees` ADD CONSTRAINT `fk_emp_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`) ON DELETE SET NULL;
-- ALTER TABLE `employees` ADD CONSTRAINT `fk_emp_manager` FOREIGN KEY (`manager_id`) REFERENCES `employees` (`employee_id`) ON DELETE SET NULL;

-- ============================================================
-- SAMPLE DATA INSERTION
-- ============================================================

-- Insert Salary Grades
INSERT INTO `salary_grades` (`grade_name`, `grade_level`, `min_salary`, `midpoint_salary`, `max_salary`, `currency`, `effective_from`, `description`, `is_active`) VALUES
('Grade A - Executive', 5, 120000.00, 150000.00, 180000.00, 'PHP', '2026-01-01', 'Executive Level', 1),
('Grade B - Director', 4, 80000.00, 100000.00, 120000.00, 'PHP', '2026-01-01', 'Director/Manager Level', 1),
('Grade C - Senior', 3, 50000.00, 65000.00, 80000.00, 'PHP', '2026-01-01', 'Senior Professional', 1),
('Grade D - Mid', 2, 30000.00, 40000.00, 50000.00, 'PHP', '2026-01-01', 'Mid-level Professional', 1),
('Grade E - Entry', 1, 18000.00, 25000.00, 30000.00, 'PHP', '2026-01-01', 'Entry Level', 1),
('Grade F - Support', 0, 12000.00, 16000.00, 20000.00, 'PHP', '2026-01-01', 'Support Staff', 1);

-- Insert Locations
INSERT INTO `locations` (`location_name`, `location_code`, `address`, `city`, `state_province`, `postal_code`, `country`, `timezone`, `is_active`) VALUES
('Head Office - Manila', 'HQ', '123 Business Park, Makati', 'Makati', 'Metro Manila', '1200', 'Philippines', 'Asia/Manila', 1),
('Branch - Cebu', 'BR01', '456 SM City Cebu', 'Cebu', 'Cebu', '6000', 'Philippines', 'Asia/Manila', 1),
('Branch - Davao', 'BR02', '789 Abreeza Mall', 'Davao', 'Davao', '8000', 'Philippines', 'Asia/Manila', 1);

-- Insert Employment Types
INSERT INTO `employment_types` (`employment_type_name`, `employment_type_code`, `description`, `benefits_eligible`, `leaves_entitled`, `sick_leave_entitled`, `maternity_leave_entitled`, `notice_period_days`, `salary_type`, `is_active`) VALUES
('Full-Time Permanent', 'FT', 'Full-time permanent employee with full benefits', 1, 15, 5, 90, 30, 'monthly', 1),
('Part-Time', 'PT', 'Part-time employee', 0, 0, 0, 0, 7, 'hourly', 1),
('Contract/Temporary', 'CT', 'Contract or temporary employee', 0, 0, 0, 0, 7, 'daily', 1),
('Intern', 'INT', 'Internship program', 0, 0, 0, 0, 3, 'monthly', 1),
('Consultant', 'CONS', 'External consultant', 0, 0, 0, 0, 14, 'daily', 1);

-- Insert Departments
INSERT INTO `departments` (`department_name`, `department_code`, `description`, `budget_allocated`, `headcount_target`, `location_id`, `is_active`) VALUES
('Information Technology', 'IT', 'IT and Software Development', 500000.00, 12, 1, 1),
('Human Resources', 'HR', 'Human Resources and Administration', 300000.00, 8, 1, 1),
('Finance & Accounting', 'FIN', 'Finance, Accounting and Payroll', 400000.00, 10, 1, 1),
('Operations', 'OPS', 'Operations and Admin Support', 250000.00, 6, 1, 1),
('Sales & Marketing', 'SM', 'Sales and Marketing', 600000.00, 15, 1, 1),
('Customer Success', 'CS', 'Customer Support and Success', 350000.00, 10, 1, 1);

-- Insert Positions
INSERT INTO `positions` (`position_title`, `position_code`, `description`, `department_id`, `job_grade`, `level_hierarchy`, `salary_grade_min`, `salary_grade_max`, `salary_grade_midpoint`, `is_managerial`, `required_qualifications`, `is_active`) VALUES
('IT Manager', 'IT-MGR-001', 'Manages IT team and technical infrastructure', 1, 'Grade B', 4, 80000.00, 120000.00, 100000.00, 1, 'BS Computer Science, 8+ years IT experience', 1),
('Software Engineer', 'IT-DEV-001', 'Develops and maintains software applications', 1, 'Grade D', 2, 30000.00, 50000.00, 40000.00, 0, 'BS Computer Science or related', 1),
('Junior Developer', 'IT-DEV-002', 'Junior software developer', 1, 'Grade E', 1, 18000.00, 30000.00, 25000.00, 0, 'BS Computer Science or bootcamp', 1),
('HR Manager', 'HR-MGR-001', 'Manages HR operations and recruitment', 2, 'Grade C', 3, 50000.00, 80000.00, 65000.00, 1, 'BS Human Resources, 5+ years HR', 1),
('Finance Manager', 'FIN-MGR-001', 'Manages finance and accounting', 3, 'Grade C', 3, 50000.00, 80000.00, 65000.00, 1, 'BS Accounting/Finance, CPA preferred', 1),
('Accountant', 'FIN-ACC-001', 'Accounting and bookkeeping', 3, 'Grade D', 2, 30000.00, 50000.00, 40000.00, 0, 'BS Accounting', 1),
('Operations Coordinator', 'OPS-COORD-001', 'Administrative and operations support', 4, 'Grade E', 1, 18000.00, 30000.00, 25000.00, 0, 'HS Diploma or Bachelor\'s', 1),
('Sales Executive', 'SM-SALES-001', 'Sales and business development', 5, 'Grade D', 2, 30000.00, 50000.00, 40000.00, 0, 'Bachelor\'s degree', 1),
('Customer Support Specialist', 'CS-SUPP-001', 'Customer support and service', 6, 'Grade E', 1, 18000.00, 30000.00, 25000.00, 0, 'HS Diploma, customer service experience', 1),
('Executive Director', 'EXEC-DIR-001', 'Executive Director and company leadership', 2, 'Grade A', 5, 120000.00, 180000.00, 150000.00, 1, 'MBA, 15+ years management', 1);

-- Insert Qualifications
INSERT INTO `qualifications` (`qualification_name`, `qualification_type`, `issuing_body`, `description`, `is_mandatory`, `is_active`) VALUES
('Bachelor of Science in Computer Science', 'degree', 'Various Universities', 'BS Computer Science Degree', 0, 1),
('Bachelor of Science in Human Resources', 'degree', 'Various Universities', 'BS Human Resources Degree', 0, 1),
('Bachelor of Science in Accounting', 'degree', 'Various Universities', 'BS Accounting Degree', 0, 1),
('Certified Public Accountant (CPA)', 'professional_license', 'Professional Regulation Commission', 'CPA License', 0, 1),
('Project Management Professional (PMP)', 'professional_license', 'Project Management Institute', 'PMP Certification', 0, 1),
('Oracle Certified Associate Java Programmer', 'certificate', 'Oracle', 'Java Programming Certification', 0, 1),
('AWS Solutions Architect', 'certificate', 'Amazon Web Services', 'AWS Certification', 0, 1),
('High School Diploma', 'diploma', 'Various Schools', 'HS Diploma', 0, 1),
('HR Certification (CHRP)', 'professional_license', 'CHRP Board', 'Certified Human Resources Professional', 0, 1),
('Six Sigma Green Belt', 'certificate', 'Various Training Bodies', 'Six Sigma Green Belt', 0, 1);

-- ============================================================
-- INDEXES FOR PERFORMANCE
-- ============================================================
CREATE INDEX `idx_departments_active` ON `departments`(`is_active`);
CREATE INDEX `idx_positions_active` ON `positions`(`is_active`);
CREATE INDEX `idx_employment_types_active` ON `employment_types`(`is_active`);
CREATE INDEX `idx_locations_active` ON `locations`(`is_active`);
CREATE INDEX `idx_salary_grades_active` ON `salary_grades`(`is_active`);

-- ============================================================
-- END OF SCHEMA
-- ============================================================
