<?php
session_start();
require_once __DIR__ . '/../../auth/auth.php';
require_once __DIR__ . '/controllers/payrollController.php';
require_once __DIR__ . '/models/payrollModel.php';

$controller = new PayrollController();
$db = Database::getInstance()->getConnection();

// Get a sample payroll period
$periods = $controller->getPeriods();
if (empty($periods)) {
    die("No payroll periods found");
}

$periodId = $periods[0]['period_id'];
$selectedPeriod = $periods[0];

echo "<h2>Debug Payroll Data</h2>";
echo "<p>Period: " . htmlspecialchars($selectedPeriod['period_name']) . "</p>";

// Check pr_employee_details
echo "<h3>Employee Details Check</h3>";
$stmt = $db->query("
    SELECT e.employee_id, e.full_name, 
           CASE WHEN pd.employee_id IS NOT NULL THEN 'YES' ELSE 'NO' END AS has_config,
           pd.base_salary, pd.position_type,
           CASE WHEN pb.employee_id IS NOT NULL THEN 'YES' ELSE 'NO' END AS has_benefits
    FROM employees e
    LEFT JOIN pr_employee_details pd ON e.employee_id = pd.employee_id
    LEFT JOIN pr_employee_benefits pb ON e.employee_id = pb.employee_id
    WHERE e.employment_status = 'Active'
    LIMIT 10
");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr><th>Employee ID</th><th>Name</th><th>Config</th><th>Base Salary</th><th>Position</th><th>Benefits</th></tr>";
foreach ($results as $row) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['employee_id']) . "</td>";
    echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
    echo "<td>" . ($row['has_config'] === 'YES' ? '✓' : '✗') . "</td>";
    echo "<td>" . ($row['base_salary'] ?? '-') . "</td>";
    echo "<td>" . ($row['position_type'] ?? '-') . "</td>";
    echo "<td>" . ($row['has_benefits'] === 'YES' ? '✓' : '✗') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Check position deduction rates
echo "<h3>Position Deduction Rates</h3>";
$stmt = $db->query("SELECT * FROM pr_position_deduction_rates WHERE is_active = 1");
$rates = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($rates)) {
    echo "<p style='color: red;'>❌ NO DEDUCTION RATES CONFIGURED!</p>";
} else {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Position</th><th>Absence Deduction</th><th>Late/Min</th><th>Late/Hour</th></tr>";
    foreach ($rates as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['position_type']) . "</td>";
        echo "<td>" . $row['absence_deduction_amount'] . "</td>";
        echo "<td>" . $row['late_per_minute_rate'] . "</td>";
        echo "<td>" . $row['late_per_hour_rate'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Try getting payroll preview
echo "<h3>Payroll Preview Test</h3>";
$payrollModel = new PayrollModel($db);
$preview = $payrollModel->getPayrollPreview($periodId);
echo "<p>Employees with payroll data: " . count($preview) . "</p>";

if (!empty($preview)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Name</th><th>Gross Pay</th><th>Earnings Count</th><th>Deductions Count</th></tr>";
    foreach ($preview as $emp) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($emp['name']) . "</td>";
        echo "<td>" . ($emp['gross_pay'] ?? 0) . "</td>";
        echo "<td>" . count($emp['earnings'] ?? []) . "</td>";
        echo "<td>" . count($emp['deductions'] ?? []) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ NO PAYROLL DATA - Check employee configuration!</p>";
}
?>
<a href="payroll.php">&larr; Back to Payroll</a>
