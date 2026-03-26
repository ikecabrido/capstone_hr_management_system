<?php
require_once __DIR__ . '/../models/Leave.php';
require_once __DIR__ . '/../models/Employee.php';


class LeaveRequestController
{
    private $leaveModel;
    private $employeeModel;
    public function __construct()
    {
        $this->leaveModel = new Leave();
        $this->employeeModel = new Employee();
    }

    public function index()
    {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            die('User not logged in.');
        }
        $employee = $this->employeeModel->findByUserId($user_id);
        $employee_id = $employee['id'] ?? null;

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
            $_SESSION['error'] = "User not logged in.";
            header("Location: index.php?url=employee-leave-request");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=employee-leave-request");
            exit;
        }

        $leave_type  = trim($_POST['leave_type'] ?? '');
        $start_date  = $_POST['start_date'] ?? '';
        $end_date    = $_POST['end_date'] ?? '';
        $reason      = trim($_POST['reason'] ?? '');

        if (!$leave_type || !$start_date || !$end_date || !$reason) {
            $_SESSION['error'] = "All fields are required.";
            header("Location: index.php?url=employee-leave-request");
            exit;
        }

        if ($start_date > $end_date) {
            $_SESSION['error'] = "Start date cannot be later than end date.";
            header("Location: index.php?url=employee-leave-request");
            exit;
        }

        try {
            $this->leaveModel->create([
                'employee_id' => $employee_id,
                'leave_type'  => $leave_type,
                'start_date'  => $start_date,
                'end_date'    => $end_date,
                'reason'      => $reason,
                'status'      => 'Pending'
            ]);

            $_SESSION['success'] = "Leave request submitted successfully!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Failed to submit leave request.";
        }

        header("Location: index.php?url=employee-leave-request");
        exit;
    }
}
