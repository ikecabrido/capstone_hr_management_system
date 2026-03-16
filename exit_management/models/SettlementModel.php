<?php

require_once 'ExitManagementModel.php';

class SettlementModel extends ExitManagementModel
{
    /**
     * Create a settlement record
     */
    public function createSettlement(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO employee_settlements (employee_id, resignation_id, basic_salary,
                                            hra, conveyance, lta, medical_allowance,
                                            other_allowances, provident_fund, gratuity,
                                            notice_pay, outstanding_loans, other_deductions,
                                            net_payable, settlement_date, status, created_by, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft', ?, NOW())
        ");

        $stmt->execute([
            $data['employee_id'],
            $data['resignation_id'] ?? null,
            $data['basic_salary'],
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
            $data['net_payable'],
            $data['settlement_date'],
            $data['created_by']
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Get settlement by ID
     */
    public function getSettlementById(int $settlementId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, u.full_name, u.username as emp_id,
                   r.resignation_type, r.last_working_date
            FROM employee_settlements s
            JOIN users u ON s.employee_id = u.id
            LEFT JOIN resignations r ON s.resignation_id = r.id
            WHERE s.id = ?
        ");
        $stmt->execute([$settlementId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get settlements by employee
     */
    public function getSettlementsByEmployee(int $employeeId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM employee_settlements
            WHERE employee_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            UPDATE employee_settlements
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
            SELECT s.*, u.full_name, u.username as emp_id
            FROM employee_settlements s
            JOIN users u ON s.employee_id = u.id
            WHERE s.status IN ('draft', 'pending_approval')
            ORDER BY s.settlement_date ASC
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