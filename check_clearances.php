<?php
require_once 'auth/database.php';

$db = Database::getInstance()->getConnection();

try {
    echo '=== PAYROLL_CLEARANCES TABLE STRUCTURE ===' . PHP_EOL;
    $stmt = $db->query('DESCRIBE payroll_clearances');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' - ' . $row['Type'] . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
