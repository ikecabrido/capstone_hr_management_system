<?php
require_once 'auth/database.php';
$db = Database::getInstance()->getConnection();

echo "=== pr_employee_details FOR EMPLOYEE 8 ===\n";
$stmt = $db->prepare('SELECT * FROM pr_employee_details WHERE employee_id = ? LIMIT 5');
$stmt->execute(['8']);
$details = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!empty($details)) {
    foreach ($details as $d) {
        echo "base_salary: " . $d['base_salary'] . ", is_active: " . $d['is_active'] . "\n";
    }
} else {
    echo "NO RECORDS IN pr_employee_details\n";
}
echo "\n";

echo "=== rao_offer_salary FOR EMPLOYEE 8 (ALL STATUSES) ===\n";
$stmt = $db->prepare('
    SELECT ros.*, rha.employee_id
    FROM rao_offer_salary ros
    JOIN rao_hired_applicants rha ON ros.application_id = rha.application_id
    WHERE rha.employee_id = ?
    ORDER BY ros.created_at DESC
');
$stmt->execute(['8']);
$offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!empty($offers)) {
    foreach ($offers as $o) {
        echo "salary: " . $o['salary'] . ", offer_status: " . $o['offer_status'] . ", created: " . $o['created_at'] . "\n";
    }
} else {
    echo "NO RECORDS IN rao_offer_salary\n";
}
echo "\n";

echo "=== TEST PAYROLL CALCULATION BY RUNNING CALCULATEEMPLOYEEPAYROLL ===\n";
require_once 'payroll/models/payrollModel.php';
$model = new PayrollModel($db);
$result = $model->calculateEmployeePayroll('8', 10);
if (!empty($result)) {
    echo "Basic Salary: " . $result['basic_salary'] . "\n";
    echo "Gross Pay: " . $result['gross_pay'] . "\n"; 
    echo "Net Pay: " . $result['net_pay'] . "\n";
    echo "Days Worked: " . $result['days_worked'] . "\n";
} else {
    echo "NO RESULT\n";
}
?>
