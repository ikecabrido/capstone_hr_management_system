-- ============================================================
-- ADD ADDITIONAL LEAVE TYPES FOR ALL ACTIVE EMPLOYEES
-- ============================================================
-- Philippine statutory and company leaves
-- ============================================================

-- Paternity Leave (7 days) - for male employees only
INSERT INTO ta_leave_balances 
(employee_id, leave_type_id, year, opening_balance, used_balance, remaining_balance, notes)
SELECT DISTINCT e.employee_id, 5, 2026, 7.00, 0.00, 7.00, 'Paternity Leave - RA 8187'
FROM employees e
WHERE e.employment_status = 'Active' AND e.sex = 'Male'
ON DUPLICATE KEY UPDATE opening_balance = 7.00, remaining_balance = 7.00;

-- Birthday Leave (1 day) - for all employees
INSERT INTO ta_leave_balances 
(employee_id, leave_type_id, year, opening_balance, used_balance, remaining_balance, notes)
SELECT DISTINCT e.employee_id, 6, 2026, 1.00, 0.00, 1.00, 'Birthday Leave - RA 11976'
FROM employees e
WHERE e.employment_status = 'Active'
ON DUPLICATE KEY UPDATE opening_balance = 1.00, remaining_balance = 1.00;

-- Bereavement Leave (5 days) - for all employees
INSERT INTO ta_leave_balances 
(employee_id, leave_type_id, year, opening_balance, used_balance, remaining_balance, notes)
SELECT DISTINCT e.employee_id, 7, 2026, 5.00, 0.00, 5.00, 'Bereavement Leave'
FROM employees e
WHERE e.employment_status = 'Active'
ON DUPLICATE KEY UPDATE opening_balance = 5.00, remaining_balance = 5.00;

-- ============================================================
-- FINAL VERIFICATION - COMPLETE LEAVE BALANCE REPORT
-- ============================================================

SELECT 
    e.employee_no,
    e.full_name,
    e.sex,
    lt.leave_type_name,
    lb.opening_balance,
    lb.used_balance,
    lb.remaining_balance,
    lb.notes
FROM ta_leave_balances lb
JOIN employees e ON lb.employee_id = e.employee_id
JOIN ta_leave_types lt ON lb.leave_type_id = lt.leave_type_id
WHERE lb.year = 2026
ORDER BY e.full_name, lt.leave_type_id;

-- ============================================================
-- SUMMARY STATISTICS
-- ============================================================

SELECT 
    lt.leave_type_name,
    lt.days_per_year as statutory_days,
    COUNT(DISTINCT lb.employee_id) as employees_allocated,
    SUM(lb.opening_balance) as total_days_allocated,
    ROUND(AVG(lb.opening_balance), 2) as avg_per_employee
FROM ta_leave_balances lb
JOIN ta_leave_types lt ON lb.leave_type_id = lt.leave_type_id
WHERE lb.year = 2026
GROUP BY lt.leave_type_id, lt.leave_type_name, lt.days_per_year
ORDER BY lt.leave_type_id;
