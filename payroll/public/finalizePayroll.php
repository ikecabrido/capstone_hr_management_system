<?php
require_once __DIR__ . '/../controllers/PayrollController.php';

if (!isset($_POST['period_id'])) {
    die("Payroll period not selected.");
}

$controller = new PayrollController();
$runId = $controller->finalize((int)$_POST['period_id']);

header("Location: salaryOverview.php?run_id=$runId");
exit;
