<?php
require_once __DIR__ . '/../Models/Employee.php';
require_once __DIR__ . '/../Core/Controller.php';

class EmployeePortalController
{
    public function index()
    {
        $title = "Employee Portal";

        $content = __DIR__ . '/../views/employee-portal/main-content.php';

        require __DIR__ . '/../layout.php';
    }
}
