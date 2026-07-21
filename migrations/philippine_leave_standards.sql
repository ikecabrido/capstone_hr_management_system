-- ============================================================
-- PHILIPPINE LEAVE BALANCE STANDARDS
-- ============================================================
-- Based on Philippine Labor Code and standard company practices
--
-- Standard Annual Leave Allocations for Philippine Companies:
-- 1. VACATION LEAVE: 10-15 days per year (varies by company and tenure)
-- 2. SICK LEAVE: 10 days per year
-- 3. MATERNITY LEAVE: 105 days (60 paid + 45 unpaid under RA 11210)
-- 4. PATERNITY LEAVE: 7 days (RA 8187)
-- 5. EMERGENCY LEAVE: 3-5 days (company policy)
-- 6. BEREAVEMENT LEAVE: 3-5 days (company policy)
-- 7. BIRTHDAY LEAVE: 1 day (RA 11976)
-- ============================================================

-- Clear existing balances for year 2026
DELETE FROM ta_leave_balances WHERE year = 2026;

-- ============================================================
-- STANDARD ALLOCATION FOR ALL EMPLOYEES - YEAR 2026
-- ============================================================
-- Using typical Filipino company standards:
-- - Vacation Leave: 15 days (generous allocation)
-- - Sick Leave: 10 days
-- - Maternity Leave: 105 days (statutory)
-- - Emergency Leave: 5 days
-- ============================================================

INSERT INTO `ta_leave_balances` 
(`employee_id`, `leave_type_id`, `year`, `opening_balance`, `used_balance`, `remaining_balance`, `notes`)
SELECT DISTINCT e.employee_id, 1, 2026, 15.00, 0.00, 15.00, 'Vacation Leave - Annual'
FROM employees e
WHERE e.employment_status = 'Active'
ON DUPLICATE KEY UPDATE opening_balance = 15.00, remaining_balance = 15.00;

INSERT INTO `ta_leave_balances` 
(`employee_id`, `leave_type_id`, `year`, `opening_balance`, `used_balance`, `remaining_balance`, `notes`)
SELECT DISTINCT e.employee_id, 2, 2026, 10.00, 0.00, 10.00, 'Sick Leave - Annual'
FROM employees e
WHERE e.employment_status = 'Active'
ON DUPLICATE KEY UPDATE opening_balance = 10.00, remaining_balance = 10.00;

INSERT INTO `ta_leave_balances` 
(`employee_id`, `leave_type_id`, `year`, `opening_balance`, `used_balance`, `remaining_balance`, `notes`)
SELECT DISTINCT e.employee_id, 3, 2026, 105.00, 0.00, 105.00, 'Maternity Leave - Statutory (RA 11210)'
FROM employees e
WHERE e.employment_status = 'Active' AND e.sex = 'Female'
ON DUPLICATE KEY UPDATE opening_balance = 105.00, remaining_balance = 105.00;

INSERT INTO `ta_leave_balances` 
(`employee_id`, `leave_type_id`, `year`, `opening_balance`, `used_balance`, `remaining_balance`, `notes`)
SELECT DISTINCT e.employee_id, 4, 2026, 5.00, 0.00, 5.00, 'Emergency Leave - Company Policy'
FROM employees e
WHERE e.employment_status = 'Active'
ON DUPLICATE KEY UPDATE opening_balance = 5.00, remaining_balance = 5.00;

-- ============================================================
-- ADD MORE LEAVE TYPES IF NEEDED
-- ============================================================
-- If you want to add more leave types, insert them here:

-- Paternity Leave (7 days for male employees) - RA 8187
INSERT INTO ta_leave_types (leave_type_name, description, days_per_year, is_deductible, requires_approval)
VALUES ('Paternity Leave', 'Paternity Leave - RA 8187 (7 days)', 7, 1, 1)
ON DUPLICATE KEY UPDATE days_per_year = 7;

-- Birthday Leave (1 day) - RA 11976
INSERT INTO ta_leave_types (leave_type_name, description, days_per_year, is_deductible, requires_approval)
VALUES ('Birthday Leave', 'Birthday Leave - RA 11976 (1 day)', 1, 1, 1)
ON DUPLICATE KEY UPDATE days_per_year = 1;

-- Bereavement Leave (5 days)
INSERT INTO ta_leave_types (leave_type_name, description, days_per_year, is_deductible, requires_approval)
VALUES ('Bereavement Leave', 'Death of Immediate Family Member (5 days)', 5, 1, 1)
ON DUPLICATE KEY UPDATE days_per_year = 5;

-- ============================================================
-- VERIFICATION QUERIES
-- ============================================================

-- View all leave balances for 2026
SELECT 
    lb.leave_balance_id,
    e.employee_no,
    e.full_name,
    lt.leave_type_name,
    lb.opening_balance,
    lb.used_balance,
    lb.remaining_balance,
    lb.notes
FROM ta_leave_balances lb
JOIN employees e ON lb.employee_id = e.employee_id
JOIN ta_leave_types lt ON lb.leave_type_id = lt.leave_type_id
WHERE lb.year = 2026
ORDER BY e.full_name, lt.leave_type_name;

-- Summary by leave type
SELECT 
    lt.leave_type_name,
    COUNT(DISTINCT lb.employee_id) as employee_count,
    SUM(lb.opening_balance) as total_allocated,
    SUM(lb.used_balance) as total_used,
    SUM(lb.remaining_balance) as total_remaining
FROM ta_leave_balances lb
JOIN ta_leave_types lt ON lb.leave_type_id = lt.leave_type_id
WHERE lb.year = 2026
GROUP BY lt.leave_type_name;
