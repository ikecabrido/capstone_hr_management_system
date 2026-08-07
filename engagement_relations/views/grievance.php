<?php
session_start();
// require_once "auth.php";
require_once "../../auth/database.php";
require_once "../../auth/auth_check.php";

require_once __DIR__ . '/../autoload.php';

use App\Controllers\GrievanceController;
use App\Controllers\EmployeeController;
use App\Controllers\UserController;

$theme = $_SESSION['user']['theme'] ?? 'light';

$authUser = $_SESSION['user'] ?? [];
$userRole = strtolower(trim($authUser['role'] ?? ''));
$isHrAdmin = in_array($userRole, ['admin', 'hr', 'hr_admin', 'engagement_relations'], true)
    || strpos($userRole, 'hr') !== false
    || strpos($userRole, 'admin') !== false;

if (!$authUser || !$isHrAdmin) {
    http_response_code(403);
    echo '<div class="alert alert-danger">Access denied. Only HR/Admin users can access this portal.</div>';
    exit;
}

$grievanceCtrl = new GrievanceController();
$employeeCtrl = new EmployeeController();
$userCtrl = new UserController();

$payload = $payload ?? [];
$payload['grievances'] = $grievanceCtrl->getGrievances();
$payload['grievanceStats'] = $grievanceCtrl->getGrievanceStats();
$payload['employees'] = $employeeCtrl->index();
$payload['employeePayslips'] = [];
foreach ($payload['employees'] as $employee) {
    $employeeId = $employee['employee_id'] ?? $employee['id'] ?? null;
    if (!empty($employeeId)) {
        $payload['employeePayslips'][(int)$employeeId] = $grievanceCtrl->getEmployeePayslips((int)$employeeId);
    }
}
$payload['hrUsers'] = array_values(array_filter($userCtrl->index(), function ($user) {
    $role = strtolower(trim($user['role'] ?? ''));
    return $role === 'admin' || $role === 'hr' || $role === 'hr_admin' || strpos($role, 'hr') !== false || strpos($role, 'admin') !== false;
}));
$payload['attendanceLinks'] = [];
foreach ($payload['grievances'] as $grievance) {
    $grievanceId = (int)($grievance['id'] ?? 0);
    if ($grievanceId > 0) {
        $payload['attendanceLinks'][$grievanceId] = $grievanceCtrl->getAttendanceLinks($grievanceId);
    }
}

