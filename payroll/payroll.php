<?php
session_start();
// require_once "auth.php";
require_once "../auth/database.php";
require_once "controllers/dashboardController.php";
require_once "../auth/auth_check.php";
$theme = $_SESSION['user']['theme'] ?? 'light';


/* DB */
$db = Database::getInstance()->getConnection();
$controller = new DashboardController($db);
$stats = $controller->getStats();

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Payroll management system</title>

  <!-- Google Font: Source Sans Pro -->
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- Font Awesome Icons -->
  <link
    rel="stylesheet"
    href="../assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- overlayScrollbars -->
  <link
    rel="stylesheet"
    href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="custom.css" />
  <link rel="stylesheet" href="../layout/toast.css" />
</head>

<body
  class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
  <div class="wrapper">
    <!-- Preloader -->
    <div
      class="preloader flex-column justify-content-center align-items-center">
      <img
        class="animation__wobble"
        src="../assets/pics/bcpLogo.png"
        alt="AdminLTELogo"
        height="60"
        width="60" />
    </div>

    <!-- Navbar -->
    <?php include '../legal_compliance/components/header.php'; ?>

    <!-- Main Sidebar Container -->
    <?php include '../legal_compliance/components/sidebar.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Payroll Management System</h1>
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
          <!-- Info boxes -->
          <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-users"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Total Employees</span>
                  <span class="info-box-number">
                    <?= $stats['employees'] ?>
                  </span>

                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box mb-3">
                <span class="info-box-icon bg-info elevation-1"><i class="nav-icon fas fa-calendar-day"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Current Period</span>
                  <span class="info-box-number">
                    <?= $stats['period']['period_name'] ?? 'None' ?>
                  </span>

                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <!-- fix for small devices only -->
            <div class="clearfix hidden-md-up"></div>

            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="nav-icon fas fa-money-bill-wave"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Total Payroll</span>
                  <span class="info-box-number">
                    ₱<?= number_format($stats['total_payroll'], 2) ?>
                  </span>

                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Pending Payroll</span>
                  <span class="info-box-number">
                    <?= $stats['pending_runs'] ?>
                  </span>

                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->

          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title">Monthly Recap Report</h5>

                  <div class="card-tools">
                    <button
                      type="button"
                      class="btn btn-tool"
                      data-card-widget="collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                    <div class="btn-group">
                      <button
                        type="button"
                        class="btn btn-tool dropdown-toggle"
                        data-toggle="dropdown">
                        <i class="fas fa-wrench"></i>
                      </button>
                      <div
                        class="dropdown-menu dropdown-menu-right"
                        role="menu">
                        <a href="#" class="dropdown-item">Action</a>
                        <a href="#" class="dropdown-item">Another action</a>
                        <a href="#" class="dropdown-item">Something else here</a>
                        <a class="dropdown-divider"></a>
                        <a href="#" class="dropdown-item">Separated link</a>
                      </div>
                    </div>
                    <button
                      type="button"
                      class="btn btn-tool"
                      data-card-widget="remove">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-8">
                      <p class="text-center">
                        <strong>
                          Payroll Summary:
                          <?= $stats['period']['start_date'] ?? '' ?>
                          -
                          <?= $stats['period']['end_date'] ?? '' ?>
                        </strong>
                      </p>

                      <div class="chart">
                        <!-- Sales Chart Canvas -->
                        <canvas
                          id="salesChart"
                          height="180"
                          style="height: 180px"></canvas>
                      </div>
                      <!-- /.chart-responsive -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-4">
                      <p class="text-center">
                        <strong>Payroll Progress</strong>
                      </p>
                      <div class="progress-group">
                        Processed Employees
                        <span class="float-right">
                          <b><?= $stats['progress']['processed'] ?></b> /
                          <?= $stats['progress']['total'] ?>
                        </span>
                        <div class="progress progress-sm">
                          <div class="progress-bar bg-success"
                            style="width: <?= $stats['progress']['total'] > 0
                                            ? ($stats['progress']['processed'] / $stats['progress']['total']) * 100
                                            : 0 ?>%">
                          </div>
                        </div>
                      </div>
                      <div class="progress-group">
                        Pending Employees
                        <span class="float-right">
                          <b><?= $stats['progress']['pending'] ?></b>
                        </span>
                        <div class="progress progress-sm">
                          <div class="progress-bar bg-warning"
                            style="width: <?= $stats['progress']['total'] > 0
                                            ? ($stats['progress']['pending'] / $stats['progress']['total']) * 100
                                            : 0 ?>%">
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- /.col -->
                  </div>
                  <!-- /.row -->
                </div>
                <!-- ./card-body -->
                <div class="card-footer">
                  <div class="row">
                    <div class="col-sm-3 col-6">
                      <div class="description-block border-right">
                        <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 17%</span>
                        <h5 class="description-header">
                          ₱<?= number_format($stats['lifetime'], 2) ?>
                        </h5>
                        <span class="description-text">TOTAL PAID</span>
                      </div>
                      <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-6">
                      <div class="description-block border-right">
                        <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i> 0%</span>
                        <h5 class="description-header">
                          ₱<?= number_format($stats['total_payroll'], 2) ?>
                        </h5>
                        <span class="description-text">CURRENT PERIOD</span>
                      </div>
                      <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-6">
                      <div class="description-block border-right">
                        <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 20%</span>
                        <h5 class="description-header">
                          <?= $stats['employees'] ?>
                        </h5>
                        <span class="description-text">EMPLOYEES</span>
                      </div>
                      <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-6">
                      <div class="description-block">
                        <span class="description-percentage text-danger"><i class="fas fa-caret-down"></i> 18%</span>
                        <h5 class="description-header">
                          <?= $stats['pending_runs'] ?>
                        </h5>
                        <span class="description-text">PENDING PAYROLL</span>
                      </div>
                      <!-- /.description-block -->
                    </div>
                  </div>
                  <!-- /.row -->
                </div>
                <!-- /.card-footer -->
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
          </div>
          <!-- Main row -->
        </div>
        <!--/. container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <?php include "../layout/global_modal.php"; ?>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <?php include '../legal_compliance/components/footer.php'; ?>
  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
  <script src="../assets/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../assets/dist/js/adminlte.js"></script>

  <!-- PAGE PLUGINS -->
  <!-- jQuery Mapael -->
  <script src="../assets/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
  <script src="../assets/plugins/raphael/raphael.min.js"></script>
  <script src="../assets/plugins/jquery-mapael/jquery.mapael.min.js"></script>
  <script src="../assets/plugins/jquery-mapael/maps/usa_states.min.js"></script>
  <!-- ChartJS -->
  <script src="../assets/plugins/chart.js/Chart.min.js"></script>

  <!-- AdminLTE for demo purposes -->
  <!-- <script src="assets/dist/js/demo.js"></script> -->
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <!-- <script src="assets/dist/js/pages/dashboard2.js"></script> -->

  <script src="../assets/dist/js/theme.js"></script>
  <script src="../assets/dist/js/time.js"></script>
  <script src="../assets/dist/js/global_modal.js"></script>
  <script src="../assets/dist/js/profile.js"></script>
  <script src="../custom.js"></script>

  <script>
    const chartData = <?= json_encode($stats['chart']) ?>;

    const labels = chartData.map(row => row.month);
    const totals = chartData.map(row => parseFloat(row.total));

    const ctx = document.getElementById('salesChart').getContext('2d');

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Monthly Payroll',
          data: totals,
          fill: true,
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true
            }
          }]
        }
      }
    });
  </script>

  <script></script>
</body>

</html>