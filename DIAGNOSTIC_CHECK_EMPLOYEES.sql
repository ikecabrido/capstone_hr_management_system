-- ============================================================
-- DIAGNOSTIC SCRIPT
-- Check current employees table structure
-- Date: March 22, 2026
-- ============================================================

-- ============================================================
-- 1. VIEW CURRENT TABLE STRUCTURE
-- ============================================================
DESCRIBE `employees`;

-- ============================================================
-- 2. LIST ALL COLUMNS WITH DATA TYPE
-- ============================================================
SELECT 
  COLUMN_NAME,
  COLUMN_TYPE,
  IS_NULLABLE,
  COLUMN_KEY,
  COLUMN_DEFAULT,
  EXTRA,
  COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'hr_management' 
AND TABLE_NAME = 'employees'
ORDER BY ORDINAL_POSITION;

-- ============================================================
-- 3. CHECK FOR FOREIGN KEYS
-- ============================================================
SELECT 
  CONSTRAINT_NAME,
  COLUMN_NAME,
  REFERENCED_TABLE_NAME,
  REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'hr_management'
AND TABLE_NAME = 'employees'
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- ============================================================
-- 4. CHECK FOR INDEXES
-- ============================================================
SELECT 
  INDEX_NAME,
  COLUMN_NAME,
  SEQ_IN_INDEX,
  INDEX_TYPE
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'hr_management'
AND TABLE_NAME = 'employees'
ORDER BY INDEX_NAME, SEQ_IN_INDEX;

-- ============================================================
-- 5. CHECK FOR TRIGGERS
-- ============================================================
SELECT 
  TRIGGER_NAME,
  EVENT_MANIPULATION,
  EVENT_OBJECT_TABLE,
  ACTION_STATEMENT
FROM INFORMATION_SCHEMA.TRIGGERS
WHERE TRIGGER_SCHEMA = 'hr_management'
AND EVENT_OBJECT_TABLE = 'employees';

-- ============================================================
-- 6. SHOW TABLE ROW COUNT
-- ============================================================
SELECT 
  TABLE_NAME,
  TABLE_ROWS,
  DATA_LENGTH,
  INDEX_LENGTH,
  TABLE_COLLATION
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'hr_management'
AND TABLE_NAME = 'employees';

-- ============================================================
-- 7. IDENTIFY MISSING COLUMNS (ones we want to add)
-- ============================================================
SELECT 
  COLUMN_NAME,
  'NOT FOUND' as STATUS
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'hr_management' 
AND TABLE_NAME = 'employees'
UNION ALL
SELECT 'department_id', IF(COUNT(*) > 0, 'EXISTS', 'MISSING') 
FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='employees' AND COLUMN_NAME='department_id'
UNION ALL
SELECT 'position_id', IF(COUNT(*) > 0, 'EXISTS', 'MISSING') 
FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='employees' AND COLUMN_NAME='position_id'
UNION ALL
SELECT 'employment_type_id', IF(COUNT(*) > 0, 'EXISTS', 'MISSING') 
FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='employees' AND COLUMN_NAME='employment_type_id'
UNION ALL
SELECT 'location_id', IF(COUNT(*) > 0, 'EXISTS', 'MISSING') 
FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='employees' AND COLUMN_NAME='location_id'
UNION ALL
SELECT 'salary_grade_id', IF(COUNT(*) > 0, 'EXISTS', 'MISSING') 
FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='employees' AND COLUMN_NAME='salary_grade_id'
UNION ALL
SELECT 'gender', IF(COUNT(*) > 0, 'EXISTS', 'MISSING') 
FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='employees' AND COLUMN_NAME='gender'
UNION ALL
SELECT 'date_of_birth', IF(COUNT(*) > 0, 'EXISTS', 'MISSING') 
FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='employees' AND COLUMN_NAME='date_of_birth'
UNION ALL
SELECT 'age_group', IF(COUNT(*) > 0, 'EXISTS', 'MISSING') 
FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='employees' AND COLUMN_NAME='age_group'
UNION ALL
SELECT 'manager_id', IF(COUNT(*) > 0, 'EXISTS', 'MISSING') 
FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='employees' AND COLUMN_NAME='manager_id'
UNION ALL
SELECT 'base_salary', IF(COUNT(*) > 0, 'EXISTS', 'MISSING') 
FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='employees' AND COLUMN_NAME='base_salary'
UNION ALL
SELECT 'currency', IF(COUNT(*) > 0, 'EXISTS', 'MISSING') 
FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='employees' AND COLUMN_NAME='currency'
UNION ALL
SELECT 'marital_status', IF(COUNT(*) > 0, 'EXISTS', 'MISSING') 
FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='employees' AND COLUMN_NAME='marital_status'
UNION ALL
SELECT 'nationality', IF(COUNT(*) > 0, 'EXISTS', 'MISSING') 
FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='employees' AND COLUMN_NAME='nationality'
UNION ALL
SELECT 'employee_status', IF(COUNT(*) > 0, 'EXISTS', 'MISSING') 
FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='employees' AND COLUMN_NAME='employee_status';

-- ============================================================
-- 8. SAMPLE DATA FROM EMPLOYEES TABLE
-- ============================================================
SELECT * FROM `employees` LIMIT 5;

-- ============================================================
-- END OF DIAGNOSTICS
-- ============================================================
