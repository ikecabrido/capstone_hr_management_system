<?php

require_once 'ExitManagementModel.php';

class SettlementModel extends ExitManagementModel
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureSettlementSchema();
    }

    private function ensureSettlementSchema(): void
    {
        $this->ensureColumn('exit_employee_settlements', 'payroll_preview_data', 'TEXT NULL');
        $this->ensureColumn('exit_employee_settlements', 'payroll_clearance_status', 'VARCHAR(30) NOT NULL DEFAULT "not_requested"');
        $this->ensureColumn('exit_employee_settlements', 'payroll_clearance_id', 'INT NULL');
        $this->ensureColumn('exit_employee_settlements', 'payroll_notes', 'TEXT NULL');
        $this->ensureColumn('exit_employee_settlements', 'payroll_final_amount', 'DECIMAL(12,2) NULL');
        $this->ensureSettlementStatusEnum();

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS exit_settlement_adjustments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                settlement_id INT NOT NULL,
                adjustment_type VARCHAR(20) NOT NULL,
                description VARCHAR(255) NOT NULL,
                amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                taxable TINYINT(1) NOT NULL DEFAULT 0,
                notes TEXT NULL,
                created_by INT NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_settlement_adjustments_settlement_id (settlement_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    private function ensureColumn(string $table, string $column, string $definition): void
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
        $stmt->execute([$table, $column]);
        if ((int)$stmt->fetchColumn() > 0) {
            return;
        }

        $this->db->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    }

    private function ensureSettlementStatusEnum(): void
    {
        $stmt = $this->db->query("SHOW COLUMNS FROM exit_employee_settlements LIKE 'status'");
        $column = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$column) {
            return;
        }

        if (strpos($column['Type'], "'rejected'") !== false) {
            return;
        }

        $this->db->exec("ALTER TABLE exit_employee_settlements MODIFY COLUMN status ENUM('draft','approved','paid','rejected') NOT NULL DEFAULT 'draft'");
    }

    /**
     * Create a settlement record
     */
    public function createSettlement(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO exit_employee_settlements (employee_id, resignation_id, basic_salary,
                                           hra, conveyance, lta, medical_allowance,
                                           other_allowances, provident_fund, gratuity,
                                           notice_pay, outstanding_loans, other_deductions,
                                           net_payable, settlement_date, status, created_by, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft', ?, NOW())
        ");

        $stmt->execute([
            $data['employee_id'],
            $data['resignation_id'] ?? null,
            $data['basic_salary'] ?? 0,
            $data['hra'] ?? 0,
            $data['conveyance'] ?? 0,
            $data['lta'] ?? 0,
            $data['medical_allowance'] ?? 0,
            $data['other_allowances'] ?? 0,
            $data['provident_fund'] ?? 0,
            $data['gratuity'] ?? 0,
            $data['notice_pay'] ?? 0,
            $data['outstanding_loans'] ?? 0,
            $data['other_deductions'] ?? 0,
            $data['net_payable'] ?? 0,
            $data['settlement_date'] ?? null,
            $data['created_by'] ?? 0
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Update a settlement record
     */
    public function updateSettlement(int $settlementId, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE exit_employee_settlements
            SET employee_id = ?, basic_salary = ?, hra = ?, conveyance = ?,
                lta = ?, medical_allowance = ?, other_allowances = ?,
                provident_fund = ?, gratuity = ?, notice_pay = ?,
                outstanding_loans = ?, other_deductions = ?, net_payable = ?,
                settlement_date = ?, updated_at = NOW()
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['employee_id'],
            $data['basic_salary'] ?? 0,
            $data['hra'] ?? 0,
            $data['conveyance'] ?? 0,
            $data['lta'] ?? 0,
            $data['medical_allowance'] ?? 0,
            $data['other_allowances'] ?? 0,
            $data['provident_fund'] ?? 0,
            $data['gratuity'] ?? 0,
            $data['notice_pay'] ?? 0,
            $data['outstanding_loans'] ?? 0,
            $data['other_deductions'] ?? 0,
            $data['net_payable'] ?? 0,
            $data['settlement_date'] ?? null,
            $settlementId
        ]);
    }

    /**
     * Get settlement by ID
     */
    public function getSettlementById(int $settlementId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, e.full_name, e.employee_id as emp_id,
                   r.resignation_type, r.last_working_date
            FROM exit_employee_settlements s
            JOIN employees e ON s.employee_id = e.employee_id
            LEFT JOIN exit_resignations r ON s.resignation_id = r.id
            WHERE s.id = ?
        ");
        $stmt->execute([$settlementId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get settlements by employee
     */
    public function getSettlementsByEmployee(string $employeeId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM exit_employee_settlements
            WHERE employee_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveSettlementAdjustments(int $settlementId, array $adjustments, int $createdBy): int
    {
        $deleteStmt = $this->db->prepare("DELETE FROM exit_settlement_adjustments WHERE settlement_id = ?");
        $deleteStmt->execute([$settlementId]);

        $insertStmt = $this->db->prepare("
            INSERT INTO exit_settlement_adjustments (settlement_id, adjustment_type, description, amount, taxable, notes, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $inserted = 0;
        foreach ($adjustments as $adjustment) {
            $description = trim((string)($adjustment['description'] ?? ''));
            if ($description === '') {
                continue;
            }

            $insertStmt->execute([
                $settlementId,
                (string)($adjustment['adjustment_type'] ?? 'credit'),
                $description,
                (float)($adjustment['amount'] ?? 0),
                !empty($adjustment['taxable']) ? 1 : 0,
                trim((string)($adjustment['notes'] ?? '')),
                $createdBy
            ]);
            $inserted++;
        }

        return $inserted;
    }

    public function getSettlementAdjustments(int $settlementId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM exit_settlement_adjustments WHERE settlement_id = ? ORDER BY id ASC");
        $stmt->execute([$settlementId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function savePayrollPreview(int $settlementId, array $previewData, string $notes = '', int $updatedBy = 0): bool
    {
        $previewJson = json_encode($previewData, JSON_UNESCAPED_UNICODE);
        $stmt = $this->db->prepare("
            UPDATE exit_employee_settlements
            SET payroll_preview_data = ?, payroll_notes = ?, updated_at = NOW()
            WHERE id = ?
        ");

        return $stmt->execute([$previewJson ?: null, $notes, $settlementId]);
    }

    public function updatePayrollClearanceStatus(int $settlementId, string $status, ?int $clearanceId = null, ?string $notes = null): bool
    {
        $stmt = $this->db->prepare("
            UPDATE exit_employee_settlements
            SET payroll_clearance_status = ?, payroll_clearance_id = ?, payroll_notes = ?, updated_at = NOW()
            WHERE id = ?
        ");

        return $stmt->execute([$status, $clearanceId, $notes, $settlementId]);
    }

    public function updatePayrollFinalAmount(int $settlementId, float $amount): bool
    {
        $stmt = $this->db->prepare("
            UPDATE exit_employee_settlements
            SET payroll_final_amount = ?, updated_at = NOW()
            WHERE id = ?
        ");

        return $stmt->execute([$amount, $settlementId]);
    }

    /**
     * Calculate gratuity
     */
    public function calculateGratuity(float $basicSalary, int $yearsOfService): float
    {
        // Gratuity = (Basic Salary × 15 × Number of years of service) / 26
        return ($basicSalary * 15 * $yearsOfService) / 26;
    }

    /**
     * Calculate provident fund
     */
    public function calculateProvidentFund(float $basicSalary, float $da = 0): float
    {
        // Employee contribution: 12% of (Basic + DA)
        return ($basicSalary + $da) * 0.12;
    }

    /**
     * Calculate notice pay
     */
    public function calculateNoticePay(float $basicSalary, int $noticeDays): float
    {
        // Notice pay = (Basic Salary / 30) × Number of notice days
        return ($basicSalary / 30) * $noticeDays;
    }

    /**
     * Update settlement status
     */
    public function updateSettlementStatus(int $settlementId, string $status, string $approvedBy = null): bool
    {
        $stmt = $this->db->prepare("
            UPDATE exit_employee_settlements
            SET status = ?, approved_by = ?, approved_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$status, $approvedBy, $settlementId]);
    }

    /**
     * Get pending settlements
     */
    public function getPendingSettlements(): array
    {
        $stmt = $this->db->query("
            SELECT s.*, e.full_name, e.employee_id as emp_id
            FROM exit_employee_settlements s
            JOIN employees e ON s.employee_id = e.employee_id
            WHERE s.status IN ('draft', 'pending_approval')
            ORDER BY s.settlement_date ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all settlements
     */
    public function getAllSettlements(): array
    {
        $stmt = $this->db->query("
            SELECT 
                s.id,
                s.employee_id,
                s.settlement_date,
                s.basic_salary,
                s.hra,
                s.conveyance,
                s.lta,
                s.medical_allowance,
                s.other_allowances,
                s.provident_fund,
                s.gratuity,
                s.notice_pay,
                s.outstanding_loans,
                s.other_deductions,
                s.net_payable,
                s.status,
                s.payroll_clearance_status,
                s.payroll_notes,
                s.payroll_final_amount,
                s.created_at,
                s.updated_at,
                e.full_name as employee_name
            FROM exit_employee_settlements s
            JOIN employees e ON s.employee_id = e.employee_id
            ORDER BY s.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calculate total settlement amount
     */
    public function calculateTotalSettlement(array $components): float
    {
        $earnings = ($components['basic_salary'] ?? 0) +
                   ($components['hra'] ?? 0) +
                   ($components['conveyance'] ?? 0) +
                   ($components['lta'] ?? 0) +
                   ($components['medical_allowance'] ?? 0) +
                   ($components['other_allowances'] ?? 0) +
                   ($components['gratuity'] ?? 0) +
                   ($components['notice_pay'] ?? 0);

        $deductions = ($components['provident_fund'] ?? 0) +
                     ($components['outstanding_loans'] ?? 0) +
                     ($components['other_deductions'] ?? 0);

        return $earnings - $deductions;
    }
}