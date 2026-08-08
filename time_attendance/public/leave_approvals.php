<?php
/**
 * Leave Management Page
 * Department heads and HR can review leave requests and view employee leave balances
 */

require_once "../app/controllers/AuthController.php";
require_once "../app/controllers/LeaveController.php";
require_once "../app/models/Leave.php";
require_once "../app/models/Employee.php";
require_once "../app/helpers/Helper.php";
require_once "../app/helpers/LeaveAbsenceHelper.php";
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

// Only the time module approver roles can access this page
if (!in_array($role, ['time', 'HR_ADMIN', 'DEPARTMENT_HEAD'], true)) {
    header("Location: employee_dashboard.php");
    exit;
}

$leaveModel = new Leave();
$employeeModel = new Employee();
$leaveController = new LeaveController();

$message = "";
$messageType = "";
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $messageType = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

// Pagination settings
$recordsPerPage = max(5, min(50, (int)($_REQUEST['employee_page_size'] ?? 10)));
$employeePage = max(1, (int)($_REQUEST['employee_page'] ?? 1));
$leavePage = max(1, (int)($_REQUEST['leave_page'] ?? 1));
$employeeSearch = trim($_REQUEST['employee_search'] ?? '');

$employeeOffset = ($employeePage - 1) * $recordsPerPage;
$leaveOffset = ($leavePage - 1) * $recordsPerPage;

// Load employee list for the management table
$employees = $employeeModel->getAll('Active', $recordsPerPage, $employeeOffset, $employeeSearch);
$totalEmployees = $employeeModel->getTotalCount('Active', $employeeSearch);
$totalEmployeePages = max(1, (int)ceil($totalEmployees / $recordsPerPage));

// Get pending requests based on role
// Determine which leave requests to display based on approver role
if ($role === 'DEPARTMENT_HEAD') {
    $pendingRequests = $leaveModel->getPendingByDepartmentHead($user_id, $recordsPerPage, $leaveOffset);
    $totalLeaveRequests = $leaveModel->countPendingByDepartmentHead($user_id);
} else {
    $pendingRequests = $leaveModel->getForHRApproval($recordsPerPage, $leaveOffset);
    $totalLeaveRequests = $leaveModel->countForHRApproval();
}

$totalLeavePages = max(1, (int)ceil($totalLeaveRequests / $recordsPerPage));

// Load leave balances for pending request rows to display alongside approvals
$leaveBalances = [];
foreach ($pendingRequests as $request) {
    $balanceKey = $request['employee_id'] . '_' . $request['leave_type_id'];
    if (!isset($leaveBalances[$balanceKey])) {
        $leaveBalances[$balanceKey] = $leaveModel->getLeaveBalance($request['employee_id'], $request['leave_type_id']);
    }
}

// Load balance availability for current employees in the employee management table
$employeeBalances = [];
foreach ($employees as $emp) {
    $employeeBalances[$emp['employee_id']] = !empty($leaveModel->getLeaveBalance($emp['employee_id']));
}

// Handle approval/rejection
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = trim($_POST['action'] ?? '');
    $leave_request_id = (int)($_POST['leave_request_id'] ?? 0);
    $remarks = Helper::sanitize($_POST['remarks'] ?? '');
    $is_hr = in_array($role, ['time', 'HR_ADMIN'], true);
    $is_department_head = $role === 'DEPARTMENT_HEAD';
    $submittedEmployeePageSize = max(5, min(50, (int)($_POST['employee_page_size'] ?? $recordsPerPage)));
    $submittedEmployeeSearch = trim($_POST['employee_search'] ?? $employeeSearch);

    // Debug logging
    error_log("DEBUG: POST received - Action: $action, LeaveID: $leave_request_id, User: $user_id, Role: $role");

    if ($leave_request_id && $action === 'approve') {
        $result = $leaveController->approve($leave_request_id, $user_id, $is_hr, $remarks);
        if ($result['success']) {
            $_SESSION['flash_message'] = "Leave request approved successfully!";
            $_SESSION['flash_type'] = "success";
            header("Location: " . $_SERVER['PHP_SELF'] . "?employee_page={$employeePage}&leave_page={$leavePage}&employee_page_size={$submittedEmployeePageSize}&employee_search=" . urlencode($submittedEmployeeSearch));
            exit;
        }

        $message = $result['message'] ?? 'Failed to process approval.';
        $messageType = "error";
    } elseif ($leave_request_id && $action === 'reject') {
        if (empty($remarks)) {
            $message = "Rejection reason is required.";
            $messageType = "error";
        } else {
            $result = $leaveController->reject($leave_request_id, $user_id, $remarks);
            if ($result['success']) {
                $_SESSION['flash_message'] = "Leave request rejected.";
                $_SESSION['flash_type'] = "warning";
                header("Location: " . $_SERVER['PHP_SELF'] . "?employee_page={$employeePage}&leave_page={$leavePage}&employee_page_size={$submittedEmployeePageSize}&employee_search=" . urlencode($submittedEmployeeSearch));
                exit;
            }
            $message = $result['message'] ?? 'Failed to process rejection.';
            $messageType = "error";
        }
    }
}


