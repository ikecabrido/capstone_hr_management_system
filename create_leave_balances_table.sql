-- ============================================
-- LEAVE BALANCE TABLE FOR HR_MANAGEMENT DATABASE
-- ============================================

-- Create Leave Balance Table
CREATE TABLE IF NOT EXISTS leave_balances (
    leave_balance_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(50) NOT NULL,
    leave_type_id INT NOT NULL,
    year INT NOT NULL,
    opening_balance DECIMAL(5,2) DEFAULT 0,
    used_balance DECIMAL(5,2) DEFAULT 0,
    remaining_balance DECIMAL(5,2) DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(leave_type_id) ON DELETE CASCADE,
    UNIQUE KEY unique_employee_leave_year (employee_id, leave_type_id, year),
    INDEX idx_employee_year (employee_id, year),
    INDEX idx_leave_type_year (leave_type_id, year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- ALTERNATIVE: If you also need monthly tracking
-- ============================================

-- Create Leave Balance Monthly Table (Optional)
CREATE TABLE IF NOT EXISTS leave_balance_monthly (
    balance_monthly_id INT AUTO_INCREMENT PRIMARY KEY,
    leave_balance_id INT NOT NULL,
    month INT NOT NULL,
    monthly_balance DECIMAL(5,2) DEFAULT 0,
    monthly_used DECIMAL(5,2) DEFAULT 0,
    monthly_remaining DECIMAL(5,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (leave_balance_id) REFERENCES leave_balances(leave_balance_id) ON DELETE CASCADE,
    INDEX idx_month (month)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- SAMPLE INSERT DATA
-- ============================================

-- Example 1: Insert leave balance for one employee, all leave types for 2026
INSERT INTO leave_balances (employee_id, leave_type_id, year, opening_balance, used_balance, remaining_balance, notes)
VALUES 
    (1, 1, 2026, 15.00, 0.00, 15.00, 'Vacation Leave - Annual Allowance'),
    (1, 2, 2026, 10.00, 0.00, 10.00, 'Sick Leave - Annual Allowance'),
    (1, 3, 2026, 5.00, 0.00, 5.00, 'Maternity Leave - Annual Allowance'),
    (1, 4, 2026, 3.00, 0.00, 3.00, 'Emergency Leave - Annual Allowance');

-- Example 2: Insert for multiple employees
-- First get all employees and leave types, then insert
INSERT INTO leave_balances (employee_id, leave_type_id, year, opening_balance, used_balance, remaining_balance)
SELECT 
    e.employee_id,
    lt.leave_type_id,
    2026,
    CASE 
        WHEN lt.leave_type_name = 'Vacation Leave' THEN 15.00
        WHEN lt.leave_type_name = 'Sick Leave' THEN 10.00
        WHEN lt.leave_type_name = 'Maternity Leave' THEN 5.00
        WHEN lt.leave_type_name = 'Emergency Leave' THEN 3.00
        ELSE 0.00
    END as opening_balance,
    0.00,
    CASE 
        WHEN lt.leave_type_name = 'Vacation Leave' THEN 15.00
        WHEN lt.leave_type_name = 'Sick Leave' THEN 10.00
        WHEN lt.leave_type_name = 'Maternity Leave' THEN 5.00
        WHEN lt.leave_type_name = 'Emergency Leave' THEN 3.00
        ELSE 0.00
    END as remaining_balance
FROM employees e
CROSS JOIN leave_types lt
WHERE lt.is_active = 1
AND NOT EXISTS (
    SELECT 1 FROM leave_balances lb
    WHERE lb.employee_id = e.employee_id
    AND lb.leave_type_id = lt.leave_type_id
    AND lb.year = 2026
);

-- ============================================
-- QUERIES TO USE WITH THE TABLE
-- ============================================

-- Get leave balance for specific employee
SELECT 
    lb.leave_balance_id,
    e.employee_id,
    e.employee_name,
    lt.leave_type_name,
    lb.year,
    lb.opening_balance,
    lb.used_balance,
    lb.remaining_balance
FROM leave_balances lb
JOIN employees e ON lb.employee_id = e.employee_id
JOIN leave_types lt ON lb.leave_type_id = lt.leave_type_id
WHERE e.employee_id = ? AND lb.year = YEAR(NOW());

-- Get all leave balances for current year
SELECT 
    e.employee_id,
    e.employee_name,
    lt.leave_type_name,
    lb.opening_balance,
    lb.used_balance,
    lb.remaining_balance,
    CONCAT(ROUND((lb.used_balance / lb.opening_balance * 100), 2), '%') as usage_percentage
FROM leave_balances lb
JOIN employees e ON lb.employee_id = e.employee_id
JOIN leave_types lt ON lb.leave_type_id = lt.leave_type_id
WHERE lb.year = YEAR(NOW())
ORDER BY e.employee_name, lt.leave_type_name;

-- Update used balance when leave is approved
UPDATE leave_balances
SET 
    used_balance = used_balance + ?,
    remaining_balance = opening_balance - (used_balance + ?),
    updated_at = NOW()
WHERE employee_id = ? AND leave_type_id = ? AND year = YEAR(NOW());

-- Get employees with low leave balance (less than 2 days remaining)
SELECT 
    e.employee_id,
    e.employee_name,
    lt.leave_type_name,
    lb.remaining_balance
FROM leave_balances lb
JOIN employees e ON lb.employee_id = e.employee_id
JOIN leave_types lt ON lb.leave_type_id = lt.leave_type_id
WHERE lb.year = YEAR(NOW())
AND lb.remaining_balance < 2
AND lb.remaining_balance > 0;

-- Get leave balance summary by department
SELECT 
    d.department_name,
    lt.leave_type_name,
    COUNT(DISTINCT e.employee_id) as employee_count,
    ROUND(AVG(lb.remaining_balance), 2) as avg_remaining,
    ROUND(SUM(lb.remaining_balance), 2) as total_remaining
FROM leave_balances lb
JOIN employees e ON lb.employee_id = e.employee_id
JOIN departments d ON e.department_id = d.department_id
JOIN leave_types lt ON lb.leave_type_id = lt.leave_type_id
WHERE lb.year = YEAR(NOW())
GROUP BY d.department_id, lt.leave_type_id
ORDER BY d.department_name, lt.leave_type_name;

-- ============================================
-- IMPORTANT NOTES:
-- ============================================
-- 1. Make sure you have employees and leave_types tables already
-- 2. Adjust opening_balance values based on your company policy
-- 3. The table uses CASE statement in insert example for common leave types
-- 4. Modify leave type names in the CASE statement to match your database
-- 5. Foreign keys require both tables to exist first
-- 6. Run the bulk insert for all employees at the start of each year
-- 7. Update remaining_balance automatically when leave is approved

