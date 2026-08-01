<?php
require 'auth/database.php';
$db = Database::getInstance()->getConnection();
$tokens = $db->query("SELECT token_id, token, generated_for_date, expires_at, used, used_by, used_at FROM ta_attendance_tokens WHERE used = 0 ORDER BY token_id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
echo "TOKENS:\n";
echo json_encode($tokens, JSON_PRETTY_PRINT);
?>