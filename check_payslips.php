<?php
require_once 'auth/database.php';

$db = Database::getInstance()->getConnection();

try {
    echo '=== PR_PAYSLIPS TABLE STRUCTURE ===' . PHP_EOL;
    $stmt = $db->query('DESCRIBE pr_payslips');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' - ' . $row['Type'] . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
