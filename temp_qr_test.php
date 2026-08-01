<?php
require 'auth/database.php';
$db = Database::getInstance()->getConnection();
$token = 'a9e424847b78f4634e1b8b7fd94ff46fbd9d78e5691983fb653e8213ae5ccd5d';
$stmt = $db->prepare('SELECT token_id, generated_for_date, expires_at, used, used_by FROM ta_attendance_tokens WHERE token = ?');
$stmt->execute([$token]);
print_r($stmt->fetch(PDO::FETCH_ASSOC));
$rows = $db->query('SELECT employee_id, full_name FROM employees ORDER BY employee_id LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
$rows = $db->query("SELECT attendance_id, employee_id, attendance_date, time_in, time_out FROM ta_attendance WHERE attendance_date = '2026-04-06' ORDER BY employee_id")->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
