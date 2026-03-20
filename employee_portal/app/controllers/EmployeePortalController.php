<?php

class EmployeePortalController
{
    public function index()
    {
        $title = "Employee Portal";

        $content = __DIR__ . '/../views/employee-portal/main-content.php';
        require __DIR__ . '/../views/employee-portal/index.php';
    }
}
