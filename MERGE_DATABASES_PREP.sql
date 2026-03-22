-- Merge time_and_attendance database into hr_management
-- This script imports all tables from time_and_attendance into hr_management

-- Step 1: Check which tables already exist in hr_management
-- (Some may already exist, like users, so we'll need to be careful)

-- Step 2: Drop time_and_attendance tables if they exist in hr_management with different data
-- (Skip this for now, we'll handle conflicts manually)

-- Step 3: Import all tables from time_and_attendance
-- The actual table definitions and data will be imported via the exported SQL file

-- NOTE: This script assumes the exported time_and_attendance.sql has been modified to:
-- 1. Use `hr_management` database instead of `time_and_attendance`
-- 2. Handle conflicts with existing tables (users, employees, etc.)

-- IMPORTANT TABLES TO HANDLE:
-- - users: May conflict with existing hr_management.users
-- - employees: May conflict with existing employees
-- - audit_logs: Already exists in time_and_attendance, needs to be merged
-- - department_heads: Check for conflicts
-- - leave_balances: From hr_management, keep it
-- - leave_requests: From hr_management, keep it
-- - leave_types: From hr_management, keep it

-- ACTION: Manually run the merged SQL file after this preparation
SELECT 'Database consolidation ready. See steps below:' AS instructions;

-- STEPS:
-- 1. Backup both databases first
-- 2. Run the import script with proper handling of table conflicts
-- 3. Update all PHP files to use hr_management database
-- 4. Test all functionality
-- 5. Drop the time_and_attendance database if everything works
