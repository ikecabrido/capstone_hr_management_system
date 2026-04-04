-- =====================================================
-- PAYROLL EMPLOYEE CONFIGURATION TABLES
-- These tables store payroll-specific employee data
-- that is independent of the main employee table
-- =====================================================

-- Table 1: Employee Payroll Details
-- Stores ONLY payroll-specific data (base salary, position type)
-- Teacher qualifications and units come from School Management System
CREATE TABLE IF NOT EXISTS pr_employee_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL UNIQUE,
    base_salary DECIMAL(12,2) NOT NULL COMMENT 'Monthly base salary - from Legal & Compliance',
    position_type ENUM('Admin', 'Teacher', 'Other') DEFAULT 'Admin' COMMENT 'Determines calculation method',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
    INDEX idx_employee_id (employee_id),
    INDEX idx_position_type (position_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================

-- Table 2: Employee Benefits/Deductions
-- Stores trio deduction enrollment status from Legal & Compliance
CREATE TABLE IF NOT EXISTS pr_employee_benefits (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL UNIQUE,
    has_sss BOOLEAN DEFAULT 1 COMMENT 'SSS Contribution enrollment',
    has_philhealth BOOLEAN DEFAULT 1 COMMENT 'PhilHealth enrollment',
    has_pagibig BOOLEAN DEFAULT 1 COMMENT 'Pag-IBIG enrollment',
    sss_amount_override DECIMAL(10,2) DEFAULT NULL COMMENT 'Manual override if needed',
    philhealth_amount_override DECIMAL(10,2) DEFAULT NULL,
    pagibig_amount_override DECIMAL(10,2) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
    INDEX idx_employee_id (employee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================

-- Table 3: Absence/Late Deduction Rates by Position
-- Stores position-specific deduction amounts
CREATE TABLE IF NOT EXISTS pr_position_deduction_rates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    position_type ENUM('Admin', 'Teacher', 'Other') NOT NULL UNIQUE,
    absence_deduction_amount DECIMAL(10,2) NOT NULL COMMENT 'Per absence deduction',
    late_per_minute_rate DECIMAL(5,2) DEFAULT 2.00 COMMENT 'Deduction per minute late',
    late_per_hour_rate DECIMAL(5,2) DEFAULT 120.00 COMMENT 'Deduction per hour late',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_position_type (position_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================

-- Table 4: Teacher Loads (Assignments)
-- Managed by College Coordinator - Stores teaching units and qualifications
CREATE TABLE IF NOT EXISTS pr_teacher_loads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL,
    academic_year VARCHAR(10) NOT NULL COMMENT 'e.g., 2025-2026',
    semester ENUM('1st', '2nd', 'Summer') NOT NULL,
    qualification ENUM('ProfEd', 'LPT', 'Masteral') NOT NULL COMMENT 'Teacher qualification',
    total_units DECIMAL(5,2) NOT NULL COMMENT 'Total teaching units (per semester)',
    created_by VARCHAR(100),
    approved_by VARCHAR(100) COMMENT 'College Coordinator approval',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
    UNIQUE KEY unique_teacher_semester (employee_id, academic_year, semester),
    INDEX idx_employee_id (employee_id),
    INDEX idx_academic_year (academic_year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Teacher course assignments and units - managed by College Coordinator';

-- =====================================================

-- Table 5: Teacher Qualification Pay Rates
-- Stores pay per unit by teacher qualification
CREATE TABLE IF NOT EXISTS pr_teacher_qualification_rates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    qualification ENUM('ProfEd', 'LPT', 'Masteral') NOT NULL UNIQUE,
    pay_per_unit DECIMAL(10,2) NOT NULL COMMENT 'PHP per unit',
    description VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_qualification (qualification)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- SAMPLE DATA INSERTION
-- =====================================================

-- Insert Sample Teacher Qualification Rates
INSERT INTO pr_teacher_qualification_rates (qualification, pay_per_unit, description) 
VALUES 
    ('ProfEd', 128.00, 'ProfEd/Normal Teacher - Default'),
    ('LPT', 130.00, 'Licensed Professional Teacher'),
    ('Masteral', 250.00, 'Teachers with Masteral Degree');

-- Insert Sample Position Deduction Rates
INSERT INTO pr_position_deduction_rates (position_type, absence_deduction_amount, late_per_minute_rate, late_per_hour_rate) 
VALUES 
    ('Admin', 1020.00, 2.00, 120.00),
    ('Teacher', 1536.00, 2.00, 120.00),
    ('Other', 1000.00, 2.00, 120.00);

-- Insert Sample Employee Details (for employee_id 1, 2, 3)
INSERT INTO pr_employee_details (employee_id, base_salary, position_type) 
VALUES 
    (1, 30000.00, 'Admin'),
    (2, 35000.00, 'Admin'),
    (3, 32000.00, 'Admin');

-- Insert Sample Teacher Load (College Coordinator managed)
-- Example: Employee 1 teaches 30 units with Masteral qualification in 2025-2026 1st semester
INSERT INTO pr_teacher_loads (employee_id, academic_year, semester, qualification, total_units, created_by)
VALUES
    (1, '2025-2026', '1st', 'Masteral', 30.00, 'college_coordinator'),
    (1, '2025-2026', '2nd', 'Masteral', 28.00, 'college_coordinator');

-- Insert Sample Employee Benefits (all trio deductions enabled by default)
INSERT INTO pr_employee_benefits (employee_id, has_sss, has_philhealth, has_pagibig) 
VALUES 
    ('EMP001', 1, 1, 1),
    ('EMP002', 1, 1, 1),
    ('EMP003', 1, 1, 1);

-- =====================================================
-- NOTES FOR INTEGRATION:
-- =====================================================
-- 1. When Legal & Compliance provides their SSS/PhilHealth/Pag-IBIG enrollment data,
--    update the pr_employee_benefits table to match their system
--
-- 2. When Employee Management confirms teacher info, update:
--    - teacher_qualification
--    - teaching_units
--    - position_type to 'Teacher' for teachers
--
-- 3. The rates in pr_position_deduction_rates and pr_teacher_qualification_rates
--    can be updated if your professor provides different rates
--
-- 4. All employee_id values must match employees(employee_id) foreign key constraint
-- =====================================================
