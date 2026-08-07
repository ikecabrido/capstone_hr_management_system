<?php
require_once __DIR__ . '/auth/database.php';

try {
    $db = Database::getInstance()->getConnection();

    // Get current table structure
    echo "Current employees table structure:\n";
    $stmt = $db->query('DESCRIBE employees');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Key'] . ' - ' . $row['Default'] . ' - ' . $row['Extra'] . "\n";
    }

    // Count current records
    $countStmt = $db->query('SELECT COUNT(*) as count FROM employees');
    $count = $countStmt->fetch(PDO::FETCH_ASSOC);
    echo "\nCurrent record count: " . $count['count'] . "\n";

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>