<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Employee.php';

require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Core/Auth.php';

class EmployeeController extends Controller
{
    public function index()
    {
        Auth::requireAdmin();

        $employeeModel = new Employee();
        $employees = $employeeModel->all();

        $this->view('employee/index', [
            'title' => 'Employee Management | SEMSYS',
            'employees' => $employees
        ]);
    }
    public function viewEmployee()
    {
        Auth::requireAdmin();

        if (!isset($_GET['id'])) {
            header("Location: index.php?url=employee-index");
            exit;
        }

        $userId = (int) $_GET['id'];

        $employeeModel = new Employee();
        $employee = $employeeModel->findFullProfileByUserId($userId);

        if (!$employee) {
            $_SESSION['error'] = "Employee not found.";
            header("Location: index.php?url=employee-index");
            exit;
        }

        $this->view('employee/show-employee', [
            'title'    => 'Employee Profile | SEMSYS',
            'employee' => $employee
        ]);
    }

    public function editEmployee()
    {
        Auth::requireAdmin();

        if (!isset($_GET['id'])) {
            header("Location: index.php?url=employee-index");
            exit;
        }

        $userId = (int) $_GET['id'];
        $employeeModel = new Employee();

        $employee = $employeeModel->findFullProfileByUserId($userId);

        if (!$employee || !$employee['employee_id']) {
            $_SESSION['error'] = "Employee profile not found for this user.";
            header("Location: index.php?url=employee-index");
            exit;
        }

        $this->view('employee/edit-employee', [
            'title' => 'Edit Employee | SEMSYS',
            'employee' => $employee
        ]);
    }

}
