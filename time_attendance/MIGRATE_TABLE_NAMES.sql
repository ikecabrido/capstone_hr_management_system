    -- ============================================
-- Time & Attendance Module - Table Rename Migration
-- Rename all time_attendance tables with 'ta_' prefix
-- ============================================

-- Core Attendance & Time Tracking Tables
RENAME TABLE attendance TO ta_attendance;
RENAME TABLE attendance_tokens TO ta_attendance_tokens;
RENAME TABLE employee_shifts TO ta_employee_shifts;
RENAME TABLE shifts TO ta_shifts;
RENAME TABLE flexible_schedules TO ta_flexible_schedules;

-- Leave Management Tables
RENAME TABLE leave_balances TO ta_leave_balances;
RENAME TABLE leave_types TO ta_leave_types;
RENAME TABLE leave_requests TO ta_leave_requests;

-- Schedule & Calendar Tables
RENAME TABLE holidays TO ta_holidays;

-- ============================================
-- Verify the changes
-- ============================================
SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME LIKE 'ta_%'
ORDER BY TABLE_NAME;

-- Expected tables (13 total):
-- ta_attendance
-- ta_attendance_tokens
-- ta_custom_shift_times
-- ta_custom_shifts
-- ta_department_heads
-- ta_employee_shifts
-- ta_flexible_schedules
-- ta_holidays
-- ta_leave_balances
-- ta_leave_requests
-- ta_leave_types
-- ta_notifications
-- ta_shifts
