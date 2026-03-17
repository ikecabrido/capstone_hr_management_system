<?php

require_once __DIR__ . '/../Models/Request.php';

class RequestController
{
    public function index()
    {
        $title = "Manage Request";
        $model = new Request();
        $requests = $model->all();

        $content = __DIR__ . '/../views/request/main-content.php';

        require __DIR__ . '/../views/request/index.php';
    }
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Invalid request method.";
            header("Location: index.php?url=dashboard");
            exit;
        }

        try {

            $userId = $_SESSION['user']['id'] ?? 1;

            if (
                empty($_POST['request_type_id']) ||
                empty($_POST['title']) ||
                empty($_POST['details'])
            ) {
                throw new Exception("All required fields must be filled.");
            }

            $requestTypeModel = new RequestType();
            $type = $requestTypeModel->find($_POST['request_type_id']);

            if (!$type) {
                throw new Exception("Invalid request type.");
            }

            if ($type['requires_attachment'] && empty($_FILES['attachment']['name'])) {
                throw new Exception("Attachment is required.");
            }

            $attachmentName = null;

            if (!empty($_FILES['attachment']['name'])) {

                $uploadDir = __DIR__ . "/../public/uploads/";

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = basename($_FILES['attachment']['name']);
                $attachmentName = time() . "_" . $fileName;

                $targetPath = $uploadDir . $attachmentName;

                if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
                    throw new Exception("Failed to upload attachment.");
                }
            }

            $data = [
                'user_id' => $userId,
                'request_type_id' => $_POST['request_type_id'],
                'title' => trim($_POST['title']),
                'details' => trim($_POST['details']),
                'attachment' => $attachmentName
            ];

            $requestModel = new Request();
            $requestModel->create($data);

            $_SESSION['success'] = "Request submitted successfully.";
        } catch (Exception $e) {

            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: index.php?url=dashboard");
        exit;
    }
    public function updateRequestStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=request-index");
            exit;
        }

        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;
        $remarks = trim($_POST['admin_remarks'] ?? '');

        if (!$id) {
            $_SESSION['error'] = "Invalid request ID.";
            header("Location: index.php?url=request-index");
            exit;
        }

        $allowedStatuses = ['Pending', 'Approved', 'Rejected', 'Cancelled', 'Completed'];

        if (!$status || !in_array($status, $allowedStatuses)) {
            $_SESSION['error'] = "Invalid status value.";
            header("Location: index.php?url=request-index");
            exit;
        }

        $requestModel = new Request();

        $updated = $requestModel->updateStatusAndRemarks($id, $status, $remarks);

        if ($updated) {
            $_SESSION['success'] = "Request status updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update request.";
        }

        header("Location: index.php?url=request-index");
        exit;
    }

    public function download()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            die("Invalid file request.");
        }

        $requestModel = new Request();
        $request = $requestModel->find($id);

        if (!$request || empty($request['attachment'])) {
            die("File not found.");
        }

        $fileName = trim($request['attachment']);
        $filePath = $_SERVER['DOCUMENT_ROOT']
            . "/capstone_hr_management_system/employee_portal/public/uploads/"
            . $fileName;

        if (!file_exists($filePath)) {
            die("File missing: " . $filePath);
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        $mime = mime_content_type($filePath) ?: "application/octet-stream";

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: public');
        header('Expires: 0');

        readfile($filePath);
        exit;
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Invalid request method.";
            header("Location: index.php?url=request-index");
            exit;
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            $_SESSION['error'] = "Invalid request ID.";
            header("Location: index.php?url=request-index");
            exit;
        }

        $requestModel = new Request();

        $request = $requestModel->find($id);
        if (!$request) {
            $_SESSION['error'] = "Request not found.";
            header("Location: index.php?url=request-index");
            exit;
        }

        if (!empty($request['attachment'])) {
            $filePath = $_SERVER['DOCUMENT_ROOT'] . "/capstone_hr_management_system/employee_portal/public/uploads/" . $request['attachment'];
            if (file_exists($filePath)) {
                @unlink($filePath); 
            }
        }

        $deleted = $requestModel->delete($id);

        if ($deleted) {
            $_SESSION['success'] = "Request deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete request.";
        }

        header("Location: index.php?url=request-index");
        exit;
    }
}
