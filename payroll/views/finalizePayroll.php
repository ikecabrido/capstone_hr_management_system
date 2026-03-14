<?php
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../controllers/PayrollController.php';

if (!isset($_POST['period_id'])) {
    die('Invalid request');
}

$periodId = (int) $_POST['period_id'];

// Use controller
$controller = new PayrollController();

// Finalize payroll
$runId = $controller->finalize($periodId);


// Redirect back
header("Location: payrollProcess.php?success=1");
exit;
