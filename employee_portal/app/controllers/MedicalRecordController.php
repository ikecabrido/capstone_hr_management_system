<?php
require_once __DIR__ . '/../models/MedicalRecord.php';
require_once __DIR__ . '/../models/Employee.php';
class MedicalRecordController
{
    private $medicalRecordModel;
    private $employeeModel;
    public function __construct()
    {
        $this->medicalRecordModel = new MedicalRecord();
        $this->employeeModel = new Employee();
    }
    public function index()
    {
        $user_id = $_SESSION['user_id'];
        $employee = $this->employeeModel->findByUserId($user_id);
        $employee_id = $employee['employee_id'];
        $records = $this->medicalRecordModel->getByEmployee($employee_id);

        $title = "Medical Records";
        $content = __DIR__ . '/../views/medical-records/main-content.php';
        require __DIR__ . '/../views/employee-portal/index.php';
    }
}
