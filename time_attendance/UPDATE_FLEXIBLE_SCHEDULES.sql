-- ============================================================================
-- UPDATE TA_FLEXIBLE_SCHEDULES TO USE INT EMPLOYEE_ID
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Update ta_flexible_schedules to use INT
ALTER TABLE ta_flexible_schedules MODIFY COLUMN employee_id INT NOT NULL;

-- Drop and recreate index
ALTER TABLE ta_flexible_schedules DROP INDEX IF EXISTS idx_employee;
ALTER TABLE ta_flexible_schedules ADD INDEX idx_employee (employee_id);

-- Drop old foreign key if exists
ALTER TABLE ta_flexible_schedules DROP FOREIGN KEY IF EXISTS fk_flex_schedules_employee;
ALTER TABLE ta_flexible_schedules DROP FOREIGN KEY IF EXISTS ta_flexible_schedules_ibfk_1;

-- Add proper foreign key constraint
ALTER TABLE ta_flexible_schedules ADD CONSTRAINT fk_flex_schedules_employee 
  FOREIGN KEY (employee_id) REFERENCES employees(employee_id) 
  ON DELETE CASCADE ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;

-- Verify
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'ta_flexible_schedules' AND COLUMN_NAME = 'employee_id'
  AND TABLE_SCHEMA = 'hr_management';

SELECT 'Migration Complete: ta_flexible_schedules now uses INT employee_id' as status;
