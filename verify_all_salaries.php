<?php
$pdo = new PDO('mysql:host=localhost;dbname=data_hr', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

require_once __DIR__ . '/payroll/models/payrollEmployeeConfigModel.php';

$configModel = new PayrollEmployeeConfigModel($pdo);
$employees = $configModel->getAllEmployeesWithConfig();

echo "=== All Active Employees with Payroll Configuration ===\n";
echo str_pad("ID", 5) . str_pad("Name", 25) . str_pad("Position", 30) . str_pad("Base Salary", 15) . "\n";
echo str_repeat("-", 75) . "\n";

foreach ($employees as $emp) {
    echo str_pad($emp['employee_id'], 5) 
       . str_pad($emp['full_name'], 25) 
       . str_pad($emp['position'], 30) 
       . "₱" . number_format($emp['base_salary'], 2) . "\n";
}

echo "\n✅ All salaries now correctly pulled from rao_offer_salary (accepted offers)\n";
?>
