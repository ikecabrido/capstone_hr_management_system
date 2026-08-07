<?php
require_once __DIR__ . '/auth/database.php';

try {
    $db = Database::getInstance()->getConnection();

    // Check if employees table exists
    $result = $db->query('SHOW TABLES LIKE "employees"');
    if ($result->rowCount() > 0) {
        echo "employees table exists\n";

        // Count records
        $countStmt = $db->query('SELECT COUNT(*) as count FROM employees');
        $count = $countStmt->fetch(PDO::FETCH_ASSOC);
        echo 'Record count: ' . $count['count'] . "\n";
    } else {
        echo "employees table does not exist\n";
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>