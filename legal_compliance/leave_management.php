<?php
/**
 * Leave Management - HR Validation Module
 * 
 * This module provides HR with tools to validate and process leave requests
 * with dynamic eligibility checklists based on Philippine labor laws.
 * 
 * Leave Types:
 * - Maternity Leave (RA 11210 - Expanded Maternity Leave Law)
 * - Paternity Leave (RA 8187 - Paternity Leave Act)
 * - Sick Leave
 * - Vacation Leave
 * - Bereavement Leave
 * - Emergency Leave
 * 
 * @author HR Development Team
 * @version 2.0 (Refactored - Modular Structure)
 */

session_start();
require_once "../auth/database.php";
require_once "../auth/auth_check.php";
require_once "controllers/LeaveManagementController.php";

// Page configuration
$pageTitle = 'Leave Management';
$currentPage = 'Leave Management';

// Base path for assets
$basePath = '../';

// Get database connection
$db = Database::getInstance()->getConnection();

// Initialize controller
$controller = new LeaveManagementController($db);

// Initialize database tables
$controller->initializeDatabase();

// Handle PHP-dependent modal
$viewLeaveId = isset($_GET['view']) ? (int)$_GET['view'] : null;
$leaveDetails = null;

if ($viewLeaveId) {
    $leaveDetails = $controller->getLeaveDetails($viewLeaveId);
    if ($leaveDetails) {
        $leaveDetails['employee_name'] = ($leaveDetails['first_name'] ?? '') . ' ' . ($leaveDetails['last_name'] ?? '');
        $leaveDetails['duration_days'] = $leaveDetails['total_days'] ?? 0;
    }
}

// Get filter status first (needed for POST handling)
$statusFilter = $_GET['status'] ?? 'pending';

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status' && !empty($_POST['leave_id'])) {
    $leaveId = (int)$_POST['leave_id'];
    $status = $_POST['status'];
    $hrId = $_SESSION['user_id'] ?? null;
    $result = $controller->updateLeaveStatus($leaveId, $status, '', $hrId);
    if ($result['success']) {
        // For AJAX requests, don't redirect - just return JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            echo json_encode($result);
            exit;
        }
        header('Location: leave_management.php?status=' . $statusFilter . '&success=updated');
        exit;
    } else {
        $error = $result['message'];
    }
}

// Handle AJAX requests
$controller->handleAjaxRequest();

// Get leave requests and statistics
$leaveRequests = $controller->getLeaveRequests($statusFilter);
$stats = $controller->getStatistics();

