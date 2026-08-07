<?php
require_once 'auth/database.php';
require_once 'payroll/models/payrollModel.php';

$db = Database::getInstance()->getConnection();
$model = new PayrollModel($db);

echo "=== TESTING PAYROLL CALCULATION FOR EMPLOYEE 8, PERIOD 10 ===\n\n";

$result = $model->calculateEmployeePayroll('8', 10);

if (!empty($result)) {
    echo "Basic Salary: ₱" . number_format($result['basic_salary'], 2) . "\n";
    echo "Days Worked: " . $result['days_worked'] . "\n";
    echo "Gross Pay: ₱" . number_format($result['gross_pay'], 2) . "\n";
    echo "Total Deductions: ₱" . number_format($result['total_deductions'], 2) . "\n";
    echo "Net Pay: ₱" . number_format($result['net_pay'], 2) . "\n";
    echo "\n✅ SUCCESS - Payroll now has values!\n";
    
    if(!empty($result['earnings'])) {
        echo "\nEarnings breakdown:\n";
        foreach ($result['earnings'] as $earning) {
            echo "  - " . $earning['description'] . ": ₱" . number_format($earning['amount'], 2) . "\n";
        }
    }
} else {
    echo "❌ RESULT IS EMPTY\n";
}
?>
