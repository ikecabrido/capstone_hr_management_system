<?php
require_once __DIR__ . '/auth/database.php';

$db = Database::getInstance()->getConnection();

try {
    $stmt = $db->prepare("SELECT pc.id AS pc_id, pc.settlement_id, pc.status FROM payroll_clearances pc LEFT JOIN exit_employee_settlements es ON pc.settlement_id = es.id WHERE es.payroll_clearance_id IS NULL");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($rows) . " payroll_clearances with no linked settlement record.\n";

    $updated = 0;
    foreach ($rows as $r) {
        $upd = $db->prepare("UPDATE exit_employee_settlements SET payroll_clearance_id = :pid, payroll_clearance_status = :status WHERE id = :sid");
        $upd->execute([':pid' => $r['pc_id'], ':status' => $r['status'], ':sid' => $r['settlement_id']]);
        $updated += $upd->rowCount();
    }

    echo "Updated settlements: $updated\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

?>