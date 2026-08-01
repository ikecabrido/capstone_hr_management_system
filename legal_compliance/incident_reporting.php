<?php
/**
 * Incident Reporting Module
 * Main view page for incident reporting and management
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once '../auth/database.php';
require_once 'models/Incident.php';
require_once 'models/Employee.php';
require_once 'models/DisciplinaryAction.php';

// Initialize models
$incidentModel = new Incident();
$employeeModel = new Employee();
$disciplinaryModel = new DisciplinaryAction();

// Get current user info
$userId = $_SESSION['user_id'] ?? null;
$userRole = $_SESSION['user_role'] ?? 'employee';
$userName = $_SESSION['user_name'] ?? 'User';

// Get statistics
$incidentStats = $incidentModel->getStatistics();
$disciplinaryStats = $disciplinaryModel->getStatistics();

// Get HR employees for assignment dropdown
$hrEmployees = $employeeModel->getHREmployees();

// Get incident types FROM lc_database
$incidentTypes = [
    'workplace_safety' => 'Workplace Safety',
    'harassment' => 'Harassment',
    'policy_violation' => 'Policy Violation',
    'complaint' => 'Complaint',
    'other' => 'Other'
];

// Get severity levels
$severityLevels = [
    'low' => 'Low',
    'medium' => 'Medium',
    'high' => 'High',
    'critical' => 'Critical'
];

// Get status options
$statusOptions = [
    'submitted' => 'Submitted',
    'under_review' => 'Under Review',
    'investigation' => 'Investigation',
    'escalated' => 'Escalated',
    'resolved' => 'Resolved',
    'closed' => 'Closed'
];

// Get disciplinary action types
$actionTypes = [
    'verbal_warning' => 'Verbal Warning',
    'written_warning' => 'Written Warning',
    'suspension' => 'Suspension',
    'termination' => 'Termination',
    'final_warning' => 'Final Warning',
    'other' => 'Other'
];

// Get disciplinary action statuses
$actionStatuses = [
    'pending' => 'Pending',
    'issued' => 'Issued',
    'appealed' => 'Appealed',
    'upheld' => 'Upheld',
    'dismissed' => 'Dismissed'
];

// Handle GET parameters for PHP-dependent modals
$showCreateForm = isset($_GET['create']) && $_GET['create'] == '1';
$viewIncidentId = isset($_GET['view']) ? (int)$_GET['view'] : null;
$editIncidentId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$incidentDetails = null;
$editIncidentData = null;

if ($viewIncidentId) {
    $incidentDetails = $incidentModel->getById($viewIncidentId);
    if ($incidentDetails) {
        $incidentDetails['evidence'] = $incidentModel->getEvidenceByIncidentId($viewIncidentId);
    }
}

if ($editIncidentId) {
    $editIncidentData = $incidentModel->getById($editIncidentId);
    if ($editIncidentData) {
        $editIncidentData['evidence'] = $incidentModel->getEvidenceByIncidentId($editIncidentId);
    }
}

// Handle POST requests
$message = null;
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'created') {
        $message = ['type' => 'success', 'text' => 'Incident reported successfully'];
    } elseif ($_GET['success'] === 'updated') {
        $message = ['type' => 'success', 'text' => 'Incident updated successfully'];
    }
}
if (isset($error)) {
    $message = ['type' => 'error', 'text' => $error];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'controllers/IncidentController.php';
    $controller = new IncidentController();
    
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'create') {
            $result = $controller->create();
            if ($result['success']) {
                header('Location: incident_reporting.php?success=created');
                exit;
            } else {
                $error = $result['message'];
                error_log('Incident creation error: ' . $result['message']);
            }
        } elseif ($action === 'update') {
            $id = (int)$_POST['id'];
            $result = $controller->update($id);
            if ($result['success']) {
                header('Location: incident_reporting.php?success=updated');
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}

// Determine base paths for assets
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$basePath = ($currentFolder === 'legal_compliance') ? '../' : '../';
$componentsPath = $basePath . 'legal_compliance/components/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident Reporting - BCP HR System</title>
    
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=swap">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/plugins/fontawesome-free/css/all.min.css">
    
    <!-- AdminLTE -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/dist/css/adminlte.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $componentsPath ?>custom.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- DataTables -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= $basePath ?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    
    <!-- Incident Reporting CSS -->
    <link rel="stylesheet" href="css/incident_reporting.css">
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
                        <h1 class="m-0">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Incident Reporting
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Incident Reporting</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="content">
            <div class="container-fluid">
                <?php if ($message): ?>
                <div class="alert alert-<?= $message['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <?= htmlspecialchars($message['text']) ?>
                </div>
                <?php endif; ?>
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?php echo $incidentStats['total'] ?? 0; ?></h3>
                                <p>Total Incidents</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?php echo $incidentStats['submitted'] ?? 0; ?></h3>
                                <p>Pending Review</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><?php echo $incidentStats['investigation'] ?? 0; ?></h3>
                                <p>Under Investigation</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?php echo $incidentStats['resolved'] ?? 0; ?></h3>
                                <p>Resolved</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?php echo $incidentStats['critical_severity'] ?? 0; ?></h3>
                                <p>Critical Incidents</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3><?php echo $disciplinaryStats['total'] ?? 0; ?></h3>
                                <p>Disciplinary Actions</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-gavel"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="row mb-3">
                    <div class="col-12">
                        <a href="?create=1" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Report New Incident
                        </a>
                        <button class="btn btn-secondary" id="btn-view-incidents">
                            <i class="fas fa-list"></i> View All Incidents
                        </button>
                        <button class="btn btn-secondary" id="btn-view-disciplinary">
                            <i class="fas fa-gavel"></i> View Disciplinary Actions
                        </button>
                    </div>
                </div>
                
                <!-- Create Incident Form (PHP dependent) -->
                <?php if ($showCreateForm): ?>
                <div class="card" id="create-incident-form">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">
                            <i class="fas fa-plus"></i> Report New Incident
                        </h3>
                        <div class="card-tools">
                            <a href="incident_reporting.php" class="btn btn-light btn-sm">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                    <form action="incident_reporting.php" method="POST" enctype="multipart/form-data" id="create-incident-form-element">
                        <input type="hidden" name="action" value="create">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="incident-title">Incident Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="incident-title" name="title" required placeholder="Brief description of the incident">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="incident-type">Incident Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="incident-type" name="incident_type" required>
                                            <option value="">Select Type</option>
                                            <?php foreach ($incidentTypes as $key => $label): ?>
                                                <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="incident-category">Category <span class="text-danger">*</span></label>
                                        <select class="form-control" id="incident-category" name="type" required>
                                            <option value="">Select Category</option>
                                            <option value="workplace_safety">Workplace Safety</option>
                                            <option value="harassment">Harassment</option>
                                            <option value="policy_violation">Policy Violation</option>
                                            <option value="complaint">Complaint</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="incident-severity">Severity <span class="text-danger">*</span></label>
                                        <select class="form-control" id="incident-severity" name="severity" required>
                                            <option value="">Select Severity</option>
                                            <?php foreach ($severityLevels as $value => $label): ?>
                                                <option value="<?= htmlspecialchars($value) ?>"><?= htmlspecialchars($label) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="incident-location">Location</label>
                                        <input type="text" class="form-control" id="incident-location" name="location" placeholder="Where did it happen?">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="incident-date">Incident Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="incident-date" name="incident_date" required value="<?= date('Y-m-d') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="incident-time">Incident Time</label>
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                            <select id="incident-time_hour" name="incident_time_hour" class="form-control" style="flex: 1;">
                                                <option value="">Hour</option>
                                                <?php for ($h = 0; $h < 24; $h++): ?>
                                                    <option value="<?php echo sprintf('%02d', $h); ?>"><?php echo sprintf('%02d', $h); ?></option>
                                                <?php endfor; ?>
                                            </select>
                                            <span>:</span>
                                            <select id="incident-time_minute" name="incident_time_minute" class="form-control" style="flex: 1;">
                                                <option value="">Minute</option>
                                                <?php for ($m = 0; $m < 60; $m++): ?>
                                                    <option value="<?php echo sprintf('%02d', $m); ?>"><?php echo sprintf('%02d', $m); ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="incident-description">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="incident-description" name="description" rows="4" required placeholder="Provide detailed description of the incident..."></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="incident-respondent">Respondent(s) (Person being reported)</label>
                                        <div class="input-group mb-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            </div>
                                            <input type="text" class="form-control" id="respondent-search" placeholder="Search employees by name...">
                                        </div>
                                        <div class="border p-2" style="max-height: 200px; overflow-y: auto;">
                                            <?php 
                                            $employees = $employeeModel->getAll();
                                            foreach ($employees as $emp): 
                                            ?>
                                                <div class="form-check respondent-item">
                                                    <input class="form-check-input respondent-checkbox" type="checkbox" name="respondent_id[]" value="<?= $emp['id'] ?>" id="respondent-<?= $emp['id'] ?>">
                                                    <label class="form-check-label" for="respondent-<?= $emp['id'] ?>">
                                                        <?= htmlspecialchars($emp['first_name']) ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <small class="form-text text-muted">Search and check boxes to select multiple employees</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="incident-respondent-relationship">Relationship to Reporter</label>
                                        <select class="form-control" id="incident-respondent-relationship" name="respondent_relationship">
                                            <option value="co_worker">Co-worker</option>
                                            <option value="supervisor">Supervisor</option>
                                            <option value="subordinate">Subordinate</option>
                                            <option value="external">External</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="incident-reporter-role">Your Role in Incident</label>
                                        <select class="form-control" id="incident-reporter-role" name="reporter_role">
                                            <option value="reporter">Reporter</option>
                                            <option value="witness">Witness</option>
                                            <option value="victim">Victim</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="incident-assigned">Assign To (HR Officer)</label>
                                        <select class="form-control" id="incident-assigned" name="assigned_to">
                                            <option value="">Auto-assign</option>
                                            <?php foreach ($hrEmployees as $hr): ?>
                                                <option value="<?= $hr['id'] ?>">
                                                    <?= htmlspecialchars($hr['first_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="incident-evidence">Evidence Files</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="incident-evidence" name="evidence[]" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv">
                                    <label class="custom-file-label" for="incident-evidence">Choose files</label>
                                </div>
                                <small class="form-text text-muted">You can upload multiple files (images, documents, etc.). Max 10MB per file.</small>
                                <div id="evidence-preview" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Incident
                            </button>
                            <a href="incident_reporting.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
                
                <!-- View Incident Details (PHP dependent) -->
                <?php if ($incidentDetails): ?>
                <div class="card" id="view-incident-details">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title">
                            <i class="fas fa-eye"></i> Incident Details - #<?= htmlspecialchars($incidentDetails['incident_id']) ?>
                        </h3>
                        <div class="card-tools">
                            <a href="incident_reporting.php" class="btn btn-light btn-sm">
                                <i class="fas fa-times"></i> Close
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Basic Information</h5>
                                <table class="table table-bordered">
                                    <tr><th>Incident ID</th><td><?= htmlspecialchars($incidentDetails['incident_id']) ?></td></tr>
                                    <tr><th>Title</th><td><?= htmlspecialchars($incidentDetails['title']) ?></td></tr>
                                    <tr><th>Type</th><td><?= htmlspecialchars($incidentDetails['incident_type']) ?></td></tr>
                                    <tr><th>Category</th><td><?= htmlspecialchars($incidentDetails['type']) ?></td></tr>
                                    <tr><th>Severity</th><td><span class="badge badge-<?= $incidentDetails['severity'] == 'critical' ? 'dark' : ($incidentDetails['severity'] == 'high' ? 'danger' : ($incidentDetails['severity'] == 'medium' ? 'warning' : 'info')) ?>"><?= htmlspecialchars($incidentDetails['severity']) ?></span></td></tr>
                                    <tr><th>Status</th><td><span class="badge badge-<?= $incidentDetails['status'] == 'resolved' ? 'success' : ($incidentDetails['status'] == 'investigation' ? 'warning' : ($incidentDetails['status'] == 'escalated' ? 'danger' : 'primary')) ?>"><?= htmlspecialchars(str_replace('_', ' ', $incidentDetails['status'])) ?></span></td></tr>
                                    <tr><th>Date</th><td><?= htmlspecialchars($incidentDetails['incident_date']) ?> <?= $incidentDetails['incident_time'] ? htmlspecialchars($incidentDetails['incident_time']) : '' ?></td></tr>
                                    <tr><th>Location</th><td><?= htmlspecialchars($incidentDetails['location'] ?: 'Not specified') ?></td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>People Involved</h5>
                                <table class="table table-bordered">
                                    <tr><th>Reporter</th><td><?= htmlspecialchars($incidentDetails['reporter_name']) ?> (<?= htmlspecialchars($incidentDetails['reporter_employee_no'] ?? 'N/A') ?>)</td></tr>
                                    <tr><th>Reporter Dept.</th><td><?= htmlspecialchars($incidentDetails['reporter_department'] ?? 'N/A') ?></td></tr>
                                    <tr><th>Reporter Pos.</th><td><?= htmlspecialchars($incidentDetails['reporter_position'] ?? 'N/A') ?></td></tr>
                                    <tr><th>Respondent</th><td><?= htmlspecialchars($incidentDetails['respondent_name'] ?: 'Not specified') ?> (<?= htmlspecialchars($incidentDetails['respondent_employee_no'] ?? 'N/A') ?>)</td></tr>
                                    <tr><th>Respondent Dept.</th><td><?= htmlspecialchars($incidentDetails['respondent_department'] ?? 'N/A') ?></td></tr>
                                    <tr><th>Respondent Pos.</th><td><?= htmlspecialchars($incidentDetails['respondent_position'] ?? 'N/A') ?></td></tr>
                                    <tr><th>Relationship</th><td><?= htmlspecialchars($incidentDetails['respondent_relationship'] ?: 'Not specified') ?></td></tr>
                                    <tr><th>Reporter Role</th><td><?= htmlspecialchars($incidentDetails['reporter_role'] ?: 'Not specified') ?></td></tr>
                                    <tr><th>Assigned To</th><td><?= htmlspecialchars($incidentDetails['assigned_name'] ?: 'Not assigned') ?></td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h5>Description</h5>
                                <div class="alert alert-light border-left-primary">
                                    <?= nl2br(htmlspecialchars($incidentDetails['description'])) ?>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($incidentDetails['evidence'])): ?>
                        <div class="row">
                            <div class="col-12">
                                <h5>Evidence Files</h5>
                                <div class="list-group">
                                    <?php foreach ($incidentDetails['evidence'] as $file): ?>
                                        <a href="<?= htmlspecialchars($file['file_path']) ?>" target="_blank" class="list-group-item list-group-item-action">
                                            <i class="fas fa-file"></i> <?= htmlspecialchars($file['file_name']) ?>
                                            <span class="float-right text-muted small"><?= date('M d, Y H:i', strtotime($file['uploaded_at'])) ?></span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="?edit=<?= $incidentDetails['id'] ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Incident
                        </a>
                        <button type="button" class="btn btn-danger btn-create-disciplinary" data-id="<?= $incidentDetails['id'] ?>">
                            <i class="fas fa-gavel"></i> Create Disciplinary Action
                        </button>
                        <a href="incident_reporting.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Close
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Edit Incident Form (PHP dependent) -->
                <?php if ($editIncidentData): ?>
                <div class="card" id="edit-incident-form">
                    <div class="card-header bg-warning text-white">
                        <h3 class="card-title">
                            <i class="fas fa-edit"></i> Edit Incident - #<?= htmlspecialchars($editIncidentData['incident_id']) ?>
                        </h3>
                        <div class="card-tools">
                            <a href="incident_reporting.php" class="btn btn-light btn-sm">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                    <form action="incident_reporting.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= $editIncidentData['id'] ?>">
                        <input type="hidden" name="id" value="<?= $editIncidentData['id'] ?>">
                        <div class="card-body">
                            <!-- Similar form fields as create, pre-filled with data -->
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="edit-incident-title">Incident Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="edit-incident-title" name="title" required value="<?= htmlspecialchars($editIncidentData['title']) ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit-incident-type">Incident Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="edit-incident-type" name="incident_type" required>
                                            <option value="">Select Type</option>
                                            <?php foreach ($incidentTypes as $key => $label): ?>
                                                <option value="<?= htmlspecialchars($key) ?>" <?= $editIncidentData['incident_type'] == $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Add other fields similarly -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit-incident-category">Category <span class="text-danger">*</span></label>
                                        <select class="form-control" id="edit-incident-category" name="type" required>
                                            <option value="">Select Category</option>
                                            <option value="workplace_safety" <?= $editIncidentData['type'] == 'workplace_safety' ? 'selected' : '' ?>>Workplace Safety</option>
                                            <option value="harassment" <?= $editIncidentData['type'] == 'harassment' ? 'selected' : '' ?>>Harassment</option>
                                            <option value="policy_violation" <?= $editIncidentData['type'] == 'policy_violation' ? 'selected' : '' ?>>Policy Violation</option>
                                            <option value="complaint" <?= $editIncidentData['type'] == 'complaint' ? 'selected' : '' ?>>Complaint</option>
                                            <option value="other" <?= $editIncidentData['type'] == 'other' ? 'selected' : '' ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit-incident-severity">Severity <span class="text-danger">*</span></label>
                                        <select class="form-control" id="edit-incident-severity" name="severity" required>
                                            <option value="">Select Severity</option>
                                            <?php foreach ($severityLevels as $value => $label): ?>
                                                <option value="<?= htmlspecialchars($value) ?>" <?= $editIncidentData['severity'] == $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit-incident-location">Location</label>
                                        <input type="text" class="form-control" id="edit-incident-location" name="location" value="<?= htmlspecialchars($editIncidentData['location']) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit-incident-date">Incident Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="edit-incident-date" name="incident_date" required value="<?= htmlspecialchars($editIncidentData['incident_date']) ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit-incident-time">Incident Time</label>
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                            <select id="edit-incident-time_hour" name="incident_time_hour" class="form-control" style="flex: 1;">
                                                <option value="">Hour</option>
                                                <?php 
                                                $currentHour = isset($editIncidentData['incident_time']) ? explode(':', $editIncidentData['incident_time'])[0] : '';
                                                for ($h = 0; $h < 24; $h++): 
                                                    $selected = ($currentHour == sprintf('%02d', $h)) ? 'selected' : '';
                                                ?>
                                                    <option value="<?php echo sprintf('%02d', $h); ?>" <?php echo $selected; ?>><?php echo sprintf('%02d', $h); ?></option>
                                                <?php endfor; ?>
                                            </select>
                                            <span>:</span>
                                            <select id="edit-incident-time_minute" name="incident_time_minute" class="form-control" style="flex: 1;">
                                                <option value="">Minute</option>
                                                <?php 
                                                $currentMinute = isset($editIncidentData['incident_time']) ? explode(':', $editIncidentData['incident_time'])[1] : '';
                                                for ($m = 0; $m < 60; $m++): 
                                                    $selected = ($currentMinute == sprintf('%02d', $m)) ? 'selected' : '';
                                                ?>
                                                    <option value="<?php echo sprintf('%02d', $m); ?>" <?php echo $selected; ?>><?php echo sprintf('%02d', $m); ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="edit-incident-description">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="edit-incident-description" name="description" rows="4" required><?= htmlspecialchars($editIncidentData['description']) ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                 <label for="edit-incident-evidence">Add More Evidence Files</label>
                                 <div class="custom-file">
                                     <input type="file" class="custom-file-input" id="edit-incident-evidence" name="evidence[]" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv">
                                     <label class="custom-file-label" for="edit-incident-evidence">Choose files</label>
                                 </div>
                                 <small class="form-text text-muted">You can upload additional files. Max 10MB per file.</small>
                                 <div id="edit-evidence-preview" class="mt-2 evidence-preview"></div>
                             </div>

                            <?php if (!empty($editIncidentData['evidence'])): ?>
                            <div class="form-group">
                                <label>Current Evidence</label>
                                <div class="list-group">
                                    <?php foreach ($editIncidentData['evidence'] as $file): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-file"></i> <?= htmlspecialchars($file['file_name']) ?></span>
                                            <a href="<?= htmlspecialchars($file['file_path']) ?>" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            <!-- Add other fields -->
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Incident
                            </button>
                            <a href="incident_reporting.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
                
                <!-- Incidents Table -->
                <div class="card" id="incidents-section">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-2"></i>
                            Recent Incidents
                        </h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 150px;">
                                <select id="filter-status" class="form-control form-control-sm">
                                    <option value="">All Statuses</option>
                                    <?php foreach ($statusOptions as $value => $label): ?>
                                        <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="input-group input-group-sm" style="width: 150px; margin-left: 5px;">
                                <select id="filter-severity" class="form-control form-control-sm">
                                    <option value="">All Severities</option>
                                    <?php foreach ($severityLevels as $value => $label): ?>
                                        <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="input-group input-group-sm" style="width: 200px; margin-left: 5px;">
                                <input type="text" id="filter-search" class="form-control form-control-sm" placeholder="Search incidents...">
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap" id="incidents-table">
                            <thead>
                                <tr>
                                    <th>Incident ID</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Severity</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Reporter</th>
                                    <th>Respondent</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer clearfix" id="incidents-pagination">
                        <!-- Pagination will be loaded via AJAX -->
                    </div>
                </div>
                
                <!-- Disciplinary Actions Table -->
                <div class="card" id="disciplinary-section" style="display: none;">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-gavel mr-2"></i>
                            Disciplinary Actions
                        </h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 150px;">
                                <select id="filter-action-status" class="form-control form-control-sm">
                                    <option value="">All Statuses</option>
                                    <?php foreach ($actionStatuses as $value => $label): ?>
                                        <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="input-group input-group-sm" style="width: 150px; margin-left: 5px;">
                                <select id="filter-action-type" class="form-control form-control-sm">
                                    <option value="">All Types</option>
                                    <?php foreach ($actionTypes as $value => $label): ?>
                                        <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="input-group input-group-sm" style="width: 200px; margin-left: 5px;">
                                <input type="text" id="filter-action-search" class="form-control form-control-sm" placeholder="Search actions...">
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap" id="disciplinary-table">
                            <thead>
                                <tr>
                                    <th>Action Reference</th>
                                    <th>Incident ID</th>
                                    <th>Employee</th>
                                    <th>Action Type</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                    <th>Issued By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer clearfix" id="disciplinary-pagination">
                        <!-- Pagination will be loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Footer -->
</div>

<?php include __DIR__ . '/includes/incident_modals.php'; ?>
<?php include __DIR__ . '/includes/disciplinary_modals.php'; ?>

<!-- Scripts -->
<script src="<?= $basePath ?>assets/plugins/jquery/jquery.min.js"></script>
<script src="<?= $basePath ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= $basePath ?>assets/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- DataTables -->
<script src="<?= $basePath ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= $basePath ?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= $basePath ?>assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= $basePath ?>assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?= $basePath ?>assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<script src="js/incident_reporting.js"></script>
<script>
// Search functionality for respondent checkboxes
$(document).ready(function() {
    bsCustomFileInput.init();
    
    $('#respondent-search').on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();
        $('.respondent-item').each(function() {
            var label = $(this).find('label').text().toLowerCase();
            if (label.indexOf(searchTerm) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
</script>
</body>
</html>
