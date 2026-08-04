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
            $required = ['employee_id', 'basic_salary', 'settlement_date'];
            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    return ['success' => false, 'message' => "Field '$field' is required"];
                }
            }

            if (!isset($data['net_payable']) || $data['net_payable'] === '') {
                $data['net_payable'] = $this->settlementModel->calculateTotalSettlement($data);
            }

            if (!empty($data['resignation_id']) && $this->settlementModel->hasExistingSettlementForResignation((int)$data['resignation_id'])) {
                return ['success' => false, 'message' => 'A settlement already exists for this resignation.'];
            }

            $data['created_by'] = $_SESSION['user']['id'] ?? 0;
            $data['status'] = $data['status'] ?? 'draft';

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

    public function updateSettlement(array $data): array
    {
        try {
            if (empty($data['settlement_id'])) {
                return ['success' => false, 'message' => 'Settlement ID is required for update'];
            }

            $settlementId = (int)$data['settlement_id'];
            if (!isset($data['net_payable']) || $data['net_payable'] === '') {
                $data['net_payable'] = $this->settlementModel->calculateTotalSettlement($data);
            }

            if (!empty($data['resignation_id']) && $this->settlementModel->hasExistingSettlementForResignation((int)$data['resignation_id'], $settlementId)) {
                return ['success' => false, 'message' => 'A settlement already exists for this resignation'];
            }

            $data['updated_by'] = $_SESSION['user']['id'] ?? 0;
            $success = $this->settlementModel->updateSettlement($settlementId, $data);

            if ($success) {
                return ['success' => true, 'message' => 'Settlement updated successfully'];
            }

            return ['success' => false, 'message' => 'Failed to update settlement'];
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
     * Get all settlements (with optional status filter)
     */
    public function getSettlements(string $status = null): array
    {
        return $this->settlementModel->getAllSettlements($status);
    }

    /**
     * Print settlement (placeholder for PDF generation)
     */
    public function printSettlement(int $settlementId)
    {
        $settlement = $this->settlementModel->getSettlementById($settlementId);
        if (!$settlement) {
            return ['success' => false, 'message' => 'Settlement not found'];
        }

        return [
            'success' => true,
            'settlement' => $settlement
        ];
    }

    public function renderSettlementPrintPage(int $settlementId): string
    {
        $settlement = $this->settlementModel->getSettlementById($settlementId);
        if (!$settlement) {
            return '<!doctype html><html><head><title>Settlement Not Found</title></head><body><h1>Settlement not found</h1></body></html>';
        }

        $employeeName = htmlspecialchars($settlement['full_name'] ?? 'Unknown', ENT_QUOTES);
        $settlementDate = htmlspecialchars($settlement['settlement_date'] ?? '', ENT_QUOTES);
        $paymentDate = htmlspecialchars($settlement['payment_date'] ?? 'N/A', ENT_QUOTES);
        $status = htmlspecialchars($settlement['status'] ?? '', ENT_QUOTES);
        $netPayable = number_format($settlement['net_payable'] ?? 0, 2);

        $html = '<!doctype html><html><head><meta charset="UTF-8"><title>Final Settlement</title>' .
            '<style>body{font-family:Arial,sans-serif;margin:24px;}h1,h2{margin-bottom:0.5rem;}table{width:100%;border-collapse:collapse;margin-top:1rem;}th,td{padding:8px;border:1px solid #ddd;text-align:left;}th{background:#f4f4f4;}</style>' .
            '</head><body>' .
            '<h1>Final Settlement Report</h1>' .
            '<p><strong>Employee:</strong> ' . $employeeName . '</p>' .
            '<p><strong>Settlement Date:</strong> ' . $settlementDate . '</p>' .
            '<p><strong>Payment Date:</strong> ' . $paymentDate . '</p>' .
            '<p><strong>Status:</strong> ' . $status . '</p>' .
            '<table><thead><tr><th>Description</th><th>Amount</th></tr></thead><tbody>' .
            '<tr><td>Basic Salary</td><td>' . number_format($settlement['basic_salary'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Remaining Salary</td><td>' . number_format($settlement['remaining_salary'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Unused Leave Conversion</td><td>' . number_format($settlement['unused_leave_conversion'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Overtime Pay</td><td>' . number_format($settlement['overtime_pay'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Holiday Pay</td><td>' . number_format($settlement['holiday_pay'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Bonuses</td><td>' . number_format($settlement['bonuses'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Commission</td><td>' . number_format($settlement['commission'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>HRA</td><td>' . number_format($settlement['hra'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Conveyance</td><td>' . number_format($settlement['conveyance'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>LTA</td><td>' . number_format($settlement['lta'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Medical Allowance</td><td>' . number_format($settlement['medical_allowance'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Other Allowances</td><td>' . number_format($settlement['other_allowances'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Separation Pay</td><td>' . number_format($settlement['separation_pay'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Provident Fund</td><td>' . number_format($settlement['provident_fund'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Gratuity</td><td>' . number_format($settlement['gratuity'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Notice Pay</td><td>' . number_format($settlement['notice_pay'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Tax</td><td>' . number_format($settlement['tax'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>SSS</td><td>' . number_format($settlement['sss'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>PhilHealth</td><td>' . number_format($settlement['philhealth'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Pag-IBIG</td><td>' . number_format($settlement['pagibig'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Cash Advance</td><td>' . number_format($settlement['cash_advance'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Company Loan</td><td>' . number_format($settlement['company_loan'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Equipment Damage</td><td>' . number_format($settlement['equipment_damage'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Missing Assets</td><td>' . number_format($settlement['missing_assets'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Late Deductions</td><td>' . number_format($settlement['late_deductions'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Absence Deductions</td><td>' . number_format($settlement['absence_deductions'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Outstanding Loans</td><td>' . number_format($settlement['outstanding_loans'] ?? 0, 2) . '</td></tr>' .
            '<tr><td>Other Deductions</td><td>' . number_format($settlement['other_deductions'] ?? 0, 2) . '</td></tr>' .
            '<tr><th>Total Net Payable</th><th>' . $netPayable . '</th></tr>' .
            '</tbody></table>' .
            '<script>window.onload=function(){window.print();};</script>' .
            '</body></html>';

        return $html;
    }

    /**
     * Handle AJAX requests for settlements
     */
    public function handleAjaxRequest(string $action, array $data = []): array
    {
        switch ($action) {
            case 'submit_settlement':
            case 'create_settlement':
                return $this->createSettlement($data);

            case 'update_settlement':
                return $this->updateSettlement($data);

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
                return $this->settlementModel->getAllSettlements(
                    $data['status'] ?? null,
                    $data['page'] ?? 1,
                    $data['limit'] ?? 10,
                    $data['search'] ?? ''
                );

            case 'print_settlement':
                return $this->printSettlement($data['settlement_id'] ?? 0);

            case 'archive_settlement':
                return $this->archiveSettlement($data['settlement_id'] ?? 0);

            case 'unarchive_settlement':
                return $this->unarchiveSettlement($data['settlement_id'] ?? 0);

            case 'get_settlement_details':
                return $this->getSettlementDetails($data['settlement_id'] ?? 0);

            default:
                return parent::handleAjaxRequest($action, $data);
        }
    }

    /**
     * Archive settlement
     */
    public function archiveSettlement(int $settlementId): array
    {
        try {
            $archiveReason = $_POST['archive_reason'] ?? 'Manual archive';
            $success = $this->settlementModel->archiveSettlement($settlementId, $archiveReason);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Settlement archived successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to archive settlement'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Unarchive settlement
     */
    public function unarchiveSettlement(int $settlementId): array
    {
        try {
            $success = $this->settlementModel->unarchiveSettlement($settlementId);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Settlement unarchived successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to unarchive settlement'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get settlement details for archiving
     */
    private function getSettlementDetails(int $settlementId): array
    {
        try {
            if (empty($settlementId)) {
                return [
                    'success' => false,
                    'message' => 'Settlement ID is required'
                ];
            }

            $settlement = $this->settlementModel->getSettlementById($settlementId);

            if (!$settlement) {
                return [
                    'success' => false,
                    'message' => 'Settlement not found'
                ];
            }

            // Get employee name
            $employee = $this->settlementModel->getEmployeeById($settlement['employee_id']);

            return [
                'success' => true,
                'data' => [
                    'id' => $settlement['id'],
                    'employee_id' => $settlement['employee_id'],
                    'employee_name' => $employee ? $employee['first_name'] . ' ' . $employee['last_name'] : 'Unknown',
                    'settlement_date' => $settlement['settlement_date'],
                    'net_payable' => $settlement['net_payable'],
                    'status' => $settlement['status']
                ]
            ];
        } catch (Exception $e) {
            error_log("Error getting settlement details: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while retrieving settlement details'
            ];
        }
    }
}