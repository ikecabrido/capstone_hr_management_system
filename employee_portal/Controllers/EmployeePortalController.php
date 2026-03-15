<?php
require_once __DIR__ . '/../Models/Employee.php';

class EmployeePortalController
{
    public function index()
    {
        $title = "Employee Portal";
        $model = new RequestType();
        $requestTypes = $model->all();

        $content = __DIR__ . '/../views/employee-portal/main-content.php';

        require __DIR__ . '/../views/employee-portal/index.php';
    }
}
