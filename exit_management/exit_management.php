<?php
session_start();
require_once "../auth/auth_check.php";
require_once "controllers/ExitManagementController.php";
require_once "controllers/ResignationController.php";
require_once "controllers/ExitInterviewController.php";
require_once "controllers/KnowledgeTransferController.php";
require_once "controllers/SettlementController.php";
require_once "controllers/DocumentationController.php";
require_once "controllers/SurveyController.php";

$theme = $_SESSION['user']['theme'] ?? 'light';

// Initialize controllers
$exitController = new ExitManagementController();
$resignationController = new ResignationController();
$interviewController = new ExitInterviewController();
$transferController = new KnowledgeTransferController();
$settlementController = new SettlementController();
$documentationController = new DocumentationController();
$surveyController = new SurveyController();

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
  <link rel="stylesheet" href="../layout/toast.css" />
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
              <a href="#dashboard" class="nav-link active" onclick="showSection('dashboard')">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#resignations" class="nav-link" onclick="showSection('resignations')">
                <i class="nav-icon fas fa-user-times"></i>
                <p>Resignations</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#interviews" class="nav-link" onclick="showSection('interviews')">
                <i class="nav-icon fas fa-comments"></i>
                <p>Exit Interviews</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#transfers" class="nav-link" onclick="showSection('transfers')">
                <i class="nav-icon fas fa-exchange-alt"></i>
                <p>Knowledge Transfer</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#settlements" class="nav-link" onclick="showSection('settlements')">
                <i class="nav-icon fas fa-calculator"></i>
                <p>Settlements</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#documents" class="nav-link" onclick="showSection('documents')">
                <i class="nav-icon fas fa-file-alt"></i>
                <p>Documentation</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#surveys" class="nav-link" onclick="showSection('surveys')">
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
            <div class="row">
              <div class="col-lg-3 col-6">
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
              <div class="col-lg-3 col-6">
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
              <div class="col-lg-3 col-6">
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
              <div class="col-lg-3 col-6">
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
            </div>
          </div>

          <!-- Resignations Section -->
          <div id="resignations-section" class="section" style="display: none;">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Resignation Management</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-primary" onclick="showResignationModal()">
                    <i class="fas fa-plus"></i> New Resignation
                  </button>
                </div>
              </div>
              <div class="card-body">
                <table id="resignations-table" class="table table-bordered table-striped table-sm">
                  <thead>
                    <tr>
                      <th>Employee</th>
                      <th>Department</th>
                      <th>Email</th>
                      <th>Type</th>
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
            </div>
          </div>

          <!-- Interviews Section -->
          <div id="interviews-section" class="section" style="display: none;">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Exit Interviews</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-success" onclick="showInterviewModal()">
                    <i class="fas fa-plus"></i> Schedule Interview
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
              <div class="card-header">
                <h3 class="card-title">Knowledge Transfer</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-warning" onclick="showTransferModal()">
                    <i class="fas fa-plus"></i> Create Transfer Plan
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
              </div>
            </div>
          </div>

          <!-- Settlements Section -->
          <div id="settlements-section" class="section" style="display: none;">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Final Settlements</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-danger" onclick="showSettlementModal()">
                    <i class="fas fa-plus"></i> Calculate Settlement
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
              </div>
            </div>
          </div>

          <!-- Documents Section -->
          <div id="documents-section" class="section" style="display: none;">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Documentation Management</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-info" onclick="showDocumentModal()">
                    <i class="fas fa-plus"></i> Upload Document
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
              </div>
            </div>
          </div>

          <!-- Surveys Section -->
          <div id="surveys-section" class="section" style="display: none;">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Post-Exit Surveys</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-primary" onclick="showSurveyModal()">
                    <i class="fas fa-plus"></i> Create Survey
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
  <script src="custom.js"></script>

  <script>
    // Section navigation
    function showSection(sectionName) {
      // Hide all sections
      document.querySelectorAll('.section').forEach(section => {
        section.style.display = 'none';
      });

      // Show selected section
      document.getElementById(sectionName + '-section').style.display = 'block';

      // Update active nav link
      document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
      });
      event.target.closest('.nav-link').classList.add('active');

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