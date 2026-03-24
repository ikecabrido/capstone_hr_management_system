<?php
session_start();
require_once "../auth/database.php";
require_once "controllers/IncidentController.php";
require_once "../auth/auth_check.php";

// Page configuration
$pageTitle = 'Incident & Disciplinary Management';

$db = Database::getInstance()->getConnection();
$controller = new IncidentController($db);

// Get current user info
$currentUserId = $_SESSION['user_id'] ?? 1;
$currentUserRole = $_SESSION['role'] ?? 'admin';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create_incident':
            $result = $controller->createIncident($_POST, $currentUserId);
            break;
        case 'update_status':
            $result = $controller->updateIncidentStatus($_POST['incident_id'], $_POST['new_status'], $currentUserId);
            break;
        case 'issue_nte':
            $result = $controller->issueNTE($_POST, $currentUserId);
            break;
        case 'submit_explanation':
            $result = $controller->submitExplanation($_POST, $currentUserId);
            break;
        case 'make_decision':
            $result = $controller->makeDecision($_POST, $currentUserId);
            break;
        case 'close_case':
            $result = $controller->closeCase($_POST['incident_id'], $_POST['closure_reason'], $currentUserId);
            break;
    }
    
    if (isset($result) && $result['success']) {
        $_SESSION['success_message'] = $result['message'];
        header("Location: incident.php");
        exit;
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'get_incidents':
            $incidents = $controller->getAllIncidents();
            echo json_encode($incidents);
            break;
            
        case 'get_incident_details':
            $incident = $controller->getIncidentById($_GET['id']);
            echo json_encode($incident);
            break;
            
        case 'get_workflow_history':
            $history = $controller->getWorkflowHistory($_GET['id']);
            echo json_encode($history);
            break;
            
        case 'get_employees':
            $employees = $controller->getEmployees();
            echo json_encode($employees);
            break;
            
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
    exit;
}

// Get data for display
try {
    $stats = $controller->getStats();
    $incidents = $controller->getAllIncidents();
    $pendingIncidents = $controller->getIncidentsByStatus('submitted');
    $underReviewIncidents = $controller->getIncidentsByStatus('under_review');
} catch (Exception $e) {
    $stats = [
        'total_incidents' => 0,
        'pending_review' => 0,
        'nte_issued' => 0,
        'under_evaluation' => 0,
        'decisions_made' => 0,
        'closed_cases' => 0
    ];
    $incidents = [];
    $pendingIncidents = [];
    $underReviewIncidents = [];
}

