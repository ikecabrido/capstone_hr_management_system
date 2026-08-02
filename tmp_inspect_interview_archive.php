<?php
require_once 'auth/database.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT id, original_id, archive_data, restored, archive_reason FROM exit_archive WHERE archive_type = 'interview' ORDER BY id DESC LIMIT 5");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    echo "ROW id={$row['id']} original_id={$row['original_id']} restored={$row['restored']} reason={$row['archive_reason']}\n";
    echo $row['archive_data'] . "\n---\n";
}
