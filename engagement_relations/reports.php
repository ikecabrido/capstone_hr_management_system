<?php
session_start();
// require_once "auth.php";
require_once "../auth/database.php";
require_once "../auth/auth_check.php";
$theme = $_SESSION['user']['theme'] ?? 'light';

$reports = [];

try {
    require_once __DIR__ . '/config/db.php';

    // Direct reports data fetching (sa halip na internal API call) 
    $engagementTotal = $pdo->query('SELECT COUNT(*) AS total_surveys FROM engagement_surveys')->fetch(PDO::FETCH_ASSOC);

    // Since engagement_surveys has no status field in schema, fallback to surveys with responses or all surveys
    $activeSurveys = $pdo->query('SELECT COUNT(DISTINCT survey_id) AS active_surveys FROM survey_responses')->fetch(PDO::FETCH_ASSOC);

    $complaints = $pdo->query('SELECT status, COUNT(*) AS count FROM grievances GROUP BY status')->fetchAll(PDO::FETCH_ASSOC);

    $reports[] = [
        'title' => 'Engagement Survey Summary',
        'report_type' => 'Engagement',
        'description' => 'A summary of surveys in the system.',
        'data_points' => $engagementTotal['total_surveys'] ?? 0,
        'page_count' => 1,
        'generated_date' => date('Y-m-d H:i:s'),
        'generated_by' => $user['name'] ?? 'System',
        'details' => [
            'total_surveys' => (int)($engagementTotal['total_surveys'] ?? 0),
            'active_surveys' => (int)($activeSurveys['active_surveys'] ?? 0)
        ]
    ];

    $reports[] = [
        'title' => 'Complaint Trends',
        'report_type' => 'Complaints',
        'description' => 'Grievance volume grouped by status.',
        'data_points' => array_sum(array_column($complaints, 'count')),
        'page_count' => 1,
        'generated_date' => date('Y-m-d H:i:s'),
        'generated_by' => $user['name'] ?? 'System',
        'details' => $complaints
    ];
} catch (Exception $e) {
    $reportError = $e->getMessage();
    $reports = [];
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
  <link rel="stylesheet" href="css/reports.css" />

  <link rel="stylesheet" href="/css/reports.css" />
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
              <a href="auditlogs.php" class="nav-link">
                <i class="nav-icon fas fa-table"></i>
                <p>Audit logs</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="reports.php" class="nav-link active">
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
              <h1 class="m-0">Reports</h1>
            </div>
            <!-- /.col -->

            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
      </div>

    <!-- MAIN CONTENT -->

        <div id="content">

        <?php if (!empty($reportError)): ?>
            <div class="empty-state" style="background:#ffe6e6; color:#c0392b;">
                <p><strong>Error loading reports:</strong> <?= htmlspecialchars($reportError) ?></p>
                <p class="note">Please check database tables <code>engagement_surveys</code>, <code>survey_responses</code>, <code>grievances</code>.</p>
            </div>
        <?php elseif (empty($reports)): ?>
            <div class="empty-state">
                <p>No reports yet. Reports are generated from system data.</p>
                <p class="note">Reports will be available once data is populated in the system.</p>
                <p class="note">If no rows exist, run the SQL inserts from `schema.sql`.</p>
            </div>
        <?php else: ?>
            <?php foreach ($reports as $report): ?>
                <div class="report-card">
                    <div class="report-header">
                        <div class="report-title">
                            <?= htmlspecialchars($report['title'] ?? 'Report') ?>
                        </div>
                        <span class="report-type">
                            <?= htmlspecialchars($report['report_type'] ?? 'General') ?>
                        </span>
                    </div>
                    <div class="report-description">
                        <?= htmlspecialchars($report['description'] ?? '') ?>
                    </div>
                    <div class="report-stats">
                        <div class="stat">
                            <div class="stat-number"><?= $report['data_points'] ?? '0' ?></div>
                            <div class="stat-label">Data Points</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number"><?= $report['page_count'] ?? '1' ?></div>
                            <div class="stat-label">Pages</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number"><?= isset($report['generated_date']) ? 'OK' : 'N/A' ?></div>
                            <div class="stat-label">Status</div>
                        </div>
                    </div>
                    <div class="report-meta">
                        <strong>Generated:</strong> <?= date('M d, Y H:i', strtotime($report['generated_date'] ?? 'now')) ?>
                        <br>
                        <strong>By:</strong> <?= htmlspecialchars($report['generated_by'] ?? 'System') ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
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
</body>
</html>
