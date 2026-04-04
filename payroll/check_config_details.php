<?php
require_once __DIR__ . '/../auth/database.php';

$db = Database::getInstance()->getConnection();

// Get configured employees
$stmt = $db->query("
    SELECT pd.*, e.full_name, e.position
    FROM pr_employee_details pd
    JOIN employees e ON pd.employee_id = e.employee_id
");
$configured = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Configured Employees:\n";
foreach ($configured as $emp) {
    echo "- ID: {$emp['employee_id']}, Name: {$emp['full_name']}\n";
    echo "  Base Salary: {$emp['base_salary']}, Position Type: {$emp['position_type']}\n\n";
}
