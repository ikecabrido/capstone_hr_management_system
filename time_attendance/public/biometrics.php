<?php
require_once '../app/controllers/AuthController.php';
require_once '../app/core/Session.php';
require_once '../app/models/BiometricModule.php';
require_once '../../auth/database.php';

Session::start();

if (!AuthController::isAuthenticated()) {
    header('Location: ../../login_form.php');
    exit;
}

if (!AuthController::hasRole('time')) {
    header('Location: employee_dashboard.php');
    exit;
}

$database = Database::getInstance();
$db = $database->getConnection();
$biometricModule = new BiometricModule($db);
$biometricModule->ensureTables();

$employees = $db->query("SELECT employee_id, employee_no, full_name, department, position FROM employees WHERE employment_status = 'Active' ORDER BY full_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$devices = $biometricModule->getDevices();
$enrollments = $biometricModule->getEnrollments();
$recentLogs = $biometricModule->getRecentLogs(10);
$stats = $biometricModule->getStats();

$current_page = 'biometrics.php';
$current_role = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'time';

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if ($_POST['action'] === 'register_device') {
            $deviceId = $biometricModule->createDevice([
                'device_name' => trim($_POST['device_name'] ?? ''),
                'device_type' => trim($_POST['device_type'] ?? 'fingerprint'),
                'manufacturer' => trim($_POST['manufacturer'] ?? 'NGTeco'),
                'model' => trim($_POST['model'] ?? 'Unknown'),
                'serial_number' => trim($_POST['serial_number'] ?? ''),
                'ip_address' => trim($_POST['ip_address'] ?? ''),
                'port' => trim($_POST['port'] ?? '4370'),
                'mac_address' => trim($_POST['mac_address'] ?? ''),
                'location' => trim($_POST['location'] ?? ''),
                'status' => trim($_POST['status'] ?? 'active'),
                'capacity' => trim($_POST['capacity'] ?? '500'),
                'firmware_version' => trim($_POST['firmware_version'] ?? ''),
                'notes' => trim($_POST['notes'] ?? ''),
            ]);
            $successMessage = 'Biometric device registered successfully.';
        } elseif ($_POST['action'] === 'register_enrollment') {
            $employeeId = (int) ($_POST['employee_id'] ?? 0);
            $deviceId = (int) ($_POST['device_id'] ?? 0);
            if ($employeeId > 0 && $deviceId > 0) {
                $biometricModule->createEnrollment([
                    'employee_id' => $employeeId,
                    'device_id' => $deviceId,
                    'biometric_id' => trim($_POST['biometric_id'] ?? ''),
                    'enrollment_type' => trim($_POST['enrollment_type'] ?? 'fingerprint'),
                    'finger_position' => trim($_POST['finger_position'] ?? 'Right Thumb'),
                    'quality_score' => trim($_POST['quality_score'] ?? 0),
                    'enrollment_status' => trim($_POST['enrollment_status'] ?? 'pending'),
                    'notes' => trim($_POST['notes'] ?? ''),
                ]);
                $successMessage = 'Biometric enrollment record created.';
            } else {
                throw new Exception('Please select an employee and a device.');
            }
        } elseif ($_POST['action'] === 'test_connection') {
            $test = $biometricModule->testConnection($_POST['test_ip'] ?? '', $_POST['test_port'] ?? 4370);
            if ($test['success']) {
                $successMessage = $test['message'];
            } else {
                $errorMessage = $test['message'];
            }
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biometric Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/dashboard.css">
    <link rel="stylesheet" href="../assets/adminlte-overrides.css">
    <style>
        body { background: #f4f6f9; }
        .content-wrapper { padding: 24px; padding-top: 80px; }
        .card { border-radius: 12px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08); }
        .stat-box { border-left: 4px solid #1976d2; }
        .badge-success { background: #2e7d32; color: white; }
        .badge-warning { background: #f57f17; color: white; }
        .badge-info { background: #1565c0; color: white; }
        .table th { background: #f8fafc; }
        @media (max-width: 768px) { .content-wrapper { margin-left: 0; } }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
<?php require_once '../app/components/Sidebar.php'; ?>
<div class="main-content">
    <div class="content-wrapper">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="mb-1"><i class="fas fa-fingerprint"></i> Biometric Registration</h2>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stat-box">
                    <div class="card-body">
                        <h6 class="text-muted">Registered Devices</h6>
                        <h3><?php echo $stats['devices']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stat-box">
                    <div class="card-body">
                        <h6 class="text-muted">Active Enrollments</h6>
                        <h3><?php echo $stats['enrollments']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stat-box">
                    <div class="card-body">
                        <h6 class="text-muted">Pending Logs</h6>
                        <h3><?php echo $stats['pending_logs']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stat-box">
                    <div class="card-body">
                        <h6 class="text-muted">Logs Today</h6>
                        <h3><?php echo $stats['today_logs']; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0"><i class="fas fa-network-wired"></i> Register Device</h5></div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="action" value="register_device">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Device Name</label>
                                    <input type="text" name="device_name" class="form-control" required placeholder="Main Entrance NGTeco">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Device Type</label>
                                    <input type="text" name="device_type" class="form-control" value="fingerprint">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Manufacturer</label>
                                    <input type="text" name="manufacturer" class="form-control" value="NGTeco">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Model</label>
                                    <input type="text" name="model" class="form-control" placeholder="Unknown">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>IP Address</label>
                                    <input type="text" name="ip_address" class="form-control" placeholder="192.168.1.100">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Port</label>
                                    <input type="number" name="port" class="form-control" value="4370">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Location</label>
                                    <input type="text" name="location" class="form-control" placeholder="Main Entrance">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Capacity</label>
                                    <input type="number" name="capacity" class="form-control" value="500">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Firmware Version</label>
                                    <input type="text" name="firmware_version" class="form-control" placeholder="1.0.0">
                                </div>
                                <div class="col-12 mb-3">
                                    <label>Notes</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Ethernet-connected device, planned real-time sync"></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Device</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0"><i class="fas fa-plug"></i> Network Test</h5></div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="action" value="test_connection">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>IP Address</label>
                                    <input type="text" name="test_ip" class="form-control" placeholder="192.168.1.100">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Port</label>
                                    <input type="number" name="test_port" class="form-control" value="4370">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-outline-primary"><i class="fas fa-satellite-dish"></i> Test Connection</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0"><i class="fas fa-id-badge"></i> Enroll Employee</h5></div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="action" value="register_enrollment">
                            <div class="mb-3">
                                <label>Employee</label>
                                <select name="employee_id" class="form-control" required>
                                    <option value="">Select Employee</option>
                                    <?php foreach ($employees as $employee): ?>
                                        <option value="<?php echo (int) $employee['employee_id']; ?>"><?php echo htmlspecialchars($employee['full_name'] . ' (' . $employee['employee_no'] . ')'); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Device</label>
                                <select name="device_id" class="form-control" required>
                                    <option value="">Select Device</option>
                                    <?php foreach ($devices as $device): ?>
                                        <option value="<?php echo (int) $device['id']; ?>"><?php echo htmlspecialchars($device['device_name'] . ' - ' . $device['location']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Biometric ID</label>
                                    <input type="text" name="biometric_id" class="form-control" placeholder="F001">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Finger Position</label>
                                    <input type="text" name="finger_position" class="form-control" value="Right Thumb">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Quality Score</label>
                                    <input type="number" step="0.01" name="quality_score" class="form-control" value="90">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Status</label>
                                    <select name="enrollment_status" class="form-control">
                                        <option value="pending">Pending</option>
                                        <option value="enrolled">Enrolled</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Notes</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Enrollment note or scanner feedback"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success"><i class="fas fa-user-plus"></i> Save Enrollment</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0"><i class="fas fa-history"></i> Recent Biometric Logs</h5></div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr><th>Employee</th><th>Device</th><th>Status</th><th>Time</th></tr>
                            </thead>
                            <tbody>
                                <?php if ($recentLogs): foreach ($recentLogs as $log): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($log['full_name'] ?? 'Unmatched'); ?></td>
                                        <td><?php echo htmlspecialchars($log['device_name'] ?? '-'); ?></td>
                                        <td><span class="badge badge-<?php echo $log['status'] === 'verified' ? 'success' : ($log['status'] === 'pending' ? 'warning' : 'info'); ?>"><?php echo htmlspecialchars($log['status']); ?></span></td>
                                        <td><?php echo htmlspecialchars($log['log_datetime'] ?? $log['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="4" class="text-center text-muted py-3">No logs yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0"><i class="fas fa-list"></i> Enrollments</h5></div>
                    <div class="card-body p-0">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr><th>Employee</th><th>Employee #</th><th>Device</th><th>Biometric ID</th><th>Status</th><th>Quality</th><th>Created</th></tr>
                            </thead>
                            <tbody>
                                <?php if ($enrollments): foreach ($enrollments as $entry): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($entry['full_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($entry['employee_no'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($entry['device_name'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($entry['biometric_id'] ?? '-'); ?></td>
                                        <td><span class="badge badge-info"><?php echo htmlspecialchars($entry['enrollment_status']); ?></span></td>
                                        <td><?php echo htmlspecialchars($entry['quality_score']); ?></td>
                                        <td><?php echo htmlspecialchars($entry['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="7" class="text-center text-muted py-3">No biometric registrations yet.</td></tr>
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
</body>
</html>
