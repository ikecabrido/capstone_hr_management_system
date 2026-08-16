<?php
session_start();
require_once __DIR__ . "/../auth/auth_check.php";
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

// Controllers are instantiated on-demand to avoid heavy model initialization on every request
// (this prevents DB schema repair calls from running for unrelated AJAX requests).
 $exitController = null;
 $resignationController = null;
 $terminationController = null;
 $interviewController = null;
 $transferController = null;
 $settlementController = null;
 $documentationController = null;
 $surveyController = null;

// Handle GET requests for document viewing and print views
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax_action'])) {
    $action = $_GET['ajax_action'];
    
    if (($action === 'print_settlement' && isset($_GET['settlement_id']))
        || ($action === 'print_interview' && isset($_GET['interview_id']))
        || ($action === 'print_transfer' && isset($_GET['plan_id']))
        || ($action === 'print_resignation' && isset($_GET['resignation_id']))) {
        header('Content-Type: text/html; charset=UTF-8');

        if ($action === 'print_settlement') {
          $settlementController = $settlementController ?? new SettlementController();
          echo $settlementController->renderSettlementPrintPage((int)$_GET['settlement_id']);
        } elseif ($action === 'print_interview') {
          $interviewController = $interviewController ?? new ExitInterviewController();
          echo $interviewController->renderInterviewPrintPage((int)$_GET['interview_id']);
        } elseif ($action === 'print_transfer') {
          $transferController = $transferController ?? new KnowledgeTransferController();
          echo $transferController->renderTransferPrintPage((int)$_GET['plan_id']);
        } else {
          $resignationController = $resignationController ?? new ResignationController();
          echo $resignationController->renderResignationPrintPage((int)$_GET['resignation_id']);
        }

        exit;
    }

    header('Content-Type: application/json');
    
    if ($action === 'view_document' && isset($_GET['document_id'])) {
      $documentationController = $documentationController ?? new DocumentationController();
      $response = $documentationController->viewDocument((int)$_GET['document_id']);
      echo json_encode($response);
      exit;
    } elseif ($action === 'serve_document' && isset($_GET['document_id'])) {
      // stream the document file for inline preview
      $documentationController = $documentationController ?? new DocumentationController();
      $documentationController->serveDocument((int)$_GET['document_id']);
      exit;
    } elseif ($action === 'download_document' && isset($_GET['document_id'])) {
      $documentationController = $documentationController ?? new DocumentationController();
      $response = $documentationController->downloadDocument((int)$_GET['document_id']);
      echo json_encode($response);
      exit;
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
  // For AJAX requests, don't echo PHP warnings into the JSON response; log them instead.
  ini_set('display_errors', 0);
  header('Content-Type: application/json');

  $action = $_POST['ajax_action'];
  $controller = $_POST['controller'] ?? 'exit_management';

  $data = $_POST;
  unset($data['ajax_action'], $data['controller']);

  error_log("=== AJAX REQUEST: action=$action, controller=$controller, data=" . json_encode($data) . " ===");

    switch ($controller) {
      case 'resignation':
        $resignationController = $resignationController ?? new ResignationController();
        $response = $resignationController->handleAjaxRequest($action, $data);
        break;
      case 'termination':
        $terminationController = $terminationController ?? new TerminationController();
        $response = $terminationController->handleAjaxRequest($action, $data);
        break;
      case 'interview':
        $interviewController = $interviewController ?? new ExitInterviewController();
        $response = $interviewController->handleAjaxRequest($action, $data);
        break;
      case 'transfer':
        $transferController = $transferController ?? new KnowledgeTransferController();
        $response = $transferController->handleAjaxRequest($action, $data);
        break;
      case 'settlement':
        $settlementController = $settlementController ?? new SettlementController();
        $response = $settlementController->handleAjaxRequest($action, $data);
        break;
      case 'documentation':
        $documentationController = $documentationController ?? new DocumentationController();
        $response = $documentationController->handleAjaxRequest($action, $data);
        break;
      case 'survey':
        $surveyController = $surveyController ?? new SurveyController();
        $response = $surveyController->handleAjaxRequest($action, $data);
        break;
      default:
        $exitController = $exitController ?? new ExitManagementController();
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
        <div class="dashboard-header">
          <h1>Dashboard</h1>
        </div>
        <div class="container-fluid">
          <!-- Dashboard Section -->
          <div id="dashboard-section" class="section">
            <div class="row">
              <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center kpi-card kpi-active" style="background: linear-gradient(180deg, #1f6ddb 0%, #1557b0 100%); color:#fff;">
                  <div class="card-body d-flex align-items-start">
                    <div>
                      <div class="kpi-value" id="active-exits">0</div>
                      <div class="kpi-label">Total Exited (this year)</div>
                    </div>
                    <i class="kpi-ghost fa fa-exchange-alt" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center kpi-card kpi-pending" style="background: linear-gradient(180deg, #2b90ff 0%, #1f6ddb 100%); color:#fff;">
                  <div class="card-body d-flex align-items-start">
                    <div>
                      <div class="kpi-value" id="pending-approval">0</div>
                      <div class="kpi-label">Avg notice period (days)</div>
                    </div>
                    <i class="kpi-ghost fa fa-calendar-alt" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center kpi-card kpi-upcoming" style="background: linear-gradient(180deg, #4aa3ff 0%, #2b90ff 100%); color:#fff;">
                  <div class="card-body d-flex align-items-start">
                    <div>
                      <div class="kpi-value" id="upcoming-exits-small">0</div>
                      <div class="kpi-label">Top resignation reason</div>
                    </div>
                    <i class="kpi-ghost fa fa-chart-pie" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center kpi-card kpi-settlements" style="background: linear-gradient(180deg, #1fb3a3 0%, #0f9b8a 100%); color:#fff;">
                  <div class="card-body d-flex align-items-start">
                    <div>
                      <div class="kpi-value" id="settlements-pending">0</div>
                      <div class="kpi-label">Settlements Pending</div>
                    </div>
                    <i class="kpi-ghost fa fa-hand-holding-usd" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Exit Process Pipeline</h3>
                  </div>
                  <div class="card-body" style="min-height:120px;">
                    <canvas id="exitPipelineChart" height="120"></canvas>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-lg-6 col-md-12 mb-3">
                <div class="card h-100">
                  <div class="card-header"><h3 class="card-title">Upcoming Last Working Dates</h3></div>
                  <div class="card-body p-2" style="min-height:160px;">
                    <ul class="list-unstyled" id="upcoming-exits-list">
                      <!-- fallback to table rows if needed -->
                    </ul>
                    <div class="mt-2 text-right"><button class="btn btn-sm btn-link" onclick="showSection('resignations', event)">View All Resignations</button></div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-md-12 mb-3">
                <div class="card h-100">
                  <div class="card-header d-flex justify-content-between align-items-center"><h3 class="card-title">Action Required</h3><button class="btn btn-sm btn-outline-primary" onclick="loadActionRequiredList()">Refresh</button></div>
                  <div class="card-body p-2" style="min-height:160px;">
                    <div class="list-group" id="action-required-list"></div>
                    <div class="mt-2 text-right"><button class="btn btn-sm btn-link" onclick="showSection('resignations', event)">Manage All</button></div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-12">
                <div class="card">
                  <div class="card-header"><h3 class="card-title">Recent / Active Exit Cases</h3></div>
                  <div class="card-body p-2">
                    <div class="table-responsive">
                      <table class="table table-sm table-hover mb-0">
                        <thead>
                          <tr>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Last Day</th>
                            <th>Stage</th>
                            <th>Status</th>
                            <th>View</th>
                          </tr>
                        </thead>
                        <tbody id="recent-active-tbody"></tbody>
                      </table>
                    </div>
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
                  <button id="open-archived-resignations" type="button" class="btn btn-warning btn-sm mr-2" onclick="openArchivedResignationsModal()">
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
                  <button type="button" class="btn btn-success btn-sm mr-2" onclick="showTerminationModal()">
                    <i class="fas fa-plus"></i> New Termination
                  </button>
                  <button id="open-archived-terminations" type="button" class="btn btn-warning btn-sm" onclick="openArchivedTerminationsModal()">
                    <i class="fas fa-archive"></i> Archive
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
                    <option value="pending_approval">Pending Approval</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                  </select>
                </div>
                <div class="card-tools d-flex align-items-center">
                  <button type="button" class="btn btn-warning btn-sm mr-2" onclick="openArchivedSettlementsModal()">
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
                    <!-- Main UI document search hidden: printing search lives in the Print modal -->
                    <input type="text" id="document-search" class="form-control" placeholder="Search documents..." onkeyup="onDocumentSearchChange()" style="display:none;">
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
                  <button type="button" class="btn btn-info btn-sm" onclick="openPrintSelectorModal()">
                    <i class="fas fa-print"></i> Print
                  </button>
                </div>
              </div>
              <div class="card-body">
                <table id="documents-table" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Employee</th>
                      <th>Case Type</th>
                      <th>Exit Reason</th>
                      <th>Notice / Exit Date</th>
                      <th>Status</th>
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
                  <button id="showSurveyModalBtn" type="button" class="btn btn-success btn-sm" onclick="showSurveyModal()" style="display: inline-block;" aria-label="Schedule Post-Exit Survey">
                    <i class="fas fa-plus"></i> Schedule Survey
                  </button>
                </div>
              </div>
              <div class="card-body">
                <table id="surveys-table" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Employee</th>
                      <th>Survey Title</th>
                      <th>Schedule Date</th>
                      <th>Schedule Time</th>
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

      // Debug / fix: ensure surveys action buttons are visible when surveys section is shown
      if (sectionName === 'surveys') {
        try {
          const scheduleBtn = document.querySelector('#surveys-section .card-tools .btn-success');
          if (scheduleBtn) {
            scheduleBtn.style.display = '';
            console.log('Survey schedule button present and unhidden');
          } else {
            console.log('Survey schedule button NOT found in DOM');
          }
        } catch (e) {
          console.error('Error ensuring survey button visible', e);
        }
      }
    }

    // Initialize
    $(document).ready(function() {
      loadDashboardData();
    });
  </script>
</body>

</html>