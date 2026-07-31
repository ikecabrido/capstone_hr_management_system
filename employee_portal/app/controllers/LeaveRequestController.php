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
        $user_id = AuthController::getCurrentUserId();
        $employee = $this->employeeModel->getByUserId($user_id);
        $employee_id = $employee['id'];

        $leaves = $this->leaveModel->getLeavesByEmployee($employee_id);
        $allLeaveTypes = $this->leaveTypeModel->getAllLeaveTypes();
        $leaveTypeMap = [];

        foreach ($allLeaveTypes as $type) {
            $leaveTypeMap[$type['leave_type_id']] = $type['leave_type_name'];
        }

        foreach ($leaves as &$leave) {
            $leave['leave_type_name'] = $leaveTypeMap[$leave['leave_type_id']] ?? 'Unknown';
        }
        unset($leave);

        $leaveBalances = $this->leaveModel->getLeaveBalances($employee_id);

        $content = __DIR__ . '/../views/leave-request/main-content.php';
        require __DIR__ . '/../views/employee-portal/index.php';
    }
    public function indexAdmin()
    {
        $leaves = $this->leaveModel->all();

        $pendingLeaves = array_filter($leaves, function ($leave) {
            return $leave['status'] == 'Pending';
        });

        $approvedLeaves = array_filter($leaves, function ($leave) {
            return $leave['status'] == 'Approved';
        });

        $allLeaveTypes = $this->leaveTypeModel->getAllLeaveTypes();

        $leaveTypeMap = [];
        $totalEntitlement = 0;

        foreach ($allLeaveTypes as $type) {
            $leaveTypeMap[$type['leave_type_id']] = $type['leave_type_name'];
            $totalEntitlement += (int)$type['days_per_year'];
        }

        $usedLeaves = 0;

        foreach ($approvedLeaves as $leave) {
            $start = new DateTime($leave['start_date']);
            $end = new DateTime($leave['end_date']);
            $usedLeaves += $start->diff($end)->days + 1;
        }

        $remainingLeaves = $totalEntitlement - $usedLeaves;

        $content = __DIR__ . '/../views/admin/leave-request/main-content.php';
        require __DIR__ . '/../views/admin/index.php';
    }
    public function store()
    {
        $user_id = $_POST['user_id'] ?? null;

        $employee = $this->employeeModel->findByUserId($user_id);
        $employee_id = $employee['employee_id'] ?? null;
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
    public function adminStore()
    {
        $user_id = $_POST['user_id'] ?? null;

        $employee = $this->employeeModel->findByUserId($user_id);
        $employee_id = $employee['employee_id'] ?? null;
        if (!$employee_id) {
            $_SESSION['error'] = "Employee record not found.";
            header("Location: index.php?url=admin-leave-request");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=admin-leave-request");
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
            header("Location: index.php?url=admin-leave-request");
            exit;
        }

        // Validate leave type exists
        $leaveTypeModel = new LeaveType();
        $leaveType = $leaveTypeModel->getById($leave_type_id);
        if (!$leaveType) {
            $_SESSION['error'] = "Selected leave type is invalid.";
            header("Location: index.php?url=admin-leave-request");
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

        header("Location: index.php?url=admin-leave-request");
        exit;
    }
    public function updateLeaveStatus()
    {
        $id = $_POST['leave_id'] ?? null;
        $status = $_POST['status'] ?? null;
        $reject_reason = trim($_POST['reject_reason'] ?? '');

        if (!$id || !in_array($status, ['Approved', 'Rejected'])) {
            $_SESSION['error'] = "Invalid request.";
            header("Location: index.php?url=admin-leave-request");
            exit;
        }

        // Only save reject reason if rejected
        if ($status === 'Approved') {
            $reject_reason = null;
        }

        if ($this->leaveModel->updateStatus($id, $status, $reject_reason)) {
            $_SESSION['success'] = "Leave request updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update leave request.";
        }

        header("Location: index.php?url=admin-leave-request");
        exit;
    }
}
