<?php
require_once __DIR__ . '/app/config/Database.php';

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->query('DESCRIBE ta_leave_types');
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "ta_leave_types columns:\n";
foreach($result as $col) {
    echo "  • " . $col['Field'] . " (" . $col['Type'] . ")\n";
}

echo "\nAll leave types:\n";
$stmt = $conn->query('SELECT * FROM ta_leave_types');
$leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($leaves as $leave) {
    echo "  • " . $leave['leave_type_name'] . " (ID: " . $leave['leave_type_id'] . ")\n";
}
?>
