<?php
class PayrollModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Existing method for overview
    public function getSalaryOverview(?int $periodId = null, ?string $employmentType = null): array
    {
        $sql = "
        SELECT 
            p.id AS payroll_id, 
            CONCAT(e.first_name,' ',e.last_name) AS employee_name,
            pos.title AS position,
            et.name AS employment_type,
            pp.period_name, 
            p.gross_pay, 
            p.total_deductions, 
            p.net_pay,
            pr.status as payroll_status
        FROM payslips p
        JOIN employees e ON p.employee_id = e.id
        LEFT JOIN positions pos ON e.position_id = pos.id
        LEFT JOIN employment_types et ON e.employment_type_id = et.id
        LEFT JOIN payroll_runs pr ON p.payroll_run_id = pr.id
        LEFT JOIN payroll_periods pp ON pr.payroll_period_id = pp.id
        WHERE e.status = 'active'
    ";

        $params = [];

        if ($periodId !== null) {
            $sql .= " AND pp.id = :periodId";
            $params[':periodId'] = $periodId;
        }
        if ($employmentType !== null && $employmentType !== '') {
            $sql .= " AND et.name = :employmentType";
            $params[':employmentType'] = $employmentType;
        }

        $sql .= " ORDER BY pp.start_date DESC, e.last_name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getEmploymentTypes(): array
    {
        $stmt = $this->db->query("SELECT name FROM employment_types ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    public function isPeriodClosed(int $periodId): bool
    {
        $stmt = $this->db->prepare("SELECT status FROM payroll_periods WHERE id = ?");
        $stmt->execute([$periodId]);
        $status = $stmt->fetchColumn();

        return $status === 'closed';
    }
    // New: get single payslip with breakdown
    public function getPayslipById(int $payslipId): ?array
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

    public function getPayrollPeriods(): array
    {
        $stmt = $this->db->query("SELECT * FROM payroll_periods ORDER BY start_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllEmployees(): array
    {
        $stmt = $this->db->query("
        SELECT id,
               CONCAT(first_name,' ',last_name) AS name
        FROM employees
        WHERE status = 'active'
        ORDER BY last_name
    ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmployeesForPayroll(int $periodId): array
    {
        $stmt = $this->db->prepare("
        SELECT e.id, CONCAT(e.first_name,' ',e.last_name) AS name, pos.title AS position
        FROM employees e
        LEFT JOIN positions pos ON e.position_id = pos.id
        WHERE e.status='active'
        AND NOT EXISTS (
            SELECT 1
            FROM payslips p
            JOIN payroll_runs pr ON pr.id = p.payroll_run_id
            WHERE p.employee_id = e.id
            AND pr.payroll_period_id = :periodId
        )
    ");
        $stmt->execute(['periodId' => $periodId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function calculateEmployeePayroll(int $employeeId, int $periodId): array
    {
        /* ==============================
       Get Payroll Period Dates
    ============================== */
        $stmtPeriod = $this->db->prepare("
        SELECT start_date, end_date
        FROM payroll_periods
        WHERE id = :pid
    ");
        $stmtPeriod->execute([':pid' => $periodId]);
        $period = $stmtPeriod->fetch(PDO::FETCH_ASSOC);
        if (!$period) {
            return [];
        }
        $start = $period['start_date'];
        $end   = $period['end_date'];

        /* ==============================
       Get Employee Info
    ============================== */
        $stmtEmp = $this->db->prepare("
        SELECT e.*, et.name as employment_type
        FROM employees e
        JOIN employment_types et ON e.employment_type_id = et.id
        WHERE e.id = :eid
    ");
        $stmtEmp->execute([':eid' => $employeeId]);
        $employee = $stmtEmp->fetch(PDO::FETCH_ASSOC);
        $isPartTime = ($employee['employment_type'] === 'Part-Time');

        /* ==============================
       Get Basic Salary
    ============================== */
        $stmtSalary = $this->db->prepare("
        SELECT es.rate, ss.basic_salary
        FROM employee_salary es
        LEFT JOIN salary_structures ss ON es.salary_structure_id = ss.id
        WHERE es.employee_id = :eid
        ORDER BY es.effective_date DESC
        LIMIT 1
    ");
        $stmtSalary->execute([':eid' => $employeeId]);
        $salaryData = $stmtSalary->fetch(PDO::FETCH_ASSOC);

        $basicSalary = 0;

        if ($isPartTime) {
            // Part-time: rate is hourly, get hours worked
            $hourlyRate = $salaryData ? (float)$salaryData['rate'] : 0;

            $stmtHours = $this->db->prepare("
            SELECT hours_worked
            FROM part_time_hours
            WHERE employee_id = :eid AND payroll_period_id = :pid
        ");
            $stmtHours->execute([':eid' => $employeeId, ':pid' => $periodId]);
            $hoursData = $stmtHours->fetch(PDO::FETCH_ASSOC);
            $hoursWorked = $hoursData ? (float)$hoursData['hours_worked'] : 0;

            $basicSalary = $hourlyRate * $hoursWorked;
        } else {
            // Regular/Contractual: monthly salary (semi-monthly = half)
            $monthlySalary = $salaryData ? (float)$salaryData['basic_salary'] : 0;
            $basicSalary = $monthlySalary / 2; // Semi-monthly (15 days)
        }

        /* ==============================
       Count Absences
    ============================== */
        $stmtAbsent = $this->db->prepare("
        SELECT COUNT(*) AS total_absent
        FROM attendance
        WHERE employee_id = :eid
          AND status = 'absent'
          AND date BETWEEN :start AND :end
    ");
        $stmtAbsent->execute([
            ':eid'   => $employeeId,
            ':start' => $start,
            ':end'   => $end
        ]);
        $absentData = $stmtAbsent->fetch(PDO::FETCH_ASSOC);
        $absentDays = (int)$absentData['total_absent'];

        /* ==============================
   Get Adjustments (Per Period)
============================== */
        $stmtAdj = $this->db->prepare("
    SELECT type, description, amount
    FROM employee_adjustments
    WHERE employee_id = :eid
      AND payroll_period_id = :pid
");
        $stmtAdj->execute([
            ':eid' => $employeeId,
            ':pid' => $periodId
        ]);

        $adjustments = $stmtAdj->fetchAll(PDO::FETCH_ASSOC);


        /* ==============================
       Get Overtime
    ============================== */
        $stmtOT = $this->db->prepare("
        SELECT SUM(hours * rate) as overtime_pay
        FROM overtime
        WHERE employee_id = :eid
          AND date BETWEEN :start AND :end
          AND approved = 1
    ");
        $stmtOT->execute([
            ':eid'   => $employeeId,
            ':start' => $start,
            ':end'   => $end
        ]);
        $otData = $stmtOT->fetch(PDO::FETCH_ASSOC);
        $overtimePay = $otData ? (float)$otData['overtime_pay'] : 0;

        /* ==============================
       Compute Earnings
    ============================== */

        $deductions = [];
        $totalDeductions = 0;
        $earnings = [
            [
                'description' => 'Basic Salary',
                'amount' => $basicSalary
            ]
        ];
        $grossPay = $basicSalary;
        // Apply adjustments
        foreach ($adjustments as $adj) {

            if ($adj['type'] === 'allowance') {

                $earnings[] = [
                    'description' => $adj['description'],
                    'amount' => (float)$adj['amount']
                ];

                $grossPay += (float)$adj['amount'];
            } else { // deduction

                $deductions[] = [
                    'description' => $adj['description'],
                    'amount' => (float)$adj['amount']
                ];

                $totalDeductions += (float)$adj['amount'];
            }
        }


        // Add overtime
        if ($overtimePay > 0) {
            $earnings[] = [
                'description' => 'Overtime Pay',
                'amount' => $overtimePay
            ];
            $grossPay += $overtimePay;
        }

        /* ==============================
   STOP IF NO PAY
============================== */
        if ($grossPay <= 0) {
            return [
                'gross_pay' => 0,
                'net_pay' => 0,
                'total_deductions' => 0,
                'earnings' => $earnings,
                'deductions' => []
            ];
        }


        /* ==============================
       Compute PHILIPPINE DEDUCTIONS
    ============================== */


        // Monthly salary for contribution calculation
        $monthlySalary = $isPartTime ? ($basicSalary * 2) : ($salaryData['basic_salary'] ?? 0);

        // 1. SSS Contribution (2024 rates)
        $sssContribution = $this->calculateSSS($monthlySalary);
        if ($sssContribution > 0) {
            $deductions[] = [
                'description' => 'SSS',
                'amount' => $sssContribution
            ];
            $totalDeductions += $sssContribution;
        }

        // 2. PhilHealth Contribution (2024 rates)
        $philhealthContribution = $this->calculatePhilHealth($monthlySalary);
        if ($philhealthContribution > 0) {
            $deductions[] = [
                'description' => 'PhilHealth',
                'amount' => $philhealthContribution
            ];
            $totalDeductions += $philhealthContribution;
        }

        // 3. Pag-IBIG Contribution (2024 rates)
        $pagibigContribution = $this->calculatePagIBIG($monthlySalary);
        if ($pagibigContribution > 0) {
            $deductions[] = [
                'description' => 'Pag-IBIG',
                'amount' => $pagibigContribution
            ];
            $totalDeductions += $pagibigContribution;
        }

        // 4. Withholding Tax (TRAIN Law)
        $taxableIncome = ($grossPay * 2) - ($sssContribution * 2) - ($philhealthContribution * 2) - ($pagibigContribution * 2);
        $withholdingTax = $this->calculateWithholdingTax($taxableIncome) / 2; // Semi-monthly

        if ($withholdingTax > 0) {
            $deductions[] = [
                'description' => 'Withholding Tax',
                'amount' => $withholdingTax
            ];
            $totalDeductions += $withholdingTax;
        }

        // 5. Absence Deduction
        if (!$isPartTime && $absentDays > 0) {
            $dailyRate = $basicSalary / 11; // 11 working days in semi-monthly
            $absenceDeduction = $absentDays * $dailyRate;

            $deductions[] = [
                'description' => "Absence ({$absentDays} days)",
                'amount' => $absenceDeduction
            ];
            $totalDeductions += $absenceDeduction;
        }

        /* ==============================
       Net Pay
    ============================== */
        $netPay = $grossPay - $totalDeductions;

        return [
            'gross_pay' => $grossPay,
            'net_pay' => $netPay,
            'total_deductions' => $totalDeductions,
            'earnings' => $earnings,
            'deductions' => $deductions
        ];
    }

    /* ==============================
   PHILIPPINE CONTRIBUTION CALCULATORS
============================== */

    private function calculateSSS(float $monthlySalary): float
    {
        // 2024 SSS Contribution Table (Employee share only)
        if ($monthlySalary < 4250) return 180.00;
        if ($monthlySalary < 4750) return 202.50;
        if ($monthlySalary < 5250) return 225.00;
        if ($monthlySalary < 5750) return 247.50;
        if ($monthlySalary < 6250) return 270.00;
        if ($monthlySalary < 6750) return 292.50;
        if ($monthlySalary < 7250) return 315.00;
        if ($monthlySalary < 7750) return 337.50;
        if ($monthlySalary < 8250) return 360.00;
        if ($monthlySalary < 8750) return 382.50;
        if ($monthlySalary < 9250) return 405.00;
        if ($monthlySalary < 9750) return 427.50;
        if ($monthlySalary < 10250) return 450.00;
        if ($monthlySalary < 10750) return 472.50;
        if ($monthlySalary < 11250) return 495.00;
        if ($monthlySalary < 11750) return 517.50;
        if ($monthlySalary < 12250) return 540.00;
        if ($monthlySalary < 12750) return 562.50;
        if ($monthlySalary < 13250) return 585.00;
        if ($monthlySalary < 13750) return 607.50;
        if ($monthlySalary < 14250) return 630.00;
        if ($monthlySalary < 14750) return 652.50;
        if ($monthlySalary < 15250) return 675.00;
        if ($monthlySalary < 15750) return 697.50;
        if ($monthlySalary < 16250) return 720.00;
        if ($monthlySalary < 16750) return 742.50;
        if ($monthlySalary < 17250) return 765.00;
        if ($monthlySalary < 17750) return 787.50;
        if ($monthlySalary < 18250) return 810.00;
        if ($monthlySalary < 18750) return 832.50;
        if ($monthlySalary < 19250) return 855.00;
        if ($monthlySalary < 19750) return 877.50;
        if ($monthlySalary >= 19750) return 900.00; // Maximum

        return 0;
    }

    private function calculatePhilHealth(float $monthlySalary): float
    {
        // 2024 PhilHealth: 5% of basic salary (2.5% employee share)
        // Minimum: ₱10,000, Maximum: ₱100,000
        $baseSalary = max(10000, min($monthlySalary, 100000));
        return ($baseSalary * 0.05) / 2; // Employee share is half
    }

    private function calculatePagIBIG(float $monthlySalary): float
    {
        // 2024 Pag-IBIG: 2% of monthly salary
        // Maximum salary ceiling: ₱5,000
        if ($monthlySalary <= 1500) {
            return $monthlySalary * 0.01; // 1% if ≤ ₱1,500
        } else {
            $baseSalary = min($monthlySalary, 5000);
            return $baseSalary * 0.02; // 2% capped at ₱5,000
        }
    }

    private function calculateWithholdingTax(float $annualIncome): float
    {
        // TRAIN Law 2024 Tax Table (Annual)
        if ($annualIncome <= 250000) {
            return 0; // Tax exempt
        } elseif ($annualIncome <= 400000) {
            return ($annualIncome - 250000) * 0.15;
        } elseif ($annualIncome <= 800000) {
            return 22500 + (($annualIncome - 400000) * 0.20);
        } elseif ($annualIncome <= 2000000) {
            return 102500 + (($annualIncome - 800000) * 0.25);
        } elseif ($annualIncome <= 8000000) {
            return 402500 + (($annualIncome - 2000000) * 0.30);
        } else {
            return 2202500 + (($annualIncome - 8000000) * 0.35);
        }
    }

    public function createPayrollRun(int $periodId): int
    {
        $stmt = $this->db->prepare("INSERT INTO payroll_runs (payroll_period_id, processed_at, status) VALUES (:pid, NOW(), 'draft')");
        $stmt->execute([':pid' => $periodId]);
        return (int)$this->db->lastInsertId();
    }
    public function generatePayslip(int $runId, int $employeeId, array $data): void
    {
        if ($data['gross_pay'] <= 0) {
            return; // Do not insert empty payslips
        }

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
    public function closePayrollPeriod(int $periodId): bool
    {
        $stmt = $this->db->prepare("UPDATE payroll_periods SET status='closed' WHERE id=:pid");
        return $stmt->execute([':pid' => $periodId]);
    }
    public function finalizeRun($runId)
    {
        $stmt = $this->db->prepare("UPDATE payroll_runs SET status='finalized' WHERE id=:rid AND status='draft' ");
        return $stmt->execute([':rid' => $runId]);
    }
    // Preview payroll before processing
    public function getPayrollPreview(int $periodId): array
    {
        $employees = $this->getEmployeesForPayroll($periodId);
        $preview = [];

        foreach ($employees as $emp) {
            $payroll = $this->calculateEmployeePayroll($emp['id'], $periodId);


            if (!isset($payroll['gross_pay']) || $payroll['gross_pay'] <= 0) {
                continue;
            }

            $preview[] = array_merge($emp, $payroll);
        }

        return $preview;
    }
}
