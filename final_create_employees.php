<?php
require_once __DIR__ . '/auth/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Creating employees table with foreign key checks disabled...\n";

    // Disable foreign key checks
    $db->exec('SET FOREIGN_KEY_CHECKS = 0');
    echo "Foreign key checks disabled\n";

    // Create table
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
    echo "Table created successfully\n";

    // Add primary key
    $db->exec("ALTER TABLE `employees` ADD PRIMARY KEY (`employee_id`)");
    $db->exec("ALTER TABLE `employees` MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35");
    echo "Primary key and auto increment added\n";

    // Insert data
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
    echo "Data inserted successfully\n";

    // Re-enable foreign key checks
    $db->exec('SET FOREIGN_KEY_CHECKS = 1');
    echo "Foreign key checks re-enabled\n";

    // Verify
    $countStmt = $db->query('SELECT COUNT(*) as count FROM employees');
    $count = $countStmt->fetch(PDO::FETCH_ASSOC);
    echo "\n✅ Employees table created successfully with " . $count['count'] . " records!\n";

} catch (Exception $e) {
    echo '❌ Error: ' . $e->getMessage() . "\n";
}
?>