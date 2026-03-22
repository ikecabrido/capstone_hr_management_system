<?php

require_once __DIR__ . '/../models/Grievance.php';

class EmployeeGrievanceController
{
    public function index()
    {
        $employee_id = Session::get('employee_id');

        $content = __DIR__ . '/../views/engagement-relations/main-content.php';
        require __DIR__ . '/../views/engagement-relations/index.php';
    }

    public function create()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "index.php?url=employee-grievance"));
                exit;
            }

            $employee_id  = $_POST['employee_id'] ?? null;
            $subject  = trim($_POST['subject'] ?? '');
            $description   = trim($_POST['description'] ?? '');
            $status   = $_POST['status'] ?? null;
            $anonymous   = $_POST['anonymous'] ?? null;
            $attachment_path    = $_POST['attachment_path'] ?? null;

            $attachmentPath = null;
            $created_at = date('Y-m-d H:i:s');

            if (!empty($_FILES['attachment_path']['name'])) {
                $uploadDir = __DIR__ . "/../../public/uploads/";

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileTmp  = $_FILES['attachment_path']['tmp_name'];
                $fileName = $_FILES['attachment_path']['name'];
                $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

                if (!in_array($fileExt, $allowed)) {
                    throw new Exception("Invalid file type.");
                }

                $newFileName = time() . "_" . bin2hex(random_bytes(4)) . "." . $fileExt;
                $targetPath  = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmp, $targetPath)) {
                    $attachmentPath = "uploads/" . $newFileName;
                } else {
                    throw new Exception("Failed to upload file.");
                }
            }

            $data = [
                'employee_id' => $employee_id,
                'subject' => $subject,
                'description' => $description,
                'assigned_to' => null,
                'status' => 'pending',
                'category' => $category ?? 'Workplace Conflict',
                'anonymous' => $anonymous,
                'attachment_path' => $attachmentPath
            ];

            $grievanceModel = new Grievance();
            $grievanceModel->create($data);

            $_SESSION['success'] = "Document submitted successfully.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage() ?: "Something went wrong while submitting.";
        }

        $redirectTo = $_SERVER['HTTP_REFERER'] ?? "index.php?url=employee-grievance";
        header("Location: $redirectTo");
        exit;
    }
}
