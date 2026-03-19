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

// Get database connection
$db = Database::getInstance()->getConnection();

// Initialize controller
$controller = new LeaveManagementController($db);

// Initialize database tables
$controller->initializeDatabase();

// Handle AJAX requests
$controller->handleAjaxRequest();

// Get filter status
$statusFilter = $_GET['status'] ?? 'pending';

// Get leave requests and statistics
$leaveRequests = $controller->getLeaveRequests($statusFilter);
$stats = $controller->getStatistics();

// Include Header Template
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$basePath = ($currentFolder === 'legal_compliance') ? '../' : '../';

include "components/header_template.php";
?>

                <!-- Leave Management Content -->
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
                                                        <button type="button" class="btn btn-sm btn-info btn-action-review" 
                                                                onclick="viewLeaveDetails(<?= $leave['id'] ?>, '<?= htmlspecialchars($leave['leave_type']) ?>')">
                                                            <i class="fas fa-eye"></i> Review
                                                        </button>
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

<?php 
// Include footer
include "components/footer_template.php"; 
?>

<!-- Custom Styles for Leave Management -->
<link rel="stylesheet" href="css/leave_management.css" />

<!-- Leave Management JavaScript -->
<script src="js/leave_management.js"></script>

<script>
// ULTRA AGGRESSIVE cleanup - Remove ANY leftover modal backdrops IMMEDIATELY
(function() {
    var removeBackdrops = function() {
        var backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(function(backdrop) {
            backdrop.parentNode.removeChild(backdrop);
        });
        
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    };
    
    // Run immediately
    removeBackdrops();
    
    // Run again after a short delay
    setTimeout(removeBackdrops, 50);
    
    // Run again on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(removeBackdrops, 100);
    });
    
    // Run again after load
    window.addEventListener('load', function() {
        setTimeout(removeBackdrops, 200);
    });
})();
</script>

</body>
</html>
