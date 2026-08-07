<?php
session_start();
require_once __DIR__ . '/../../auth/auth.php';
require_once __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../controllers/payrollClearanceController.php';

$theme = $_SESSION['user']['theme'] ?? 'light';
$controller = new PayrollClearanceController();
$message = null;
$messageType = 'info';
$selectedSettlement = null;
$settlementPreview = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = $_SESSION['user']['id'] ?? 0;

    if ($action === 'calculate_settlement' && !empty($_POST['settlement_id'])) {
        $settlementId = (int)$_POST['settlement_id'];
        $employeeId = (int)($_POST['employee_id'] ?? 0);
        $selectedSettlement = $controller->getSettlementDetails($settlementId);
        $settlementPreview = $controller->calculateSettlementPreview($settlementId, $employeeId);

        if (!empty($selectedSettlement)) {
            $message = 'Settlement preview loaded for ' . htmlspecialchars($selectedSettlement['full_name'] ?? 'the selected employee') . '.';
            $messageType = 'info';
        }
    }

    if ($action === 'request_clearance' && !empty($_POST['settlement_id'])) {
        $result = $controller->createClearanceRequest((int)$_POST['settlement_id'], $userId);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }

    if ($action === 'approve_clearance' && !empty($_POST['clearance_id'])) {
        $clearanceId = (int)$_POST['clearance_id'];
        $result = $controller->approveClearance($clearanceId, $userId, $_POST['comments'] ?? null);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';

        if ($result['success']) {
            $clearance = $controller->getClearanceDetails($clearanceId);
            if ($clearance) {
                $selectedSettlement = $controller->getSettlementDetails((int)$clearance['settlement_id']);
            }
        }
    }

    if ($action === 'reject_clearance' && !empty($_POST['clearance_id'])) {
        $clearanceId = (int)$_POST['clearance_id'];
        $result = $controller->rejectClearance($clearanceId, $userId, $_POST['comments'] ?? null);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';

        if ($result['success']) {
            $clearance = $controller->getClearanceDetails($clearanceId);
            if ($clearance) {
                $selectedSettlement = $controller->getSettlementDetails((int)$clearance['settlement_id']);
            }
        }
    }
}

