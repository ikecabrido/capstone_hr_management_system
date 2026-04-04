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
        if ($this->model->updatePayrollClearanceStatus($clearanceId, 'approved', $approvedBy, $comments)) {
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
}