$current_page = 'leave_approvals.php';
$current_role = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'time';
$page_title = 'Leave Management';
$page_subtitle = 'Review requests and view employee leave balances in one place';
$page_icon = 'fa-file-signature';
$page_head_extra = "<link rel=\"icon\" href=\"../Bestlink College of the Philippines.jpeg\" type=\"image/jpeg\">\n<link rel=\"stylesheet\" href=\"../../assets/dist/css/adminlte.min.css\">\n<link rel=\"stylesheet\" href=\"../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css\">\n<link rel=\"stylesheet\" href=\"../assets/style.css\">\n<link rel=\"stylesheet\" href=\"../assets/dashboard.css\">\n<link rel=\"stylesheet\" href=\"../assets/adminlte-overrides.css\">\n<link rel=\"stylesheet\" href=\"../assets/hr-template.css\">\n<link rel=\"stylesheet\" href=\"https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback\">\n<script src=\"../assets/mobile-responsive.js\" defer></script>";
?>

<?php require_once __DIR__ . '/../layout/page_start.php'; ?>
<?php require_once __DIR__ . '/../layout/sidebar.php'; ?>
<?php require_once __DIR__ . '/../layout/content_header.php'; ?>

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
            background: linear-gradient(135deg, #1e90ff 0%, #0066cc 100%);
            color: white;
            border: 1px solid #005bb5;
        }
        .btn-approve:hover {
            background: linear-gradient(135deg, #005bb5 0%, #004494 100%);
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
        /* AdminLTE Preloader Styles */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0d47a1 0%, #0b3c91 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 99999;
        }

        .preloader.flex-column {
            flex-direction: column;
        }

        .preloader.justify-content-center {
            justify-content: center;
        }

        .preloader.align-items-center {
            align-items: center;
        }

        .preloader img {
            max-width: 100px;
            height: auto;
            display: block;
        }

        .animation__wobble {
            animation: wobble 2.5s infinite ease-in-out;
        }

        @keyframes wobble {
            0% {
                transform: translateX(0);
            }
            15% {
                transform: translateX(-5px) rotate(-5deg);
            }
            30% {
                transform: translateX(3px) rotate(3deg);
            }
            45% {
                transform: translateX(-3px) rotate(-3deg);
            }
            60% {
                transform: translateX(2px) rotate(2deg);
            }
            75% {
                transform: translateX(-1px) rotate(-1deg);
            }
            100% {
                transform: translateX(0);
            }
        }

    </style>

    <div class="card shadow-sm border-0">
        <?php if (!empty($message)): ?>
            <div class="card-body pb-0">
                <div class="alert alert-<?php echo $messageType; ?> mb-0">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="card-body">
            <div class="section-header">
                <h4>Employee Leave Balances</h4>
                <p>Click an employee to view their remaining leave balances.</p>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap" style="gap: 10px;">
                <form method="GET" class="d-flex flex-wrap" style="gap: 10px; align-items: center; margin: 0;">
                    <input type="hidden" name="leave_page" value="<?php echo $leavePage; ?>">
                    <input type="hidden" name="employee_page_size" value="<?php echo $recordsPerPage; ?>">
                    <input type="text" name="employee_search" class="form-control" placeholder="Search employees..." value="<?php echo htmlspecialchars($employeeSearch); ?>" style="min-width:240px;">
                    <button type="submit" class="action-btn btn-approve" style="padding: 10px 18px;">Search</button>
                    <a href="leave_approvals.php?employee_page_size=<?php echo $recordsPerPage; ?>&leave_page=<?php echo $leavePage; ?>&employee_search=" class="action-btn" style="background: #ffffff; color: #0066cc; padding: 10px 18px; border: 1px solid #cce4ff;">Clear</a>
                </form>
                <div style="display:flex; gap:10px; align-items:center; flex-wrap: wrap;">
                    <label for="employeePageSize" style="margin:0; font-weight:600;">Page size:</label>
                    <select id="employeePageSize" class="form-control" onchange="changeEmployeePageSize(this.value)" style="min-width:100px;">
                        <?php foreach ([5, 10, 15, 20, 30] as $size): ?>
                            <option value="<?php echo $size; ?>" <?php echo $recordsPerPage === $size ? 'selected' : ''; ?>><?php echo $size; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="action-btn btn-approve" type="button" onclick="provisionLeaveBalances()" style="padding: 10px 18px;">Provision Balances for All Active Employees</button>
                </div>
            </div>
            <?php if (empty($employees)): ?>
                <div class="alert alert-info mb-4">
                    No employees found.
                </div>
            <?php else: ?>
                <div class="table-responsive mb-4">
                    <table class="approvals-table employee-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employees as $emp): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($emp['full_name']); ?></td>
                                    <td>
                                    <?php echo htmlspecialchars($emp['department'] ?? 'N/A'); ?>
                                    <?php if (empty($employeeBalances[$emp['employee_id']])): ?>
                                        <span class="badge badge-warning" style="margin-left: 10px; font-size: 12px;">No balances yet</span>
                                    <?php endif; ?>
                                </td>
                                    <td>
                                        <button class="action-btn btn-approve balance-button" type="button" data-employee-id="<?php echo htmlspecialchars($emp['employee_id'], ENT_QUOTES, 'UTF-8'); ?>" data-full-name="<?php echo htmlspecialchars($emp['full_name'], ENT_QUOTES, 'UTF-8'); ?>">View Balance</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="pagination mb-4">
                    <?php if ($employeePage > 1): ?>
                        <a href="?employee_page=<?php echo $employeePage - 1; ?>&leave_page=<?php echo $leavePage; ?>&employee_page_size=<?php echo $recordsPerPage; ?>&employee_search=<?php echo urlencode($employeeSearch); ?>">&laquo; Previous</a>
                    <?php endif; ?>
                    <span class="active">Employee Page <?php echo $employeePage; ?> of <?php echo $totalEmployeePages; ?></span>
                    <?php if ($employeePage < $totalEmployeePages): ?>
                        <a href="?employee_page=<?php echo $employeePage + 1; ?>&leave_page=<?php echo $leavePage; ?>&employee_page_size=<?php echo $recordsPerPage; ?>&employee_search=<?php echo urlencode($employeeSearch); ?>">Next &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="section-header">
                <h4>Pending Leave Requests</h4>
                <p>Review request approvals alongside employee leave balances.</p>
            </div>

            <?php if (empty($pendingRequests)): ?>
                <div class="alert alert-info mb-0">
                    No pending leave requests to review.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="approvals-table" id="leaveApprovalTable">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Days</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingRequests as $req): ?>
                                <?php $balanceKey = $req['employee_id'] . '_' . $req['leave_type_id'];
                                      $balance = $leaveBalances[$balanceKey] ?? null;
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($req['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($req['leave_type_name']); ?></td>
                                    <td><?php echo Helper::formatDate($req['start_date']); ?></td>
                                    <td><?php echo Helper::formatDate($req['end_date']); ?></td>
                                    <td><?php echo rtrim(rtrim(number_format($req['total_days'], 2), '0'), '.'); ?></td>
                                    <td>
                                        <?php if ($balance): ?>
                                            <span title="Used / Total">
                                                <?php echo rtrim(rtrim(number_format($balance['remaining_days'], 2), '0'), '.'); ?> left of <?php echo rtrim(rtrim(number_format($balance['total_days'], 2), '0'), '.'); ?>
                                            </span>
                                            <br>
                                            <small class="text-muted"><?php echo rtrim(rtrim(number_format($balance['used_days'], 2), '0'), '.'); ?> used</small>
                                        <?php else: ?>
                                            <span class="text-muted">No balance record</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo LeaveAbsenceHelper::getLeaveStatusBadge($req['status']); ?>
                                    </td>
                                    <td>
                                        <button type="button" class="action-btn btn-approve approve-request-btn" data-request-id="<?php echo intval($req['id']); ?>">Approve</button>
                                        <button type="button" class="action-btn btn-reject reject-request-btn" data-request-id="<?php echo intval($req['id']); ?>">Reject</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <?php if ($leavePage > 1): ?>
                        <a href="?leave_page=<?php echo $leavePage - 1; ?>&employee_page=<?php echo $employeePage; ?>&employee_page_size=<?php echo $recordsPerPage; ?>&employee_search=<?php echo urlencode($employeeSearch); ?>">&laquo; Previous</a>
                    <?php endif; ?>
                    <span class="active">Leave Page <?php echo $leavePage; ?> of <?php echo $totalLeavePages; ?></span>
                    <?php if ($leavePage < $totalLeavePages): ?>
                        <a href="?leave_page=<?php echo $leavePage + 1; ?>&employee_page=<?php echo $employeePage; ?>&employee_page_size=<?php echo $recordsPerPage; ?>&employee_search=<?php echo urlencode($employeeSearch); ?>">Next &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal('approveModal')">&times;</span>
            <h3>Approve Leave Request</h3>
            <form id="approveForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>" method="POST">
                <input type="hidden" name="action" value="approve">
                <input type="hidden" name="leave_request_id" id="approveRequestId">
                <input type="hidden" name="employee_page" value="<?php echo $employeePage; ?>">
                <input type="hidden" name="leave_page" value="<?php echo $leavePage; ?>">
                <input type="hidden" name="employee_page_size" value="<?php echo $recordsPerPage; ?>">
                <input type="hidden" name="employee_search" value="<?php echo htmlspecialchars($employeeSearch, ENT_QUOTES, 'UTF-8'); ?>">
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
            <form id="rejectForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>" method="POST">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="leave_request_id" id="rejectRequestId">
                <input type="hidden" name="employee_page" value="<?php echo $employeePage; ?>">
                <input type="hidden" name="leave_page" value="<?php echo $leavePage; ?>">
                <input type="hidden" name="employee_page_size" value="<?php echo $recordsPerPage; ?>">
                <input type="hidden" name="employee_search" value="<?php echo htmlspecialchars($employeeSearch, ENT_QUOTES, 'UTF-8'); ?>">
                <div style="margin: 15px 0;">
                    <label>Rejection Reason:</label>
                    <textarea name="remarks" required></textarea>
                </div>
                <button type="submit" class="action-btn btn-reject">Reject</button>
                <button type="button" class="action-btn" onclick="closeModal('rejectModal')" style="background: #95a5a6;">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Employee Balance Modal -->
    <div id="balanceModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal('balanceModal')">&times;</span>
            <h3 id="balanceModalTitle">Employee Leave Balances</h3>
            <div id="balanceModalBody">
                <p>Loading leave balances...</p>
            </div>
            <div class="modal-button-group">
                <button type="button" class="action-btn" onclick="closeModal('balanceModal')" style="background: #95a5a6;">Close</button>
            </div>
        </div>
    </div>

    <script>
        console.log('leave approvals script loaded');
        function openApproveModal(requestId) {
            console.log('openApproveModal', requestId);
            document.getElementById('approveRequestId').value = requestId;
            document.getElementById('approveModal').classList.add('active');
        }

        function openRejectModal(requestId) {
            console.log('openRejectModal', requestId);
            document.getElementById('rejectRequestId').value = requestId;
            document.getElementById('rejectModal').classList.add('active');
        }

        function formatLeaveDays(value) {
            var num = parseFloat(value);
            if (!Number.isFinite(num)) {
                return '';
            }
            return num % 1 === 0 ? num.toString() : num.toFixed(2).replace(/\.0+$/, '');
        }

        function openBalanceModal(employeeId, fullName) {
            const modal = document.getElementById('balanceModal');
            const body = document.getElementById('balanceModalBody');
            const title = document.getElementById('balanceModalTitle');

            title.textContent = fullName + ' — Leave Balances';
            body.innerHTML = '<p>Loading leave balances...</p>';
            modal.classList.add('active');

            var balanceApiUrl = '<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/../app/api/get_leave_balance.php';
            fetch(balanceApiUrl + '?employee_id=' + encodeURIComponent(employeeId))
                .then(function(response) {
                    if (!response.ok) {
                        return response.text().then(function(text) {
                            body.innerHTML = '<p class="text-danger">Unable to load leave balances. Server returned ' + response.status + '.</p>';
                            throw new Error('HTTP ' + response.status + ': ' + text);
                        });
                    }

                    return response.json().then(function(data) {
                        return { status: response.status, body: data };
                    });
                })
                .then(function(result) {
                    var data = result.body;
                    if (!data.success) {
                        body.innerHTML = '<p class="text-danger">' + (data.message || 'Unable to load leave balances.') + '</p>';
                        return;
                    }

                    var balances = data.data;
                    if (!balances || balances.length === 0) {
                        body.innerHTML = '<p>No leave balances available for this employee.</p>';
                        return;
                    }

                    var rows = balances.map(function(balance) {
                        return '<tr>' +
                               '<td>' + (balance.leave_type_name || '') + '</td>' +
                               '<td>' + formatLeaveDays(balance.total_days) + '</td>' +
                               '<td>' + formatLeaveDays(balance.used_days) + '</td>' +
                               '<td>' + formatLeaveDays(balance.remaining_days) + '</td>' +
                               '</tr>';
                    }).join('');

                    body.innerHTML = '<div class="table-responsive">' +
                                     '<table class="approvals-table">' +
                                     '<thead><tr>' +
                                     '<th>Leave Type</th>' +
                                     '<th>Total Days</th>' +
                                     '<th>Used</th>' +
                                     '<th>Remaining</th>' +
                                     '</tr></thead>' +
                                     '<tbody>' + rows + '</tbody>' +
                                     '</table></div>';
                })
                .catch(function() {
                    body.innerHTML = '<p class="text-danger">Unable to load leave balances at this time.</p>';
                });
        }

        function debounce(fn, delay) {
            var timer;
            return function() {
                var context = this, args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function() {
                    fn.apply(context, args);
                }, delay);
            };
        }

        function attachLiveSearch() {
            var searchInput = document.querySelector('input[name="employee_search"]');
            if (!searchInput) return;

            searchInput.addEventListener('input', debounce(function() {
                var form = this.closest('form');
                if (!form) return;
                var url = new URL(window.location.href);
                url.searchParams.set('employee_search', this.value);
                url.searchParams.set('employee_page', 1);
                url.searchParams.set('leave_page', '<?php echo $leavePage; ?>');
                url.searchParams.set('employee_page_size', '<?php echo $recordsPerPage; ?>');
                window.location.href = url.toString();
            }, 300));
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        document.addEventListener('DOMContentLoaded', function() {
            var balanceButtons = document.querySelectorAll('.balance-button');
            balanceButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    openBalanceModal(this.dataset.employeeId, this.dataset.fullName);
                });
            });
            var approveButtons = document.querySelectorAll('.approve-request-btn');
            approveButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    openApproveModal(this.dataset.requestId);
                });
            });
            var rejectButtons = document.querySelectorAll('.reject-request-btn');
            rejectButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    openRejectModal(this.dataset.requestId);
                });
            });
            var approveForm = document.getElementById('approveForm');
            if (approveForm) {
                approveForm.addEventListener('submit', function() {
                    console.log('approveForm submitted');
                });
            }
            var rejectForm = document.getElementById('rejectForm');
            if (rejectForm) {
                rejectForm.addEventListener('submit', function() {
                    console.log('rejectForm submitted');
                });
            }
            attachLiveSearch();
        });

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }

        function changeEmployeePageSize(pageSize) {
            const searchValue = document.querySelector('input[name="employee_search"]').value;
            const url = new URL(window.location.href);
            url.searchParams.set('employee_page_size', pageSize);
            url.searchParams.set('employee_page', 1);
            url.searchParams.set('leave_page', '<?php echo $leavePage; ?>');
            url.searchParams.set('employee_search', searchValue || '');
            window.location.href = url.toString();
        }

        function provisionLeaveBalances() {
            if (!confirm('Provision leave balances for all active employees now?')) {
                return;
            }

            var provisionUrl = '<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/../app/api/provision_leave_balances.php';
            fetch(provisionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ year: new Date().getFullYear() })
            })
            .then(function(response) {
                return response.json().then(function(data) {
                    if (!response.ok) {
                        throw new Error(data.message || 'Request failed');
                    }
                    return data;
                });
            })
            .then(function(data) {
                if (data.success) {
                    alert('Leave balances provisioned for ' + data.employees_processed + ' active employees for ' + data.year + '.');
                    window.location.reload();
                } else {
                    alert('Provisioning failed: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(function(error) {
                alert('Unable to provision leave balances. ' + error.message);
            });
        }

        // Load dark mode preference (default to light mode for time_attendance)
        window.preloaderHold = true;

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

            if (window.releasePreloader) {
                window.releasePreloader(1200);
            }
        });
    </script>
<?php require_once __DIR__ . '/../layout/content_footer.php'; ?>
<?php require_once __DIR__ . '/../layout/page_end.php'; ?>


