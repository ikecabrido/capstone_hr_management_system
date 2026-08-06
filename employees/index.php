<?php
session_start();
require_once '../auth/Auth.php';
require_once '../auth/database.php';

$auth = new Auth();
if (!$auth->check()) {
    header('Location: ../login_form.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$employees = [];
$error = '';

try {
    $stmt = $db->query("
        SELECT 
            employee_id,
            employee_no,
            full_name,
            email,
            contact_number,
            department,
            position,
            date_hired,
            employment_status,
            birthdate,
            sex,
            marital_status
        FROM employees
        ORDER BY full_name ASC
    ");
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $error = 'Failed to load employees: ' . $e->getMessage();
    error_log($error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees - HR Management System</title>
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
    <style>
        .search-box { margin-bottom: 20px; }
        .search-box input { width: 300px; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; }
        .employee-count { font-size: 14px; color: #666; margin-bottom: 10px; }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include '../legal_compliance/components/topbar.php'; ?>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Employees</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../router.php">Home</a></li>
                            <li class="breadcrumb-item active">Employees</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Employee Directory</h3>
                        <div class="card-tools">
                            <input type="text" id="employeeSearch" class="form-control" placeholder="Search employees...">
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <div class="employee-count">
                            Showing <?php echo count($employees); ?> employee<?php echo count($employees) !== 1 ? 's' : ''; ?>
                        </div>

                        <?php if (empty($employees)): ?>
                            <div class="alert alert-warning">No employees found.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="employeesTable">
                                    <thead>
                                        <tr>
                                            <th>Employee ID</th>
                                            <th>Employee No</th>
                                            <th>Full Name</th>
                                            <th>Department</th>
                                            <th>Position</th>
                                            <th>Email</th>
                                            <th>Contact</th>
                                            <th>Status</th>
                                            <th>Date Hired</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($employees as $emp): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($emp['employee_id']); ?></td>
                                            <td><?php echo htmlspecialchars($emp['employee_no']); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($emp['full_name']); ?></strong>
                                                <?php if (!empty($emp['birthdate'])): ?>
                                                    <br><small class="text-muted">DOB: <?php echo htmlspecialchars($emp['birthdate']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($emp['department'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($emp['position'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($emp['email'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($emp['contact_number'] ?? '-'); ?></td>
                                            <td>
                                                <?php $status = $emp['employment_status'] ?? 'Unknown'; ?>
                                                <span class="badge <?php echo $status === 'Regular' ? 'badge-success' : ($status === 'Probationary' ? 'badge-warning' : 'badge-secondary'); ?>">
                                                    <?php echo htmlspecialchars($status); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($emp['date_hired'] ?? '-'); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/dist/js/adminlte.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#employeeSearch').on('input', function() {
                const query = $(this).val().toLowerCase();
                $('#employeesTable tbody tr').each(function() {
                    const text = $(this).text().toLowerCase();
                    $(this).toggle(text.includes(query));
                });
            });
        });
    </script>
</div>
</body>
</html>
