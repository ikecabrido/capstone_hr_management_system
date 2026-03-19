<?php
session_start();
require_once "../../auth/database.php";
require_once "../controllers/policyWorkflowController.php";
require_once "../../auth/auth_check.php";

// Page configuration
$pageTitle = 'Policy Management';
$currentPage = 'Policy Management';

$db = Database::getInstance()->getConnection();
$controller = new PolicyWorkflowController();

// Handle AJAX requests for modals
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    if ($_GET['action'] === 'get' && isset($_GET['id'])) {
        $policy = $controller->getPolicy($_GET['id']);
        echo json_encode($policy);
        exit;
    }
    
    if ($_GET['action'] === 'categories') {
        $categories = $controller->getCategories();
        echo json_encode($categories);
        exit;
    }
}

// Get current tab and filters
$tab = $_GET['tab'] ?? 'all';
$status = $_GET['status'] ?? null;
$category = $_GET['category'] ?? null;
$search = $_GET['search'] ?? null;

// Handle form submissions
$message = '';
$error = '';
$userId = $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId) {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            $result = $controller->createPolicy($_POST, $userId);
            if ($result['success']) {
                $message = "Policy created successfully! <strong>Note: This policy will require approval from supervisors/management before it can be published.</strong>";
            } else {
                $error = $result['error'];
            }
            break;
            
        case 'update':
            $result = $controller->updatePolicy($_POST['policy_id'], $_POST, $userId);
            if ($result['success']) {
                $message = "Policy updated successfully!";
            } else {
                $error = $result['error'];
            }
            break;
            
        case 'delete':
            if ($controller->deletePolicy($_POST['policy_id'])) {
                // Check if AJAX request
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Policy deleted successfully!']);
                    exit;
                }
                $message = "Policy deleted successfully!";
            } else {
                $error = "Cannot delete policy - only draft or pending approval policies can be deleted.";
            }
            break;
            
        case 'submit':
            if ($controller->submitForApproval($_POST['policy_id'], $userId)['success']) {
                $message = "Policy submitted for approval! <strong>Pending review by supervisors/management with approval permissions.</strong>";
            } else {
                $error = "Failed to submit policy.";
            }
            break;
            
        case 'supervisor_approve':
            if ($controller->supervisorApprove($_POST['policy_id'], $userId, $_POST['remarks'] ?? null)['success']) {
                $message = "Policy approved by supervisor!";
            } else {
                $error = "Failed to approve policy.";
            }
            break;
            
        case 'approve':
            if ($controller->approvePolicy($_POST['policy_id'], $userId, $_POST['remarks'] ?? null)['success']) {
                $message = "Policy approved!";
            } else {
                $error = "Failed to approve policy.";
            }
            break;
            
        case 'reject':
            if (!empty($_POST['remarks'])) {
                if ($controller->rejectPolicy($_POST['policy_id'], $userId, $_POST['remarks'])['success']) {
                    $message = "Policy rejected!";
                } else {
                    $error = "Failed to reject policy.";
                }
            } else {
                $error = "Remarks are required when rejecting.";
            }
            break;
            
        case 'publish':
            if ($controller->publishPolicy($_POST['policy_id'], $userId, $_POST['effective_date'] ?? null)['success']) {
                $message = "Policy published!";
            } else {
                $error = "Failed to publish policy. Must be approved first.";
            }
            break;
    }
}

// Check user permissions
$isHR = false;
$canApprove = false;
$isSupervisor = false;
$isManagement = false;
try {
    $isHR = $controller->isHRAdmin($userId);
    $canApprove = $controller->canApprove($userId);
    $isSupervisor = $controller->isSupervisor($userId);
    $isManagement = $controller->isManagement($userId);
} catch (Exception $e) {
    // Database not ready
}

// Determine if user can create policies
$canCreatePolicy = ($isHR || $canApprove || $isSupervisor || $isManagement);

// If all role checks failed but user is logged in, allow access
if (!$canCreatePolicy && !empty($userId)) {
    $canCreatePolicy = true;
}

