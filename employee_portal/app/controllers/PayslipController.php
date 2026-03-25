<?php
require_once __DIR__ . '/../models/Payslip.php';

class PayslipController
{
    private $payslipModel;

    public function __construct()
    {
        $this->payslipModel = new Payslip();
    }

    public function index()
    {
        $employee_id = AuthController::getCurrentUserId();

        $records = $this->payslipModel->getByEmployee($employee_id);

        $content = __DIR__ . '/../views/payslips/main-content.php';
        require __DIR__ . '/../views/payslips/index.php';
    }

    public function viewPayslip()
    {
        if (!isset($_GET['id'])) {
            die("Payslip ID missing.");
        }

        $payslipId = (int) $_GET['id'];

        
        $payslip = $this->payslipModel->viewPayslip($payslipId);

        if (!$payslip) {
            die("Payslip not found.");
        }

        $content = __DIR__ . '/../views/payslips/view_payslip.php';
        require __DIR__ . '/../views/payslips/index.php';
    }
}
