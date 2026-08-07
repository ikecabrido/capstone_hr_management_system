<?php
session_start();
require_once __DIR__ . '/../../auth/auth.php';
require_once __DIR__ . '/../controllers/payrollController.php';
require_once __DIR__ . '/../../auth/auth_check.php';
$theme = $_SESSION['user']['theme'] ?? 'light';

ini_set('display_errors', 1);
error_reporting(E_ALL);



$user = $auth->user();
$controller = new PayrollController();

// Get periods
$periods = $controller->getPeriods();

// Get all active employees for search functionality
$allEmployees = $controller->getEmployees();

// Get selected period
$selectedPeriodId = $_POST['period_id'] ?? '';

// Initialize toast variables
$toastMessage = null;
$toastType = 'info';

// Preview + calculation
$previewData = [];
$payrollResults = [];

if ($selectedPeriodId !== '') {
    $selectedPeriodId = (int)$selectedPeriodId;

    // Preview payroll
    $previewData = $controller->previewPayroll($selectedPeriodId);

    // Try calculating payroll
    try {
        $payrollResults = $controller->calculate($selectedPeriodId);
        $toastMessage = "Payroll calculated successfully.";
        $toastType = 'success';
    } catch (Exception $e) {
        $toastMessage = $e->getMessage();
        $toastType = 'danger';
        $payrollResults = [];
    }
}

// Handle finalize success toast
if (isset($_GET['finalized']) && $_GET['finalized'] == 1) {
    $toastMessage = "Payroll finalized successfully.";
    $toastType = 'success';
}

// Handle finalize error toast
if (isset($_GET['error'])) {
    $toastMessage = urldecode($_GET['error']);
    $toastType = 'danger';
}
?>



<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Payroll management system - Payroll Processing</title>

    <!-- Google Font: Source Sans Pro -->
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <!-- Font Awesome Icons -->
    <link
        rel="stylesheet"
        href="../../assets/plugins/fontawesome-free/css/all.min.css" />
    <!-- overlayScrollbars -->
    <link
        rel="stylesheet"
        href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
    <!-- Theme style -->
    <!-- <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css" /> -->
    <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../custom.css" />
    <link rel="stylesheet" href="../../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
</head>

