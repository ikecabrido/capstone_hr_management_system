<?php
require_once 'auth/database.php';
$db = Database::getInstance()->getConnection();

echo "=== PAYROLL PERIOD ===\n";
$stmt = $db->query('SELECT period_id, period_name, start_date, end_date FROM pr_periods WHERE period_id = 10');
$period = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Period 10: " . $period['period_name'] . " (" . $period['start_date'] . " to " . $period['end_date'] . ")\n\n";

echo "=== EMPLOYEE 8 - JESSICA MARTINEZ ===\n";
$stmt = $db->prepare('SELECT employee_id, full_name, position, employment_status FROM employees WHERE employee_id = ?');
$stmt->execute(['8']);
$emp = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Employee: " . $emp['full_name'] . " (" . $emp['position'] . ") - " . $emp['employment_status'] . "\n\n";

echo "=== SALARY DATA FOR EMPLOYEE 8 ===\n";
$stmt = $db->prepare('
    SELECT ros.application_id, ros.salary, ros.offer_status, ros.created_at
    FROM rao_offer_salary ros
    JOIN rao_hired_applicants rha ON ros.application_id = rha.application_id
    WHERE rha.employee_id = ?
    ORDER BY ros.created_at DESC
');
$stmt->execute(['8']);
$salaries = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Found " . count($salaries) . " salary records:\n";
foreach ($salaries as $s) {
    echo "  - Amount: " . $s['salary'] . ", Status: " . $s['offer_status'] . ", Created: " . $s['created_at'] . "\n";
}
if (empty($salaries)) {
    echo "  NO SALARY RECORDS FOUND\n";
}
echo "\n";

echo "=== ATTENDANCE DATA IN PERIOD RANGE (2026-01-01 to 2026-01-15) FOR EMPLOYEE 8 ===\n";
$stmt = $db->prepare('
    SELECT attendance_id, attendance_date, status, time_in, time_out, 
           total_hours_worked, regular_hours, overtime_hours, late_minutes
    FROM ta_attendance
    WHERE employee_id = ? 
      AND attendance_date BETWEEN ? AND ?
    ORDER BY attendance_date
');
$stmt->execute(['8', '2026-01-01', '2026-01-15']);
$attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Found " . count($attendance) . " attendance records in period:\n";
foreach ($attendance as $a) {
    echo "  - Date: " . $a['attendance_date'] . ", Status: " . $a['status'] . 
         ", Hours: " . $a['total_hours_worked'] . ", Late: " . $a['late_minutes'] . "\n";
}
if (empty($attendance)) {
    echo "  NO ATTENDANCE RECORDS IN PERIOD\n";
}
echo "\n";

echo "=== ALL ATTENDANCE DATA FOR EMPLOYEE 8 (ALL DATES) ===\n";
$stmt = $db->prepare('
    SELECT attendance_id, attendance_date, status, time_in, time_out, 
           total_hours_worked, regular_hours, overtime_hours, late_minutes
    FROM ta_attendance
    WHERE employee_id = ?
    ORDER BY attendance_date DESC
    LIMIT 20
');
$stmt->execute(['8']);
$attendance_all = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "All " . count($attendance_all) . " attendance records:\n";
foreach ($attendance_all as $a) {
    echo "  - Date: " . $a['attendance_date'] . ", Status: " . $a['status'] . 
         ", Hours: " . $a['total_hours_worked'] . ", Late: " . $a['late_minutes'] . "\n";
}
echo "\n";

echo "=== TEACHER LOADS (if applicable) ===\n";
$stmt = $db->prepare('SELECT * FROM pr_teacher_loads WHERE employee_id = ?');
$stmt->execute(['8']);
$loads = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Found " . count($loads) . " teacher load records\n\n";

echo "=== POSITION TYPE CONFIGURATION ===\n";
$stmt = $db->prepare('SELECT e.position FROM employees e WHERE employee_id = ?');
$stmt->execute(['8']);
$pos_emp = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Position: " . $pos_emp['position'] . "\n";
$stmt = $db->query("SELECT * FROM pr_position_deduction_rates WHERE position_type = 'professional' AND is_active = 1");
$rates = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rates) {
    echo "Found position rates\n";
} else {
    echo "NO position rates found for 'professional'\n";
}
?>
