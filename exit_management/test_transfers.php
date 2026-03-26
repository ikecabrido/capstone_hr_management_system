<?php
require_once "../auth/database.php";

header('Content-Type: application/json');

$db = Database::getInstance()->getConnection();

// Test 1: Count transfer plans
$stmt = $db->query("SELECT COUNT(*) as count FROM knowledge_transfer_plans");
$count = $stmt->fetch(PDO::FETCH_ASSOC);

// Test 2: Get all transfer plans with JOIN
$stmt = $db->query("
    SELECT 
        ktp.id,
        ktp.employee_id,
        ktp.successor_id,
        ktp.start_date,
        ktp.end_date,
        ktp.status,
        ktp.created_at,
        ktp.updated_at,
        e.full_name as employee_name,
        s.full_name as successor_name
    FROM knowledge_transfer_plans ktp
    JOIN employees e ON ktp.employee_id = e.employee_id
    LEFT JOIN employees s ON ktp.successor_id = s.employee_id
    ORDER BY ktp.created_at DESC
");
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Test 3: Get employees to verify they exist
$stmt = $db->query("SELECT COUNT(*) as count FROM employees");
$empCount = $stmt->fetch(PDO::FETCH_ASSOC);

// Test 4: Get sample employee IDs
$stmt = $db->query("SELECT employee_id, full_name FROM employees LIMIT 3");
$sampleEmployees = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'total_plans' => $count['count'],
    'transfer_plans' => $plans,
    'total_employees' => $empCount['count'],
    'sample_employees' => $sampleEmployees,
    'query_success' => count($plans) >= 0
]);
?>
