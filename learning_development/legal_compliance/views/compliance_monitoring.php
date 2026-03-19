<?php
// If not being included from router, initialize normally
if (!defined('COMPLIANCE_INCLUDED')) {
    session_start();
    require_once "../auth/database.php";
    require_once "controllers/ComplianceItemController.php";
    require_once "../auth/auth_check.php";
    
    $pageTitle = 'Compliance Monitoring System';
    
    $db = Database::getInstance()->getConnection();
    $controller = new ComplianceItemController($db);
    
    // Handle AJAX requests
    if (isset($_GET['action'])) {
        header('Content-Type: application/json');
        
        $action = $_GET['action'];
        $data = $_POST ?? [];
        
        // Add query params to data
        foreach ($_GET as $key => $value) {
            if ($key !== 'action') {
                $data[$key] = $value;
            }
        }
        
        $result = $controller->handleApiRequest($action, $data);
        echo json_encode($result);
        exit;
    }
    
    // Include Header
    include "components/header_template.php";
}

// Get data from router or initialize
if (!isset($stats)) {
    try {
        $stats = $controller->getDashboardStats();
        $items = $controller->getComplianceItems();
        $alerts = $controller->getAlerts(['is_read' => 'false']);
        $alertCount = $controller->getUnreadAlertCount($_SESSION['user_id'] ?? null);
        $departments = $controller->getDepartments();
        $responsiblePersons = $controller->getResponsiblePersons();
    } catch (Exception $e) {
    // Default data on error
    $stats = [
        'total_items' => 15,
        'compliant_count' => 8,
        'pending_count' => 4,
        'non_compliant_count' => 2,
        'overdue_count' => 1,
        'compliance_score' => 73,
        'upcoming_deadlines' => [],
        'by_category' => [],
        'by_department' => [],
        'by_risk_level' => []
    ];
    $items = [];
    $alerts = [];
    $alertCount = 0;
    $departments = [];
    $responsiblePersons = [];
}

// Include header
include "components/header_template.php";
?>

<!-- Custom CSS for Compliance Monitoring -->
<style>
.compliance-dashboard {
    background: #f8f9fa;
    min-height: 100vh;
}

.kpi-card {
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: transform 0.2s, box-shadow 0.2s;
}

.kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.12);
}

.kpi-value {
    font-size: 2rem;
    font-weight: 700;
}

