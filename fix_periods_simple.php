<?php
require_once 'auth/database.php';

$db = Database::getInstance()->getConnection();

try {
    echo '=== FIXING AUTO_INCREMENT ===' . PHP_EOL;

    // Delete the problematic record
    $db->exec('DELETE FROM pr_periods WHERE period_id = 0');
    echo 'Deleted period with ID 0' . PHP_EOL;

    // Reset auto_increment
    $db->exec('ALTER TABLE pr_periods AUTO_INCREMENT = 1');
    echo 'Reset auto_increment to 1' . PHP_EOL;

    // Verify the fix
    $stmt = $db->query('SHOW TABLE STATUS LIKE "pr_periods"');
    $status = $stmt->fetch(PDO::FETCH_ASSOC);
    echo 'New auto_increment: ' . ($status['Auto_increment'] ?? 'NULL') . PHP_EOL;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
