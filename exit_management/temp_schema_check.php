<?php
require __DIR__ . '/../auth/database.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query('SHOW COLUMNS FROM exit_employee_settlements');
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $c) {
    echo $c['Field'] . ' ' . $c['Type'] . "\n";
}