// Get policies based on filter
// Priority: status parameter > tab parameter
if (!empty($status)) {
    // Direct status filter from ?status= parameter
    $policies = $controller->getAllPolicies($status, $category, $search);
} elseif ($tab === 'all') {
    // Show all policies regardless of status
    $policies = $controller->getAllPolicies(null, null, $search);
} elseif ($tab === 'pending') {
    $policies = $controller->getAllPolicies('Pending Approval', null, $search);
} elseif ($tab === 'supervisor') {
    $policies = $controller->getAllPolicies('Supervisor Approved', null, $search);
} elseif ($tab === 'approved') {
    $policies = $controller->getAllPolicies('Approved', null, $search);
} else {
    // Default: show all policies
    $policies = $controller->getAllPolicies($status, $category, $search);
}

$categories = [];
try {
    $categories = $controller->getCategories();
} catch (Exception $e) {
    // Categories table not ready
}

// Get stats
$stats = ['draft' => 0, 'pending' => 0, 'approved' => 0, 'published' => 0, 'rejected' => 0];
try {
    $allPolicies = $controller->getAllPolicies();
    if (!empty($allPolicies)) {
        $stats = [
            'draft' => count(array_filter($allPolicies, fn($p) => ($p['status'] ?? '') === 'Draft')),
            'pending' => count(array_filter($allPolicies, fn($p) => ($p['status'] ?? '') === 'Pending Approval')),
            'approved' => count(array_filter($allPolicies, fn($p) => ($p['status'] ?? '') === 'Approved')),
            'published' => count(array_filter($allPolicies, fn($p) => ($p['status'] ?? '') === 'Published')),
            'rejected' => count(array_filter($allPolicies, fn($p) => ($p['status'] ?? '') === 'Rejected'))
        ];
    }
} catch (Exception $e) {
    // Database not ready yet
}

// Include Header Template
include "../components/header_template.php";
?>

<!-- Custom CSS for this page -->
<link rel="stylesheet" href="compliance.css">

<style>
/* Disabled button styling */
.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed !important;
}

/* ============================================
   Edit Policy Modal Styles
   ============================================ */

/* Larger modal dialog for Edit Policy */
.modal-dialog-large {
    max-width: 90%;
    width: 90%;
}

@media (min-width: 1200px) {
    .modal-dialog-large {
        max-width: 1100px;
        width: 1100px;
    }
}

@media (max-width: 991px) {
    .modal-dialog-large {
        max-width: 95%;
        width: 95%;
        margin: 10px auto;
    }
}

@media (max-width: 767px) {
    .modal-dialog-large {
        max-width: 100%;
        width: 100%;
        margin: 0;
    }
}

/* Edit Policy Modal Container */
#editPolicyModal .modal-content {
    border-radius: 8px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

/* Edit Policy Modal Header */
#editPolicyModal .modal-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    padding: 15px 20px;
    border-bottom: 3px solid #0056b3;
}

#editPolicyModal .modal-header .modal-title {
    font-weight: 600;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

#editPolicyModal .modal-header .close {
    color: white;
    opacity: 0.8;
    font-size: 1.5rem;
    padding: 0;
    margin: 0;
    width: 30px;
    height: 30px;
    line-height: 30px;
    text-align: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

#editPolicyModal .modal-header .close:hover {
    opacity: 1;
    background: rgba(255, 255, 255, 0.2);
}

/* Edit Policy Modal Body */
#editPolicyModal .modal-body {
    padding: 20px;
    background: #f8f9fa;
    max-height: 70vh;
    overflow-y: auto;
}

/* Form Groups */
#editPolicyModal .form-group {
    margin-bottom: 20px;
}

#editPolicyModal .form-group:last-child {
    margin-bottom: 0;
}

/* Form Labels */
#editPolicyModal label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 0.9rem;
    display: block;
}

#editPolicyModal label:after {
    content: '';
}

/* Required field indicator */
#editPolicyModal label[for=""],
#editPolicyModal .form-group:has(input[required]) label,
#editPolicyModal .form-group:has(textarea[required]) label,
#editPolicyModal .form-group:has(select[required]) label {
    position: relative;
}

#editPolicyModal .form-group:has(input[required]) label::after,
#editPolicyModal .form-group:has(textarea[required]) label::after,
#editPolicyModal .form-group:has(select[required]) label::after {
    content: ' *';
    color: #dc3545;
}

