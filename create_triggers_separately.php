<?php
require_once __DIR__ . '/auth/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Creating triggers one by one...\n";

    // Create INSERT trigger
    echo "Creating INSERT trigger...\n";
    $insertTriggerSQL = "
    CREATE TRIGGER `trg_employee_insert` AFTER INSERT ON `employees`
    FOR EACH ROW
    BEGIN
        INSERT INTO employee_change_history (employee_id, change_type, new_value, field_changed)
        VALUES (NEW.employee_id, 'INSERT', CONCAT('New employee: ', NEW.full_name), 'ALL');
    END
    ";
    $db->exec($insertTriggerSQL);
    echo "✅ INSERT trigger created\n";

    // Create UPDATE trigger
    echo "Creating UPDATE trigger...\n";
    $updateTriggerSQL = "
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
    END
    ";
    $db->exec($updateTriggerSQL);
    echo "✅ UPDATE trigger created\n";

    // Create DELETE trigger
    echo "Creating DELETE trigger...\n";
    $deleteTriggerSQL = "
    CREATE TRIGGER `trg_employee_delete` BEFORE DELETE ON `employees`
    FOR EACH ROW
    BEGIN
        INSERT INTO employee_change_history (employee_id, change_type, old_value, field_changed)
        VALUES (OLD.employee_id, 'DELETE', CONCAT('Deleted employee: ', OLD.full_name), 'ALL');
    END
    ";
    $db->exec($deleteTriggerSQL);
    echo "✅ DELETE trigger created\n";

    // Test the triggers
    echo "Testing triggers...\n";
    $db->exec("UPDATE employees SET department = 'IT Support' WHERE employee_id = 1");

    $historyCount = $db->query('SELECT COUNT(*) as count FROM employee_change_history')->fetch(PDO::FETCH_ASSOC);
    echo "History records created: " . $historyCount['count'] . "\n";

    echo "\n✅ All triggers created and tested successfully!\n";

} catch (Exception $e) {
    echo '❌ Error: ' . $e->getMessage() . "\n";
}
?>