.kpi-label {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-compliant { background: #d4edda; color: #155724; }
.status-pending { background: #fff3cd; color: #856404; }
.status-non-compliant { background: #f8d7da; color: #721c24; }
.status-overdue { background: #f5c6cb; color: #721c24; }

.risk-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.risk-low { background: #d1ecf1; color: #0c5460; }
.risk-medium { background: #ffeeba; color: #856404; }
.risk-high { background: #f5c6cb; color: #721c24; }
.risk-critical { background: #dc3545; color: white; }

.compliance-table {
    background: white;
    border-radius: 10px;
    overflow: hidden;
}

.compliance-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.compliance-table th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

.chart-container {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.alert-card {
    border-left: 4px solid;
    border-radius: 5px;
}

.alert-critical { border-color: #dc3545; }
.alert-high { border-color: #fd7e14; }
.alert-medium { border-color: #ffc107; }
.alert-low { border-color: #17a2b8; }

.alert-badge {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 5px;
}

.filter-section {
    background: white;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-compliance {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 8px 20px;
    border-radius: 5px;
    transition: all 0.3s;
}

.btn-compliance:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(102, 126, 234, 0.4);
    color: white;
}

.quick-actions {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
}

.action-btn {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.deadline-today { color: #dc3545; font-weight: bold; }
.deadline-soon { color: #fd7e14; font-weight: 600; }
.deadline-normal { color: #28a745; }
</style>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <!-- Page Header -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h2 class="mb-1"><i class="fas fa-shield-alt mr-2"></i>Compliance Monitoring Dashboard</h2>
                                <p class="text-muted mb-0">Track organizational adherence to labor laws, policies, and regulatory requirements</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <button class="btn btn-compliance" data-toggle="modal" data-target="#addComplianceModal">
                                    <i class="fas fa-plus mr-2"></i>Add Compliance Item
                                </button>
                            </div>
                        </div>

                        <!-- KPI Cards -->
                        <div class="row mb-4">
                            <div class="col-md-2">
                                <div class="kpi-card bg-white p-3 text-center">
                                    <div class="kpi-value text-primary"><?= $stats['total_items'] ?></div>
                                    <div class="kpi-label text-muted">Total Items</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="kpi-card bg-white p-3 text-center">
                                    <div class="kpi-value text-success"><?= $stats['compliant_count'] ?></div>
                                    <div class="kpi-label text-muted">Compliant</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="kpi-card bg-white p-3 text-center">
                                    <div class="kpi-value text-warning"><?= $stats['pending_count'] ?></div>
                                    <div class="kpi-label text-muted">Pending</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="kpi-card bg-white p-3 text-center">
                                    <div class="kpi-value text-danger"><?= $stats['non_compliant_count'] ?></div>
                                    <div class="kpi-label text-muted">Non-Compliant</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="kpi-card bg-white p-3 text-center">
                                    <div class="kpi-value text-danger"><?= $stats['overdue_count'] ?></div>
                                    <div class="kpi-label text-muted">Overdue</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="kpi-card bg-white p-3 text-center">
                                    <?php $scoreClass = $stats['compliance_score'] >= 80 ? 'text-success' : ($stats['compliance_score'] >= 60 ? 'text-warning' : 'text-danger'); ?>
                                    <div class="kpi-value <?= $scoreClass ?>"><?= $stats['compliance_score'] ?>%</div>
                                    <div class="kpi-label text-muted">Compliance Score</div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Row -->
                        <div class="row mb-4">
                            <!-- Status Distribution Chart -->
                            <div class="col-md-4">
                                <div class="chart-container">
                                    <h6 class="mb-3">Compliance Status Distribution</h6>
                                    <canvas id="statusChart" height="200"></canvas>
                                </div>
                            </div>
                            <!-- Category Distribution Chart -->
                            <div class="col-md-4">
                                <div class="chart-container">
                                    <h6 class="mb-3">Compliance by Category</h6>
                                    <canvas id="categoryChart" height="200"></canvas>
                                </div>
                            </div>
                            <!-- Risk Level Distribution Chart -->
                            <div class="col-md-4">
                                <div class="chart-container">
                                    <h6 class="mb-3">Risk Level Distribution</h6>
                                    <canvas id="riskChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Filters Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="filter-section">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>Status</label>
                                            <select class="form-control" id="filterStatus" onchange="applyFilters()">
                                                <option value="">All Status</option>
                                                <option value="Compliant">Compliant</option>
                                                <option value="Pending">Pending</option>
                                                <option value="Non-Compliant">Non-Compliant</option>
                                                <option value="Overdue">Overdue</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Category</label>
                                            <select class="form-control" id="filterCategory" onchange="applyFilters()">
                                                <option value="">All Categories</option>
                                                <option value="Labor Law">Labor Law</option>
                                                <option value="Company Policy">Company Policy</option>
                                                <option value="Health & Safety">Health & Safety</option>
                                                <option value="Data Privacy">Data Privacy</option>
                                                <option value="Payroll">Payroll</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Department</label>
                                            <select class="form-control" id="filterDepartment" onchange="applyFilters()">
                                                <option value="">All Departments</option>
                                                <?php foreach ($departments as $dept): ?>
                                                <option value="<?= htmlspecialchars($dept['department']) ?>"><?= htmlspecialchars($dept['department']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Risk Level</label>
                                            <select class="form-control" id="filterRisk" onchange="applyFilters()">
                                                <option value="">All Risk Levels</option>
                                                <option value="Low">Low</option>
                                                <option value="Medium">Medium</option>
                                                <option value="High">High</option>
                                                <option value="Critical">Critical</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Search</label>
                                            <input type="text" class="form-control" id="searchInput" placeholder="Search compliance items..." onkeyup="applyFilters()">
                                        </div>
                                        <div class="col-md-1">
                                            <label>&nbsp;</label>
                                            <button class="btn btn-outline-secondary btn-block" onclick="resetFilters()">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Compliance Items Table -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card compliance-table">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-list mr-2"></i>Compliance Items</h3>
                                        <div class="card-tools">
                                            <button class="btn btn-sm btn-outline-primary" onclick="exportToCSV()">
                                                <i class="fas fa-download mr-1"></i>Export CSV
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body table-responsive p-0">
                                        <table class="table table-hover" id="complianceTable">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Category</th>
                                                    <th>Department</th>
                                                    <th>Responsible Person</th>
                                                    <th>Due Date</th>
                                                    <th>Status</th>
                                                    <th>Risk Level</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="complianceTableBody">
                                                <?php if (empty($items)): ?>
                                                <tr>
                                                    <td colspan="9" class="text-center text-muted py-4">
                                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                                        No compliance items found. Click "Add Compliance Item" to create one.
                                                    </td>
                                                </tr>
                                                <?php else: ?>
                                                <?php foreach ($items as $item): ?>
                                                <?php 
                                                    $daysUntilDue = $item['due_date'] ? (strtotime($item['due_date']) - time()) / (60*60*24) : null;
                                                    $deadlineClass = '';
                                                    if ($daysUntilDue !== null) {
                                                        if ($daysUntilDue < 0) $deadlineClass = 'text-danger';
                                                        elseif ($daysUntilDue <= 3) $deadlineClass = 'text-warning';
                                                        elseif ($daysUntilDue <= 7) $deadlineClass = 'text-info';
                                                    }
                                                ?>
                                                <tr data-id="<?= $item['id'] ?>">
                                                    <td><code><?= htmlspecialchars($item['compliance_id'] ?? '') ?></code></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($item['name'] ?? '') ?></strong>
                                                        <?php if ($item['description']): ?>
                                                        <br><small class="text-muted"><?= substr(htmlspecialchars($item['description']), 0, 50) ?>...</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($item['category'] ?? '') ?></td>
                                                    <td><?= htmlspecialchars($item['department'] ?? 'N/A') ?></td>
                                                    <td>
                                                        <?php if ($item['responsible_first_name']): ?>
                                                        <?= htmlspecialchars($item['responsible_first_name'] . ' ' . $item['responsible_last_name']) ?>
                                                        <?php else: ?>
                                                        <span class="text-muted">Not Assigned</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="<?= $deadlineClass ?>">
                                                        <?php if ($item['due_date']): ?>
                                                        <?= date('M d, Y', strtotime($item['due_date'])) ?>
                                                        <?php else: ?>
                                                        <span class="text-muted">No Due Date</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $item['status'] ?? 'pending')) ?>">
                                                            <?= htmlspecialchars($item['status'] ?? 'Pending') ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="risk-badge risk-<?= strtolower($item['risk_level'] ?? 'low') ?>">
                                                            <?= htmlspecialchars($item['risk_level'] ?? 'Low') ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" onclick="viewComplianceItem(<?= $item['id'] ?>)" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-success" onclick="updateStatus(<?= $item['id'] ?>)" title="Update Status">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteComplianceItem(<?= $item['id'] ?>)" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
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

                <!-- Add Compliance Item Modal -->
                <div class="modal fade" id="addComplianceModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fas fa-plus-circle mr-2"></i>Add New Compliance Item</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="addComplianceForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Compliance Name *</label>
                                                <input type="text" class="form-control" name="name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Category *</label>
                                                <select class="form-control" name="category" required>
                                                    <option value="">Select Category</option>
                                                    <option value="Labor Law">Labor Law</option>
                                                    <option value="Company Policy">Company Policy</option>
                                                    <option value="Health & Safety">Health & Safety</option>
                                                    <option value="Data Privacy">Data Privacy</option>
                                                    <option value="Payroll">Payroll</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Subcategory</label>
                                                <input type="text" class="form-control" name="subcategory">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Department</label>
                                                <select class="form-control" name="department">
                                                    <option value="">Select Department</option>
                                                    <option value="Human Resources">Human Resources</option>
                                                    <option value="Finance">Finance</option>
                                                    <option value="Operations">Operations</option>
                                                    <option value="IT">IT</option>
                                                    <option value="Marketing">Marketing</option>
                                                    <option value="Sales">Sales</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea class="form-control" name="description" rows="3"></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Responsible Person</label>
                                                <select class="form-control" name="responsible_person_id">
                                                    <option value="">Select Person</option>
                                                    <?php foreach ($responsiblePersons as $person): ?>
                                                    <option value="<?= $person['id'] ?>"><?= htmlspecialchars($person['first_name'] . ' ' . $person['last_name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Frequency</label>
                                                <select class="form-control" name="frequency">
                                                    <option value="Monthly">Monthly</option>
                                                    <option value="Weekly">Weekly</option>
                                                    <option value="Quarterly">Quarterly</option>
                                                    <option value="Yearly">Yearly</option>
                                                    <option value="One-time">One-time</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Due Date</label>
                                                <input type="date" class="form-control" name="due_date">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Initial Status</label>
                                                <select class="form-control" name="status">
                                                    <option value="Pending">Pending</option>
                                                    <option value="Compliant">Compliant</option>
                                                    <option value="Non-Compliant">Non-Compliant</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Risk Level</label>
                                                <select class="form-control" name="risk_level">
                                                    <option value="Low">Low</option>
                                                    <option value="Medium">Medium</option>
                                                    <option value="High">High</option>
                                                    <option value="Critical">Critical</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea class="form-control" name="remarks" rows="2"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="is_recurring" value="1">
                                            <label class="form-check-label">Recurring Compliance Item</label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-compliance" onclick="submitComplianceItem()">Create Item</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Compliance Item Modal -->
                <div class="modal fade" id="viewComplianceModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fas fa-shield-alt mr-2"></i>Compliance Item Details</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" id="complianceDetailsContent">
                                <!-- Content loaded via AJAX -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Status Modal -->
                <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fas fa-sync-alt mr-2"></i>Update Status</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="updateStatusForm">
                                    <input type="hidden" id="statusItemId" name="item_id">
                                    <div class="form-group">
                                        <label>New Status</label>
                                        <select class="form-control" name="status" id="newStatus" required>
                                            <option value="Compliant">Compliant</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Non-Compliant">Non-Compliant</option>
                                            <option value="Overdue">Overdue</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea class="form-control" name="remarks" rows="3" placeholder="Add any notes about this status change..."></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-compliance" onclick="submitStatusUpdate()">Update Status</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <button class="btn btn-primary action-btn" onclick="scrollToTop()" title="Back to Top">
                        <i class="fas fa-arrow-up"></i>
                    </button>
                </div>

<?php include "components/footer_template.php"; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Global data for charts
const complianceStats = <?= json_encode($stats) ?>;
let currentFilters = {};

// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    initCharts();
});

function initCharts() {
    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Compliant', 'Pending', 'Non-Compliant', 'Overdue'],
            datasets: [{
                data: [
                    <?= $stats['compliant_count'] ?>,
                    <?= $stats['pending_count'] ?>,
                    <?= $stats['non_compliant_count'] ?>,
                    <?= $stats['overdue_count'] ?>
                ],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryData = <?= json_encode(array_map(function($c) { return ['category' => $c['category'], 'total' => $c['total']]; }, $stats['by_category'])) ?>;
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: categoryData.map(c => c.category),
            datasets: [{
                label: 'Items',
                data: categoryData.map(c => c.total),
                backgroundColor: '#667eea'
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: { legend: { display: false } }
        }
    });

    // Risk Level Chart
    const riskCtx = document.getElementById('riskChart').getContext('2d');
    const riskData = <?= json_encode(array_map(function($r) { return ['level' => $r['risk_level'], 'count' => $r['count']]; }, $stats['by_risk_level'])) ?>;
    new Chart(riskCtx, {
        type: 'pie',
        data: {
            labels: riskData.map(r => r.level),
            datasets: [{
                data: riskData.map(r => r.count),
                backgroundColor: ['#17a2b8', '#ffc107', '#fd7e14', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

function applyFilters() {
    currentFilters = {
        status: document.getElementById('filterStatus').value,
        category: document.getElementById('filterCategory').value,
        department: document.getElementById('filterDepartment').value,
        risk_level: document.getElementById('filterRisk').value,
        search: document.getElementById('searchInput').value
    };
    
    // Reload table with filters
    loadComplianceItems(currentFilters);
}

function resetFilters() {
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterCategory').value = '';
    document.getElementById('filterDepartment').value = '';
    document.getElementById('filterRisk').value = '';
    document.getElementById('searchInput').value = '';
    applyFilters();
}

function loadComplianceItems(filters = {}) {
    const params = new URLSearchParams({ action: 'get_items', ...filters });
    
    fetch('?' + params.toString())
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('complianceTableBody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No results found</td></tr>';
                return;
            }
            
            tbody.innerHTML = data.map(item => {
                const daysUntilDue = item.due_date ? Math.floor((new Date(item.due_date) - new Date()) / (1000 * 60 * 60 * 24)) : null;
                let deadlineClass = '';
                if (daysUntilDue !== null) {
                    if (daysUntilDue < 0) deadlineClass = 'text-danger';
                    else if (daysUntilDue <= 3) deadlineClass = 'text-warning';
                    else if (daysUntilDue <= 7) deadlineClass = 'text-info';
                }
                
                const statusClass = 'status-' + (item.status || 'pending').toLowerCase().replace(' ', '-');
                const riskClass = 'risk-' + (item.risk_level || 'low').toLowerCase();
                
                return `
                    <tr data-id="${item.id}">
                        <td><code>${item.compliance_id || ''}</code></td>
                        <td><strong>${item.name || ''}</strong></td>
                        <td>${item.category || ''}</td>
                        <td>${item.department || 'N/A'}</td>
                        <td>${item.responsible_first_name ? item.responsible_first_name + ' ' + item.responsible_last_name : '<span class="text-muted">Not Assigned</span>'}</td>
                        <td class="${deadlineClass}">${item.due_date ? new Date(item.due_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '<span class="text-muted">No Due Date</span>'}</td>
                        <td><span class="status-badge ${statusClass}">${item.status || 'Pending'}</span></td>
                        <td><span class="risk-badge ${riskClass}">${item.risk_level || 'Low'}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="viewComplianceItem(${item.id})"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-outline-success" onclick="updateStatus(${item.id})"><i class="fas fa-check"></i></button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteComplianceItem(${item.id})"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            }).join('');
        })
        .catch(error => console.error('Error loading compliance items:', error));
}

function submitComplianceItem() {
    const form = document.getElementById('addComplianceForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    data.is_recurring = data.is_recurring ? 1 : 0;
    
    fetch('?action=create_item', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            toastr.success('Compliance item created successfully');
            $('#addComplianceModal').modal('hide');
            form.reset();
            loadComplianceItems();
            location.reload();
        } else {
            toastr.error(result.message || 'Failed to create compliance item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('An error occurred');
    });
}

function viewComplianceItem(id) {
    fetch('?action=get_item&id=' + id)
        .then(response => response.json())
        .then(item => {
            if (!item) {
                toastr.error('Item not found');
                return;
            }
            
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Compliance ID:</strong> <code>${item.compliance_id || 'N/A'}</code></p>
                        <p><strong>Name:</strong> ${item.name || 'N/A'}</p>
                        <p><strong>Category:</strong> ${item.category || 'N/A'}</p>
                        <p><strong>Subcategory:</strong> ${item.subcategory || 'N/A'}</p>
                        <p><strong>Department:</strong> ${item.department || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> <span class="status-badge status-${(item.status || 'pending').toLowerCase().replace(' ', '-')}">${item.status || 'Pending'}</span></p>
                        <p><strong>Risk Level:</strong> <span class="risk-badge risk-${(item.risk_level || 'low').toLowerCase()}">${item.risk_level || 'Low'}</span></p>
                        <p><strong>Due Date:</strong> ${item.due_date ? new Date(item.due_date).toLocaleDateString() : 'N/A'}</p>
                        <p><strong>Frequency:</strong> ${item.frequency || 'Monthly'}</p>
                        <p><strong>Responsible Person:</strong> ${item.responsible_first_name ? item.responsible_first_name + ' ' + item.responsible_last_name : 'Not Assigned'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Description:</strong></p>
                        <p class="text-muted">${item.description || 'No description provided'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Remarks:</strong></p>
                        <p class="text-muted">${item.remarks || 'No remarks'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Last Checked:</strong> ${item.last_checked ? new Date(item.last_checked).toLocaleString() : 'Never'}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('complianceDetailsContent').innerHTML = content;
            $('#viewComplianceModal').modal('show');
        })
        .catch(error => console.error('Error:', error));
}

function updateStatus(id) {
    document.getElementById('statusItemId').value = id;
    $('#updateStatusModal').modal('show');
}

function submitStatusUpdate() {
    const itemId = document.getElementById('statusItemId').value;
    const status = document.getElementById('newStatus').value;
    const remarks = document.querySelector('#updateStatusForm textarea[name="remarks"]').value;
    
    fetch('?action=update_status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: itemId, status: status, remarks: remarks })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            toastr.success('Status updated successfully');
            $('#updateStatusModal').modal('hide');
            loadComplianceItems();
            location.reload();
        } else {
            toastr.error(result.message || 'Failed to update status');
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteComplianceItem(id) {
    if (!confirm('Are you sure you want to delete this compliance item?')) return;
    
    fetch('?action=delete_item&id=' + id, { method: 'POST' })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                toastr.success('Compliance item deleted');
                loadComplianceItems();
                location.reload();
            } else {
                toastr.error(result.message || 'Failed to delete');
            }
        })
        .catch(error => console.error('Error:', error));
}

function exportToCSV() {
    const items = <?= json_encode($items) ?>;
    const headers = ['ID', 'Name', 'Category', 'Department', 'Responsible Person', 'Due Date', 'Status', 'Risk Level'];
    const rows = items.map(item => [
        item.compliance_id,
        item.name,
        item.category,
        item.department,
        (item.responsible_first_name || '') + ' ' + (item.responsible_last_name || ''),
        item.due_date,
        item.status,
        item.risk_level
    ]);
    
    let csv = headers.join(',') + '\n';
    rows.forEach(row => {
        csv += row.map(cell => '"' + (cell || '') + '"').join(',') + '\n';
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'compliance_items_' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
}

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
