-- ============================================================
-- AGE_GROUP CALCULATION - EXPLANATION & ALTERNATIVES
-- ============================================================
-- 
-- PROBLEM: GENERATED ALWAYS columns don't support CURDATE()
-- 
-- SOLUTION USED: TRIGGERS
-- - Trigger on INSERT: Auto-calculates age_group from date_of_birth
-- - Trigger on UPDATE: Recalculates age_group if date_of_birth changes
-- 
-- ============================================================

-- ============================================================
-- ALTERNATIVE 1: IF YOU PREFER STORED PROCEDURES
-- ============================================================
-- Instead of triggers, you can call a procedure to update all age_groups

DELIMITER $$

CREATE PROCEDURE `sp_update_all_employee_age_groups`()
BEGIN
  UPDATE `employees`
  SET age_group = CASE 
    WHEN date_of_birth IS NULL THEN NULL
    WHEN YEAR(CURDATE()) - YEAR(date_of_birth) < 25 THEN '18-24'
    WHEN YEAR(CURDATE()) - YEAR(date_of_birth) < 35 THEN '25-34'
    WHEN YEAR(CURDATE()) - YEAR(date_of_birth) < 45 THEN '35-44'
    WHEN YEAR(CURDATE()) - YEAR(date_of_birth) < 55 THEN '45-54'
    ELSE '55+'
  END
  WHERE date_of_birth IS NOT NULL;
END$$

DELIMITER ;

-- Run once when needed:
-- CALL sp_update_all_employee_age_groups();

-- ============================================================
-- ALTERNATIVE 2: CREATE A VIEW FOR QUERIES
-- ============================================================

CREATE VIEW `vw_employees_with_age_group` AS
SELECT 
  e.*,
  CASE 
    WHEN e.date_of_birth IS NULL THEN NULL
    WHEN YEAR(CURDATE()) - YEAR(e.date_of_birth) < 25 THEN '18-24'
    WHEN YEAR(CURDATE()) - YEAR(e.date_of_birth) < 35 THEN '25-34'
    WHEN YEAR(CURDATE()) - YEAR(e.date_of_birth) < 45 THEN '35-44'
    WHEN YEAR(CURDATE()) - YEAR(e.date_of_birth) < 55 THEN '45-54'
    ELSE '55+'
  END AS calculated_age_group
FROM `employees` e;

-- Use this for queries that need age_group:
-- SELECT * FROM vw_employees_with_age_group WHERE calculated_age_group = '25-34';

-- ============================================================
-- WHICH APPROACH TO USE?
-- ============================================================
/*
TRIGGERS (RECOMMENDED - Used in main migration):
✅ Pros:
  - Automatic: age_group always current without manual updates
  - Transparent: Works like a regular column in SELECT queries
  - No performance impact on SELECT (only on INSERT/UPDATE)
  - Data always available for analytics

❌ Cons:
  - Adds slight overhead to INSERT/UPDATE operations
  - More complex to debug if issues arise

STORED PROCEDURE:
✅ Pros:
  - Simple, explicit control
  - Lightweight on database
  - Easy to schedule with cron/event

❌ Cons:
  - Must call manually or schedule
  - age_group might be stale between updates
  - Requires separate update process

VIEWS:
✅ Pros:
  - Decouples stored data from calculated data
  - Always up-to-date
  - Clear separation of concerns

❌ Cons:
  - Performance hit if calculating for every SELECT
  - Need to update queries to use view instead of table
  - Can't use age_group in WHERE clauses efficiently

DECISION: Use TRIGGERS for automatic, real-time updates
*/

-- ============================================================
-- VERIFY TRIGGERS ARE CREATED
-- ============================================================

-- View all triggers on employees table:
-- SELECT * FROM INFORMATION_SCHEMA.TRIGGERS WHERE TRIGGER_SCHEMA = 'hr_management' AND EVENT_OBJECT_TABLE = 'employees';

-- Disable a trigger if needed:
-- ALTER TABLE `employees` DISABLE KEYS;
-- DROP TRIGGER `trig_emp_update_age_group_insert`;
-- DROP TRIGGER `trig_emp_update_age_group_update`;
-- ALTER TABLE `employees` ENABLE KEYS;

-- ============================================================
-- TEST THE TRIGGER
-- ============================================================

-- After migration, test with:
INSERT INTO `employees` (
  employee_id,
  full_name,
  email,
  contact_number,
  date_hired,
  date_of_birth,
  gender,
  nationality
) VALUES (
  'TEST001',
  'Test Employee',
  'test@example.com',
  '555-0123',
  '2024-01-01',
  '1995-05-15',  -- Will automatically calculate to '25-34'
  'Male',
  'Philippines'
);

-- Then verify:
SELECT employee_id, full_name, date_of_birth, age_group 
FROM employees 
WHERE employee_id = 'TEST001';

-- You should see age_group populated as '25-34' automatically

-- ============================================================
-- POPULATE AGE_GROUP FOR EXISTING EMPLOYEES
-- ============================================================

-- After creating the columns, populate age_group for existing records:
UPDATE `employees`
SET age_group = CASE 
  WHEN date_of_birth IS NULL THEN NULL
  WHEN YEAR(CURDATE()) - YEAR(date_of_birth) < 25 THEN '18-24'
  WHEN YEAR(CURDATE()) - YEAR(date_of_birth) < 35 THEN '25-34'
  WHEN YEAR(CURDATE()) - YEAR(date_of_birth) < 45 THEN '35-44'
  WHEN YEAR(CURDATE()) - YEAR(date_of_birth) < 55 THEN '45-54'
  ELSE '55+'
END
WHERE date_of_birth IS NOT NULL AND age_group IS NULL;

-- Verify the update:
SELECT age_group, COUNT(*) as count 
FROM employees 
GROUP BY age_group;

-- ============================================================
-- MONITORING & MAINTENANCE
-- ============================================================

-- Check if age_groups are calculated correctly:
SELECT 
  employee_id,
  full_name,
  date_of_birth,
  age_group,
  YEAR(CURDATE()) - YEAR(date_of_birth) as calculated_age
FROM employees
WHERE date_of_birth IS NOT NULL
ORDER BY date_of_birth DESC;

-- Find employees with NULL age_group (data quality check):
SELECT * FROM employees 
WHERE date_of_birth IS NOT NULL AND age_group IS NULL;

-- Find employees with NULL date_of_birth:
SELECT * FROM employees 
WHERE date_of_birth IS NULL;

-- ============================================================
-- ANNUAL MAINTENANCE
-- ============================================================

-- The age_group values are automatically updated daily via triggers
-- No manual maintenance needed
-- The triggers run on every INSERT and UPDATE operation

-- If you ever need to refresh all age_groups (e.g., after data restoration):
-- CALL sp_update_all_employee_age_groups();

-- ============================================================
-- END OF AGE_GROUP SETUP
-- ============================================================
