<?php
require_once __DIR__ . '/payroll/controllers/payrollClearanceController.php';
require_once __DIR__ . '/auth/database.php';

$settlementId = (int)($argv[1] ?? 33);
$requestedBy = (int)($argv[2] ?? 1);
$controller = new PayrollClearanceController();
$result = $controller->createClearanceRequest($settlementId, $requestedBy);

echo "createClearanceRequest result:\n";
print_r($result);

$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT * FROM payroll_clearances WHERE settlement_id = :sid ORDER BY requested_at DESC LIMIT 1");
$stmt->execute([':sid' => $settlementId]);
$pc = $stmt->fetch(PDO::FETCH_ASSOC);

echo "\nPayroll clearance row:\n";
print_r($pc);

?>