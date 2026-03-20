<?php
session_start();
// require_once "auth.php";
require_once "../../auth/database.php";
require_once "../../auth/auth_check.php";
require_once __DIR__ . '/../autoload.php';

use App\Controllers\GrievanceController;

$theme = $_SESSION['user']['theme'] ?? 'light';

$ctrl = new GrievanceController();
$payload = $payload ?? [];
$payload['grievances'] = $ctrl->getGrievances();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['subject']) && !empty($_POST['description'])) {
        $employeeId = (int)($_SESSION['user']['employee_id'] ?? 0);
        if ($employeeId > 0) {
            $ctrl->fileGrievance($employeeId, $_POST['subject'], $_POST['description']);
            $_SESSION['flash_success'] = 'Grievance submitted successfully.';
        } else {
            $_SESSION['flash_error'] = 'Your account is not linked to an employee record.';
        }
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    if (!empty($_POST['id']) && !empty($_POST['status']) && isset($_POST['update_status'])) {
        $ctrl->updateStatus((int)$_POST['id'], $_POST['status']);
        $_SESSION['flash_success'] = 'Grievance status updated.';
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Employee Engagement and Relations Management</title>

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
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />

  <link rel="stylesheet" href="../../layout/toast.css" />
  <link rel="stylesheet" href="css/grievance.css" />
  <link rel="stylesheet" href="../custom.css" />    
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
          <a href="../engagement_relations.php" class="nav-link">Home</a>        </li>
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
      <a href="../engagement_relations.php" class="brand-link">

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
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
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
              <a href="dashboard.php" class="nav-link ">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="communication.php" class="nav-link">
                <i class="nav-icon fas fa-chart-pie"></i>
                <p>Communication</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="survey.php" class="nav-link">
                <i class="nav-icon fas fa-poll"></i>
                <p>Survey</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="recognition.php" class="nav-link">
                <i class="nav-icon fas fa-award"></i>
                <p>Recognition</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="grievance.php" class="nav-link active">
                <i class="nav-icon fas fa-exclamation-triangle"></i>
                <p> Grievances</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="social.php" class="nav-link">
                <i class="nav-icon fas fa-users"></i>
                <p> Social</p>
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


        <!-- MAIN CONTENT --
            <!-- HEADER -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Grievances</h1>
                <p class="text-muted">Submit and manage employee grievances</p>
            </div>
          </div>
        </div>

    <div class="row">
      <div class="col-12">
        <?php if (!empty($flashSuccess)): ?>
          <div class="alert alert-success"><?=htmlspecialchars($flashSuccess)?></div>
        <?php endif; ?>
        <?php if (!empty($flashError)): ?>
          <div class="alert alert-danger"><?=htmlspecialchars($flashError)?></div>
        <?php endif; ?>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card card-danger card-outline">
          <div class="card-header"><h3 class="card-title">Submit Complaint</h3></div>
          <div class="card-body">
            <form method="post" class="grievance-form">
              <div class="form-group">
                <label for="grievance-subject">Subject</label>
                <input id="grievance-subject" class="form-control" type="text" name="subject" placeholder="Grievance subject" required>
              </div>
              <div class="form-group">
                <label for="grievance-desc">Description</label>
                <textarea id="grievance-desc" class="form-control" name="description" rows="4" placeholder="Describe your issue" required></textarea>
              </div>
              <button class="btn btn-danger" type="submit">Submit Complaint</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card card-warning card-outline">
          <div class="card-header"><h3 class="card-title">All Grievances</h3></div>
          <div class="card-body">
            <?php if (empty($payload['grievances'])): ?>
              <p class="text-muted">No grievances submitted yet.</p>
            <?php endif; ?>

            <?php foreach ($payload['grievances'] as $g): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                          <h5 class="card-title mb-0"><?=htmlspecialchars($g['subject'])?></h5>
                          <span class="badge badge-pill badge-<?= $g['status']=='resolved' ? 'success' : ($g['status']=='in progress' ? 'warning' : 'secondary') ?>"><?=htmlspecialchars($g['status'])?></span>
                        </div>
                        <p class="card-text mt-2 text-clamp-3"><?=nl2br(htmlspecialchars($g['description']))?></p>
                        <small class="text-muted">Filed by <?=htmlspecialchars($g['employee_name'])?> on <?=htmlspecialchars($g['created_at'])?></small>
                        <form method="post" class="form-inline">
                            <input type="hidden" name="id" value="<?=htmlspecialchars($g['id'])?>">
                            <div class="form-group mr-2">
                                <select class="form-control form-control-sm" name="status">
                                    <option value="pending" <?= $g['status']=='pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="in progress" <?= $g['status']=='in progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="resolved" <?= $g['status']=='resolved' ? 'selected' : '' ?>>Resolved</option>
                                </select>
                            </div>
                            <button class="btn btn-sm btn-primary" type="submit" name="update_status">Update</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
      </div>
    </div>
  <!-- CONTENT -->

    </div>
    <?php include "../../layout/global_modal.php"; ?>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->

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
  <script src="../../assets/dist/js/adminlte.js"></script>

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
  <script src="../../assets/dist/js/theme.js"></script>
  <script src="../../assets/dist/js/time.js"></script>
  <script src="../../assets/dist/js/global_modal.js"></script>
  <script src="../../assets/dist/js/profile.js"></script>

  <script></script>
  <script src="views/js/grievance.js"></script>
</body>

</html>
