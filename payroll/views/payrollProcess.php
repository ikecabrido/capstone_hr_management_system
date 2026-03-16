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

// Get selected period
$selectedPeriodId = $_POST['period_id'] ?? null;

// Initialize toast variables
$toastMessage = null;
$toastType = 'info';

// Preview + calculation
$previewData = [];
$payrollResults = [];

if ($selectedPeriodId) {
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
    class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
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
                                <p>Allowance & Deductions</p>
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
                    <!-- Main row -->
                    <form method="POST" class="mb-3">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label><strong>Payroll Period</strong></label>
                                <select name="period_id" class="form-control" required>
                                    <option value="">-- Select Payroll Period --</option>
                                    <?php foreach ($periods as $p): ?>
                                        <option value="<?= $p['id'] ?>"
                                            <?= ($selectedPeriodId == $p['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['period_name']) ?>
                                            (<?= ucfirst($p['status']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-calculator"></i> Calculate
                                </button>
                            </div>
                        </div>
                    </form>
                    <?php if ($selectedPeriodId && !empty($payrollResults)): ?>
                        <div class="card mt-4">
                            <div class="card-header bg-info">
                                <h3 class="card-title">
                                    <i class="fas fa-eye"></i> Payroll Preview
                                </h3>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Employee</th>
                                            <th>Position</th>
                                            <th>Gross Pay</th>
                                            <th>Deductions</th>
                                            <th>Net Pay</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($payrollResults as $row): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['name']) ?></td>
                                                <td><?= htmlspecialchars($row['position'] ?? '-') ?></td>
                                                <td>₱<?= number_format($row['gross_pay'], 2) ?></td>
                                                <td>₱<?= number_format($row['total_deductions'], 2) ?></td>
                                                <td>
                                                    <strong>
                                                        ₱<?= number_format($row['net_pay'], 2) ?>
                                                    </strong>
                                                </td>

                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php if ($selectedPeriodId && !empty($payrollResults)): ?>
                            <button type="button"
                                class="btn btn-success mt-3 mb-3"
                                <?= empty($payrollResults) ? 'disabled' : '' ?>
                                data-toggle="modal"
                                data-target="#finalizeModal">
                                <i class="fas fa-check"></i> Finalize Payroll
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
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
        <footer class="main-footer">
            <strong>Copyright &copy; 2026-2027 Bestlink College of the
                Philippines.</strong>
            All rights reserved.
            <!-- <div class="float-right d-none d-sm-inline-block">
          <b>Version</b> 3.2.0
        </div> -->
        </footer>
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
    <script>
        $(document).ready(function() {
            <?php if ($toastMessage): ?>
                $('#adminlteToast').toast('show');
            <?php endif; ?>
        });
    </script>


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
    <script src="../../assets/dist/js/theme.js"></script>
    <script src="../../assets/dist/js/time.js"></script>
    <script src="../../assets/dist/js/global_modal.js"></script>
    <script src="../../assets/dist/js/profile.js"></script>
    <script></script>

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