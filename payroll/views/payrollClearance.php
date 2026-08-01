<?php
session_start();
require_once __DIR__ . '/../../auth/auth.php';
require_once __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../controllers/payrollClearanceController.php';

$theme = $_SESSION['user']['theme'] ?? 'light';
$controller = new PayrollClearanceController();
$message = null;
$messageType = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = $_SESSION['user']['id'] ?? 0;

    if ($action === 'request_clearance' && !empty($_POST['settlement_id'])) {
        $result = $controller->createClearanceRequest((int)$_POST['settlement_id'], $userId);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }

    if ($action === 'approve_clearance' && !empty($_POST['clearance_id'])) {
        $result = $controller->approveClearance(
            (int)$_POST['clearance_id'],
            $userId,
            $_POST['comments'] ?? null
        );
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }

    if ($action === 'reject_clearance' && !empty($_POST['clearance_id'])) {
        $result = $controller->rejectClearance(
            (int)$_POST['clearance_id'],
            $userId,
            $_POST['comments'] ?? null
        );
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
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
    <title>Payroll Clearance Requests</title>

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

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
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
                            <a href="salaryOverview.php" class="nav-link">
                                <i class="nav-icon fas fa-money-check-alt"></i>
                                <p>Salary Overview</p>
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
                                <p>Benefits & Deductions</p>
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
                                <p>Payroll Clearance</p>
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
                            <h1 class="m-0">Payroll Clearance Requests</h1>
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
                                    <h3 class="card-title">Approved Settlements Needing Payroll Clearance</h3>
                                </div>
                                <div class="card-body">
                                    <p>This list shows approved exit settlements which can be requested for payroll clearance.</p>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Employee</th>
                                                    <th>Settlement Date</th>
                                                    <th>Net Payable</th>
                                                    <th>Last Working Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($eligibleSettlements)): ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center">No eligible settlements available.</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($eligibleSettlements as $index => $settlement): ?>
                                                        <tr>
                                                            <td><?= $index + 1 ?></td>
                                                            <td><?= htmlspecialchars($settlement['full_name']) ?></td>
                                                            <td><?= htmlspecialchars($settlement['settlement_date']) ?></td>
                                                            <td>₱<?= number_format((float)$settlement['net_payable'], 2) ?></td>
                                                            <td><?= htmlspecialchars($settlement['last_working_date']) ?></td>
                                                            <td class="no-print">
                                                                <form method="post" style="display:inline-block;">
                                                                    <input type="hidden" name="action" value="request_clearance" />
                                                                    <input type="hidden" name="settlement_id" value="<?= (int)$settlement['settlement_id'] ?>" />
                                                                    <button type="submit" class="btn btn-primary btn-sm">Request Clearance</button>
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
                                    <h3 class="card-title">Pending Payroll Clearance Requests</h3>
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
                                    <h3 class="card-title">All Payroll Clearance Requests</h3>
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
    <script src="../../assets/dist/js/adminlte.min.js"></script>
    <script src="../custom.js"></script>
    <script src="../../assets/dist/js/theme.js"></script>
    <script src="../../assets/dist/js/time.js"></script>
    <script src="../../assets/dist/js/global_modal.js"></script>
    <script src="../../assets/dist/js/profile.js"></script>
</body>

</html>