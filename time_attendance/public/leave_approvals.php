<?php
/**
 * Leave Approvals Page
 * Department heads and HR can review and approve leave requests
 */

require_once "../app/controllers/AuthController.php";
require_once "../app/models/Leave.php";
require_once "../app/helpers/Helper.php";
require_once "../app/core/Session.php";

Session::start();

// Check if user is authenticated
// Try global session first, then time_attendance session
$authenticated = false;
$role = null;
$user_id = null;

// Check global login session
if (isset($_SESSION['user']) && is_array($_SESSION['user']) && isset($_SESSION['user']['id'])) {
    $authenticated = true;
    $role = $_SESSION['user']['role'];
    $user_id = $_SESSION['user']['id'];
} else if (AuthController::isAuthenticated()) {
    // Fallback to time_attendance auth check
    $authenticated = true;
    $role = AuthController::getCurrentRole();
    $user_id = AuthController::getCurrentUserId();
}

if (!$authenticated) {
    header("Location: ../../login_form.php");
    exit;
}

// Only 'time' role can access this page
if ($role !== 'time') {
    header("Location: employee_dashboard.php");
    exit;
}

$leaveModel = new Leave();

$message = "";
$messageType = "";

// Handle approval/rejection
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = trim($_POST['action'] ?? '');
    $leave_request_id = (int)($_POST['leave_request_id'] ?? 0);
    $remarks = Helper::sanitize($_POST['remarks'] ?? '');
    $is_hr = ($role === 'time');

    if ($action === 'approve' && $leave_request_id) {
        $new_status = $is_hr ? 'APPROVED_BY_HR' : 'APPROVED_BY_HEAD';
        if ($leaveModel->updateStatus($leave_request_id, $new_status, $user_id, $remarks)) {
            $message = "Leave request approved successfully!";
            $messageType = "success";
            // Optionally refresh the list
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $message = "Failed to process approval.";
            $messageType = "error";
        }
    } elseif ($action === 'reject' && $leave_request_id) {
        if ($leaveModel->updateStatus($leave_request_id, 'REJECTED', $user_id, $remarks)) {
            $message = "Leave request rejected.";
            $messageType = "warning";
        } else {
            $message = "Failed to process rejection.";
            $messageType = "error";
        }
    }
}

// Get pending requests based on role
// All 'time' role users see all pending and head-approved requests
$pendingRequests = $leaveModel->getForHRApproval();

$current_page = 'leave_approvals.php';
$current_role = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'time';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Approvals - Time & Attendance System</title>
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
            margin-bottom: 20px;
        }
        body.dark-mode h1,
        body.dark-mode h2 {
            color: #b0c4de;
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
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .badge-pending {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe57f 100%);
            color: #856404;
            border: 1px solid #ffc107;
        }
        .badge-pending:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(255, 193, 7, 0.3);
        }
        .badge-approved {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border: 1px solid #28a745;
        }
        .badge-approved:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
        }
        body.dark-mode .badge-pending {
            background: linear-gradient(135deg, #664d03 0%, #997404 100%);
            color: #fff3cd;
            border-color: #997404;
        }
        body.dark-mode .badge-approved {
            background: linear-gradient(135deg, #0f3622 0%, #1a5e3a 100%);
            color: #d4edda;
            border-color: #1a5e3a;
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
        .btn-reject {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            border: 1px solid #c0392b;
        }
        .btn-reject:hover {
            background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
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
            max-width: 500px;
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
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
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
        .modal-button-group {
            margin-top: 15px;
            text-align: right;
        }
        .modal-button-group button {
            margin-left: 10px;
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
            <h1>Leave Request Approvals</h1>
            <p>Review and approve leave requests.</p>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="container">
                <?php if (empty($pendingRequests)): ?>
                    <div class="alert alert-info">
                        No pending leave requests to review.
                    </div>
                <?php else: ?>
                    <table class="approvals-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Days</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingRequests as $req): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($req['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($req['leave_type_name']); ?></td>
                                    <td><?php echo Helper::formatDate($req['start_date']); ?></td>
                                    <td><?php echo Helper::formatDate($req['end_date']); ?></td>
                                    <td><?php echo $req['total_days']; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $req['status'] === 'PENDING' ? 'badge-pending' : 'badge-approved'; ?>">
                                            <?php echo $req['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="action-btn btn-approve" onclick="openApproveModal(<?php echo $req['leave_request_id']; ?>)">Approve</button>
                                        <button class="action-btn btn-reject" onclick="openRejectModal(<?php echo $req['leave_request_id']; ?>)">Reject</button>
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
            <h3>Approve Leave Request</h3>
            <form method="POST">
                <input type="hidden" name="action" value="approve">
                <input type="hidden" name="leave_request_id" id="approveRequestId">
                <div style="margin: 15px 0;">
                    <label>Remarks (optional):</label>
                    <textarea name="remarks"></textarea>
                </div>
                <button type="submit" class="action-btn btn-approve">Approve</button>
                <button type="button" class="action-btn" onclick="closeModal('approveModal')" style="background: #95a5a6;">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal('rejectModal')">&times;</span>
            <h3>Reject Leave Request</h3>
            <form method="POST">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="leave_request_id" id="rejectRequestId">
                <div style="margin: 15px 0;">
                    <label>Rejection Reason:</label>
                    <textarea name="remarks" required></textarea>
                </div>
                <button type="submit" class="action-btn btn-reject">Reject</button>
                <button type="button" class="action-btn" onclick="closeModal('rejectModal')" style="background: #95a5a6;">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function openApproveModal(requestId) {
            document.getElementById('approveRequestId').value = requestId;
            document.getElementById('approveModal').classList.add('active');
        }

        function openRejectModal(requestId) {
            document.getElementById('rejectRequestId').value = requestId;
            document.getElementById('rejectModal').classList.add('active');
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