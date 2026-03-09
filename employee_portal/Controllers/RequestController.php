<?php

require_once __DIR__ . '/../Models/Request.php';
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Core/Auth.php';

class RequestController extends Controller
{
    public function index()
    {
        Auth::requireLogin();

        $requestModel = new Request();
        $requests = $requestModel->getAll();

        $this->view('admin/request/index', [
            'title' => 'All Requests Table',
            'requests' => $requests
        ]);
    }
    public function create()
    {
        Auth::requireLogin();

        try {

            $userId = $_SESSION['user']['id'];

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

                $uploadDir = __DIR__ . "/../../public/uploads/requests/";

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $attachmentName = time() . "_" . basename($_FILES['attachment']['name']);

                move_uploaded_file(
                    $_FILES['attachment']['tmp_name'],
                    $uploadDir . $attachmentName
                );
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

        header("Location: index.php?url=employee-portal");
        exit;
    }

    public function updateRequestStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=admin-requests");
            exit;
        }

        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? 'Pending';
        $remarks = $_POST['admin_remarks'] ?? '';

        if (!$id) {
            $_SESSION['error'] = "Invalid request ID.";
            header("Location: index.php?url=admin-requests");
            exit;
        }

        $allowedStatuses = ['Pending', 'Approved', 'Rejected', 'Cancelled', 'Completed'];

        if (!in_array($status, $allowedStatuses)) {
            $_SESSION['error'] = "Invalid status value.";
            header("Location: index.php?url=admin-requests");
            exit;
        }

        $requestModel = new Request();

        $updated = $requestModel->updateStatusAndRemarks($id, $status, $remarks);

        if ($updated) {
            $_SESSION['success'] = "Request updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update request.";
        }

        header("Location: index.php?url=admin-request");
        exit;
    }

    public function downloadRequest()
    {
        Auth::requireAdmin();

        $id = $_GET['id'] ?? null;

        if (!$id) {
            die('Invalid request.');
        }

        $requestModel = new Request();
        $request = $requestModel->find($id);

        if (!$request || empty($request['attachment'])) {
            die('File not found.');
        }

        $filePath = __DIR__ . "/../../public/uploads/requests/" . $request['attachment'];

        if (!file_exists($filePath)) {
            die('File does not exist.');
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));

        readfile($filePath);
        exit;
    }
}
