<?php

require_once __DIR__ . '/../models/SettlementModel.php';
require_once __DIR__ . '/../../payroll/controllers/payrollClearanceController.php';

class SettlementController extends ExitManagementController
{
    private SettlementModel $settlementModel;

    public function __construct()
    {
        parent::__construct();
        $this->settlementModel = new SettlementModel();
    }

    public function createSettlement(array $data): array
    {
        try {
            // Minimum required fields for a draft settlement: employee and settlement date
            $required = ['employee_id', 'settlement_date'];
            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    return ['success' => false, 'message' => "Field '$field' is required"];
                }
            }

            // If net_payable is not provided, compute a best-effort total from available components
            if (!isset($data['net_payable'])) {
                $data['net_payable'] = $this->settlementModel->calculateTotalSettlement($data);
            }

            $data['created_by'] = $_SESSION['user']['id'] ?? 0;
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

    public function calculateSettlement(array $data): array
    {
        try {
            $calculations = [];

            if (isset($data['basic_salary']) && isset($data['years_of_service'])) {
                $calculations['gratuity'] = $this->settlementModel->calculateGratuity(
                    $data['basic_salary'],
                    $data['years_of_service']
                );
            }

            if (isset($data['basic_salary'])) {
                $da = $data['da'] ?? 0;
                $calculations['provident_fund'] = $this->settlementModel->calculateProvidentFund(
                    $data['basic_salary'],
                    $da
                );
            }

            if (isset($data['basic_salary']) && isset($data['notice_days'])) {
                $calculations['notice_pay'] = $this->settlementModel->calculateNoticePay(
                    $data['basic_salary'],
                    $data['notice_days']
                );
            }

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

    public function previewPayroll(int $settlementId, int $employeeId): array
    {
        try {
            $settlement = $this->settlementModel->getSettlementById($settlementId);
            if (!$settlement) {
                return ['success' => false, 'message' => 'Settlement not found'];
            }

            $payrollController = new PayrollClearanceController();
            $preview = $payrollController->calculateSettlementPreview($settlementId, $employeeId);
            $adjustments = $this->settlementModel->getSettlementAdjustments($settlementId);

            if (!empty($adjustments)) {
                $preview['manual_adjustments'] = array_map(function (array $adjustment): array {
                    return [
                        'description' => $adjustment['description'],
                        'amount' => (float)$adjustment['amount'],
                        'adjustment_type' => $adjustment['adjustment_type'],
                        'taxable' => (bool)$adjustment['taxable'],
                        'notes' => $adjustment['notes']
                    ];
                }, $adjustments);
            }

            $this->settlementModel->savePayrollPreview($settlementId, $preview, $settlement['payroll_notes'] ?? '');

            if (isset($preview['net_pay'])) {
                $this->settlementModel->updatePayrollFinalAmount($settlementId, (float)$preview['net_pay']);
            }

            return [
                'success' => true,
                'message' => 'Payroll preview generated successfully',
                'preview' => $preview
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function saveAdjustments(int $settlementId, array $adjustments, int $createdBy): array
    {
        try {
            $count = $this->settlementModel->saveSettlementAdjustments($settlementId, $adjustments, $createdBy);
            return [
                'success' => true,
                'message' => 'Settlement adjustments saved successfully',
                'count' => $count
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function requestPayrollClearance(int $settlementId, int $requestedBy, ?string $notes = null): array
    {
        try {
            $settlement = $this->settlementModel->getSettlementById($settlementId);
            if (!$settlement) {
                return ['success' => false, 'message' => 'Settlement not found'];
            }

            $payrollController = new PayrollClearanceController();
            $result = $payrollController->createClearanceRequest($settlementId, $requestedBy);
            if ($result['success']) {
                $preview = $this->previewPayroll($settlementId, (int)$settlement['employee_id']);
                if ($preview['success'] && isset($preview['preview']['net_pay'])) {
                    $this->settlementModel->updatePayrollFinalAmount($settlementId, (float)$preview['preview']['net_pay']);
                }
                $this->settlementModel->updatePayrollClearanceStatus($settlementId, 'pending', $result['id'] ?? null, $notes);
                return $result;
            }

            return $result;
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getSettlement(int $settlementId): array
    {
        $settlement = $this->settlementModel->getSettlementById($settlementId);

        if (!$settlement) {
            return ['error' => 'Settlement not found'];
        }

        $settlement['adjustments'] = $this->settlementModel->getSettlementAdjustments($settlementId);
        $settlement['payroll_preview'] = [];

        if (!empty($settlement['payroll_preview_data'])) {
            $decoded = json_decode($settlement['payroll_preview_data'], true);
            if (is_array($decoded)) {
                $settlement['payroll_preview'] = $decoded;
            }
        }

        return $settlement;
    }

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

    public function getPendingSettlements(): array
    {
        return $this->settlementModel->getPendingSettlements();
    }

    public function getSettlements(): array
    {
        return $this->settlementModel->getAllSettlements();
    }

    public function printSettlement(int $settlementId): array
    {
        return [
            'success' => true,
            'message' => 'Settlement print functionality not yet implemented'
        ];
    }

    public function handleAjaxRequest(string $action, array $data = []): array
    {
        switch ($action) {
            case 'submit_settlement':
            case 'update_settlement':
            case 'create_settlement':
                return $this->createSettlement($data);

            case 'calculate_settlement':
                return $this->calculateSettlement($data);

            case 'preview_payroll':
                return $this->previewPayroll((int)($data['settlement_id'] ?? 0), (int)($data['employee_id'] ?? 0));

            case 'save_adjustments':
                return $this->saveAdjustments(
                    (int)($data['settlement_id'] ?? 0),
                    json_decode((string)($data['adjustments'] ?? '[]'), true) ?: [],
                    (int)($_SESSION['user']['id'] ?? 0)
                );

            case 'request_payroll_clearance':
                return $this->requestPayrollClearance(
                    (int)($data['settlement_id'] ?? 0),
                    (int)($_SESSION['user']['id'] ?? 0),
                    $data['notes'] ?? null
                );

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