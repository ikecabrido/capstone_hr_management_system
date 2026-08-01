<?php
/**
 * Payroll Calculations Display Fixer
 * This script checks what's preventing the calculation data from showing
 */
session_start();
require_once __DIR__ . '/../../auth/auth.php';
require_once __DIR__ . '/controllers/payrollController.php';
require_once __DIR__ . '/models/payrollModel.php';

$db = Database::getInstance()->getConnection();
$controller = new PayrollController();
$payrollModel = new PayrollModel($db);

$issues = [];
$fixes = [];

// Check 1: Are there payroll periods?
$periods = $controller->getPeriods();
if (empty($periods)) {
    $issues[] = "❌ No payroll periods found in pr_periods table";
} else {
    $fixes[] = "✓ Payroll periods exist";
}

// Check 2: Are position deduction rates configured?
$stmt = $db->query("SELECT COUNT(*) FROM pr_position_deduction_rates WHERE is_active = 1");
$rateCount = $stmt->fetchColumn();
if ($rateCount == 0) {
    $issues[] = "❌ No position deduction rates configured - Run: <code>php setup_config.php</code>";
} else {
    $fixes[] = "✓ Position deduction rates configured ($rateCount)";
}

// Check 3: Check employee details
$stmt = $db->query("SELECT COUNT(*) FROM employees WHERE employment_status = 'Active'");
$activeEmployees = $stmt->fetchColumn();

$stmt = $db->query("SELECT COUNT(*) FROM pr_employee_details");
$configuredEmployees = $stmt->fetchColumn();

if ($configuredEmployees < $activeEmployees) {
    $issues[] = "⚠️ Only $configuredEmployees/$activeEmployees employees have payroll configuration";
    $issues[] = "Clear employees need configuration via setup_config.php";
} else {
    $fixes[] = "✓ All $activeEmployees employees configured";
}

// Check 4: Check benefits
$stmt = $db->query("SELECT COUNT(*) FROM pr_employee_benefits");
$benefitsCount = $stmt->fetchColumn();
if ($benefitsCount == 0) {
    $issues[] = "⚠️ No employee benefits configured";
} else {
    $fixes[] = "✓ Employee benefits configured ($benefitsCount)";
}

// Check 5: Test a sample period
if (!empty($periods)) {
    $periodId = $periods[0]['period_id'];
    $preview = $payrollModel->getPayrollPreview($periodId);
    
    if (empty($preview)) {
        $issues[] = "⚠️ No payroll data returned for test period - Likely configuration issue";
    } else {
        $fixes[] = "✓ Payroll calculation working (" . count($preview) . " employees)";
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Payroll System Diagnostics</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">
    <style>
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        .issue { color: #dc3545; }
        .fix { color: #28a745; }
        .warning { color: #ffc107; }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1 class="m-0"><i class="fas fa-stethoscope"></i> Payroll System Diagnostics</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">System Status</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($issues)): ?>
                                        <h5 class="issue"><i class="fas fa-exclamation-circle"></i> Issues Found:</h5>
                                        <ul>
                                            <?php foreach ($issues as $issue): ?>
                                                <li class="issue"><?= htmlspecialchars($issue) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>

                                    <?php if (!empty($fixes)): ?>
                                        <h5 class="fix"><i class="fas fa-check-circle"></i> ✓ OK:</h5>
                                        <ul>
                                            <?php foreach ($fixes as $fix): ?>
                                                <li class="fix"><?= htmlspecialchars($fix) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if (!empty($issues)): ?>
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">Quick Fix</h3>
                                    </div>
                                    <div class="card-body">
                                        <p><a href="setup_config.php" class="btn btn-success">
                                            <i class="fas fa-cog"></i> Run Payroll Setup Now
                                        </a></p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="card card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">System Ready</h3>
                                    </div>
                                    <div class="card-body">
                                        <p>All configurations look good! <a href="payrollProcess.php" class="btn btn-primary">
                                            <i class="fas fa-arrow-right"></i> Go to Payroll Processing
                                        </a></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
