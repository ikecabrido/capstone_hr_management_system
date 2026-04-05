<?php
session_start();
require_once "../auth/auth_check.php";
require_once "../auth/database.php";
require_once "core/BaseModel.php";
require_once "models/EmergencyCase.php";
require_once "models/Patient.php";
require_once "models/MedicineInventory.php";

$database = Database::getInstance();
$db = $database->getConnection();

if ($db === null) {
    $_SESSION['error'] = "Database connection failed.";
    header('Location: ../index.php');
    exit;
}

// Proactive database schema update
try {
    $stmt = $db->query("SHOW COLUMNS FROM cm_emergency_cases");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('parents_notified', $columns)) {
        $db->exec("ALTER TABLE cm_emergency_cases ADD COLUMN parents_notified BOOLEAN DEFAULT FALSE AFTER ambulance_arrival_time");
    }
    if (!in_array('parent_notification_time', $columns)) {
        $db->exec("ALTER TABLE cm_emergency_cases ADD COLUMN parent_notification_time DATETIME AFTER parents_notified");
    }
    if (!in_array('witness_names', $columns)) {
        $db->exec("ALTER TABLE cm_emergency_cases ADD COLUMN witness_names TEXT AFTER parent_notification_time");
    }
    
    // Update ENUMs
    $db->exec("ALTER TABLE cm_emergency_cases MODIFY COLUMN severity_level ENUM('Low', 'Medium', 'High', 'Critical', 'Minor')");
    $db->exec("ALTER TABLE cm_emergency_cases MODIFY COLUMN case_status ENUM('Active', 'Resolved', 'Transferred', 'Closed', 'Open') DEFAULT 'Active'");
    
    // Allow attending_staff to be a name string instead of just ID
    try {
        $db->exec("ALTER TABLE cm_emergency_cases DROP FOREIGN KEY cm_emergency_cases_ibfk_2");
    } catch (PDOException $e) {} // Ignore if already dropped
    $db->exec("ALTER TABLE cm_emergency_cases MODIFY COLUMN attending_staff VARCHAR(255)");
} catch (PDOException $e) {
    // Silently ignore if it's already updated or fails
}

$emergency = new EmergencyCase($db);
$patient = new Patient($db);
$medicine_inventory = new MedicineInventory($db);

$action = $_POST['action'] ?? '';
$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['message'], $_SESSION['error']);

if ($action === 'add_emergency') {
    try {
        $case_id = 'EM' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        $incident_date = $_POST['incident_date_only'] . ' ' . $_POST['incident_time_only'];
        
        $ambulance_arrival_time = null;
        if (!empty($_POST['ambulance_arrival_time'])) {
            $ambulance_arrival_time = $_POST['incident_date_only'] . ' ' . $_POST['ambulance_arrival_time'];
        }
        
        $parent_notification_time = null;
        if (!empty($_POST['parent_notification_time'])) {
            $parent_notification_time = $_POST['incident_date_only'] . ' ' . $_POST['parent_notification_time'];
        }
        
        $data = [
            'case_id' => $case_id,
            'patient_id' => $_POST['patient_id'],
            'incident_date' => $incident_date,
            'incident_type' => $_POST['incident_type'],
            'severity_level' => $_POST['severity_level'],
            'case_status' => $_POST['case_status'] ?? 'Open',
            'chief_complaint' => $_POST['incident_description'] ?? '',
            'initial_assessment' => $_POST['injury_description'] ?? '',
            'treatment_provided' => $_POST['immediate_action'] ?? '',
            'attending_staff' => $_POST['attending_staff'],
            'ambulance_called' => isset($_POST['ambulance_called']) ? 1 : 0,
            'ambulance_arrival_time' => $ambulance_arrival_time,
            'parents_notified' => isset($_POST['parents_notified']) ? 1 : 0,
            'parent_notification_time' => $parent_notification_time,
            'witness_names' => $_POST['witness_names'] ?? '',
            'follow_up_required' => isset($_POST['follow_up_required']) ? 1 : 0,
            'follow_up_date' => $_POST['follow_up_date'] ?? null,
            'created_by' => $_SESSION['user']['name']
        ];
        
        if ($emergency->create($data)) {
            // Handle medicine usage if provided
            if (isset($_POST['medicines']) && is_array($_POST['medicines'])) {
                foreach ($_POST['medicines'] as $med) {
                    if (!empty($med['id']) && !empty($med['quantity']) && $med['quantity'] > 0) {
                        $medicine_inventory->deductStock(
                            $med['id'], 
                            $med['quantity'], 
                            "Used in Emergency: $case_id", 
                            $_SESSION['user']['name']
                        );
                    }
                }
            }
            $_SESSION['message'] = "Emergency case reported successfully!";
        } else {
            $_SESSION['error'] = "Failed to report emergency case.";
            if (isset($db->errorInfo()[2])) {
                $_SESSION['error'] .= " DB Error: " . $db->errorInfo()[2];
            }
        }
        header("Location: Emergency.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: Emergency.php");
        exit;
    }
}