$pendingRequests = $controller->getPendingClearances();
$allRequests = $controller->getAllClearances();
$eligibleSettlements = $controller->getEligibleSettlements();

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Final Settlements</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
    <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
    <link rel="stylesheet" href="../custom.css" />
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
    <div class="wrapper">
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__wobble" src="../../assets/pics/bcpLogo.png" alt="AdminLTELogo" height="60" width="60" />
        </div>

        <nav class="main-header navbar navbar-expand navbar-dark">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="../payroll.php" class="nav-link">Home</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <div class="nav-link" id="clock">--:--:--</div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button"><i class="fas fa-expand-arrows-alt"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="darkToggle" role="button" title="Toggle Dark Mode">
                        <i class="fas fa-moon" id="themeIcon"></i>
                    </a>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="../payroll.php" class="brand-link">
                <img src="../../assets/pics/bcpLogo.png" alt="AdminLTE Logo" class="brand-image elevation-3" style="opacity: 0.9" />
                <span class="brand-text font-weight-light">BCP Bulacan</span>
            </a>
            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image"></div>
                    <div class="info">
                        <a href="#" onclick="openGlobalModal('Profile Settings ','../../user_profile/profile_form.php')" class="d-block">
                            Admin <?= htmlspecialchars($_SESSION['user']['name']) ?>
                        </a>
                    </div>
                </div>
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="../payroll.php" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="periodManager.php" class="nav-link">
                                <i class="nav-icon fas fa-calendar-alt"></i>
                                <p>Payroll Periods</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="payrollProcess.php" class="nav-link">
                                <i class="nav-icon fas fa-calculator"></i>
                                <p>Payroll Processing</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="payslip.php" class="nav-link">
                                <i class="nav-icon fas fa-receipt"></i>
                                <p>Payslips</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="allowance.php" class="nav-link">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>Deductions</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="reports.php" class="nav-link">
                                <i class="nav-icon fas fa-balance-scale"></i>
                                <p>Reports</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="payrollClearance.php" class="nav-link active">
                                <i class="nav-icon fas fa-file-signature"></i>
                                <p>Final Settlements</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../../logout.php" class="nav-link">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Final Settlements</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <?php if ($message): ?>
                        <div class="alert alert-<?= htmlspecialchars($messageType) ?> alert-dismissible no-print">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-dark">
                                <div class="card-header">
                                    <h3 class="card-title">Settlements Waiting for Payroll Review</h3>
                                </div>
                                <div class="card-body">
                                    <p>This queue shows exit-management requests that are already sent to payroll for review. Payroll can preview the final payout, approve it, or reject it, and the decision is sent back to Exit Management as the official settlement result.</p>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Employee</th>
                                                    <th>Settlement Date</th>
                                                    <th>Requested Amount</th>
                                                    <th>Current Status</th>
                                                    <th>Last Working Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($eligibleSettlements)): ?>
                                                    <tr>
                                                        <td colspan="7" class="text-center">No eligible settlements available.</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($eligibleSettlements as $index => $settlement): ?>
                                                        <tr>
                                                            <td><?= $index + 1 ?></td>
                                                            <td><?= htmlspecialchars($settlement['full_name']) ?></td>
                                                            <td><?= htmlspecialchars($settlement['settlement_date']) ?></td>
                                                            <td>₱<?= number_format((float)($settlement['payroll_final_amount'] ?? $settlement['net_payable']), 2) ?></td>
                                                            <td><?= htmlspecialchars(ucfirst($settlement['payroll_clearance_status'] ?? 'pending')) ?></td>
                                                            <td><?= htmlspecialchars($settlement['last_working_date']) ?></td>
                                                            <td class="no-print">
                                                                <form method="post" style="display:inline-block;">
                                                                    <input type="hidden" name="action" value="calculate_settlement" />
                                                                    <input type="hidden" name="settlement_id" value="<?= (int)$settlement['settlement_id'] ?>" />
                                                                    <input type="hidden" name="employee_id" value="<?= (int)$settlement['employee_id'] ?>" />
                                                                    <button type="submit" class="btn btn-info btn-sm">Review Request</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($selectedSettlement) || !empty($settlementPreview)): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Payroll Final Settlement Preview</h3>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($selectedSettlement)): ?>
                                            <div class="row mb-3">
                                                <div class="col-md-3"><strong>Employee:</strong><br><?= htmlspecialchars($selectedSettlement['full_name'] ?? '-') ?></div>
                                                <div class="col-md-3"><strong>Position:</strong><br><?= htmlspecialchars($selectedSettlement['position'] ?? '-') ?></div>
                                                <div class="col-md-3"><strong>Last Working Date:</strong><br><?= htmlspecialchars($selectedSettlement['last_working_date'] ?? '-') ?></div>
                                                <div class="col-md-3"><strong>Settlement Date:</strong><br><?= htmlspecialchars($selectedSettlement['settlement_date'] ?? '-') ?></div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-3"><strong>Exit Status:</strong><br><?= htmlspecialchars(ucfirst($selectedSettlement['status'] ?? 'draft')) ?></div>
                                                <div class="col-md-3"><strong>Clearance Status:</strong><br><?= htmlspecialchars(ucfirst($selectedSettlement['payroll_clearance_status'] ?? 'not requested')) ?></div>
                                                <div class="col-md-3"><strong>Payroll Final Amount:</strong><br>₱<?= number_format((float)($selectedSettlement['payroll_final_amount'] ?? 0), 2) ?></div>
                                                <div class="col-md-3"><strong>Notes:</strong><br><?= nl2br(htmlspecialchars($selectedSettlement['payroll_notes'] ?? '-')) ?></div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-3"><strong>Calculation Layer:</strong><br><?= htmlspecialchars($settlementPreview['calculation_layer'] ?? 'legacy-payroll') ?></div>
                                                <div class="col-md-3"><strong>Salary Source:</strong><br><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $settlementPreview['salary_source'] ?? 'none'))) ?></div>
                                                <div class="col-md-3"><strong>Days Worked:</strong><br><?= (int)($settlementPreview['days_worked'] ?? 0) ?></div>
                                                <div class="col-md-3"><strong>Base Salary:</strong><br>₱<?= number_format((float)($settlementPreview['base_salary_monthly'] ?? 0), 2) ?></div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($selectedSettlement['adjustments'])): ?>
                                            <div class="alert alert-info">
                                                <strong>Manual Adjustments from Exit Management</strong>
                                                <ul class="mb-0 mt-2">
                                                    <?php foreach ($selectedSettlement['adjustments'] as $adjustment): ?>
                                                        <li>
                                                            <?= htmlspecialchars($adjustment['description'] ?? '-') ?>
                                                            — <?= ucfirst($adjustment['adjustment_type'] ?? 'credit') ?>
                                                            — ₱<?= number_format((float)($adjustment['amount'] ?? 0), 2) ?>
                                                            <?php if (!empty($adjustment['notes'])): ?>
                                                                <span class="text-muted">(<?= htmlspecialchars($adjustment['notes']) ?>)</span>
                                                            <?php endif; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
 
                                        <?php if (!empty($settlementPreview['earnings']) || !empty($settlementPreview['deductions'])): ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Type</th>
                                                            <th>Description</th>
                                                            <th class="text-right">Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach (($settlementPreview['earnings'] ?? []) as $earning): ?>
                                                            <tr class="table-success">
                                                                <td>Earning</td>
                                                                <td><?= htmlspecialchars($earning['description'] ?? '-') ?></td>
                                                                <td class="text-right">₱<?= number_format((float)($earning['amount'] ?? 0), 2) ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                        <?php foreach (($settlementPreview['deductions'] ?? []) as $deduction): ?>
                                                            <tr class="table-danger">
                                                                <td>Deduction</td>
                                                                <td><?= htmlspecialchars($deduction['description'] ?? '-') ?></td>
                                                                <td class="text-right">₱<?= number_format((float)($deduction['amount'] ?? 0), 2) ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="alert alert-success mb-0">
                                                <strong>Gross Pay:</strong> ₱<?= number_format((float)($settlementPreview['gross_pay'] ?? 0), 2) ?> &nbsp;&nbsp;
                                                <strong>Total Deductions:</strong> ₱<?= number_format((float)($settlementPreview['total_deductions'] ?? 0), 2) ?> &nbsp;&nbsp;
                                                <strong>Net Final Settlement:</strong> ₱<?= number_format((float)($settlementPreview['net_pay'] ?? 0), 2) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning mb-0">No settlement preview available for the selected employee.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-dark">
                                <div class="card-header">
                                    <h3 class="card-title">Pending Payroll Review Decisions</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Employee</th>
                                                    <th>Settlement Date</th>
                                                    <th>Net Payable</th>
                                                    <th>Status</th>
                                                    <th class="no-print">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($pendingRequests)): ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center">No pending clearance requests.</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($pendingRequests as $index => $request): ?>
                                                        <tr>
                                                            <td><?= $index + 1 ?></td>
                                                            <td><?= htmlspecialchars($request['full_name']) ?></td>
                                                            <td><?= htmlspecialchars($request['settlement_date']) ?></td>
                                                            <td>₱<?= number_format((float)$request['net_payable'], 2) ?></td>
                                                            <td><?= htmlspecialchars(ucfirst($request['status'])) ?></td>
                                                            <td class="no-print">
                                                                <a href="printPayrollClearance.php?clearance_id=<?= (int)$request['id'] ?>" target="_blank" class="btn btn-secondary btn-sm">Print Form</a>
                                                                <form method="post" style="display:inline-block; margin-left:0.25rem;">
                                                                    <input type="hidden" name="action" value="approve_clearance" />
                                                                    <input type="hidden" name="clearance_id" value="<?= (int)$request['id'] ?>" />
                                                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                                                </form>
                                                                <form method="post" style="display:inline-block; margin-left:0.25rem;">
                                                                    <input type="hidden" name="action" value="reject_clearance" />
                                                                    <input type="hidden" name="clearance_id" value="<?= (int)$request['id'] ?>" />
                                                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-dark">
                                <div class="card-header">
                                    <h3 class="card-title">All Final Settlement Requests</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Employee</th>
                                                    <th>Request Date</th>
                                                    <th>Settlement</th>
                                                    <th>Status</th>
                                                    <th>Approved By</th>
                                                    <th class="no-print">Print</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($allRequests)): ?>
                                                    <tr>
                                                        <td colspan="7" class="text-center">No clearance requests found.</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($allRequests as $index => $request): ?>
                                                        <tr>
                                                            <td><?= $index + 1 ?></td>
                                                            <td><?= htmlspecialchars($request['full_name']) ?></td>
                                                            <td><?= htmlspecialchars($request['requested_at']) ?></td>
                                                            <td>₱<?= number_format((float)$request['net_payable'], 2) ?></td>
                                                            <td><?= htmlspecialchars(ucfirst($request['status'])) ?></td>
                                                            <td><?= htmlspecialchars($request['approved_by'] ?? '-') ?></td>
                                                            <td class="no-print">
                                                                <a href="printPayrollClearance.php?clearance_id=<?= (int)$request['id'] ?>" target="_blank" class="btn btn-secondary btn-sm">Print</a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include "../../layout/global_modal.php"; ?>
    </div>

    <script src="../../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/dist/js/adminlte.js"></script>
    <script>
        // Initialize all AdminLTE widgets and features
        $(document).ready(function() {
            console.log("[AdminLTE Init] Starting AdminLTE initialization...");

            // Check if AdminLTE loaded properly
            if (typeof window.adminlte === 'undefined') {
                console.error("[AdminLTE Init] ERROR: AdminLTE not loaded!");
            } else {
                console.log("[AdminLTE Init] AdminLTE loaded successfully");

                // Initialize Layout component
                if (window.adminlte.Layout && $.fn.Layout) {
                    $('body').Layout();
                    console.log("[AdminLTE Init] Layout component initialized via jQuery");
                } else if (window.adminlte.Layout) {
                    // Fallback: manually initialize
                    new window.adminlte.Layout(document.body);
                    console.log("[AdminLTE Init] Layout component initialized manually");
                }

                // Initialize PushMenu component
                if ($.fn.PushMenu) {
                    $('[data-widget="pushmenu"]').PushMenu();
                    console.log("[AdminLTE Init] PushMenu component initialized via jQuery");
                } else if (window.adminlte.PushMenu) {
                    // Fallback: manually initialize
                    $('[data-widget="pushmenu"]').each(function() {
                        new window.adminlte.PushMenu(this);
                    });
                    console.log("[AdminLTE Init] PushMenu component initialized manually");
                }

                // Initialize Fullscreen component
                if ($.fn.Fullscreen) {
                    $('[data-widget="fullscreen"]').Fullscreen();
                    console.log("[AdminLTE Init] Fullscreen component initialized via jQuery");
                } else if (window.adminlte.Fullscreen) {
                    // Fallback: manually initialize
                    $('[data-widget="fullscreen"]').each(function() {
                        new window.adminlte.Fullscreen(this);
                    });
                    console.log("[AdminLTE Init] Fullscreen component initialized manually");
                }

                // Initialize Treeview component
                if ($.fn.Treeview) {
                    $('[data-widget="treeview"]').Treeview();
                    console.log("[AdminLTE Init] Treeview component initialized via jQuery");
                } else if (window.adminlte.Treeview) {
                    // Fallback: manually initialize
                    $('[data-widget="treeview"]').each(function() {
                        new window.adminlte.Treeview(this);
                    });
                    console.log("[AdminLTE Init] Treeview component initialized manually");

                    // Initialize fullscreen widget with better event handling
                    var fullscreenBtn = $('[data-widget="fullscreen"]');
                    console.log("[Fullscreen] Button elements found:", fullscreenBtn.length);

                    fullscreenBtn.on('click', function(e) {
                        console.log("[Fullscreen] Button clicked!");
                        e.preventDefault();
                        e.stopPropagation();

                        const isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement);
                        console.log("[Fullscreen] Current state - fullscreen:", isFullscreen);

                        if (isFullscreen) {
                            // Exit fullscreen
                            if (document.exitFullscreen) {
                                document.exitFullscreen().catch(err => console.error("[Fullscreen] Exit error:", err));
                            } else if (document.webkitExitFullscreen) {
                                document.webkitExitFullscreen();
                            }
                            console.log("[Fullscreen] Exiting fullscreen");
                        } else {
                            // Enter fullscreen
                            const elem = document.documentElement;
                            if (elem.requestFullscreen) {
                                elem.requestFullscreen().catch(err => console.error("[Fullscreen] Request error:", err));
                            } else if (elem.webkitRequestFullscreen) {
                                elem.webkitRequestFullscreen();
                            }
                            console.log("[Fullscreen] Requesting fullscreen");
                        }
                    });

                    // Ensure fullscreen button is interactive
                    fullscreenBtn.css({
                        'cursor': 'pointer',
                        'pointer-events': 'auto'
                    });
                };
            }
        });
    </script>
    <script src="../custom.js"></script>
    <script src="../../assets/dist/js/time.js"></script>
    <script src="../../assets/dist/js/global_modal.js"></script>
    <script src="../../assets/dist/js/profile.js"></script>
</body>

</html>