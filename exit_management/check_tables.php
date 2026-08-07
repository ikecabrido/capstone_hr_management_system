<?php
require_once __DIR__ . '/../auth/database.php';

$db = Database::getInstance()->getConnection();

echo "Exit Management Tables in Database:\n";
echo "═════════════════════════════════════════\n\n";

// Get all tables like 'exit%' or '%settlement%'
$stmt = $db->query("
    SELECT TABLE_NAME 
    FROM INFORMATION_SCHEMA.TABLES 
    WHERE TABLE_SCHEMA = 'data_hr'
    AND (TABLE_NAME LIKE 'exit%' OR TABLE_NAME LIKE '%settlement%')
");

$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($tables)) {
    echo "No exit/settlement tables found.\n";
} else {
    foreach ($tables as $table) {
        echo "✓ $table\n";
        
        // Show columns for this table
        $result = $db->query("DESCRIBE $table");
        $columns = $result->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($columns as $col) {
            echo "    └─ " . $col['Field'] . " (" . $col['Type'] . ")\n";
        }
        echo "\n";
    }
}

?>
