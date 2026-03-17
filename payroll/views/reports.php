<?php
require_once __DIR__ . '/../../auth/auth.php';
require_once __DIR__ . '/../controllers/reportController.php';
require_once __DIR__ . '/../../auth/auth_check.php';
$theme = $_SESSION['user']['theme'] ?? 'light';


$user = $auth->user();
$controller = new ReportController(Database::getInstance()->getConnection());

$periodId = $_GET['period_id'] ?? 0;
$data = $controller->index($periodId);
$periods = $data['periods'];
$periodName = 'All Periods';

foreach ($periods as $p) {
    if ($p['id'] == $periodId) {
        $periodName = $p['period_name'];
        break;
    }
}

$payroll = $data['payroll'];



?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Payroll management system - Reports</title>

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
            <a href="payroll.html" class="brand-link">
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
                            <a href="salaryOverview.php" class="nav-link ">
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
                                <p>Allowance & Deductions</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="reports.php" class="nav-link active">
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
                            <h1 class="m-0">Payroll Report — <?= htmlspecialchars($periodName) ?></h1>

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
                    <!-- Filters -->
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title">
                                <i class="fas fa-filter"></i> Report Filters
                            </h3>
                        </div>

                        <div class="card-body">
                            <form method="GET" class="row">

                                <div class="col-md-4">
                                    <label>Payroll Period</label>
                                    <select name="period_id" class="form-control" required>
                                        <option value="">-- Select Period --</option>

                                        <?php foreach ($periods as $p): ?>
                                            <option value="<?= $p['id'] ?>"
                                                <?= $p['id'] == $periodId ? 'selected' : '' ?>>

                                                <?= $p['period_name'] ?>
                                                (<?= $p['start_date'] ?> - <?= $p['end_date'] ?>)

                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                </div>

                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-search"></i> Generate
                                    </button>
                                    <?php if ($periodId): ?>

                                        <a href="../public/report_pdf.php?period_id=<?= $periodId ?>"
                                            class="btn btn-danger ml-2 mr-2"
                                            target="_blank">

                                            <i class="fas fa-file-pdf"></i> Export PDF

                                        </a>

                                    <?php endif; ?>
                                    <a href="reports.php" class="btn btn-secondary">
                                        <i class="fas fa-sync"></i> Reset
                                    </a>
                                </div>

                            </form>
                        </div>
                    </div>


                    <!-- Summary Boxes -->
                    <?php
                    $totalGross = 0;
                    $totalDeduct = 0;
                    $totalNet = 0;

                    foreach ($payroll as $p) {
                        $totalGross  += $p['gross_pay'];
                        $totalDeduct += $p['total_deductions'];
                        $totalNet    += $p['net_pay'];
                    }
                    ?>

                    <div class="row mt-3">

                        <div class="col-md-4">
                            <div class="info-box bg-info">
                                <span class="info-box-icon">
                                    <i class="fas fa-money-bill"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Total Gross</span>
                                    <span class="info-box-number">
                                        ₱<?= number_format($totalGross, 2) ?>
                                    </span>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon">
                                    <i class="fas fa-minus-circle"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Total Deductions</span>
                                    <span class="info-box-number">
                                        ₱<?= number_format($totalDeduct, 2) ?>
                                    </span>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="info-box bg-success">
                                <span class="info-box-icon">
                                    <i class="fas fa-check-circle"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Total Net Pay</span>
                                    <span class="info-box-number">
                                        ₱<?= number_format($totalNet, 2) ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>


                    <!-- Report Table -->
                    <div class="card mt-3">

                        <div class="card-header bg-info">
                            <h3 class="card-title">
                                <i class="fas fa-file-alt"></i> Payroll Report
                            </h3>
                        </div>

                        <div class="card-body">

                            <table id="reportTable"
                                class="table table-bordered table-striped">

                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Position</th>
                                        <th>Employment</th>
                                        <th>Gross</th>
                                        <th>Deductions</th>
                                        <th>Net Pay</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php if (empty($payroll)): ?>

                                        <tr>
                                            <td colspan="6"
                                                class="text-center text-muted">
                                                No records found
                                            </td>
                                        </tr>

                                    <?php else: ?>

                                        <?php foreach ($payroll as $p): ?>

                                            <tr>
                                                <td><?= htmlspecialchars($p['employee_name']) ?></td>
                                                <td><?= htmlspecialchars($p['position']) ?></td>
                                                <td><?= htmlspecialchars($p['employment_type']) ?></td>

                                                <td>
                                                    ₱<?= number_format($p['gross_pay'], 2) ?>
                                                </td>

                                                <td>
                                                    ₱<?= number_format($p['total_deductions'], 2) ?>
                                                </td>

                                                <td>
                                                    ₱<?= number_format($p['net_pay'], 2) ?>
                                                </td>
                                            </tr>

                                        <?php endforeach; ?>

                                    <?php endif; ?>

                                </tbody>

                            </table>

                        </div>
                    </div>
                </div>
                <!--/. container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php include "../../layout/global_modal.php"; ?>
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
    <!-- ./wrapper -->

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

            $('#reportTable').DataTable({
                    responsive: true,
                    autoWidth: false,
                    lengthChange: false,
                    pageLength: 10
                }).buttons().container()
                .appendTo('#reportTable_wrapper .col-md-6:eq(0)');

        });
    </script>


</body>

</html>