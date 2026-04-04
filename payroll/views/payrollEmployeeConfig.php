<?php
session_start();
require_once __DIR__ . "/../../auth/database.php";
require_once __DIR__ . "/../../auth/auth.php";
require_once __DIR__ . "/../../auth/auth_check.php";
require_once __DIR__ . "/../controllers/payrollEmployeeConfigController.php";

// $theme = $_SESSION['user']['theme'] ?? 'light';
$db = Database::getInstance()->getConnection();
$controller = new PayrollEmployeeConfigController();

$employees = $controller->getAllEmployees();
$referenceData = $controller->getReferenceData();
$summary = $referenceData['summary'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Payroll Employee Configuration</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css" />
    <link rel="stylesheet" href="payroll-config.css" />
    <link rel="stylesheet" href="../layout/toast.css" />
</head>

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__wobble" src="../assets/pics/bcpLogo.png" alt="BCP Logo" height="60" width="60" />
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="payroll.php" class="nav-link">Back to Payroll</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <div class="nav-link" id="clock">--:--:--</div>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="payroll.php" class="brand-link">
                <img src="../assets/pics/bcpLogo.png" alt="BCP Logo" class="brand-image elevation-3" style="opacity: 0.9" />
                <span class="brand-text font-weight-light">BCP Payroll</span>
            </a>
            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="info">
                        <a href="#" class="d-block"><?= htmlspecialchars($_SESSION['user']['name']) ?></a>
                    </div>
                </div>
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="payroll.php" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="views/payrollEmployeeConfig.php" class="nav-link active">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>Employee Configuration</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Payroll Employee Configuration</h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">
                    <!-- Summary Statistics -->
                    <div class="summary-stats">
                        <div class="stat-box">
                            <h4>Admin Staff</h4>
                            <div class="number"><?= $summary['admin_count'] ?? 0 ?></div>
                        </div>
                        <div class="stat-box">
                            <h4>Teachers</h4>
                            <div class="number"><?= $summary['teacher_count'] ?? 0 ?></div>
                        </div>
                        <div class="stat-box">
                            <h4>SSS Enrolled</h4>
                            <div class="number"><?= $summary['sss_count'] ?? 0 ?></div>
                        </div>
                        <div class="stat-box">
                            <h4>PhilHealth</h4>
                            <div class="number"><?= $summary['philhealth_count'] ?? 0 ?></div>
                        </div>
                        <div class="stat-box">
                            <h4>Pag-IBIG</h4>
                            <div class="number"><?= $summary['pagibig_count'] ?? 0 ?></div>
                        </div>
                    </div>

                    <!-- Employees List -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Employee Payroll Configuration</h3>
                            <div class="card-tools">
                                <input type="text" class="form-control form-control-sm" id="searchEmployee" placeholder="Search employee...">
                            </div>
                        </div>
                        <div class="card-body" id="employeesList">
                            <?php foreach ($employees as $emp): ?>
                                <div class="employee-row employee-search-item">
                                    <div class="employee-info">
                                        <div class="employee-name">
                                            <?= htmlspecialchars($emp['full_name']) ?>
                                            <span class="badge-position <?= $emp['position_type'] === 'Teacher' ? 'badge-teacher' : 'badge-admin' ?>">
                                                <?= htmlspecialchars($emp['position_type']) ?>
                                            </span>
                                        </div>
                                        <div class="employee-meta">
                                            <strong>ID:</strong> <?= htmlspecialchars($emp['employee_id']) ?> |
                                            <strong>Position:</strong> <?= htmlspecialchars($emp['position']) ?> |
                                            <strong>Department:</strong> <?= htmlspecialchars($emp['department']) ?>
                                        </div>
                                        <div class="employee-meta" style="margin-top: 8px;">
                                            <strong>Salary:</strong> ₱<?= number_format($emp['base_salary'], 2) ?> |
                                            <strong>Trio:</strong>
                                            <span class="status-indicator <?= $emp['has_sss'] ? 'status-active' : 'status-inactive' ?>;">SSS <?= $emp['has_sss'] ? '✓' : '✗' ?></span>
                                            <span class="status-indicator <?= $emp['has_philhealth'] ? 'status-active' : 'status-inactive' ?>;">PhilHealth <?= $emp['has_philhealth'] ? '✓' : '✗' ?></span>
                                            <span class="status-indicator <?= $emp['has_pagibig'] ? 'status-active' : 'status-inactive' ?>;">Pag-IBIG <?= $emp['has_pagibig'] ? '✓' : '✗' ?></span>
                                        </div>
                                        <?php if ($emp['position_type'] === 'Teacher'): ?>
                                            <div class="employee-meta teacher-detail">
                                                <strong>Qualification:</strong> <?= htmlspecialchars($emp['teacher_qualification']) ?> |
                                                <strong>Units:</strong> <?= $emp['teaching_units'] ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="employee-actions">
                                        <button class="btn btn-sm btn-primary btn-config" onclick="editEmployee('<?= $emp['employee_id'] ?>')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-config" onclick="deleteEmployee('<?= $emp['employee_id'] ?>', '<?= htmlspecialchars($emp['full_name']) ?>')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Configure Employee Payroll</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="configForm">
                        <input type="hidden" id="employee_id" name="employee_id" />
                        <input type="hidden" name="action" value="save" />

                        <!-- Employee Display Info -->
                        <div class="form-section">
                            <h5>Employee Information</h5>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Employee ID</label>
                                    <input type="text" class="form-control" id="emp_id_display" disabled />
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Full Name</label>
                                    <input type="text" class="form-control" id="emp_name_display" disabled />
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Position</label>
                                    <input type="text" class="form-control" id="emp_position_display" disabled />
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Department</label>
                                    <input type="text" class="form-control" id="emp_dept_display" disabled />
                                </div>
                            </div>
                        </div>

                        <!-- Payroll Details -->
                        <div class="form-section">
                            <h5>Payroll Details</h5>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="base_salary">Base Salary (Monthly) *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">₱</span>
                                        </div>
                                        <input type="number" class="form-control" id="base_salary" name="base_salary" step="0.01" min="0" required />
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="position_type">Position Type *</label>
                                    <select class="form-control" id="position_type" name="position_type" required onchange="updatePositionType()">
                                        <option value="Admin">Admin Staff</option>
                                        <option value="Teacher">Teacher/Professor/Instructor</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Teacher Info (Hidden by default) -->
                            <div id="teacherSection" class="teacher-info">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="teacher_qualification">Qualification *</label>
                                        <select class="form-control" id="teacher_qualification" name="teacher_qualification">
                                            <option value="ProfEd">ProfEd/Normal Teacher (₱128/unit)</option>
                                            <option value="LPT">Licensed Professional Teacher (₱130/unit)</option>
                                            <option value="Masteral">Masteral (₱250/unit)</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="teaching_units">Teaching Units *</label>
                                        <input type="number" class="form-control" id="teaching_units" name="teaching_units" step="0.5" min="0" placeholder="e.g., 30" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Trio Deductions -->
                        <div class="form-section">
                            <h5>Trio Contributions (from Legal & Compliance)</h5>
                            <p>Check which social security benefits this employee is enrolled in</p>
                            <div class="benefits-grid">
                                <div class="benefit-item">
                                    <input type="checkbox" id="has_sss" name="has_sss" value="1" />
                                    <label for="has_sss">
                                        <strong>SSS</strong><br />
                                        <span>Social Security</span>
                                    </label>
                                </div>
                                <div class="benefit-item">
                                    <input type="checkbox" id="has_philhealth" name="has_philhealth" value="1" />
                                    <label for="has_philhealth">
                                        <strong>PhilHealth</strong><br />
                                        <span>Health Insurance</span>
                                    </label>
                                </div>
                                <div class="benefit-item">
                                    <input type="checkbox" id="has_pagibig" name="has_pagibig" value="1" />
                                    <label for="has_pagibig">
                                        <strong>Pag-IBIG</strong><br />
                                        <span>Housing Fund</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Deduction Summary -->
                        <div class="form-section">
                            <h5>Deduction Rates (Reference)</h5>
                            <p>Based on position type</p>
                            <div class="alert alert-info" id="deductionInfo">
                                <strong>Admin Staff:</strong><br />
                                &nbsp;&nbsp;• Absence: ₱1,020 per day<br />
                                &nbsp;&nbsp;• Late: ₱2/minute or ₱120/hour<br />
                                <hr>
                                <strong>Teacher:</strong><br />
                                &nbsp;&nbsp;• Absence: ₱1,536 per day<br />
                                &nbsp;&nbsp;• Late: ₱2/minute or ₱120/hour
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveEmployee()">Save Configuration</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/dist/js/adminlte.min.js"></script>
    <script src="../layout/toast.js"></script>

    <script>
        function editEmployee(employeeId) {
            $.ajax({
                url: 'controllers/payrollEmployeeConfigController.php',
                type: 'POST',
                data: {
                    action: 'get_employee',
                    employee_id: employeeId
                },
                dataType: 'json',
                success: function(response) {
                    const emp = response.employee;
                    $('#employee_id').val(emp.employee_id);
                    $('#emp_id_display').val(emp.employee_id);
                    $('#emp_name_display').val(emp.full_name);
                    $('#emp_position_display').val(emp.position);
                    $('#emp_dept_display').val(emp.department);
                    $('#base_salary').val(emp.base_salary);
                    $('#position_type').val(emp.position_type);
                    $('#teacher_qualification').val(emp.teacher_qualification);
                    $('#teaching_units').val(emp.teaching_units);
                    $('#has_sss').prop('checked', emp.has_sss == 1);
                    $('#has_philhealth').prop('checked', emp.has_philhealth == 1);
                    $('#has_pagibig').prop('checked', emp.has_pagibig == 1);

                    updatePositionType();
                    $('#editModal').modal('show');
                }
            });
        }

        function updatePositionType() {
            const type = $('#position_type').val();
            if (type === 'Teacher') {
                $('#teacherSection').addClass('show');
                $('#base_salary').prop('disabled', true);
                $('#base_salary').attr('title', 'For teachers, salary is calculated from units × rate per unit ÷ 2');
            } else {
                $('#teacherSection').removeClass('show');
                $('#base_salary').prop('disabled', false);
            }
        }

        function saveEmployee() {
            const formData = new FormData($('#configForm')[0]);
            const data = Object.fromEntries(formData);
            data.action = 'save';

            $.ajax({
                url: 'controllers/payrollEmployeeConfigController.php',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showToast('Success', response.message, 'success');
                        $('#editModal').modal('hide');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('Error', response.message, 'error');
                    }
                }
            });
        }

        function deleteEmployee(employeeId, name) {
            if (!confirm(`Delete payroll configuration for ${name}?`)) return;

            $.ajax({
                url: 'controllers/payrollEmployeeConfigController.php',
                type: 'POST',
                data: {
                    action: 'delete',
                    employee_id: employeeId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showToast('Success', response.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('Error', response.message, 'error');
                    }
                }
            });
        }

        $('#searchEmployee').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.employee-search-item').each(function() {
                const text = $(this).text().toLowerCase();
                $(this).toggle(text.includes(searchTerm));
            });
        });

        // Update clock
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString();
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>

</html>