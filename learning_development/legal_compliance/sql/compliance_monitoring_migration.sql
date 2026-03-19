-- =====================================================
-- HR COMPLIANCE MONITORING SYSTEM DATABASE
-- =====================================================

-- Create compliance_items table
CREATE TABLE IF NOT EXISTS compliance_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compliance_id VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    category ENUM('Labor Law', 'Company Policy', 'Health & Safety', 'Data Privacy', 'Payroll', 'Other') NOT NULL,
    subcategory VARCHAR(100),
    description TEXT,
    department VARCHAR(100),
    responsible_person_id INT,
    frequency ENUM('Daily', 'Weekly', 'Monthly', 'Quarterly', 'Yearly', 'One-time') DEFAULT 'Monthly',
    due_date DATE,
    status ENUM('Compliant', 'Pending', 'Non-Compliant', 'Overdue') DEFAULT 'Pending',
    risk_level ENUM('Low', 'Medium', 'High', 'Critical') DEFAULT 'Low',
    last_checked DATETIME,
    remarks TEXT,
    attachment_path VARCHAR(255),
    is_recurring TINYINT(1) DEFAULT 0,
    parent_item_id INT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (responsible_person_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_item_id) REFERENCES compliance_items(id) ON DELETE SET NULL,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_department (department),
    INDEX idx_due_date (due_date),
    INDEX idx_risk_level (risk_level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create compliance_logs table for audit trail
CREATE TABLE IF NOT EXISTS compliance_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compliance_item_id INT NOT NULL,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    old_value JSON,
    new_value JSON,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (compliance_item_id) REFERENCES compliance_items(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_compliance_item (compliance_item_id),
    INDEX idx_user (user_id),
    INDEX idx_timestamp (timestamp),
    INDEX idx_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create alerts table for notifications
CREATE TABLE IF NOT EXISTS alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compliance_item_id INT,
    alert_type ENUM('upcoming_deadline', 'overdue', 'critical_non_compliance', 'status_change', 'risk_elevated', 'system') NOT NULL,
    priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    message TEXT NOT NULL,
    recipient_id INT,
    is_read TINYINT(1) DEFAULT 0,
    is_resolved TINYINT(1) DEFAULT 0,
    resolved_at DATETIME,
    resolved_by INT,
    snoozed_until DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (compliance_item_id) REFERENCES compliance_items(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_compliance (compliance_item_id),
    INDEX idx_recipient (recipient_id),
    INDEX idx_is_read (is_read),
    INDEX idx_is_resolved (is_resolved),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create risk_indicators table
CREATE TABLE IF NOT EXISTS risk_indicators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compliance_item_id INT NOT NULL,
    risk_level ENUM('Low', 'Medium', 'High', 'Critical') NOT NULL,
    triggering_condition VARCHAR(255) NOT NULL,
    mitigation_steps TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (compliance_item_id) REFERENCES compliance_items(id) ON DELETE CASCADE,
    INDEX idx_compliance (compliance_item_id),
    INDEX idx_risk_level (risk_level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create alert_config table for configurable thresholds
CREATE TABLE IF NOT EXISTS alert_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100),
    alert_type VARCHAR(50) NOT NULL,
    days_before_deadline INT DEFAULT 7,
    priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    is_enabled TINYINT(1) DEFAULT 1,
    notify_responsible_person TINYINT(1) DEFAULT 1,
    notify_hr_admin TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_alert_type (alert_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create compliance_department_summary view for analytics
CREATE TABLE IF NOT EXISTS compliance_department_summary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department VARCHAR(100) NOT NULL,
    total_items INT DEFAULT 0,
    compliant_count INT DEFAULT 0,
    pending_count INT DEFAULT 0,
    non_compliant_count INT DEFAULT 0,
    overdue_count INT DEFAULT 0,
    compliance_score DECIMAL(5,2) DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_department (department)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create compliance_reports table for scheduled reports
CREATE TABLE IF NOT EXISTS compliance_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_name VARCHAR(255) NOT NULL,
    report_type ENUM('daily', 'weekly', 'monthly', 'quarterly', 'custom') NOT NULL,
    filters JSON,
    generated_by INT,
    file_path VARCHAR(255),
    status ENUM('pending', 'generating', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME,
    FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default alert configurations
INSERT INTO alert_config (category, alert_type, days_before_deadline, priority, is_enabled) VALUES
('Labor Law', 'upcoming_deadline', 7, 'low', 1),
('Labor Law', 'upcoming_deadline', 3, 'medium', 1),
('Labor Law', 'upcoming_deadline', 1, 'high', 1),
('Labor Law', 'overdue', 0, 'critical', 1),
('Company Policy', 'upcoming_deadline', 7, 'low', 1),
('Company Policy', 'upcoming_deadline', 3, 'medium', 1),
('Company Policy', 'overdue', 0, 'high', 1),
('Health & Safety', 'upcoming_deadline', 7, 'medium', 1),
('Health & Safety', 'upcoming_deadline', 3, 'high', 1),
('Health & Safety', 'overdue', 0, 'critical', 1),
('Data Privacy', 'upcoming_deadline', 7, 'medium', 1),
('Data Privacy', 'overdue', 0, 'critical', 1),
('Payroll', 'upcoming_deadline', 7, 'medium', 1),
('Payroll', 'overdue', 0, 'critical', 1),
('Other', 'upcoming_deadline', 7, 'low', 1),
('Other', 'overdue', 0, 'high', 1);

-- Insert sample compliance items for testing
INSERT INTO compliance_items (compliance_id, name, category, subcategory, description, department, responsible_person_id, frequency, due_date, status, risk_level, remarks, created_by) VALUES
('COMP-001', 'Maternity Leave Compliance', 'Labor Law', 'Leave Benefits', 'Ensure all pregnant employees receive 105 days paid maternity leave as per RA 11210', 'Human Resources', 1, 'Yearly', '2026-12-31', 'Compliant', 'High', 'All maternity leaves are properly documented', 1),
('COMP-002', 'Paternity Leave Processing', 'Labor Law', 'Leave Benefits', 'Process 7 days paternity leave for qualifying employees as per RA 8187', 'Human Resources', 1, 'Yearly', '2026-12-31', 'Compliant', 'Medium', 'Paternity leaves are being processed', 1),
('COMP-003', 'Solo Parent Leave Implementation', 'Labor Law', 'Leave Benefits', 'Implement 7 days solo parent leave per RA 8972 for qualifying employees', 'Human Resources', 1, 'Yearly', '2026-12-31', 'Pending', 'Medium', 'Awaiting policy update', 1),
('COMP-004', 'OSHA Safety Training', 'Health & Safety', 'Training', 'Conduct quarterly safety training for all employees per RA 11058', 'Operations', 1, 'Quarterly', '2026-03-31', 'Compliant', 'High', 'Q1 training completed', 1),
('COMP-005', 'Data Privacy Compliance', 'Data Privacy', 'Data Protection', 'Ensure compliance with Data Privacy Act RA 10173 - data mapping and privacy impact assessments', 'IT', 1, 'Yearly', '2026-06-30', 'Pending', 'High', 'Privacy impact assessment in progress', 1),
('COMP-006', 'Anti-Sexual Harassment Policy', 'Company Policy', 'Policy Compliance', 'Maintain zero-tolerance policy for sexual harassment per RA 7877', 'Human Resources', 1, 'Monthly', '2026-03-31', 'Compliant', 'Critical', 'Policy posted and acknowledged', 1),
('COMP-007', 'Safe Spaces Act Compliance', 'Labor Law', 'Workplace Safety', 'Ensure workplace safety from gender-based harassment per RA 11313', 'Human Resources', 1, 'Monthly', '2026-03-31', 'Compliant', 'High', 'Complaint mechanism in place', 1),
('COMP-008', 'Payroll Tax Compliance', 'Payroll', 'Tax', 'Ensure accurate withholding and remittance of taxes per BIR regulations', 'Finance', 1, 'Monthly', '2026-03-25', 'Compliant', 'High', 'Tax filings up to date', 1),
('COMP-009', 'SSS Contribution Remittance', 'Payroll', 'Benefits', 'Monthly SSS contribution remittance for all employees', 'Finance', 1, 'Monthly', '2026-03-31', 'Pending', 'Medium', 'Awaiting HR verification', 1),
('COMP-010', 'PhilHealth Contribution', 'Payroll', 'Benefits', 'Monthly PhilHealth contribution remittance', 'Finance', 1, 'Monthly', '2026-03-31', 'Pending', 'Medium', 'Awaiting HR verification', 1),
('COMP-011', 'Pag-IBIG Contribution', 'Payroll', 'Benefits', 'Monthly Pag-IBIG contribution remittance', 'Finance', 1, 'Monthly', '2026-03-31', 'Compliant', 'Low', 'Contributions remitted', 1),
('COMP-012', 'Employee Manual Update', 'Company Policy', 'Documentation', 'Review and update employee handbook annually', 'Human Resources', 1, 'Yearly', '2026-06-30', 'Pending', 'Low', 'Review scheduled for Q2', 1),
('COMP-013', 'Workplace Fire Safety Inspection', 'Health & Safety', 'Safety', 'Annual fire safety inspection and certification', 'Operations', 1, 'Yearly', '2026-09-30', 'Compliant', 'Critical', 'Inspection passed', 1),
('COMP-014', 'First Aid Kit Maintenance', 'Health & Safety', 'Safety', 'Monthly inspection and restocking of first aid kits', 'Operations', 1, 'Monthly', '2026-03-31', 'Non-Compliant', 'Medium', 'Several kits need restocking', 1),
('COMP-015', 'Employee Data Encryption', 'Data Privacy', 'Data Protection', 'Ensure all employee personal data is encrypted at rest', 'IT', 1, 'Quarterly', '2026-06-30', 'Pending', 'High', 'Encryption audit in progress', 1);

-- Insert sample risk indicators
INSERT INTO risk_indicators (compliance_item_id, risk_level, triggering_condition, mitigation_steps) VALUES
(3, 'Medium', 'Pending status for more than 30 days', 'Expedite policy review and approval process'),
(5, 'High', 'Non-compliant status for data privacy', 'Complete privacy impact assessment immediately'),
(9, 'High', 'Pending SSS contribution for more than 5 days', 'Process payment immediately to avoid penalties'),
(10, 'High', 'Pending PhilHealth contribution for more than 5 days', 'Process payment immediately to avoid penalties'),
(14, 'Medium', 'First aid kits not fully stocked', 'Order supplies and restock within 24 hours');

-- Insert sample alerts
INSERT INTO alerts (compliance_item_id, alert_type, priority, message, recipient_id) VALUES
(4, 'upcoming_deadline', 'low', 'Q1 Safety Training deadline approaching on March 31, 2026', 1),
(8, 'upcoming_deadline', 'medium', 'Payroll tax compliance deadline on March 25, 2026', 1),
(9, 'upcoming_deadline', 'medium', 'SSS contribution deadline approaching on March 31, 2026', 1),
(14, 'critical_non_compliance', 'critical', 'First Aid Kit Maintenance is marked as Non-Compliant - immediate action required', 1);

-- Create stored procedure to calculate compliance score
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS calculate_compliance_score()
BEGIN
    DECLARE total_items INT;
    DECLARE compliant_items INT;
    DECLARE non_compliant_items INT;
    DECLARE overdue_items INT;
    DECLARE score DECIMAL(5,2);
    
    SELECT COUNT(*) INTO total_items FROM compliance_items;
    
    IF total_items > 0 THEN
        SELECT COUNT(*) INTO compliant_items FROM compliance_items WHERE status = 'Compliant';
        SELECT COUNT(*) INTO non_compliant_items FROM compliance_items WHERE status = 'Non-Compliant';
        SELECT COUNT(*) INTO overdue_items FROM compliance_items WHERE status = 'Overdue';
        
        SET score = ((compliant_items * 100) / total_items) 
                    - (overdue_items * 2) 
                    - (non_compliant_items * 5);
        
        IF score < 0 THEN
            SET score = 0;
        END IF;
        
        SELECT score as compliance_score, total_items, compliant_items, non_compliant_items, overdue_items;
    ELSE
        SELECT 0 as compliance_score, 0 as total_items, 0 as compliant_items, 0 as non_compliant_items, 0 as overdue_items;
    END IF;
END //
DELIMITER ;

-- Create stored procedure to check overdue items
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS check_overdue_items()
BEGIN
    UPDATE compliance_items 
    SET status = 'Overdue', 
        risk_level = CASE 
            WHEN risk_level = 'Low' THEN 'Medium'
            WHEN risk_level = 'Medium' THEN 'High'
            WHEN risk_level = 'High' THEN 'Critical'
            ELSE 'Critical'
        END
    WHERE due_date < CURDATE() 
    AND status NOT IN ('Compliant', 'Overdue');
    
    -- Insert alert for newly overdue items
    INSERT INTO alerts (compliance_item_id, alert_type, priority, message, recipient_id, created_at)
    SELECT id, 'overdue', 'critical', 
           CONCAT('Compliance item "', name, '" is now overdue'), 
           responsible_person_id, NOW()
    FROM compliance_items 
    WHERE due_date < CURDATE() 
    AND status = 'Overdue'
    AND id NOT IN (SELECT compliance_item_id FROM alerts WHERE alert_type = 'overdue' AND created_at > DATE_SUB(NOW(), INTERVAL 1 DAY));
END //
DELIMITER ;

-- Create stored procedure to generate upcoming deadline alerts
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS generate_deadline_alerts()
BEGIN
    -- 7 days before deadline
    INSERT INTO alerts (compliance_item_id, alert_type, priority, message, recipient_id)
    SELECT id, 'upcoming_deadline', 'low',
           CONCAT('Compliance item "', name, '" due in 7 days'),
           responsible_person_id
    FROM compliance_items
    WHERE due_date = DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    AND status NOT IN ('Compliant', 'Overdue')
    AND id NOT IN (SELECT compliance_item_id FROM alerts WHERE alert_type = 'upcoming_deadline' AND created_at > DATE_SUB(NOW(), INTERVAL 1 DAY));
    
    -- 3 days before deadline
    INSERT INTO alerts (compliance_item_id, alert_type, priority, message, recipient_id)
    SELECT id, 'upcoming_deadline', 'medium',
           CONCAT('Compliance item "', name, '" due in 3 days'),
           responsible_person_id
    FROM compliance_items
    WHERE due_date = DATE_ADD(CURDATE(), INTERVAL 3 DAY)
    AND status NOT IN ('Compliant', 'Overdue')
    AND id NOT IN (SELECT compliance_item_id FROM alerts WHERE alert_type = 'upcoming_deadline' AND created_at > DATE_SUB(NOW(), INTERVAL 1 DAY));
    
    -- 1 day before deadline
    INSERT INTO alerts (compliance_item_id, alert_type, priority, message, recipient_id)
    SELECT id, 'upcoming_deadline', 'high',
           CONCAT('Compliance item "', name, '" due tomorrow'),
           responsible_person_id
    FROM compliance_items
    WHERE due_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
    AND status NOT IN ('Compliant', 'Overdue')
    AND id NOT IN (SELECT compliance_item_id FROM alerts WHERE alert_type = 'upcoming_deadline' AND created_at > DATE_SUB(NOW(), INTERVAL 1 DAY));
END //
DELIMITER ;
