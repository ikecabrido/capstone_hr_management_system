-- REMAINING MIGRATION STEPS (Already completed: FK constraint)

-- ============================================
-- STEP 3: Add performance indexes
-- ============================================
ALTER TABLE `ta_attendance`
ADD INDEX `idx_employee_date_status` (`employee_id`, `attendance_date`, `status`);

ALTER TABLE `ta_attendance`
ADD INDEX `idx_leave_request` (`leave_request_id`);

-- ============================================
-- STEP 4: Link absence records to leave
-- ============================================
ALTER TABLE `ta_absence_late_records`
ADD COLUMN `leave_request_id` INT NULL;

ALTER TABLE `ta_absence_late_records`
ADD CONSTRAINT `fk_absence_leave_request` 
FOREIGN KEY (`leave_request_id`) REFERENCES `ta_leave_requests`(`id`)
ON DELETE SET NULL ON UPDATE CASCADE;

-- ============================================
-- STEP 5: Add balance_deducted column
-- ============================================
ALTER TABLE `ta_leave_requests`
ADD COLUMN `balance_deducted` BOOLEAN DEFAULT 0 AFTER `rejected_by`;

-- ============================================
-- STEP 6: Create helper table for daily leave records
-- ============================================
CREATE TABLE IF NOT EXISTS `ta_leave_daily_records` (
    `daily_record_id` INT PRIMARY KEY AUTO_INCREMENT,
    `leave_request_id` INT NOT NULL,
    `employee_id` INT NOT NULL,
    `leave_date` DATE NOT NULL,
    `leave_type_id` INT NOT NULL,
    `is_holiday` BOOLEAN DEFAULT 0,
    `balance_deducted` BOOLEAN DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`leave_request_id`) REFERENCES `ta_leave_requests`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`employee_id`) REFERENCES `employees`(`employee_id`) ON DELETE CASCADE,
    FOREIGN KEY (`leave_type_id`) REFERENCES `ta_leave_types`(`leave_type_id`) ON DELETE RESTRICT,
    
    UNIQUE KEY `unique_daily_leave` (`leave_request_id`, `leave_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
