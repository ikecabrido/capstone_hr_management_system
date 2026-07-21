-- ============================================================
-- LEAVE REQUEST SYSTEM - DATABASE MIGRATIONS
-- ============================================================

-- 1. Add missing columns to ta_leave_requests table
-- Run this SQL to add document storage and reason fields

ALTER TABLE `ta_leave_requests` 
ADD COLUMN `documents` LONGTEXT COMMENT 'JSON array of uploaded document file paths' AFTER `supporting_document`,
ADD COLUMN `reason` TEXT COMMENT 'Detailed reason for leave request' AFTER `details`,
ADD COLUMN `document_uploaded_at` TIMESTAMP NULL DEFAULT NULL AFTER `documents`;

-- ============================================================
-- SAMPLE DATA - Create leave balances for employees
-- ============================================================

-- For Employee 1 (John Doe) - Year 2026
INSERT INTO `ta_leave_balances` 
(`employee_id`, `leave_type_id`, `year`, `opening_balance`, `used_balance`, `remaining_balance`)
VALUES
(1, 1, 2026, 15.00, 0.00, 15.00),  -- Vacation Leave
(1, 2, 2026, 10.00, 0.00, 10.00),  -- Sick Leave
(1, 3, 2026, 5.00, 0.00, 5.00);    -- Maternity Leave

-- For Employee 4 (Sarah Williams) - Year 2026
INSERT INTO `ta_leave_balances` 
(`employee_id`, `leave_type_id`, `year`, `opening_balance`, `used_balance`, `remaining_balance`)
VALUES
(4, 1, 2026, 15.00, 0.00, 15.00),  -- Vacation Leave
(4, 2, 2026, 10.00, 0.00, 10.00),  -- Sick Leave
(4, 4, 2026, 3.00, 0.00, 3.00);    -- Emergency Leave

-- ============================================================
-- VERIFICATION QUERIES
-- ============================================================

-- Check ta_leave_requests structure
DESC ta_leave_requests;

-- Check ta_leave_balances data
SELECT * FROM ta_leave_balances WHERE year = 2026;

-- Check pending leave requests
SELECT tlr.id, tlr.employee_id, tlr.leave_type_id, tlr.start_date, tlr.end_date, tlr.status
FROM ta_leave_requests tlr
WHERE tlr.status = 'Pending';

-- Check lc_leave_requests (for Legal & Compliance)
SELECT * FROM lc_leave_requests;

-- Check lc_leave_documents
SELECT * FROM lc_leave_documents;

-- ============================================================
-- CLEANUP (if needed)
-- ============================================================

-- Clear test leave requests (caution!)
-- DELETE FROM ta_leave_requests WHERE employee_id IN (1,2,3,4);

-- Clear test leave balances (caution!)
-- DELETE FROM ta_leave_balances WHERE employee_id IN (1,2,3,4) AND year = 2026;
