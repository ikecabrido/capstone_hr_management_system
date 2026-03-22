<?php
require_once "../app/config/Database.php";
require_once "../app/controllers/AuthController.php";
require_once "../app/controllers/AttendanceController.php";
require_once "../app/models/Employee.php";
require_once "../app/models/Attendance.php";
require_once "../app/models/Leave.php";
require_once "../app/models/EmployeeShift.php";
require_once "../app/models/Shift.php";
require_once "../app/core/Helper.php";
require_once "../app/core/Session.php";

Session::start();
AuthController::requireAuth();

$user_id = AuthController::getCurrentUserId();
$employeeModel = new Employee();
$attendanceModel = new Attendance();
$leaveModel = new Leave();
$db = new Database();
$conn = $db->getConnection();
$employeeShiftModel = new EmployeeShift($conn);
$attendanceController = new AttendanceController();

$employee = $employeeModel->getByUserId($user_id);
if (!is_array($employee) || !isset($employee['employee_id'])) {
    header("Location: ../../login_form.php");
    exit;
}
$employee_id = $employee['employee_id'];

$statusInfo = $attendanceController->getStatus($employee_id);

$message = "";
$messageType = "";

if (isset($_SESSION['qr_success'])) {
    $message = $_SESSION['qr_success'];
    $messageType = "success";
    unset($_SESSION['qr_success']);
}

if (isset($_SESSION['qr_error'])) {
    $message = $_SESSION['qr_error'];
    $messageType = "error";
    unset($_SESSION['qr_error']);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = trim($_POST['action'] ?? '');
    if ($action === 'time_in') {
        $result = $attendanceController->timeIn($employee_id, 'MANUAL');
        $message = $result['message'];
        $messageType = $result['success'] ? "success" : "error";
        if ($result['success']) $_SESSION['show_time_in_confirm'] = true;
        $statusInfo = $attendanceController->getStatus($employee_id);
    } elseif ($action === 'time_out') {
        $result = $attendanceController->timeOut($employee_id);
        $message = $result['message'];
        $messageType = $result['success'] ? "success" : "error";
        if ($result['success']) $_SESSION['show_time_out_confirm'] = true;
        $statusInfo = $attendanceController->getStatus($employee_id);
    }
}

$current_month_start = date('Y-m-01');
$current_month_end = date('Y-m-t');

$query = "SELECT * FROM attendance WHERE employee_id = ? AND DATE(time_in) BETWEEN ? AND ? ORDER BY time_in DESC";
$stmt = $conn->prepare($query);
$stmt->execute([$employee_id, $current_month_start, $current_month_end]);
$monthly_attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

$present_count = $late_count = $absent_count = $total_hours = $total_overtime = 0;
foreach ($monthly_attendance as $record) {
    $total_hours += $record['total_hours_worked'] ?? 0;
    $total_overtime += $record['overtime_hours'] ?? 0;
    if ($record['status'] === 'ON_TIME' || $record['status'] === 'EARLY') $present_count++;
    elseif ($record['status'] === 'LATE') $late_count++;
}

$six_months_ago = date('Y-m-d', strtotime('-6 months'));
$query_six = "SELECT DATE(time_in) as date, status, total_hours_worked FROM attendance WHERE employee_id = ? AND DATE(time_in) >= ? ORDER BY time_in DESC";
$stmt_six = $conn->prepare($query_six);
$stmt_six->execute([$employee_id, $six_months_ago]);
$six_months_data = $stmt_six->fetchAll(PDO::FETCH_ASSOC);

$current_year = date('Y');
$query_balance = "SELECT lb.*, lt.leave_type_name FROM leave_balances lb JOIN leave_types lt ON lb.leave_type_id = lt.leave_type_id WHERE lb.employee_id = ? AND lb.year = ?";
$stmt_balance = $conn->prepare($query_balance);
$stmt_balance->execute([$employee_id, $current_year]);
$leave_balances = $stmt_balance->fetchAll(PDO::FETCH_ASSOC);

$working_days = Helper::calculateWorkingDays($current_month_start, $current_month_end);
$attendance_percentage = $working_days > 0 ? ($present_count / $working_days) * 100 : 0;

$today = date('Y-m-d');
$query_shift = "SELECT es.*, s.shift_name, s.start_time, s.end_time, s.break_duration FROM employee_shifts es JOIN shifts s ON es.shift_id = s.shift_id WHERE es.employee_id = ? AND es.is_active = 1 AND (es.effective_to IS NULL OR es.effective_to >= ?) AND ? BETWEEN es.effective_from AND COALESCE(es.effective_to, ?)";
$stmt_shift = $conn->prepare($query_shift);
$stmt_shift->execute([$employee_id, $today, $today]);
$today_shift = $stmt_shift->fetch(PDO::FETCH_ASSOC);

$query_requests = "SELECT lr.*, lt.leave_type_name FROM leave_requests lr JOIN leave_types lt ON lr.leave_type_id = lt.leave_type_id WHERE lr.employee_id = ? ORDER BY lr.date_submitted DESC LIMIT 10";
$stmt_requests = $conn->prepare($query_requests);
$stmt_requests->execute([$employee_id]);
$leave_requests = $stmt_requests->fetchAll(PDO::FETCH_ASSOC);

$query_types = "SELECT * FROM leave_types WHERE is_active = 1";
$stmt_types = $conn->prepare($query_types);
$stmt_types->execute();
$leave_types = $stmt_types->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'submit_leave') {
    header('Content-Type: application/json');
    $leave_type_id = trim($_POST['leave_type_id'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $reason = trim($_POST['reason'] ?? '');
    if (empty($leave_type_id) || empty($start_date) || empty($end_date) || empty($reason)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    if (strtotime($start_date) > strtotime($end_date)) {
        echo json_encode(['success' => false, 'message' => 'Start date must be before end date']);
        exit;
    }
    if (strtotime($start_date) < strtotime('today')) {
        echo json_encode(['success' => false, 'message' => 'Cannot submit leave for past dates']);
        exit;
    }
    try {
        $insert_query = "INSERT INTO leave_requests (employee_id, leave_type_id, start_date, end_date, reason, status, created_at) VALUES (?, ?, ?, ?, ?, 'Pending', NOW())";
        $insert_stmt = $conn->prepare($insert_query);
        $result = $insert_stmt->execute([$employee_id, $leave_type_id, $start_date, $end_date, $reason]);
        echo json_encode(['success' => $result, 'message' => $result ? 'Leave request submitted successfully' : 'Error submitting request']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
