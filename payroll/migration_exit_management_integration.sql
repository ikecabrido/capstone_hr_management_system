-- =====================================================
-- Payroll Exit Management Integration
-- Date: 2026-04-01
-- Purpose: Add exit management tables and modify payroll 
--          to connect with exit management system
-- =====================================================

-- =====================================================
-- 1. CREATE exit_resignations TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `exit_resignations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `resignation_type` enum('voluntary','involuntary') NOT NULL,
  `reason` text NOT NULL,
  `notice_date` date NOT NULL,
  `last_working_date` date NOT NULL COMMENT 'Critical: Used to calculate pro-rata payroll',
  `comments` text DEFAULT NULL,
  `submitted_by` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected','withdrawn') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  KEY `status` (`status`),
  CONSTRAINT `fk_exit_resignation_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees`(`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Track employee resignations and exit dates';

-- =====================================================
-- 2. CREATE exit_employee_settlements TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `exit_employee_settlements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) DEFAULT NULL,
  `resignation_id` int(11) DEFAULT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `hra` decimal(10,2) DEFAULT 0.00,
  `conveyance` decimal(10,2) DEFAULT 0.00,
  `lta` decimal(10,2) DEFAULT 0.00,
  `medical_allowance` decimal(10,2) DEFAULT 0.00,
  `other_allowances` decimal(10,2) DEFAULT 0.00,
  `provident_fund` decimal(10,2) DEFAULT 0.00,
  `gratuity` decimal(10,2) DEFAULT 0.00 COMMENT 'Added to final payslip earnings',
  `notice_pay` decimal(10,2) DEFAULT 0.00 COMMENT 'Added to final payslip earnings',
  `outstanding_loans` decimal(10,2) DEFAULT 0.00 COMMENT 'Deducted from final payslip',
  `other_deductions` decimal(10,2) DEFAULT 0.00 COMMENT 'Deducted from final payslip',
  `net_payable` decimal(10,2) NOT NULL,
  `settlement_date` date NOT NULL,
  `status` enum('draft','approved','paid') DEFAULT 'draft',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  KEY `resignation_id` (`resignation_id`),
  KEY `status` (`status`),
  CONSTRAINT `fk_settlement_resignation` FOREIGN KEY (`resignation_id`) REFERENCES `exit_resignations`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_settlement_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees`(`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Final settlement calculations for exiting employees';

-- =====================================================
-- 3. MODIFY pr_payslips TABLE - Add Exit Links
-- =====================================================
-- Check if columns exist before adding
ALTER TABLE `pr_payslips` 
ADD COLUMN IF NOT EXISTS `is_exit_settlement` tinyint(1) DEFAULT 0 COMMENT 'Flag: 1 if this is an exit/final payslip',
ADD COLUMN IF NOT EXISTS `settlement_id` int(11) DEFAULT NULL COMMENT 'Links to exit_employee_settlements.id',
ADD COLUMN IF NOT EXISTS `resignation_id` int(11) DEFAULT NULL COMMENT 'Links to exit_resignations.id',
ADD KEY IF NOT EXISTS `idx_is_exit_settlement` (`is_exit_settlement`),
ADD KEY IF NOT EXISTS `fk_settlement_id` (`settlement_id`),
ADD KEY IF NOT EXISTS `fk_resignation_id` (`resignation_id`);

-- Add foreign key constraints if tables exist
ALTER TABLE `pr_payslips`
ADD CONSTRAINT `fk_payslip_settlement` FOREIGN KEY (`settlement_id`) REFERENCES `exit_employee_settlements`(`id`) ON DELETE SET NULL;

ALTER TABLE `pr_payslips`
ADD CONSTRAINT `fk_payslip_resignation` FOREIGN KEY (`resignation_id`) REFERENCES `exit_resignations`(`id`) ON DELETE SET NULL;

-- =====================================================
-- 4. SUMMARY OF CHANGES
-- =====================================================
-- Tables Added:
-- - exit_resignations: Tracks employee resignations
-- - exit_employee_settlements: Tracks settlement details
--
-- Columns Added to pr_payslips:
-- - is_exit_settlement: Boolean flag
-- - settlement_id: Foreign key to settlements
-- - resignation_id: Foreign key to resignations
--
-- =====================================================
