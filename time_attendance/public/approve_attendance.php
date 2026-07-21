<?php
/**
 * Attendance Approval Page - Time & Attendance System
 * Review and approve pending manual attendance entries
 */

require_once "../app/controllers/AuthController.php";
require_once "../app/models/Attendance.php";
require_once "../app/helpers/Helper.php";
require_once "../app/helpers/AuditLog.php";
require_once "../app/core/Session.php";

Session::start();

// Check if user is authenticated
if (!AuthController::isAuthenticated()) {
    header("Location: ../../login_form.php");
    exit;
}

// Only HR can access this page
if (!AuthController::hasRole('time')) {
    header("Location: employee_dashboard.php");
    exit;
}

$attendanceModel = new Attendance();
$auditLog = new AuditLog();
$user_id = AuthController::getCurrentUserId();

$message = "";
$messageType = "";

// Handle approval
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = trim($_POST['action'] ?? '');
    $attendance_id = (int)($_POST['attendance_id'] ?? 0);
    $remarks = trim($_POST['remarks'] ?? '');

    if ($action === 'approve' && $attendance_id > 0) {
        if ($attendanceModel->approve($attendance_id, $user_id, $remarks)) {
            $message = "Attendance record approved successfully!";
            $messageType = "success";
            $auditLog->log('ATTENDANCE_APPROVED', $user_id, null, $attendance_id, 
                ['remarks' => $remarks], 'SUCCESS');
        } else {
            $message = "Failed to approve attendance record.";
            $messageType = "error";
        }
    }
}

// Get all pending approvals
$pendingApprovals = $attendanceModel->getPendingApprovals(1000);

$current_page = 'approve_attendance.php';
$current_role = $_SESSION['role'] ?? 'HR_ADMIN';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Manual Time - Time & Attendance System</title>
    <link rel="icon" href="../Bestlink College of the Philippines.jpeg" type="image/jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/dashboard.css">
    <link rel="stylesheet" href="../../payroll/custom.css">
    <link rel="stylesheet" href="../assets/adminlte-overrides.css">
    <script src="../assets/mobile-responsive.js" defer></script>