if ($action === 'update_case') {
    try {
        $case_id = $_POST['case_id'];
        $data = [
            'case_status' => $_POST['case_status'],
            'treatment_provided' => $_POST['treatment_provided'],
            'transfer_hospital' => $_POST['transfer_hospital'] ?? '',
            'follow_up_required' => isset($_POST['follow_up_required']) ? 1 : 0,
            'follow_up_date' => $_POST['follow_up_date'] ?? null,
            'notes' => $_POST['notes']
        ];
        
        if ($emergency->update($case_id, $data)) {
            $_SESSION['message'] = "Emergency case updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update emergency case.";
        }
        header("Location: Emergency.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: Emergency.php");
        exit;
    }
}

if ($action === 'close_case') {
    try {
        $case_id = $_POST['case_id'];
        if ($emergency->closeCase($case_id, $_SESSION['user']['name'])) {
            $_SESSION['message'] = "Emergency case closed successfully!";
        } else {
            $_SESSION['error'] = "Failed to close emergency case.";
        }
        header("Location: Emergency.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: Emergency.php");
        exit;
    }
}

if ($action === 'delete_case') {
    try {
        $case_id = $_POST['case_id'];
        if ($emergency->delete($case_id)) {
            $_SESSION['message'] = "Emergency case deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete emergency case.";
        }
        header("Location: Emergency.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: Emergency.php");
        exit;
    }
}

$stats = $emergency->getEmergencyStats();
$active_cases = $emergency->readActive();
$all_cases = $emergency->readAllWithPatientDetails('incident_date DESC');
$patients = $patient->read([], 'last_name ASC, first_name ASC');
$available_medicines = $medicine_inventory->read(['status' => 'Available'], 'medicine_name ASC');

