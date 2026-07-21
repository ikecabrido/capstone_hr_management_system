<?php
session_start();
require_once "../auth/auth_check.php";
$theme = $_SESSION['user']['theme'] ?? 'light';

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Workforce Analytics and Reporting Management</title>

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
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- WFA Dashboard Styles -->
  <style>
    .wfa-container {
      background: #e6ffed;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
    }

    .wfa-metrics-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 15px;
      margin-bottom: 30px;
    }

    .wfa-metric-card {
      background: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      border-left: 4px solid #007bff;
    }

    .wfa-metric-card.danger { border-left-color: #dc3545; }
    .wfa-metric-card.warning { border-left-color: #ffc107; }
    .wfa-metric-card.success { border-left-color: #28a745; }
    .wfa-metric-card.info { border-left-color: #17a2b8; }

    .wfa-metric-label {
      font-size: 0.85rem;
      color: #6c757d;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 8px;
    }

    .wfa-metric-value {
      font-size: 2rem;
      font-weight: bold;
      color: #212529;
      margin-bottom: 5px;
    }

    .wfa-metric-change {
      font-size: 0.85rem;
      color: #6c757d;
    }

    .wfa-charts-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .wfa-chart-container {
      background: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .wfa-chart-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 15px;
      color: #212529;
    }

    .wfa-table-container {
      background: linear-gradient(180deg, #ffffff 0%, #f8f9fb 100%);
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 18px 45px rgba(56, 85, 123, 0.08);
      margin-bottom: 20px;
      border: 1px solid rgba(33, 37, 41, 0.06);
    }

    .wfa-table-container h3 {
      margin-bottom: 20px;
      color: #1f2a44;
      font-size: 1.25rem;
      font-weight: 700;
      letter-spacing: -0.02em;
    }

    .wfa-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 12px;
      min-width: 0;
    }

    .wfa-table thead {
      background-color: transparent;
    }

    .wfa-table th {
      padding: 16px 18px;
      text-align: left;
      font-weight: 700;
      color: #495057;
      font-size: 0.92rem;
      text-transform: uppercase;
      letter-spacing: 0.03em;
      border-bottom: none;
      white-space: nowrap;
    }

    .wfa-table td {
      padding: 16px 18px;
      background: white;
      border: none;
      vertical-align: middle;
      color: #495057;
      font-size: 0.95rem;
    }

    .wfa-table tbody tr {
      background: white;
      border: 1px solid #e9ecef;
      border-radius: 14px;
      box-shadow: 0 4px 12px rgba(40, 54, 66, 0.05);
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .wfa-table tbody tr:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 20px rgba(40, 54, 66, 0.09);
    }

    .wfa-table tbody tr td:first-child {
      border-top-left-radius: 14px;
      border-bottom-left-radius: 14px;
    }

    .wfa-table tbody tr td:last-child {
      border-top-right-radius: 14px;
      border-bottom-right-radius: 14px;
    }

    .wfa-table tbody tr td > small {
      color: #6c757d;
      display: block;
      margin-top: 6px;
      font-size: 0.85rem;
    }

    .wfa-table .view-appraisal-btn {
      width: 36px;
      height: 36px;
      padding: 0;
      border-radius: 12px;
      display: inline-flex;
      justify-content: center;
      align-items: center;
      border: 1px solid #dfe3e8;
      background-color: #ffffff;
      color: #495057;
      transition: all 0.2s ease;
    }

    .wfa-table .view-appraisal-btn:hover {
      background-color: #eef2ff;
      color: #2f3d72;
      border-color: #bbc7f0;
      transform: translateY(-1px);
    }

    .review-badge,
    .status-pill {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 999px;
      padding: 6px 14px;
      font-size: 0.82rem;
      font-weight: 600;
      white-space: nowrap;
    }

    .review-badge.quarterly {
      background: #d4edda;
      color: #155724;
    }

    .review-badge.annual {
      background: #d4edda;
      color: #155724;
    }

    .review-badge.mid-year {
      background: #fff3cd;
      color: #856404;
    }

    .review-badge.monthly {
      background: #fff3cd;
      color: #856404;
    }

    .status-pill.recent {
      background: #e6f4ff;
      color: #0b74de;
    }

    .status-pill.active {
      background: #e6ffed;
      color: #1d7a2d;
    }

    .status-pill.inactive {
      background: #f8f9fa;
      color: #6c757d;
    }

    .status-pill.unknown {
      background: #f2f2f2;
      color: #7f8c8d;
    }

    .wfa-risk-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 500;
    }

    .wfa-risk-badge.high { 
      background-color: #f8d7da;
      color: #721c24;
    }

    .wfa-risk-badge.medium { 
      background-color: #fff3cd;
      color: #856404;
    }

    .wfa-risk-badge.low { 
      background-color: #d4edda;
      color: #155724;
    }

    .wfa-loading {
      text-align: center;
      padding: 40px;
      color: #6c757d;
    }

    .wfa-error {
      background-color: #f8d7da;
      color: #721c24;
      padding: 15px;
      border-radius: 4px;
      margin-bottom: 20px;
    }

    /* Ensure inactive tabs are hidden */
    .tab-pane {
      display: none !important;
      visibility: hidden !important;
    }

    .tab-pane.active {
      display: block !important;
      visibility: visible !important;
    }

    .tab-pane.fade.show.active {
      display: block !important;
      visibility: visible !important;
      opacity: 1 !important;
    }

    /* Enhanced Button Styles */
    .wfa-btn {
      padding: 11px 24px;
      border: none;
      border-radius: 6px;
      font-size: 0.95rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
      letter-spacing: 0.5px;
    }

    .wfa-btn:hover {
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.16);
      transform: translateY(-2px);
    }

    .wfa-btn:active {
      transform: translateY(0);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .wfa-btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .wfa-btn-primary:hover {
      background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
      color: white;
    }

    .wfa-btn-secondary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .wfa-btn-secondary:hover {
      background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
      color: white;
    }

    .wfa-btn-success {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      color: white;
    }

    .wfa-btn-success:hover {
      background: linear-gradient(135deg, #3da5e8 0%, #00dbe5 100%);
      color: white;
    }

    .wfa-btn-danger {
      background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
      color: white;
    }

    .wfa-btn-danger:hover {
      background: linear-gradient(135deg, #f5588a 0%, #fdd130 100%);
      color: white;
    }

    .wfa-btn-warning {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
    }

    .wfa-btn-warning:hover {
      background: linear-gradient(135deg, #e87aeb 0%, #e43c5c 100%);
      color: white;
    }

    .wfa-btn-info {
      background: linear-gradient(135deg, #00c9ff 0%, #92fe9d 100%);
      color: white;
    }

    .wfa-btn-info:hover {
      background: linear-gradient(135deg, #00b8e8 0%, #82ed8d 100%);
      color: white;
    }

    .wfa-btn-sm {
      padding: 6px 12px;
      font-size: 0.85rem;
    }

    .wfa-btn-lg {
      padding: 14px 28px;
      font-size: 1.1rem;
    }

    .wfa-btn-block {
      width: 100%;
      justify-content: center;
    }

    .wfa-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      box-shadow: none;
    }

    .wfa-btn:disabled:hover {
      transform: none;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Filter Actions Container */
    .wfa-filter-actions {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
      margin-top: 25px;
      padding-top: 20px;
      border-top: 2px solid #e0e0e0;
    }

    /* Enhanced Select/Dropdown Styles */
    select, input[type="date"], input[type="text"], .wfa-filter-input, .wfa-filter-select {
      padding: 10px 14px;
      border: 2px solid #e0e0e0;
      border-radius: 6px;
      font-size: 0.95rem;
      background-color: #fff;
      color: #212529;
      transition: all 0.3s ease;
      font-family: inherit;
    }

    select:hover, input[type="date"]:hover, input[type="text"]:hover, .wfa-filter-input:hover, .wfa-filter-select:hover {
      border-color: #667eea;
      box-shadow: 0 2px 6px rgba(102, 126, 234, 0.1);
    }

    select:focus, input[type="date"]:focus, input[type="text"]:focus, .wfa-filter-input:focus, .wfa-filter-select:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
      background-color: #f8f9ff;
    }

    /* Dropdown Arrow Styling */
    select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23667eea' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 10px center;
      padding-right: 30px;
      background-size: 12px;
    }

    /* Filter Row Spacing */
    .wfa-filter-row {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
      align-items: flex-end;
      margin-bottom: 15px;
      padding-bottom: 15px;
    }

    .wfa-filter-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
      flex: 1;
      min-width: 200px;
    }

    .wfa-filter-group label {
      font-weight: 600;
      color: #212529;
      font-size: 0.9rem;
      display: block;
    }

    /* Spacing between sections */
    .wfa-metrics-grid {
      margin-top: 25px;
      margin-bottom: 35px;
    }

    .wfa-charts-grid {
      margin-top: 30px;
      margin-bottom: 35px;
    }

    .wfa-table-container {
      margin-top: 25px;
      margin-bottom: 25px;
    }

    /* Insights Section Styles */
    .wfa-insights-section {
      margin-top: 40px;
      margin-bottom: 40px;
      padding: 25px;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      border-radius: 12px;
      border-left: 5px solid #667eea;
    }

    .wfa-insight-card {
      background: white;
      border-radius: 8px;
      padding: 18px;
      margin-bottom: 15px;
      border-left: 4px solid #667eea;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
    }

    .wfa-insight-card:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
      transform: translateX(2px);
    }

    .wfa-insight-card.success {
      border-left-color: #28a745;
      background: #f0f9f6;
    }

    .wfa-insight-card.danger {
      border-left-color: #dc3545;
      background: #fdf5f6;
    }

    .wfa-insight-card.warning {
      border-left-color: #ffc107;
      background: #fffaf0;
    }

    .wfa-insight-card.info {
      border-left-color: #17a2b8;
      background: #f0f8fb;
    }

    .insight-header {
      display: flex;
      align-items: flex-start;
      gap: 15px;
      margin-bottom: 12px;
    }

    .insight-icon {
      font-size: 24px;
      line-height: 1.2;
      flex-shrink: 0;
    }

    .insight-recommendation {
      padding: 12px 15px;
      background: rgba(102, 126, 234, 0.08);
      border-radius: 6px;
      font-size: 0.9rem;
      color: #333;
      line-height: 1.5;
      margin-top: 12px;
    }

    .wfa-insight-card.success .insight-recommendation {
      background: rgba(40, 167, 69, 0.08);
    }

    .wfa-insight-card.danger .insight-recommendation {
      background: rgba(220, 53, 69, 0.08);
    }

    .wfa-insight-card.warning .insight-recommendation {
      background: rgba(255, 193, 7, 0.08);
    }

    .wfa-insight-card.info .insight-recommendation {
      background: rgba(23, 162, 184, 0.08);
    }

  </style>
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
          <a href="workforce.php" class="nav-link">Home</a>
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
      <a href="workforce.php" class="brand-link">

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
              <a href="#dashboard" data-toggle="tab" class="nav-link active">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard Metrics</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#attrition" data-toggle="tab" class="nav-link">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Attrition & Turnover</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#diversity" data-toggle="tab" class="nav-link">
                <i class="nav-icon fas fa-handshake"></i>
                <p>Diversity & Inclusion</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#performance" data-toggle="tab" class="nav-link">
                <i class="nav-icon fas fa-star"></i>
                <p>Performance</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#reports" data-toggle="tab" class="nav-link">
                <i class="nav-icon fas fa-file-alt"></i>
                <p>Custom Reports</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#snapshots" data-toggle="tab" class="nav-link">
                <i class="nav-icon fas fa-history"></i>
                <p>Report History</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#actions" data-toggle="tab" class="nav-link">
                <i class="nav-icon fas fa-tasks"></i>
                <p>Action Tracking</p>
              </a>
            </li>


            <!-- Logout -->
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

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">

          <!-- Tab Content -->
          <div class="tab-content" id="mainTabContent">
            <!-- Dashboard Tab -->
            <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
              <div class="wfa-container" id="dashboardContainer">
                <div class="wfa-loading">
                  <i class="fas fa-spinner fa-spin"></i> Loading Dashboard...
                </div>
              </div>
            </div>
            <!-- /.tab-pane dashboard -->

            <!-- Attrition Tab -->
            <div class="tab-pane fade" id="attrition" role="tabpanel">
              <?php include 'public/attrition.php'; ?>
            </div>
            <!-- /.tab-pane attrition -->

            <!-- Diversity Tab -->
            <div class="tab-pane fade" id="diversity" role="tabpanel">
              <?php include 'public/diversity.php'; ?>
            </div>
            <!-- /.tab-pane diversity -->

            <!-- Performance Tab -->
            <div class="tab-pane fade" id="performance" role="tabpanel">
              <?php include 'public/performance.php'; ?>
            </div>
            <!-- /.tab-pane performance -->

            <!-- Reports Tab -->
            <div class="tab-pane fade" id="reports" role="tabpanel">
              <?php include 'public/reports.php'; ?>
            </div>
            <!-- /.tab-pane reports -->

            <!-- Snapshots Tab -->
            <div class="tab-pane fade" id="snapshots" role="tabpanel">
              <?php include 'public/snapshots.php'; ?>
            </div>
            <!-- /.tab-pane snapshots -->

            <!-- Action Tracking Tab -->
            <div class="tab-pane fade" id="actions" role="tabpanel">
              <div class="container-fluid">
                <div class="row mt-3">
                  <div class="col-12">
                    <div class="card shadow-sm">
                      <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                          <i class="fas fa-tasks"></i> Performance Action Tracking
                        </h5>
                      </div>
                      <div class="card-body">
                        <p class="text-muted">Select an employee to view and manage their assigned performance actions.</p>
                        
                        <div class="form-group">
                          <label for="actionEmployeeSelect">Select Employee:</label>
                          <select class="form-control" id="actionEmployeeSelect" onchange="loadEmployeeActionTracking(this.value)">
                            <option value="">-- Choose Employee --</option>
                            <?php
                            require_once "../auth/database.php";
                            $db = \Database::getInstance()->getConnection();
                            $stmt = $db->query("SELECT employee_id, full_name FROM employees ORDER BY full_name");
                            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($employees as $emp) {
                              echo "<option value='{$emp['employee_id']}'>{$emp['full_name']}</option>";
                            }
                            ?>
                          </select>
                        </div>

                        <!-- Action Tracking Dashboard will load here -->
                        <div id="actionTrackingDashboard"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.tab-pane actions -->
          </div>
          <!-- /.tab-content -->
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
  <!-- ./wrapper -->
  <?php include "../layout/global_modal.php"; ?>
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
  <!-- Dashboard Analytics -->
  <script src="assets/dashboard.js"></script>

  <script>
    // WFA Dashboard Data Loader
    let wfaCharts = {
      departmentChart: null,
      genderChart: null,
      attritionChart: null,
      separationChart: null
    };

    async function loadWFADashboard() {
      try {
        const date = new Date().toISOString().split('T')[0];
        const basePath = '/capstone_hr_management_system/workforce';
        
        console.log('Loading WFA Dashboard for date:', date);
        
        // Fetch employee data (REQUIRED)
        console.log('Fetching employee data...');
        const empResponse = await fetch(`${basePath}/api/wfa/employees_data.php`);
        if (!empResponse.ok) throw new Error(`Employees API error: ${empResponse.status}`);
        const empText = await empResponse.text();
        let empData = {};
        try {
          empData = JSON.parse(empText);
        } catch (e) {
          console.error('Invalid JSON from employees API:', empText);
          throw new Error('Employees API returned invalid JSON');
        }
        console.log('Employee data:', empData);
        
        // Fetch insights (REQUIRED)
        console.log('Fetching insights...');
        const insightsResponse = await fetch(`${basePath}/api/wfa/insights_analytics.php`);
        if (!insightsResponse.ok) throw new Error(`Insights API error: ${insightsResponse.status}`);
        const insightsText = await insightsResponse.text();
        let insightsData = {};
        try {
          insightsData = JSON.parse(insightsText);
        } catch (e) {
          console.error('Invalid JSON from insights API:', insightsText);
          throw new Error('Insights API returned invalid JSON');
        }
        console.log('Insights data:', insightsData);

        // Fetch optional APIs with fallback
        let metricsData = { data: {} };
        let atRiskData = { data: [] };
        let attritionData = { data: {} };
        let deptData = { data: {} };
        let diversityData = { data: {} };

        // Try fetching metrics (optional)
        try {
          console.log('Fetching metrics...');
          const metricsResponse = await fetch(`${basePath}/api/wfa/dashboard_metrics.php?date=${date}`);
          if (metricsResponse.ok) {
            const metricsText = await metricsResponse.text();
            metricsData = JSON.parse(metricsText);
            console.log('Metrics data:', metricsData);
          }
        } catch (e) {
          console.warn('Metrics API unavailable:', e);
        }

        // Try fetching at-risk (optional)
        try {
          console.log('Fetching at-risk employees...');
          const atRiskResponse = await fetch(`${basePath}/api/wfa/at_risk_employees.php?limit=5&risk_level=high`);
          if (atRiskResponse.ok) {
            const atRiskText = await atRiskResponse.text();
            atRiskData = JSON.parse(atRiskText);
            console.log('At-risk data:', atRiskData);
          }
        } catch (e) {
          console.warn('At-risk API unavailable:', e);
        }

        // Try fetching attrition (optional)
        try {
          console.log('Fetching attrition metrics...');
          const attritionResponse = await fetch(`${basePath}/api/wfa/attrition_metrics.php`);
          if (attritionResponse.ok) {
            const attritionText = await attritionResponse.text();
            attritionData = JSON.parse(attritionText);
            console.log('Attrition data:', attritionData);
          }
        } catch (e) {
          console.warn('Attrition API unavailable:', e);
        }

        // Try fetching department (optional)
        try {
          console.log('Fetching department analytics...');
          const deptResponse = await fetch(`${basePath}/api/wfa/department_analytics.php?date=${date}`);
          if (deptResponse.ok) {
            const deptText = await deptResponse.text();
            deptData = JSON.parse(deptText);
            console.log('Department data:', deptData);
          }
        } catch (e) {
          console.warn('Department API unavailable:', e);
        }

        // Try fetching diversity (optional)
        try {
          console.log('Fetching diversity metrics...');
          const diversityResponse = await fetch(`${basePath}/api/wfa/diversity_metrics.php?date=${date}&category=gender`);
          if (diversityResponse.ok) {
            const diversityText = await diversityResponse.text();
            diversityData = JSON.parse(diversityText);
            console.log('Diversity data:', diversityData);
          }
        } catch (e) {
          console.warn('Diversity API unavailable:', e);
        }

        // Build the dashboard HTML
        buildDashboard(empData, metricsData, atRiskData, attritionData, deptData, diversityData, insightsData);
      } catch (error) {
        console.error('Error loading WFA dashboard:', error);
        const container = document.getElementById('dashboardContainer');
        container.innerHTML = `
          <div class="wfa-error" style="padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; color: #721c24;">
            <h4>Dashboard Loading Error</h4>
            <p>${error.message}</p>
            <p><small>Check browser console (F12) for more details</small></p>
          </div>
        `;
      }
    }

    function buildDashboard(empData, metricsData, atRiskData, attritionData, deptData, diversityData, insightsData) {
      // Handle different data structures
      let employees = [];
      if (Array.isArray(empData)) {
        employees = empData;
      } else if (empData.data && Array.isArray(empData.data.employees)) {
        employees = empData.data.employees;
      } else if (empData.data && Array.isArray(empData.data)) {
        employees = empData.data;
      }
      
      console.log('Extracted employees array:', employees);
      
      const metrics = metricsData.data?.employee_metrics || {};
      const atRiskCount = metricsData.data?.at_risk_count || 0;
      
      let html = `
        <!-- HR Dashboard Hero -->
        <div class="wfa-dashboard-hero" style="margin-bottom: 24px; padding: 24px; background: #f5f7ff; border-radius: 16px; box-shadow: 0 12px 24px rgba(33, 40, 82, 0.06);">
          <h2 style="margin: 0 0 10px; font-size: 28px; color: #253858;">HR Dashboard & Metrics</h2>
        </div>

        <!-- HR Metrics Cards -->
        <div class="wfa-metrics-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
          <div class="wfa-metric-card success" style="padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; color: white; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
            <div class="wfa-metric-icon" style="font-size: 24px; margin-bottom: 10px;">👥</div>
            <div class="wfa-metric-content">
              <h3 style="margin: 0 0 8px; font-size: 14px; opacity: 0.9;">Total Employees</h3>
              <p class="wfa-metric-value" style="margin: 0; font-size: 28px; font-weight: 700;">${employees.filter(e => e.employment_status === 'Active').length}</p>
            </div>
          </div>

          <div class="wfa-metric-card info" style="padding: 20px; background: linear-gradient(135deg, #f093fb 0%, #764ba2 100%); border-radius: 12px; color: white; box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);">
            <div class="wfa-metric-icon" style="font-size: 24px; margin-bottom: 10px;">📊</div>
            <div class="wfa-metric-content">
              <h3 style="margin: 0 0 8px; font-size: 14px; opacity: 0.9;">Attendance Rate</h3>
              <p class="wfa-metric-value" style="margin: 0; font-size: 28px; font-weight: 700;" id="attendance-rate">-</p>
            </div>
          </div>

          <div class="wfa-metric-card" style="padding: 20px; background: linear-gradient(135deg, #43e97b 0%, #764ba2 100%); border-radius: 12px; color: white; box-shadow: 0 4px 15px rgba(67, 233, 123, 0.3);">
            <div class="wfa-metric-icon" style="font-size: 24px; margin-bottom: 10px;">⭐</div>
            <div class="wfa-metric-content">
              <h3 style="margin: 0 0 8px; font-size: 14px; opacity: 0.9;">Performance Rating</h3>
              <p class="wfa-metric-value" style="margin: 0; font-size: 28px; font-weight: 700;" id="avg-performance">-</p>
            </div>
          </div>

          <div class="wfa-metric-card danger" style="padding: 20px; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border-radius: 12px; color: white; box-shadow: 0 4px 15px rgba(250, 112, 154, 0.3);">
            <div class="wfa-metric-icon" style="font-size: 24px; margin-bottom: 10px;">📈</div>
            <div class="wfa-metric-content">
              <h3 style="margin: 0 0 8px; font-size: 14px; opacity: 0.9;">Turnover Rate</h3>
              <p class="wfa-metric-value" style="margin: 0; font-size: 28px; font-weight: 700;" id="turnover-rate">-</p>
            </div>
          </div>
        </div>

        <!-- Charts Section -->
        <div class="wfa-charts-section" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-bottom: 30px;">
          <div class="wfa-chart-container" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 20px; color: #333; font-size: 18px;">Attendance Breakdown</h3>
            <canvas id="attendanceChart" style="max-height: 300px;"></canvas>
          </div>

          <div class="wfa-chart-container" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 20px; color: #333; font-size: 18px;">Performance Rating Distribution</h3>
            <canvas id="performanceChart" style="max-height: 300px;"></canvas>
          </div>

          <div class="wfa-chart-container" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 20px; color: #333; font-size: 18px;">Turnover Trend</h3>
            <canvas id="turnoverChart" style="max-height: 300px;"></canvas>
          </div>

          <div class="wfa-chart-container" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 20px; color: #333; font-size: 18px;">Employees by Department</h3>
            <canvas id="departmentChart" style="max-height: 300px;"></canvas>
          </div>
        </div>

        <!-- Export Actions -->
        <div class="wfa-export-actions" style="margin-top: 40px; padding: 25px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 12px; border-left: 5px solid #667eea;">
          <h3 style="margin-top: 0; margin-bottom: 20px; color: #333;">📥 Export Report</h3>
          <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <button class="wfa-btn wfa-btn-primary" onclick="exportDashboardPDF()">
              <i class="fas fa-file-pdf"></i> Export as PDF
            </button>
            <button class="wfa-btn wfa-btn-success" onclick="exportDashboardCSV()">
              <i class="fas fa-file-csv"></i> Export as CSV
            </button>
            <button class="wfa-btn wfa-btn-info" onclick="printDashboard()">
              <i class="fas fa-print"></i> Print Report
            </button>
          </div>
        </div>
      `;

      document.getElementById('dashboardContainer').innerHTML = html;

      // Load HR metrics and initialize charts
      loadHRMetrics();
      initializeHRCharts(employees);
    }

    // Load HR metrics from API
    function loadHRMetrics() {
      fetch('/capstone_hr_management_system/workforce/api/dashboard_metrics.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const metrics = data.data;
            
            // Update KPI cards with real data
            if (document.getElementById('attendance-rate')) {
              document.getElementById('attendance-rate').textContent = (metrics.attendance_rate || 0) + '%';
            }
            if (document.getElementById('avg-performance')) {
              document.getElementById('avg-performance').textContent = (metrics.employee_metrics?.avg_performance || 0).toFixed(2);
            }
            if (document.getElementById('turnover-rate')) {
              document.getElementById('turnover-rate').textContent = (metrics.separated_count || 0) + ' employees';
            }

            // Initialize charts with the metrics data
            if (metrics.attendance_breakdown && metrics.attendance_breakdown.length > 0) {
              loadAttendanceChart(metrics.attendance_breakdown);
            }
            if (metrics.performance_distribution && metrics.performance_distribution.length > 0) {
              loadPerformanceRatingChart(metrics.performance_distribution);
            }
            if (metrics.attendance_trend && metrics.attendance_trend.length > 0) {
              loadTurnoverChart(metrics.attendance_trend);
            }
          }
        })
        .catch(error => console.error('Error loading HR metrics:', error));
    }

    // Initialize HR-focused charts
    function initializeHRCharts(employees) {
      if (!employees || employees.length === 0) return;

      // Calculate department distribution
      const deptCounts = {};
      employees.forEach(emp => {
        deptCounts[emp.department] = (deptCounts[emp.department] || 0) + 1;
      });
      const deptLabels = Object.keys(deptCounts);
      const deptValues = Object.values(deptCounts);

      // Department Chart
      const deptCtx = document.getElementById('departmentChart')?.getContext('2d');
      if (deptCtx) {
        new Chart(deptCtx, {
          type: 'bar',
          data: {
            labels: deptLabels,
            datasets: [{
              label: 'Employee Count',
              data: deptValues,
              backgroundColor: '#667eea',
              borderColor: '#764ba2',
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
          }
        });
      }
    }

    // Load attendance breakdown chart
    function loadAttendanceChart(attendanceData) {
      if (!document.getElementById('attendanceChart')) return;

      const labels = attendanceData.map(item => item.status || item.category || 'Unknown');
      const counts = attendanceData.map(item => parseInt(item.count) || parseInt(item.percentage) || 0);
      const ctx = document.getElementById('attendanceChart').getContext('2d');

      // Destroy existing chart if it exists
      if (window.attendanceChartInstance) {
        window.attendanceChartInstance.destroy();
      }

      window.attendanceChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: counts,
            backgroundColor: ['#2ecc71', '#f1c40f', '#e74c3c', '#3498db', '#9b59b6', '#1abc9c'],
            borderColor: '#ffffff',
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      });
    }

    // Load performance rating distribution chart
    function loadPerformanceRatingChart(ratingData) {
      if (!document.getElementById('performanceChart')) return;

      const labels = ratingData.map(item => item.performance_level || item.rating || 'Unknown');
      const counts = ratingData.map(item => parseInt(item.count) || parseInt(item.percentage) || 0);
      const ctx = document.getElementById('performanceChart').getContext('2d');

      // Destroy existing chart if it exists
      if (window.performanceChartInstance) {
        window.performanceChartInstance.destroy();
      }

      window.performanceChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Rating Count',
            data: counts,
            backgroundColor: '#8e44ad',
            borderColor: '#6441a5',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    }

    // Load turnover trend chart
    function loadTurnoverChart(trendData) {
      if (!document.getElementById('turnoverChart')) return;

      const labels = trendData.map(item => item.month || item.date || 'Unknown');
      const counts = trendData.map(item => parseInt(item.count) || parseFloat(item.daily_rate) || 0);
      const ctx = document.getElementById('turnoverChart').getContext('2d');

      // Destroy existing chart if it exists
      if (window.turnoverChartInstance) {
        window.turnoverChartInstance.destroy();
      }

      window.turnoverChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: 'Daily Rate (%)',
            data: counts,
            borderColor: '#e67e22',
            backgroundColor: 'rgba(230, 126, 34, 0.2)',
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointBackgroundColor: '#e67e22'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: {
              display: true
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              max: 100
            }
          }
        }
      });
    }

    function initializeCharts(employees) {
      if (!employees || employees.length === 0) return;

      // Calculate department distribution
      const deptCounts = {};
      employees.forEach(emp => {
        deptCounts[emp.department] = (deptCounts[emp.department] || 0) + 1;
      });
      const deptLabels = Object.keys(deptCounts);
      const deptValues = Object.values(deptCounts);

      // Department Chart
      const deptCtx = document.getElementById('wfaDeptChart')?.getContext('2d');
      if (deptCtx) {
        new Chart(deptCtx, {
          type: 'bar',
          data: {
            labels: deptLabels,
            datasets: [{
              label: 'Employee Count',
              data: deptValues,
              backgroundColor: '#667eea',
              borderColor: '#764ba2',
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
          }
        });
      }

      // Status Distribution Chart (Pie)
      const statusCounts = {};
      employees.forEach(emp => {
        statusCounts[emp.employment_status] = (statusCounts[emp.employment_status] || 0) + 1;
      });
      const statusLabels = Object.keys(statusCounts);
      const statusValues = Object.values(statusCounts);

      const genderCtx = document.getElementById('wfaGenderChart')?.getContext('2d');
      if (genderCtx) {
        new Chart(genderCtx, {
          type: 'doughnut',
          data: {
            labels: statusLabels,
            datasets: [{
              data: statusValues,
              backgroundColor: ['#28a745', '#dc3545', '#ffc107']
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
          }
        });
      }

      // Tenure Distribution Chart (Line)
      const tenureCounts = {};
      employees.forEach(emp => {
        const tenure = Math.floor(emp.years_employed || 0);
        const group = tenure <= 1 ? '0-1 yr' : tenure <= 3 ? '1-3 yrs' : tenure <= 5 ? '3-5 yrs' : '5+ yrs';
        tenureCounts[group] = (tenureCounts[group] || 0) + 1;
      });
      const tenureLabels = ['0-1 yr', '1-3 yrs', '3-5 yrs', '5+ yrs'];
      const tenureValues = tenureLabels.map(label => tenureCounts[label] || 0);

      const attrCtx = document.getElementById('wfaAttritionChart')?.getContext('2d');
      if (attrCtx) {
        new Chart(attrCtx, {
          type: 'bar',
          data: {
            labels: tenureLabels,
            datasets: [{
              label: 'Employee Count',
              data: tenureValues,
              backgroundColor: '#00c9ff',
              borderColor: '#92fe9d',
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
          }
        });
      }

      // Position Distribution Chart (Pie)
      const posCounts = {};
      employees.forEach(emp => {
        posCounts[emp.position] = (posCounts[emp.position] || 0) + 1;
      });
      const posLabels = Object.keys(posCounts).slice(0, 5);
      const posValues = posLabels.map(pos => posCounts[pos]);

      const sepCtx = document.getElementById('wfaSeparationChart')?.getContext('2d');
      if (sepCtx) {
        new Chart(sepCtx, {
          type: 'pie',
          data: {
            labels: posLabels,
            datasets: [{
              data: posValues,
              backgroundColor: ['#f093fb', '#f5576c', '#4facfe', '#00f2fe', '#43e97b']
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
          }
        });
      }
    }

    // Load dashboard on page load and handle tab switching
    document.addEventListener('DOMContentLoaded', function() {
      loadWFADashboard();
      
      // Handle tab clicks - attach to the tab container
      const tabContainer = document.querySelector('ul.nav-pills.nav-sidebar');
      console.log('Tab container found:', !!tabContainer);
      if (tabContainer) {
        tabContainer.addEventListener('click', function(e) {
          const tabLink = e.target.closest('a[data-toggle="tab"]');
          if (tabLink) {
            e.preventDefault();
            const targetTab = tabLink.getAttribute('href').substring(1); // Remove #
            console.log('Tab clicked:', targetTab);
            
            // Remove active class from all tabs and links
            document.querySelectorAll('a[data-toggle="tab"]').forEach(link => {
              link.classList.remove('active');
            });
            document.querySelectorAll('.tab-pane').forEach(pane => {
              pane.classList.remove('show', 'active');
            });
            
            // Add active class to clicked tab and show target pane
            tabLink.classList.add('active');
            const targetPane = document.getElementById(targetTab);
            if (targetPane) {
              targetPane.classList.add('show', 'active');
              console.log('Showing tab pane:', targetTab);
            }
            
            // Call appropriate load function based on tab
            setTimeout(() => {
              if (targetTab === 'attrition' && typeof loadAttritionTab === 'function') {
                console.log('Loading attrition tab');
                loadAttritionTab();
              } else if (targetTab === 'diversity' && typeof loadDiversityTab === 'function') {
                console.log('Loading diversity tab');
                loadDiversityTab();
              } else if (targetTab === 'performance' && typeof loadPerformanceTab === 'function') {
                console.log('Loading performance tab');
                loadPerformanceTab();
              } else if (targetTab === 'reports' && typeof loadReportsTab === 'function') {
                console.log('Loading reports tab');
                loadReportsTab();
              } else if (targetTab === 'snapshots' && typeof loadSnapshotsTab === 'function') {
                console.log('Loading snapshots tab');
                loadSnapshotsTab();
              } else if (targetTab === 'actions') {
                console.log('Loading actions tab');
                // Action tracking tab loaded, user can select employee
              } else if (targetTab === 'dashboard' && typeof loadWFADashboard === 'function') {
                console.log('Loading dashboard tab');
                loadWFADashboard();
              } else {
                console.log('No matching function for tab:', targetTab);
              }
            }, 100);
          }
        });
      } else {
        console.log('Tab container not found');
      }
      
      const preloader = document.querySelector('.preloader');
      setTimeout(() => {
        if (preloader) {
          preloader.style.display = 'none';
        }
      }, 3000);
    });

    // Export Functions
    function exportDashboardPDF() {
      const basePath = '/capstone_hr_management_system/workforce';
      window.location.href = `${basePath}/api/wfa/generate_pdf_report.php?type=dashboard`;
    }

    function exportDashboardCSV() {
      const basePath = '/capstone_hr_management_system/workforce';
      let csv = 'WORKFORCE ANALYTICS DASHBOARD\n';
      csv += `Generated: ${new Date().toLocaleString()}\n\n`;
      csv += 'SUMMARY METRICS\n';
      csv += '===============\n';
      csv += `Report Date,${new Date().toISOString().split('T')[0]}\n`;
      csv += '\n';
      
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const link = document.createElement('a');
      const url = URL.createObjectURL(blob);
      link.setAttribute('href', url);
      link.setAttribute('download', `Dashboard_Report_${new Date().toISOString().split('T')[0]}.csv`);
      link.style.visibility = 'hidden';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    function printDashboard() {
      window.print();
    }

    // Load Employee Action Tracking
    async function loadEmployeeActionTracking(employeeId) {
      const container = document.getElementById('actionTrackingDashboard');
      
      if (!employeeId) {
        container.innerHTML = '<p class="text-muted">Select an employee to view their action tracking dashboard.</p>';
        return;
      }

      container.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>';

      try {
        const response = await fetch(`/capstone_hr_management_system/workforce/api/wfa/get_performance_actions.php?employee_id=${employeeId}`, {
          method: 'GET',
          credentials: 'include'  // Include cookies with cross-origin request
        });
        
        // Check if response is OK
        if (!response.ok) {
          throw new Error(`API returned status ${response.status}`);
        }
        
        // Check content type
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
          // If not JSON, log the response for debugging
          const text = await response.text();
          console.error('API returned non-JSON response:', text.substring(0, 200));
          throw new Error('API did not return JSON - got ' + (contentType || 'unknown content type'));
        }
        
        const result = await response.json();

        if (result.success && result.actions && result.actions.length > 0) {
          let html = `
            <div class="row mt-4 mb-4">
              <div class="col-md-2">
                <div class="small bg-light p-3 rounded text-center">
                  <h4>${result.summary.total_actions}</h4>
                  <small>Total Actions</small>
                </div>
              </div>
              <div class="col-md-2">
                <div class="small bg-info text-white p-3 rounded text-center">
                  <h4>${result.summary.pending}</h4>
                  <small>Pending</small>
                </div>
              </div>
              <div class="col-md-2">
                <div class="small bg-warning text-white p-3 rounded text-center">
                  <h4>${result.summary.ongoing}</h4>
                  <small>Ongoing</small>
                </div>
              </div>
              <div class="col-md-2">
                <div class="small bg-success text-white p-3 rounded text-center">
                  <h4>${result.summary.completed}</h4>
                  <small>Completed</small>
                </div>
              </div>
              <div class="col-md-2">
                <div class="small bg-danger text-white p-3 rounded text-center">
                  <h4>${result.summary.overdue}</h4>
                  <small>Overdue</small>
                </div>
              </div>
              <div class="col-md-2">
                <div class="small bg-danger text-white p-3 rounded text-center">
                  <h4>${result.summary.high_priority}</h4>
                  <small>High Priority</small>
                </div>
              </div>
            </div>

            <div class="card-group">
          `;

          result.actions.forEach(action => {
            const statusColor = action.status === 'completed' ? 'success' : (action.status === 'ongoing' ? 'info' : (action.status === 'pending' ? 'secondary' : 'danger'));
            const priorityColor = action.priority === 'CRITICAL' ? 'danger' : (action.priority === 'HIGH' ? 'warning' : (action.priority === 'MEDIUM' ? 'info' : 'success'));
            
            html += `
              <div class="card border-left-4 border-left-${priorityColor} mb-3">
                <div class="card-body">
                  <h6 class="card-title">${action.title}</h6>
                  <p class="card-text text-muted small">${action.description || 'No description'}</p>
                  
                  <div class="mb-2">
                    <small>Progress: ${action.progress_percentage}%</small>
                    <div class="progress" style="height: 8px;">
                      <div class="progress-bar" role="progressbar" style="width: ${action.progress_percentage}%" aria-valuenow="${action.progress_percentage}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>

                  <div class="row small">
                    <div class="col-6">
                      <strong>Status:</strong><br>
                      <span class="badge badge-${statusColor}">${action.status}</span>
                    </div>
                    <div class="col-6">
                      <strong>Days Remaining:</strong><br>
                      <span class="text-${action.days_remaining < 0 ? 'danger' : 'success'}">
                        ${action.days_remaining < 0 ? 'OVERDUE (' + Math.abs(action.days_remaining) + ')' : action.days_remaining + ' days'}
                      </span>
                    </div>
                  </div>

                  <div class="row small mt-2">
                    <div class="col-6">
                      <strong>Target Date:</strong><br>
                      ${new Date(action.dates.target).toLocaleDateString()}
                    </div>
                    <div class="col-6">
                      <strong>Priority:</strong><br>
                      <span class="badge badge-${priorityColor}">${action.priority}</span>
                    </div>
                  </div>
                  
                  <!-- Action Management Buttons -->
                  <div style="margin-top: 12px; display: flex; gap: 8px; flex-wrap: wrap;">
                    ${action.status !== 'completed' && action.status !== 'cancelled' ? `
                      ${action.status === 'pending' ? `
                        <button class="btn btn-sm btn-info" onclick="startAction(${action.action_id})" title="Start this action">
                          <i class="fas fa-play"></i> Start
                        </button>
                      ` : ''}
                      ${action.status !== 'pending' ? `
                        <button class="btn btn-sm btn-success" onclick="completeAction(${action.action_id})" title="Mark as completed">
                          <i class="fas fa-check"></i> Complete
                        </button>
                      ` : ''}
                      <button class="btn btn-sm btn-warning" onclick="openActionModal(${action.action_id})" title="View details and add notes">
                        <i class="fas fa-edit"></i> Details
                      </button>
                      <button class="btn btn-sm btn-danger" onclick="failAction(${action.action_id})" title="Mark as failed">
                        <i class="fas fa-times"></i> Fail
                      </button>
                    ` : `
                      <button class="btn btn-sm btn-secondary" onclick="openActionModal(${action.action_id})" title="View action summary">
                        <i class="fas fa-eye"></i> View
                      </button>
                    `}
                  </div>
                </div>
              </div>
            `;
          });

          html += `</div>`;
          container.innerHTML = html;
        } else {
          container.innerHTML = '<div class="alert alert-info"><i class="fas fa-check-circle"></i> No actions assigned yet.</div>';
        }
      } catch (error) {
        container.innerHTML = `<div class="alert alert-danger">Error loading actions: ${error.message}</div>`;
      }
    }
    
    // Start action (pending → ongoing)
    async function startAction(actionId) {
      if (!confirm('Start this action?')) return;
      await updateActionStatus(actionId, 'ongoing', 'Action started');
    }
    
    // Complete action (ongoing → completed)
    async function completeAction(actionId) {
      const note = prompt('Add a note for completion:');
      if (note === null) return;
      await updateActionStatus(actionId, 'completed', note || 'Action completed successfully');
    }
    
    // Fail action (ongoing → failed)
    async function failAction(actionId) {
      const reason = prompt('Reason for failure:');
      if (reason === null) return;
      await updateActionStatus(actionId, 'failed', 'Failed: ' + (reason || 'No reason specified'));
    }
    
    // Update action status
    async function updateActionStatus(actionId, newStatus, notes) {
      try {
        const response = await fetch('/capstone_hr_management_system/workforce/api/wfa/update_action_status.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'include',
          body: JSON.stringify({
            action_id: actionId,
            new_status: newStatus,
            notes: notes
          })
        });
        
        const result = await response.json();
        if (result.success) {
          alert('✓ Action ' + newStatus);
          // Reload the actions
          const employeeSelect = document.getElementById('actionEmployeeSelect');
          if (employeeSelect && employeeSelect.value) {
            loadEmployeeActionTracking(employeeSelect.value);
          }
        } else {
          alert('Error: ' + result.message);
        }
      } catch (error) {
        alert('Error updating action: ' + error.message);
      }
    }
    
    // Open detailed action modal
    async function openActionModal(actionId) {
      try {
        const response = await fetch(`/capstone_hr_management_system/workforce/api/wfa/get_action_progress.php?action_id=${actionId}`, {
          method: 'GET',
          credentials: 'include'
        });
        
        const result = await response.json();
        if (!result.success) {
          alert('Error: ' + result.message);
          return;
        }
        
        const action = result.action;
        const notesHtml = action.notes_history.map(note => `
          <div style="margin-bottom: 12px; padding: 10px; background: #f5f5f5; border-radius: 6px; border-left: 3px solid #2196f3;">
            <small style="color: #666; font-weight: bold;">${note.timestamp}</small><br>
            <small style="color: #333;">${note.note}</small>
          </div>
        `).join('');
        
        const statusBadgeColors = {
          'pending': 'secondary',
          'ongoing': 'info',
          'completed': 'success',
          'failed': 'danger',
          'cancelled': 'dark'
        };
        
        const modalHtml = `
          <div class="modal fade" id="actionDetailModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                  <h5 class="modal-title">Action Details - ${action.title}</h5>
                  <button type="button" class="close text-white" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <p><strong>Employee:</strong> ${action.employee_name}</p>
                      <p><strong>Department:</strong> ${action.department}</p>
                      <p><strong>Position:</strong> ${action.position}</p>
                    </div>
                    <div class="col-md-6">
                      <p><strong>Action Type:</strong> <span class="badge badge-info">${action.action_type}</span></p>
                      <p><strong>Status:</strong> <span class="badge badge-${statusBadgeColors[action.status]}">${action.status}</span></p>
                      <p><strong>Priority:</strong> <span class="badge badge-warning">${action.priority}</span></p>
                    </div>
                  </div>
                  
                  <hr>
                  
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <p><strong>Created:</strong> ${new Date(action.dates.created).toLocaleDateString()}</p>
                      <p><strong>Start Date:</strong> ${action.dates.start ? new Date(action.dates.start).toLocaleDateString() : 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                      <p><strong>Target Date:</strong> ${action.dates.target ? new Date(action.dates.target).toLocaleDateString() : 'N/A'}</p>
                      <p><strong>Completed:</strong> ${action.dates.completed ? new Date(action.dates.completed).toLocaleDateString() : 'N/A'}</p>
                    </div>
                  </div>
                  
                  <div class="progress mb-3" style="height: 25px;">
                    <div class="progress-bar ${action.progress_percentage >= 100 ? 'bg-success' : 'bg-info'}" 
                         style="width: ${action.progress_percentage}%">
                      ${action.progress_percentage}% Complete
                    </div>
                  </div>
                  
                  <p><strong>Reason:</strong> ${action.reason || 'N/A'}</p>
                  <p><strong>Description:</strong><br>${action.description || 'N/A'}</p>
                  
                  <hr>
                  
                  <h6 class="mb-2"><i class="fas fa-comments"></i> Progress Notes</h6>
                  ${notesHtml || '<p class="text-muted">No progress notes yet.</p>'}
                  
                  ${action.status !== 'completed' && action.status !== 'failed' && action.status !== 'cancelled' ? `
                    <hr>
                    <div class="form-group">
                      <label><strong>Add Progress Note:</strong></label>
                      <textarea id="progressNoteInput" class="form-control" rows="3" placeholder="Enter your progress update..."></textarea>
                    </div>
                  ` : ''}
                </div>
                <div class="modal-footer">
                  ${action.status !== 'completed' && action.status !== 'failed' && action.status !== 'cancelled' ? `
                    <button type="button" class="btn btn-primary" onclick="addProgressNote(${action.action_id})">
                      <i class="fas fa-save"></i> Add Note
                    </button>
                  ` : ''}
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
        `;
        
        // Remove old modal if exists
        const oldModal = document.getElementById('actionDetailModal');
        if (oldModal) oldModal.remove();
        
        // Add new modal to DOM
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Show modal
        $('#actionDetailModal').modal('show');
        
      } catch (error) {
        alert('Error loading action details: ' + error.message);
      }
    }
    
    // Add progress note
    async function addProgressNote(actionId) {
      const noteInput = document.getElementById('progressNoteInput');
      const note = noteInput.value.trim();
      
      if (!note) {
        alert('Please enter a note');
        return;
      }
      
      await updateActionStatus(actionId, null, note);
      // Re-open modal to refresh
      setTimeout(() => openActionModal(actionId), 1000);
    }
  </script>
</body>

</html>