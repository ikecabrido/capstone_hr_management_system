<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../../auth/database.php";

$theme = $_SESSION['user']['theme'] ?? 'light';

// Database connection
$db = Database::getInstance()->getConnection();

// Handle CRUD operations
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? $action;
    
    if ($action === 'create') {
        // Validate required fields
        if (empty($_POST['employee_id']) || empty($_POST['full_name'])) {
            $_SESSION['error'] = "Employee ID and Full Name are required fields!";
            header("Location: " . basename(__FILE__));
            exit;
        }
        
        $stmt = $db->prepare("
            INSERT INTO employees (employee_id, full_name, address, contact_number, email, department, position, date_hired, employment_status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_POST['employee_id'],
            $_POST['full_name'],
            $_POST['address'],
            $_POST['contact_number'],
            $_POST['email'],
            $_POST['department'],
            $_POST['position'],
            $_POST['date_hired'],
            $_POST['employment_status']
        ]);
        $_SESSION['success'] = "Employee added successfully!";
        header("Location: " . basename(__FILE__));
        exit;
    }
    
    if ($action === 'update' && $id) {
        // Validate required fields
        if (empty($_POST['employee_id']) || empty($_POST['full_name'])) {
            $_SESSION['error'] = "Employee ID and Full Name are required fields!";
            header("Location: " . basename(__FILE__));
            exit;
        }
        
        $stmt = $db->prepare("
            UPDATE employees 
            SET full_name = ?, address = ?, contact_number = ?, email = ?, department = ?, position = ?, date_hired = ?, employment_status = ?
            WHERE employee_id = ?
        ");
        $stmt->execute([
            $_POST['full_name'],
            $_POST['address'],
            $_POST['contact_number'],
            $_POST['email'],
            $_POST['department'],
            $_POST['position'],
            $_POST['date_hired'],
            $_POST['employment_status'],
            $id
        ]);
        $_SESSION['success'] = "Employee updated successfully!";
        header("Location: " . basename(__FILE__));
        exit;
    }
    
    if ($action === 'delete' && $id) {
        $stmt = $db->prepare("DELETE FROM employees WHERE employee_id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Employee deleted successfully!";
        header("Location: " . basename(__FILE__));
        exit;
    }
}

// Fetch single employee for editing
$employee = null;
if ($action === 'edit' && $id) {
    $stmt = $db->prepare("SELECT * FROM employees WHERE employee_id = ?");
    $stmt->execute([$id]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all employees for listing
$search = $_GET['search'] ?? '';
$department_filter = $_GET['department'] ?? '';
$status_filter = $_GET['status'] ?? '';

$query = "SELECT * FROM employees WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (full_name LIKE ? OR employee_id LIKE ? OR email LIKE ? OR department LIKE ? OR position LIKE ?)";
    $searchParam = "%$search%";
    $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
}

if ($department_filter) {
    $query .= " AND department = ?";
    $params[] = $department_filter;
}

if ($status_filter) {
    $query .= " AND employment_status = ?";
    $params[] = $status_filter;
}

$query .= " ORDER BY full_name";

$stmt = $db->prepare($query);
$stmt->execute($params);
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique departments for filter
$stmt = $db->query("SELECT DISTINCT department FROM employees ORDER BY department");
$departments = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Get unique statuses for filter
$statuses = ['Resigned', 'Terminated', 'On Leave', 'Contractual', 'Active'];

// Messages
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Employee Profile Management</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
    <!-- Theme style -->
    <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
    <link rel="stylesheet" href="../custom.css" />
    <link rel="stylesheet" href="../../layout/toast.css" />
    
    <style>
        .employee-card {
            transition: all 0.3s ease;
            border-left: 4px solid #007bff;
        }
        .employee-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .status-active { border-left-color: #28a745; }
        .status-resigned { border-left-color: #dc3545; }
        .status-terminated { border-left-color: #dc3545; }
        .status-onleave { border-left-color: #ffc107; }
        .status-contractual { border-left-color: #6c757d; }
        
        .search-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .action-buttons .btn {
            margin: 0 2px;
        }
        
        .employee-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #495057;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
    <div class="wrapper">
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__wobble" src="../../assets/pics/bcpLogo.png" alt="AdminLTELogo" height="60" width="60" />
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="../employee.php" class="nav-link">Home</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="../employee.php" class="brand-link">
                <img src="../../assets/pics/bcpLogo.png" alt="AdminLTE Logo" class="brand-image elevation-3" style="opacity: 0.9" />
                <span class="brand-text font-weight-light">BCP Bulacan</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="../../assets/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image" />
                    </div>
                    <div class="info">
                        <a href="#" class="d-block"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') ?></a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="../employee.php" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Employee Database.php" class="nav-link active">
                                <i class="nav-icon fas fa-chart-pie"></i>
                                <p>Employee Database</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Employee history.php" class="nav-link">
                                <i class="nav-icon fas fa-tree"></i>
                                <p>Employee History</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Employee Self Service.php" class="nav-link">
                                <i class="nav-icon fas fa-edit"></i>
                                <p>Employee Self Service</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Document Management.php" class="nav-link">
                                <i class="nav-icon fas fa-table"></i>
                                <p>Document Management</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Personal Information.php" class="nav-link">
                                <i class="nav-icon fas fa-user"></i>
                                <p>Personal Information</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../logout.php" class="nav-link">
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
                            <h1 class="m-0">Employee Profile Management</h1>
                        </div>
                        <div class="col-sm-6 text-sm-right">
                            <a href="?action=create" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Employee
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle mr-2"></i><?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($action === 'create' || $action === 'edit'): ?>
                        <!-- Add/Edit Employee Form -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-user-edit mr-2"></i>
                                    <?= $action === 'create' ? 'Add New Employee' : 'Edit Employee' ?>
                                </h3>
                            </div>
                            <form method="post" class="card-body">
                                <input type="hidden" name="action" value="<?= $action ?>">
                                <?php if ($action === 'edit'): ?>
                                    <input type="hidden" name="employee_id" value="<?= htmlspecialchars($employee['employee_id']) ?>">
                                <?php endif; ?>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="employee_id">Employee ID *</label>
                                            <input type="text" class="form-control" id="employee_id" name="employee_id" 
                                                   value="<?= htmlspecialchars($employee['employee_id'] ?? '') ?>" 
                                                   <?= $action === 'edit' ? 'readonly' : '' ?> required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="full_name">Full Name *</label>
                                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                                   value="<?= htmlspecialchars($employee['full_name'] ?? '') ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email *</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?= htmlspecialchars($employee['email'] ?? '') ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contact_number">Contact Number *</label>
                                            <input type="text" class="form-control" id="contact_number" name="contact_number" 
                                                   value="<?= htmlspecialchars($employee['contact_number'] ?? '') ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($employee['address'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="department">Department *</label>
                                            <select class="form-control" id="department" name="department" required>
                                                <option value="">Select Department</option>
                                                <?php foreach (['IT', 'HR', 'Finance', 'Marketing', 'Operations', 'Sales', 'Clinic'] as $dept): ?>
                                                    <option value="<?= $dept ?>" <?= (isset($employee['department']) && $employee['department'] === $dept) ? 'selected' : '' ?>>
                                                        <?= $dept ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="position">Position/Job Title *</label>
                                            <input type="text" class="form-control" id="position" name="position" 
                                                   value="<?= htmlspecialchars($employee['position'] ?? '') ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_hired">Date Hired *</label>
                                            <input type="date" class="form-control" id="date_hired" name="date_hired" 
                                                   value="<?= htmlspecialchars($employee['date_hired'] ?? '') ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="employment_status">Employment Status *</label>
                                            <select class="form-control" id="employment_status" name="employment_status" required>
                                                <?php foreach ($statuses as $status): ?>
                                                    <option value="<?= $status ?>" <?= (isset($employee['employment_status']) && $employee['employment_status'] === $status) ? 'selected' : '' ?>>
                                                        <?= $status ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-2"></i><?= $action === 'create' ? 'Add Employee' : 'Update Employee' ?>
                                        </button>
                                        <a href="Employee Database.php" class="btn btn-secondary">
                                            <i class="fas fa-times mr-2"></i>Cancel
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <!-- Search and Filter Section -->
                        <div class="search-section">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-search mr-2"></i>Search Employees</label>
                                        <input type="text" class="form-control" id="searchInput" 
                                               placeholder="Search by name, ID, email, department, or position..."
                                               value="<?= htmlspecialchars($search) ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fas fa-building mr-2"></i>Department Filter</label>
                                        <select class="form-control" id="departmentFilter">
                                            <option value="">All Departments</option>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?= htmlspecialchars($dept) ?>" <?= $dept === $department_filter ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($dept) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fas fa-user-tag mr-2"></i>Status Filter</label>
                                        <select class="form-control" id="statusFilter">
                                            <option value="">All Statuses</option>
                                            <?php foreach ($statuses as $status): ?>
                                                <option value="<?= $status ?>" <?= $status === $status_filter ? 'selected' : '' ?>>
                                                    <?= $status ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-warning btn-block" onclick="clearFilters()">
                                            <i class="fas fa-eraser mr-2"></i>Clear
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Employee List -->
                        <div class="row">
                            <?php if (empty($employees)): ?>
                                <div class="col-12">
                                    <div class="alert alert-info text-center">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        No employees found matching your criteria.
                                    </div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($employees as $emp): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card employee-card status-<?= strtolower(str_replace(' ', '', $emp['employment_status'])) ?>">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="employee-photo mr-3">
                                                        <?= strtoupper(substr($emp['full_name'], 0, 2)) ?>
                                                    </div>
                                                    <div>
                                                        <h5 class="card-title mb-1"><?= htmlspecialchars($emp['full_name']) ?></h5>
                                                        <span class="badge badge-<?= $emp['employment_status'] === 'Active' ? 'success' : 'secondary' ?>">
                                                            <?= htmlspecialchars($emp['employment_status']) ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <div class="employee-details">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <small class="text-muted">Employee ID</small>
                                                            <p class="mb-1"><strong><?= htmlspecialchars($emp['employee_id']) ?></strong></p>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">Department</small>
                                                            <p class="mb-1"><?= htmlspecialchars($emp['department']) ?></p>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <small class="text-muted">Position</small>
                                                            <p class="mb-1"><?= htmlspecialchars($emp['position']) ?></p>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">Date Hired</small>
                                                            <p class="mb-1"><?= htmlspecialchars($emp['date_hired']) ?></p>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <small class="text-muted">Contact</small>
                                                            <p class="mb-1">
                                                                <i class="fas fa-envelope mr-1"></i><?= htmlspecialchars($emp['email']) ?><br>
                                                                <i class="fas fa-phone mr-1"></i><?= htmlspecialchars($emp['contact_number']) ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    <?php if (!empty($emp['address'])): ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <small class="text-muted">Address</small>
                                                            <p class="mb-1"><?= htmlspecialchars($emp['address']) ?></p>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="action-buttons mt-3">
                                                    <a href="?action=edit&id=<?= urlencode($emp['employee_id']) ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?action=delete&id=<?= urlencode($emp['employee_id']) ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this employee?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <script src="../../assets/dist/js/adminlte.js"></script>
    
    <script>
        // Search and filter functionality
        $(document).ready(function() {
            // Auto-submit search on input change with delay
            let searchTimeout;
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    applyFilters();
                }, 500);
            });
            
            $('#departmentFilter, #statusFilter').on('change', function() {
                applyFilters();
            });
        });
        
        function applyFilters() {
            const search = $('#searchInput').val();
            const department = $('#departmentFilter').val();
            const status = $('#statusFilter').val();
            
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (department) params.append('department', department);
            if (status) params.append('status', status);
            
            window.location.href = 'Employee Database.php?' + params.toString();
        }
        
        function clearFilters() {
            window.location.href = 'Employee Database.php';
        }
        
        // Show success/error messages as toast notifications
        <?php if ($success): ?>
            $(document).ready(function() {
                showNotification('success', '<?= htmlspecialchars($success) ?>');
            });
        <?php endif; ?>
        
        <?php if ($error): ?>
            $(document).ready(function() {
                showNotification('error', '<?= htmlspecialchars($error) ?>');
            });
        <?php endif; ?>
    </script>
</body>
</html>