$theme = $_SESSION['user']['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Cases - BCP Clinic</title>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" rel="stylesheet">
    <link href="../assets/plugins/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/dist/css/adminlte.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="custom.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #007bff;
            --light-bg: #f4f6f9;
            --card-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Source Sans Pro', sans-serif;
        }

        .main-header {
            background-color: #007bff !important;
            border-bottom: none !important;
        }

        .navbar-nav .nav-link {
            color: white !important;
        }

        .content-header h1 {
            font-size: 1.8rem;
            margin: 0;
            color: #333;
            font-weight: 600;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 0;
        }

        .breadcrumb-item a {
            color: #007bff;
        }

        .breadcrumb-item.active {
            color: #6c757d;
        }

        .card {
            border: none;
            box-shadow: var(--card-shadow);
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0,0,0,.125);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 400;
            color: #333;
            margin: 0;
        }

        .btn-report {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }

        .btn-report:hover {
            background-color: #0069d9;
            color: white;
        }

        .search-container {
            padding: 15px 20px;
        }

        .search-group {
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 8px 15px;
            padding-right: 40px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            border-top: none;
            border-bottom: 2px solid #dee2e6;
            color: #333;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 12px 15px;
        }

        .table tbody td {
            padding: 12px 15px;
            vertical-align: middle;
            color: #555;
            font-size: 0.9rem;
        }

        .severity-dot {
            display: inline-block;
            width: 12px;
            height: 4px;
            border-radius: 2px;
            background-color: #00bcd4;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
        }

        .status-closed {
            background-color: #28a745;
        }

        .status-active {
            background-color: #ffc107;
            color: #333;
        }

        .action-btns {
            display: flex;
            gap: 5px;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            border: none;
            color: white;
            font-size: 0.85rem;
        }

        .btn-view { background-color: #17a2b8; }
        .btn-edit { background-color: #ffc107; }
        .btn-delete { background-color: #dc3545; }

        .btn-action:hover {
            opacity: 0.85;
            color: white;
        }
    </style>
</head>
<body class="<?= $theme === 'dark' ? 'dark-mode' : '' ?>">
    <div class="wrapper">
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__wobble" src="../assets/pics/bcpLogo.png" alt="AdminLTELogo" height="60" width="60">
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="clinic.php" class="nav-link">Home</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="clinic.php" class="nav-link">Dashboard</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <div class="nav-link" id="clock">--:--:--</div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="darkToggle" role="button" title="Toggle Dark Mode">
                        <i class="fas fa-moon" id="themeIcon"></i>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="clinic.php" class="brand-link">
                <img src="../assets/pics/bcpLogo.png" alt="Logo" class="brand-image elevation-3" style="opacity: 0.9">
                <span class="brand-text font-weight-light">BCP Clinic</span>
            </a>
            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="info">
                        <a href="#" class="d-block">Admin <?= htmlspecialchars($_SESSION['user']['name']) ?></a>
                    </div>
                </div>
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="clinic.php" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Employee_Patient.php" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Employee</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="MedicalRecordsHistory.php" class="nav-link">
                                <i class="nav-icon fas fa-file-medical"></i>
                                <p>Medical Records History</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="MedicinesInventory.php" class="nav-link">
                                <i class="nav-icon fas fa-pills"></i>
                                <p>Medicines Inventory</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Clinic_Reports.php" class="nav-link">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Clinic Reports</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Emergency.php" class="nav-link active">
                                <i class="nav-icon fas fa-ambulance"></i>
                                <p>Emergency Cases</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php">
                               <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-6">
                            <h1 class="m-0">Emergency Management</h1>
                            <p class="text-muted mb-0">Manage and record emergency cases and incidents within the clinic</p>
                        </div>
                        <div class="col-sm-6 text-right">
                            <a href="generate_clinic_pdf.php?type=emergency_list" class="btn btn-success mr-2" target="_blank">
                                <i class="fas fa-file-pdf"></i> Export List
                            </a>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#addEmergencyModal">
                                <i class="fas fa-plus mr-1"></i> Report Emergency
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <!-- Messages -->
                    <?php if ($message): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="fas fa-check-circle mr-2"></i> <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="fas fa-exclamation-triangle mr-2"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Emergency Cases</h3>
                        </div>
                        <div class="search-container">
                            <div class="search-group">
                                <input type="text" id="caseSearch" class="search-input" placeholder="Search cases...">
                                <i class="fas fa-search search-icon"></i>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="emergencyTable">
                                    <thead>
                                        <tr>
                                            <th>Case ID</th>
                                            <th>Patient</th>
                                            <th>Incident Type</th>
                                            <th>Date/Time</th>
                                            <th>Severity</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($all_cases)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">No emergency cases found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($all_cases as $case): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($case['case_id']) ?></td>
                                                <td><?= htmlspecialchars(($case['first_name'] ?? '') . ' ' . ($case['last_name'] ?? '')) ?></td>
                                                <td><?= htmlspecialchars($case['incident_type']) ?></td>
                                                <td><?= date('M d, Y H:i', strtotime($case['incident_date'])) ?></td>
                                                <td>
                                                    <span class="severity-dot" title="<?= htmlspecialchars($case['severity_level']) ?>"></span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status = $case['case_status'] ?? 'Active';
                                                    $status_class = strtolower($status) === 'closed' ? 'status-closed' : 'status-active';
                                                    ?>
                                                    <span class="status-badge <?= $status_class ?>"><?= htmlspecialchars($status) ?></span>
                                                </td>
                                                <td>
                                                    <div class="action-btns">
                                                        <a href="generate_clinic_pdf.php?type=emergency&id=<?= $case['case_id'] ?>" class="btn-action btn-success" title="Download Report" target="_blank">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                        <button class="btn-action btn-view" onclick="viewCase(<?= htmlspecialchars(json_encode($case)) ?>)" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn-action btn-edit" onclick="updateCase(<?= htmlspecialchars(json_encode($case)) ?>)" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn-action btn-delete" onclick="deleteCase('<?= htmlspecialchars($case['case_id']) ?>')" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
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
            </section>
        </div>
    </div>

    <!-- Add Emergency Modal -->
    <div class="modal fade" id="addEmergencyModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Report Emergency Case</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add_emergency">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Patient</label>
                                    <select name="patient_id" class="form-control" required>
                                        <option value="">Select Patient</option>
                                        <?php foreach ($patients as $p): ?>
                                        <option value="<?= htmlspecialchars($p['patient_id']) ?>">
                                            <?= htmlspecialchars(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? '')) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Incident Type</label>
                                    <select name="incident_type" class="form-control" required>
                                        <option value="">Select Type</option>
                                        <option value="Accident">Accident</option>
                                        <option value="Medical Emergency">Medical Emergency</option>
                                        <option value="Injury">Injury</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Incident Date</label>
                                    <input type="date" name="incident_date_only" class="form-control" required value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Incident Time</label>
                                    <input type="time" name="incident_time_only" class="form-control" required value="<?= date('H:i') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Incident Description</label>
                            <textarea name="incident_description" class="form-control" rows="3" placeholder="Enter incident description..."></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Injury Description</label>
                            <textarea name="injury_description" class="form-control" rows="3" placeholder="Enter injury description..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Severity Level</label>
                                    <select name="severity_level" class="form-control" required>
                                        <option value="Minor">Minor</option>
                                        <option value="Medium" selected>Medium</option>
                                        <option value="High">High</option>
                                        <option value="Critical">Critical</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Case Status</label>
                                    <select name="case_status" class="form-control" required>
                                        <option value="Open" selected>Open</option>
                                        <option value="Closed">Closed</option>
                                        <option value="Transferred">Transferred</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Attending Staff</label>
                                    <input type="text" name="attending_staff" class="form-control" placeholder="Enter staff name">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Immediate Action Taken</label>
                            <textarea name="immediate_action" class="form-control" rows="3" placeholder="Enter immediate action taken..."></textarea>
                        </div>
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Dispense Medicines</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" id="addMedicineBtn">
                                        <i class="fas fa-plus"></i> Add Medicine
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm" id="medicinesTable">
                                    <thead>
                                        <tr>
                                            <th>Medicine</th>
                                            <th width="100px">Qty</th>
                                            <th width="50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="medicinesList">
                                        <!-- Medicine rows will be added here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row align-items-center mb-3">
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="ambulance_called" class="custom-control-input" id="ambulanceCheck">
                                    <label class="custom-control-label" for="ambulanceCheck">Ambulance Called</label>
                                </div>
                            </div>
                            <div class="col-md-6" id="ambulanceTimeCol" style="display: none;">
                                <div class="form-group mb-0">
                                    <label class="form-label">Ambulance Arrival Time</label>
                                    <input type="time" name="ambulance_arrival_time" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center mb-3">
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="parents_notified" class="custom-control-input" id="parentsNotifiedCheck">
                                    <label class="custom-control-label" for="parentsNotifiedCheck">Parents Notified</label>
                                </div>
                            </div>
                            <div class="col-md-6" id="parentNotificationTimeCol" style="display: none;">
                                <div class="form-group mb-0">
                                    <label class="form-label">Parent Notification Time</label>
                                    <input type="time" name="parent_notification_time" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Witness Names</label>
                            <input type="text" name="witness_names" class="form-control" placeholder="Enter witness names...">
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="follow_up_required" class="custom-control-input" id="followupCheck">
                                    <label class="custom-control-label" for="followupCheck">Follow-up Required</label>
                                </div>
                            </div>
                            <div class="col-md-6" id="followupDateCol" style="display: none;">
                                <div class="form-group mb-0">
                                    <label class="form-label">Follow-up Date</label>
                                    <input type="date" name="follow_up_date" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Case</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Emergency Modal -->
    <div class="modal fade" id="viewEmergencyModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h4 class="modal-title"><i class="fas fa-eye mr-2"></i>Emergency Case Details</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="viewCaseContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Emergency Modal -->
    <div class="modal fade" id="updateEmergencyModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h4 class="modal-title"><i class="fas fa-edit mr-2"></i>Update Emergency Case</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="update_case">
                    <input type="hidden" name="case_id" id="update_case_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Case Status</label>
                                    <select name="case_status" class="form-control" required>
                                        <option value="Active">Active</option>
                                        <option value="Resolved">Resolved</option>
                                        <option value="Transferred">Transferred</option>
                                        <option value="Closed">Closed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Transfer Hospital</label>
                                    <input type="text" name="transfer_hospital" id="update_transfer_hospital" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Treatment Provided</label>
                            <textarea name="treatment_provided" id="update_treatment_provided" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="follow_up_required" class="form-check-input" id="updateFollowupCheck">
                                        <label class="form-check-label" for="updateFollowupCheck">Follow-up Required</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Follow-up Date</label>
                                    <input type="date" name="follow_up_date" id="update_follow_up_date" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" id="update_notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Case</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/dist/js/adminlte.js"></script>
    <script src="../assets/dist/js/theme.js"></script>
    <script src="../assets/dist/js/time.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            var medicineIndex = 0;
            var availableMedicines = <?= json_encode($available_medicines) ?>;

            $('#addMedicineBtn').click(function() {
                var row = `
                    <tr id="med-row-${medicineIndex}">
                        <td>
                            <select name="medicines[${medicineIndex}][id]" class="form-control form-control-sm select2" required>
                                <option value="">Select Medicine</option>
                                ${availableMedicines.map(m => `<option value="${m.medicine_id}">${m.medicine_name} (${m.current_stock} available)</option>`).join('')}
                            </select>
                        </td>
                        <td>
                            <input type="number" name="medicines[${medicineIndex}][quantity]" class="form-control form-control-sm" value="1" min="1" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-med" data-row="${medicineIndex}">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#medicinesList').append(row);
                medicineIndex++;
            });

            $(document).on('click', '.remove-med', function() {
                var rowId = $(this).data('row');
                $('#med-row-' + rowId).remove();
            });

            // Case Search Functionality
            $("#caseSearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#emergencyTable tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            $('#ambulanceCheck').change(function() {
                if ($(this).is(':checked')) {
                    $('#ambulanceTimeCol').show();
                } else {
                    $('#ambulanceTimeCol').hide();
                }
            });
            
            $('#parentsNotifiedCheck').change(function() {
                if ($(this).is(':checked')) {
                    $('#parentNotificationTimeCol').show();
                } else {
                    $('#parentNotificationTimeCol').hide();
                }
            });
            
            $('#followupCheck').change(function() {
                if ($(this).is(':checked')) {
                    $('#followupDateCol').show();
                } else {
                    $('#followupDateCol').hide();
                }
            });
            
            <?php if ($message || $error): ?>
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
            <?php endif; ?>
        });
        
        function viewCase(caseData) {
            var caseInfo = typeof caseData === 'string' ? JSON.parse(caseData) : caseData;
            
            var html = '<div class="row">';
            html += '<div class="col-md-6"><p><strong>Case ID:</strong> ' + caseInfo.case_id + '</p></div>';
            html += '<div class="col-md-6"><p><strong>Patient:</strong> ' + (caseInfo.first_name || '') + ' ' + (caseInfo.last_name || '') + '</p></div>';
            html += '<div class="col-md-6"><p><strong>Incident Type:</strong> ' + (caseInfo.incident_type || 'N/A') + '</p></div>';
            html += '<div class="col-md-6"><p><strong>Severity:</strong> ' + (caseInfo.severity_level || 'Minor') + '</p></div>';
            html += '<div class="col-md-6"><p><strong>Date/Time:</strong> ' + formatDateTime(caseInfo.incident_date) + '</p></div>';
            html += '<div class="col-md-6"><p><strong>Status:</strong> ' + (caseInfo.case_status || 'Open') + '</p></div>';
            html += '<div class="col-md-12"><p><strong>Incident Description:</strong><br>' + (caseInfo.chief_complaint || 'N/A') + '</p></div>';
            html += '<div class="col-md-12"><p><strong>Injury Description:</strong><br>' + (caseInfo.initial_assessment || 'N/A') + '</p></div>';
            html += '<div class="col-md-12"><p><strong>Immediate Action Taken:</strong><br>' + (caseInfo.treatment_provided || 'N/A') + '</p></div>';
            html += '<div class="col-md-6"><p><strong>Attending Staff:</strong> ' + (caseInfo.attending_staff || 'N/A') + '</p></div>';
            html += '<div class="col-md-6"><p><strong>Ambulance Called:</strong> ' + (caseInfo.ambulance_called == 1 ? 'Yes' : 'No') + '</p></div>';
            if (caseInfo.ambulance_called == 1) {
                html += '<div class="col-md-6"><p><strong>Ambulance Arrival:</strong> ' + (caseInfo.ambulance_arrival_time || 'N/A') + '</p></div>';
            }
            html += '<div class="col-md-6"><p><strong>Parents Notified:</strong> ' + (caseInfo.parents_notified == 1 ? 'Yes' : 'No') + '</p></div>';
            if (caseInfo.parents_notified == 1) {
                html += '<div class="col-md-6"><p><strong>Parent Notification:</strong> ' + (caseInfo.parent_notification_time || 'N/A') + '</p></div>';
            }
            html += '<div class="col-md-12"><p><strong>Witness Names:</strong><br>' + (caseInfo.witness_names || 'N/A') + '</p></div>';
            html += '<div class="col-md-6"><p><strong>Follow-up Required:</strong> ' + (caseInfo.follow_up_required == 1 ? 'Yes' : 'No') + '</p></div>';
            if (caseInfo.follow_up_required == 1) {
                html += '<div class="col-md-6"><p><strong>Follow-up Date:</strong> ' + (caseInfo.follow_up_date || 'N/A') + '</p></div>';
            }
            html += '</div>';
            
            $('#viewCaseContent').html(html);
            $('#viewEmergencyModal').modal('show');
        }
        
        function updateCase(caseData) {
            var caseInfo = typeof caseData === 'string' ? JSON.parse(caseData) : caseData;
            
            $('#update_case_id').val(caseInfo.case_id);
            $('#update_transfer_hospital').val(caseInfo.transfer_hospital || '');
            $('#update_treatment_provided').val(caseInfo.treatment_provided || '');
            $('#update_notes').val(caseInfo.notes || '');
            $('#update_follow_up_date').val(caseInfo.follow_up_date || '');
            
            if (caseInfo.follow_up_required == 1 || caseInfo.follow_up_required === true) {
                $('#updateFollowupCheck').prop('checked', true);
            } else {
                $('#updateFollowupCheck').prop('checked', false);
            }
            
            $('#updateEmergencyModal').modal('show');
        }
        
        function closeCase(caseId) {
            if (confirm('Are you sure you want to close this emergency case?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'close_case';
                
                const caseIdInput = document.createElement('input');
                caseIdInput.type = 'hidden';
                caseIdInput.name = 'case_id';
                caseIdInput.value = caseId;
                
                form.appendChild(actionInput);
                form.appendChild(caseIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function deleteCase(caseId) {
            if (confirm('Are you sure you want to delete this emergency case? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete_case';
                
                const caseIdInput = document.createElement('input');
                caseIdInput.type = 'hidden';
                caseIdInput.name = 'case_id';
                caseIdInput.value = caseId;
                
                form.appendChild(actionInput);
                form.appendChild(caseIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function formatDateTime(dateStr) {
            if (!dateStr) return 'N/A';
            var date = new Date(dateStr);
            var options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            return date.toLocaleDateString('en-US', options);
        }
    </script>
</body>
</html>
