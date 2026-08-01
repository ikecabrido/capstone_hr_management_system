<?php
require_once __DIR__ . '/../../auth/database.php';
require_once __DIR__ . '/../controllers/PayrollController.php';
require_once __DIR__ . '/../../auth/auth_check.php'; // Check authentication

if (!isset($_POST['period_id'])) {
    die('Invalid request');
}

$periodId = (int) $_POST['period_id'];
$userId = $_SESSION['user']['id'] ?? null;

// Use controller
$controller = new PayrollController();

// Finalize payroll
$runId = $controller->finalize($periodId, $userId);


// Redirect back
header("Location: payrollProcess.php?success=1");
exit;
