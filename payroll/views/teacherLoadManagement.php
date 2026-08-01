<?php
require_once __DIR__ . '/../../auth/auth.php';
require_once __DIR__ . '/../models/payrollModel.php';
require_once __DIR__ . '/../../auth/auth_check.php';

$theme = $_SESSION['user']['theme'] ?? 'light';
$user = $auth->user();
$payrollModel = new PayrollModel(Database::getInstance()->getConnection());

// Get only teaching-related employees for the dropdown
$employees = $payrollModel->getTeacherEmployees();

// Get existing teacher loads
$db = Database::getInstance()->getConnection();
$stmt = $db->query("
    SELECT 
        tl.id,
        e.full_name,
        e.employee_id,
        tl.academic_year,
        tl.semester,
        tl.qualification,
        tl.total_units,
        tl.created_by,
        tl.approved_by
    FROM pr_teacher_loads tl
    JOIN employees e ON tl.employee_id = e.employee_id
    ORDER BY tl.academic_year DESC, tl.semester DESC
");
$teacherLoads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Teacher Load Management - College Coordinator</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../custom.css" />
    <link rel="stylesheet" href="../../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
</head>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__wobble" src="../../assets/pics/bcpLogo.png" alt="Logo" height="60" width="60" />
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="../payroll.php" class="nav-link">Home</a>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="payroll.html" class="brand-link">
                <img src="../../assets/pics/bcpLogo.png" alt="Logo" class="brand-image elevation-3" style="opacity: 0.9" />
                <span class="brand-text font-weight-light">BCP Bulacan</span>
            </a>
            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="info">
                        <a href="#" class="d-block">College Coordinator <?= htmlspecialchars($_SESSION['user']['name'] ?? 'User') ?></a>
                    </div>
                </div>

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="../payroll.php" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="teacherLoadManagement.php" class="nav-link active">
                                <i class="nav-icon fas fa-book-open"></i>
                                <p>Teacher Loads</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../../auth/logout.php" class="nav-link">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Teacher Load Management</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Add New Teacher Load Form -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-plus"></i> Add Teacher Load (Semester Assignment)</h3>
                        </div>
                        <form method="POST" action="teacherLoadHandler.php" class="teacher-load-form">
                            <input type="hidden" name="action" value="add_load">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Teacher Name <span class="text-danger">*</span></label>
                                            <select name="employee_id" class="form-control" required>
                                                <option value="">-- Select Teacher --</option>
                                                <?php foreach ($employees as $emp): ?>
                                                    <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Academic Year <span class="text-danger">*</span></label>
                                            <input type="text" name="academic_year" class="form-control" placeholder="e.g., 2025-2026" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Semester <span class="text-danger">*</span></label>
                                            <select name="semester" class="form-control" required>
                                                <option value="">-- Select Semester --</option>
                                                <option value="1st">1st Semester</option>
                                                <option value="2nd">2nd Semester</option>
                                                <option value="Summer">Summer</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Teacher Qualification <span class="text-danger">*</span></label>
                                            <select name="qualification" class="form-control" required>
                                                <option value="">-- Select Qualification --</option>
                                                <option value="ProfEd">ProfEd (₱128/unit)</option>
                                                <option value="LPT">LPT (₱130/unit)</option>
                                                <option value="Masteral">Masteral (₱250/unit)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Total Units <span class="text-danger">*</span></label>
                                            <input type="number" name="total_units" class="form-control" step="0.5" placeholder="e.g., 30" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="notes" class="form-label">Notes (Optional)</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Add any notes about this assignment..."></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Load Assignment
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Clear
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Teacher Loads List -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-list"></i> Teacher Load Assignments</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Teacher Name</th>
                                        <th>Academic Year</th>
                                        <th>Semester</th>
                                        <th>Qualification</th>
                                        <th>Total Units</th>
                                        <th>Payroll Per 1/2 Month</th>
                                        <th>Created By</th>
                                        <th>Approved By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($teacherLoads as $load): ?>
                                        <?php
                                        $qualRates = ['ProfEd' => 128, 'LPT' => 130, 'Masteral' => 250];
                                        $payPerUnit = $qualRates[$load['qualification']] ?? 128;
                                        $payrollAmount = ($load['total_units'] * $payPerUnit) / 2;
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($load['full_name']) ?></td>
                                            <td><?= htmlspecialchars($load['academic_year']) ?></td>
                                            <td>
                                                <span class="badge badge-info"><?= htmlspecialchars($load['semester']) ?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-success"><?= htmlspecialchars($load['qualification']) ?></span>
                                            </td>
                                            <td><?= number_format($load['total_units'], 2) ?></td>
                                            <td>₱<?= number_format($payrollAmount, 2) ?></td>
                                            <td><?= htmlspecialchars($load['created_by'] ?? '-') ?></td>
                                            <td>
                                                <?= $load['approved_by'] ? 
                                                    '<span class="badge badge-success">✓ ' . htmlspecialchars($load['approved_by']) . '</span>' : 
                                                    '<span class="badge badge-warning">Pending</span>' 
                                                ?>
                                            </td>
                                            <td>
                                                <form method="POST" action="teacherLoadHandler.php" style="display:inline;">
                                                    <input type="hidden" name="action" value="delete_load">
                                                    <input type="hidden" name="load_id" value="<?= $load['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this assignment?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php if (empty($teacherLoads)): ?>
                                <div class="text-center p-4">
                                    <p class="text-muted">No teacher load assignments yet.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> How Teacher Payroll Works</h5>
                        <ul>
                            <li>Each semester, assign teaching units and qualifications to teachers</li>
                            <li>Payroll uses these assignments to calculate monthly pay</li>
                            <li>Formula: <strong>(Units × Rate per Unit) ÷ 2</strong> = Semi-monthly amount</li>
                            <li>Rates: ProfEd ₱128/unit | LPT ₱130/unit | Masteral ₱250/unit</li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/dist/js/adminlte.min.js"></script>
    <script src="../../assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Show success/error messages if present
            <?php if (isset($_GET['success'])): ?>
                alert('✓ Teacher load assignment <?= htmlspecialchars($_GET['success']) ?>');
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                alert('✗ Error: <?= htmlspecialchars($_GET['error']) ?>');
            <?php endif; ?>
        });
    </script>
</body>
</html>
