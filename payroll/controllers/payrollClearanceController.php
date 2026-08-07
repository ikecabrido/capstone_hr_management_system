<?php
require_once __DIR__ . '/../../auth/database.php';
require_once __DIR__ . '/../models/payrollModel.php';

class PayrollClearanceController
{
    private PDO $db;
    private PayrollModel $model;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->model = new PayrollModel($this->db);
    }

    public function getEligibleSettlements(): array
    {
        return $this->model->getExitSettlementsEligibleForClearance();
    }

    public function getPendingClearances(): array
    {
        return $this->model->getPayrollClearancesByStatus('pending');
    }

    public function getAllClearances(): array
    {
        return $this->model->getAllPayrollClearances();
    }

    public function createClearanceRequest(int $settlementId, int $requestedBy): array
    {
        $existing = $this->model->getPayrollClearanceBySettlementId($settlementId);
        if ($existing && $existing['status'] === 'pending') {
            return ['success' => false, 'message' => 'A clearance request is already pending for this settlement.'];
        }

        $requestId = $this->model->createPayrollClearanceRequest($settlementId, $requestedBy);
        if ($requestId) {
            return ['success' => true, 'message' => 'Payroll clearance request created successfully.', 'id' => $requestId];
        }

        return ['success' => false, 'message' => 'Unable to create payroll clearance request.'];
    }

    public function approveClearance(int $clearanceId, int $approvedBy, ?string $comments = null): array
    {
        $clearance = $this->model->getPayrollClearanceById($clearanceId);
        $finalAmount = null;

        if ($clearance) {
            $settlement = $this->getSettlementDetails((int)$clearance['settlement_id']);
            if ($settlement) {
                $finalAmount = (float)($settlement['payroll_final_amount'] ?? $settlement['net_payable'] ?? 0);
            }
        }

        if ($this->model->updatePayrollClearanceStatus($clearanceId, 'approved', $approvedBy, $comments, $finalAmount)) {
            return ['success' => true, 'message' => 'Payroll clearance approved successfully.'];
        }

        return ['success' => false, 'message' => 'Unable to approve payroll clearance.'];
    }

    public function rejectClearance(int $clearanceId, int $approvedBy, ?string $comments = null): array
    {
        if ($this->model->updatePayrollClearanceStatus($clearanceId, 'rejected', $approvedBy, $comments)) {
            return ['success' => true, 'message' => 'Payroll clearance rejected successfully.'];
        }

        return ['success' => false, 'message' => 'Unable to reject payroll clearance.'];
    }

    public function getClearanceDetails(int $clearanceId): ?array
    {
        return $this->model->getPayrollClearanceById($clearanceId);
    }

    public function getSettlementDetails(int $settlementId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                es.id AS settlement_id,
                es.employee_id,
                e.full_name,
                e.position,
                e.department,
                er.last_working_date,
                es.net_payable,
                es.settlement_date,
                es.gratuity,
                es.notice_pay,
                es.outstanding_loans,
                es.other_deductions,
                es.status,
                es.payroll_preview_data,
                es.payroll_clearance_status,
                es.payroll_notes,
                es.payroll_final_amount
            FROM exit_employee_settlements es
            LEFT JOIN exit_resignations er ON es.resignation_id = er.id
            JOIN employees e ON es.employee_id = e.employee_id
            WHERE es.id = :settlement_id
        ");
        $stmt->execute([':settlement_id' => $settlementId]);
        $settlement = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$settlement) {
            return null;
        }

        $settlement['adjustments'] = $this->getSettlementAdjustments($settlementId);

        if (!empty($settlement['payroll_preview_data'])) {
            $decodedPreview = json_decode($settlement['payroll_preview_data'], true);
            if (is_array($decodedPreview)) {
                $settlement['payroll_preview'] = $decodedPreview;
            }
        }

        return $settlement;
    }

    private function getSettlementAdjustments(int $settlementId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM exit_settlement_adjustments WHERE settlement_id = :settlement_id ORDER BY id ASC");
        $stmt->execute([':settlement_id' => $settlementId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function calculateSettlementPreview(int $settlementId, int $employeeId): array
    {
        return $this->calculateFinalSettlement($settlementId, $employeeId);
    }

    public function calculateFinalSettlement(int $settlementId, int $employeeId): array
    {
        return $this->model->calculateExitPayslip($employeeId, $settlementId);
    }
}