<body
    class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
    <div class="wrapper">
        <!-- Preloader -->
        <div
            class="preloader flex-column justify-content-center align-items-center">
            <img
                class="animation__wobble"
                src="../../assets/pics/bcpLogo.png"
                alt="AdminLTELogo"
                height="60"
                width="60" />
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="../payroll.php" class="nav-link">Home</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Navbar Search -->
                <li class="nav-item">
                    <div class="nav-link" id="clock">--:--:--</div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="#"
                        id="darkToggle"
                        role="button"
                        title="Toggle Dark Mode">
                        <i class="fas fa-moon" id="themeIcon"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="../payroll.php" class="brand-link">
                <img
                    src="../../assets/pics/bcpLogo.png"
                    alt="AdminLTE Logo"
                    class="brand-image elevation-3"
                    style="opacity: 0.9" />
                <span class="brand-text font-weight-light">BCP Bulacan </span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                    </div>
                    <div class="info">
                        <a href="#" onclick="openGlobalModal('Profile Settings ','../../user_profile/profile_form.php')" class="d-block">
                            Admin <?= htmlspecialchars($_SESSION['user']['name']) ?>
                        </a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul
                        class="nav nav-pills nav-sidebar flex-column"
                        data-widget="treeview"
                        role="menu"
                        data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                        <li class="nav-item">
                            <a href="../payroll.php" class="nav-link ">
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
                            <a href="payrollProcess.php" class="nav-link active">
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
                                <p>
                                    Reports
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="payrollClearance.php" class="nav-link">
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
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Payroll Processing</h1>
                        </div>
                        <!-- /.col -->

                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Period Selection (Full Width) -->
                    <form method="POST" class="mb-4">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Payroll Period</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label><strong>Select Period</strong></label>
                                        <select name="period_id" class="form-control" required onchange="this.form.submit();">
                                            <option value="">-- Select Payroll Period --</option>
                                            <?php foreach ($periods as $p): ?>
                                                <option value="<?= $p['period_id'] ?>"
                                                    <?= ($selectedPeriodId !== '' && (int)$selectedPeriodId === $p['period_id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($p['period_name']) ?>
                                                    (<?= ucfirst($p['status']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <!-- Left Sidebar: Employee Search & Info -->
                        <div class="col-md-3">
                            <!-- Search Card -->
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-search"></i> Find Employee
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label><strong>Employee Name or ID</strong></label>
                                        <div class="input-group">
                                            <input
                                                type="text"
                                                id="employeeSearchInput"
                                                class="form-control"
                                                placeholder="Start typing to search employees..."
                                                autocomplete="off">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button" id="searchBtn" onclick="clearSearch()">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted d-block mt-2">
                                            Search by name, ID, position, or department. Select an employee to view their payroll details.
                                        </small>
                                    </div>

                                    <!-- Search Results Dropdown -->
                                    <div id="employeeSearchResults" class="bg-light border rounded mt-2" style="display: none; max-height: 300px; overflow-y: auto;">
                                        <div id="employeeResultsList" class="list-group list-group-flush"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Selected Employee Info Card (Shown after selection) -->
                            <div id="selectedEmployeeCard" class="card card-success" style="display: none;">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-user"></i> Selected Employee
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group mb-3">
                                        <label class="text-muted small">Employee Name</label>
                                        <p class="m-0 font-weight-bold" id="selectedEmployeeName" style="font-size: 16px;">-</p>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="text-muted small">Employee ID</label>
                                        <p class="m-0" id="selectedEmployeeID">-</p>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="text-muted small">Position</label>
                                        <p class="m-0" id="selectedEmployeePosition">-</p>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="text-muted small">Department</label>
                                        <p class="m-0" id="selectedEmployeeDepartment">-</p>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="text-muted small">Position Type</label>
                                        <p class="m-0"><span id="selectedEmployeeType" class="badge badge-info">-</span></p>
                                    </div>
                                </div>
                            </div>

                            <?php if (empty($previewData)): ?>
                                <div class="alert alert-warning mt-3 mb-0">
                                    <small><i class="fas fa-info-circle"></i> No employees available. Select a payroll period first.</small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Right Side: Salary Calculation Details -->
                        <div class="col-md-9">
                            <!-- Salary Details Container (Shown when employee is selected) -->
                            <div id="salaryDetailsContainer" style="display: none;">
                                <!-- Period Info -->
                                <div class="card card-secondary mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Period Details</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 text-center">
                                                <small class="text-muted">Period Start</small>
                                                <p class="m-0"><strong id="periodStart">-</strong></p>
                                            </div>
                                            <div class="col-md-4 text-center">
                                                <small class="text-muted">Period End</small>
                                                <p class="m-0"><strong id="periodEnd">-</strong></p>
                                            </div>
                                            <div class="col-md-4 text-center">
                                                <small class="text-muted">Pay Date</small>
                                                <p class="m-0"><strong id="payDate">-</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hours & Days Worked -->
                                <div class="card card-warning mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-clock"></i> Hours & Attendance</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="text-muted">Hours Worked</label>
                                                    <h5 id="hoursWorked" class="m-0"><strong>0</strong></h5>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="text-muted">Days Worked</label>
                                                    <h5 id="daysWorked" class="m-0"><strong>0</strong></h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="text-muted">Overtime Pay</label>
                                                    <h5 id="overtimePay" class="m-0"><strong>₱0.00</strong></h5>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="text-muted">Overtime Multiplier</label>
                                                    <h5 id="overtimeMultiplier" class="m-0"><strong>1.25x</strong></h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Earnings Breakdown -->
                                <div class="card card-success mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-plus-circle"></i> Earnings</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-hover table-sm m-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Description</th>
                                                    <th class="text-right" style="width: 150px;">Amount (₱)</th>
                                                </tr>
                                            </thead>
                                            <tbody id="earningsTable">
                                                <tr>
                                                    <td colspan="2" class="text-muted text-center py-3">-</td>
                                                </tr>
                                            </tbody>
                                            <tfoot class="bg-light font-weight-bold">
                                                <tr>
                                                    <td>Total Earnings</td>
                                                    <td class="text-right"><strong id="totalEarnings">₱0.00</strong></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <!-- Deductions Breakdown -->
                                <div class="card card-danger mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-minus-circle"></i> Deductions</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-hover table-sm m-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Description</th>
                                                    <th class="text-right" style="width: 150px;">Amount (₱)</th>
                                                </tr>
                                            </thead>
                                            <tbody id="deductionsTable">
                                                <tr>
                                                    <td colspan="2" class="text-muted text-center py-3">No Deductions</td>
                                                </tr>
                                            </tbody>
                                            <tfoot class="bg-light font-weight-bold">
                                                <tr>
                                                    <td>Total Deductions</td>
                                                    <td class="text-right"><strong id="totalDeductions">₱0.00</strong></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <!-- Net Pay Summary -->
                                <div class="card card-primary">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="text-muted">Gross Pay</label>
                                                    <h4 id="grossPay" class="m-0">₱0.00</h4>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="text-muted">Net Pay</label>
                                                    <h4 id="netPay" class="m-0 text-success">₱0.00</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-4 mb-3">
                                    <button type="button"
                                        class="btn btn-success"
                                        data-toggle="modal"
                                        data-target="#finalizeModal"
                                        <?= empty($payrollResults) ? 'disabled' : '' ?>>
                                        <i class="fas fa-check"></i> Process Payroll
                                    </button>
                                </div>
                            </div>

                            <!-- Initial Message -->
                            <div id="initialMessage" class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Select an employee from the search bar to view salary details.
                            </div>
                        </div>
                    </div>
                </div>
                <!--/. container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
    </div>
    <!-- Edit Payroll Modal -->
    <div class="modal fade" id="editPayrollModal" tabindex="-1" aria-labelledby="editPayrollLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="editPayrollForm" method="POST" action="../public/editPayroll.php">
                <input type="hidden" name="employee_id" id="modalEmployeeId">
                <input type="hidden" name="period_id" value="<?= $selectedPeriodId ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPayrollLabel">Edit Payroll for <span id="modalEmployeeName"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Gross Pay</label>
                            <input type="number" step="0.01" class="form-control" name="gross_pay" id="modalGross" required>
                        </div>
                        <div class="form-group">
                            <label>Total Deductions</label>
                            <input type="number" step="0.01" class="form-control" name="total_deductions" id="modalDeductions" required>
                        </div>
                        <div class="form-group">
                            <label>Net Pay</label>
                            <input type="number" step="0.01" class="form-control" name="net_pay" id="modalNet" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Finalize Payroll Modal -->
    <div class="modal fade" id="finalizeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">
                        Confirm Payroll Finalization
                    </h5>
                    <button type="button"
                        class="close"
                        data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-circle
           fa-3x text-warning mb-3"></i>
                    <p class="mt-3">
                        Are you sure you want to finalize this payroll?
                    </p>
                    <p class="text-muted">
                        You will no longer be able to edit it.
                    </p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                        Cancel
                    </button>
                    <form method="POST"
                        action="finalizePayroll.php">
                        <input type="hidden"
                            name="period_id"
                            value="<?= $selectedPeriodId ?>">
                        <button type="submit"
                            class="btn btn-success">
                            Yes, Finalize
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Toast Container -->
    <div class="position-fixed top-0 right-0 p-3" style="z-index: 9999; right: 0; top: 0;">
        <div id="adminlteToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000">
            <div class="toast-header bg-<?= $toastType ?? 'info' ?> text-white">
                <strong class="mr-auto">Notification</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body bg-<?= $toastType ?? 'info' ?> text-white">
                <?= htmlspecialchars($toastMessage ?? '') ?>
            </div>
        </div>
    </div>


    <!-- ./wrapper -->
    <?php include "../../layout/global_modal.php"; ?>

    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="../../assets/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../../assets/dist/js/adminlte.min.js"></script>

    <!-- PAGE PLUGINS -->
    <!-- jQuery Mapael -->
    <script src="../../assets/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
    <script src="../../assets/plugins/raphael/raphael.min.js"></script>
    <script src="../../assets/plugins/jquery-mapael/jquery.mapael.min.js"></script>
    <script src="../../assets/plugins/jquery-mapael/maps/usa_states.min.js"></script>
    <!-- ChartJS -->
    <script src="../../assets/plugins/chart.js/Chart.min.js"></script>

    <!-- AdminLTE for demo purposes -->
    <!-- <script src="assets/dist/js/demo.js"></script> -->
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <!-- <script src="assets/dist/js/pages/dashboard2.js"></script> -->
    <script src="../custom.js"></script>
    <script src="../../assets/dist/js/time.js"></script>
    <script src="../../assets/dist/js/global_modal.js"></script>
    <script src="../../assets/dist/js/profile.js"></script>
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
                }

                console.log("[AdminLTE Init] AdminLTE initialization completed");
            }

            <?php if ($toastMessage): ?>
                $('#adminlteToast').toast('show');
            <?php endif; ?>
        });
    </script>
    <script>
        // Store payroll data for JavaScript access
        const payrollData = <?php echo json_encode($previewData); ?>;
        const allEmployees = <?php echo json_encode($allEmployees); ?>;
        const periodData = <?php echo json_encode($periods); ?>;
        const selectedPeriodId = <?php echo $selectedPeriodId ?? 'null'; ?>;
        let currentSelectedIndex = -1;

        // Debug logging
        console.log('Payroll Data Count:', payrollData ? payrollData.length : 0);
        console.log('All Employees Count:', allEmployees ? allEmployees.length : 0);
        console.log('Selected Period ID:', selectedPeriodId);
        if (payrollData && payrollData.length > 0) {
            console.log('First Employee Data:', payrollData[0]);
        }

        // Clear Search Function
        function clearSearch() {
            const searchInput = document.getElementById('employeeSearchInput');
            const searchResults = document.getElementById('employeeSearchResults');
            const selectedCard = document.getElementById('selectedEmployeeCard');
            const salaryContainer = document.getElementById('salaryDetailsContainer');
            const initialMessage = document.getElementById('initialMessage');

            searchInput.value = '';
            searchResults.style.display = 'none';
            selectedCard.style.display = 'none';
            salaryContainer.style.display = 'none';
            initialMessage.style.display = 'block';
            currentSelectedIndex = -1;
        }

        // Show All Employees (limited for performance)
        function showAllEmployees() {
            if (!allEmployees || allEmployees.length === 0) {
                document.getElementById('employeeSearchResults').style.display = 'none';
                return;
            }

            const resultsList = document.getElementById('employeeResultsList');
            const displayEmployees = allEmployees.slice(0, 10); // Show first 10

            resultsList.innerHTML = displayEmployees.map((emp, idx) => `
                <button type="button"
                    class="list-group-item list-group-item-action text-left search-result-item"
                    data-employee-id="${emp.id}"
                    data-employee-name="${emp.name}"
                    onclick="selectEmployee('${emp.id}', '${emp.name.replace(/'/g, "\\'")}')">
                    <div class="d-flex justify-content-between">
                        <strong>${emp.name}</strong>
                        <small class="text-muted">ID: ${emp.id}</small>
                    </div>
                </button>
            `).join('');

            if (allEmployees.length > 10) {
                resultsList.innerHTML += '<div class="p-2 text-center text-muted"><small>Showing first 10 employees. Type to search.</small></div>';
            }

            document.getElementById('employeeSearchResults').style.display = 'block';
        }

        // Display Search Results
        function displaySearchResults(filtered) {
            const resultsList = document.getElementById('employeeResultsList');
            const searchResults = document.getElementById('employeeSearchResults');

            if (filtered.length > 0) {
                resultsList.innerHTML = filtered.slice(0, 10).map((emp) => `
                    <button type="button"
                        class="list-group-item list-group-item-action text-left search-result-item"
                        data-employee-id="${emp.id}"
                        data-employee-name="${emp.name}"
                        onclick="selectEmployee('${emp.id}', '${emp.name.replace(/'/g, "\\'")}')">
                        <div class="d-flex justify-content-between">
                            <strong>${emp.name}</strong>
                            <small class="text-muted">ID: ${emp.id}</small>
                        </div>
                    </button>
                `).join('');

                if (filtered.length > 10) {
                    resultsList.innerHTML += '<div class="p-2 text-center text-muted"><small>Showing first 10 matches</small></div>';
                }
            } else {
                resultsList.innerHTML = '<div class="p-3 text-center text-muted"><small><i class="fas fa-search"></i> No employees found</small></div>';
            }

            searchResults.style.display = 'block';
        }

        // Employee Search Functionality with Autocomplete
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('employeeSearchInput');
            const searchResults = document.getElementById('employeeSearchResults');
            const resultsList = document.getElementById('employeeResultsList');
            let selectedIndex = -1;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim().toLowerCase();
                    selectedIndex = -1;

                    if (query.length === 0) {
                        if (allEmployees && allEmployees.length > 0) {
                            showAllEmployees();
                        } else {
                            searchResults.style.display = 'none';
                        }
                        return;
                    }

                    const filtered = allEmployees.filter(emp => {
                        const name = (emp.name || '').toLowerCase();
                        const empId = (emp.id || '').toString().toLowerCase();
                        return name.includes(query) || empId.includes(query);
                    });
                    displaySearchResults(filtered);
                });

                searchInput.addEventListener('keydown', function(e) {
                    const items = resultsList.querySelectorAll('.search-result-item');
                    if (items.length === 0) return;

                    items.forEach(item => item.classList.remove('active'));

                    switch (e.key) {
                        case 'ArrowDown':
                            e.preventDefault();
                            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                            break;
                        case 'ArrowUp':
                            e.preventDefault();
                            selectedIndex = Math.max(selectedIndex - 1, 0);
                            break;
                        case 'Enter':
                            e.preventDefault();
                            if (selectedIndex >= 0 && items[selectedIndex]) {
                                const selectedItem = items[selectedIndex];
                                selectEmployee(selectedItem.getAttribute('data-employee-id'), selectedItem.getAttribute('data-employee-name'));
                            } else if (items[0]) {
                                selectEmployee(items[0].getAttribute('data-employee-id'), items[0].getAttribute('data-employee-name'));
                            }
                            return;
                        case 'Escape':
                            searchResults.style.display = 'none';
                            selectedIndex = -1;
                            return;
                    }

                    if (selectedIndex >= 0 && items[selectedIndex]) {
                        items[selectedIndex].classList.add('active');
                        items[selectedIndex].scrollIntoView({
                            block: 'nearest'
                        });
                    }
                });

                searchInput.addEventListener('focus', function() {
                    if (this.value.trim().length === 0 && allEmployees && allEmployees.length > 0) {
                        showAllEmployees();
                    }
                });

                document.addEventListener('click', function(e) {
                    if (searchInput && !searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.style.display = 'none';
                        selectedIndex = -1;
                    }
                });
            }
        });

        function selectEmployee(employeeId, name) {
            // Find employee in payroll data
            const employee = payrollData ? payrollData.find(emp => emp.employee_id == employeeId) : null;

            if (!employee) {
                // Employee exists but no payroll data - show message
                showErrorMessage(`Employee ${name} exists but has no payroll data for the selected period. Please ensure the employee has proper payroll configuration.`);
                return;
            }

            // Validate employee has calculation data
            if (!employee.gross_pay && employee.gross_pay !== 0) {
                console.error('Employee missing gross_pay:', employee);
                showErrorMessage(`Employee ${name} has incomplete payroll data. Visit diagnostics.php to fix configuration.`);
                return;
            }

            currentSelectedIndex = employeeId;

            // Update search input with selected employee
            const searchInput = document.getElementById('employeeSearchInput');
            if (searchInput) {
                searchInput.value = name;
                document.getElementById('employeeSearchResults').style.display = 'none';
            }

            // Show selected employee card with detailed info
            const selectedCard = document.getElementById('selectedEmployeeCard');
            if (selectedCard) {
                document.getElementById('selectedEmployeeName').textContent = employee.name || '-';
                document.getElementById('selectedEmployeeID').textContent = employee.employee_id || '-';
                document.getElementById('selectedEmployeePosition').textContent = employee.position || 'Position not specified';
                document.getElementById('selectedEmployeeDepartment').textContent = employee.department || '-';
                document.getElementById('selectedEmployeeType').textContent = employee.position_type || 'N/A';
                selectedCard.style.display = 'block';
            }

            // Find period data by period_id
            let period = null;
            if (periodData && periodData.length > 0) {
                period = periodData.find(p => p.period_id == selectedPeriodId);
            }

            // Update period info
            if (period) {
                document.getElementById('periodStart').textContent = period.start_date || '-';
                document.getElementById('periodEnd').textContent = period.end_date || '-';
                document.getElementById('payDate').textContent = period.pay_date || '-';
            }

            // Update earnings - safely handle missing data
            let earningsHtml = '';
            if (employee.earnings && Array.isArray(employee.earnings) && employee.earnings.length > 0) {
                earningsHtml = employee.earnings.map(e => `
                    <tr>
                        <td>${e.description || 'N/A'}</td>
                        <td class="text-right">₱${parseFloat(e.amount || 0).toFixed(2)}</td>
                    </tr>
                `).join('');
            }
            document.getElementById('earningsTable').innerHTML = earningsHtml || '<tr><td colspan="2" class="text-muted text-center py-3">No earnings</td></tr>';
            document.getElementById('totalEarnings').textContent = `₱${parseFloat(employee.gross_pay || 0).toFixed(2)}`;

            // Update hours and attendance
            document.getElementById('hoursWorked').textContent = (employee.hours_worked || 0).toFixed(2);
            document.getElementById('daysWorked').textContent = (employee.days_worked || 0);
            document.getElementById('overtimePay').textContent = `₱${parseFloat(employee.overtime_pay || 0).toFixed(2)}`;
            document.getElementById('overtimeMultiplier').textContent = `${employee.overtime_multiplier || 1.25}x (${(employee.overtime_hours || 0).toFixed(2)} hrs)`;

            // Update deductions - safely handle missing data
            let deductionsHtml = '';
            if (employee.deductions && Array.isArray(employee.deductions) && employee.deductions.length > 0) {
                deductionsHtml = employee.deductions.map(d => `
                    <tr>
                        <td>${d.description || 'N/A'}</td>
                        <td class="text-right">₱${parseFloat(d.amount || 0).toFixed(2)}</td>
                    </tr>
                `).join('');
            }
            document.getElementById('deductionsTable').innerHTML = deductionsHtml || '<tr><td colspan="2" class="text-muted text-center py-3">No deductions</td></tr>';
            document.getElementById('totalDeductions').textContent = `₱${parseFloat(employee.total_deductions || 0).toFixed(2)}`;

            // Update summary
            document.getElementById('grossPay').textContent = `₱${parseFloat(employee.gross_pay || 0).toFixed(2)}`;
            document.getElementById('netPay').textContent = `₱${parseFloat(employee.net_pay || 0).toFixed(2)}`;

            // Show details and hide initial message
            document.getElementById('salaryDetailsContainer').style.display = 'block';
            document.getElementById('initialMessage').style.display = 'none';
        }

        function showErrorMessage(message) {
            const errorHtml = `<div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Error:</strong> ${message}<br>
                <a href="../diagnostics.php" class="btn btn-sm btn-warning mt-2">Run Diagnostics</a>
            </div>`;
            const initialMsg = document.getElementById('initialMessage');
            if (initialMsg) {
                initialMsg.innerHTML = errorHtml;
                initialMsg.style.display = 'block';
            }
        }

        // Employee Search Functionality with Autocomplete
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('employeeSearchInput');
            const searchResults = document.getElementById('employeeSearchResults');
            const resultsList = document.getElementById('employeeResultsList');
            let selectedIndex = -1;

            if (searchInput) {
                // Live search as user types
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim().toLowerCase();
                    selectedIndex = -1; // Reset selection

                    if (query.length === 0) {
                        // Show all employees when input is empty
                        if (allEmployees && allEmployees.length > 0) {
                            showAllEmployees();
                        } else {
                            searchResults.style.display = 'none';
                        }
                    } else {
                        // Filter and show results as user types
                        const filtered = allEmployees.filter(emp => {
                            const name = (emp.name || '').toLowerCase();
                            const empId = (emp.id || '').toString().toLowerCase();
                            return name.includes(query) || empId.includes(query);
                        });
                        displaySearchResults(filtered);
                    }
                });

                // Keyboard navigation
                searchInput.addEventListener('keydown', function(e) {
                    const items = resultsList.querySelectorAll('.search-result-item');

                    if (items.length === 0) return;

                    // Remove previous selection
                    items.forEach(item => item.classList.remove('active'));

                    switch (e.key) {
                        case 'ArrowDown':
                            e.preventDefault();
                            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                            if (selectedIndex >= 0) {
                                items[selectedIndex].classList.add('active');
                                items[selectedIndex].scrollIntoView({
                                    block: 'nearest'
                                });
                            }
                            break;

                        case 'ArrowUp':
                            e.preventDefault();
                            selectedIndex = Math.max(selectedIndex - 1, -1);
                            if (selectedIndex >= 0) {
                                items[selectedIndex].classList.add('active');
                            }
                            break;

                        case 'Enter':
                            e.preventDefault();
                            if (selectedIndex >= 0 && items[selectedIndex]) {
                                const selectedItem = items[selectedIndex];
                                const employeeId = selectedItem.getAttribute('data-employee-id');
                                const employeeName = selectedItem.getAttribute('data-employee-name');
                                selectEmployee(employeeId, employeeName);
                            } else if (searchResults.style.display === 'block') {
                                // If no item selected but results are showing, select first item
                                const firstItem = items[0];
                                if (firstItem) {
                                    const employeeId = firstItem.getAttribute('data-employee-id');
                                    const employeeName = firstItem.getAttribute('data-employee-name');
                                    selectEmployee(employeeId, employeeName);
                                }
                            }
                            break;

                        case 'Escape':
                            searchResults.style.display = 'none';
                            selectedIndex = -1;
                            break;
                    }
                });

                // Show all results on focus if input is empty
                searchInput.addEventListener('focus', function() {
                    if (this.value.trim().length === 0 && allEmployees && allEmployees.length > 0) {
                        showAllEmployees();
                    }
                });

                // Close results when clicking outside
                document.addEventListener('click', function(e) {
                    if (searchInput && !searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.style.display = 'none';
                        selectedIndex = -1;
                    }
                });
            }
        });
    </script>

    <!-- DataTables -->
    <script src="../../assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="../../assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../../assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="../../assets/plugins/jszip/jszip.min.js"></script>
    <script src="../../assets/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="../../assets/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="../../assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="../../assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="../../assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>


    <script>
        $(document).ready(function() {

            if (!$.fn.DataTable) {
                console.error("DataTables not loaded!");
                return;
            }

            $('#example1').DataTable({
                    responsive: true,
                    autoWidth: false,
                    lengthChange: false
                    // buttons: ['print']
                }).buttons().container()
                .appendTo('#example1_wrapper .col-md-6:eq(0)');

        });
    </script>

</body>

</html>