-- TRAIN Law Contribution Rates Table (2026)
-- Stores SSS, PhilHealth, and Pag-IBIG rates
-- Payroll System Ownership: pr_ prefix

-- Drop existing table to ensure clean state (removes old UNIQUE constraints)
DROP TABLE IF EXISTS pr_contribution_rates;

CREATE TABLE pr_contribution_rates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contribution_type VARCHAR(50) NOT NULL,
    employee_rate DECIMAL(5, 2) NOT NULL,
    min_salary DECIMAL(10, 2) DEFAULT 0,
    max_salary DECIMAL(10, 2) DEFAULT 9999999,
    is_percentage BOOLEAN DEFAULT 1,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_contribution_type (contribution_type),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert 2026 TRAIN Law Rates (Employee Portion)
INSERT INTO pr_contribution_rates (contribution_type, employee_rate, min_salary, max_salary, is_percentage, is_active)
VALUES 
('sss', 5.00, 0, 9999999, 1, 1),
('philhealth', 2.50, 0, 9999999, 1, 1),
('pagibig', 1.00, 0, 1500, 1, 1),
('pagibig', 2.00, 1501, 9999999, 1, 1);
