<?php
require_once 'auth/database.php';
$db = Database::getInstance()->getConnection();

echo "=== ALL TABLES IN DATABASE ===\n";
$stmt = $db->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $table) {
    echo "- $table\n";
}

echo "\n=== CHECK IF pr_employee_details EXISTS ===\n";
$stmt = $db->query("SHOW TABLES LIKE 'pr_employee_details'");
$exists = $stmt->fetch(PDO::FETCH_ASSOC);
if ($exists) {
    echo "YES, pr_employee_details exists\n";
} else {
    echo "NO, pr_employee_details does NOT exist\n";
}

echo "\n=== EMPLOYEE TABLE STRUCTURE ===\n";
$stmt = $db->query("DESC employees");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
}
?>
