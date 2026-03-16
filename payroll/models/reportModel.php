<?php
class ReportModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Get payroll overview for a period (auto-generated table)
    public function getPayrollOverview(int $periodId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                e.id AS employee_id,
                CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
                pos.title AS position,
                et.name AS employment_type,
                p.gross_pay, 
                p.total_deductions, 
                p.net_pay
            FROM payslips p
            JOIN employees e ON p.employee_id = e.id
            LEFT JOIN positions pos ON e.position_id = pos.id
            LEFT JOIN employment_types et ON e.employment_type_id = et.id
            JOIN payroll_runs pr ON pr.id = p.payroll_run_id
            WHERE pr.payroll_period_id = :pid
            ORDER BY e.last_name ASC
        ");
        $stmt->execute([':pid' => $periodId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getPeriods(): array
    {
        $stmt = $this->db->query("
            SELECT 
                id,
                period_name,
                start_date,
                end_date,
                status
            FROM payroll_periods
            ORDER BY start_date DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getPeriodById(int $periodId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM payroll_periods
            WHERE id = ?
        ");

        $stmt->execute([$periodId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }
    // Get payroll summary for a period
    public function getPayrollSummary(int $periodId): array
    {
        $stmt = $this->db->prepare("
        SELECT
            COUNT(p.id) AS total_employees,
            SUM(p.gross_pay) AS total_gross,
            SUM(p.total_deductions) AS total_deductions,
            SUM(p.net_pay) AS total_net,
            AVG(p.net_pay) AS average_net
        FROM payslips p
        JOIN payroll_runs pr ON pr.id = p.payroll_run_id
        WHERE pr.payroll_period_id = :pid
    ");

        $stmt->execute([':pid' => $periodId]);

        return $stmt->fetch() ?: [];
    }
}