$uploadDir = __DIR__ . '/../../uploads/grievances/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentUserId = $_SESSION['user']['id'] ?? null;
    $isFinalized = false;
    $existingGrievance = null;
    $grievanceId = !empty($_POST['grievance_id']) ? (int)$_POST['grievance_id'] : 0;

    if ($grievanceId) {
        $existingGrievance = $grievanceCtrl->getGrievanceById($grievanceId);
        if ($existingGrievance && in_array($existingGrievance['status'], ['Resolved', 'Closed'], true)) {
            $isFinalized = true;
        }
    }

    if ($currentUserId && !empty($_POST['management_action'])) {
        if (!$grievanceId) {
            $_SESSION['flash_error'] = 'Please select a grievance record before saving.';
        } else {
            $status = trim($_POST['status'] ?? '');
            $hrRemarks = trim($_POST['hr_remarks'] ?? '');
            $resolution = trim($_POST['final_resolution'] ?? '');
            if ($status === '' || $hrRemarks === '' || $resolution === '') {
                $_SESSION['flash_error'] = 'All management fields are required before saving.';
            } else {
                try {
                    $data = [];
                    $data['status'] = $status;
                    $data['resolution_of_complaint'] = $resolution;
                    $data['action_taken'] = $hrRemarks;
                    $data['confidential'] = isset($_POST['confidential']) ? 1 : 0;

                    if (!empty($_FILES['supporting_document']['name']) && is_uploaded_file($_FILES['supporting_document']['tmp_name'])) {
                        $extension = pathinfo($_FILES['supporting_document']['name'], PATHINFO_EXTENSION);
                        $fileName = 'grievance_' . $grievanceId . '_' . time() . '.' . $extension;
                        $targetPath = $uploadDir . $fileName;
                        if (move_uploaded_file($_FILES['supporting_document']['tmp_name'], $targetPath)) {
                            $data['attachment_path'] = 'uploads/grievances/' . $fileName;
                        }
                    }

                    $grievanceCtrl->updateGrievanceManagement($grievanceId, $data, $currentUserId);

                    $investigationNotes = trim($_POST['investigation_notes'] ?? '');
                    if ($investigationNotes !== '') {
                        $grievanceCtrl->addUpdate($grievanceId, $investigationNotes, $currentUserId);
                    }
                    $grievanceCtrl->addUpdate($grievanceId, 'HR Remarks: ' . $hrRemarks, $currentUserId);
                    $grievanceCtrl->addUpdate($grievanceId, 'Final Resolution: ' . $resolution, $currentUserId);

                    $_SESSION['flash_success'] = 'Grievance management updated successfully.';
                } catch (Exception $e) {
                    $_SESSION['flash_error'] = 'Error updating grievance: ' . $e->getMessage();
                }
            }
        }
    } elseif ($currentUserId) {
        if (!empty($_POST['subject']) && !empty($_POST['description'])) {
            try {
                $employeeId = $_POST['employee_id'] ?? null;
                $anonymous = isset($_POST['anonymous']) ? 1 : 0;
                $attachmentPath = null;

                if (!empty($_FILES['supporting_document']['name']) && is_uploaded_file($_FILES['supporting_document']['tmp_name'])) {
                    $extension = pathinfo($_FILES['supporting_document']['name'], PATHINFO_EXTENSION);
                    $fileName = 'grievance_' . time() . '_' . uniqid() . '.' . $extension;
                    $targetPath = $uploadDir . $fileName;
                    if (move_uploaded_file($_FILES['supporting_document']['tmp_name'], $targetPath)) {
                        $attachmentPath = 'uploads/grievances/' . $fileName;
                    }
                }

                $payslipId = !empty($_POST['payslip_id']) ? (int)$_POST['payslip_id'] : null;
                $payslipInformation = trim($_POST['payslip_information'] ?? '');

                $grievanceCtrl->fileGrievance(
                    $employeeId,
                    $_POST['subject'],
                    $_POST['description'],
                    $_POST['category'] ?? 'Workplace Conflict',
                    $anonymous,
                    $attachmentPath,
                    $currentUserId,
                    $payslipId,
                    $payslipInformation,
                    7,
                    date('Y-m-d')
                );
                $_SESSION['flash_success'] = 'Grievance submitted successfully.';
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Error submitting grievance: ' . $e->getMessage();
            }
        } elseif ($isFinalized) {
            $_SESSION['flash_error'] = 'This grievance is already resolved or closed and cannot be modified.';
        } elseif (!empty($_POST['grievance_id']) && !empty($_POST['status']) && strtolower(trim($_POST['status'])) === 'resolved') {
            $resolution = trim($_POST['resolution'] ?? '');
            $grievanceCtrl->resolveGrievance($_POST['grievance_id'], $resolution, $currentUserId);
            $_SESSION['flash_success'] = 'Grievance resolved successfully.';
        } elseif (!empty($_POST['grievance_id']) && !empty($_POST['status'])) {
            $status = $_POST['status'];
            $resolution = trim($_POST['resolution'] ?? '');
            if (strtolower(trim($status)) === 'resolution proposed' && $resolution !== '') {
                $grievanceCtrl->updateResolution($_POST['grievance_id'], $resolution, 'Updated by HR Personnel ID: ' . $currentUserId);
            }
            $grievanceCtrl->updateStatus($_POST['grievance_id'], $status);
            $_SESSION['flash_success'] = 'Grievance status updated.';
        } elseif (!empty($_POST['bulk_status_ids']) && !empty($_POST['bulk_status'])) {
            $ids = array_filter(array_map('intval', explode(',', $_POST['bulk_status_ids'])));
            $status = $_POST['bulk_status'];
            foreach ($ids as $id) {
                if ($id > 0) {
                    $existing = $grievanceCtrl->getGrievanceById($id);
                    if ($existing && !in_array($existing['status'], ['Resolved', 'Closed'], true)) {
                        $grievanceCtrl->updateStatus($id, $status);
                    }
                }
            }
            $_SESSION['flash_success'] = 'Bulk status update completed.';
        } elseif (!empty($_POST['grievance_id']) && !empty($_POST['resolution'])) {
            $grievanceCtrl->updateResolution($_POST['grievance_id'], $_POST['resolution'], 'Updated by HR Personnel ID: ' . $currentUserId);
            $_SESSION['flash_success'] = 'Grievance resolution notes saved.';
        } elseif (!empty($_POST['grievance_id']) && !empty($_POST['escalation_reason']) && isset($_POST['escalate_action'])) {
            try {
                $newLevel = !empty($_POST['new_escalation_level']) ? (int)$_POST['new_escalation_level'] : (($_POST['current_level'] ?? 1) + 1);
                $grievanceCtrl->escalateGrievance($_POST['grievance_id'], $_POST['escalation_reason'], $newLevel);
                $_SESSION['flash_success'] = 'Grievance escalated successfully to Level ' . $newLevel . '.';
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Error escalating grievance: ' . $e->getMessage();
            }
        } elseif (isset($_POST['grievance_id']) && isset($_POST['confidential'])) {
            $grievanceCtrl->markConfidential($_POST['grievance_id'], (bool)$_POST['confidential']);
            $_SESSION['flash_success'] = 'Confidentiality setting updated.';
        } elseif (!empty($_POST['grievance_id']) && !empty($_POST['investigation_notes'])) {
            $grievanceCtrl->addInvestigationNotes($_POST['grievance_id'], $_POST['investigation_notes'], $currentUserId);
            $_SESSION['flash_success'] = 'Investigation notes added.';
        }
    }

    $isAjaxRequest = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($isAjaxRequest) {
        $responseSuccess = !empty($_SESSION['flash_success']);
        $responseMessage = $_SESSION['flash_success'] ?? ($_SESSION['flash_error'] ?? 'Request processed.');

        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $responseSuccess,
            'message' => $responseMessage,
        ]);
        exit;
    }

    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Helper functions
