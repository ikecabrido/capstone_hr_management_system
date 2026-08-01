<?php
/**
 * Medical Emergencies Module
 * HR Legal & Compliance System
 * Bestlink College of the Philippines
 * 
 * This module displays medical emergency cases FROM lc_cm_emergency_cases table
 */

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include Form Protection utility
require_once __DIR__ . '/../includes/FormProtection.php';

// Determine base paths for assets
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$basePath = ($currentFolder === 'legal_compliance') ? '../' : '../';
$componentsPath = $basePath . 'legal_compliance/components/';

// Include database connection
require_once $basePath . 'auth/database.php';
require_once $basePath . 'auth/auth_check.php';

// Get database connection
$db = Database::getInstance()->getConnection();

// Get emergency cases FROM lc_database
$query = "SELECT ec.*, CONCAT(p.first_name, ' ', p.last_name) as patient_name 
          FROM cm_emergency_cases ec 
          LEFT JOIN cm_patients p ON ec.patient_id = p.patient_id 
          ORDER BY ec.incident_date DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$emergencyCases = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$totalCases = count($emergencyCases);
$activeCases = count(array_filter($emergencyCases, function($c) { return $c['case_status'] === 'Active' || $c['case_status'] === 'Open'; }));
$criticalCases = count(array_filter($emergencyCases, function($c) { return $c['severity_level'] === 'Critical'; }));
$resolvedCases = count(array_filter($emergencyCases, function($c) { return $c['case_status'] === 'Resolved' || $c['case_status'] === 'Closed'; }));

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Emergencies - BCP HR System</title>
    
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=swap">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/plugins/fontawesome-free/css/all.min.css">
    
    <!-- AdminLTE -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/dist/css/adminlte.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $componentsPath ?>custom.css">
    
    <!-- Medical Emergencies CSS -->
    <link rel="stylesheet" href="css/medical_emergencies.css">
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
                            <i class="fas fa-ambulance mr-2"></i>
                            Medical Emergencies
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Medical Emergencies</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="content">
            <div class="container-fluid">
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= $totalCases ?></h3>
                                <p>Total Cases</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-file-medical"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= $activeCases ?></h3>
                                <p>Active Cases</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?= $criticalCases ?></h3>
                                <p>Critical Cases</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= $resolvedCases ?></h3>
                                <p>Resolved Cases</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Emergency Cases Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-ambulance mr-2"></i> Emergency Cases</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="emergencyCasesTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Case ID</th>
                                                <th>Patient Name</th>
                                                <th>Incident Date</th>
                                                <th>Type</th>
                                                <th>Severity</th>
                                                <th>Chief Complaint</th>
                                                <th>Attending Staff</th>
                                                <th>Status</th>
                                                <th>Ambulance</th>
                                                <th>Parents Notified</th>
                                                <th>Follow-up</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($emergencyCases) > 0): ?>
                                                <?php foreach ($emergencyCases as $case): ?>
                                                    <tr>
                                                        <td><strong><?= htmlspecialchars($case['case_id']) ?></strong></td>
                                                        <td><?= htmlspecialchars($case['patient_name'] ?? 'Unknown Patient') ?> <small class="text-muted">(<?= htmlspecialchars($case['patient_id']) ?>)</small></td>
                                                        <td><?= date('M d, Y h:i A', strtotime($case['incident_date'])) ?></td>
                                                        <td>
                                                            <?php 
                                                            $typeColors = [
                                                                'Accident' => 'primary',
                                                                'Medical Emergency' => 'danger',
                                                                'Injury' => 'warning',
                                                                'Other' => 'secondary'
                                                            ];
                                                            $typeColor = $typeColors[$case['incident_type']] ?? 'secondary';
                                                            ?>
                                                            <span class="badge badge-<?= $typeColor ?>"><?= htmlspecialchars($case['incident_type']) ?></span>
                                                        </td>
                                                        <td>
                                                            <?php 
                                                            $severityColors = [
                                                                'Critical' => 'danger',
                                                                'High' => 'warning',
                                                                'Medium' => 'info',
                                                                'Low' => 'secondary',
                                                                'Minor' => 'success'
                                                            ];
                                                            $severityColor = $severityColors[$case['severity_level']] ?? 'secondary';
                                                            ?>
                                                            <span class="badge badge-<?= $severityColor ?>"><?= htmlspecialchars($case['severity_level']) ?></span>
                                                        </td>
                                                        <td><?= htmlspecialchars($case['chief_complaint']) ?></td>
                                                        <td><?= htmlspecialchars($case['attending_staff'] ?? 'N/A') ?></td>
                                                        <td>
                                                            <?php 
                                                            $statusColors = [
                                                                'Active' => 'warning',
                                                                'Open' => 'info',
                                                                'Resolved' => 'success',
                                                                'Closed' => 'secondary',
                                                                'Transferred' => 'primary'
                                                            ];
                                                            $statusColor = $statusColors[$case['case_status']] ?? 'secondary';
                                                            ?>
                                                            <span class="badge badge-<?= $statusColor ?>"><?= htmlspecialchars($case['case_status']) ?></span>
                                                        </td>
                                                        <td>
                                                            <?php if ($case['ambulance_called']): ?>
                                                                <span class="badge badge-success">Yes</span>
                                                                <?php if ($case['ambulance_arrival_time']): ?>
                                                                    <br><small><?= date('h:i A', strtotime($case['ambulance_arrival_time'])) ?></small>
                                                                <?php endif; ?>
                                                            <?php else: ?>
                                                                <span class="badge badge-secondary">No</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($case['parents_notified']): ?>
                                                                <span class="badge badge-success">Yes</span>
                                                                <?php if ($case['parent_notification_time']): ?>
                                                                    <br><small><?= date('h:i A', strtotime($case['parent_notification_time'])) ?></small>
                                                                <?php endif; ?>
                                                            <?php else: ?>
                                                                <span class="badge badge-secondary">No</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($case['follow_up_required']): ?>
                                                                <span class="badge badge-warning">Required</span>
                                                                <?php if ($case['follow_up_date']): ?>
                                                                    <br><small><?= date('M d, Y', strtotime($case['follow_up_date'])) ?></small>
                                                                <?php endif; ?>
                                                            <?php else: ?>
                                                                <span class="badge badge-secondary">Not Required</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="11" class="text-center text-muted py-4">
                                                        <i class="fas fa-ambulance fa-3x mb-3"></i>
                                                        <p>No emergency cases found</p>
                                                    </td>
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
    
    <!-- Main Footer -->
</div>

<!-- Scripts -->
<script src="<?= $basePath ?>assets/plugins/jquery/jquery.min.js"></script>
<script src="<?= $basePath ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= $basePath ?>assets/dist/js/adminlte.min.js"></script>

<!-- DataTables -->
<script src="<?= $basePath ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= $basePath ?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#emergencyCasesTable').DataTable({
        "responsive": true,
        "autoWidth": false,
        "order": [[2, "desc"]],
        "pageLength": 10,
        "language": {
            "emptyTable": "No emergency cases found",
            "zeroRecords": "No matching records found"
        }
    });
});
</script>

<!-- Form Protection JavaScript -->
<script src="<?= $basePath ?>includes/form-protection.js"></script>
</body>
</html>
