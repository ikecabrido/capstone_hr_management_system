<?php

class DashboardModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /* ================= EMPLOYEES ================= */

    public function getEmployeeCount()
    {
        return $this->db
            ->query("SELECT COUNT(*) FROM employees")
            ->fetchColumn();
    }


    /* ================= PERIOD ================= */

    public function getLatestPeriod()
    {
        $sql = "
            SELECT *
            FROM payroll_periods
            ORDER BY start_date DESC
            LIMIT 1
        ";

        return $this->db->query($sql)->fetch(PDO::FETCH_ASSOC);
    }


    /* ================= PAYROLL ================= */

    public function getTotalPayroll($runId)
    {
        $sql = "
            SELECT IFNULL(SUM(net_pay),0)
            FROM payslips
            WHERE payroll_run_id = ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$runId]);

        return $stmt->fetchColumn();
    }


    public function getPendingCount($runId)
    {
        $sql = "
            SELECT COUNT(*)
            FROM payslips p
            JOIN payroll_runs r ON p.payroll_run_id = r.id
            WHERE p.payroll_run_id = ?
            AND r.status = 'draft'
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$runId]);

        return $stmt->fetchColumn() ?? 0;
    }


    public function getPaidCount($runId)
    {
        $sql = "
            SELECT COUNT(*)
            FROM payslips p
            JOIN payroll_runs r ON p.payroll_run_id = r.id
        WHERE p.payroll_run_id = ?
        AND r.status = 'finalized'
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$runId]);

        return $stmt->fetchColumn() ?? 0;
    }


    /* ================= CHART ================= */

    public function getMonthlyTotals()
    {
        $sql = "
            SELECT 
                DATE_FORMAT(pp.start_date,'%Y-%m') AS month,
                SUM(ps.net_pay) AS total
            FROM payroll_periods pp
            JOIN payroll_runs pr ON pr.payroll_period_id = pp.id
            JOIN payslips ps ON ps.payroll_run_id = pr.id
            WHERE pr.status != 'draft'
            GROUP BY month
            ORDER BY month ASC
            LIMIT 12
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    /* ================= LIFETIME ================= */

    public function getLifetimePayroll()
    {
        return $this->db
            ->query("SELECT SUM(net_pay) FROM payslips p JOIN payroll_runs r ON p.payroll_run_id = r.id WHERE r.status='finalized'")
            ->fetchColumn() ?? 0;
    }


    /* ================= UTILITIES ================= */
    public function getActivePeriod()
    {
        $sql = "
        SELECT *
        FROM payroll_periods
        WHERE status = 'open'
        LIMIT 1
    ";

        return $this->db->query($sql)->fetch(PDO::FETCH_ASSOC);
    }
    public function getCurrentRun($periodId)
    {
        $sql = "
        SELECT *
        FROM payroll_runs
        WHERE payroll_period_id = ?
        AND status IN ('draft','finalized')
        ORDER BY id DESC
        LIMIT 1
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$periodId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getRunProgress($runId)
    {
        $sql = "
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN p.net_pay > 0 THEN 1 ELSE 0 END) AS processed,
            SUM(CASE WHEN p.net_pay = 0 THEN 1 ELSE 0 END) AS pending
        FROM payslips p
        WHERE p.payroll_run_id = ?
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$runId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getPendingRuns()
    {
        $sql = "
        SELECT COUNT(*) 
        FROM payroll_runs
        WHERE status = 'draft'
    ";

        return $this->db->query($sql)->fetchColumn();
    }
    public function getLatestFinalizedRun()
    {
        $sql = "SELECT IFNULL(SUM(ps.net_pay),0) AS total
        FROM payroll_runs pr
        JOIN payslips ps ON ps.payroll_run_id = pr.id
        WHERE pr.status = 'finalized'
        AND pr.id = (
            SELECT id
            FROM payroll_runs
            WHERE status = 'finalized'
            ORDER BY id DESC
            LIMIT 1
        )";

        return $this->db->query($sql)->fetchColumn() ?? 0;
    }
}
