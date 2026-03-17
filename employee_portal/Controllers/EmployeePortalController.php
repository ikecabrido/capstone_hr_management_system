<?php
require_once __DIR__ . '/../Models/Employee.php';

class EmployeePortalController
{
    public function index()
    {
        $title = "Employee Portal";

        $requestTypeModel = new RequestType();
        $employeeModel = new Employee();

        $requestTypes = $requestTypeModel->all();
        $employees = $employeeModel->all();

        $employee = null;
        foreach ($employees as $emp) {
            if ($emp['employee_id'] === 'EMP001') {
                $employee = $emp;
                break;
            }
        }

        if (!$employee && !empty($employees)) {
            $employee = $employees[0];
        }

        $content = __DIR__ . '/../views/employee-portal/main-content.php';
        require __DIR__ . '/../views/employee-portal/index.php';
    }
}
