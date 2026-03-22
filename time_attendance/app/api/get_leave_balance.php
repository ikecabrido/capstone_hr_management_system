<?php
/**
 * Get Leave Balance API
 * GET /api/get_leave_balance.php?employee_id=1&leave_type_id=1
 * 
 * Returns current leave balance for an employee
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../models/Leave.php';

Session::start();

// Check if user is authenticated
if (!Session::get('user_id')) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Please log in']);
    exit;
}

// Verify request method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// Get parameters
$employee_id = isset($_GET['employee_id']) ? (int)$_GET['employee_id'] : null;
$leave_type_id = isset($_GET['leave_type_id']) ? (int)$_GET['leave_type_id'] : null;

// Validate parameters
if (!$employee_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'employee_id is required']);
    exit;
}

// Check if user is requesting their own balance or is HR admin
$user_role = Session::get('role');
$session_user_id = Session::get('user_id');

if ($employee_id != $session_user_id && $user_role !== 'HR_ADMIN' && $user_role !== 'DEPARTMENT_HEAD') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden - Cannot view other employees balance']);
    exit;
}

// Get leave balance
$leaveModel = new Leave();

try {
    if ($leave_type_id) {
        $balance = $leaveModel->getLeaveBalance($employee_id, $leave_type_id);
        if (!$balance) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Leave balance not found']);
            exit;
        }
        $data = $balance;
    } else {
        $balances = $leaveModel->getLeaveBalance($employee_id);
        if (empty($balances)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'No leave balances found']);
            exit;
        }
        $data = $balances;
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Leave balance retrieved successfully',
        'data' => $data,
        'current_year' => date('Y')
    ]);
} catch (Exception $e) {
    error_log("Get Leave Balance Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
