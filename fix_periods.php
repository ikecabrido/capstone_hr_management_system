<?php
require_once 'auth/database.php';

$db = Database::getInstance()->getConnection();

try {
    // Check if period 0 is referenced in payslips
    echo '=== CHECKING REFERENCES TO PERIOD 0 ===' . PHP_EOL;
    $stmt = $db->query('SELECT COUNT(*) as count FROM pr_payslips WHERE period_id = 0');
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo 'Payslips referencing period 0: ' . $count['count'] . PHP_EOL;


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
