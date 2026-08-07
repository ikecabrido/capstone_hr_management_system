<?php
require_once 'auth/database.php';
$db = Database::getInstance()->getConnection();

$employeeId = '8';

$stmt = $db->prepare('SELECT * FROM ta_attendance WHERE employee_id = ? ORDER BY attendance_date DESC LIMIT 20');
$stmt->execute([$employeeId]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Attendance rows for employee_id={$employeeId}:\n";
foreach ($rows as $r) {
    echo implode(' | ', [
        $r['attendance_id'],
        $r['employee_id'],
        $r['attendance_date'],
        $r['status'],
        $r['time_in'],
        $r['time_out'],
        $r['total_hours_worked'],
        $r['regular_hours'],
        $r['overtime_hours'],
        $r['late_minutes'],
    ]) . "\n";
}

$stmt2 = $db->prepare('SELECT * FROM rao_offer_salary ros JOIN rao_hired_applicants rha ON ros.application_id = rha.application_id WHERE rha.employee_id = ? ORDER BY ros.created_at DESC LIMIT 5');
$stmt2->execute([$employeeId]);
$rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
echo "\nSalary rows for employee_id={$employeeId}:\n";
foreach ($rows2 as $r) {
    echo implode(' | ', [
        $r['application_id'],
        $r['salary'],
        $r['offer_status'],
        $r['created_at'],
    ]) . "\n";
}

$stmt3 = $db->prepare('SELECT e.employee_id,e.position,e.employment_status FROM employees e WHERE e.employee_id = ?');
$stmt3->execute([$employeeId]);
$e = $stmt3->fetch(PDO::FETCH_ASSOC);

echo "\nEmployee row: ";
print_r($e);