<style>
        body.dark-mode .container {
            background: rgba(30, 30, 30, 0.85);
            color: #e0e0e0;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .approvals-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
        }
        body.dark-mode .approvals-table {
            background: #1e1e1e;
            color: #e0e0e0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }
        .approvals-table th {
            background: linear-gradient(135deg, #003d82 0%, #0066cc 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        body.dark-mode .approvals-table th {
            background: linear-gradient(135deg, #003d82 0%, #0066cc 100%);
        }
        .approvals-table td {
            padding: 15px;
            border-bottom: 1px solid #e8eef7;
        }
        body.dark-mode .approvals-table td {
            border-color: #404040;
        }
        .approvals-table tr:hover {
            background: #f9fbfd;
        }
        body.dark-mode .approvals-table tr:hover {
            background: #2a2a2a;
        }
        .action-btn {
            padding: 8px 14px;
            margin: 3px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .btn-approve {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            color: white;
            border: 1px solid #229954;
        }
        .btn-approve:hover {
            background: linear-gradient(135deg, #229954 0%, #1e8449 100%);
        }
        body.dark-mode .action-btn {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
        }
        body.dark-mode .action-btn:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1500;
            justify-content: center;
            align-items: center;
        }
        .modal.active {
            display: flex;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }
        body.dark-mode .modal-content {
            background: #1e1e1e;
            color: #e0e0e0;
        }
        .modal-close {
            float: right;
            cursor: pointer;
            font-size: 28px;
            font-weight: bold;
            color: #999;
            margin-top: -10px;
            margin-right: -10px;
        }
        .modal-close:hover {
            color: #333;
        }
        body.dark-mode .modal-close:hover {
            color: #e0e0e0;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        body.dark-mode .alert-success {
            background: #1e4a3b;
            color: #9fd9c7;
            border-color: #0d3a2a;
        }
        body.dark-mode .alert-error {
            background: #4a1f1f;
            color: #e9a8a8;
            border-color: #6b2f2f;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 10px 0;
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }
        body.dark-mode textarea {
            background: #2a2a2a;
            color: #e0e0e0;
            border-color: #404040;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .info-box {
            padding: 12px;
            background: #f5f5f5;
            border-left: 4px solid #3498db;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        body.dark-mode .info-box {
            background: #2a2a2a;
            border-color: #5DADE2;
        }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }

        .page-header {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 61, 130, 0.15);
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .page-title i {
            font-size: 36px;
            opacity: 0.95;
        }

        .page-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
            margin: 8px 0 0 0;
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div
      class="preloader flex-column justify-content-center align-items-center">
      <img
        class="animation__wobble"
        src="../../assets/pics/bcpLogo.png"
        alt="AdminLTELogo"
        height="60"
        width="60" />
    </div>
    <?php require_once "../app/components/Sidebar.php"; ?>

    <div class="main-content">
        <div class="content-wrapper">
            <div class="page-header">
                <div class="page-title">
                    <i class="fas fa-clock"></i> Approve Manual Time
                </div>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="container glass-panel">
                <?php if (empty($pendingApprovals)): ?>
                    <div class="alert" style="background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb;">
                        <strong>All Clear!</strong> No pending attendance records to approve.
                    </div>
                <?php else: ?>
                    <table class="approvals-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Method</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingApprovals as $record): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($record['full_name']); ?></strong>
                                    </td>
                                    <td><?php echo Helper::formatDate($record['attendance_date']); ?></td>
                                    <td><?php echo Helper::formatTime($record['time_in']); ?></td>
                                    <td><?php echo Helper::formatTime($record['time_out'] ?? 'N/A'); ?></td>
                                    <td><span class="badge badge-info"><?php echo $record['recorded_by']; ?></span></td>
                                    <td>
                                        <button class="action-btn btn-approve" onclick="openApproveModal(<?php echo $record['attendance_id']; ?>, '<?php echo htmlspecialchars($record['full_name']); ?>', '<?php echo Helper::formatDate($record['attendance_date']); ?>', '<?php echo Helper::formatTime($record['time_in']); ?>', '<?php echo Helper::formatTime($record['time_out'] ?? 'N/A'); ?>')">Approve</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal('approveModal')">&times;</span>
            <h3>Approve Attendance Record</h3>
            
            <div class="info-box" id="recordInfo"></div>

            <form method="POST">
                <input type="hidden" name="action" value="approve">
                <input type="hidden" name="attendance_id" id="approveRecordId">
                
                <div class="form-group">
                    <label>Approval Remarks (Optional)</label>
                    <textarea name="remarks" placeholder="Add any notes about this approval..."></textarea>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="action-btn btn-approve" style="flex: 1;">Approve</button>
                    <button type="button" class="action-btn" onclick="closeModal('approveModal')" style="background: #95a5a6; color: white; flex: 1;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openApproveModal(recordId, empName, date, timeIn, timeOut) {
            document.getElementById('approveRecordId').value = recordId;
            const infoBox = `
                <strong>Employee:</strong> ${empName}<br>
                <strong>Date:</strong> ${date}<br>
                <strong>Time In:</strong> ${timeIn} | <strong>Time Out:</strong> ${timeOut}
            `;
            document.getElementById('recordInfo').innerHTML = infoBox;
            document.getElementById('approveModal').classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }

        // Load dark mode preference (default to light mode for time_attendance)
        window.addEventListener('load', function() {
            const darkModeSetting = localStorage.getItem('darkMode');
            const darkMode = darkModeSetting === 'true'; // Only true if explicitly set
            
            // Reset to light mode by default on each page load for time_attendance
            if (!darkModeSetting) {
                localStorage.setItem('darkMode', 'false');
            }
            
            if (darkMode) {
                document.body.classList.add('dark-mode');
            }
        });
    </script>
</body>
</html>
