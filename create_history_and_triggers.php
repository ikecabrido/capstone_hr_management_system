<?php
require_once __DIR__ . '/auth/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Creating employee_change_history table...\n";

    $createHistoryTableSQL = "
    CREATE TABLE `employee_change_history` (
      `history_id` int(11) NOT NULL,
      `employee_id` int(11) NOT NULL,
      `change_type` varchar(50) NOT NULL,
      `old_value` text DEFAULT NULL,
      `new_value` text DEFAULT NULL,
      `changed_by` int(11) DEFAULT NULL,
      `change_date` timestamp NOT NULL DEFAULT current_timestamp(),
      `field_changed` varchar(100) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";

    $db->exec($createHistoryTableSQL);

    // Add primary key
    $db->exec("ALTER TABLE `employee_change_history` ADD PRIMARY KEY (`history_id`)");
    $db->exec("ALTER TABLE `employee_change_history` MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT");

    // Add foreign key constraint
    $db->exec("ALTER TABLE `employee_change_history` ADD CONSTRAINT `fk_employee_history` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE");

    echo "✅ employee_change_history table created successfully!\n";

    // Now create the triggers
    echo "Creating triggers...\n";

    $triggerSQL = "
    DELIMITER ;;

    CREATE TRIGGER `trg_employee_insert` AFTER INSERT ON `employees`
    FOR EACH ROW
    BEGIN
        INSERT INTO employee_change_history (employee_id, change_type, new_value, field_changed)
        VALUES (NEW.employee_id, 'INSERT', CONCAT('New employee: ', NEW.full_name), 'ALL');
    END;;

    CREATE TRIGGER `trg_employee_update` AFTER UPDATE ON `employees`
    FOR EACH ROW
    BEGIN
        IF OLD.full_name != NEW.full_name THEN
            INSERT INTO employee_change_history (employee_id, change_type, old_value, new_value, field_changed)
            VALUES (NEW.employee_id, 'UPDATE', OLD.full_name, NEW.full_name, 'full_name');
        END IF;

        IF OLD.department != NEW.department THEN
            INSERT INTO employee_change_history (employee_id, change_type, old_value, new_value, field_changed)
            VALUES (NEW.employee_id, 'UPDATE', OLD.department, NEW.department, 'department');
        END IF;

        IF OLD.position != NEW.position THEN
            INSERT INTO employee_change_history (employee_id, change_type, old_value, new_value, field_changed)
            VALUES (NEW.employee_id, 'UPDATE', OLD.position, NEW.position, 'position');
        END IF;

        IF OLD.employment_status != NEW.employment_status THEN
            INSERT INTO employee_change_history (employee_id, change_type, old_value, new_value, field_changed)
            VALUES (NEW.employee_id, 'UPDATE', OLD.employment_status, NEW.employment_status, 'employment_status');
        END IF;
    END;;

    CREATE TRIGGER `trg_employee_delete` BEFORE DELETE ON `employees`
    FOR EACH ROW
    BEGIN
        INSERT INTO employee_change_history (employee_id, change_type, old_value, field_changed)
        VALUES (OLD.employee_id, 'DELETE', CONCAT('Deleted employee: ', OLD.full_name), 'ALL');
    END;;

    DELIMITER ;
    ";

    $db->exec($triggerSQL);
    echo "✅ Triggers created successfully!\n";

    // Test the triggers by updating a record
    echo "Testing triggers...\n";
    $db->exec("UPDATE employees SET department = 'IT Department' WHERE employee_id = 1");

    $historyCount = $db->query('SELECT COUNT(*) as count FROM employee_change_history')->fetch(PDO::FETCH_ASSOC);
    echo "History records created: " . $historyCount['count'] . "\n";

    echo "\n✅ All setup completed successfully!\n";

} catch (Exception $e) {
    echo '❌ Error: ' . $e->getMessage() . "\n";
}
?>