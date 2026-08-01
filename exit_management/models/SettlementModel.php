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
     * Get all settlements with optional status filter and pagination
     */
    public function getAllSettlements(string $status = null, int $page = 1, int $limit = 10, string $search = ''): array
    {
        $offset = ($page - 1) * $limit;

        $sql = "
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
                s.created_at,
                s.updated_at,
                e.full_name as employee_name
            FROM exit_employee_settlements s
            JOIN employees e ON s.employee_id = e.employee_id
        ";

        $countSql = "
            SELECT COUNT(*) as total
            FROM exit_employee_settlements s
            JOIN employees e ON s.employee_id = e.employee_id
        ";

        $params = [];
        $whereClause = "";

        if ($status && $status !== 'all') {
            $whereClause = " WHERE s.status = :status";
            $params['status'] = $status;
        }

        // Add search condition if provided
        if (!empty($search)) {
            $searchCondition = $whereClause ? " AND" : " WHERE";
            $searchCondition .= " (e.full_name LIKE :search0 OR s.settlement_date LIKE :search1)";
            $whereClause .= $searchCondition;
            $searchParam = "%$search%";
            $params['search0'] = $searchParam;
            $params['search1'] = $searchParam;
        }

        // Get total count
        $countStmt = $this->db->prepare($countSql . $whereClause);
        $countStmt->execute($params);
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Get paginated data
        $stmt = $this->db->prepare($sql . $whereClause . " ORDER BY s.created_at DESC LIMIT :limit OFFSET :offset");
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ];
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

    /**
     * Archive settlement
     */
    public function archiveSettlement(int $settlementId, string $archiveReason = 'Manual archive'): bool
    {
        // Get the full settlement data
        $stmt = $this->db->prepare("SELECT * FROM exit_employee_settlements WHERE id = ?");
        $stmt->execute([$settlementId]);
        $settlement = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$settlement) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            // Insert into exit_archive
            $archiveStmt = $this->db->prepare("
                INSERT INTO exit_archive (
                    archive_type, original_id, employee_id, title, description, content,
                    status, original_created_by, archived_by, archive_reason, archive_data
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $title = "Settlement - Employee " . ($settlement['employee_id'] ?? 'Unknown');
            $description = "Archived settlement record";
            $content = json_encode($settlement);
            $archivedBy = $_SESSION['user']['id'] ?? 1;

            $archiveStmt->execute([
                'settlement',
                $settlementId,
                $settlement['employee_id'],
                $title,
                $description,
                $content,
                $settlement['status'],
                $settlement['created_by'],
                $archivedBy,
                $archiveReason,
                $content
            ]);

            // Delete from exit_employee_settlements
            $deleteStmt = $this->db->prepare("DELETE FROM exit_employee_settlements WHERE id = ?");
            $deleteStmt->execute([$settlementId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Settlement archive error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Unarchive settlement
     */
    public function unarchiveSettlement(int $settlementId): bool
    {
        // Get archived data
        $stmt = $this->db->prepare("SELECT * FROM exit_archive WHERE archive_type = 'settlement' AND original_id = ?");
        $stmt->execute([$settlementId]);
        $archive = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$archive) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            // Decode the archived data
            $settlementData = json_decode($archive['archive_data'], true);
            if (!$settlementData) {
                return false;
            }

            // Insert back into exit_employee_settlements
            $insertStmt = $this->db->prepare("
                INSERT INTO exit_employee_settlements (
                    id, employee_id, resignation_id, basic_salary, hra, conveyance, lta,
                    medical_allowance, other_allowances, provident_fund, gratuity,
                    notice_pay, outstanding_loans, other_deductions, net_payable,
                    settlement_date, status, created_by, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $insertStmt->execute([
                $settlementData['id'],
                $settlementData['employee_id'],
                $settlementData['resignation_id'],
                $settlementData['basic_salary'],
                $settlementData['hra'],
                $settlementData['conveyance'],
                $settlementData['lta'],
                $settlementData['medical_allowance'],
                $settlementData['other_allowances'],
                $settlementData['provident_fund'],
                $settlementData['gratuity'],
                $settlementData['notice_pay'],
                $settlementData['outstanding_loans'],
                $settlementData['other_deductions'],
                $settlementData['net_payable'],
                $settlementData['settlement_date'],
                $settlementData['status'] ?? 'draft',
                $settlementData['created_by'],
                $settlementData['created_at'],
                date('Y-m-d H:i:s')
            ]);

            // Update archive record to mark as restored
            $updateStmt = $this->db->prepare("
                UPDATE exit_archive
                SET restored = 1, restored_by = ?, restored_at = NOW()
                WHERE id = ?
            ");
            $restoredBy = $_SESSION['user']['id'] ?? 1;
            $updateStmt->execute([$restoredBy, $archive['id']]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Settlement unarchive error: " . $e->getMessage());
            return false;
        }
    }
}