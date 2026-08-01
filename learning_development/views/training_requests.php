<?php
session_start();
require_once __DIR__ . "/../../auth/auth_check.php";
require_once __DIR__ . "/../../auth/database.php";

$theme = $_SESSION['user']['theme'] ?? 'light';
$db = Database::getInstance()->getConnection();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_request_status'])) {
    $ld_request_id = intval($_POST['ld_request_id'] ?? 0);
    $new_status = $_POST['new_status'] ?? 'New';
    if ($ld_request_id > 0 && in_array($new_status, ['Received', 'Approved', 'Rejected', 'Completed'], true)) {
        try {
            $sql = "UPDATE ld_training_requests SET request_status = ?, processed_at = NOW(), updated_at = NOW() WHERE ld_request_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$new_status, $ld_request_id]);
            $message = '<div class="alert alert-success">Request status updated to ' . htmlspecialchars($new_status) . '.</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Error updating request: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

$query = "SELECT lr.*, u.full_name as employee_name, p.title as program_title, c.title as course_title FROM ld_training_requests lr LEFT JOIN users u ON lr.employee_user_id = u.id LEFT JOIN ld_training_programs p ON lr.ld_training_program_id = p.ld_training_programs_id LEFT JOIN ld_courses c ON lr.ld_course_id = c.ld_courses_id ORDER BY lr.received_at DESC";
$stmt = $db->query($query);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>LD Training Requests</title>
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="../custom.css" />
  <link rel="stylesheet" href="../../layout/toast.css" />
  <style>
    .main-sidebar {
      position: fixed !important;
      top: 0 !important;
      left: 0 !important;
      width: 250px !important;
      height: 100vh !important;
      z-index: 1031 !important;
    }
    .content-wrapper {
      margin-left: 250px !important;
      min-height: 100vh !important;
    }
    .main-header {
      margin-left: 250px !important;
      width: calc(100% - 250px) !important;
    }
    @media (max-width: 767.98px) {
      .main-sidebar {
        transform: translateX(-100%) !important;
      }
      .content-wrapper, .main-header {
        margin-left: 0 !important;
        width: 100% !important;
      }
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="hold-transition sidebar-mini layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="../learning_development.php" class="nav-link">Home</a>
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
        <a class="nav-link" href="#" id="darkToggle" role="button" title="Toggle Dark Mode">
          <i class="fas fa-moon" id="themeIcon"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="../learning_development.php" class="brand-link">
      <img src="../../assets/pics/bcpLogo.png" alt="AdminLTE Logo" class="brand-image elevation-3" style="opacity: 0.9" />
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
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="../learning_development.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="browse.php" class="nav-link">
              <i class="nav-icon fas fa-book"></i>
              <p>Browse</p>
            </a>
          </li>
          <?php if ($_SESSION['user']['role'] === 'learning'): ?>
          <li class="nav-item">
            <a href="learning_management.php" class="nav-link">
              <i class="nav-icon fas fa-tasks"></i>
              <p>Learning Management</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="training_requests.php" class="nav-link active">
              <i class="nav-icon fas fa-clipboard-list"></i>
              <p>Training Requests</p>
            </a>
          </li>
          <?php endif; ?>
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
            <h1 class="m-0">Training Requests Management</h1>
          </div>
          <!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../learning_development.php">Home</a></li>
              <li class="breadcrumb-item active">Training Requests</li>
            </ol>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </div>
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Learning & Development Requests</h3>
      </div>
      <div class="card-body">
        <?= $message ?>
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Employee</th>
                <th>KPI</th>
                <th>Request Reason</th>
                <th>Program</th>
                <th>Course</th>
                <th>LD Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($requests)): ?>
                <tr><td colspan="8" class="text-center">No incoming training requests.</td></tr>
              <?php else: ?>
                <?php foreach ($requests as $req): ?>
                  <tr>
                    <td><?= $req['ld_request_id'] ?></td>
                    <td><?= htmlspecialchars($req['employee_name'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($req['kpi_name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($req['request_reason']) ?></td>
                    <td><?= htmlspecialchars($req['program_title'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($req['course_title'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($req['request_status']) ?></td>
                    <td>
                      <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="ld_request_id" value="<?= $req['ld_request_id'] ?>">
                        <input type="hidden" name="update_request_status" value="1">
                        <select name="new_status" class="form-control form-control-sm mb-2">
                          <option value="Received" <?= $req['request_status'] === 'Received' ? 'selected' : '' ?>>Received</option>
                          <option value="Approved" <?= $req['request_status'] === 'Approved' ? 'selected' : '' ?>>Approved</option>
                          <option value="Rejected" <?= $req['request_status'] === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                          <option value="Completed" <?= $req['request_status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
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
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

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

</body>
</html>
