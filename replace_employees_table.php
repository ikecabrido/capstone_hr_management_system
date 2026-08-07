<?php
require_once __DIR__ . '/auth/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Starting employees table replacement...\n";

    // Step 1: Disable foreign key checks
    echo "Step 1: Disabling foreign key checks...\n";
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Step 2: Backup current data
    echo "Step 2: Backing up current data...\n";
    $backupStmt = $db->query("SELECT * FROM employees");
    $currentData = $backupStmt->fetchAll(PDO::FETCH_ASSOC);

    // Step 2: Drop existing table
    echo "Step 2: Dropping existing table...\n";
    $db->exec("DROP TABLE employees");

    // Step 3: Create new table structure
    echo "Step 3: Creating new table structure...\n";
    $createTableSQL = "
    CREATE TABLE `employees` (
      `employee_id` int(11) NOT NULL,
      `employee_no` varchar(50) NOT NULL,
      `user_id` int(11) DEFAULT NULL,
      `full_name` varchar(255) NOT NULL,
      `address` text DEFAULT NULL,
      `contact_number` varchar(20) DEFAULT NULL,
      `email` varchar(255) DEFAULT NULL,
      `department` varchar(100) DEFAULT NULL,
      `position` varchar(100) DEFAULT NULL,
      `date_hired` date DEFAULT NULL,
      `employment_status` varchar(50) DEFAULT 'Active',
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      `birthdate` date DEFAULT NULL,
      `sex` varchar(10) DEFAULT NULL,
      `teacher_qualification` varchar(255) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    $db->exec($createTableSQL);

    // Step 4: Add primary key and auto increment
    echo "Step 4: Adding indexes and constraints...\n";
    $db->exec("ALTER TABLE `employees` ADD PRIMARY KEY (`employee_id`);");
    $db->exec("ALTER TABLE `employees` MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;");

    // Step 5: Insert new data from SQL file
    echo "Step 5: Inserting new data...\n";
    $insertDataSQL = "
    INSERT INTO `employees` (`employee_id`, `employee_no`, `user_id`, `full_name`, `address`, `contact_number`, `email`, `department`, `position`, `date_hired`, `employment_status`, `created_at`, `updated_at`, `birthdate`, `sex`, `teacher_qualification`) VALUES
    (1, '', 4, 'John Doe', '123 Main St', '123-456-7890', 'john.doe@example.com', 'IT', 'Software Engineer', '2023-01-01', 'Active', '2026-03-22 07:51:30', '2026-03-31 05:05:26', NULL, NULL, NULL),
    (2, '', 30, 'Jane Smith', '456 Oak Ave', '098-765-4321', 'jane.smith@example.com', 'HR', 'HR Manager', '2023-02-15', 'Active', '2026-03-22 07:51:30', '2026-03-27 13:13:07', NULL, NULL, NULL),
    (3, '', 31, 'Mike Johnson', '789 Pine Rd', '555-123-4567', 'mike.johnson@example.com', 'Finance', 'Accountant', '2023-03-10', 'Active', '2026-03-22 07:51:30', '2026-03-27 13:13:07', NULL, NULL, NULL),
    (4, '', 32, 'Sarah Williams', '321 Elm St', '555-789-0123', 'sarah.williams@example.com', 'Operations', 'Operations Manager', '2023-04-01', 'Active', '2026-03-27 11:25:55', '2026-03-27 13:13:07', NULL, NULL, NULL),
    (5, '', 33, 'David Brown', '654 Maple Dr', '555-456-7890', 'david.brown@example.com', 'IT', 'Junior Developer', '2023-05-15', 'Active', '2026-03-27 11:25:55', '2026-03-27 13:13:07', NULL, NULL, NULL),
    (6, '', 34, 'Emily Davis', '987 Cedar Ln', '555-321-6543', 'emily.davis@example.com', 'HR', 'HR Specialist', '2023-06-01', 'Active', '2026-03-27 11:25:55', '2026-03-27 13:13:07', NULL, NULL, NULL),
    (7, '', 35, 'Robert Wilson', '147 Oak St', '555-654-3210', 'robert.wilson@example.com', 'Finance', 'Financial Analyst', '2023-07-10', 'Active', '2026-03-27 11:25:55', '2026-03-27 13:13:07', NULL, NULL, NULL),
    (8, '', 36, 'Jessica Martinez', '258 Pine Ave', '555-987-6543', 'jessica.martinez@example.com', 'Operations', 'Staff Coordinator', '2023-08-20', 'Active', '2026-03-27 11:25:55', '2026-03-27 13:13:07', NULL, NULL, NULL),
    (30, '', NULL, 'Test Employee', NULL, '09123456789', 'test@email.com', 'IT', 'Tester', NULL, 'Active', '2026-03-29 09:27:35', '2026-04-05 08:46:02', NULL, NULL, NULL),
    (31, '', NULL, 'Test Employee', NULL, '09123456789', 'test@email.com', 'IT', 'Tester', NULL, 'Active', '2026-03-29 09:27:55', '2026-04-05 08:46:09', NULL, NULL, NULL),
    (32, '', NULL, 'Admin User', NULL, '09123456789', 'admin@bcp.com', 'Administration', 'System Administrator', NULL, 'Active', '2026-03-29 10:10:37', '2026-04-05 08:46:22', NULL, NULL, NULL),
    (33, 'T004', 4, 'Placer, Jan Russel C.', '', '09876543211', 'ronalddeguzman@gmail.com', 'Finance', 'hr', '2026-04-15', 'Active', '2026-04-05 06:55:08', '2026-04-05 09:23:35', '2026-04-08', 'Male', 'Profed'),
    (34, 'T004', 4, 'Placer, Jan Russel C.', '', '09876543211', 'placerrussel5@gmail.com', 'Finance', 'hr', '2026-04-15', 'Active', '2026-04-05 06:55:39', '2026-04-05 09:23:35', '2026-04-07', 'Male', 'Profed');
    ";
    $db->exec($insertDataSQL);

    // Step 6: Create trigger (skip if employee_change_history doesn't exist)
    echo "Step 6: Checking for trigger requirements...\n";
    $historyTableExists = false;
    try {
        $checkStmt = $db->query('SHOW TABLES LIKE "employee_change_history"');
        $historyTableExists = $checkStmt->rowCount() > 0;
    } catch (Exception $e) {
        $historyTableExists = false;
    }

    if ($historyTableExists) {
        echo "Creating trigger...\n";
        $triggerSQL = "
        DELIMITER $$
        CREATE TRIGGER `after_employee_update` AFTER UPDATE ON `employees` FOR EACH ROW BEGIN
            -- Log department change
            IF OLD.department != NEW.department AND NEW.department IS NOT NULL THEN
                INSERT INTO employee_change_history
                (employee_id, change_type, old_value, new_value, effective_date, updated_by)
                VALUES
                (NEW.employee_id, 'Department Transfer', OLD.department, NEW.department, CURDATE(), 'System Trigger');
            END IF;

            -- Log position change
            IF OLD.position != NEW.position AND NEW.position IS NOT NULL THEN
                INSERT INTO employee_change_history
                (employee_id, change_type, old_value, new_value, effective_date, updated_by)
                VALUES
                (NEW.employee_id, 'Position Change', OLD.position, NEW.position, CURDATE(), 'System Trigger');
            END IF;

            -- Log status change
            IF OLD.employment_status != NEW.employment_status AND NEW.employment_status IS NOT NULL THEN
                INSERT INTO employee_change_history
                (employee_id, change_type, old_value, new_value, effective_date, updated_by)
                VALUES
                (NEW.employee_id, 'Status Change', OLD.employment_status, NEW.employment_status, CURDATE(), 'System Trigger');
            END IF;

            -- Log name change
            IF OLD.full_name != NEW.full_name AND NEW.full_name IS NOT NULL THEN
                INSERT INTO employee_change_history
                (employee_id, change_type, old_value, new_value, effective_date, updated_by)
                VALUES
                (NEW.employee_id, 'Name Change', OLD.full_name, NEW.full_name, CURDATE(), 'System Trigger');
            END IF;

            -- Log contact number change
            IF OLD.contact_number != NEW.contact_number AND NEW.contact_number IS NOT NULL THEN
                INSERT INTO employee_change_history
                (employee_id, change_type, old_value, new_value, effective_date, updated_by)
                VALUES
                (NEW.employee_id, 'Contact Update', OLD.contact_number, NEW.contact_number, CURDATE(), 'System Trigger');
            END IF;

            -- Log email change
            IF OLD.email != NEW.email AND NEW.email IS NOT NULL THEN
                INSERT INTO employee_change_history
                (employee_id, change_type, old_value, new_value, effective_date, updated_by)
                VALUES
                (NEW.employee_id, 'Email Update', OLD.email, NEW.email, CURDATE(), 'System Trigger');
            END IF;

            -- Log address change
            IF OLD.address != NEW.address AND NEW.address IS NOT NULL THEN
                INSERT INTO employee_change_history
                (employee_id, change_type, old_value, new_value, effective_date, updated_by)
                VALUES
                (NEW.employee_id, 'Address Update', OLD.address, NEW.address, CURDATE(), 'System Trigger');
            END IF;

            -- Log date hired change
            IF OLD.date_hired != NEW.date_hired AND NEW.date_hired IS NOT NULL THEN
                INSERT INTO employee_change_history
                (employee_id, change_type, old_value, new_value, effective_date, updated_by)
                VALUES
                (NEW.employee_id, 'Hire Date Change', OLD.date_hired, NEW.date_hired, CURDATE(), 'System Trigger');
            END IF;
        END
        $$
        DELIMITER ;
        ";
        $db->exec($triggerSQL);
        echo "Trigger created successfully.\n";
    } else {
        echo "Skipping trigger creation (employee_change_history table not found).\n";
        echo "Note: You can create the trigger later by first creating the employee_change_history table.\n";
    }

    // Step 7: Verify the replacement
    echo "Step 7: Verifying replacement...\n";
    $countStmt = $db->query('SELECT COUNT(*) as count FROM employees');
    $newCount = $countStmt->fetch(PDO::FETCH_ASSOC);

    echo "\n✅ Table replacement completed successfully!\n";
    echo "New record count: " . $newCount['count'] . "\n";
    echo "Backup of old data is available in this script for reference.\n";

    // Step 8: Re-enable foreign key checks
    echo "Step 8: Re-enabling foreign key checks...\n";
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Show backup data summary
    echo "\nOld data backup (first 3 records):\n";
    for ($i = 0; $i < min(3, count($currentData)); $i++) {
        echo "- " . $currentData[$i]['full_name'] . " (" . $currentData[$i]['employee_id'] . ")\n";
    }
    if (count($currentData) > 3) {
        echo "- ... and " . (count($currentData) - 3) . " more records\n";
    }

} catch (Exception $e) {
    echo '❌ Error during table replacement: ' . $e->getMessage() . "\n";
    echo "The table may be in an inconsistent state. Please check the database.\n";
}
?>