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
$logs = [];

// Prepare DB connection for fallback role lookups
require_once __DIR__ . '/config/db.php';
try {
    $roleStmt = $pdo->prepare('SELECT role, name FROM employees WHERE id = ? LIMIT 1');
    $logsStmt = $pdo->query('SELECT * FROM audit_logs ORDER BY performed_at DESC LIMIT 200');
    $logs = $logsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $roleStmt = null;
    $logs = [];
}
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

    <link rel="stylesheet" href="css/auditlogs.css" />
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
              <a href="recognition-rewards.php" class="nav-link">
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
              <a href="auditlogs.php" class="nav-link active" >
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
              <h1 class="m-0">Audit logs</h1>
            </div>
            <!-- /.col -->

            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
       <div id="content">
            <?php if (empty($logs)): ?>
                <div style="background: white; padding: 40px; border-radius: 10px; text-align: center; color: #888;">
                    <p style="font-size: 1.1em;">No audit logs found</p>
                </div>
            <?php else: ?>
            <div class="logs-table">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Entity</th>
                            <th>Changes</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <?php
                                    $performedBy = $log['performed_by'] ?? null;
                                    $userLabel = 'Unknown';
                                    if ($performedBy !== null && $performedBy !== '') {
                                        $userLabel = htmlspecialchars($performedBy);
                                        if (isset($roleStmt) && $roleStmt) {
                                            try {
                                                $roleStmt->execute([$performedBy]);
                                                $empRow = $roleStmt->fetch();
                                                if ($empRow && !empty($empRow['name'])) {
                                                    $userLabel = htmlspecialchars($empRow['name'] . ' (' . $performedBy . ')');
                                                }
                                            } catch (Exception $e) {
                                                // ignore and fallback to ID
                                            }
                                        }
                                    }
                                ?>
                                <td><?= $userLabel ?></td>
                                <td>
                                    <span class="action-badge action-<?= strtolower($log['action'] ?? 'read') ?>">
                                        <?= strtoupper($log['action'] ?? 'READ') ?>
                                    </span>
                                </td>
                                <?php
                                    $tt = $log['target_type'] ?? null;
                                    $roleLabels = [
                                        'employee' => 'Employee',
                                        'manager' => 'Manager',
                                        'hr' => 'HR',
                                        'admin' => 'Admin'
                                    ];

                                    // If this is a LOGIN and target_type is generic 'employees' (legacy),
                                    // try to fetch the actual role from DB using performed_by
                                    if (isset($log['action']) && strtoupper($log['action']) === 'LOGIN' && ($tt === null || in_array(strtolower($tt), ['employees','employee']))) {
                                        $fetchedRole = null;
                                        if ($roleStmt && !empty($log['performed_by'])) {
                                            try {
                                                $roleStmt->execute([$log['performed_by']]);
                                                $emp = $roleStmt->fetch();
                                                if ($emp && !empty($emp['role'])) {
                                                    $fetchedRole = $emp['role'];
                                                }
                                            } catch (Exception $e) {
                                                $fetchedRole = null;
                                            }
                                        }
                                        if ($fetchedRole) {
                                            $tt = $fetchedRole;
                                        }
                                    }

                                    if ($tt === null) {
                                        $entityLabel = 'N/A';
                                    } elseif (isset($roleLabels[strtolower($tt)])) {
                                        $entityLabel = $roleLabels[strtolower($tt)];
                                    } else {
                                        $entityLabel = ucwords(str_replace('_', ' ', rtrim($tt, 's')));
                                    }
                                ?>
                                <td><?= htmlspecialchars($entityLabel) ?></td>
                                <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    ID: <?= htmlspecialchars($log['target_id'] ?? '-') ?>
                                </td>
                                <td><?= htmlspecialchars(substr($log['details'] ?? '-', 0, 50)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
      </div>
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
</body>
</html>
