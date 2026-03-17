<?php

require_once __DIR__ . '/../Models/Employee.php';
require_once __DIR__ . '/../Core/Controller.php';

class EmployeeController extends Controller
{
    public function update()
    {
        session_start(); 

        try {
            $employeeModel = new Employee();

            $result = $employeeModel->update($_POST);

            if ($result) {
                $_SESSION['success'] = "Employee information updated successfully.";
            } else {
                $_SESSION['error'] = "Failed to update employee information.";
            }
        } catch (Exception $e) {
            error_log("Employee Update Error: " . $e->getMessage());

            $_SESSION['error'] = "Something went wrong while updating. Please try again.";
        }

        header("Location: index.php?url=dashboard");
        exit;
    }
}
