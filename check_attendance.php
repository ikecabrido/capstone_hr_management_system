<?php
require_once __DIR__ . '/auth/database.php';
$db = Database::getInstance()->getConnection();

// Get payroll periods
$stmt = $db->query('SELECT period_id, period_name, start_date, end_date FROM pr_periods ORDER BY start_date DESC LIMIT 5');
$periods = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Payroll Periods:\n";
foreach ($periods as $p) {
    echo $p['period_id'] . ': ' . $p['period_name'] . ' (' . $p['start_date'] . ' to ' . $p['end_date'] . ")\n";
}

// Get sample attendance data
echo "\nSample ta_attendance data:\n";
$stmt = $db->query('SELECT employee_id, attendance_date, status, total_hours_worked, regular_hours, overtime_hours, late_minutes FROM ta_attendance ORDER BY attendance_date DESC LIMIT 10');
$attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($attendance as $a) {
    echo $a['employee_id'] . ' - ' . $a['attendance_date'] . ' - ' . $a['status'] . ' - Hours: ' . $a['total_hours_worked'] . ' - Late: ' . $a['late_minutes'] . "\n";
}
?>