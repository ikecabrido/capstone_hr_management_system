<?php

class PayslipModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(?int $periodId = null, ?int $employeeId = null): array
    {
        $query = "
        SELECT 
            p.id,
            CONCAT(e.first_name,' ',e.last_name) AS employee_name,
            pos.title AS position,
            et.name AS employment_type,
            pp.period_name, 
            p.gross_pay, 
            p.total_deductions, 
            p.net_pay,
            prun.status AS payroll_status,
            p.generated_at
        FROM payslips p
        JOIN employees e ON p.employee_id = e.id
        LEFT JOIN positions pos ON e.position_id = pos.id
        LEFT JOIN employment_types et ON e.employment_type_id = et.id
        LEFT JOIN payroll_runs prun ON p.payroll_run_id = prun.id
        LEFT JOIN payroll_periods pp ON prun.payroll_period_id = pp.id
        WHERE 1=1
    ";

        $params = [];

        if ($periodId) {
            $query .= " AND prun.payroll_period_id = :pid";
            $params[':pid'] = $periodId;
        }

        if ($employeeId) {
            $query .= " AND e.id = :eid";
            $params[':eid'] = $employeeId;
        }

        $query .= " ORDER BY pp.start_date DESC, e.last_name ASC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getById(int $payslipId): ?array
    {
        // Main payslip info
        $stmt = $this->db->prepare("
            SELECT 
                p.*, 
                e.first_name, 
                e.last_name, 
                pos.title AS position,
                et.name AS employment_type
            FROM payslips p
            JOIN employees e ON p.employee_id = e.id
            LEFT JOIN positions pos ON e.position_id = pos.id
            LEFT JOIN employment_types et ON e.employment_type_id = et.id
            WHERE p.id = :id
        ");

        $stmt->execute([':id' => $payslipId]);
        $payslip = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$payslip) return null;

        // Breakdown items (earnings/deductions)
        $stmt2 = $this->db->prepare("
            SELECT item_type, description, amount
            FROM payslip_items
            WHERE payslip_id = :id
        ");
        $stmt2->execute([':id' => $payslipId]);
        $items = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // Separate earnings and deductions
        $payslip['earnings'] = array_filter($items, fn($i) => $i['item_type'] === 'earning');
        $payslip['deductions'] = array_filter($items, fn($i) => $i['item_type'] === 'deduction');

        return $payslip;
    }

    public function create(int $runId, int $employeeId, array $data): void
    {
        // Insert main payslip
        $stmt = $this->db->prepare("
            INSERT INTO payslips (payroll_run_id, employee_id, gross_pay, total_deductions, net_pay)
            VALUES (:run, :eid, :gross, :ded, :net)
        ");
        $stmt->execute([
            ':run' => $runId,
            ':eid' => $employeeId,
            ':gross' => $data['gross_pay'],
            ':ded' => $data['total_deductions'],
            ':net' => $data['net_pay']
        ]);

        $payslipId = (int)$this->db->lastInsertId();

        // Insert earnings & deductions
        $stmt2 = $this->db->prepare("
            INSERT INTO payslip_items (payslip_id, item_type, description, amount)
            VALUES (:pid, :type, :desc, :amt)
        ");

        foreach ($data['earnings'] as $e) {
            $stmt2->execute([
                ':pid' => $payslipId,
                ':type' => 'earning',
                ':desc' => $e['description'],
                ':amt' => $e['amount']
            ]);
        }

        foreach ($data['deductions'] as $d) {
            $stmt2->execute([
                ':pid' => $payslipId,
                ':type' => 'deduction',
                ':desc' => $d['description'],
                ':amt' => $d['amount']
            ]);
        }
    }

    public function getTotalGrossPay(?int $periodId = null): float
    {
        $query = "SELECT COALESCE(SUM(gross_pay), 0) as total FROM payslips";

        if ($periodId) {
            $query .= " WHERE payroll_run_id IN (
                SELECT id FROM payroll_runs WHERE payroll_period_id = :pid
            )";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':pid' => $periodId]);
        } else {
            $stmt = $this->db->query($query);
        }

        return (float)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getTotalDeductions(?int $periodId = null): float
    {
        $query = "SELECT COALESCE(SUM(total_deductions), 0) as total FROM payslips";

        if ($periodId) {
            $query .= " WHERE payroll_run_id IN (
                SELECT id FROM payroll_runs WHERE payroll_period_id = :pid
            )";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':pid' => $periodId]);
        } else {
            $stmt = $this->db->query($query);
        }

        return (float)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getTotalNetPay(?int $periodId = null): float
    {
        $query = "SELECT COALESCE(SUM(net_pay), 0) as total FROM payslips";

        if ($periodId) {
            $query .= " WHERE payroll_run_id IN (
                SELECT id FROM payroll_runs WHERE payroll_period_id = :pid
            )";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':pid' => $periodId]);
        } else {
            $stmt = $this->db->query($query);
        }

        return (float)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