// Include Header Template
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$componentsPath = 'components/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - BCP HR System</title>
    
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=swap">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/plugins/fontawesome-free/css/all.min.css">
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/dist/css/adminlte.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $componentsPath ?>custom.css">
    
    <!-- Leave Management CSS -->
    <link rel="stylesheet" href="css/leave_management.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <?php include $componentsPath . 'header.php'; ?>
    
    <!-- Main Sidebar -->
    <?php include $componentsPath . 'sidebar.php'; ?>
    
    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?= $pageTitle ?></h1>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="content">
            <div class="row">
                    <div class="col-12">
                        <!-- Stats Cards - Legal Compliance Style -->
                        <div class="row mb-4">
                            <div class="col-lg-3 col-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Pending Requests</span>
                                        <span class="info-box-number"><?= $stats['pending'] ?? 0 ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Approved</span>
                                        <span class="info-box-number"><?= $stats['approved'] ?? 0 ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-times-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Rejected</span>
                                        <span class="info-box-number"><?= $stats['rejected'] ?? 0 ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-calendar-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Requests</span>
                                        <span class="info-box-number"><?= $stats['total'] ?? 0 ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Tabs -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-list-alt mr-2"></i>Leave Requests
                                </h3>
                                <div class="card-tools">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm <?= $statusFilter === 'pending' ? 'btn-primary' : 'btn-default' ?>" 
                                                onclick="filterStatus('pending')">Pending</button>
                                        <button type="button" class="btn btn-sm <?= $statusFilter === 'approved' ? 'btn-success' : 'btn-default' ?>" 
                                                onclick="filterStatus('approved')">Approved</button>
                                        <button type="button" class="btn btn-sm <?= $statusFilter === 'rejected' ? 'btn-danger' : 'btn-default' ?>" 
                                                onclick="filterStatus('rejected')">Rejected</button>
                                        <button type="button" class="btn btn-sm <?= $statusFilter === 'all' ? 'btn-secondary' : 'btn-default' ?>" 
                                                onclick="filterStatus('all')">All</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap" id="leaveTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Employee</th>
                                            <th>Leave Type</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Days</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($leaveRequests) > 0): ?>
                                            <?php foreach ($leaveRequests as $leave): ?>
                                                <tr>
                                                    <td><?= $leave['id'] ?></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($leave['first_name'] ?? '') ?> <?= htmlspecialchars($leave['last_name'] ?? '') ?></strong>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                            $leaveTypeIcons = [
                                                                'Maternity Leave' => 'fa-baby',
                                                                'Paternity Leave' => 'fa-baby-carriage',
                                                                'Sick Leave' => 'fa-user-nurse',
                                                                'Vacation Leave' => 'fa-umbrella-beach',
                                                                'Bereavement Leave' => 'fa-dove',
                                                                'Emergency Leave' => 'fa-exclamation-triangle'
                                                            ];
                                                            $icon = $leaveTypeIcons[$leave['leave_type']] ?? 'fa-calendar';
                                                        ?>
                                                        <i class="fas <?= $icon ?> mr-1"></i>
                                                        <?= htmlspecialchars($leave['leave_type']) ?>
                                                    </td>
                                                    <td><?= date('M d, Y', strtotime($leave['start_date'])) ?></td>
                                                    <td><?= date('M d, Y', strtotime($leave['end_date'])) ?></td>
                                                    <td><span class="badge badge-primary"><?= $leave['total_days'] ?></span></td>
                                                    <td>
                                                        <?php 
                                                            $statusColors = [
                                                                'pending' => 'warning',
                                                                'approved' => 'success',
                                                                'rejected' => 'danger'
                                                            ];
                                                        ?>
                                                        <span class="badge badge-<?= $statusColors[$leave['status']] ?>">
                                                            <?= ucfirst($leave['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="?view=<?= $leave['id'] ?>&status=<?= $statusFilter ?>" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> Review
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4 empty-state">
                                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                                    <p>No leave requests found</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leave Details (PHP dependent) -->
                <?php if ($leaveDetails): ?>
                <div class="card mt-3" id="leave-details">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title">
                            <i class="fas fa-eye"></i> Leave Request Details - #<?= htmlspecialchars($leaveDetails['id']) ?>
                        </h3>
                        <div class="card-tools">
                            <a href="leave_management.php?status=<?= $statusFilter ?>" class="btn btn-light btn-sm">
                                <i class="fas fa-times"></i> Close
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Leave details content here -->
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Basic Information</h5>
                                <table class="table table-bordered">
                                    <tr><th>Employee</th><td><?= htmlspecialchars($leaveDetails['employee_name']) ?></td></tr>
                                    <tr><th>Leave Type</th><td><?= htmlspecialchars($leaveDetails['leave_type']) ?></td></tr>
                                    <tr><th>Start Date</th><td><?= htmlspecialchars($leaveDetails['start_date']) ?></td></tr>
                                    <tr><th>End Date</th><td><?= htmlspecialchars($leaveDetails['end_date']) ?></td></tr>
                                    <tr><th>Duration</th><td><?= htmlspecialchars($leaveDetails['duration_days']) ?> days</td></tr>
                                    <tr><th>Status</th><td><span class="badge badge-<?= $leaveDetails['status'] == 'approved' ? 'success' : ($leaveDetails['status'] == 'rejected' ? 'danger' : 'warning') ?>"><?= ucfirst($leaveDetails['status']) ?></span></td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Additional Information</h5>
                                <table class="table table-bordered">
                                    <tr><th>Reason</th><td><?= htmlspecialchars($leaveDetails['reason']) ?></td></tr>
                                    <tr><th>Emergency Contact</th><td><?= htmlspecialchars($leaveDetails['emergency_contact'] ?? 'Not provided') ?></td></tr>
                                    <tr><th>Submitted</th><td><?= htmlspecialchars($leaveDetails['created_at']) ?></td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h5>Eligibility Checklist</h5>
                                <?php 
                                    $checklist = $controller->getEligibilityChecklist($leaveDetails['leave_type']);
                                    if ($checklist): 
                                ?>
                                <div class="alert alert-light border-left-<?= $checklist['color'] ?>">
                                    <h6><i class="fas <?= $checklist['icon'] ?> mr-2"></i><?= htmlspecialchars($checklist['title']) ?></h6>
                                    <p class="text-muted mb-2"><i class="fas fa-balance-scale mr-1"></i> <?= htmlspecialchars($checklist['legalRef']) ?></p>
                                    
                                    <div class="mt-3">
                                        <strong>Employee Requirements:</strong>
                                        <ul class="list-unstyled mt-2">
                                            <?php 
                                            // Parse checklist data if exists
                                            $checklistData = [];
                                            if (!empty($leaveDetails['checklist_data'])) {
                                                $checklistData = json_decode($leaveDetails['checklist_data'], true);
                                                if (!is_array($checklistData)) {
                                                    $checklistData = [];
                                                }
                                            }
                                            $requirementsData = $checklistData['requirements'] ?? [];
                                            ?>
                                            <?php foreach ($checklist['requirements'] as $req): ?>
                                            <li class="mb-1">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" 
                                                           class="custom-control-input" 
                                                           id="emp_req_<?= htmlspecialchars($req['id']) ?>" 
                                                           name="requirements[<?= htmlspecialchars($req['id']) ?>]"
                                                           value="1"
                                                           <?= isset($requirementsData[$req['id']]) && $requirementsData[$req['id']] ? 'checked' : '' ?>
                                                    >
                                                    <label class="custom-control-label" for="emp_req_<?= htmlspecialchars($req['id']) ?>">
                                                        <?= htmlspecialchars($req['label']) ?>
                                                        <?php if ($req['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                </div>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    

                                    
                                    <div class="mt-3">
                                        <strong>Supporting Documents:</strong>
                                        <?php 
                                            $documents = $controller->getLeaveDocuments($leaveDetails['id']);
                                            if (!empty($documents)): 
                                        ?>
                                        <div class="table-responsive mt-2">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Type</th>
                                                        <th>File Name</th>
                                                        <th>Uploaded</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($documents as $doc): ?>
                                                    <tr>
                                                        <td>
                                                            <?php 
                                                                $ext = pathinfo($doc['file_path'], PATHINFO_EXTENSION);
                                                                $icon = 'fas fa-file text-secondary';
                                                                if ($ext === 'pdf') $icon = 'fas fa-file-pdf text-danger';
                                                                elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) $icon = 'fas fa-file-image text-info';
                                                                elseif (in_array($ext, ['doc', 'docx'])) $icon = 'fas fa-file-word text-primary';
                                                                elseif (in_array($ext, ['xls', 'xlsx'])) $icon = 'fas fa-file-excel text-success';
                                                            ?>
                                                            <i class="<?= $icon ?> mr-1"></i> <?= htmlspecialchars($doc['document_type']) ?>
                                                        </td>
                                                        <td><?= htmlspecialchars(basename($doc['file_path'])) ?></td>
                                                        <td><?= date('M d, Y', strtotime($doc['uploaded_at'])) ?></td>
                                                        <td>
                                                            <a href="<?= htmlspecialchars($doc['file_path']) ?>" target="_blank" class="btn btn-sm btn-info mr-1">
                                                                <i class="fas fa-eye"></i> View
                                                            </a>
                                                            <a href="<?= htmlspecialchars($doc['file_path']) ?>" download class="btn btn-sm btn-success">
                                                                <i class="fas fa-download"></i> Download
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php else: ?>
                                        <p class="text-muted mt-2"><i class="fas fa-info-circle mr-1"></i> No supporting documents have been uploaded.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-light border-left-primary">
                                    <p>No checklist available for this leave type.</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php if ($leaveDetails['status'] == 'pending'): ?>
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="leave_id" value="<?= $leaveDetails['id'] ?>">
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="leave_id" value="<?= $leaveDetails['id'] ?>">
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </form>
                        <?php endif; ?>
                        <a href="leave_management.php?status=<?= $statusFilter ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Close
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Leave Details Modal -->
                <div class="modal fade" id="leaveDetailsModal" tabindex="-1" role="dialog" aria-labelledby="leaveDetailsModalLabel" data-backdrop="static" data-keyboard="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-fixed-size" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title" id="leaveDetailsModalLabel">
                                    <i class="fas fa-clipboard-check mr-2"></i>Leave Request Review
                                </h5>
                                <button type="button" class="close text-white" id="leaveDetailsCloseBtn" aria-label="Close" onclick="hideLeaveModal()">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Leave Info Header -->
                                <div class="alert alert-light border-left-primary" id="leaveInfoSection">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong><i class="fas fa-user mr-1"></i>Employee:</strong> <span id="modalEmployeeName">-</span></p>
                                            <p class="mb-1"><strong><i class="fas fa-calendar mr-1"></i>Leave Type:</strong> <span id="modalLeaveType">-</span></p>
                                            <p class="mb-1"><strong><i class="fas fa-clock mr-1"></i>Duration:</strong> <span id="modalDuration">-</span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong><i class="fas fa-play mr-1"></i>Start Date:</strong> <span id="modalStartDate">-</span></p>
                                            <p class="mb-1"><strong><i class="fas fa-stop mr-1"></i>End Date:</strong> <span id="modalEndDate">-</span></p>
                                            <p class="mb-1"><strong><i class="fas fa-align-left mr-1"></i>Reason:</strong> <span id="modalReason">-</span></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic Eligibility Checklist -->
                                <div class="card card-primary card-outline" id="checklistCard">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            <i class="fas fa-tasks mr-2"></i>
                                            <span id="checklistTitle">Eligibility Checklist</span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Eligibility Status -->
                                        <div class="alert alert-info mb-3" id="eligibilityStatus">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <span id="eligibilityMessage">Review the checklist below to determine eligibility</span>
                                        </div>

                                        <!-- Dynamic Checklist Container -->
                                        <div id="dynamicChecklist">
                                            <!-- Checklist items will be loaded dynamically based on leave type -->
                                        </div>
                                    </div>
                                </div>

                                <!-- HR Decision Section -->
                                <div class="card card-success card-outline" id="decisionCard">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            <i class="fas fa-gavel mr-2"></i>HR Decision
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="hrComments">HR Comments (Optional)</label>
                                            <textarea class="form-control" id="hrComments" rows="3" 
                                                      placeholder="Add any notes or comments about this decision..."></textarea>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <button type="button" id="rejectBtn" class="btn btn-danger btn-reject" onclick="updateLeaveStatus('rejected')">
                                                <i class="fas fa-times mr-1"></i> Reject
                                            </button>
                                            <button type="button" id="approveBtn" class="btn btn-success btn-approve" onclick="updateLeaveStatus('approved')">
                                                <i class="fas fa-check mr-1"></i> Approve
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Documents Section -->
                                <div class="card card-warning card-outline mt-3" id="documentsCard">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            <i class="fas fa-file-alt mr-2"></i>Submitted Documents
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="documentsList">
                                            <p class="text-muted text-center">Loading documents...</p>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- /.content-wrapper -->

<?php 
// Include footer
?>

<!-- Bootstrap Bundle JS -->
<script src="<?= $basePath ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Leave Management JavaScript -->
<script src="js/leave_management.js"></script>

</body>
</html>
