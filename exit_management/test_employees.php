<?php
require_once "../auth/database.php";

header('Content-Type: application/json');

$db = Database::getInstance()->getConnection();

// Test 1: Check if employees table exists and count
$stmt = $db->query("SELECT COUNT(*) as count FROM employees");
$count = $stmt->fetch(PDO::FETCH_ASSOC);

// Test 2: Get all employees regardless of status
$stmt = $db->query("SELECT * FROM employees LIMIT 5");
$allEmployees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Test 3: Get active employees (matching our query)
$stmt = $db->query("
    SELECT
        e.employee_id AS id,
        e.employee_id AS username,
        e.full_name,
        e.email,
        e.position,
        e.department,
        'active' AS status
    FROM employees e
    WHERE e.employment_status = 'Active'
    ORDER BY e.full_name
");
$activeEmployees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Test 4: Check employment_status values
$stmt = $db->query("SELECT DISTINCT employment_status FROM employees");
$statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'total_employees' => $count['count'],
    'all_employees_sample' => $allEmployees,
    'active_employees' => $activeEmployees,
    'distinct_statuses' => $statuses,
    'query_success' => count($activeEmployees) > 0
]);
?>
