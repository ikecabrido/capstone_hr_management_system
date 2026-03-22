<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/AttendanceController.php';

class EmployeePortalController
{
    public function index()
    {
        Auth::requireAuth();

        $title = "Employee Portal";
        $employee_id = Session::get('employee_id');
        $employeeModel = new Employee();
        $employee = $employeeModel->getById($employee_id);

        if (!$employee) {
            Session::set('error', 'Employee data not found');
            header("Location: index.php?url=auth-index");
            exit;
        }

        $attendanceController = new AttendanceController();
        $employeeId = Session::get('employee_id');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];

            if ($action === 'time_in') {
                $result = $attendanceController->timeIn($employee_id);
            } elseif ($action === 'time_out') {
                $result = $attendanceController->timeOut($employee_id);
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

        $statusInfo = $attendanceController->getStatus($employee_id);

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
