<?php
require_once __DIR__ . '/../auth/database.php';

$db = Database::getInstance()->getConnection();

// Check active employees
$stmt = $db->query("SELECT COUNT(*) as count FROM employees WHERE employment_status='Active'");
$activeCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Check configured employees
$stmt = $db->query("SELECT COUNT(*) as count FROM pr_employee_details");
$configuredCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Get the employees that don't have configuration
$stmt = $db->query("
    SELECT e.employee_id, e.full_name, e.position
    FROM employees e
    WHERE e.employment_status='Active'
    AND e.employee_id NOT IN (SELECT employee_id FROM pr_employee_details)
");
$unconfigured = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Active Employees: $activeCount\n";
echo "Configured Employees: $configuredCount\n";
echo "Unconfigured Employees: " . count($unconfigured) . "\n\n";

if (count($unconfigured) > 0) {
    echo "Employees WITHOUT Payroll Configuration:\n";
    foreach ($unconfigured as $emp) {
        echo "- ID: {$emp['employee_id']}, Name: {$emp['full_name']}, Position: {$emp['position']}\n";
    }
}
