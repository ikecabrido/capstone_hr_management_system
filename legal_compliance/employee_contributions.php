<?php
/**
 * Employee Contributions Checklist - HR Validation Module
 * 
 * This module provides HR with tools to track and validate employee
 * government contributions including SSS, PAGIBIG, and PhilHealth.
 * 
 * Contributions Tracked:
 * - SSS (Social Security System)
 * - PAGIBIG (Home Development Mutual Fund)
 * - PhilHealth (Philippine Health Insurance Corporation)
 * 
 * @author HR Development Team
 * @version 1.0
 */

session_start();
require_once "../auth/database.php";
require_once "../auth/auth_check.php";

// Page configuration
$pageTitle = 'Employee Contributions Checklist';
$currentPage = 'Employee Contributions';

// Base path for assets
$basePath = '../';

// Get database connection
$db = Database::getInstance()->getConnection();

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'update_contribution') {
        $employeeId = (int)$_POST['employee_id'];
        $contributionType = $_POST['contribution_type'];
        $status = $_POST['status'];
        $contributionNumber = $_POST['contribution_number'] ?? '';
        
        try {
            // Check if record exists
            $stmt = $db->prepare("SELECT id FROM employees_contributions WHERE employee_id = ? AND contribution_type = ?");
            $stmt->execute([$employeeId, $contributionType]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                // Update existing record
                $stmt = $db->prepare("UPDATE employees_contributions SET status = ?, contribution_number = ?, updated_at = NOW() WHERE employee_id = ? AND contribution_type = ?");
                $stmt->execute([$status, $contributionNumber, $employeeId, $contributionType]);
            } else {
                // Insert new record
                $stmt = $db->prepare("INSERT INTO employees_contributions (employee_id, contribution_type, status, contribution_number, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
                $stmt->execute([$employeeId, $contributionType, $status, $contributionNumber]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Contribution status updated successfully']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    if ($_POST['action'] === 'bulk_update') {
        $employeeIds = json_decode($_POST['employee_ids'], true);
        $contributionType = $_POST['contribution_type'];
        $status = $_POST['status'];
        
        try {
            $db->beginTransaction();
            
            foreach ($employeeIds as $employeeId) {
                $employeeId = (int)$employeeId;
                
                // Check if record exists
                $stmt = $db->prepare("SELECT id FROM employees_contributions WHERE employee_id = ? AND contribution_type = ?");
                $stmt->execute([$employeeId, $contributionType]);
                $existing = $stmt->fetch();
                
                if ($existing) {
                    $stmt = $db->prepare("UPDATE employees_contributions SET status = ?, updated_at = NOW() WHERE employee_id = ? AND contribution_type = ?");
                    $stmt->execute([$status, $employeeId, $contributionType]);
                } else {
                    $stmt = $db->prepare("INSERT INTO employees_contributions (employee_id, contribution_type, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
                    $stmt->execute([$employeeId, $contributionType, $status]);
                }
            }
            
            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Bulk update completed successfully']);
        } catch (PDOException $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }
}

// Initialize database tables
try {
    $db->exec("CREATE TABLE IF NOT EXISTS employees_contributions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        contribution_type ENUM('sss', 'pagibig', 'philhealth') NOT NULL,
        status ENUM('submitted', 'pending') DEFAULT 'pending',
        contribution_number VARCHAR(50) DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_employee_contribution (employee_id, contribution_type),
        INDEX idx_employee_id (employee_id),
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
} catch (PDOException $e) {
    // Table might already exist
}

// Try to add contribution_number column if it doesn't exist
try {
    $db->exec("ALTER TABLE employees_contributions ADD COLUMN contribution_number VARCHAR(50) DEFAULT NULL");
} catch (PDOException $e) {
    // Column might already exist
}

// Try to modify status column to remove not_submitted
try {
    $db->exec("ALTER TABLE employees_contributions MODIFY COLUMN status ENUM('submitted', 'pending') DEFAULT 'pending'");
} catch (PDOException $e) {
    // Column might already be correct
}

// Get filter parameters
$statusFilter = $_GET['status'] ?? 'all';
$contributionFilter = $_GET['contribution'] ?? 'all';
$searchQuery = $_GET['search'] ?? '';

// Build query
$query = "SELECT 
    MIN(e.employee_id) as employee_id,
    e.full_name as employee_name,
    COALESCE(e.department, 'N/A') as department,
    COALESCE(e.position, 'N/A') as position,
    MAX(ec_sss.status) as sss_status,
    MAX(ec_sss.contribution_number) as sss_number,
    MAX(ec_pagibig.status) as pagibig_status,
    MAX(ec_pagibig.contribution_number) as pagibig_number,
    MAX(ec_philhealth.status) as philhealth_status,
    MAX(ec_philhealth.contribution_number) as philhealth_number
FROM employees e
LEFT JOIN lc_positions p ON e.position = p.title
LEFT JOIN employees_contributions ec_sss ON e.employee_id = ec_sss.employee_id AND ec_sss.contribution_type = 'sss'
LEFT JOIN employees_contributions ec_pagibig ON e.employee_id = ec_pagibig.employee_id AND ec_pagibig.contribution_type = 'pagibig'
LEFT JOIN employees_contributions ec_philhealth ON e.employee_id = ec_philhealth.employee_id AND ec_philhealth.contribution_type = 'philhealth'
WHERE e.employment_status = 'active'";

$params = [];

if ($searchQuery) {
    $query .= " AND (e.full_name LIKE ? OR e.department LIKE ?)";
    $searchParam = "%{$searchQuery}%";
    $params[] = $searchParam;
    $params[] = $searchParam;
}

$query .= " GROUP BY e.full_name, e.department, e.position";
$query .= " ORDER BY e.full_name";

try {
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $employees = [];
    $error = "Error fetching employees: " . $e->getMessage();
}

// Calculate statistics
$stats = [
    'total_employees' => count($employees),
    'sss_submitted' => 0,
    'sss_pending' => 0,
    'pagibig_submitted' => 0,
    'pagibig_pending' => 0,
    'philhealth_submitted' => 0,
    'philhealth_pending' => 0,
];

foreach ($employees as $emp) {
    // SSS
    if ($emp['sss_status'] === 'submitted') $stats['sss_submitted']++;
    else $stats['sss_pending']++;
    
    // PAGIBIG
    if ($emp['pagibig_status'] === 'submitted') $stats['pagibig_submitted']++;
    else $stats['pagibig_pending']++;
    
    // PhilHealth
    if ($emp['philhealth_status'] === 'submitted') $stats['philhealth_submitted']++;
    else $stats['philhealth_pending']++;
}

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
    
    <!-- Contributions CSS -->
    <link rel="stylesheet" href="css/employee_contributions.css">
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
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="legal_compliance.php">Dashboard</a></li>
                            <li class="breadcrumb-item active"><?= $pageTitle ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Statistics Cards -->
                <div class="row">
                    <!-- SSS Card -->
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= $stats['sss_submitted'] ?>/<?= $stats['total_employees'] ?></h3>
                                <p>SSS Submitted</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <a href="?contribution=sss" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    
                    <!-- PAGIBIG Card -->
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= $stats['pagibig_submitted'] ?>/<?= $stats['total_employees'] ?></h3>
                                <p>PAGIBIG Submitted</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <a href="?contribution=pagibig" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    
                    <!-- PhilHealth Card -->
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= $stats['philhealth_submitted'] ?>/<?= $stats['total_employees'] ?></h3>
                                <p>PhilHealth Submitted</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                            <a href="?contribution=philhealth" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-filter"></i> Filters</h3>
                            </div>
                            <div class="card-body">
                                <form method="GET" class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Search Employee</label>
                                            <input type="text" name="search" class="form-control" placeholder="Name or department..." value="<?= htmlspecialchars($searchQuery) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Contribution Type</label>
                                            <select name="contribution" class="form-control">
                                                <option value="all" <?= $contributionFilter === 'all' ? 'selected' : '' ?>>All Contributions</option>
                                                <option value="sss" <?= $contributionFilter === 'sss' ? 'selected' : '' ?>>SSS</option>
                                                <option value="pagibig" <?= $contributionFilter === 'pagibig' ? 'selected' : '' ?>>PAGIBIG</option>
                                                <option value="philhealth" <?= $contributionFilter === 'philhealth' ? 'selected' : '' ?>>PhilHealth</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="status" class="form-control">
                                                <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>All Status</option>
                                                <option value="submitted" <?= $statusFilter === 'submitted' ? 'selected' : '' ?>>Submitted</option>
                                                <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                                                <a href="employee_contributions.php" class="btn btn-default"><i class="fas fa-times"></i> Clear</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Contributions Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-users"></i> Employee Contributions Checklist</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#bulkUpdateModal">
                                        <i class="fas fa-edit"></i> Bulk Update
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (isset($error)): ?>
                                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                                <?php endif; ?>
                                
                                <div class="table-responsive">
                                    <table id="contributionsTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAll"></th>
                                                <th>Employee</th>
                                                <th>Department</th>
                                                <th>Position</th>
                                                <th>SSS</th>
                                                <th>PAGIBIG</th>
                                                <th>PhilHealth</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($employees as $emp): ?>
                                                <?php
                                                // Apply filters
                                                if ($statusFilter !== 'all') {
                                                    $hasStatus = false;
                                                    if ($contributionFilter === 'all' || $contributionFilter === 'sss') {
                                                        if (($emp['sss_status'] ?? 'pending') === $statusFilter) $hasStatus = true;
                                                    }
                                                    if ($contributionFilter === 'all' || $contributionFilter === 'pagibig') {
                                                        if (($emp['pagibig_status'] ?? 'pending') === $statusFilter) $hasStatus = true;
                                                    }
                                                    if ($contributionFilter === 'all' || $contributionFilter === 'philhealth') {
                                                        if (($emp['philhealth_status'] ?? 'pending') === $statusFilter) $hasStatus = true;
                                                    }
                                                    if (!$hasStatus) continue;
                                                }
                                                
                                                if ($contributionFilter !== 'all') {
                                                    $hasContribution = false;
                                                    if ($contributionFilter === 'sss' && ($emp['sss_status'] ?? 'pending') !== 'pending') $hasContribution = true;
                                                    if ($contributionFilter === 'pagibig' && ($emp['pagibig_status'] ?? 'pending') !== 'pending') $hasContribution = true;
                                                    if ($contributionFilter === 'philhealth' && ($emp['philhealth_status'] ?? 'pending') !== 'pending') $hasContribution = true;
                                                    if (!$hasContribution && $statusFilter === 'all') continue;
                                                }
                                                ?>
                                                <tr data-employee-id="<?= $emp['employee_id'] ?>">
                                                    <td><input type="checkbox" class="employee-checkbox" value="<?= $emp['employee_id'] ?>"></td>
                                                    <td><?= htmlspecialchars($emp['employee_name']) ?></td>
                                                    <td><?= htmlspecialchars($emp['department'] ?? 'N/A') ?></td>
                                                    <td><?= htmlspecialchars($emp['position'] ?? 'N/A') ?></td>
                                                    <td data-sss-number="<?= htmlspecialchars($emp['sss_number'] ?? '') ?>">
                                                        <span class="badge badge-<?= ($emp['sss_status'] ?? 'pending') === 'submitted' ? 'success' : 'warning' ?>">
                                                            <?= ucfirst($emp['sss_status'] ?? 'pending') ?>
                                                        </span>
                                                    </td>
                                                    <td data-pagibig-number="<?= htmlspecialchars($emp['pagibig_number'] ?? '') ?>">
                                                        <span class="badge badge-<?= ($emp['pagibig_status'] ?? 'pending') === 'submitted' ? 'success' : 'warning' ?>">
                                                            <?= ucfirst($emp['pagibig_status'] ?? 'pending') ?>
                                                        </span>
                                                    </td>
                                                    <td data-philhealth-number="<?= htmlspecialchars($emp['philhealth_number'] ?? '') ?>">
                                                        <span class="badge badge-<?= ($emp['philhealth_status'] ?? 'pending') === 'submitted' ? 'success' : 'warning' ?>">
                                                            <?= ucfirst($emp['philhealth_status'] ?? 'pending') ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info view-details" data-employee-id="<?= $emp['employee_id'] ?>" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-primary edit-contribution" data-employee-id="<?= $emp['employee_id'] ?>" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Edit Contribution Modal -->
    <div class="modal fade" id="editContributionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Employee Contributions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Employee Information</h6>
                            <p><strong>Name:</strong> <span id="modalEmployeeName"></span></p>
                            <p><strong>Department:</strong> <span id="modalDepartment"></span></p>
                            <p><strong>Position:</strong> <span id="modalPosition"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Contribution Status</h6>
                            <form id="contributionForm">
                                <input type="hidden" id="modalEmployeeId" name="employee_id">
                                
                                <div class="form-group">
                                    <label>SSS Status</label>
                                    <select name="sss_status" class="form-control" id="modalSssStatus">
                                        <option value="pending">Pending</option>
                                        <option value="submitted">Submitted</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>SSS Number</label>
                                    <input type="text" name="sss_number" class="form-control" id="modalSssNumber" placeholder="XX-XXXXXXX-X">
                                    <small class="form-text text-muted">Format: XX-XXXXXXX-X (e.g., 12-3456789-0)</small>
                                </div>
                                
                                <div class="form-group">
                                    <label>PAGIBIG Status</label>
                                    <select name="pagibig_status" class="form-control" id="modalPagibigStatus">
                                        <option value="pending">Pending</option>
                                        <option value="submitted">Submitted</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>PAG-IBIG MID Number</label>
                                    <input type="text" name="pagibig_number" class="form-control" id="modalPagibigNumber" placeholder="XXXX-XXXX-XXXX">
                                    <small class="form-text text-muted">Format: XXXX-XXXX-XXXX (e.g., 1234-5678-9012)</small>
                                </div>
                                
                                <div class="form-group">
                                    <label>PhilHealth Status</label>
                                    <select name="philhealth_status" class="form-control" id="modalPhilhealthStatus">
                                        <option value="pending">Pending</option>
                                        <option value="submitted">Submitted</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>PhilHealth Number</label>
                                    <input type="text" name="philhealth_number" class="form-control" id="modalPhilhealthNumber" placeholder="XXXX-XXXX-XXXX">
                                    <small class="form-text text-muted">Format: XXXX-XXXX-XXXX (e.g., 1234-5678-9012)</small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveContributions"><i class="fas fa-save"></i> Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Update Modal -->
    <div class="modal fade" id="bulkUpdateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Bulk Update Contributions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <span id="selectedCount">0</span> employee(s) selected
                    </div>
                    <form id="bulkUpdateForm">
                        <div class="form-group">
                            <label>Contribution Type</label>
                            <select name="contribution_type" class="form-control" required>
                                <option value="sss">SSS</option>
                                <option value="pagibig">PAGIBIG</option>
                                <option value="philhealth">PhilHealth</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="pending">Pending</option>
                                <option value="submitted">Submitted</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="applyBulkUpdate"><i class="fas fa-check"></i> Apply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal fade" id="viewDetailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title"><i class="fas fa-eye"></i> Contribution Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="viewDetailsContent">
                    <!-- Content loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Footer -->
</div>

<!-- Scripts -->
<script src="<?= $basePath ?>assets/plugins/jquery/jquery.min.js"></script>
<script src="<?= $basePath ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= $basePath ?>assets/dist/js/adminlte.min.js"></script>

<!-- DataTables -->
<link rel="stylesheet" href="<?= $basePath ?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?= $basePath ?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<script src="<?= $basePath ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= $basePath ?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= $basePath ?>assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= $basePath ?>assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

<!-- SweetAlert2 -->
<script src="<?= $basePath ?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>

<!-- Contributions JavaScript -->
<script src="js/employee_contributions.js"></script>

</body>
</html>
