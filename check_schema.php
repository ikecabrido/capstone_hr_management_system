<?php
require_once __DIR__ . '/auth/database.php';

$db = Database::getInstance()->getConnection();

echo "EMPLOYEES TABLE STRUCTURE:\n";
echo "═══════════════════════════════════════\n\n";

$result = $db->query('DESCRIBE employees');
$cols = $result->fetchAll(PDO::FETCH_ASSOC);

foreach($cols as $col) {
    echo $col['Field'] . " (" . $col['Type'] . ")";
    if ($col['Null'] === 'NO') echo " NOT NULL";
    if ($col['Key']) echo " [" . $col['Key'] . "]";
    echo "\n";
}

echo "\n\nSample Employees:\n";
echo "═════════════════════════════════════════════════════\n\n";

$result = $db->query('SELECT employee_id, full_name, position, department, employment_status, date_hired FROM employees LIMIT 3');
$employees = $result->fetchAll(PDO::FETCH_ASSOC);

foreach($employees as $emp) {
    echo "ID: " . $emp['employee_id'] . "\n";
    echo "Name: " . $emp['full_name'] . "\n";
    echo "Position: " . $emp['position'] . "\n";
    echo "Department: " . $emp['department'] . "\n";
    echo "Hire Date: " . $emp['date_hired'] . "\n";
    echo "Status: " . $emp['employment_status'] . "\n";
    echo "──────────────────────\n";
}

?>
