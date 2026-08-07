<?php
require_once __DIR__ . '/auth/database.php';

try {
    $db = Database::getInstance()->getConnection();

    // Check if employee_change_history table exists
    $result = $db->query('SHOW TABLES LIKE "employee_change_history"');
    if ($result->rowCount() > 0) {
        echo "employee_change_history table exists\n";
    } else {
        echo "employee_change_history table does not exist\n";
    }

    // Check current database
    $stmt = $db->query('SELECT DATABASE() as db');
    $dbName = $stmt->fetch(PDO::FETCH_ASSOC);
    echo 'Current database: ' . $dbName['db'] . "\n";

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>