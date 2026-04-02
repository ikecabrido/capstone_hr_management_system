<?php
require_once __DIR__ . '/../models/Leave.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/LeaveType.php';


class LeaveRequestController
{
    private $leaveModel;
    private $employeeModel;
    private $leaveTypeModel;
    public function __construct()
    {
        $this->leaveModel = new Leave();
        $this->employeeModel = new Employee();
        $this->leaveTypeModel = new LeaveType();
    }

    public function index()
    {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            die('User not logged in.');
        }

        $employee = $this->employeeModel->findByUserId($user_id);
        $employee_id = $employee['id'] ?? null;

        if (!$employee_id) {
            die('Employee record not found.');
        }

        $leaves = $this->leaveModel->getLeavesByEmployee($employee_id);

        $leaveTypeModel = new LeaveType();
        $allLeaveTypes = $leaveTypeModel->getAllLeaveTypes();

        $leaveTypeMap = [];
        foreach ($allLeaveTypes as $type) {
            $leaveTypeMap[$type['leave_type_id']] = $type['leave_type_name'];
        }

        foreach ($leaves as &$leave) {
            $leave['leave_type_name'] = $leaveTypeMap[$leave['leave_type_id']] ?? 'Unknown';
        }
        unset($leave); 

        $totalLeaves     = $this->leaveModel->getTotalLeaves($employee_id);
        $usedLeaves      = $this->leaveModel->getUsedLeaves($employee_id);
        $remainingLeaves = $totalLeaves - $usedLeaves;

        $content = __DIR__ . '/../views/leave-request/main-content.php';
        require __DIR__ . '/../views/leave-request/index.php';
    }

    public function indexAdmin()
    {
        $user_id = $_SESSION['user_id'] ?? null;
        var_dump($user_id);
        die;
        if (!$user_id) {
            die('User not logged in.');
        }
        $employee = $this->employeeModel->findByUserId($user_id);
        $employee_id = $employee['id'] ?? null;

        $leaveTypeMap = [];
        foreach ($allLeaveTypes as $type) {
            $leaveTypeMap[$type['leave_type_id']] = $type['leave_type_name'];
        }
        $totalLeaves = $this->leaveModel->getTotalLeaves($employee_id);
        $usedLeaves  = $this->leaveModel->getUsedLeaves($employee_id);
        $remainingLeaves = $totalLeaves - $usedLeaves;

        $leaves = $this->leaveModel->getLeavesByEmployee($employee_id);

        $content = __DIR__ . '/../views/leave-request/main-content.php';
        require __DIR__ . '/../views/leave-request/index.php';
    }

    public function store()
    {
        $user_id = $_POST['user_id'] ?? null;
        if (!$user_id) {
            die('User not logged in.');
        }

        $employee = $this->employeeModel->findByUserId($user_id);
        $employee_id = $employee['id'] ?? null;

        if (!$employee_id) {
            $_SESSION['error'] = "Employee record not found.";
            header("Location: index.php?url=employee-leave-request");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=employee-leave-request");
            exit;
        }

        // Fetch submitted fields
        $leave_type_id = $_POST['leave_type_id'] ?? null;
        $start_date    = $_POST['start_date'] ?? '';
        $end_date      = $_POST['end_date'] ?? '';
        $reason        = trim($_POST['reason'] ?? '');

        // Validate all fields
        if (!$leave_type_id || !$start_date || !$end_date || !$reason) {
            $_SESSION['error'] = "All fields are required.";
            header("Location: index.php?url=employee-leave-request");
            exit;
        }

        if ($start_date > $end_date) {
            $_SESSION['error'] = "Start date cannot be later than end date.";
            header("Location: index.php?url=employee-leave-request");
            exit;
        }

        // Validate leave type exists
        $leaveTypeModel = new LeaveType();
        $leaveType = $leaveTypeModel->getById($leave_type_id);
        if (!$leaveType) {
            $_SESSION['error'] = "Selected leave type is invalid.";
            header("Location: index.php?url=employee-leave-request");
            exit;
        }

        try {
            $this->leaveModel->create([
                'employee_id'       => $employee_id,
                'leave_type_id'     => $leave_type_id,
                'start_date'        => $start_date,
                'end_date'          => $end_date,
                'details'           => $reason,
                'supporting_document' => null
            ]);

            $_SESSION['success'] = "Leave request submitted successfully!";
        } catch (Exception $e) {
            error_log("Leave submission failed: " . $e->getMessage());
            $_SESSION['error'] = "Failed to submit leave request. Please try again later.";
        }

        header("Location: index.php?url=employee-leave-request");
        exit;
    }
}
