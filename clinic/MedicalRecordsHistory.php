<?php
session_start();
require_once "../auth/auth_check.php";
require_once "../auth/database.php";
require_once "core/BaseModel.php";
require_once "models/Patient.php";
require_once "models/MedicalRecord.php";
require_once "models/MedicineInventory.php";

$database = Database::getInstance();
$db = $database->getConnection();

if ($db === null) {
    $_SESSION['error'] = "Database connection failed.";
    header('Location: ../index.php');
    exit;
}

$patient = new Patient($db);
$medical_record = new MedicalRecord($db);
$medicine_inventory = new MedicineInventory($db);

$action = $_POST['action'] ?? '';
$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['message'], $_SESSION['error']);

if ($action === 'add_record') {
    try {
        $record_id = 'MR' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        $data = [
            'record_id' => $record_id,
            'patient_id' => $_POST['patient_id'],
            'chief_complaint' => $_POST['chief_complaint'],
            'diagnosis' => $_POST['diagnosis'],
            'treatment' => $_POST['treatment'],
            'consultation_type' => $_POST['consultation_type'],
            'attending_physician' => $_POST['attending_physician'],
            'vital_signs' => json_encode([
                'bp_systolic' => $_POST['bp_systolic'],
                'bp_diastolic' => $_POST['bp_diastolic'],
                'heart_rate' => $_POST['heart_rate'],
                'temperature' => $_POST['temperature'],
                'weight' => $_POST['weight'],
                'height' => $_POST['height']
            ]),
            'medications_prescribed' => $_POST['medications_prescribed'],
            'notes' => $_POST['notes'],
            'follow_up_date' => $_POST['follow_up_date'],
            'created_by' => $_SESSION['user']['name']
        ];
        
        if ($medical_record->create($data)) {
            // Handle medicine usage if provided
            if (isset($_POST['medicines']) && is_array($_POST['medicines'])) {
                foreach ($_POST['medicines'] as $med) {
                    if (!empty($med['id']) && !empty($med['quantity']) && $med['quantity'] > 0) {
                        if ($medicine_inventory->deductStock(
                            $med['id'], 
                            $med['quantity'], 
                            "Prescribed in Record: $record_id", 
                            $_SESSION['user']['name']
                        )) {
                            $medicine_inventory->updateStatus($med['id']); // Update status after deduction
                        }
                    }
                }
            }
            $_SESSION['message'] = "Medical record added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add medical record.";
        }
        header("Location: MedicalRecordsHistory.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: MedicalRecordsHistory.php");
        exit;
    }
}

if ($action === 'update_record') {
    try {
        $record_id = $_POST['record_id'];
        $data = [
            'chief_complaint' => $_POST['chief_complaint'],
            'diagnosis' => $_POST['diagnosis'],
            'treatment' => $_POST['treatment'],
            'consultation_type' => $_POST['consultation_type'],
            'attending_physician' => $_POST['attending_physician'],
            'vital_signs' => json_encode([
                'bp_systolic' => $_POST['bp_systolic'],
                'bp_diastolic' => $_POST['bp_diastolic'],
                'heart_rate' => $_POST['heart_rate'],
                'temperature' => $_POST['temperature'],
                'weight' => $_POST['weight'],
                'height' => $_POST['height']
            ]),
            'medications_prescribed' => $_POST['medications_prescribed'],
            'notes' => $_POST['notes'],
            'follow_up_date' => $_POST['follow_up_date'],
            'status' => $_POST['status'] ?? 'Pending'
        ];
        
        if ($medical_record->update($record_id, $data)) {
            $_SESSION['message'] = "Medical record updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update medical record.";
        }
        header("Location: MedicalRecordsHistory.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: MedicalRecordsHistory.php");
        exit;
    }
}

if ($action === 'delete_record') {
    try {
        $record_id = $_POST['record_id'];
        
        if ($medical_record->delete($record_id)) {
            $_SESSION['message'] = "Medical record deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete medical record.";
        }
        header("Location: MedicalRecordsHistory.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: MedicalRecordsHistory.php");
        exit;
    }
}

