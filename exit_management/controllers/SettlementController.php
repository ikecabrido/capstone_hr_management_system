<?php

require_once __DIR__ . '/../models/SettlementModel.php';

class SettlementController extends ExitManagementController
{
    private SettlementModel $settlementModel;

    public function __construct()
    {
        parent::__construct();
        $this->settlementModel = new SettlementModel();
    }

    /**
     * Create settlement
     */
    public function createSettlement(array $data): array
    {
        try {
            // Validate required fields
            $required = ['employee_id', 'basic_salary', 'net_payable', 'settlement_date'];
            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    return ['success' => false, 'message' => "Field '$field' is required"];
                }
            }

            // Calculate total if not provided
            if (!isset($data['net_payable'])) {
                $data['net_payable'] = $this->settlementModel->calculateTotalSettlement($data);
            }

            $settlementId = $this->settlementModel->createSettlement($data);

            return [
                'success' => true,
                'message' => 'Settlement created successfully',
                'settlement_id' => $settlementId
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Calculate settlement components
     */
    public function calculateSettlement(array $data): array
    {
        try {
            $calculations = [];

            // Calculate gratuity if years of service provided
            if (isset($data['basic_salary']) && isset($data['years_of_service'])) {
                $calculations['gratuity'] = $this->settlementModel->calculateGratuity(
                    $data['basic_salary'],
                    $data['years_of_service']
                );
            }

            // Calculate PF
            if (isset($data['basic_salary'])) {
                $da = $data['da'] ?? 0;
                $calculations['provident_fund'] = $this->settlementModel->calculateProvidentFund(
                    $data['basic_salary'],
                    $da
                );
            }

            // Calculate notice pay
            if (isset($data['basic_salary']) && isset($data['notice_days'])) {
                $calculations['notice_pay'] = $this->settlementModel->calculateNoticePay(
                    $data['basic_salary'],
                    $data['notice_days']
                );
            }

            // Calculate total
            $total = $this->settlementModel->calculateTotalSettlement($data);
            $calculations['net_payable'] = $total;

            return [
                'success' => true,
                'calculations' => $calculations
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get settlement details
     */
    public function getSettlement(int $settlementId): array
    {
        $settlement = $this->settlementModel->getSettlementById($settlementId);

        if (!$settlement) {
            return ['error' => 'Settlement not found'];
        }

        return $settlement;
    }

    /**
     * Approve settlement
     */
    public function approveSettlement(int $settlementId, int $approvedBy): array
    {
        try {
            $success = $this->settlementModel->updateSettlementStatus($settlementId, 'approved', $approvedBy);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Settlement approved successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to approve settlement'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get pending settlements
     */
    public function getPendingSettlements(): array
    {
        return $this->settlementModel->getPendingSettlements();
    }

    /**
     * Get all settlements
     */
    public function getSettlements(): array
    {
        return $this->settlementModel->getAllSettlements();
    }

    /**
     * Print settlement (placeholder for PDF generation)
     */
    public function printSettlement(int $settlementId): array
    {
        // This would generate a PDF or redirect to print view
        // For now, return success
        return [
            'success' => true,
            'message' => 'Settlement print functionality not yet implemented'
        ];
    }

    /**
     * Handle AJAX requests for settlements
     */
    public function handleAjaxRequest(string $action, array $data = []): array
    {
        switch ($action) {
            case 'create_settlement':
                return $this->createSettlement($data);

            case 'calculate_settlement':
                return $this->calculateSettlement($data);

            case 'get_settlement':
                return $this->getSettlement($data['settlement_id'] ?? 0);

            case 'approve_settlement':
                return $this->approveSettlement(
                    $data['settlement_id'] ?? 0,
                    $data['approved_by'] ?? 0
                );

            case 'get_pending_settlements':
                return $this->getPendingSettlements();

            case 'get_settlements':
                return $this->getSettlements();

            case 'print_settlement':
                return $this->printSettlement($data['settlement_id'] ?? 0);

            default:
                return parent::handleAjaxRequest($action, $data);
        }
    }
}