function getStatusBadgeClass($status) {
    $status = strtolower(trim($status));
    switch ($status) {
        case 'pending':
        case 'submitted':
            return 'warning';
        case 'under review':
            return 'info';
        case 'resolved':
            return 'success';
        case 'closed':
            return 'secondary';
        case 'escalated':
            return 'danger';
        default:
            return 'light';
    }
}

function getStatusProgressClass($status) {
    $status = strtolower(trim($status));
    switch ($status) {
        case 'pending':
        case 'submitted':
            return 'warning';
        case 'under review':
            return 'info';
        case 'resolved':
            return 'success';
        case 'closed':
            return 'secondary';
        case 'escalated':
            return 'danger';
        default:
            return 'light';
    }
}

function getStatusProgress($status) {
    $status = strtolower(trim($status));
    switch ($status) {
        case 'pending':
        case 'submitted':
            return 25;
        case 'under review':
            return 50;
        case 'resolved':
            return 100;
        case 'closed':
            return 100;
        case 'escalated':
            return 75;
        default:
            return 0;
    }
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
    href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- overlayScrollbars -->
  <link
    rel="stylesheet"
    href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />

  <link rel="stylesheet" href="../../layout/toast.css" />
  <link rel="stylesheet" href="../custom.css" />
  <link rel="stylesheet" href="css/grievance.css" />


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
          <a href="../engagement_relations.php" class="nav-link">Home</a>
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

    <!-- MAIN CONTENT -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Grievances</h1>
              <p class="text-muted">Manage employee grievances and complaints</p>
            </div>
          </div>
        </div>
      </div>

      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <!-- Flash Messages -->
              <?php if ($flashSuccess): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <i class="fas fa-check-circle"></i> <?= htmlspecialchars($flashSuccess) ?>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
              <?php endif; ?>
              <?php if ($flashError): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($flashError) ?>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Grievance Management Tabs -->
          <div class="card shadow-sm border-0 grievance-tabs-card">
            <div class="card-header p-0 border-0">
              <div class="px-3 py-2"><strong>HR / Admin Functions</strong></div>
              <ul class="nav nav-tabs grievance-nav-tabs" id="grievance-tabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="all-grievances-tab" data-toggle="pill" href="#all-grievances" role="tab">
                    <i class="fas fa-list"></i> All Grievances
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="management-tab" data-toggle="pill" href="#management" role="tab">
                    <i class="fas fa-cogs"></i> Manage Grievance
                  </a>
                </li>
                
                <li class="nav-item">
                  <a class="nav-link" id="reports-tab" data-toggle="pill" href="#reports" role="tab">
                    <i class="fas fa-file-alt"></i> Reports
                  </a>
                </li>
              </ul>
            </div>

            <div class="card-body">
              <div class="tab-content" id="grievance-tabs-content">
                <div class="tab-pane fade show active" id="all-grievances" role="tabpanel" aria-labelledby="all-grievances-tab">
                  <!-- Record Employee Grievance form removed per request -->

                  <div class="row mb-3">
                     <div class="col-12 mb-2">
                      <p class="text-muted small mb-0">All Grievances.</p>
                    </div>
                    <!-- helper removed per request -->
                    <div class="col-md-2">
                      <select class="form-control" id="status-filter">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="under review">Under Review</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                        <option value="escalated">Escalated</option>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <select class="form-control" id="category-filter">
                        <option value="">All Categories</option>
                        <option value="Payroll Issues">Payroll Issues</option>
                        <option value="Attendance & Leave">Attendance & Leave</option>
                        <option value="Workplace Harassment">Workplace Harassment</option>
                        <option value="Supervisor/Management Issues">Supervisor/Management Issues</option>
                        <option value="Co-worker Issues">Co-worker Issues</option>
                        <option value="Health and Safety">Health and Safety</option>
                        <option value="Company Policy Violations">Company Policy Violations</option>
                        <option value="Benefits Concerns">Benefits Concerns</option>
                        <option value="Other Employment Concerns">Other Employment Concerns</option>
                        <option value="Other">Other</option>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <select class="form-control" id="department-filter">
                        <option value="">All Departments</option>
                        <?php foreach (array_unique(array_filter(array_column($payload['grievances'], 'department'))) as $department): ?>
                          <option value="<?= htmlspecialchars($department) ?>"><?= htmlspecialchars($department) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <input type="date" class="form-control" id="date-filter" placeholder="Date">
                    </div>
                    <div class="col-md-2">
                      <input type="text" class="form-control" id="search-filter" placeholder="Search...">
                    </div>
                  </div>

                  <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="all-grievances-table">
                      <thead>
                        <tr>
                          <th>Grievance ID</th>
                          <th>Employee Name</th>
                          <th>Subject</th>
                          <th>Category</th>
                          <th>Status</th>
                          <th>Priority</th>
                          <th>Date Submitted</th>
                          <th>Payslip</th>
                          <th>Employee Adjustments</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($payload['grievances'])): ?>
                          <?php foreach ($payload['grievances'] as $grievance): ?>
                            <?php
                              $payslipLabel = '';
                              if (!empty($grievance['payslip_id'])) {
                                  $payslipLabel = 'Payslip ' . (int)$grievance['payslip_id'];
                                  if (!empty($grievance['gross_pay']) || !empty($grievance['net_pay'])) {
                                      $payslipLabel .= ' | Gross ' . number_format((float)$grievance['gross_pay'], 2);
                                      $payslipLabel .= ' | Net ' . number_format((float)$grievance['net_pay'], 2);
                                  }
                              }
                            ?>
                            <tr class="grievance-row"
                                data-id="<?= (int)($grievance['id'] ?? 0) ?>"
                                data-status="<?= strtolower(htmlspecialchars($grievance['status'] ?? '')) ?>"
                                data-category="<?= htmlspecialchars($grievance['category'] ?? '') ?>"
                                data-priority="<?= strtolower(htmlspecialchars($grievance['priority'] ?? '')) ?>"
                                data-department="<?= htmlspecialchars($grievance['department'] ?? '') ?>"
                                data-date="<?= htmlspecialchars(substr($grievance['created_at'] ?? '', 0, 10)) ?>"
                                data-payroll-module="<?= htmlspecialchars(strtolower($grievance['payroll_module'] ?? 'no payroll link')) ?>"
                                data-search="<?= strtolower(htmlspecialchars($grievance['subject'] ?? '') . ' ' . ($grievance['employee_name'] ?? '') . ' ' . ($grievance['category'] ?? '') . ' ' . ($payslipLabel) . ' ' . ($grievance['payroll_module'] ?? '') . ' ' . ($grievance['payroll_reference_id'] ?? '') . ' ' . ($grievance['employee_adjustments'] ?? '')) ?>">
                              <td><?= (int)($grievance['id'] ?? 0) ?></td>
                              <td><?= htmlspecialchars($grievance['employee_name'] ?? 'Unknown') ?></td>
                              <td><?= htmlspecialchars($grievance['subject'] ?? '') ?></td>
                              <td><?= htmlspecialchars($grievance['category'] ?? 'N/A') ?></td>
                              <td><span class="badge badge-<?= getStatusBadgeClass($grievance['status']) ?>"><?= htmlspecialchars(ucfirst($grievance['status'] ?? 'Pending')) ?></span></td>
                              <td><span class="badge badge-<?= strtolower($grievance['priority'] ?? '') === 'high' ? 'danger' : 'secondary' ?>"><?= htmlspecialchars(ucfirst($grievance['priority'] ?? 'Medium')) ?></span></td>
                              <td><?= htmlspecialchars(date('M d, Y', strtotime($grievance['created_at'] ?? 'now'))) ?></td>
                              <td><?= htmlspecialchars($payslipLabel ?: 'None') ?></td>
                              <td>
                                <?php if (!empty($grievance['employee_adjustments'])): ?>
                                  <small title="<?= htmlspecialchars($grievance['employee_adjustments']) ?>"><?= htmlspecialchars(substr($grievance['employee_adjustments'], 0, 50)) ?><?= strlen($grievance['employee_adjustments']) > 50 ? '...' : '' ?></small>
                                <?php else: ?>
                                  <span class="text-muted">None</span>
                                <?php endif; ?>
                              </td>
                              <td>
                                <button class="btn btn-sm btn-info" title="View Grievance" onclick="viewGrievanceDetails(<?= (int)($grievance['id'] ?? 0) ?>)"><i class="fas fa-eye"></i></button>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr><td colspan="10" class="text-center text-muted">No grievance records found.</td></tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="tab-pane fade" id="management" role="tabpanel" aria-labelledby="management-tab">
                  <div class="card card-success">
                    <div class="card-header">
                      <h3 class="card-title"><i class="fas fa-user-shield"></i> Grievance</h3>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Choose grievance</label>
                            <select class="form-control" id="management-grievance-select">
                              <option value="">Select grievance record</option>
                              <?php foreach ($payload['grievances'] as $grievance): ?>
                                <option value="<?= (int)($grievance['id'] ?? 0) ?>" data-status="<?= strtolower(htmlspecialchars($grievance['status'] ?? '')) ?>"><?= (int)($grievance['id'] ?? 0) ?> - <?= htmlspecialchars($grievance['subject'] ?? '') ?></option>
                              <?php endforeach; ?>
                            </select>
                          </div>
                        </div>
                        </div>
                      <div id="management-form-alert" class="alert d-none" role="alert"></div>
                      <form id="management-update-form" method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="management_action" value="1">
                        <input type="hidden" name="grievance_id" id="management-grievance-id" value="">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>Status</label>
                              <select class="form-control" name="status">
                                <option value="Pending">Pending</option>
                                <option value="Under Review">Under Review</option>
                                <option value="Resolved">Resolved</option>
                                <option value="Closed">Closed</option>
                                <option value="Escalated">Escalated</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <!-- Investigation Notes removed from management UI per request -->
                        <div class="form-group">
                          <label>HR Remarks</label>
                          <textarea class="form-control" name="hr_remarks" rows="4" placeholder="Add HR remarks or actions taken"></textarea>
                        </div>
                        <div class="form-group">
                          <label>Final Resolution</label>
                          <textarea class="form-control" name="final_resolution" rows="4" placeholder="Record the final resolution"></textarea>
                        </div>
                        <div class="form-group">
                          <label>Supporting Documents</label>
                          <input type="file" class="form-control-file" name="supporting_document">
                        </div>
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" name="confidential" value="1">
                          <label class="form-check-label">Mark as confidential</label>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Save Management Update</button>
                      </form>
                    </div>
                  </div>
                </div>

                

                <div class="tab-pane fade" id="reports" role="tabpanel" aria-labelledby="reports-tab">
                  <p class="text-muted mb-3">Grievance Reports & Exports – generate and export reports derived from grievance records (not directly from payroll tables).</p>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="card card-primary">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-file-alt"></i> Generate Reports</h3></div>
                        <div class="card-body">
                          <form id="report-form">
                            <div class="form-group"><label>Report Type</label><select class="form-control" id="report-type"><option value="summary">Summary Report</option><option value="detailed">Detailed Report</option><option value="category">Category Analysis</option><option value="resolution">Resolution Report</option></select></div>
                            <div class="form-group"><label>Date Range</label><div class="input-group"><input type="date" class="form-control" id="report-start-date"><div class="input-group-prepend"><span class="input-group-text">to</span></div><input type="date" class="form-control" id="report-end-date"></div></div>
                            <div class="form-group"><label>Department</label><select class="form-control" id="report-department"><option value="">All Departments</option><?php foreach (array_unique(array_filter(array_column($payload['grievances'], 'department'))) as $department): ?><option value="<?= htmlspecialchars($department) ?>"><?= htmlspecialchars($department) ?></option><?php endforeach; ?></select></div>
                            <div class="form-group"><label>Category</label><select class="form-control" id="report-category"><option value="">All Categories</option><option value="Payroll Issues">Payroll Issues</option><option value="Attendance & Leave">Attendance & Leave</option><option value="Workplace Harassment">Workplace Harassment</option><option value="Supervisor/Management Issues">Supervisor/Management Issues</option><option value="Co-worker Issues">Co-worker Issues</option><option value="Health and Safety">Health and Safety</option><option value="Company Policy Violations">Company Policy Violations</option><option value="Benefits Concerns">Benefits Concerns</option><option value="Other Employment Concerns">Other Employment Concerns</option><option value="Other">Other</option></select></div>
                            <div class="form-group"><label>Status</label><select class="form-control" id="report-status"><option value="">All Status</option><option value="Pending">Pending</option><option value="Under Review">Under Review</option><option value="Resolved">Resolved</option><option value="Closed">Closed</option><option value="Escalated">Escalated</option></select></div>
                            <div class="form-group"><label>Employee</label><input type="text" class="form-control" id="report-employee" placeholder="Employee name"></div>
                            <div class="form-group"><label>Format</label><select class="form-control" id="report-format"><option value="pdf">PDF</option><option value="excel">Excel</option></select></div>
                            <button type="button" class="btn btn-primary btn-block" onclick="generateCustomReport()"><i class="fas fa-download"></i> Generate Report</button>
                          </form>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-8">
                      <div id="generated-report" class="card mb-3 hidden">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-file-alt"></i> Generated Report Result</h3></div>
                        <div class="card-body"><div id="generated-report-summary" class="mb-3"></div><div class="table-responsive"><table class="table table-bordered table-sm" id="generated-report-table"><thead></thead><tbody></tbody></table></div></div>
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
  <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.5.28/dist/jspdf.plugin.autotable.min.js"></script>

  <script>
    // Set grievances data from PHP to global variable
    window.grievancesData = <?php echo json_encode($payload['grievances'] ?? []); ?>;
    window.grievancePayslipsData = <?php echo json_encode($payload['employeePayslips'] ?? []); ?>;
  </script>
  <script src="js/live_updates.js"></script>
  <script src="js/grievance.js"></script>
</body>

</html>