$stats = $medical_record->getMedicalStats();
$records = $medical_record->getRecordsWithPatientDetails();
$patients = $patient->read([], 'last_name ASC, first_name ASC');
$available_medicines = $medicine_inventory->read(['status' => 'Available'], 'medicine_name ASC');

$theme = $_SESSION['user']['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records - Clinic System</title>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" rel="stylesheet">
    <link href="../assets/plugins/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/dist/css/adminlte.min.css" rel="stylesheet">
    <link href="custom.css" rel="stylesheet">
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
                    <a href="MedicalRecordsHistory.php" class="nav-link">Medical Records</a>
                </li>
            </ul>

            <!-- Right navbar links -->
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
                            <a href="MedicalRecordsHistory.php" class="nav-link active">
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
                            <a href="Emergency.php" class="nav-link">
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
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Medical Records</h1>
                            <p class="text-muted">Store health records of employees including diagnoses, treatments, and vital signs</p>
                        </div>
                        <div class="col-sm-6">
                            <a href="generate_clinic_pdf.php?type=medical_record_list" class="btn btn-success float-right ml-2" target="_blank">
                                <i class="fas fa-file-pdf"></i> Export List
                            </a>
                            <button class="btn btn-primary float-right" data-toggle="modal" data-target="#addRecordModal">
                                <i class="fas fa-plus"></i> Add Record
                            </button>
                        </div>
                    </div>
                </div>
            </div>
                    <!-- Messages -->
                    <?php if ($message): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Records Table -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Medical Records</h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Record ID</th>
                                        <th>Date</th>
                                        <th>Patient</th>
                                        <th>Chief Complaint</th>
                                        <th>Diagnosis</th>
                                        <th>Physician</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($records as $record): ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-light border"><?= htmlspecialchars($record['record_id']) ?></span>
                                        </td>
                                        <td><?= date('M d, Y H:i', strtotime($record['visit_date'])) ?></td>
                                        <td>
                                            <?= htmlspecialchars($record['patient_name']) ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($record['patient_id']) ?> - <?= htmlspecialchars($record['patient_type']) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars(substr($record['chief_complaint'], 0, 50)) ?>...</td>
                                        <td><?= htmlspecialchars(substr($record['diagnosis'], 0, 50)) ?>...</td>
                                        <td><?= htmlspecialchars($record['attending_physician']) ?></td>
                                        <td>
                                            <?php
                                            $status = $record['status'] ?? 'Pending';
                                            $badge_class = $status === 'Completed' ? 'success' : ($status === 'Pending' ? 'warning' : 'info');
                                            ?>
                                            <span class="badge badge-<?= $badge_class ?>"><?= htmlspecialchars($status) ?></span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="viewRecord(<?= htmlspecialchars(json_encode($record)) ?>)" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="generate_clinic_pdf.php?type=medical_record&id=<?= $record['record_id'] ?>" class="btn btn-sm btn-success" title="Download PDF" target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <button class="btn btn-sm btn-warning" onclick="editRecord(<?= htmlspecialchars(json_encode($record)) ?>)" title="Edit Record">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteRecord('<?= htmlspecialchars($record['record_id']) ?>')" title="Delete Record">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Add Record Modal -->
    <div class="modal fade" id="addRecordModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Medical Record</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="addRecordForm" method="POST" action="MedicalRecordsHistory.php">
                    <input type="hidden" name="action" value="add_record">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Patient</label>
                                    <select name="patient_id" class="form-control" required>
                                        <option value="">Select Patient</option>
                                        <?php foreach ($patients as $p): ?>
                                        <option value="<?= htmlspecialchars($p['patient_id']) ?>">
                                            <?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Consultation Type</label>
                                    <select name="consultation_type" class="form-control">
                                        <option value="Walk-in">Walk-in</option>
                                        <option value="Appointment">Appointment</option>
                                        <option value="Emergency">Emergency</option>
                                        <option value="Follow-up">Follow-up</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Chief Complaint</label>
                            <textarea name="chief_complaint" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Diagnosis</label>
                            <textarea name="diagnosis" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Treatment</label>
                            <textarea name="treatment" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Medications Prescribed (General Notes)</label>
                            <textarea name="medications_prescribed" class="form-control" rows="2" placeholder="General notes about medications..."></textarea>
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

                        <h5>Vital Signs</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>BP (Systolic)</label>
                                    <input type="number" name="bp_systolic" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>BP (Diastolic)</label>
                                    <input type="number" name="bp_diastolic" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Heart Rate</label>
                                    <input type="number" name="heart_rate" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Temperature (°C)</label>
                                    <input type="number" name="temperature" class="form-control" step="0.1">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Weight (kg)</label>
                                    <input type="number" name="weight" class="form-control" step="0.1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Height (cm)</label>
                                    <input type="number" name="height" class="form-control" step="0.1">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Attending Physician</label>
                                    <input type="text" name="attending_physician" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Follow-up Date</label>
                                    <input type="date" name="follow_up_date" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Record Modal -->
    <div class="modal fade" id="editRecordModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Medical Record</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" id="editRecordForm" action="MedicalRecordsHistory.php">
                    <input type="hidden" name="action" value="update_record">
                    <input type="hidden" name="record_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Patient</label>
                                    <select name="patient_id" class="form-control" required>
                                        <option value="">Select Patient</option>
                                        <?php foreach ($patients as $patient): ?>
                                            <option value="<?= htmlspecialchars($patient['patient_id']) ?>">
                                                <?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Chief Complaint</label>
                                    <input type="text" name="chief_complaint" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Diagnosis</label>
                                    <textarea name="diagnosis" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Treatment</label>
                                    <textarea name="treatment" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Consultation Type</label>
                                    <select name="consultation_type" class="form-control" required>
                                        <option value="Walk-in">Walk-in</option>
                                        <option value="Appointment">Appointment</option>
                                        <option value="Emergency">Emergency</option>
                                        <option value="Follow-up">Follow-up</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Attending Physician</label>
                                    <input type="text" name="attending_physician" class="form-control" required>
                                </div>
                                <h5>Vital Signs</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Blood Pressure (Systolic)</label>
                                            <input type="number" name="bp_systolic" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Blood Pressure (Diastolic)</label>
                                            <input type="number" name="bp_diastolic" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Heart Rate</label>
                                            <input type="number" name="heart_rate" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Temperature (°C)</label>
                                            <input type="number" step="0.1" name="temperature" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Weight (kg)</label>
                                            <input type="number" step="0.1" name="weight" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Height (cm)</label>
                                            <input type="number" name="height" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Medications Prescribed</label>
                                    <textarea name="medications_prescribed" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Follow-up Date</label>
                                    <input type="date" name="follow_up_date" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="Pending">Pending</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Follow-up">Follow-up</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea name="notes" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Record</button>
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

            // Prevent double submission
            $('#addRecordForm, #editRecordForm').on('submit', function() {
                var submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            });

            // Reset add record modal on close
            $('#addRecordModal').on('hidden.bs.modal', function() {
                $('#addRecordForm')[0].reset();
                $('#medicinesList').empty();
                medicineIndex = 0;
            });

            // Table Search Logic
            $('input[name="table_search"]').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('table tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Sick Leave Days Calculation
        });

        function viewRecord(record) {
            var vitalSigns = JSON.parse(record.vital_signs || '{}');
            var details = `
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <strong>Record ID:</strong> <span class="badge badge-primary">${record.record_id}</span>
                    </div>
                    <div class="col-sm-6 text-right">
                        <strong>Visit Date:</strong> ${new Date(record.visit_date).toLocaleString()}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Patient:</strong> ${record.patient_name}<br>
                        <strong>Patient Type:</strong> ${record.patient_type}<br>
                        <strong>Consultation Type:</strong> ${record.consultation_type}<br>
                        <strong>Attending Physician:</strong> ${record.attending_physician}<br>
                    </div>
                    <div class="col-md-6 border-left">
                        <strong>Status:</strong> <span class="badge badge-${(record.status || 'Pending') === 'Completed' ? 'success' : ((record.status || 'Pending') === 'Pending' ? 'warning' : 'info')}">${record.status || 'Pending'}</span><br>
                        <strong>Follow-up Date:</strong> ${record.follow_up_date || 'N/A'}<br>
                    </div>
                </div>
                <hr>
                <strong>Chief Complaint:</strong> ${record.chief_complaint}<br>
                <strong>Diagnosis:</strong> ${record.diagnosis}<br>
                <strong>Treatment:</strong> ${record.treatment}<br>
                <hr>
                <h5>Vital Signs</h5>
                <div class="row">
                    <div class="col-md-4"><strong>Blood Pressure:</strong> ${vitalSigns.bp_systolic || 'N/A'}/${vitalSigns.bp_diastolic || 'N/A'}</div>
                    <div class="col-md-4"><strong>Heart Rate:</strong> ${vitalSigns.heart_rate || 'N/A'} bpm</div>
                    <div class="col-md-4"><strong>Temperature:</strong> ${vitalSigns.temperature || 'N/A'}°C</div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-4"><strong>Weight:</strong> ${vitalSigns.weight || 'N/A'} kg</div>
                    <div class="col-md-4"><strong>Height:</strong> ${vitalSigns.height || 'N/A'} cm</div>
                </div>
                <hr>
                <strong>Medications:</strong> ${record.medications_prescribed || 'N/A'}<br>
                <strong>Notes:</strong> ${record.notes || 'N/A'}
            `;
            
            // Create modal to show record details
            var modal = '<div class="modal fade" id="viewRecordModal" tabindex="-1">' +
                    '<div class="modal-dialog modal-lg">' +
                        '<div class="modal-content">' +
                            '<div class="modal-header">' +
                                '<h4 class="modal-title">Medical Record Details</h4>' +
                                '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
                            '</div>' +
                            '<div class="modal-body">' + details + '</div>' +
                            '<div class="modal-footer">' +
                                '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            
            // Remove existing modal if any
            $('#viewRecordModal').remove();
            // Add new modal to body and show it
            $('body').append(modal);
            $('#viewRecordModal').modal('show');
        }
        
        function editRecord(record) {
            // Handle both JSON string and object
            var recordData = typeof record === 'string' ? JSON.parse(record) : record;
            
            // Populate form with record data
            $('#editRecordForm input[name="record_id"]').val(recordData.record_id);
            $('#editRecordForm select[name="patient_id"]').val(recordData.patient_id);
            $('#editRecordForm input[name="chief_complaint"]').val(recordData.chief_complaint);
            $('#editRecordForm textarea[name="diagnosis"]').val(recordData.diagnosis);
            $('#editRecordForm textarea[name="treatment"]').val(recordData.treatment);
            $('#editRecordForm select[name="consultation_type"]').val(recordData.consultation_type);
            $('#editRecordForm input[name="attending_physician"]').val(recordData.attending_physician);
            $('#editRecordForm input[name="follow_up_date"]').val(recordData.follow_up_date);
            $('#editRecordForm textarea[name="notes"]').val(recordData.notes);
            
            // Populate vital signs
            var vitalSigns = JSON.parse(recordData.vital_signs || '{}');
            $('#editRecordForm input[name="bp_systolic"]').val(vitalSigns.bp_systolic || '');
            $('#editRecordForm input[name="bp_diastolic"]').val(vitalSigns.bp_diastolic || '');
            $('#editRecordForm input[name="heart_rate"]').val(vitalSigns.heart_rate || '');
            $('#editRecordForm input[name="temperature"]').val(vitalSigns.temperature || '');
            $('#editRecordForm input[name="weight"]').val(vitalSigns.weight || '');
            $('#editRecordForm input[name="height"]').val(vitalSigns.height || '');
            
            // Set status if exists
            if (recordData.status) {
                $('#editRecordForm select[name="status"]').val(recordData.status);
            }
            
            // Show edit modal
            $('#editRecordModal').modal('show');
        }
        
        function deleteRecord(recordId) {
            if (confirm('Are you sure you want to delete this medical record? This action cannot be undone.')) {
                // Create form for delete action
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = 'MedicalRecordsHistory.php';
                form.style.display = 'none';
                
                var actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete_record';
                
                var recordIdInput = document.createElement('input');
                recordIdInput.type = 'hidden';
                recordIdInput.name = 'record_id';
                recordIdInput.value = recordId;
                
                form.appendChild(actionInput);
                form.appendChild(recordIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
