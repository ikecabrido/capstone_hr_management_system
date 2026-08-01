<?php
require 'auth/database.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT attendance_id, employee_id, attendance_date, time_in, time_out, recorded_by, status, created_at, updated_at FROM ta_attendance ORDER BY attendance_id DESC LIMIT 10");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    echo json_encode($row, JSON_UNESCAPED_SLASHES) . PHP_EOL;
}
