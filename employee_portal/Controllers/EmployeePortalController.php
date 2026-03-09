<?php

require_once "../app/Models/User.php";
require_once "../app/Models/Employee.php";

require_once "../app/Core/Controller.php";
require_once "../app/Core/Auth.php";

class EmployeePortalController extends Controller
{
    public function index()
    {
        Auth::requireLogin();

        $userId = (int) $_SESSION['user']['id'];

        $employeeModel = new Employee();
        $employee = $employeeModel->findFullProfileByUserId($userId);

        if (!$employee) {
            die('Employee profile not found for this user');
        }

        $requestTypeModel = new RequestType();
        $requestTypes = $requestTypeModel->all();

        $this->view('user/employee/index', [
            'title' => 'Employee Portal | SEMSYS',
            'employee' => $employee,
            'requestTypes' => $requestTypes
        ]);
    }

    public function employeeModule()
    {
        Auth::requireLogin();

        $_SESSION['pageTitle'] = 'Employee Portal';

        $userId = (int) $_SESSION['user']['id'];

        $employeeModel = new Employee();
        $employee = $employeeModel->findFullProfileByUserId($userId);

        $contentFile = __DIR__ . '/../../Modules/employee-portal/index.php';

        if (!file_exists($contentFile)) {
            die("Employee portal index.php not found at: $contentFile");
        }

        ob_start();
        include $contentFile;
        $moduleContent = ob_get_clean();

        $layoutFile = __DIR__ . '/../../Modules/employee-portal/partials/layout.php';

        if (!file_exists($layoutFile)) {
            die("Layout file not found at: $layoutFile");
        }

        include $layoutFile;
    }
}
