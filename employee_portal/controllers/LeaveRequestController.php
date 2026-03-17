<?php
require_once __DIR__ . '/../Models/LeaveRequest.php';

class LeaveRequestController
{
    public function index()
    {
        $title = "Leave Requests";
        require_once __DIR__ . '/../models/LeaveRequest.php';
        $leaveModel = new LeaveRequest();
        $leaveRequests = $leaveModel->all();
        $content = __DIR__ . '/../views/leave-request/main-content.php';
        require __DIR__ . '/../views/leave-request/index.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Invalid request method.";
            header("Location: index.php?url=leave-requests-index");
            exit;
        }

        $type_of_leave = trim($_POST['type_of_leave'] ?? '');
        $start_date = $_POST['start_date'] ?? null;
        $end_date = $_POST['end_date'] ?? null;
        $details = trim($_POST['details'] ?? '');
        $reject_reason = trim($_POST['reject_reason'] ?? '');
        $date_submitted = date('Y-m-d');
        $supporting_document = null;

        if (!empty($_FILES['supporting_document']['name'])) {
            $uploadDir = __DIR__ . '/../public/uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . '_' . basename($_FILES['supporting_document']['name']);
            $targetFile = $uploadDir . $fileName;

            if (!move_uploaded_file($_FILES['supporting_document']['tmp_name'], $targetFile)) {
                $_SESSION['error'] = "Failed to upload supporting document.";
                header("Location: index.php?url=leave-requests-index");
                exit;
            }

            $supporting_document = $fileName;
        }

        try {
            $model = new LeaveRequest();

            $data = [
                'name' => null,
                'type_of_leave' => $type_of_leave,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'details' => $details,
                'supporting_document' => $supporting_document,
                'date_submitted' => $date_submitted,
                'status' => 'Pending',
                'updated_at' => null,
                'reject_reason' => $reject_reason
            ];

            $inserted = $model->create($data);

            if ($inserted) {
                $_SESSION['success'] = "Leave request submitted successfully.";
            } else {
                $_SESSION['error'] = "Failed to submit leave request.";
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = "An error occurred.";
        }

        header("Location: index.php?url=leave-requests-index");
        exit;
    }

    public function createLeave()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Invalid request method.";
            header("Location: index.php?url=dashboard");
            exit;
        }

        $type_of_leave = trim($_POST['type_of_leave'] ?? '');
        $start_date = $_POST['start_date'] ?? null;
        $end_date = $_POST['end_date'] ?? null;
        $details = trim($_POST['details'] ?? '');
        $reject_reason = trim($_POST['reject_reason'] ?? '');
        $date_submitted = date('Y-m-d');
        $supporting_document = null;

        if (!empty($_FILES['supporting_document']['name'])) {
            $uploadDir = __DIR__ . '/../public/uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . '_' . basename($_FILES['supporting_document']['name']);
            $targetFile = $uploadDir . $fileName;

            if (!move_uploaded_file($_FILES['supporting_document']['tmp_name'], $targetFile)) {
                $_SESSION['error'] = "Failed to upload supporting document.";
                header("Location: index.php?url=dashboard");
                exit;
            }

            $supporting_document = $fileName;
        }

        try {
            $model = new LeaveRequest();

            $data = [
                'name' => null,
                'type_of_leave' => $type_of_leave,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'details' => $details,
                'supporting_document' => $supporting_document,
                'date_submitted' => $date_submitted,
                'status' => 'Pending',
                'updated_at' => null,
                'reject_reason' => $reject_reason
            ];

            $inserted = $model->create($data);

            if ($inserted) {
                $_SESSION['success'] = "Leave request submitted successfully.";
            } else {
                $_SESSION['error'] = "Failed to submit leave request.";
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = "An error occurred.";
        }

        header("Location: index.php?url=dashboard");
        exit;
    }

    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Invalid request method.";
            header("Location: index.php?url=leave-requests-index");
            exit;
        }

        try {
            $leaveId = $_POST['leave_id'] ?? null;
            $status = $_POST['status'] ?? null;
            $rejectReason = trim($_POST['reject_reason'] ?? '');
            $adminName = $_SESSION['user']['name'] ?? 'Admin';

            if (!$leaveId || !$status) {
                throw new Exception("Invalid leave request data.");
            }

            $leaveRequest = new LeaveRequest();
            $updated = $leaveRequest->updateStatus($leaveId, $status, $adminName, $rejectReason);

            if ($updated) {
                $_SESSION['success'] = "Leave request status updated successfully.";
            } else {
                $_SESSION['error'] = "Failed to update leave request status.";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: index.php?url=leave-requests-index");
        exit;
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Invalid request method.";
            header("Location: index.php?url=leave-requests-index");
            exit;
        }

        try {
            $leaveId = $_POST['leave_id'] ?? null;

            if (!$leaveId) {
                throw new Exception("Leave request ID is required.");
            }

            $leaveRequest = new LeaveRequest();
            $deleted = $leaveRequest->delete($leaveId);

            if ($deleted) {
                $_SESSION['success'] = "Leave request deleted successfully.";
            } else {
                $_SESSION['error'] = "Failed to delete leave request.";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: index.php?url=leave-requests-index");
        exit;
    }
}
