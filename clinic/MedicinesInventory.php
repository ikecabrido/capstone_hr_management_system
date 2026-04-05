<?php
session_start();
require_once "../auth/auth_check.php";
require_once "../auth/database.php";
require_once "core/BaseModel.php";
require_once "models/MedicineInventory.php";
require_once "services/ClinicReportService.php";

$database = Database::getInstance();
$db = $database->getConnection();

if ($db === null) {
    $_SESSION['error'] = "Database connection failed.";
    header('Location: ../index.php');
    exit;
}

$medicine = new MedicineInventory($db);
$medicine->updateAllStatuses(); // Update all statuses on page load
$clinic_report = new ClinicReportService($db);

$action = $_POST['action'] ?? '';
$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['message'], $_SESSION['error']);

if ($action === 'add_medicine') {
    try {
        $medicine_id = 'MED' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        $data = [
            'medicine_id' => $medicine_id,
            'medicine_name' => $_POST['medicine_name'],
            'generic_name' => $_POST['generic_name'],
            'category' => $_POST['category'],
            'current_stock' => $_POST['current_stock'],
            'reorder_level' => $_POST['reorder_level'],
            'expiry_date' => $_POST['expiry_date'],
            'created_by' => $_SESSION['user']['name']
        ];
        
        if ($medicine->create($data)) {
            $medicine->updateStatus($medicine_id); // Update status based on new data
            $_SESSION['message'] = "Medicine added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add medicine.";
        }
        header("Location: MedicinesInventory.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: MedicinesInventory.php");
        exit;
    }
}

if ($action === 'update_medicine') {
    try {
        $medicine_id = $_POST['medicine_id'];
        $data = [
            'medicine_name' => $_POST['medicine_name'],
            'generic_name' => $_POST['generic_name'],
            'category' => $_POST['category'],
            'current_stock' => $_POST['current_stock'],
            'reorder_level' => $_POST['reorder_level'],
            'expiry_date' => $_POST['expiry_date']
        ];
        
        if ($medicine->update($medicine_id, $data)) {
            $medicine->updateStatus($medicine_id); // Update status based on updated data
            $_SESSION['message'] = "Medicine updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update medicine.";
        }
        header("Location: MedicinesInventory.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: MedicinesInventory.php");
        exit;
    }
}

if ($action === 'delete_medicine') {
    try {
        $medicine_id = $_POST['medicine_id'];
        if ($medicine->delete($medicine_id)) {
            $_SESSION['message'] = "Medicine deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete medicine.";
        }
        header("Location: MedicinesInventory.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: MedicinesInventory.php");
        exit;
    }
}

if ($action === 'add_stock') {
    try {
        $medicine_id = $_POST['medicine_id'];
        $quantity = $_POST['quantity'];
        
        if ($medicine->addStock($medicine_id, $quantity, $_SESSION['user']['name'])) {
            $medicine->updateStatus($medicine_id); // Update status after stock addition
            $_SESSION['message'] = "Stock added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add stock.";
        }
        header("Location: MedicinesInventory.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: MedicinesInventory.php");
        exit;
    }
}

$stats = $medicine->getInventoryStats();
$medicines = $medicine->read([], 'medicine_name ASC');

