<?php

require_once __DIR__ . '/../models/BenefitsAndGovContrib.php';
require_once __DIR__ . '/../models/Employee.php';


class BenefitsAndGovContribController
{
    private $benefitsAndGovContribModel;
    private $employeeModel;

    public function __construct()
    {
        $this->benefitsAndGovContribModel = new BenefitsAndGovContrib();
        $this->employeeModel = new Employee();
    }
    public function index()
    {
        $benefitsAndGovContrib =
            $this->benefitsAndGovContribModel->all();
        $employeeList = $this->employeeModel->all();


        $title = "Benefits & Government Contributions";

        $content =
            __DIR__ . '/../views/admin/benefits-and-gov-contrib/main-content.php';

        require __DIR__ . '/../views/admin/index.php';
    }
    public function create()
    {
        try {

            $uploadDir = __DIR__ . '/../../public/uploads/benefits/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = null;
            $filePath = null;

            if (isset($_FILES['benefit_file']) && $_FILES['benefit_file']['error'] === UPLOAD_ERR_OK) {

                $extension = strtolower(pathinfo($_FILES['benefit_file']['name'], PATHINFO_EXTENSION));

                $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

                if (!in_array($extension, $allowed)) {
                    throw new Exception("Invalid file type. Only PDF, JPG, JPEG, and PNG files are allowed.");
                }

                $fileName = time() . '_' . basename($_FILES['benefit_file']['name']);
                $destination = $uploadDir . $fileName;

                if (!move_uploaded_file($_FILES['benefit_file']['tmp_name'], $destination)) {
                    throw new Exception("Failed to upload the file.");
                }

                $filePath = 'public/uploads/benefits/' . $fileName;
            }

            $data = [
                'employee_id' => $_POST['employee_id'],
                'record_type' => $_POST['record_type'],
                'period' => $_POST['period'],
                'description' => $_POST['description'],
                'file_name' => $fileName,
                'file_path' => $filePath,
                'uploaded_by' => $_SESSION['user_id']
            ];

            if (!$this->benefitsAndGovContribModel->create($data)) {
                throw new Exception("Failed to save the record to the database.");
            }

            $_SESSION['success'] = "Record uploaded successfully.";
        } catch (Exception $e) {

            $_SESSION['error'] = $e->getMessage();
            error_log($e->getMessage());
        }

        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {

            header('Location: index.php?url=admin-benefits-and-gov-contrib');
        } else {

            header('Location: index.php?url=benefits-and-gov-contrib');
        }
        exit;
    }
    public function update()
    {
        try {

            $id = $_POST['benefit_id'];

            $benefit = $this->benefitsAndGovContribModel->find($id);

            if (!$benefit) {
                throw new Exception("Record not found.");
            }

            $fileName = $benefit['file_name'];
            $filePath = $benefit['file_path'];

            $uploadDir = __DIR__ . '/../../public/uploads/benefits/';

            if (
                isset($_FILES['benefit_file']) &&
                $_FILES['benefit_file']['error'] === UPLOAD_ERR_OK
            ) {

                $extension = strtolower(pathinfo($_FILES['benefit_file']['name'], PATHINFO_EXTENSION));

                $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

                if (!in_array($extension, $allowed)) {
                    throw new Exception("Invalid file type.");
                }

                if (!empty($benefit['file_path']) && file_exists($benefit['file_path'])) {
                    unlink($benefit['file_path']);
                }

                $fileName = time() . '_' . basename($_FILES['benefit_file']['name']);

                $destination = $uploadDir . $fileName;

                if (!move_uploaded_file($_FILES['benefit_file']['tmp_name'], $destination)) {
                    throw new Exception("Failed to upload file.");
                }

                // Save relative path
                $filePath = 'public/uploads/benefits/' . $fileName;
            }

            $data = [
                'record_type' => $_POST['record_type'],
                'period' => $_POST['period'],
                'description' => $_POST['description'],
                'file_name' => $fileName,
                'file_path' => $filePath
            ];

            $this->benefitsAndGovContribModel->update($id, $data);

            $_SESSION['success'] = "Benefit record updated successfully.";
        } catch (Exception $e) {

            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: index.php?url=admin-benefits-and-gov-contrib');
        exit;
    }
    public function delete()
    {
        try {

            $id = $_POST['benefit_id'] ?? null;

            if (!$id) {
                throw new Exception("Invalid record.");
            }

            $benefit = $this->benefitsAndGovContribModel->find($id);

            if (!$benefit) {
                throw new Exception("Record not found.");
            }

            if (!empty($benefit['file_path'])) {

                $absolutePath = __DIR__ . '/../../' . $benefit['file_path'];

                if (file_exists($absolutePath)) {
                    unlink($absolutePath);
                }
            }

            $this->benefitsAndGovContribModel->delete($id);

            $_SESSION['success'] = "Benefit record deleted successfully.";
        } catch (Exception $e) {

            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: index.php?url=admin-benefits-and-gov-contrib');
        exit;
    }
    public function employeeBenefits()
    {
        $employeeID = $this->benefitsAndGovContribModel
            ->getEmployeeByUserId($_SESSION['user_id']);

        $benefits = $this->benefitsAndGovContribModel
            ->getEmployeeBenefits($_SESSION['user_id']);

        $title = "Employee Benefits and Government Contribution";
        $content = __DIR__ . '/../views/benefits-and-gov-contrib/main-content.php';

        require __DIR__ . '/../views/employee-portal/index.php';
    }
}
