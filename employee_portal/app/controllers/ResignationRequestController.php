<?php

require_once __DIR__ . '/../models/ResignationRequest.php';
require_once __DIR__ . '/../models/Users.php';
require_once __DIR__ . '/../models/Employee.php';

class ResignationRequestController
{
    private $userModel;
    private $employeeModel;
    private $resignationRequestModel;


    public function __construct()
    {
        $this->userModel = new Users();
        $this->employeeModel = new Employee();
        $this->resignationRequestModel = new ResignationRequest();
    }

    public function adminIndex()
    {
        $resignationRequests = $this->resignationRequestModel->all();
        $employees = $this->employeeModel->all();

        $title = "Resignation Request";
        $content = __DIR__ . '/../views/admin/resignation-request/main-content.php';
        require __DIR__ . '/../views/admin/index.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=resignation-request');
            exit;
        }

        $user = $this->userModel->findById($_SESSION['user_id']);

        $redirect = ($user && $user['is_admin'])
            ? 'index.php?url=admin-resignation-request'
            : 'index.php?url=resignation-request';

        $employee = $this->employeeModel->findByUserId($_SESSION['user_id']);

        if (!$employee) {
            $_SESSION['error'] = 'Employee record not found.';
            header("Location: {$redirect}");
            exit;
        }

        $attachment = null;

        if (!empty($_FILES['attachment']['name']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {

            $uploadDir = __DIR__ . '/../../assets/uploads/resignation_letters/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $extension = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));

            if (in_array($extension, ['pdf', 'doc', 'docx'])) {

                $filename = uniqid('resignation_') . '.' . $extension;

                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $filename)) {
                    $attachment = 'assets/uploads/resignation_letters/' . $filename;
                }
            }
        }

        $success = $this->resignationRequestModel->create([
            'employee_id' => $employee['employee_id'],
            'resignation_type' => trim($_POST['resignation_type']),
            'employee_remarks' => trim($_POST['employee_remarks'] ?? null),
            'resignation_reason' => trim($_POST['resignation_reason']),
            'intended_last_working_day' => $_POST['intended_last_working_day'],
            'status' => "Pending",
            'attachment' => $attachment
        ]);

        $_SESSION[$success ? 'success' : 'error'] = $success
            ? 'Resignation request submitted successfully.'
            : 'Unable to submit resignation request.';

        header("Location: {$redirect}");
        exit;
    }
    public function updateRemarks()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=admin-resignation-request');
            exit;
        }

        $data = [
            'resignation_id' => $_POST['resignation_id'],
            'hr_remarks'     => trim($_POST['hr_remarks'])
        ];

        if ($this->resignationRequestModel->updateRemarks($data)) {
            $_SESSION['success'] = 'Remarks updated successfully.';
        } else {
            $_SESSION['error'] = 'Failed to update remarks.';
        }

        header('Location: index.php?url=admin-resignation-request');
        exit;
    }
    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=admin-resignation-request');
            exit;
        }

        $data = [
            'resignation_id' => $_POST['resignation_id'],
            'status'         => $_POST['status'],
            'reviewed_by'    => $_SESSION['user_id'],
            'reviewed_at'    => date('Y-m-d H:i:s')
        ];

        if ($this->resignationRequestModel->updateStatus($data)) {
            $_SESSION['success'] = 'Status updated successfully.';
        } else {
            $_SESSION['error'] = 'Failed to update status.';
        }

        header('Location: index.php?url=admin-resignation-request');
        exit;
    }
    public function index()
    {
        $employee = $this->employeeModel->findByUserId($_SESSION['user_id']);

        $resignationRequests = [];

        if ($employee) {
            $resignationRequests = $this->resignationRequestModel
                ->getByEmployee($employee['employee_id']);
        }

        $title = "Benefits & Government Contributions";
        $content = __DIR__ . '/../views/resignation-request/main-content.php';

        require __DIR__ . '/../views/employee-portal/index.php';
    }
    public function cancel()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=resignation-request');
            exit;
        }

        $success = $this->resignationRequestModel->updateStatus([
            'resignation_id' => $_POST['resignation_id'],
            'status' => 'Cancelled',
            'hr_remarks' => null,
            'reviewed_by' => null,
            'reviewed_at' => null
        ]);

        $_SESSION[$success ? 'success' : 'error'] =
            $success
            ? 'Resignation request cancelled successfully.'
            : 'Failed to cancel resignation request.';

        header('Location: index.php?url=resignation-request');
        exit;
    }
}
