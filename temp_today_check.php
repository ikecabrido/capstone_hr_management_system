<?php
require 'auth/database.php';
$db = Database::getInstance()->getConnection();
$dates = ['2026-04-05','2026-04-06'];
foreach ($dates as $date) {
    echo "DATE: $date\n";
    $rows = $db->query("SELECT employee_id, attendance_id, time_in, time_out, created_at, updated_at FROM ta_attendance WHERE attendance_date = '$date' ORDER BY employee_id, attendance_id")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows, JSON_PRETTY_PRINT);
    echo "\n\n";
}
$duplicates = $db->query("SELECT employee_id, attendance_date, COUNT(*) as cnt FROM ta_attendance GROUP BY employee_id, attendance_date HAVING cnt > 1 ORDER BY attendance_date, employee_id")->fetchAll(PDO::FETCH_ASSOC);
echo "DUPLICATES:\n";
echo json_encode($duplicates, JSON_PRETTY_PRINT);
?>