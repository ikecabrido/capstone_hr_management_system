-- Migration: add leave management, notifications, holidays, and department_heads
-- Non-destructive: uses IF NOT EXISTS / ADD COLUMN IF NOT EXISTS where supported

USE `time_and_attendance`;

-- Leave Types
CREATE TABLE IF NOT EXISTS leave_types (
  leave_type_id INT PRIMARY KEY AUTO_INCREMENT,
  leave_type_name VARCHAR(50) NOT NULL UNIQUE,
  description VARCHAR(255),
  days_per_year INT NOT NULL DEFAULT 10,
  is_deductible BOOLEAN DEFAULT 1,
  requires_approval BOOLEAN DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Leave Balances
CREATE TABLE IF NOT EXISTS leave_balances (
  balance_id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id INT NOT NULL,
  leave_type_id INT NOT NULL,
  year INT NOT NULL,
  total_days INT NOT NULL DEFAULT 0,
  used_days INT NOT NULL DEFAULT 0,
  remaining_days INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_balance (employee_id, leave_type_id, year),
  INDEX idx_employee_id (employee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Leave Requests
CREATE TABLE IF NOT EXISTS leave_requests (
  leave_request_id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id INT NOT NULL,
  leave_type_id INT NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  reason VARCHAR(500),
  total_days INT NOT NULL,
  status ENUM('PENDING','APPROVED_BY_HEAD','APPROVED_BY_HR','REJECTED','CANCELLED') NOT NULL DEFAULT 'PENDING',
  submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  department_head_id INT,
  department_head_approval_date DATETIME,
  department_head_remarks VARCHAR(500),
  hr_admin_id INT,
  hr_admin_approval_date DATETIME,
  hr_admin_remarks VARCHAR(500),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_employee_id (employee_id),
  INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Department Heads
CREATE TABLE IF NOT EXISTS department_heads (
  dept_head_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL UNIQUE,
  department VARCHAR(100) NOT NULL,
  supervises_from DATE,
  supervises_until DATE,
  is_active BOOLEAN DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Notifications
CREATE TABLE IF NOT EXISTS notifications (
  notification_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  employee_id INT,
  notification_type VARCHAR(50) NOT NULL,
  title VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  related_id INT,
  related_type VARCHAR(50),
  is_read BOOLEAN DEFAULT 0,
  send_via_email BOOLEAN DEFAULT 1,
  send_via_sms BOOLEAN DEFAULT 1,
  email_sent BOOLEAN DEFAULT 0,
  sms_sent BOOLEAN DEFAULT 0,
  sms_status ENUM('QUEUED','SENT','FAILED','SIMULATED') DEFAULT 'QUEUED',
  email_status ENUM('QUEUED','SENT','FAILED') DEFAULT 'QUEUED',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  sent_at DATETIME,
  read_at DATETIME,
  INDEX idx_user_id (user_id),
  INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Holidays
CREATE TABLE IF NOT EXISTS holidays (
  holiday_id INT PRIMARY KEY AUTO_INCREMENT,
  holiday_date DATE NOT NULL UNIQUE,
  holiday_name VARCHAR(100) NOT NULL,
  description VARCHAR(255),
  is_working_day BOOLEAN DEFAULT 0,
  year INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_holiday_date (holiday_date),
  INDEX idx_year (year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add hours/overtime columns to attendance if missing
ALTER TABLE attendance
  ADD COLUMN IF NOT EXISTS total_hours_worked DECIMAL(5,2) NULL,
  ADD COLUMN IF NOT EXISTS regular_hours DECIMAL(5,2) NULL,
  ADD COLUMN IF NOT EXISTS overtime_hours DECIMAL(5,2) NULL,
  ADD COLUMN IF NOT EXISTS is_within_time_window TINYINT(1) DEFAULT 1,
  ADD COLUMN IF NOT EXISTS is_within_timeout_window TINYINT(1) DEFAULT 1;

-- Foreign keys (MariaDB compatible using prepared statements for conditional adds)
DELIMITER $$

CREATE PROCEDURE add_fk_if_not_exists(
  p_table VARCHAR(64),
  p_constraint_name VARCHAR(64),
  p_alter_sql VARCHAR(500)
)
BEGIN
  DECLARE constraint_exists INT;
  SELECT COUNT(*) INTO constraint_exists FROM information_schema.KEY_COLUMN_USAGE 
    WHERE CONSTRAINT_NAME = p_constraint_name AND TABLE_NAME = p_table;
  IF constraint_exists = 0 THEN
    SET @sql = p_alter_sql;
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END$$

DELIMITER ;

CALL add_fk_if_not_exists('leave_balances', 'fk_lb_employee', 
  'ALTER TABLE leave_balances ADD CONSTRAINT fk_lb_employee FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE');

CALL add_fk_if_not_exists('leave_balances', 'fk_lb_type', 
  'ALTER TABLE leave_balances ADD CONSTRAINT fk_lb_type FOREIGN KEY (leave_type_id) REFERENCES leave_types(leave_type_id)');

CALL add_fk_if_not_exists('leave_requests', 'fk_lr_employee', 
  'ALTER TABLE leave_requests ADD CONSTRAINT fk_lr_employee FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE');

CALL add_fk_if_not_exists('leave_requests', 'fk_lr_type', 
  'ALTER TABLE leave_requests ADD CONSTRAINT fk_lr_type FOREIGN KEY (leave_type_id) REFERENCES leave_types(leave_type_id)');

DROP PROCEDURE add_fk_if_not_exists;

-- Seed standard leave types if not present
INSERT INTO leave_types (leave_type_name, description, days_per_year, is_deductible, requires_approval)
SELECT 'Sick Leave', 'For medical reasons and illness', 10, 1, 1
WHERE NOT EXISTS (SELECT 1 FROM leave_types WHERE leave_type_name = 'Sick Leave');

INSERT INTO leave_types (leave_type_name, description, days_per_year, is_deductible, requires_approval)
SELECT 'Vacation Leave', 'Annual paid time off', 10, 1, 1
WHERE NOT EXISTS (SELECT 1 FROM leave_types WHERE leave_type_name = 'Vacation Leave');

INSERT INTO leave_types (leave_type_name, description, days_per_year, is_deductible, requires_approval)
SELECT 'Emergency Leave', 'For unforeseen circumstances', 5, 1, 1
WHERE NOT EXISTS (SELECT 1 FROM leave_types WHERE leave_type_name = 'Emergency Leave');

COMMIT;
