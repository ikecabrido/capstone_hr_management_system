<?php
session_start();
require_once "auth/auth_check.php";
$theme = $_SESSION['user']['theme'] ?? 'light';
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard - HR Management System</title>

  <!-- Google Font: Source Sans Pro -->
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- Font Awesome Icons -->
  <link
    rel="stylesheet"
    href="assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- overlayScrollbars -->
  <link
    rel="stylesheet"
    href="assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="customGlobal.css" />
  <link rel="stylesheet" href="layout/toast.css" />
</head>

<body
  class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
  <div class="wrapper">
    <!-- Preloader -->
    <div
      class="preloader flex-column justify-content-center align-items-center">
      <img
        class="animation__wobble"
        src="assets/pics/bcpLogo.png"
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
          <a href="admin_dashboard.php" class="nav-link">Home</a>
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
      <a href="admin_dashboard.php" class="brand-link">
        <img
          src="assets/pics/bcpLogo.png"
          alt="AdminLTE Logo"
          class="brand-image elevation-3"
          style="opacity: 0.9" />
        <span class="brand-text font-weight-light">BCP Admin</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
          <div class="image">
          </div>
          <div class="info">
            <a href="#" onclick="openGlobalModal('Profile Settings ','user_profile/profile_form.php')" class="d-block">
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
            <li class="nav-item">
              <a href="admin_dashboard.php" class="nav-link active">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-header">HR MODULES</li>
            <li class="nav-item">
              <a href="recruitment/recruitment.php" class="nav-link">
                <i class="nav-icon fas fa-users"></i>
                <p>Recruitment</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="employee/employee.php" class="nav-link">
                <i class="nav-icon fas fa-user-tie"></i>
                <p>Employee Management</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="payroll/payroll.php" class="nav-link">
                <i class="nav-icon fas fa-calculator"></i>
                <p>Payroll</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="time_attendance/time_attendance.php" class="nav-link">
                <i class="nav-icon fas fa-clock"></i>
                <p>Time & Attendance</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="performance/performance.php" class="nav-link">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Performance</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="learning_development/learning_development.php" class="nav-link">
                <i class="nav-icon fas fa-graduation-cap"></i>
                <p>Learning & Development</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="exit_management/exit_management.php" class="nav-link">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                <p>Exit Management</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="compliance_legal/compliance.php" class="nav-link">
                <i class="nav-icon fas fa-gavel"></i>
                <p>Compliance & Legal</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="clinic/clinic.php" class="nav-link">
                <i class="nav-icon fas fa-medkit"></i>
                <p>Clinic</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="workforce/workforce.php" class="nav-link">
                <i class="nav-icon fas fa-users-cog"></i>
                <p>Workforce Analytics</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="engagement_relations/engagement_relations.php" class="nav-link">
                <i class="nav-icon fas fa-handshake"></i>
                <p>Employee Relations</p>
              </a>
            </li>
            <li class="nav-header">ACCOUNT</li>
            <li class="nav-item">
              <a href="logout.php" class="nav-link">
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
              <h1 class="m-0">HR Management System - Admin Dashboard</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Admin Dashboard</li>
              </ol>
            </div>
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
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Total Employees</span>
                  <span class="info-box-number" id="total-employees">0</span>
                </div>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-user-times"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Pending Resignations</span>
                  <span class="info-box-number" id="pending-resignations">0</span>
                </div>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Active Projects</span>
                  <span class="info-box-number" id="active-projects">0</span>
                </div>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Performance Reviews</span>
                  <span class="info-box-number" id="performance-reviews">0</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Module Cards -->
          <div class="row">
            <div class="col-lg-3 col-6">
              <div class="small-box bg-info">
                <div class="inner">
                  <h3>Recruitment</h3>
                  <p>Manage hiring process</p>
                </div>
                <div class="icon">
                  <i class="fas fa-users"></i>
                </div>
                <a href="recruitment/recruitment.php" class="small-box-footer">
                  Access Module <i class="fas fa-arrow-circle-right"></i>
                </a>
              </div>
            </div>

            <div class="col-lg-3 col-6">
              <div class="small-box bg-success">
                <div class="inner">
                  <h3>Payroll</h3>
                  <p>Salary & compensation</p>
                </div>
                <div class="icon">
                  <i class="fas fa-calculator"></i>
                </div>
                <a href="payroll/payroll.php" class="small-box-footer">
                  Access Module <i class="fas fa-arrow-circle-right"></i>
                </a>
              </div>
            </div>

            <div class="col-lg-3 col-6">
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3>Exit Management</h3>
                  <p>Employee offboarding</p>
                </div>
                <div class="icon">
                  <i class="fas fa-sign-out-alt"></i>
                </div>
                <a href="exit_management/exit_management.php" class="small-box-footer">
                  Access Module <i class="fas fa-arrow-circle-right"></i>
                </a>
              </div>
            </div>

            <div class="col-lg-3 col-6">
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3>Performance</h3>
                  <p>Employee evaluations</p>
                </div>
                <div class="icon">
                  <i class="fas fa-chart-line"></i>
                </div>
                <a href="performance/performance.php" class="small-box-footer">
                  Access Module <i class="fas fa-arrow-circle-right"></i>
                </a>
              </div>
            </div>
          </div>

          <!-- Additional Modules -->
          <div class="row">
            <div class="col-lg-4 col-6">
              <div class="small-box bg-primary">
                <div class="inner">
                  <h4>Time & Attendance</h4>
                  <p>Track working hours</p>
                </div>
                <div class="icon">
                  <i class="fas fa-clock"></i>
                </div>
                <a href="time_attendance/time_attendance.php" class="small-box-footer">
                  Access Module <i class="fas fa-arrow-circle-right"></i>
                </a>
              </div>
            </div>

            <div class="col-lg-4 col-6">
              <div class="small-box bg-secondary">
                <div class="inner">
                  <h4>Learning & Development</h4>
                  <p>Training programs</p>
                </div>
                <div class="icon">
                  <i class="fas fa-graduation-cap"></i>
                </div>
                <a href="learning_development/learning_development.php" class="small-box-footer">
                  Access Module <i class="fas fa-arrow-circle-right"></i>
                </a>
              </div>
            </div>

            <div class="col-lg-4 col-6">
              <div class="small-box bg-dark">
                <div class="inner">
                  <h4>Employee Relations</h4>
                  <p>Engagement & relations</p>
                </div>
                <div class="icon">
                  <i class="fas fa-handshake"></i>
                </div>
                <a href="engagement_relations/engagement_relations.php" class="small-box-footer">
                  Access Module <i class="fas fa-arrow-circle-right"></i>
                </a>
              </div>
            </div>
          </div>

          <!-- System Status -->
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">System Overview</h3>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-3">
                      <div class="description-block border-right">
                        <span class="description-percentage text-green"><i class="fas fa-caret-up"></i> 100%</span>
                        <h5 class="description-header">SYSTEM STATUS</h5>
                        <span class="description-text">ALL MODULES OPERATIONAL</span>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="description-block border-right">
                        <span class="description-percentage text-blue"><i class="fas fa-info-circle"></i></span>
                        <h5 class="description-header">DATABASE</h5>
                        <span class="description-text">HR_MANAGEMENT</span>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="description-block border-right">
                        <span class="description-percentage text-yellow"><i class="fas fa-user"></i></span>
                        <h5 class="description-header">ADMIN USER</h5>
                        <span class="description-text">FULL ACCESS</span>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="description-block">
                        <span class="description-percentage text-red"><i class="fas fa-calendar"></i></span>
                        <h5 class="description-header">LAST UPDATE</h5>
                        <span class="description-text"><?php echo date('M d, Y'); ?></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <?php include "layout/global_modal.php"; ?>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
  </div>

  <!-- REQUIRED SCRIPTS -->
  <script src="assets/plugins/jquery/jquery.min.js"></script>
  <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <script src="assets/dist/js/adminlte.js"></script>
  <script src="assets/dist/js/theme.js"></script>
  <script src="assets/dist/js/time.js"></script>
  <script src="assets/dist/js/global_modal.js"></script>
  <script src="assets/dist/js/profile.js"></script>

  <script>
    // Load dashboard statistics
    function loadDashboardStats() {
      // This would normally fetch real data from the database
      // For now, showing placeholder data
      $('#total-employees').text('150');
      $('#pending-resignations').text('3');
      $('#active-projects').text('12');
      $('#performance-reviews').text('8');
    }

    $(document).ready(function() {
      loadDashboardStats();
    });
  </script>
</body>

</html>