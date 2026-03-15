<?php
session_start();

// Aggressive cache prevention - prevent ALL caching
header('Cache-Control: no-cache, no-store, must-revalidate, private, max-age=0, must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
header('Expires: 0');
header('Date: ' . date('r'));
header('ETag: "' . time() . mt_rand() . '"');
header('Vary: *');
header('X-UA-Compatible: IE=edge');
header('X-Frame-Options: SAMEORIGIN');

// Start output buffering to prevent any cached content
ob_start();

// CRITICAL: Check authentication FIRST before any output
if (!isset($_SESSION['user']) || empty($_SESSION['user']) || isset($_SESSION['logged_out'])) {
    ob_end_clean();
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
// Data is loaded by JS from API endpoints
$recognitions = [];
$rewards = [];
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
    href="../assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- overlayScrollbars -->
  <link
    rel="stylesheet"
    href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="/custom.css" />
  <link rel="stylesheet" href="../layout/toast.css" />
  <link rel="stylesheet" href="css/recognition-rewards.css" />
    
</head>

<body
  class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
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
    <nav class="main-header navbar navbar-expand navbar-dark">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="engagement_relations.php" class="nav-link">Home</a>
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
      <a href="engagement_relations.php" class="brand-link">

        <img
          src="../assets/pics/bcpLogo.png"
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
            <a href="#" onclick="openGlobalModal('Profile Settings ','../user_profile/profile_form.php')" class="d-block">
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
              <a href="dashboard.php" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="announcements.php" class="nav-link">
                <i class="nav-icon fas fa-chart-pie"></i>
                <p>Announcements</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="survey-management.php" class="nav-link">
                <i class="nav-icon fas fa-tree"></i>
                <p>Surveys</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="feedback-suggestions.php" class="nav-link">
                <i class="nav-icon fas fa-edit"></i>
                <p>Feedback & Suggestions</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="grievances.php" class="nav-link">
                <i class="nav-icon fas fa-table"></i>
                <p> Grievances</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="events.php" class="nav-link">
                <i class="nav-icon fas fa-table"></i>
                <p>Events</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="recognition-rewards.php" class="nav-link active">
                <i class="nav-icon fas fa-table"></i>
                <p>Recognition & Rewards</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="employees.php" class="nav-link">
                <i class="nav-icon fas fa-table"></i>
                <p>Employees</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="departments.php" class="nav-link">
                <i class="nav-icon fas fa-table"></i>
                <p>Departments</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="auditlogs.php" class="nav-link">
                <i class="nav-icon fas fa-table"></i>
                <p>Audit logs</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="reports.php" class="nav-link">
                <i class="nav-icon fas fa-table"></i>
                <p>Reports</p>
              </a>
            </li>
            <li class="nav-header">OTHER EXAMPLES</li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-calendar-alt"></i>
                <p>
                  Calendar
                  <span class="badge badge-info right">2</span>
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon far fa-image"></i>
                <p>Gallery</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-columns"></i>
                <p>Kanban Board</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../logout.php" class="nav-link">
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
              <h1 class="m-0">Recognition & Rewards</h1>
            </div>
            <!-- /.col -->

            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
      </div>
        <div id="content">
          <div class="tab-buttons">
            <button class="tab-btn active" onclick="switchTab('recognition')">Recognition</button>
            <button class="tab-btn" onclick="switchTab('rewards')">Rewards</button>
        </div>

        <!-- RECOGNITION TAB -->
        <div id="recognition" class="tab-content active">
            <?php if (empty($recognitions)): ?>
                <div class="empty-state">
                    <p>No recognitions yet</p>
                </div>
            <?php else: ?>
                <?php foreach ($recognitions as $rec): ?>
                    <div class="recognition-card">
                        <div class="recognition-badge">⭐ Recognition</div>
                        <div class="recipient-name">
                            To Employee ID: <?= htmlspecialchars($rec['to_employee_id'] ?? 'Unknown') ?>
                        </div>
                        <div class="recognition-reason">
                            <?= htmlspecialchars($rec['message'] ?? '') ?>
                        </div>
                        <div class="recognition-meta">
                            <div class="meta-item">
                                <div class="meta-label">From Employee</div>
                                <div class="meta-value"><?= htmlspecialchars($rec['from_employee_id'] ?? 'Admin') ?></div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-label">Type</div>
                                <div class="meta-value"><?= htmlspecialchars($rec['type'] ?? 'General') ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- REWARDS TAB -->
        <div id="rewards" class="tab-content">
            <?php if (empty($rewards)): ?>
                <div class="empty-state">
                    <p>No rewards configured yet</p>
                </div>
            <?php else: ?>
                <?php foreach ($rewards as $reward): ?>
                    <div class="reward-card">
                        <div class="reward-header">
                            <div class="reward-name">
                                <?= htmlspecialchars($reward['name'] ?? 'Reward') ?>
                            </div>
                            <div class="reward-points">
                                <?= intval($reward['points'] ?? 0) ?> pts
                            </div>
                        </div>
                        <?php if (!empty($reward['description'])): ?>
                            <div class="reward-description">
                                <?= htmlspecialchars($reward['description']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
          </div>
          <div id="recognition" class="tab-content active">
            <div id="recognitions-container" style="min-height:160px; padding:20px; color:#666;">Loading recognitions...</div>
          </div>

          <div id="rewards" class="tab-content">
            <div id="rewards-container" style="min-height:160px; padding:20px; color:#666;">Loading rewards...</div>
          </div>
        </div>
    </main>
</div>
  <?php include "../layout/global_modal.php"; ?>
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

<script src="js/main.js?v=<?= time(); ?>"></script>
<script src="js/tabs.js"></script>
<script src="js/recognition-rewards.js?v=<?= time(); ?>"></script>
</body>
</html>
