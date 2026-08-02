<?php
session_start();
require_once "../auth/auth_check.php";
require_once "controllers/ExitManagementController.php";
require_once "controllers/ResignationController.php";
require_once "controllers/TerminationController.php";
require_once "controllers/ExitInterviewController.php";
require_once "controllers/KnowledgeTransferController.php";
require_once "controllers/SettlementController.php";
require_once "controllers/DocumentationController.php";
require_once "controllers/SurveyController.php";

$theme = $_SESSION['user']['theme'] ?? 'light';
session_write_close();

// Initialize controllers
$exitController = new ExitManagementController();
$resignationController = new ResignationController();
$terminationController = new TerminationController();
$interviewController = new ExitInterviewController();
$transferController = new KnowledgeTransferController();
$settlementController = new SettlementController();
$documentationController = new DocumentationController();
$surveyController = new SurveyController();

// Handle GET requests for document viewing
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax_action'])) {
    header('Content-Type: application/json');
    
    $action = $_GET['ajax_action'];
    
    if ($action === 'view_document' && isset($_GET['document_id'])) {
        $response = $documentationController->viewDocument((int)$_GET['document_id']);
        echo json_encode($response);
        exit;
    } elseif ($action === 'download_document' && isset($_GET['document_id'])) {
        $response = $documentationController->downloadDocument((int)$_GET['document_id']);
        echo json_encode($response);
        exit;
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');

    $action = $_POST['ajax_action'];
    $controller = $_POST['controller'] ?? 'exit_management';

    $data = $_POST;
    unset($data['ajax_action'], $data['controller']);

    error_log("=== AJAX REQUEST: action=$action, controller=$controller ===");

    switch ($controller) {
        case 'resignation':
            $response = $resignationController->handleAjaxRequest($action, $data);
            break;
        case 'termination':
            $response = $terminationController->handleAjaxRequest($action, $data);
            break;
        case 'interview':
            $response = $interviewController->handleAjaxRequest($action, $data);
            break;
        case 'transfer':
            $response = $transferController->handleAjaxRequest($action, $data);
            break;
        case 'settlement':
            $response = $settlementController->handleAjaxRequest($action, $data);
            break;
        case 'documentation':
            $response = $documentationController->handleAjaxRequest($action, $data);
            break;
        case 'survey':
            $response = $surveyController->handleAjaxRequest($action, $data);
            break;
        default:
            $response = $exitController->handleAjaxRequest($action, $data);
    }

    error_log("=== AJAX RESPONSE: " . json_encode($response) . " ===");
    echo json_encode($response);
    exit;
}

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Exit Management System</title>

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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
  <link rel="stylesheet" href="../layout/toast.css" />
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
  <script>
    window.exitManagementUserRole = <?= json_encode($_SESSION['user']['role'] ?? '') ?>;
  </script>
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
          <a href="exit_management.php" class="nav-link">Home</a>
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
      <a href="exit_management.php" class="brand-link">
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
            <li class="nav-item">
              <a href="#dashboard" class="nav-link active" onclick="showSection('dashboard', event)">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#resignations" class="nav-link" onclick="showSection('resignations', event)">
                <i class="nav-icon fas fa-user-times"></i>
                <p>Resignations</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#terminations" class="nav-link" onclick="showSection('terminations', event)">
                <i class="nav-icon fas fa-gavel"></i>
                <p>Terminations</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#interviews" class="nav-link" onclick="showSection('interviews', event)">
                <i class="nav-icon fas fa-comments"></i>
                <p>Exit Interviews</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#transfers" class="nav-link" onclick="showSection('transfers', event)">
                <i class="nav-icon fas fa-exchange-alt"></i>
                <p>Knowledge Transfer</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#settlements" class="nav-link" onclick="showSection('settlements', event)">
                <i class="nav-icon fas fa-calculator"></i>
                <p>Settlements</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#documents" class="nav-link" onclick="showSection('documents', event)">
                <i class="nav-icon fas fa-file-alt"></i>
                <p>Documentation</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#surveys" class="nav-link" onclick="showSection('surveys', event)">
                <i class="nav-icon fas fa-poll"></i>
                <p>Post-Exit Surveys</p>
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

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Exit Management System</h1>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Dashboard Section -->
          <div id="dashboard-section" class="section">
            <div class="row dashboard-box-row">
              <div class="dashboard-box-col">
                <div class="small-box bg-info">
                  <div class="inner">
                    <h3 id="pending-resignations">0</h3>
                    <p>Pending Resignations</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-user-times"></i>
                  </div>
                </div>
              </div>
              <div class="dashboard-box-col">
                <div class="small-box bg-success">
                  <div class="inner">
                    <h3 id="scheduled-interviews">0</h3>
                    <p>Scheduled Interviews</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-comments"></i>
                  </div>
                </div>
              </div>
              <div class="dashboard-box-col">
                <div class="small-box bg-warning">
                  <div class="inner">
                    <h3 id="active-transfers">0</h3>
                    <p>Active Transfers</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-exchange-alt"></i>
                  </div>
                </div>
              </div>
              <div class="dashboard-box-col">
                <div class="small-box bg-danger">
                  <div class="inner">
                    <h3 id="pending-settlements">0</h3>
                    <p>Pending Settlements</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-calculator"></i>
                  </div>
                </div>
              </div>
              <div class="dashboard-box-col">
                <div class="small-box bg-primary">
                  <div class="inner">
                    <h3 id="approved-preclearances">0</h3>
                    <p>Payroll Approved</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-receipt"></i>
                  </div>
                </div>
              </div>
            </div>

            <div id="payroll-approval-notification-row" class="row mt-3" style="display: none;">
              <div class="col-12">
                <div class="card card-outline card-primary">
                  <div class="card-header">
                    <h3 class="card-title">Recent Payroll Pre-Clearance Approvals</h3>
                  </div>
                  <div class="card-body p-3">
                    <div class="table-responsive">
                      <table class="table table-sm table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Settlement Date</th>
                            <th>Net Payable</th>
                            <th>Approved At</th>
                          </tr>
                        </thead>
                        <tbody id="payroll-approval-notification-body"></tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Charts Row -->
            <!-- Charts: top row reasons/status, bottom row trends (2 + 2) -->
            <div class="row mt-4">
              <div class="col-lg-6 col-md-6 mb-3">
                <div class="card h-100">
                  <div class="card-header">
                    <h3 class="card-title">Resignation Reasons</h3>
                  </div>
                  <div class="card-body" style="min-height:260px;">
                    <canvas id="resignationReasonsChart" height="240"></canvas>
                  </div>
                </div>
              </div>

              <div class="col-lg-6 col-md-6 mb-3">
                <div class="card h-100">
                  <div class="card-header">
                    <h3 class="card-title">Termination Status</h3>
                  </div>
                  <div class="card-body" style="min-height:260px;">
                    <canvas id="terminationStatusChart" height="240"></canvas>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-4">
              <div class="col-lg-6 col-md-6 mb-3">
                <div class="card h-100">
                  <div class="card-header">
                    <h3 class="card-title">Resignation Trend</h3>
                  </div>
                  <div class="card-body" style="min-height:260px;">
                    <canvas id="resignationTrendChart" height="240"></canvas>
                  </div>
                </div>
              </div>

              <div class="col-lg-6 col-md-6 mb-3">
                <div class="card h-100">
                  <div class="card-header">
                    <h3 class="card-title">Termination Trend</h3>
                  </div>
                  <div class="card-body" style="min-height:260px;">
                    <canvas id="terminationTrendChart" height="240"></canvas>
                  </div>
                </div>
              </div>
            </div>

            <!-- Exit Status below (full width) -->
            <div class="row mt-4">
              <div class="col-12">
                <div class="card h-100">
                  <div class="card-header">
                    <h3 class="card-title">Exit Status Overview</h3>
                  </div>
                  <div class="card-body" style="min-height:260px;">
                    <canvas id="exitStatusChart" height="240"></canvas>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Resignations -->
            <div class="row mt-4">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Recent Resignations</h3>
                    <div class="card-tools">
                      <span class="badge badge-info" id="recent-count">0</span>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="recent-resignations-table" class="table table-bordered table-striped table-sm">
                        <colgroup>
                          <col style="width: 14%;">
                          <col style="width: 11%;">
                          <col style="width: 10%;">
                          <col style="width: 15%;">
                          <col style="width: 13%;">
                          <col style="width: 13%;">
                          <col style="width: 13%;">
                          <col style="width: 11%;">
                        </colgroup>
                        <thead>
                          <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Type</th>
                            <th>Reason</th>
                            <th>Notice Date</th>
                            <th>Last Working Date</th>
                            <th>Status</th>
                            <th>Days Left</th>
                          </tr>
                        </thead>
                        <tbody id="recent-resignations-tbody">
                          <tr><td colspan="8" class="text-center text-muted">Loading...</td></tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Key Metrics -->
            <div class="row mt-4 dashboard-box-row">
              <div class="dashboard-box-col">
                <div class="small-box bg-primary">
                  <div class="inner">
                    <h3 id="total-exited">0</h3>
                    <p>Total Exited (This Year)</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-sign-out-alt"></i>
                  </div>
                </div>
              </div>
              <div class="dashboard-box-col">
                <div class="small-box bg-info">
                  <div class="inner">
                    <h3 id="avg-notice">0</h3>
                    <p>Avg Notice Period (Days)</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-calendar"></i>
                  </div>
                </div>
              </div>
              <div class="dashboard-box-col">
                <div class="small-box bg-success">
                  <div class="inner">
                    <h3 id="top-reason">--</h3>
                    <p>Top Resignation Reason</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-chart-pie"></i>
                  </div>
                </div>
              </div>
              <div class="dashboard-box-col">
                <div class="small-box bg-warning">
                  <div class="inner">
                    <h3 id="avg-interviews">0%</h3>
                    <p>Interviews Completed</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-percentage"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Resignations Section -->
          <div id="resignations-section" class="section" style="display: none;">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2" style="flex: 1;">
                  <!-- Search Bar -->
                  <div class="input-group input-group-sm" style="flex: 19;">
                    <input type="text" id="resignation-search" class="form-control" placeholder="Search resignations..." onkeyup="onResignationSearchChange()">
                    <div class="input-group-append">
                      <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                  </div>
                  <!-- Filter -->
                  <select id="resignation-status-filter" class="form-control form-control-sm" onchange="onResignationStatusFilterChange()" style="flex: 1; white-space: nowrap;">
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="pending_review">Pending Review</option>
                    <option value="pending_legal_review">Pending Legal Review</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="rejected_by_legal">Rejected by Legal</option>
                    <option value="withdrawn">Withdrawn</option>
                    <option value="archived">Archived</option>
                    <option value="all">All</option>
                  </select>
                </div>
                <div class="card-tools d-flex align-items-center">
                  <button type="button" class="btn btn-warning btn-sm mr-2" onclick="toggleArchivedResignations()">
                    <i class="fas fa-archive"></i> Archive
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="resignations-table" class="table table-bordered table-striped table-sm">
                    <colgroup>
                      <col style="width: 15%;">
                      <col style="width: 8%;">
                      <col style="width: 14%;">
                      <col style="width: 10%;">
                      <col style="width: 11%;">
                      <col style="width: 8%;">
                      <col style="width: 10%;">
                      <col style="width: 8%;">
                      <col style="width: 10%;">
                      <col style="width: 6%;">
                    </colgroup>
                    <thead>
                      <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Position</th>
                        <th>Reason</th>
                        <th>Notice Date</th>
                        <th>Last Working Date</th>
                        <th>Comments</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody id="resignations-tbody">
                      <!-- Data will be loaded here -->
                    </tbody>
                  </table>
                </div>

                <div id="archived-resignations-container" class="mt-4" style="display: none;">
                  <h5>Archived Resignations</h5>
                  <div class="table-responsive">
                    <table id="archived-resignations-table" class="table table-bordered table-striped table-sm">
                      <colgroup>
                        <col style="width: 15%;">
                        <col style="width: 8%;">
                        <col style="width: 14%;">
                        <col style="width: 10%;">
                        <col style="width: 11%;">
                        <col style="width: 8%;">
                        <col style="width: 10%;">
                        <col style="width: 8%;">
                        <col style="width: 10%;">
                        <col style="width: 6%;">
                      </colgroup>
                      <thead>
                        <tr>
                          <th>Employee</th>
                          <th>Department</th>
                          <th>Email</th>
                          <th>Position</th>
                          <th>Reason</th>
                          <th>Notice Date</th>
                          <th>Last Working Date</th>
                          <th>Comments</th>
                          <th>Status</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody id="archived-resignations-tbody">
                        <!-- Data will be loaded here -->
                      </tbody>
                    </table>
                  </div>
                  <div id="archived-resignations-pagination" class="mt-2 d-flex justify-content-end"></div>
                </div>

                <!-- Pagination for main resignations table -->
                <div id="resignations-pagination" class="mt-3 d-flex justify-content-between align-items-center"></div>
              </div>
            </div>
          </div>

          <!-- Terminations Section -->
          <div id="terminations-section" class="section" style="display: none;">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2" style="flex: 1;">
                  <div class="input-group input-group-sm" style="flex: 19;">
                    <input type="text" id="termination-search" class="form-control" placeholder="Search terminations..." onkeyup="onTerminationSearchChange()">
                    <div class="input-group-append">
                      <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                  </div>
                  <select id="termination-status-filter" class="form-control form-control-sm" onchange="onTerminationStatusFilterChange()" style="flex: 1; white-space: nowrap;">
                    <option value="active">Active</option>
                    <option value="pending_review">Pending Review</option>
                    <option value="pending_legal_review">Pending Legal Review</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="rejected_by_legal">Rejected by Legal</option>
                    <option value="withdrawn">Withdrawn</option>
                    <option value="archived">Archived</option>
                    <option value="all">All</option>
                  </select>
                </div>
                <div class="card-tools d-flex align-items-center">
                  <button type="button" class="btn btn-success btn-sm" onclick="showTerminationModal()">
                    <i class="fas fa-plus"></i> New Termination
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="terminations-table" class="table table-bordered table-striped table-sm">
                    <colgroup>
                      <col style="width: 14%;">
                      <col style="width: 10%;">
                      <col style="width: 14%;">
                      <col style="width: 10%;">
                      <col style="width: 10%;">
                      <col style="width: 10%;">
                      <col style="width: 16%;">
                      <col style="width: 8%;">
                    </colgroup>
                    <thead>
                      <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Position</th>
                        <th>Reason</th>
                        <th>Effective Date</th>
                        <th>Comments</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody id="terminations-tbody">
                      <!-- Data will be loaded here -->
                    </tbody>
                  </table>
                </div>
                <div id="terminations-pagination" class="mt-3 d-flex justify-content-between align-items-center"></div>
              </div>
            </div>
          </div>

          <!-- Interviews Section -->
          <div id="interviews-section" class="section" style="display: none;">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2" style="flex: 1;">
                  <!-- Search Bar -->
                  <div class="input-group input-group-sm" style="flex: 19;">
                    <input type="text" id="interview-search" class="form-control" placeholder="Search interviews..." onkeyup="onInterviewSearchChange()">
                    <div class="input-group-append">
                      <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                  </div>
                  <!-- Filter -->
                  <select id="interview-status-filter" class="form-control form-control-sm" onchange="onInterviewStatusFilterChange()" style="flex: 1; white-space: nowrap;">
                    <option value="all">All</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="completed">Completed</option>
                    <option value="pending">Pending</option>
                  </select>
                </div>
                <div class="card-tools d-flex align-items-center">
                  <button type="button" id="viewArchivedInterviewsButton" class="btn btn-warning btn-sm mr-2" onclick="openArchiveModal()">
                    <i class="fas fa-box-open"></i> Archived <span id="archive-notif-count" class="badge badge-danger ml-1" style="display:none;">0</span>
                  </button>
                  <button type="button" class="btn btn-success btn-sm" onclick="showInterviewModal()">
                    <i class="fas fa-plus"></i> Add
                  </button>
                </div>
              </div>
              <div class="card-body">
                <table id="interviews-table" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Employee</th>
                      <th>Interviewer</th>
                      <th>Scheduled Date</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="interviews-tbody">
                    <!-- Data will be loaded here -->
                  </tbody>
                </table>

              </div>
            </div>
          </div>

          <!-- Transfers Section -->
          <div id="transfers-section" class="section" style="display: none;">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2" style="flex: 1;">
                  <!-- Search Bar -->
                  <div class="input-group input-group-sm" style="flex: 19;">
                    <input type="text" id="transfer-search" class="form-control" placeholder="Search transfers..." onkeyup="onTransferSearchChange()">
                    <div class="input-group-append">
                      <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                  </div>
                  <!-- Filter -->
                  <select id="transfer-status-filter" class="form-control form-control-sm" onchange="onTransferStatusFilterChange()" style="flex: 1; white-space: nowrap;">
                    <option value="all">All</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                  </select>
                </div>
                <div class="card-tools d-flex align-items-center">
                  <button type="button" class="btn btn-warning btn-sm mr-2 position-relative" onclick="archiveTransfers()">
                    <i class="fas fa-archive"></i> Archive
                    <span id="transfer-archive-notif-count" class="badge badge-danger archive-count-badge" style="display:none; position:absolute; top:0; right:0; transform: translate(50%, -50%);">0</span>
                  </button>
                  <button type="button" class="btn btn-success btn-sm" onclick="showTransferModal()">
                    <i class="fas fa-plus"></i> Add
                  </button>
                </div>
              </div>
              <div class="card-body">
                <table id="transfers-table" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Employee</th>
                      <th>Successor</th>
                      <th>Start Date</th>
                      <th>End Date</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="transfers-tbody">
                    <!-- Data will be loaded here -->
                  </tbody>
                </table>
                <div id="transfers-pagination" class="d-flex justify-content-center mt-3"></div>
              </div>
            </div>
          </div>

          <!-- Settlements Section -->
          <div id="settlements-section" class="section" style="display: none;">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2" style="flex: 1;">
                  <!-- Search Bar -->
                  <div class="input-group input-group-sm" style="flex: 19;">
                    <input type="text" id="settlement-search" class="form-control" placeholder="Search settlements..." onkeyup="onSettlementSearchChange()">
                    <div class="input-group-append">
                      <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                  </div>
                  <!-- Filter -->
                  <select id="settlement-status-filter" class="form-control form-control-sm" onchange="onSettlementStatusFilterChange()" style="flex: 1; white-space: nowrap;">
                    <option value="all">All</option>
                    <option value="draft">Draft</option>
                    <option value="pending_approval">Pending Approval</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                  </select>
                </div>
                <div class="card-tools d-flex align-items-center">
                  <button type="button" class="btn btn-warning btn-sm mr-2" onclick="archiveSettlements()">
                    <i class="fas fa-archive"></i> Archive
                  </button>
                  <button type="button" class="btn btn-danger btn-sm" onclick="showSettlementModal()">
                    <i class="fas fa-plus"></i> Add
                  </button>
                </div>
              </div>
              <div class="card-body">
                <table id="settlements-table" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Employee</th>
                      <th>Settlement Date</th>
                      <th>Net Payable</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="settlements-tbody">
                    <!-- Data will be loaded here -->
                  </tbody>
                </table>
                <div id="settlements-pagination" class="d-flex justify-content-center mt-3"></div>
              </div>
            </div>
          </div>

          <!-- Documents Section -->
          <div id="documents-section" class="section" style="display: none;">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2" style="flex: 1;">
                  <!-- Search Bar -->
                  <div class="input-group input-group-sm" style="flex: 19;">
                    <input type="text" id="document-search" class="form-control" placeholder="Search documents..." onkeyup="onDocumentSearchChange()">
                    <div class="input-group-append">
                      <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                  </div>
                  <!-- Filter -->
                  <select id="document-status-filter" class="form-control form-control-sm" onchange="onDocumentStatusFilterChange()" style="flex: 1; white-space: nowrap;">
                    <option value="all">All</option>
                    <option value="active">Active</option>
                    <option value="deleted">Deleted</option>
                  </select>
                </div>
                <div class="card-tools d-flex align-items-center">
                  <button type="button" class="btn btn-warning btn-sm mr-2" onclick="archiveDocuments()">
                    <i class="fas fa-archive"></i> Archive
                  </button>
                  <button type="button" class="btn btn-info btn-sm" onclick="showDocumentModal()">
                    <i class="fas fa-plus"></i> Add
                  </button>
                </div>
              </div>
              <div class="card-body">
                <table id="documents-table" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Employee</th>
                      <th>Document Type</th>
                      <th>Title</th>
                      <th>Upload Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="documents-tbody">
                    <!-- Data will be loaded here -->
                  </tbody>
                </table>
                <div id="documents-pagination" class="d-flex justify-content-center mt-3"></div>
              </div>
            </div>
          </div>

          <!-- Surveys Section -->
          <div id="surveys-section" class="section" style="display: none;">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2" style="flex: 1;">
                  <!-- Search Bar -->
                  <div class="input-group input-group-sm" style="flex: 19;">
                    <input type="text" id="survey-search" class="form-control" placeholder="Search surveys..." onkeyup="onSurveySearchChange()">
                    <div class="input-group-append">
                      <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                  </div>
                  <!-- Filter -->
                  <select id="survey-status-filter" class="form-control form-control-sm" onchange="onSurveyStatusFilterChange()" style="flex: 1; white-space: nowrap;">
                    <option value="all">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                  </select>
                </div>
                <div class="card-tools d-flex align-items-center">
                  <button type="button" class="btn btn-warning btn-sm mr-2" onclick="archiveSurveys()">
                    <i class="fas fa-archive"></i> Archive
                  </button>

                </div>
              </div>
              <div class="card-body">
                <table id="surveys-table" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Survey Title</th>
                      <th>Start Date</th>
                      <th>End Date</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="surveys-tbody">
                    <!-- Data will be loaded here -->
                  </tbody>
                </table>
                <div id="surveys-pagination" class="d-flex justify-content-center mt-3"></div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <?php include "../layout/global_modal.php"; ?>
    <?php include "modals.php"; ?>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>

  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->
  <script src="../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <script src="../assets/dist/js/adminlte.js"></script>
  <script src="../assets/dist/js/theme.js"></script>
  <script src="../assets/dist/js/time.js"></script>
  <script src="../assets/dist/js/global_modal.js"></script>
  <script src="../assets/dist/js/profile.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="custom.js"></script>

  <script>
    // Section navigation
    function showSection(sectionName, event) {
      if (event && typeof event.preventDefault === 'function') {
        event.preventDefault();
      }

      // Hide all sections
      document.querySelectorAll('.section').forEach(section => {
        section.style.display = 'none';
      });

      // Show selected section
      const sectionElement = document.getElementById(sectionName + '-section');
      if (sectionElement) {
        sectionElement.style.display = 'block';
      }

      // Update active nav link
      document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
      });
      if (event && event.target) {
        const navLink = event.target.closest('.nav-link');
        if (navLink) {
          navLink.classList.add('active');
        }
      }

      // Load data for the section
      loadSectionData(sectionName);
    }

    // Initialize
    $(document).ready(function() {
      loadDashboardData();
    });
  </script>
</body>

</html>