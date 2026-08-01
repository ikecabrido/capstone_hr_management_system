<?php
session_start();
require_once "../auth/auth_check.php";
require_once "models/ResignationModel.php";

echo "<h1>Testing Payroll Pre-clearance Integration</h1>";

// First, get a valid employee and user
try {
    $db = (new ResignationModel())->getConnection();

    // Get first available employee
    $stmt = $db->query("SELECT employee_id, full_name FROM employees LIMIT 1");
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        die("<p>❌ No employees found in database. Please add employees first.</p>");
    }

    // Get first available user for preclearance
    $stmt = $db->query("SELECT id, full_name FROM users LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("<p>❌ No users found in database. Please add users first.</p>");
    }

    echo "<p>Using Employee: {$employee['full_name']} (ID: {$employee['employee_id']})</p>";
    echo "<p>Using User for Pre-clearance: {$user['full_name']} (ID: {$user['id']})</p>";

    // Check for records with id=0
    echo "<h2>Checking for id=0 records...</h2>";
    foreach ($tables as $table) {
        $stmt = $db->query("SELECT COUNT(*) as count FROM $table WHERE id = 0");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        if ($count > 0) {
            echo "<p>❌ $table has $count record(s) with id=0 - fixing...</p>";
            $db->exec("DELETE FROM $table WHERE id = 0");
            echo "<p>✅ Deleted id=0 records from $table</p>";
        } else {
            echo "<p>✅ $table has no id=0 records</p>";
        }
    }

    // Check if employee already has pending resignation
    $stmt = $db->prepare("SELECT COUNT(*) FROM exit_resignations WHERE employee_id = ? AND status = 'pending'");
    $stmt->execute([$employee['employee_id']]);
    $pendingCount = $stmt->fetchColumn();

    if ($pendingCount > 0) {
        die("<p>❌ Employee already has a pending resignation. Please use a different employee or clear existing resignations.</p>");
    }

    // Test data
    $testData = [
        'employee_id' => $employee['employee_id'],
        'resignation_type' => 'voluntary',
        'reason' => 'Testing payroll pre-clearance integration',
        'notice_date' => '2026-04-10',
        'last_working_date' => '2026-04-30',
        'comments' => 'Automated test for payroll clearance',
        'submitted_by' => $_SESSION['user']['id'] ?? null,
        'preclearance_desk_person' => $user['id']
    ];

    $resignationModel = new ResignationModel();

    echo "<h2>Submitting Resignation...</h2>";
    $resignationId = $resignationModel->submitResignation($testData);

    echo "<p>✅ Resignation submitted successfully! ID: $resignationId</p>";

    // Check if settlement was created
    echo "<h2>Checking Settlement Creation...</h2>";
    $stmt = $resignationModel->getConnection()->prepare("SELECT * FROM exit_employee_settlements WHERE resignation_id = ?");
    $stmt->execute([$resignationId]);
    $settlement = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($settlement) {
        echo "<p>✅ Settlement created! ID: {$settlement['id']}</p>";
        echo "<p>Net Payable: ₱" . number_format($settlement['net_payable'], 2) . "</p>";
    } else {
        echo "<p>❌ Settlement not found</p>";
    }

    // Check if payroll clearance was created
    echo "<h2>Checking Payroll Clearance Request...</h2>";
    $stmt = $resignationModel->getConnection()->prepare("SELECT * FROM payroll_clearances WHERE settlement_id = ?");
    $stmt->execute([$settlement['id'] ?? 0]);
    $clearance = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($clearance) {
        echo "<p>✅ Payroll clearance request created! ID: {$clearance['id']}</p>";
        echo "<p>Status: {$clearance['status']}</p>";
        echo "<p>Comments: {$clearance['comments']}</p>";
    } else {
        echo "<p>❌ Payroll clearance request not found</p>";
    }

} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
    
    // Cleanup any partial data
    echo "<h3>Cleanup</h3>";
    try {
        // Find and delete any test resignations created in the last minute
        $stmt = $db->prepare("DELETE FROM exit_resignations WHERE reason = 'Testing payroll pre-clearance integration' AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)");
        $deleted = $stmt->execute();
        echo "<p>Deleted $deleted test resignation(s)</p>";
    } catch (Exception $cleanupError) {
        echo "<p>❌ Cleanup failed: " . $cleanupError->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<p><a href='exit_management.php'>Back to Exit Management</a></p>";
?>