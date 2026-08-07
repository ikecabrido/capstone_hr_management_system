<?php
require_once __DIR__ . '/auth/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Cleaning up employees table remnants...\n";

    // Check for any existing employees table remnants
    $result = $db->query('SHOW TABLES LIKE "employees"');
    if ($result->rowCount() > 0) {
        echo "Employees table still exists, dropping it...\n";
        $db->exec('SET FOREIGN_KEY_CHECKS = 0');
        $db->exec('DROP TABLE employees');
        $db->exec('SET FOREIGN_KEY_CHECKS = 1');
        echo "Table dropped.\n";
    }

    // Check for any triggers
    $triggerResult = $db->query('SHOW TRIGGERS');
    $foundTriggers = false;
    while ($trigger = $triggerResult->fetch(PDO::FETCH_ASSOC)) {
        if (strpos($trigger['Table'], 'employees') !== false || $trigger['Trigger'] === 'after_employee_update') {
            echo "Found trigger: " . $trigger['Trigger'] . ", dropping it...\n";
            $db->exec('DROP TRIGGER `' . $trigger['Trigger'] . '`');
            $foundTriggers = true;
        }
    }

    if (!$foundTriggers) {
        echo "No employees-related triggers found.\n";
    }

    echo "Cleanup completed successfully.\n";

} catch (Exception $e) {
    echo 'Error during cleanup: ' . $e->getMessage() . "\n";
}
?>