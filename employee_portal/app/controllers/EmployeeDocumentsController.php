<?php
require_once __DIR__ . '/../models/EmployeeDocuments.php';
require_once __DIR__ . '/../models/Departments.php';
require_once __DIR__ . '/../models/Employee.php';

class EmployeeDocumentsController
{
    public function employeeIndex()
    {
        $employeeDocumentsModel = new EmployeeDocuments();
        $departmentsModel = new Departments();
        $employeeModel = new Employee();

        $departmentId = $_GET['department'] ?? null;

        if ($departmentId) {
            $empdocs = $employeeDocumentsModel->getByDepartment($departmentId);
        } else {
            $empdocs = $employeeDocumentsModel->all();
        }

        $departments = $departmentsModel->all();
        $employees = $employeeModel->all();

        $title = "Employee Documents";
        $content = __DIR__ . '/../views/employee-documents/main-content.php';
        require __DIR__ . '/../views/employee-documents/index.php';
    }

    public function create()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "index.php?url=employee-documents-index"));
                exit;
            }

            $title        = trim($_POST['title'] ?? '');
            $description  = trim($_POST['description'] ?? '');
            $department   = $_POST['department'] ?? null;
            $submit_by    = $_POST['submit_by'] ?? null;
            $approver_id  = $_POST['approver_id'] ?? null;

            $attachmentPath = null;

            if (!empty($_FILES['attachment']['name'])) {
                $uploadDir = __DIR__ . "/../../public/uploads/";

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileTmp  = $_FILES['attachment']['tmp_name'];
                $fileName = $_FILES['attachment']['name'];
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
                'title'       => $title,
                'description' => $description,
                'remarks'     => null,
                'submit_by'   => $submit_by ?: null,
                'department'  => $department ?: null,
                'approver_id' => $approver_id ?: null,
                'file_path'   => $attachmentPath,
                'decision'    => 'Pending'
            ];

            $documentModel = new EmployeeDocuments();
            $documentModel->create($data);

            $_SESSION['success'] = "Document submitted successfully.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage() ?: "Something went wrong while submitting.";
        }

        $redirectTo = $_SERVER['HTTP_REFERER'] ?? "index.php?url=employee-documents-index";
        header("Location: $redirectTo");
        exit;
    }

public function adminDocsIndex()
{
    $employeeDocumentsModel = new EmployeeDocuments();

    try {
        $empdocs = $employeeDocumentsModel->all();

        if (!is_array($empdocs)) {
            $empdocs = [];
        }

        $title   = "Admin - Employee Documents";
        $content = __DIR__ . '/../views/admin/employee-documents/main-content.php';

        require __DIR__ . '/../views/admin/employee-documents/index.php';

    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        $empdocs = [];
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        $empdocs = [];
    }
}
}
