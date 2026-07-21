<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/Leave.php';
require_once __DIR__ . '/../models/LeaveBalance.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/AttendanceController.php';
require_once __DIR__ . '/AuthController.php';

class EmployeePortalController
{
    private $employeeModel;
    private $attendanceModel;
    private $leaveModel;
    private $attendanceController;
    private $authController;

    public function __construct()
    {
        $this->employeeModel = new Employee();
        $this->leaveModel = new Leave();
        $this->attendanceModel = new Attendance();
        $this->attendanceController = new AttendanceController();
        $this->authController = new AuthController();
    }

    public function index()
    {
        Auth::requireAuth();

        $title = "Employee Portal";

        $user_id = Session::get('user_id');
        $employee = $this->authController->checkUserEmployee($user_id);
        $employee_id = $employee['employee_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];

            if ($action === 'time_in') {
                $this->attendanceController->timeIn($employee_id);
            } elseif ($action === 'time_out') {
                $this->attendanceController->timeOut($employee_id);
            }
            exit;
        }

        if (!$employee) {
            Session::set('error', 'Employee not found');
            header("Location: index.php?url=auth-index");
            exit;
        }

        $statusInfo = $this->attendanceController->getStatus($employee_id);
        $message = Session::get('success') ?? Session::get('error') ?? null;
        $messageType = Session::get('success') ? 'success' : (Session::get('error') ? 'danger' : 'info');
        Session::set('success', null);
        Session::set('error', null);

        // Fetch dashboard data with fallback to empty arrays
        try {
            $leave_balances = $this->leaveModel->getLeaveBalances($employee_id);
            if (!is_array($leave_balances)) {
                $leave_balances = [];
            }
        } catch (Exception $e) {
            error_log("Error fetching leave balances: " . $e->getMessage());
            $leave_balances = [];
        }

        try {
            $monthly_attendance = $this->attendanceModel->getMonthlyAttendance($employee_id);
            if (!is_array($monthly_attendance)) {
                $monthly_attendance = [];
            }
        } catch (Exception $e) {
            error_log("Error fetching monthly attendance: " . $e->getMessage());
            $monthly_attendance = [];
        }

        try {
            $leave_requests = $this->leaveModel->getLeaveRequestsByEmployee($employee_id);
            if (!is_array($leave_requests)) {
                $leave_requests = [];
            }
        } catch (Exception $e) {
            error_log("Error fetching leave requests: " . $e->getMessage());
            $leave_requests = [];
        }

        // Get all leave types for modal
        try {
            require_once __DIR__ . '/../config/Database.php';
            $db = new Database();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("SELECT leave_type_id, leave_type_name FROM ta_leave_types ORDER BY leave_type_name");
            $stmt->execute();
            $allLeaveTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!is_array($allLeaveTypes)) {
                $allLeaveTypes = [];
            }
        } catch (Exception $e) {
            error_log("Error fetching leave types: " . $e->getMessage());
            $allLeaveTypes = [];
        }

        $content = __DIR__ . '/../views/employee-portal/main-content.php';
        require __DIR__ . '/../views/employee-portal/index.php';
    }
    public function adminIndex()
    {
        $content = __DIR__ . '/../views/admin/main-content.php';
        require __DIR__ . '/../views/admin/index.php';
    }
}
