<?php
// Test script: compute payroll for latest active teacher and latest payroll period
require_once __DIR__ . '/../auth/database.php';
require_once __DIR__ . '/models/payrollModel.php';

// Initialize DB and model
$db = Database::getInstance()->getConnection();
$pm = new PayrollModel($db);

// Get teachers
$teachers = $pm->getTeacherEmployees();
if (empty($teachers)) {
    echo "No active teachers found\n";
    exit(0);
}

// Get payroll periods
$periods = $pm->getPayrollPeriods();
if (empty($periods)) {
    echo "No payroll periods found\n";
    exit(0);
}

$teacher = $teachers[0];
$period = $periods[0];

// Try to compute payroll
$result = [];
try {
    $result = $pm->calculateEmployeePayroll($teacher['id'], (int)$period['period_id']);
} catch (Exception $e) {
    echo "Error while calculating: " . $e->getMessage() . "\n";
    exit(1);
}

echo json_encode([
    'teacher' => $teacher,
    'period' => $period,
    'calculation' => $result
], JSON_PRETTY_PRINT);
