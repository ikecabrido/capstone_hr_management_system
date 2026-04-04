<?php
require_once __DIR__ . '/../models/payrollModel.php';
require_once __DIR__ . '/../../auth/database.php';

class ExitPayslipController
{
    private PDO $db;
    private PayrollModel $payrollModel;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->payrollModel = new PayrollModel($this->db);
    }

    /**
     * Get list of employees with approved settlements ready for exit payslip
     */
    public function getEmployeesReadyForExitPayslip(): array
    {
        $stmt = $this->db->query("
            SELECT 
                es.id as settlement_id,
                es.employee_id,
                e.full_name,
                e.position,
                e.department,
                er.last_working_date,
                er.resignation_type,
                es.status,
                es.net_payable,
                es.settlement_date,
                es.gratuity,
                es.notice_pay
            FROM exit_employee_settlements es
            JOIN exit_resignations er ON es.resignation_id = er.id
            JOIN employees e ON es.employee_id = e.employee_id
            WHERE es.status = 'approved'
            AND NOT EXISTS (
                SELECT 1 FROM pr_payslips p
                WHERE p.settlement_id = es.id
                AND p.is_exit_settlement = 1
            )
            ORDER BY es.settlement_date ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Generate exit payslip for an employee
     */
    public function generateExitPayslip(int $employeeId, int $settlementId, int $periodId): array
    {
        try {
            // Verify settlement exists and is approved
            $stmtSettlement = $this->db->prepare("
                SELECT * FROM exit_employee_settlements
                WHERE id = :sid AND employee_id = :eid AND status = 'approved'
            ");
            $stmtSettlement->execute([
                ':sid' => $settlementId,
                ':eid' => $employeeId
            ]);
            $settlement = $stmtSettlement->fetch(PDO::FETCH_ASSOC);

            if (!$settlement) {
                return [
                    'success' => false,
                    'message' => 'Settlement not found or not approved'
                ];
            }

            // Check if payslip already exists
            $stmtCheck = $this->db->prepare("
                SELECT payslip_id FROM pr_payslips
                WHERE settlement_id = :sid AND is_exit_settlement = 1
            ");
            $stmtCheck->execute([':sid' => $settlementId]);
            if ($stmtCheck->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Exit payslip already generated for this settlement'
                ];
            }

            // Get or create payroll run
            $stmtRun = $this->db->prepare("
                SELECT run_id FROM pr_runs
                WHERE payroll_period_id = :pid
                LIMIT 1
            ");
            $stmtRun->execute([':pid' => $periodId]);
            $run = $stmtRun->fetch(PDO::FETCH_ASSOC);

            if (!$run) {
                $runId = $this->payrollModel->createPayrollRun($periodId);
            } else {
                $runId = $run['run_id'];
            }

            // Calculate exit payslip
            $payslipData = $this->payrollModel->calculateExitPayslip($employeeId, $settlementId);

            if (empty($payslipData)) {
                return [
                    'success' => false,
                    'message' => 'Failed to calculate exit payslip'
                ];
            }

            // Generate the payslip
            $payslipId = $this->payrollModel->generateExitPayslip($runId, $employeeId, $settlementId, $payslipData);

            if (!$payslipId) {
                return [
                    'success' => false,
                    'message' => 'Failed to generate exit payslip'
                ];
            }

            return [
                'success' => true,
                'message' => 'Exit payslip generated successfully',
                'payslip_id' => $payslipId,
                'data' => $payslipData
            ];
        } catch (Exception $e) {
            error_log("Exit payslip generation error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get exit payslip details
     */
    public function getExitPayslipDetails(int $payslipId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                p.*,
                e.full_name,
                e.position,
                e.department,
                es.gratuity,
                es.notice_pay,
                es.outstanding_loans,
                es.other_deductions,
                er.resignation_type,
                er.last_working_date
            FROM pr_payslips p
            JOIN employees e ON p.employee_id = e.employee_id
            LEFT JOIN exit_employee_settlements es ON p.settlement_id = es.id
            LEFT JOIN exit_resignations er ON es.resignation_id = er.id
            WHERE p.payslip_id = :pid AND p.is_exit_settlement = 1
        ");
        $stmt->execute([':pid' => $payslipId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Handle AJAX requests
     */
    public function handleAjaxRequest(string $action, array $data): array
    {
        switch ($action) {
            case 'get_ready_employees':
                return [
                    'success' => true,
                    'data' => $this->getEmployeesReadyForExitPayslip()
                ];

            case 'generate_exit_payslip':
                return $this->generateExitPayslip(
                    (int)$data['employee_id'],
                    (int)$data['settlement_id'],
                    (int)($data['period_id'] ?? 0)
                );

            case 'get_payslip_details':
                $details = $this->getExitPayslipDetails((int)$data['payslip_id']);
                return [
                    'success' => $details !== null,
                    'data' => $details,
                    'message' => $details ? 'Payslip found' : 'Payslip not found'
                ];

            default:
                return [
                    'success' => false,
                    'message' => 'Unknown action: ' . $action
                ];
        }
    }
}
