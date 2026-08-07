<?php
// Simulate calling requestPayrollClearance for a settlement to see why a request wasn't created
require_once __DIR__ . '/exit_management/controllers/ExitManagementController.php';
require_once __DIR__ . '/exit_management/controllers/SettlementController.php';
require_once __DIR__ . '/auth/database.php';

session_start();
// set a dummy user id for requested_by
$_SESSION['user'] = ['id' => 1, 'name' => 'TestUser'];

$settlementId = (int)($argv[1] ?? 33);
$notes = $argv[2] ?? 'Simulated request via CLI';

$controller = new SettlementController();
$result = $controller->requestPayrollClearance($settlementId, $_SESSION['user']['id'], $notes);

echo "Result:\n";
print_r($result);

// After simulation, print latest payroll_clearance for this settlement
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT * FROM payroll_clearances WHERE settlement_id = :sid ORDER BY requested_at DESC LIMIT 1");
$stmt->execute([':sid' => $settlementId]);
$pc = $stmt->fetch(PDO::FETCH_ASSOC);

echo "\nPayroll Clearance row (if any):\n";
print_r($pc);

// Print updated settlement row
$stmt2 = $db->prepare("SELECT id, payroll_clearance_id, payroll_clearance_status, payroll_final_amount FROM exit_employee_settlements WHERE id = :sid");
$stmt2->execute([':sid' => $settlementId]);
$sett = $stmt2->fetch(PDO::FETCH_ASSOC);

echo "\nSettlement row after simulation:\n";
print_r($sett);

?>