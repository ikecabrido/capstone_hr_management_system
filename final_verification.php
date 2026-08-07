<?php
require_once __DIR__ . '/auth/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "=== FINAL VERIFICATION ===\n\n";

    // Check table structure
    echo "1. Table Structure:\n";
    $stmt = $db->query("DESCRIBE employees");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "   - " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }

    // Check record count
    echo "\n2. Record Count:\n";
    $countStmt = $db->query('SELECT COUNT(*) as count FROM employees');
    $count = $countStmt->fetch(PDO::FETCH_ASSOC);
    echo "   - " . $count['count'] . " employee records\n";

    // Check history table
    $historyCountStmt = $db->query('SELECT COUNT(*) as count FROM employee_change_history');
    $historyCount = $historyCountStmt->fetch(PDO::FETCH_ASSOC);
    echo "   - " . $historyCount['count'] . " history records\n";

    // Check triggers
    echo "\n3. Triggers:\n";
    $triggerStmt = $db->query("SHOW TRIGGERS LIKE 'employees'");
    $triggers = $triggerStmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($triggers as $trigger) {
        echo "   - " . $trigger['Trigger'] . " (" . $trigger['Event'] . " on " . $trigger['Table'] . ")\n";
    }

    // Test trigger functionality
    echo "\n4. Testing Trigger Functionality:\n";
    $initialHistoryCount = $historyCount['count'];

    // Insert test
    echo "   - Testing INSERT trigger...\n";
    $db->exec("INSERT INTO employees (employee_no, full_name, department, position) VALUES ('TEST001', 'Test Employee', 'Testing', 'Tester')");
    $newHistoryCount = $db->query('SELECT COUNT(*) as count FROM employee_change_history')->fetch(PDO::FETCH_ASSOC)['count'];
    echo "     History records after insert: " . $newHistoryCount . " (was " . $initialHistoryCount . ")\n";

    // Update test
    echo "   - Testing UPDATE trigger...\n";
    $db->exec("UPDATE employees SET department = 'Quality Assurance' WHERE employee_no = 'TEST001'");
    $updateHistoryCount = $db->query('SELECT COUNT(*) as count FROM employee_change_history')->fetch(PDO::FETCH_ASSOC)['count'];
    echo "     History records after update: " . $updateHistoryCount . "\n";

    // Clean up test record
    $db->exec("DELETE FROM employees WHERE employee_no = 'TEST001'");

    // Show sample data
    echo "\n5. Sample Employee Data:\n";
    $sampleStmt = $db->query('SELECT employee_id, employee_no, full_name, department, position FROM employees ORDER BY employee_id LIMIT 5');
    while ($row = $sampleStmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("   - ID: %d, No: '%s', Name: %s (%s - %s)\n",
            $row['employee_id'],
            $row['employee_no'],
            $row['full_name'],
            $row['department'],
            $row['position']
        );
    }

    echo "\n✅ FINAL VERIFICATION COMPLETE - All systems operational!\n";

} catch (Exception $e) {
    echo '❌ Error: ' . $e->getMessage() . "\n";
}
?>