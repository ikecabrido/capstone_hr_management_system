<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/AttendanceController.php';

class EmployeePortalController
{
    private $employeeModel;
    private $attendanceController;

    public function __construct()
    {
        $employeeModel = new Employee();

        $attendanceController = new AttendanceController();
    }
    
public function index()
{
    Auth::requireAuth();

    $title = "Employee Portal";

    // STEP 1: Get logged-in user
    $user_id = Session::get('user_id');

    if (!$user_id) {
        die('User not logged in.');
    }

    // STEP 2: Get employee using user_id
    $employee = $this->employeeModel->findByUserId($user_id);

    if (!$employee) {
        Session::set('error', 'Employee data not found');
        header("Location: index.php?url=auth-index");
        exit;
    }

    // STEP 3: Extract needed values
    $employee_id = $employee['id'];           // ✅ THIS is what you want
    $employee_no = $employee['employee_no'];  // ✅ for attendance

    // (Optional) store in session if you want reuse
    Session::set('employee_id', $employee_id);

    // HANDLE FORM ACTIONS
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'time_in') {
            $result = $this->attendanceController->timeIn($employee_no);
        } elseif ($action === 'time_out') {
            $result = $this->attendanceController->timeOut($employee_no);
        }

        if (isset($result)) {
            if ($result['success']) {
                Session::set('success', $result['message']);
            } else {
                Session::set('error', $result['message']);
            }
            header("Location: index.php?url=dashb");
            exit;
        }
    }

    // Get attendance status
    $statusInfo = $this->attendanceController->getStatus($employee_no);

    // Messages
    $message = Session::get('success') ?? Session::get('error') ?? null;
    $messageType = Session::get('success') ? 'success' : (Session::get('error') ? 'danger' : 'info');

    Session::set('success', null);
    Session::set('error', null);

    $content = __DIR__ . '/../views/employee-portal/main-content.php';
    require __DIR__ . '/../views/employee-portal/index.php';
}
    public function adminIndex()
    {
        $content = __DIR__ . '/../views/admin/main-content.php';
        require __DIR__ . '/../views/admin/index.php';
    }
}
