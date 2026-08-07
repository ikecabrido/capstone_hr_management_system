<?php
require_once __DIR__ . '/auth/database.php';
$db = Database::getInstance()->getConnection();

$result = $db->exec("UPDATE ta_attendance SET status = 'PRESENT' WHERE status = 'PENDING_APPROVAL'");
echo "Updated $result attendance records from PENDING_APPROVAL to PRESENT.\n";
?>