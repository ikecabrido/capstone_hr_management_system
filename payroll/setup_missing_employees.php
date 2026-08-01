<?php
require_once __DIR__ . '/../auth/database.php';

$db = Database::getInstance()->getConnection();

// Get all active employees without configuration
$stmt = $db->query("
    SELECT e.employee_id, e.full_name, e.position
    FROM employees e
    WHERE e.employment_status='Active'
    AND e.employee_id NOT IN (SELECT employee_id FROM pr_employee_details)
");
$unconfigured = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($unconfigured)) {
    echo "All employees already configured!\n";
    exit;
}

// Define default salary based on position (you can adjust these values)
$defaultSalaries = [
    'Operations Manager' => 28000,
    'Junior Developer' => 25000,
    'HR Specialist' => 24000,
    'Financial Analyst' => 26000,
    'Staff Coordinator' => 22000,
];

$insertStmt = $db->prepare("
    INSERT INTO pr_employee_details (employee_id, base_salary, position_type)
    VALUES (?, ?, 'Admin')
");

$count = 0;
foreach ($unconfigured as $emp) {
    // Get default salary from position, or use 25000 as fallback
    $salary = $defaultSalaries[$emp['position']] ?? 25000;
    
    try {
        $insertStmt->execute([
            $emp['employee_id'],
            $salary
        ]);
        $count++;
        echo "✓ Added {$emp['full_name']} - Base Salary: ₱" . number_format($salary, 2) . "\n";
    } catch (Exception $e) {
        echo "✗ Failed to add {$emp['full_name']}: " . $e->getMessage() . "\n";
    }
}

echo "\nTotal configured: $count employees\n";
echo "\nAll employees should now appear in the payroll processing view!\n";
