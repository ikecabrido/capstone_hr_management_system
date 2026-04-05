<?php
session_start();
require_once "../auth/auth_check.php";
require_once "../auth/database.php";
require_once "core/BaseModel.php";
require_once "models/Employee.php";
require_once "models/Patient.php";

// Initialize database and models
$database = Database::getInstance();
$db = $database->getConnection();

if ($db === null) {
    $_SESSION['error'] = "Database connection failed. Please check your database configuration.";
    header('Location: ../index.php');
    exit;
}

$employee = new Employee($db);
$patient = new Patient($db);

// Proactive database schema update for medical fields in clinic table
try {
    // Check cm_patients
    $stmt = $db->query("SHOW COLUMNS FROM cm_patients");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('medical_conditions', $columns)) {
        $db->exec("ALTER TABLE cm_patients ADD COLUMN medical_conditions TEXT AFTER allergies");
    }
    if (!in_array('current_medications', $columns)) {
        $db->exec("ALTER TABLE cm_patients ADD COLUMN current_medications TEXT AFTER medical_conditions");
    }
} catch (PDOException $e) {
    error_log("Schema update failed: " . $e->getMessage());
}

// Handle form submissions
$action = $_POST['action'] ?? '';
$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['message'], $_SESSION['error']);

if ($action === 'add_employee') {
    try {
        $employee_id = $_POST['employee_id'] ?? null;
        
        if (!$employee_id) {
            throw new Exception("Please search and select an employee first.");
        }
        
        $data = [
            'employee_id' => $employee_id,
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'middle_name' => $_POST['middle_name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'address' => $_POST['address'] ?? '',
            'department' => $_POST['department'] ?? '',
            'position' => $_POST['position'] ?? '',
            'blood_type' => $_POST['blood_type'] ?? '',
            'allergies' => $_POST['allergies'] ?? '',
            'medical_conditions' => $_POST['medical_conditions'] ?? '',
            'current_medications' => $_POST['current_medications'] ?? '',
            'emergency_contact_name' => $_POST['emergency_contact_name'] ?? '',
            'emergency_contact_phone' => $_POST['emergency_contact_phone'] ?? '',
            'created_by' => $_SESSION['user']['name']
        ];
        
        if ($employee->createEmployeeWithPatient($data)) {
            $_SESSION['message'] = "Employee successfully added to clinic records!";
        } else {
            $_SESSION['error'] = "Failed to add employee to clinic.";
        }
        header("Location: Employee_Patient.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: Employee_Patient.php");
        exit;
    }
}

if ($action === 'update_employee') {
    try {
        $employee_id = $_POST['employee_id'];
        $patient_id = 'PAT' . $employee_id;
        
        $data = [
            'blood_type' => $_POST['blood_type'] ?? '',
            'allergies' => $_POST['allergies'] ?? '',
            'medical_conditions' => $_POST['medical_conditions'] ?? '',
            'current_medications' => $_POST['current_medications'] ?? '',
            'emergency_contact_name' => $_POST['emergency_contact_name'] ?? '',
            'emergency_contact_phone' => $_POST['emergency_contact_phone'] ?? ''
        ];
        
        if ($employee->update($patient_id, $data)) {
            $_SESSION['message'] = "Medical information updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update medical information.";
        }
        header("Location: Employee_Patient.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: Employee_Patient.php");
        exit;
    }
}

if ($action === 'delete_employee') {
    try {
        $patient_id = $_POST['patient_id'];
        if ($employee->delete($patient_id)) {
            $_SESSION['message'] = "Employee record removed from clinic successfully!";
        } else {
            $_SESSION['error'] = "Failed to remove employee record.";
        }
        header("Location: Employee_Patient.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: Employee_Patient.php");
        exit;
    }
}

// Get employee statistics
$stats = $employee->getEmployeeStats();
$employees = $employee->read([], 'last_name ASC, first_name ASC');

$theme = $_SESSION['user']['theme'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management - Clinic System</title>
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
        <nav class="main-header navbar navbar-expand navbar-dark navbar-dark">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="clinic.php" class="nav-link">Home</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="Employee_Patient.php" class="nav-link">Employee Management</a>
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
                            <a href="Employee_Patient.php" class="nav-link active">
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
                            <h1 class="m-0">Employee Management</h1>
                            <p class="text-muted">Manage all school staff including teachers, administrators, and support personnel</p>
                        </div>
                        <div class="col-sm-6">
                            <a href="generate_clinic_pdf.php?type=employee_list" class="btn btn-success float-right ml-2" target="_blank">
                                <i class="fas fa-file-pdf"></i> Export List
                            </a>
                            <button class="btn btn-primary float-right" data-toggle="modal" data-target="#addEmployeeModal">
                                <i class="fas fa-search"></i> Select/Search Employee
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

                    <!-- Employees Table -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Employees List</h3>
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
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Blood type</th>
                                        <th>Allergies</th>
                                        <th>Medical Condition</th>
                                        <th>Current Medication</th>
                                        <th>Contact Name</th>
                                        <th>Contact Number</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($employees as $emp): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($emp['employee_id']) ?></td>
                                        <td><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></td>
                                        <td><?= htmlspecialchars($emp['blood_type'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($emp['allergies'] ?: 'None') ?></td>
                                        <td><?= htmlspecialchars($emp['medical_conditions'] ?: 'None') ?></td>
                                        <td><?= htmlspecialchars($emp['current_medications'] ?: 'None') ?></td>
                                        <td><?= htmlspecialchars($emp['emergency_contact_name'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($emp['emergency_contact_phone'] ?? 'N/A') ?></td>
                                        <td>
                                            <a href="generate_clinic_pdf.php?type=employee&id=<?= $emp['patient_id'] ?>" class="btn btn-sm btn-success" title="Download Medical Profile" target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <button class="btn btn-sm btn-warning" onclick="editEmployee('<?= htmlspecialchars(json_encode($emp)) ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteEmployee('<?= htmlspecialchars($emp['patient_id']) ?>', '<?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?>')">
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

    <!-- Select/Search Employee Modal -->
    <div class="modal fade" id="addEmployeeModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Select/Search Employee</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="Employee_Patient.php">
                    <input type="hidden" name="action" value="add_employee">
                    <input type="hidden" name="employee_id" id="selected_employee_id">
                    <div class="modal-body">
                        <!-- Search Box -->
                        <div class="form-group">
                            <label>Search Employee (Name or ID)</label>
                            <div class="input-group">
                                <input type="text" id="employee_search" class="form-control" placeholder="Search employee...">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                            </div>
                            <div id="search_results" class="list-group mt-2" style="max-height: 200px; overflow-y: auto; display: none;"></div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="first_name" id="selected_first_name" class="form-control" readonly required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="last_name" id="selected_last_name" class="form-control" readonly required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input type="text" name="middle_name" id="selected_middle_name" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" id="selected_email" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" id="selected_phone" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Department</label>
                                    <input type="text" name="department" id="selected_department" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Position</label>
                                    <input type="text" name="position" id="selected_position" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h5 class="mb-3">Medical Information</h5>
                        <div class="form-group">
                            <label>Blood Type</label>
                            <select name="blood_type" class="form-control">
                                <option value="">Select Blood Type</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Allergies</label>
                            <textarea name="allergies" class="form-control" rows="2" placeholder="List any known allergies..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Medical Conditions</label>
                            <textarea name="medical_conditions" class="form-control" rows="2" placeholder="List any chronic or significant medical conditions..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Current Medications</label>
                            <textarea name="current_medications" class="form-control" rows="2" placeholder="List any medications currently being taken..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Emergency Contact Name</label>
                                    <input type="text" name="emergency_contact_name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Emergency Contact Phone</label>
                                    <input type="text" name="emergency_contact_phone" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Employee to Clinic</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div class="modal fade" id="editEmployeeModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Medical Information</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="Employee_Patient.php">
                    <input type="hidden" name="action" value="update_employee">
                    <input type="hidden" name="employee_id" id="edit_employee_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="first_name" id="edit_first_name" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="last_name" id="edit_last_name" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input type="text" name="middle_name" id="edit_middle_name" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" id="edit_email" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" id="edit_phone" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Department</label>
                                    <input type="text" name="department" id="edit_department" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Position</label>
                                    <input type="text" name="position" id="edit_position" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h5 class="mb-3">Medical Information</h5>
                        <div class="form-group">
                            <label>Blood Type</label>
                            <select name="blood_type" class="form-control">
                                <option value="">Select Blood Type</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Allergies</label>
                            <textarea name="allergies" class="form-control" rows="2" placeholder="List any known allergies..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Medical Conditions</label>
                            <textarea name="medical_conditions" class="form-control" rows="2" placeholder="List any chronic or significant medical conditions..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Current Medications</label>
                            <textarea name="current_medications" class="form-control" rows="2" placeholder="List any medications currently being taken..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Emergency Contact Name</label>
                                    <input type="text" name="emergency_contact_name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Emergency Contact Phone</label>
                                    <input type="text" name="emergency_contact_phone" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Employee Modal -->
    <div class="modal fade" id="deleteEmployeeModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h4 class="modal-title">Delete Employee</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="Employee_Patient.php">
                    <input type="hidden" name="action" value="delete_employee">
                    <input type="hidden" name="patient_id" id="delete_patient_id">
                    <div class="modal-body">
                        <p>Are you sure you want to delete <strong id="delete_employee_name"></strong> from clinic records?</p>
                        <p class="text-danger"><small>This action will also remove their associated medical history.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Employee</button>
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
            // Employee Search Logic
            $('#employee_search').on('input', function() {
                let query = $(this).val();
                // Search immediately if query is not empty, adjust length check for names vs IDs
                if (query.length > 0) {
                    $.ajax({
                        url: 'api/search_employees.php',
                        method: 'GET',
                        data: { q: query },
                        success: function(data) {
                            let results = $('#search_results');
                            results.empty();
                            if (data.length > 0) {
                                data.forEach(emp => {
                                    let item = $(`<a href="#" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">${emp.full_name}</h6>
                                            <small>ID: ${emp.employee_id}</small>
                                        </div>
                                        <p class="mb-1 small">${emp.department} - ${emp.position}</p>
                                    </a>`);
                                    item.on('click', function(e) {
                                        e.preventDefault();
                                        selectEmployee(emp);
                                    });
                                    results.append(item);
                                });
                                results.show();
                            } else {
                                results.html('<div class="list-group-item">No employees found</div>');
                                results.show();
                            }
                        }
                    });
                } else {
                    $('#search_results').hide();
                }
            });

            function selectEmployee(emp) {
                $('#selected_employee_id').val(emp.employee_id);
                
                // Split full name for the form fields
                let names = emp.full_name.split(' ');
                $('#selected_first_name').val(names[0] || '');
                $('#selected_last_name').val(names.slice(1).join(' ') || '');
                $('#selected_middle_name').val(''); // Middle name usually not in search result
                
                $('#selected_email').val(emp.email || '');
                $('#selected_phone').val(emp.phone || '');
                $('#selected_department').val(emp.department || '');
                $('#selected_position').val(emp.position || '');
                
                $('#search_results').hide();
                $('#employee_search').val(emp.full_name);
            }

            // Hide results when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#employee_search, #search_results').length) {
                    $('#search_results').hide();
                }
            });

            // Table Search Logic
            $('input[name="table_search"]').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('table tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });

        function editEmployee(employeeData) {
            var employee = typeof employeeData === 'string' ? JSON.parse(employeeData) : employeeData;
            
            // Populate edit form with employee data
            $('#edit_employee_id').val(employee.employee_id);
            
            // Populate all form fields directly
            $('#edit_first_name').val(employee.first_name || '');
            $('#edit_last_name').val(employee.last_name || '');
            $('#edit_middle_name').val(employee.middle_name || '');
            $('#edit_email').val(employee.email || '');
            $('#edit_phone').val(employee.phone || employee.contact_number || '');
            $('#edit_department').val(employee.department || '');
            $('#edit_position').val(employee.position || '');
            
            $('#editEmployeeModal select[name="blood_type"]').val(employee.blood_type || '');
            $('#editEmployeeModal textarea[name="allergies"]').val(employee.allergies || '');
            $('#editEmployeeModal textarea[name="medical_conditions"]').val(employee.medical_conditions || '');
            $('#editEmployeeModal textarea[name="current_medications"]').val(employee.current_medications || '');
            $('#editEmployeeModal input[name="emergency_contact_name"]').val(employee.emergency_contact_name || '');
            $('#editEmployeeModal input[name="emergency_contact_phone"]').val(employee.emergency_contact_phone || '');
            
            // Show edit modal
            $('#editEmployeeModal').modal('show');
        }
        
        function deleteEmployee(patientId, name) {
            $('#delete_patient_id').val(patientId);
            $('#delete_employee_name').text(name);
            $('#deleteEmployeeModal').modal('show');
        }
    </script>
</body>
</html>