// Include Header Template
include "components/header_template.php";
?>

                        <!-- Statistics Cards -->
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-exclamation-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Pending Review</span>
                                        <span class="info-box-number"><?= $stats['pending_review'] ?? 0 ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-envelope-open-text"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">NTE Issued</span>
                                        <span class="info-box-number"><?= $stats['nte_issued'] ?? 0 ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-search"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Under Evaluation</span>
                                        <span class="info-box-number"><?= $stats['under_evaluation'] ?? 0 ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Closed Cases</span>
                                        <span class="info-box-number"><?= $stats['closed_cases'] ?? 0 ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs for different views -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header p-2">
                                        <ul class="nav nav-pills" id="incidentTabs">
                                            <li class="nav-item">
                                                <a class="nav-link active" href="#tab_all" data-toggle="tab" onclick="filterIncidents('all')">
                                                    All Incidents <span class="badge badge-warning"><?= count($incidents) ?></span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#tab_pending" data-toggle="tab" onclick="filterIncidents('submitted')">
                                                    Pending <span class="badge badge-warning"><?= count($pendingIncidents) ?></span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#tab_review" data-toggle="tab" onclick="filterIncidents('under_review')">
                                                    Under Review <span class="badge badge-info"><?= count($underReviewIncidents) ?></span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#tab_nte" data-toggle="tab" onclick="filterIncidents('nte_issued')">
                                                    NTE Issued
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#tab_decision" data-toggle="tab" onclick="filterIncidents('decision_made')">
                                                    Decisions Made
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#tab_closed" data-toggle="tab" onclick="filterIncidents('closed')">
                                                    Closed
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab_all">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped" id="incidentsTable">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Date</th>
                                                                <th>Employee Involved</th>
                                                                <th>Incident Type</th>
                                                                <th>Severity</th>
                                                                <th>Status</th>
                                                                <th>Current Step</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (!empty($incidents)): ?>
                                                                <?php foreach ($incidents as $incident): ?>
                                                                    <tr>
                                                                        <td><?= htmlspecialchars($incident['incident_id'] ?? 'INC-' . $incident['id']) ?></td>
                                                                        <td><?= date('M d, Y', strtotime($incident['incident_date'])) ?></td>
                                                                        <td><?= htmlspecialchars($incident['respondent_name'] ?? 'Not assigned') ?></td>
                                                                        <td><?= htmlspecialchars($incident['incident_type']) ?></td>
                                                                        <td>
                                                                            <span class="badge badge-<?= $incident['severity'] === 'critical' ? 'danger' : ($incident['severity'] === 'high' ? 'warning' : 'info') ?>">
                                                                                <?= ucfirst($incident['severity']) ?>
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            $statusColors = [
                                                                                'submitted' => 'warning',
                                                                                'under_review' => 'info',
                                                                                'nte_issued' => 'primary',
                                                                                'explanation_received' => 'secondary',
                                                                                'hr_evaluation' => 'info',
                                                                                'decision_made' => 'success',
                                                                                'closed' => 'dark'
                                                                            ];
                                                                            ?>
                                                                            <span class="badge badge-<?= $statusColors[$incident['current_workflow_step'] ?? 'submitted'] ?? 'warning' ?>">
                                                                                <?= ucfirst(str_replace('_', ' ', $incident['current_workflow_step'] ?? 'submitted')) ?>
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            $stepLabels = [
                                                                                'submitted' => '1. Submitted',
                                                                                'under_review' => '2. Under Review',
                                                                                'nte_issued' => '3. NTE Issued',
                                                                                'explanation_received' => '4. Explanation Received',
                                                                                'hr_evaluation' => '5. HR Evaluation',
                                                                                'decision_made' => '6. Decision Made',
                                                                                'closed' => '7. Closed'
                                                                            ];
                                                                            ?>
                                                                            <small class="text-muted"><?= $stepLabels[$incident['current_workflow_step'] ?? 'submitted'] ?? 'Step 1' ?></small>
                                                                        </td>
                                                                        <td>
                                                                            <button class="btn btn-sm btn-info" onclick="viewIncident(<?= $incident['id'] ?>)" title="View Details">
                                                                                <i class="fas fa-eye"></i>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            <?php else: ?>
                                                                <tr>
                                                                    <td colspan="8" class="text-center text-muted">No incidents found</td>
                                                                </tr>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ===================================================== -->
                <!-- MODALS -->
                <!-- ===================================================== -->

                <!-- Create Incident Modal -->
                <div class="modal fade" id="createIncidentModal" tabindex="-1" role="dialog" aria-labelledby="createIncidentModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="createIncidentModalLabel">Report New Incident</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <form method="POST" action="incident.php">
                                <div class="modal-body">
                                    <input type="hidden" name="action" value="create_incident">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Employee Involved <span class="text-danger">*</span></label>
                                                <select name="respondent_id" class="form-control select2" required>
                                                    <option value="">Select Employee</option>
                                                    <!-- Populated via AJAX -->
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Incident Type <span class="text-danger">*</span></label>
                                                <select name="incident_type" class="form-control" required>
                                                    <option value="">Select Type</option>
                                                    <option value="misconduct">Misconduct</option>
                                                    <option value="tardiness">Tardiness/Absence</option>
                                                    <option value="policy_violation">Policy Violation</option>
                                                    <option value="harassment">Harassment</option>
                                                    <option value="theft">Theft/Dishonesty</option>
                                                    <option value="safety_violation">Safety Violation</option>
                                                    <option value="discrimination">Discrimination</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Severity <span class="text-danger">*</span></label>
                                                <select name="severity" class="form-control" required>
                                                    <option value="low">Low</option>
                                                    <option value="medium" selected>Medium</option>
                                                    <option value="high">High</option>
                                                    <option value="critical">Critical</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Incident Date <span class="text-danger">*</span></label>
                                                <input type="date" name="incident_date" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Reporter Name <span class="text-danger">*</span></label>
                                                <input type="text" name="reporter_name" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Reporter Type</label>
                                                <select name="reported_by" class="form-control">
                                                    <option value="Employee">Employee</option>
                                                    <option value="Supervisor">Supervisor</option>
                                                    <option value="HR">HR</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Incident Location</label>
                                        <input type="text" name="location" class="form-control" placeholder="Where did the incident occur?">
                                    </div>

                                    <div class="form-group">
                                        <label>Description <span class="text-danger">*</span></label>
                                        <textarea name="description" class="form-control" rows="4" required placeholder="Describe the incident in detail..."></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Witnesses (if any)</label>
                                        <textarea name="witnesses" class="form-control" rows="2" placeholder="List witness names and contact info"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Attachment (Optional)</label>
                                        <input type="file" name="attachment" class="form-control-file" accept=".pdf,.doc,.docx,.jpg,.png">
                                        <small class="text-muted">Upload supporting documents (PDF, DOC, images)</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Submit Incident</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Incident Details Modal -->
                <div class="modal fade" id="incidentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="incidentDetailsModalLabel" aria-hidden="true" style="z-index: 1050;">
                    <div class="modal-dialog modal-xl" style="z-index: 1051;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="incidentDetailsModalLabel">Incident Details</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body" id="incidentDetailsContent">
                                <!-- Content loaded via AJAX -->
                                <div class="text-center">
                                    <i class="fas fa-spinner fa-spin fa-2x"></i> Loading...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Global Modal -->
                <?php include "../layout/global_modal.php"; ?>

                <!-- ===================================================== -->
                <!-- JAVASCRIPT -->
                <!-- ===================================================== -->
                <script>
                // Global variables
                let currentFilter = 'all';
                let incidentsData = <?= json_encode($incidents) ?>;

                // Filter incidents by status
                function filterIncidents(status) {
                    currentFilter = status;
                    loadIncidents(status);
                }

                // Load incidents via AJAX
                function loadIncidents(status) {
                    const filtered = status === 'all' 
                        ? incidentsData 
                        : incidentsData.filter(i => (i.current_workflow_step || 'submitted') === status);
                    
                    updateIncidentsTable(filtered);
                }

                // Update the incidents table
                function updateIncidentsTable(incidents) {
                    const tbody = document.querySelector('#incidentsTable tbody');
                    if (!tbody) return;

                    if (incidents.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No incidents found</td></tr>';
                        return;
                    }

                    tbody.innerHTML = incidents.map(incident => `
                        <tr>
                            <td>${incident.incident_id || 'INC-' + incident.id}</td>
                            <td>${incident.incident_date ? new Date(incident.incident_date).toLocaleDateString() : ''}</td>
                            <td>${incident.respondent_name || 'Not assigned'}</td>
                            <td>${incident.incident_type || ''}</td>
                            <td><span class="badge badge-${incident.severity === 'critical' ? 'danger' : (incident.severity === 'high' ? 'warning' : 'info')}">${incident.severity || 'medium'}</span></td>
                            <td><span class="badge badge-${getStatusBadgeColor(incident.current_workflow_step || 'submitted')}">${formatStatus(incident.current_workflow_step || 'submitted')}</span></td>
                            <td><small class="text-muted">${getStepLabel(incident.current_workflow_step || 'submitted')}</small></td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewIncident(${incident.id})" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('');
                }

                // Get status badge color
                function getStatusBadgeColor(status) {
                    const colors = {
                        'submitted': 'warning',
                        'under_review': 'info',
                        'nte_issued': 'primary',
                        'explanation_received': 'secondary',
                        'hr_evaluation': 'info',
                        'decision_made': 'success',
                        'closed': 'dark'
                    };
                    return colors[status] || 'warning';
                }

                // Format status for display
                function formatStatus(status) {
                    return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                }

                // Get step label
                function getStepLabel(step) {
                    const labels = {
                        'submitted': '1. Submitted',
                        'under_review': '2. Under Review',
                        'nte_issued': '3. NTE Issued',
                        'explanation_received': '4. Explanation Received',
                        'hr_evaluation': '5. HR Evaluation',
                        'decision_made': '6. Decision Made',
                        'closed': '7. Closed'
                    };
                    return labels[step] || 'Step 1';
                }

                // View incident details
                function viewIncident(id) {
                    const modal = $('#incidentDetailsModal');
                    const content = $('#incidentDetailsContent');
                    
                    content.html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-2x"></i> Loading...</div>');
                    modal.modal('show');
                    
                    fetch(`incident.php?action=get_incident_details&id=${id}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && !data.error) {
                                content.html(renderIncidentDetails(data));
                            } else {
                                content.html('<div class="alert alert-warning">Incident not found or has been removed.</div>');
                            }
                        })
                        .catch(error => {
                            console.error('Error loading incident:', error);
                            content.html('<div class="alert alert-danger">Error loading incident details. Please try again.</div>');
                        });
                }

                // Render incident details
                function renderIncidentDetails(incident) {
                    return `
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Incident Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr><td><strong>Incident ID:</strong></td><td>${incident.incident_id || 'INC-' + incident.id}</td></tr>
                                            <tr><td><strong>Employee Involved:</strong></td><td>${incident.respondent_name || 'Not assigned'}</td></tr>
                                            <tr><td><strong>Incident Type:</strong></td><td>${incident.incident_type || ''}</td></tr>
                                            <tr><td><strong>Severity:</strong></td><td><span class="badge badge-${incident.severity === 'critical' ? 'danger' : (incident.severity === 'high' ? 'warning' : 'info')}">${incident.severity || 'medium'}</span></td></tr>
                                            <tr><td><strong>Incident Date:</strong></td><td>${incident.incident_date ? new Date(incident.incident_date).toLocaleDateString() : ''}</td></tr>
                                            <tr><td><strong>Description:</strong></td><td>${incident.description || ''}</td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Workflow Status</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Current Step:</strong> ${formatStatus(incident.current_workflow_step || 'submitted')}</p>
                                        <p><strong>Created:</strong> ${incident.created_at ? new Date(incident.created_at).toLocaleString() : ''}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }

                // Send reminder
                function sendReminder(incidentId) {
                    if (confirm('Send reminder to employee about NTE response?')) {
                        alert('Reminder sent successfully!');
                    }
                }

                // Initialize on document ready
                $(document).ready(function() {
                    $('.select2').select2();
                    $('input[name="incident_date"]').val(new Date().toISOString().split('T')[0]);
                });
                </script>

<?php 
// Include Footer Template
include "components/footer_template.php";

