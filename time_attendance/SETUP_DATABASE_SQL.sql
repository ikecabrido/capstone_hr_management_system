-- Saturday Exclusion Feature - Database Setup SQL
-- Run this if you prefer manual database setup instead of the PHP migration script

-- Create the ta_shift_exclusions table
CREATE TABLE IF NOT EXISTS ta_shift_exclusions (
    exclusion_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_shift_id INT NOT NULL,
    exclusion_date DATE NOT NULL,
    reason VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_exclusion (employee_shift_id, exclusion_date),
    FOREIGN KEY (employee_shift_id) REFERENCES ta_employee_shifts(employee_shift_id) ON DELETE CASCADE,
    INDEX idx_exclusion_date (exclusion_date),
    INDEX idx_employee_shift_id (employee_shift_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verify table creation
DESCRIBE ta_shift_exclusions;

-- Verify indices
SHOW KEYS FROM ta_shift_exclusions;

-- Example 1: Insert a Saturday exclusion for employee_shift_id = 1 on 2024-01-06 (a Saturday)
INSERT INTO ta_shift_exclusions (employee_shift_id, exclusion_date, reason)
VALUES (1, '2024-01-06', 'Saturday exclusion');

-- Example 2: Query employees with exclusions on a specific date
SELECT 
    e.full_name,
    s.shift_name,
    se.exclusion_date,
    se.reason
FROM ta_shift_exclusions se
JOIN ta_employee_shifts es ON se.employee_shift_id = es.employee_shift_id
JOIN employees e ON es.employee_id = e.employee_id
JOIN ta_shifts s ON es.shift_id = s.shift_id
WHERE se.exclusion_date = '2024-01-06'
ORDER BY e.full_name;

-- Example 3: Check if an employee has an exclusion for a date
SELECT COUNT(*) as has_exclusion
FROM ta_shift_exclusions se
WHERE se.exclusion_date = '2024-01-06'
AND se.employee_shift_id IN (
    SELECT employee_shift_id FROM ta_employee_shifts 
    WHERE employee_id = 1
    AND is_active = 1
);

-- Example 4: Delete old exclusions (cleanup)
DELETE FROM ta_shift_exclusions 
WHERE exclusion_date < DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
AND reason = 'Saturday exclusion';

-- Example 5: Count total Saturdays excluded for an employee
SELECT 
    e.full_name,
    COUNT(*) as saturday_count
FROM ta_shift_exclusions se
JOIN ta_employee_shifts es ON se.employee_shift_id = es.employee_shift_id
JOIN employees e ON es.employee_id = e.employee_id
WHERE se.reason = 'Saturday exclusion'
GROUP BY e.full_name
ORDER BY saturday_count DESC;

-- Example 6: View all employees and their working dates (excluding Saturday exclusions)
SELECT 
    e.full_name,
    s.shift_name,
    es.effective_from,
    es.effective_to,
    COUNT(se.exclusion_id) as excluded_dates
FROM employees e
JOIN ta_employee_shifts es ON e.employee_id = es.employee_id
JOIN ta_shifts s ON es.shift_id = s.shift_id
LEFT JOIN ta_shift_exclusions se ON se.employee_shift_id = es.employee_shift_id
WHERE es.is_active = 1
GROUP BY e.employee_id, es.employee_shift_id
ORDER BY e.full_name;
