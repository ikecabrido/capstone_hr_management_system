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

    /**
     * Admin List
     */
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

        header('Location: index.php?url=admin-benefits-and-gov-contrib');
        exit;
    }
    public function update($id, $data)
    {
        return $this->benefitsAndGovContribModel->update($id, $data);
    }

    /**
     * Delete Record
     */
    public function delete($id)
    {
        return $this->benefitsAndGovContribModel->delete($id);
    }

    /**
     * Employee View
     */
    public function employeeBenefits($employeeId)
    {
        return $this->benefitsAndGovContribModel->getByEmployee($employeeId);
    }
}
