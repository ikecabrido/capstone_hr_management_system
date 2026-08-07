<?php

class AllowanceDeductionModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Get employees
    public function getEmployees(): array
    {
        $stmt = $this->db->query("
            SELECT employee_id as id, full_name AS name
            FROM employees
            WHERE employment_status='Active'
            ORDER BY full_name
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get adjustments
    public function getRecords($periodId = null, $employeeId = null): array
    {
        $sql = "
            SELECT
                ea.*,
                'Other Deductions' AS display_type,
                e.full_name AS employee_name,
                pp.period_name,
                pp.status AS period_status
            FROM pr_employee_adjustments ea
            JOIN employees e ON ea.employee_id = e.employee_id
            JOIN pr_periods pp ON ea.payroll_period_id = pp.period_id
            WHERE 1=1
        ";

        $params = [];

        if ($periodId) {
            $sql .= " AND ea.payroll_period_id = :pid";
            $params[':pid'] = $periodId;
        }

        if ($employeeId) {
            $sql .= " AND ea.employee_id = :eid";
            $params[':eid'] = $employeeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get totals
    public function getTotals($periodId = null, $employeeId = null): array
    {
        $sql = "
            SELECT
                SUM(amount) AS total_deduction
            FROM pr_employee_adjustments
            WHERE type = 'deduction'
        ";

        $params = [];

        if ($periodId) {
            $sql .= " AND payroll_period_id = :pid";
            $params[':pid'] = $periodId;
        }

        if ($employeeId) {
            $sql .= " AND employee_id = :eid";
            $params[':eid'] = $employeeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'total_allowance' => 0, // No longer used
            'total_deduction' => $result['total_deduction'] ?? 0
        ];
    }

    // Add record
    public function store(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO pr_employee_adjustments
            (employee_id, payroll_period_id, type, deduction_subtype, description, amount, file_path)
            VALUES (?,?,?,?,?,?,?)
        ");

        return $stmt->execute([
            $data['employee_id'],
            $data['period_id'],
            'deduction',
            $data['deduction_subtype'],
            $data['description'],
            $data['amount'],
            $data['file_path'] ?? null
        ]);
    }
    public function addAdjustment($emp, $deductionSubtype, $desc, $amt, $period, $filePath = null)
    {
        $stmt = $this->db->prepare("
        INSERT INTO pr_employee_adjustments
        (employee_id, type, deduction_subtype, description, amount, payroll_period_id, file_path)
        VALUES (?,?,?,?,?,?,?)
    ");

        return $stmt->execute([
            $emp,
            'deduction',
            $deductionSubtype,
            $desc,
            $amt,
            $period,
            $filePath
        ]);
    }
    public function updateAdjustment($id, $desc, $amt)
    {
        $stmt = $this->db->prepare("
        UPDATE pr_employee_adjustments
        SET description=?, amount=?
        WHERE adjustment_id=?
    ");

        return $stmt->execute([$desc, $amt, $id]);
    }
    public function deleteAdjustment($id)
    {
        $stmt = $this->db->prepare("
        DELETE FROM pr_employee_adjustments
        WHERE adjustment_id=?
    ");

        return $stmt->execute([$id]);
    }
    public function isPeriodClosed(int $periodId): bool
    {
        $stmt = $this->db->prepare("
        SELECT status FROM pr_periods WHERE period_id = ?
    ");
        $stmt->execute([$periodId]);

        return $stmt->fetchColumn() === 'closed';
    }
}
