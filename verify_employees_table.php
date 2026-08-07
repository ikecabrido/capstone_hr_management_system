<?php
require_once __DIR__ . '/auth/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Verifying employees table structure...\n\n";

    // Get table structure
    $stmt = $db->query("DESCRIBE employees");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Table Structure:\n";
    echo "---------------\n";
    foreach ($columns as $column) {
        echo sprintf("%-20s %-15s %-10s %-10s %-10s\n",
            $column['Field'],
            $column['Type'],
            $column['Null'],
            $column['Key'],
            $column['Default']
        );
    }

    echo "\nChecking for employee_no field...\n";
    $hasEmployeeNo = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'employee_no') {
            $hasEmployeeNo = true;
            echo "✅ employee_no field found: " . $column['Type'] . "\n";
            break;
        }
    }

    if (!$hasEmployeeNo) {
        echo "❌ employee_no field NOT found!\n";
    }

    echo "\nRecord count: ";
    $countStmt = $db->query('SELECT COUNT(*) as count FROM employees');
    $count = $countStmt->fetch(PDO::FETCH_ASSOC);
    echo $count['count'] . " records\n";

    echo "\nSample data with employee_no:\n";
    $sampleStmt = $db->query('SELECT employee_id, employee_no, full_name, department FROM employees ORDER BY employee_id');
    while ($row = $sampleStmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("- ID: %-2d, No: '%-10s', Name: %-20s (%s)\n",
            $row['employee_id'],
            $row['employee_no'],
            $row['full_name'],
            $row['department']
        );
    }

} catch (Exception $e) {
    echo '❌ Error: ' . $e->getMessage() . "\n";
}
?>