/* Form Controls */
#editPolicyModal .form-control {
    border: 2px solid #ddd;
    border-radius: 6px;
    padding: 10px 12px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    background: white;
}

#editPolicyModal .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
    outline: none;
}

#editPolicyModal .form-control:hover {
    border-color: #bbb;
}

/* Textarea specific */
#editPolicyModal textarea.form-control {
    resize: vertical;
    min-height: 200px;
    font-family: inherit;
    line-height: 1.6;
}

/* Select specific */
#editPolicyModal select.form-control {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 35px;
}

/* Input focus states */
#editPolicyModal .form-control.is-valid {
    border-color: #28a745;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    padding-right: calc(1.5em + 0.75rem);
}

#editPolicyModal .form-control.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23dc3545' viewBox='0 0 16 16'%3E%3Cpath d='M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    padding-right: calc(1.5em + 0.75rem);
}

/* Row styling */
#editPolicyModal .row {
    margin-bottom: 15px;
}

#editPolicyModal .row:last-child {
    margin-bottom: 0;
}

/* Column spacing */
#editPolicyModal .col-md-4,
#editPolicyModal .col-md-8 {
    padding-right: 10px;
    padding-left: 10px;
}

/* Validation feedback */
#editPolicyModal .valid-feedback {
    color: #28a745;
    font-size: 0.8rem;
    margin-top: 5px;
}

#editPolicyModal .invalid-feedback {
    color: #dc3545;
    font-size: 0.8rem;
    margin-top: 5px;
    display: block;
}

/* Edit Policy Modal Footer */
#editPolicyModal .modal-footer {
    padding: 15px 20px;
    background: white;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Footer buttons */
#editPolicyModal .modal-footer .btn {
    padding: 10px 20px;
    font-weight: 600;
    border-radius: 6px;
    transition: all 0.2s ease;
    min-width: 100px;
}

#editPolicyModal .modal-footer .btn-default {
    background: #f8f9fa;
    border-color: #ddd;
    color: #333;
}

#editPolicyModal .modal-footer .btn-default:hover {
    background: #e9ecef;
    border-color: #ccc;
}

#editPolicyModal .modal-footer .btn-primary {
    background: #007bff;
    border-color: #007bff;
}

#editPolicyModal .modal-footer .btn-primary:hover {
    background: #0056b3;
    border-color: #0056b3;
}

/* Loading state */
#editPolicyModal .loading-spinner {
    text-align: center;
    padding: 40px;
    color: #6c757d;
}

#editPolicyModal .loading-spinner i {
    color: #007bff;
}

/* ============================================
   Responsive Styles
   ============================================ */

/* Tablet and below */
@media (max-width: 991px) {
    #editPolicyModal .modal-body {
        max-height: 60vh;
    }
    
    #editPolicyModal .row {
        margin-bottom: 10px;
    }
    
    #editPolicyModal .col-md-4,
    #editPolicyModal .col-md-8 {
        margin-bottom: 15px;
    }
}

/* Mobile */
@media (max-width: 767px) {
    #editPolicyModal .modal-header {
        padding: 12px 15px;
    }
    
    #editPolicyModal .modal-header .modal-title {
        font-size: 1rem;
    }
    
    #editPolicyModal .modal-body {
        padding: 15px;
        max-height: 55vh;
    }
    
    #editPolicyModal .form-group {
        margin-bottom: 15px;
    }
    
    #editPolicyModal textarea.form-control {
        min-height: 150px;
    }
    
    #editPolicyModal .modal-footer {
        padding: 12px 15px;
        flex-direction: column;
        gap: 10px;
    }
    
    #editPolicyModal .modal-footer .btn {
        width: 100%;
        margin: 0;
    }
}

/* ============================================
   Focus and Hover States
   ============================================ */

/* Input focus visible */
#editPolicyModal .form-control:focus-visible {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}

/* Button hover effects */
#editPolicyModal .modal-footer .btn:active {
    transform: scale(0.98);
}

/* Label hover for better UX */
#editPolicyModal label:hover {
    color: #007bff;
}

/* Custom scrollbar for modal body */
#editPolicyModal .modal-body::-webkit-scrollbar {
    width: 8px;
}

