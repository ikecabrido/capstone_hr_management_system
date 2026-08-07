<?php
require_once __DIR__ . '/auth/database.php';

$db = Database::getInstance()->getConnection();

// CLI friendly output
try {
    // Recent payroll_clearances (last 24 hours)
    $stmt = $db->prepare("SELECT * FROM payroll_clearances WHERE requested_at >= (NOW() - INTERVAL 1 DAY) ORDER BY requested_at DESC LIMIT 100");
    $stmt->execute();
    $clearances = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Any settlement rows linked to payroll_clearance_id or with payroll_preview_data set
    $stmt2 = $db->prepare("SELECT id, employee_id, settlement_date, payroll_clearance_id, payroll_clearance_status, payroll_final_amount, payroll_preview_data FROM exit_employee_settlements WHERE payroll_clearance_id IS NOT NULL OR payroll_preview_data IS NOT NULL ORDER BY id DESC LIMIT 100");
    $stmt2->execute();
    $settlements = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    echo "=== payroll_clearances (last 24h) ===\n";
    if (empty($clearances)) {
        echo "No recent payroll_clearances found.\n";
    } else {
        foreach ($clearances as $c) {
            echo json_encode($c) . "\n";
        }
    }

    echo "\n=== exit_employee_settlements (linked) ===\n";
    if (empty($settlements)) {
        echo "No linked settlements found.\n";
    } else {
        foreach ($settlements as $s) {
            echo json_encode($s) . "\n";
        }
    }

    exit(0);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(2);
}

?>