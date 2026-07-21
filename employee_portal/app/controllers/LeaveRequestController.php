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
        $employee_id = $employee['employee_id'] ?? null;

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
        $details       = trim($_POST['details'] ?? '');

        // Validate all fields
        if (!$leave_type_id || !$start_date || !$end_date || !$reason) {
            $_SESSION['error'] = "All required fields must be filled.";
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

        // Validate 2-week advance for vacation leave
        $leaveTypeName = strtolower($leaveType['leave_type_name'] ?? '');
        if (strpos($leaveTypeName, 'vacation') !== false) {
            $startDateTime = new DateTime($start_date);
            $today = new DateTime('today');
            $daysAdvance = $today->diff($startDateTime)->days;
            
            if ($daysAdvance < 14) {
                $_SESSION['error'] = "Vacation leave must be filed at least 2 weeks in advance.";
                header("Location: index.php?url=employee-leave-request");
                exit;
            }
        }

        try {
            // Prepare documents data
            $documentPaths = [];

            // Handle document uploads first
            if (!empty($_FILES['documents']['name'][0])) {
                $uploadDir = __DIR__ . '/../../uploads/leave_documents/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                foreach ($_FILES['documents']['tmp_name'] as $key => $tmpName) {
                    if (!empty($tmpName) && $_FILES['documents']['error'][$key] === UPLOAD_ERR_OK) {
                        $fileName = basename($_FILES['documents']['name'][$key]);
                        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        
                        // Validate file type
                        $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
                        if (!in_array($fileType, $allowedTypes)) {
                            continue;
                        }

                        // Validate file size (5MB max)
                        if ($_FILES['documents']['size'][$key] > 5 * 1024 * 1024) {
                            continue;
                        }

                        // Create unique filename
                        $uniqueFileName = time() . '_' . md5($fileName . rand()) . '.' . $fileType;
                        $filePath = $uploadDir . $uniqueFileName;

                        if (move_uploaded_file($tmpName, $filePath)) {
                            $documentPaths[] = 'uploads/leave_documents/' . $uniqueFileName;
                        }
                    }
                }
            }

            // Create leave request with documents
            $requestData = [
                'employee_id'   => $employee_id,
                'leave_type_id' => $leave_type_id,
                'start_date'    => $start_date,
                'end_date'      => $end_date,
                'reason'        => $reason,
                'details'       => $details,
                'documents'     => !empty($documentPaths) ? json_encode($documentPaths) : null,
                'status'        => 'Pending'
            ];

            $leaveRequestId = $this->leaveModel->create($requestData);

            $_SESSION['success'] = "Leave request submitted successfully! Your request has been sent for approval.";
        } catch (Exception $e) {
            error_log("Leave submission failed: " . $e->getMessage());
            $_SESSION['error'] = "Failed to submit leave request. Please try again later.";
        }

        header("Location: index.php?url=employee-leave-request");
        exit;
    }
}
