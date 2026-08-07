<?php
require_once 'auth/database.php';

$db = Database::getInstance()->getConnection();

try {
    // Try to modify the column to ensure auto_increment
    $db->exec('ALTER TABLE pr_periods MODIFY COLUMN period_id INT(11) NOT NULL AUTO_INCREMENT');
    echo 'Modified period_id column with AUTO_INCREMENT' . PHP_EOL;

    // Verify the fix
    $stmt = $db->query('SHOW TABLE STATUS LIKE "pr_periods"');
    $status = $stmt->fetch(PDO::FETCH_ASSOC);
    echo 'Auto_increment: ' . ($status['Auto_increment'] ?? 'NULL') . PHP_EOL;

    // Check existing records
    $stmt = $db->query('SELECT period_id, period_name FROM pr_periods ORDER BY period_id');
    echo PHP_EOL . 'Existing records:' . PHP_EOL;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo 'ID: ' . $row['period_id'] . ' - Name: ' . $row['period_name'] . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
