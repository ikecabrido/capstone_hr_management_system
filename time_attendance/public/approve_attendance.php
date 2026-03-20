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
if (!AuthController::hasRole('HR_ADMIN')) {
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
    <link rel="stylesheet" href="../assets/style.css">
    <script src="../assets/mobile-responsive.js" defer></script>
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background: #f5f5f5;
        }
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 20px;
            transition: margin-left 0.3s ease, background-color 0.3s ease, color 0.3s ease;
        }
        .content-wrapper {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1, h2 {
            color: #003d82;
            margin-bottom: 20px;
            font-weight: 700;
        }
        body.dark-mode h1,
        body.dark-mode h2 {
            color: #5fa3ff;
            font-weight: 700;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 61, 130, 0.08);
            margin-bottom: 25px;
            border: 1px solid #e8eef7;
        }
        body.dark-mode .container {
            background: #1e1e1e;
            color: #e0e0e0;
            border: 1px solid #404040;
        }
        .approvals-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 61, 130, 0.08);
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
            z-index: 1000;
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
    </style>
</head>
<body>
    <?php require_once "../app/components/Sidebar.php"; ?>

    <div class="main-content">
        <div class="content-wrapper">
            <h1>⏳ Approve Manual Time</h1>
            <p>Review and approve manual attendance entries</p>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="container">
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

        // Load dark mode preference on page load
        window.addEventListener('load', function() {
            const darkMode = localStorage.getItem('darkMode') === 'true';
            if (darkMode) {
                document.body.classList.add('dark-mode');
            }
        });
    </script>
</body>
</html>
