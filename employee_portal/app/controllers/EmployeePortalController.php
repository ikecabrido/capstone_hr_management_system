<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/Leave.php';
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
        $employee_id = $employee['id'];

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

        $leave_balances = $this->leaveModel->getLeaveBalances($employee_id);
        $monthly_attendance = $this->attendanceModel->getMonthlyAttendance($employee_id);
        $leave_requests = $this->leaveModel->getLeaveRequestsByEmployee($employee_id);

        $content = __DIR__ . '/../views/employee-portal/main-content.php';
        require __DIR__ . '/../views/employee-portal/index.php';
    }
    public function adminIndex()
    {
        $content = __DIR__ . '/../views/admin/main-content.php';
        require __DIR__ . '/../views/admin/index.php';
    }
}