#editPolicyModal .modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

#editPolicyModal .modal-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

#editPolicyModal .modal-body::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}
</style>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        
        <!-- Alerts -->
        <?php if ($message): ?>
        <div class="alert alert-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-info-circle"></i> <?php echo $message; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-edit"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Drafts</span>
                        <span class="info-box-number"><?php echo $stats['draft']; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pending</span>
                        <span class="info-box-number"><?php echo $stats['pending']; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-globe"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Published</span>
                        <span class="info-box-number"><?php echo $stats['published']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search Bar -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <a href="?tab=all" class="btn btn-sm <?php echo $tab === 'all' && empty($status) ? 'btn-primary' : 'btn-default'; ?>">All</a>
                                <?php if ($canApprove || $isSupervisor): ?>
                                <a href="?tab=pending" class="btn btn-sm <?php echo $tab === 'pending' ? 'btn-warning' : 'btn-default'; ?>">Pending Approval</a>
                                <?php endif; ?>
                                <?php if ($isSupervisor): ?>
                                <a href="?tab=supervisor" class="btn btn-sm <?php echo $tab === 'supervisor' ? 'btn-info' : 'btn-default'; ?>">Supervisor Review</a>
                                <?php endif; ?>
                                <?php if ($isManagement || $isHR): ?>
                                <a href="?tab=approved" class="btn btn-sm <?php echo $tab === 'approved' ? 'btn-success' : 'btn-default'; ?>">Ready to Publish</a>
                                <?php endif; ?>
                                <a href="?status=Draft" class="btn btn-sm <?php echo $status === 'Draft' ? 'btn-secondary' : 'btn-default'; ?>">Drafts</a>
                                <a href="?status=Pending Approval" class="btn btn-sm <?php echo $status === 'Pending Approval' ? 'btn-warning' : 'btn-default'; ?>">Pending Approval</a>
                                <a href="?status=Rejected" class="btn btn-sm <?php echo $status === 'Rejected' ? 'btn-danger' : 'btn-default'; ?>">Rejected</a>
                                <a href="?status=Published" class="btn btn-sm <?php echo $status === 'Published' ? 'btn-primary' : 'btn-default'; ?>">Published</a>
                            </div>
                            
                            <?php if ($canCreatePolicy): ?>
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createPolicyModal">
                                <i class="fas fa-plus"></i> Create New Policy
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Policies Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Policies</h3>
                        <div class="card-tools">
                            <!-- Search Form -->
                            <form method="GET" class="form-inline">
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search policies..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                                <?php if (!empty($status)): ?>
                                <input type="hidden" name="status" value="<?php echo $status; ?>">
                                <?php else: ?>
                                <input type="hidden" name="tab" value="<?php echo $tab; ?>">
                                <?php endif; ?>
                                <button type="submit" class="btn btn-sm btn-primary ml-2"><i class="fas fa-search"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Version</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($policies)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-folder-open fa-2x mb-2"></i>
                                        <p>No policies found</p>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($policies as $policy): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($policy['title'] ?? 'Untitled'); ?></strong>
                                        <?php if (!empty($policy['is_mandatory'])): ?>
                                        <span class="badge badge-danger">Mandatory</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($policy['category'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($policy['version'] ?? '1.0'); ?></td>
                                    <td>
                                        <?php
                                        $statusClass = 'secondary';
                                        $statusText = $policy['status'] ?? 'Unknown';
                                        if ($statusText === 'Draft') $statusClass = 'secondary';
                                        elseif ($statusText === 'Pending Approval') $statusClass = 'warning';
                                        elseif ($statusText === 'Approved') $statusClass = 'success';
                                        elseif ($statusText === 'Published') $statusClass = 'info';
                                        elseif ($statusText === 'Rejected') $statusClass = 'danger';
                                        ?>
                                        <span class="badge badge-<?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                    </td>
                                    <td><?php echo !empty($policy['updated_at']) ? date('M d, Y', strtotime($policy['updated_at'])) : 'N/A'; ?></td>
                                    <td>
                                        <!-- View Button - Always enabled -->
                                        <button class="btn btn-xs btn-info" onclick="viewPolicy(<?php echo $policy['id']; ?>)" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <?php
                                        // Determine if Edit/Delete should be enabled based on status
                                        $policyStatus = $policy['status'] ?? '';
                                        $canEdit = in_array($policyStatus, ['Draft', 'Pending Approval', 'Supervisor Approved']);
                                        $canDelete = in_array($policyStatus, ['Draft', 'Pending Approval']);
                                        ?>
                                        
                                        <!-- Edit Button - Enabled for Draft/Pending/Supervisor Approved, disabled for Published/Rejected -->
                                        <?php if ($canEdit): ?>
                                        <?php if ($canCreatePolicy): ?>
                                        <button class="btn btn-xs btn-primary" onclick="editPolicy(<?php echo $policy['id']; ?>)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php else: ?>
                                        <button class="btn btn-xs btn-primary" title="Edit (Disabled for <?php echo htmlspecialchars($policyStatus); ?>)" disabled>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php endif; ?>
                                        
                                        <!-- Send/Submit for Approval Button - Enabled for Draft, disabled for other statuses -->
                                        <?php if ($policyStatus === 'Draft' && $canCreatePolicy): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="submit">
                                            <input type="hidden" name="policy_id" value="<?php echo $policy['id']; ?>">
                                            <button type="submit" class="btn btn-xs btn-warning" title="Send for Approval">
                                                <i class="fas fa-paper-plane"></i> Send
                                            </button>
                                        </form>
                                        <?php else: ?>
                                        <button class="btn btn-xs btn-warning" title="Send (Disabled for <?php echo htmlspecialchars($policyStatus); ?>)" disabled>
                                            <i class="fas fa-paper-plane"></i> Send
                                        </button>
                                        <?php endif; ?>
                                        
                                        <!-- Delete Button - Enabled for Draft/Pending, disabled for Published/Rejected -->
                                        <?php if ($canDelete): ?>
                                        <?php if ($canCreatePolicy): ?>
                                        <button class="btn btn-xs btn-danger" onclick="deletePolicy(<?php echo $policy['id']; ?>)" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php else: ?>
                                        <button class="btn btn-xs btn-danger" title="Delete (Disabled for <?php echo htmlspecialchars($policyStatus); ?>)" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                        
                                        <!-- Supervisor Approve Button - Shows for Pending Approval with permissions -->
                                        <?php if ($policyStatus === 'Pending Approval' && ($isSupervisor || $isHR || $isManagement || $canApprove)): ?>
                                        <button class="btn btn-xs btn-info" onclick="supervisorApprove(<?php echo $policy['id']; ?>)" title="Supervisor Approve">
                                            <i class="fas fa-user-check"></i>
                                        </button>
                                        <button class="btn btn-xs btn-danger" onclick="rejectPolicy(<?php echo $policy['id']; ?>)" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <?php endif; ?>
                                        
                                        <!-- Final Approval Button - Shows for Supervisor Approved with Management -->
                                        <?php if ($policyStatus === 'Supervisor Approved' && $isManagement): ?>
                                        <button class="btn btn-xs btn-success" onclick="approvePolicy(<?php echo $policy['id']; ?>)" title="Final Approval">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                        <button class="btn btn-xs btn-danger" onclick="rejectPolicy(<?php echo $policy['id']; ?>)" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <?php endif; ?>
                                        
                                        <!-- Publish Button - Shows for Approved with HR/Management -->
                                        <?php if ($policyStatus === 'Approved' && ($isHR || $isManagement)): ?>
                                        <button class="btn btn-xs btn-info" onclick="publishPolicy(<?php echo $policy['id']; ?>)" title="Publish">
                                            <i class="fas fa-globe"></i>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Create Policy Modal -->
<div class="modal fade" id="createPolicyModal" data-backdrop="true" data-keyboard="true" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title"><i class="fas fa-plus"></i> Create New Policy</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Policy Title *</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Category *</label>
                                <select name="category" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['name']; ?>"><?php echo $cat['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Version</label>
                                <input type="text" name="version" class="form-control" value="1.0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Mandatory?</label>
                                <div class="form-check">
                                    <input type="checkbox" name="is_mandatory" value="1" class="form-check-input" id="mandatoryCheck">
                                    <label class="form-check-label" for="mandatoryCheck">Yes, all employees must acknowledge</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Acknowledgment Required?</label>
                                <div class="form-check">
                                    <input type="checkbox" name="acknowledgment_required" value="1" class="form-check-input" id="ackCheck" checked>
                                    <label class="form-check-label" for="ackCheck">Yes, require employee acknowledgment</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Policy Content *</label>
                        <textarea name="content" class="form-control" rows="15" required placeholder="Enter policy content here..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create as Draft</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Policy Modal -->
<div class="modal fade" id="viewPolicyModal" data-backdrop="true" data-keyboard="true" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1040;">
    <div class="modal-dialog modal-xl" style="z-index: 1041;">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title" id="viewPolicyTitle">Policy Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Category:</strong> <span id="viewPolicyCategory">N/A</span></p>
                        <p><strong>Version:</strong> <span id="viewPolicyVersion">1.0</span></p>
                        <p><strong>Status:</strong> <span id="viewPolicyStatus">N/A</span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Mandatory:</strong> <span id="viewPolicyMandatory"></span></p>
                        <p><strong>Acknowledgment Required:</strong> <span id="viewPolicyAck"></span></p>
                    </div>
                </div>
                <hr>
                <div id="viewPolicyContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Policy Modal -->
<div class="modal fade" id="editPolicyModal" data-backdrop="true" data-keyboard="true" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1050;">
    <div class="modal-dialog modal-dialog-large" style="z-index: 1051;">
        <div class="modal-content">
            <form method="POST" id="editPolicyForm">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Policy</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="editPolicyBody">
                    <!-- Dynamic content -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Approve Policy Modal -->
<div class="modal fade" id="approvePolicyModal" data-backdrop="true" data-keyboard="true" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered" style="z-index: 1071;">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-success">
                    <h4 class="modal-title"><i class="fas fa-check"></i> Final Approval</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="approve">
                    <input type="hidden" name="policy_id" id="approvePolicyId">
                    <div class="form-group">
                        <label>Remarks (Optional)</label>
                        <textarea name="remarks" class="form-control" rows="3" placeholder="Add any remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Supervisor Approval Modal -->
<div class="modal fade" id="supervisorApproveModal" data-backdrop="true" data-keyboard="true" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered" style="z-index: 1066;">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-info">
                    <h4 class="modal-title"><i class="fas fa-user-check"></i> Supervisor Approval</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="supervisor_approve">
                    <input type="hidden" name="policy_id" id="supervisorPolicyId">
                    <div class="form-group">
                        <label>Remarks (Optional)</label>
                        <textarea name="remarks" class="form-control" rows="3" placeholder="Add any remarks for the supervisor approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info"><i class="fas fa-user-check"></i> Approve as Supervisor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Policy Modal -->
<div class="modal fade" id="rejectPolicyModal" data-backdrop="true" data-keyboard="true" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1080;">
    <div class="modal-dialog modal-dialog-centered" style="z-index: 1081;">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-danger">
                    <h4 class="modal-title"><i class="fas fa-times"></i> Reject Policy</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="reject">
                    <input type="hidden" name="policy_id" id="rejectPolicyId">
                    <div class="form-group">
                        <label>Reason for Rejection *</label>
                        <textarea name="remarks" class="form-control" rows="3" required placeholder="Please explain why this policy is rejected..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i> Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Publish Policy Modal -->
<div class="modal fade" id="publishPolicyModal" data-backdrop="true" data-keyboard="true" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1090;">
    <div class="modal-dialog modal-dialog-centered" style="z-index: 1091;">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-info">
                    <h4 class="modal-title"><i class="fas fa-globe"></i> Publish Policy</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="publish">
                    <input type="hidden" name="policy_id" id="publishPolicyId">
                    <div class="form-group">
                        <label>Effective Date</label>
                        <input type="date" name="effective_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        <small class="text-muted">Leave empty for immediate effect</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Once published, employees will be able to view and acknowledge this policy.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info"><i class="fas fa-globe"></i> Publish</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="compliance.js"></script>
<script src="policy_admin.js"></script>



<?php
// Include Footer Template
include "../components/footer_template.php";
?>

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