$theme = $_SESSION['user']['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicines Inventory - Clinic System</title>
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
                    <a href="MedicinesInventory.php" class="nav-link">Medicines</a>
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
                            <a href="MedicalRecordsHistory.php" class="nav-link">
                                <i class="nav-icon fas fa-file-medical"></i>
                                <p>Medical Records History</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="MedicinesInventory.php" class="nav-link active">
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
                            <h1 class="m-0">Medicines Inventory</h1>
                            <p class="text-muted">Manage clinic medicines with stock levels, expiry tracking, and usage logs</p>
                        </div>
                        <div class="col-sm-6">
                            <a href="generate_clinic_pdf.php?type=medicine_list" class="btn btn-success float-right ml-2" target="_blank">
                                <i class="fas fa-file-pdf"></i> Export List
                            </a>
                            <button class="btn btn-primary float-right" data-toggle="modal" data-target="#addMedicineModal">
                                <i class="fas fa-plus"></i> Add Medicine
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

                    <!-- Medicines Table -->
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" id="medicineSearch" class="form-control" placeholder="Search medicine...">
                                        <div class="input-group-append">
                                            <button class="btn btn-default"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <select id="medicineTypeFilter" class="form-control">
                                            <option value="">All Types</option>
                                            <option value="Analgesic">Analgesic</option>
                                            <option value="Antibiotic">Antibiotic</option>
                                            <option value="Antacid">Antacid</option>
                                            <option value="Antihistamine">Antihistamine</option>
                                            <option value="Vitamin">Vitamin</option>
                                            <option value="Ointment">Ointment</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover" id="medicinesTable">
                                <thead>
                                    <tr>
                                        <th>Medicine Name</th>
                                        <th>Medicine Type</th>
                                        <th>Stock</th>
                                        <th>Expiry Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($medicines as $med): ?>
                                    <tr>
                                        <td>
                                            <?= htmlspecialchars($med['medicine_name']) ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($med['medicine_id']) ?></small>
                                        </td>
                                        <td>
                                            <?php 
                                            $category = htmlspecialchars($med['category'] ?? '');
                                            $badge_class = 'secondary';
                                            if ($category === 'Ointment') $badge_class = 'info';
                                            if ($category === 'Analgesic') $badge_class = 'warning';
                                            if ($category === 'Antibiotic') $badge_class = 'danger';
                                            ?>
                                            <span class="badge badge-<?= $badge_class ?>"><?= $category ?></span>
                                        </td>
                                        <td>
                                            <span class="<?= $med['current_stock'] <= ($med['reorder_level'] ?? 10) ? 'font-weight-bold text-danger' : '' ?>">
                                                <?= number_format($med['current_stock']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $expiry = $med['expiry_date'] ?? '';
                                            $expiry_class = '';
                                            if ($expiry && strtotime($expiry) < strtotime(date('Y-m-d'))) {
                                                $expiry_class = 'text-danger font-weight-bold';
                                            } elseif ($expiry && strtotime($expiry) <= strtotime('+30 days')) {
                                                $expiry_class = 'text-warning';
                                            }
                                            ?>
                                            <span class="<?= $expiry_class ?>">
                                                <?= $expiry ? date('M d, Y', strtotime($expiry)) : 'N/A' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $status = $med['status'] ?? 'Available';
                                            $badge_class = 'success';
                                            if ($status === 'Unavailable') {
                                                $badge_class = 'danger';
                                            } elseif ($status === 'Out of Stock') {
                                                $badge_class = 'secondary';
                                            } elseif ($status === 'Low Stock') {
                                                $badge_class = 'warning';
                                            }
                                            ?>
                                            <span class="badge badge-<?= $badge_class ?>"><?= htmlspecialchars($status) ?></span>
                                        </td>
                                        <td>
                                            <a href="generate_clinic_pdf.php?type=medicine&id=<?= $med['medicine_id'] ?>" class="btn btn-sm btn-info" title="Download Info Sheet" target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <button class="btn btn-sm btn-warning" onclick='editMedicine(<?= json_encode($med) ?>)' title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteMedicine('<?= htmlspecialchars($med['medicine_id']) ?>')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <button class="btn btn-sm btn-success" onclick="addStock('<?= htmlspecialchars($med['medicine_id']) ?>')" title="Add Stock">
                                                <i class="fas fa-plus"></i>
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

    <!-- Add Medicine Modal -->
    <div class="modal fade" id="addMedicineModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New Medicine</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="MedicinesInventory.php">
                    <input type="hidden" name="action" value="add_medicine">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Medicine Name</label>
                                    <input type="text" name="medicine_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Generic Name</label>
                                    <input type="text" name="generic_name" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Medicine Type</label>
                                    <select name="category" class="form-control">
                                        <option value="">Select Type</option>
                                        <option value="Analgesic">Analgesic</option>
                                        <option value="Antibiotic">Antibiotic</option>
                                        <option value="Antacid">Antacid</option>
                                        <option value="Antihistamine">Antihistamine</option>
                                        <option value="Vitamin">Vitamin</option>
                                        <option value="Ointment">Ointment</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Current Stock</label>
                                    <input type="number" name="current_stock" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Low Stock treshold</label>
                                    <input type="number" name="reorder_level" class="form-control" value="10">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Expiry Date</label>
                                    <input type="date" name="expiry_date" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Medicine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Stock Modal -->
    <div class="modal fade" id="addStockModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Stock</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="MedicinesInventory.php">
                    <input type="hidden" name="action" value="add_stock">
                    <input type="hidden" name="medicine_id" id="add_stock_medicine_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Quantity to Add</label>
                            <input type="number" name="quantity" class="form-control" required min="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Medicine Modal -->
    <div class="modal fade" id="editMedicineModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Medicine</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="MedicinesInventory.php">
                    <input type="hidden" name="action" value="update_medicine">
                    <input type="hidden" name="medicine_id" id="edit_medicine_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Medicine Name</label>
                                    <input type="text" name="medicine_name" id="edit_medicine_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Generic Name</label>
                                    <input type="text" name="generic_name" id="edit_generic_name" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Medicine Type</label>
                                    <select name="category" id="edit_category" class="form-control">
                                        <option value="">Select Type</option>
                                        <option value="Analgesic">Analgesic</option>
                                        <option value="Antibiotic">Antibiotic</option>
                                        <option value="Antacid">Antacid</option>
                                        <option value="Antihistamine">Antihistamine</option>
                                        <option value="Vitamin">Vitamin</option>
                                        <option value="Ointment">Ointment</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Current Stock</label>
                                    <input type="number" name="current_stock" id="edit_current_stock" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Low Stock treshold</label>
                                    <input type="number" name="reorder_level" id="edit_reorder_level" class="form-control" value="10">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Expiry Date</label>
                                    <input type="date" name="expiry_date" id="edit_expiry_date" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Medicine</button>
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
    <script src="../assets/plugins/chart.js/Chart.min.js"></script>
    <script>
        function addStock(medicineId) {
            $('#add_stock_medicine_id').val(medicineId);
            $('#addStockModal').modal('show');
        }
        
        function editMedicine(medicineData) {
            var medicine = typeof medicineData === 'string' ? JSON.parse(medicineData) : medicineData;
            
            $('#edit_medicine_id').val(medicine.medicine_id);
            $('#edit_medicine_name').val(medicine.medicine_name);
            $('#edit_generic_name').val(medicine.generic_name || '');
            $('#edit_category').val(medicine.category || '');
            $('#edit_current_stock').val(medicine.current_stock);
            $('#edit_reorder_level').val(medicine.reorder_level || 10);
            $('#edit_expiry_date').val(medicine.expiry_date || '');
            
            $('#editMedicineModal').modal('show');
        }
        
        function deleteMedicine(medicineId) {
            if (confirm('Are you sure you want to delete this medicine?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'MedicinesInventory.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete_medicine';
                
                const medicineIdInput = document.createElement('input');
                medicineIdInput.type = 'hidden';
                medicineIdInput.name = 'medicine_id';
                medicineIdInput.value = medicineId;
                
                form.appendChild(actionInput);
                form.appendChild(medicineIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        $(document).ready(function() {
            function filterTable() {
                var searchValue = $("#medicineSearch").val().toLowerCase();
                var typeValue = $("#medicineTypeFilter").val().toLowerCase();

                $("#medicinesTable tbody tr").filter(function() {
                    var row = $(this);
                    var nameMatch = row.find('td:nth-child(1)').text().toLowerCase().indexOf(searchValue) > -1;
                    var typeMatch = typeValue === "" || row.find('td:nth-child(2)').text().toLowerCase().indexOf(typeValue) > -1;
                    $(this).toggle(nameMatch && typeMatch);
                });
            }

            $("#medicineSearch, #medicineTypeFilter").on("keyup change", function() {
                filterTable();
            });

            <?php if ($message): ?>
            $('#addMedicineModal').modal('hide');
            $('#editMedicineModal').modal('hide');
            $('#addStockModal').modal('hide');
            $('.modal-backdrop').remove();
            <?php endif; ?>
            
            <?php if ($error): ?>
            $('#addMedicineModal').modal('hide');
            $('#editMedicineModal').modal('hide');
            $('#addStockModal').modal('hide');
            $('.modal-backdrop').remove();
            <?php endif; ?>
        });
    </script>
</body>
</html>
