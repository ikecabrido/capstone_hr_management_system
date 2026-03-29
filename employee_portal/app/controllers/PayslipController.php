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

    public function exportCsv()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="payslips.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, [
            'Employee',
            'Payroll Run',
            'Gross Pay',
            'Deductions',
            'Net Pay',
            'Date Generated'
        ]);

        $employee_id = $_SESSION['employee_id'];
        $records = $this->payslipModel->getByEmployee($employee_id);

        foreach ($records as $r) {
            fputcsv($output, [
                $r['full_name'],
                $r['payroll_run_id'] ?? 'N/A',
                $r['gross_pay'] ?? 0,
                $r['total_deductions'] ?? 0,
                $r['net_pay'] ?? 0,
                !empty($r['generated_at'])
                    ? date('M d, Y', strtotime($r['generated_at']))
                    : 'N/A'
            ]);
        }

        fclose($output);
        exit;
    }
}
