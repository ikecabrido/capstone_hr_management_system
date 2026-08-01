<?php
class PayrollModel
{
    private PDO $db;

    // Position Configuration - Defines payroll rules by position category
    private const POSITION_CONFIG = [
        'teacher' => [
            'positions' => ['Teacher', 'Assistant Teacher', 'Instructor'],
            'pay_type' => 'unit_based',
            'has_overtime' => false,
            'overtime_multiplier' => null,
            'absence_deduction' => 1536
        ],
        'admin' => [
            'positions' => ['Principal', 'Vice Principal', 'Administrative Secretary', 'Admin', 'Registrar', 'Finance Officer', 'Academic Coordinator'],
            'pay_type' => 'daily_rate',
            'has_overtime' => true,
            'overtime_multiplier' => 1.25,
            'absence_deduction' => 1020
        ],
        'support' => [
            'positions' => ['Janitor', 'Maintenance Worker', 'Security Guard', 'Driver', 'Canteen Staff', 'Gardener', 'Groundskeeper'],
            'pay_type' => 'daily_rate',
            'has_overtime' => false,
            'overtime_multiplier' => 1.0,
            'absence_deduction' => 800
        ],
        'professional' => [
            'positions' => ['Librarian', 'Counselor', 'School Nurse', 'IT Support', 'Coordinator'],
            'pay_type' => 'daily_rate',
            'has_overtime' => true,
            'overtime_multiplier' => 1.25,
            'absence_deduction' => 1020
        ]
    ];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Get position category configuration by position type
     */
    private function getPositionCategory(string $positionType): ?array
    {
        foreach (self::POSITION_CONFIG as $category => $config) {
            if (in_array($positionType, $config['positions'], true)) {
                return $config;
            }
        }
        // Default to 'admin' if position not found
        return self::POSITION_CONFIG['admin'];
    }

    // Get TA metrics for a period
    public function getTimeAttendanceMetrics($employeeId, $startDate, $endDate): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(regular_hours) AS total_regular_hours,
                SUM(overtime_hours) AS total_overtime_hours,
                SUM(late_minutes) AS total_late_minutes,
                SUM(early_out_minutes) AS total_early_out_minutes,
                SUM(total_hours_worked) AS total_hours_worked,
                COUNT(CASE WHEN status='PRESENT' THEN 1 END) AS present_days,
                COUNT(CASE WHEN status='ABSENT' THEN 1 END) AS total_absent_days,
                COUNT(CASE WHEN status='LATE' THEN 1 END) AS late_days,
                -- Count unexcused absences only (exclude approved leaves)
                SUM(CASE 
                    WHEN status='ABSENT' AND a.attendance_id NOT IN (
                        SELECT attendance_id FROM ta_absence_late_records 
                        WHERE employee_id = :eid 
                          AND type='ABSENT'
                          AND (is_excused = TRUE OR excuse_status IN ('APPROVED', 'AWAITING_DOCUMENTS'))
                    ) 
                    THEN 1 
                    ELSE 0 
                END) AS unexcused_absent_days
            FROM ta_attendance a
            WHERE a.employee_id = :eid
              AND a.attendance_date BETWEEN :start AND :end
        ");
        $stmt->execute([
            ':eid' => $employeeId,
            ':start' => $startDate,
            ':end' => $endDate
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Existing method for overview
    public function getSalaryOverview(?int $periodId = null, ?string $employmentType = null): array
    {
        $sql = "
        SELECT 
            p.payslip_id AS payroll_id, 
            e.full_name AS employee_name,
            e.position AS position,
            e.employment_status AS employment_type,
            pp.period_name, 
            p.gross_pay, 
            p.total_deductions, 
            p.net_pay,
            pr.status as payroll_status
        FROM pr_payslips p
        JOIN employees e ON p.employee_id = e.employee_id
        LEFT JOIN pr_runs pr ON p.payroll_run_id = pr.run_id
        LEFT JOIN pr_periods pp ON pr.payroll_period_id = pp.period_id
        WHERE e.employment_status = 'Active'
    ";

        $params = [];

        if ($periodId !== null) {
            $sql .= " AND pp.period_id = :periodId";
            $params[':periodId'] = $periodId;
        }
        if ($employmentType !== null && $employmentType !== '') {
            $sql .= " AND e.employment_status = :employmentType";
            $params[':employmentType'] = $employmentType;
        }

        $sql .= " ORDER BY pp.start_date DESC, e.full_name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getEmploymentTypes(): array
    {
        // Return available employment status types from new schema
        return ['Regular', 'Contract', 'Part-Time', 'Casual'];
    }
    public function isPeriodClosed(int $periodId): bool
    {
        $stmt = $this->db->prepare("SELECT status FROM pr_periods WHERE period_id = ?");
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
        e.full_name, 
        e.position,
        e.employment_status AS employment_type
    FROM pr_payslips p
    JOIN employees e ON p.employee_id = e.employee_id
    WHERE p.payslip_id = :id
");

        $stmt->execute([':id' => $payslipId]);
        $payslip = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$payslip) return null;

        // Breakdown items (earnings/deductions)
        $stmt2 = $this->db->prepare("
            SELECT item_type, description, amount
            FROM pr_payslip_items
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
        $stmt = $this->db->query("SELECT * FROM pr_periods ORDER BY start_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllEmployees(): array
    {
        $stmt = $this->db->query("
        SELECT employee_id as id,
               full_name AS name
        FROM employees
        WHERE employment_status = 'Active'
        ORDER BY full_name
    ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTeacherEmployees(): array
    {
        $positions = [
            'Teacher',
            'Assistant Teacher',
            'Instructor',
            'Professor',
            'Associate Professor'
        ];

        $placeholders = implode(',', array_fill(0, count($positions), '?'));
        $stmt = $this->db->prepare(
            "SELECT e.employee_id as id,\n" .
                "       e.full_name AS name,\n" .
                "       e.position\n" .
                "FROM employees e\n" .
                "WHERE e.employment_status = 'Active'\n" .
                "  AND e.position IN ($placeholders)\n" .
                "ORDER BY e.full_name"
        );

        $stmt->execute($positions);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getEmployeesForPayroll(int $periodId): array
    {
        $stmt = $this->db->prepare("
        SELECT e.employee_id, e.full_name AS name, e.position
        FROM employees e
        WHERE e.employment_status='Active'
        AND NOT EXISTS (
            SELECT 1
            FROM pr_payslips p
            JOIN pr_runs pr ON pr.run_id = p.payroll_run_id
            WHERE p.employee_id = e.employee_id
            AND pr.payroll_period_id = :periodId
        )
    ");
        $stmt->execute(['periodId' => $periodId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllActiveEmployeesForPeriod(int $periodId): array
    {
        $stmt = $this->db->prepare("
        SELECT e.employee_id, e.full_name AS name, e.position, e.department
        FROM employees e
        WHERE e.employment_status='Active'
    ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function calculateEmployeePayroll(string $employeeId, int $periodId): array
    {
        /* ==============================
       Get Payroll Period Dates
    ============================== */
        $stmtPeriod = $this->db->prepare("
        SELECT start_date, end_date
        FROM pr_periods
        WHERE period_id = :pid
    ");
        $stmtPeriod->execute([':pid' => $periodId]);
        $period = $stmtPeriod->fetch(PDO::FETCH_ASSOC);
        if (!$period) {
            return [];
        }
        $start = $period['start_date'];
        $end   = $period['end_date'];

        /* ==============================
       Get Payroll Employee Configuration
       (Base Salary, Position Type)
       If not found in pr_employee_details, use defaults from employees table
    ============================== */
        $stmtConfig = $this->db->prepare("
        SELECT 
            pd.base_salary,
            pd.position_type
        FROM pr_employee_details pd
        WHERE pd.employee_id = :eid
    ");
        $stmtConfig->execute([':eid' => $employeeId]);
        $config = $stmtConfig->fetch(PDO::FETCH_ASSOC);

        // If no config found, get position from employees table and use default salary
        if (!$config) {
            $stmtEmployee = $this->db->prepare("
                SELECT position
                FROM employees
                WHERE employee_id = :eid
            ");
            $stmtEmployee->execute([':eid' => $employeeId]);
            $employee = $stmtEmployee->fetch(PDO::FETCH_ASSOC);

            if (!$employee) {
                return []; // Employee not found
            }

            // Map position to default salary and category
            $positionMapping = [
                'Professor' => ['salary' => 40000, 'category' => 'Teacher'],
                'Associate Professor' => ['salary' => 35000, 'category' => 'Teacher'],
                'Instructor' => ['salary' => 28000, 'category' => 'Teacher'],
                'Software Developer' => ['salary' => 35000, 'category' => 'Admin'],
                'HR Manager' => ['salary' => 30000, 'category' => 'Admin'],
                'Accountant' => ['salary' => 28000, 'category' => 'Admin'],
                'Administrative Officer' => ['salary' => 22000, 'category' => 'Admin'],
            ];

            $position = $employee['position'];
            $defaultConfig = $positionMapping[$position] ?? ['salary' => 20000, 'category' => 'Admin'];

            $config = [
                'base_salary' => $defaultConfig['salary'],
                'position_type' => $defaultConfig['category']
            ];
        }

        $baseSalaryMonthly = (float)($config['base_salary'] ?? 0);
        $positionType = $config['position_type'] ?? 'Admin';

        // Get position category configuration
        $positionCategory = $this->getPositionCategory($positionType);

        // For teachers: Get teaching load from College Coordinator's assignments
        $teacherQualification = 'ProfEd';
        $teachingUnits = 0;

        if ($positionCategory['pay_type'] === 'unit_based') {
            // Get current teacher load from pr_teacher_loads table
            // Find the load that covers the payroll period
            $stmtTeacherLoad = $this->db->prepare("
                SELECT qualification, total_units
                FROM pr_teacher_loads
                WHERE employee_id = :eid
                  AND :pstart >= DATE_FORMAT(NOW(), '%Y-01-01')
                LIMIT 1
            ");
            $stmtTeacherLoad->execute([
                ':eid' => $employeeId,
                ':pstart' => $start
            ]);
            $teacherLoad = $stmtTeacherLoad->fetch(PDO::FETCH_ASSOC);

            if ($teacherLoad) {
                $teacherQualification = $teacherLoad['qualification'] ?? 'ProfEd';
                $teachingUnits = (float)($teacherLoad['total_units'] ?? 0);
            }
        }

        /* ==============================
       Get Employee Contributions Status
       (SSS, PhilHealth, Pag-IBIG)
    ============================== */
        $stmtContributions = $this->db->prepare("
        SELECT contribution_type, status
        FROM employee_contributions
        WHERE employee_id = :eid
    ");
        $stmtContributions->execute([':eid' => $employeeId]);
        $contributions = $stmtContributions->fetchAll(PDO::FETCH_ASSOC);

        // Check which contributions have been submitted
        $submittedContributions = [];
        foreach ($contributions as $contrib) {
            if ($contrib['status'] === 'submitted') {
                $submittedContributions[$contrib['contribution_type']] = true;
            }
        }

        /* ==============================
       Get Position Deduction Rates
       Uses position category defaults, with database overrides if available
    ============================== */
        $stmtRates = $this->db->prepare("
        SELECT absence_deduction_amount, late_per_minute_rate, late_per_hour_rate
        FROM pr_position_deduction_rates
        WHERE position_type = :ptype AND is_active = 1
    ");
        $stmtRates->execute([':ptype' => $positionType]);
        $rates = $stmtRates->fetch(PDO::FETCH_ASSOC);

        // Use config-based absence deduction, or override from database if available
        $absenceDeductionAmount = (float)($rates['absence_deduction_amount'] ?? $positionCategory['absence_deduction'] ?? 1020);
        $latePerMinute = (float)($rates['late_per_minute_rate'] ?? 2.00);

        /* ==============================
       Get Time & Attendance Metrics
    ============================== */
        $attendance = $this->getTimeAttendanceMetrics($employeeId, $start, $end);
        $daysWorked = 0;
        $totalAbsentDays = 0;
        $unapprovedAbsentDays = 0;  // Only unexcused absences for deduction
        $lateMinutes = 0;
        $hoursWorked = 0;

        if ($attendance) {
            $daysWorked = (int)($attendance['present_days'] ?? 0);
            $totalAbsentDays = (int)($attendance['total_absent_days'] ?? 0);
            $unapprovedAbsentDays = (int)($attendance['unexcused_absent_days'] ?? 0);
            $lateMinutes = (int)($attendance['total_late_minutes'] ?? 0);
            $hoursWorked = (float)($attendance['total_hours_worked'] ?? 0);
        }

        /* ==============================
       CALCULATE BASE SALARY BASED ON POSITION
    ============================== */
        $basicSalary = 0;
        $salarybasis = '';

        if ($positionCategory['pay_type'] === 'unit_based') {
            // Teacher: (teaching_units × pay_per_unit) ÷ 2 (semi-monthly)
            $stmtTeacher = $this->db->prepare("
            SELECT pay_per_unit
            FROM pr_teacher_qualification_rates
            WHERE qualification = :qual AND is_active = 1
        ");
            $stmtTeacher->execute([':qual' => $teacherQualification]);
            $teacherRate = $stmtTeacher->fetch(PDO::FETCH_ASSOC);

            $payPerUnit = (float)($teacherRate['pay_per_unit'] ?? 128);
            $basicSalary = ($teachingUnits * $payPerUnit) / 2;
            $salarybasis = "{$positionType} ({$teachingUnits} units × ₱{$payPerUnit}/unit)";
        } else {
            // All other positions (Admin, Support, Professional): base_salary ÷ 2 ÷ 15 × days_worked
            $semiMonthly = $baseSalaryMonthly / 2;
            $dailyRate = $semiMonthly / 15;
            $basicSalary = $dailyRate * $daysWorked;
            $salarybasis = "{$positionType} (₱{$baseSalaryMonthly} ÷ 2 ÷ 15 × {$daysWorked} days)";
        }

        /* ==============================
       Build Earnings
    ============================== */
        $earnings = [
            [
                'description' => "Basic Salary - {$salarybasis}",
                'amount' => $basicSalary
            ]
        ];
        $grossPay = $basicSalary;

        /* ==============================
       OVERTIME PAY 
       Multiplier varies by position category
       Hourly rate = (Base Salary ÷ 2) ÷ 160 hours
    ============================== */
        $overtimePay = 0;
        $overtimeHours = 0;
        $overtimeMultiplier = $positionCategory['overtime_multiplier'] ?? 1.0;

        if ($positionCategory['has_overtime'] && $attendance) {
            $overtimeHours = (float)($attendance['total_overtime_hours'] ?? 0);

            if ($overtimeHours > 0) {
                // Calculate hourly rate
                $semiMonthly = $baseSalaryMonthly / 2;
                $hourlyRate = $semiMonthly / 160; // 160 = standard monthly working hours (20 days × 8 hrs)

                // Calculate overtime pay with 1.25x multiplier
                $overtimePay = $overtimeHours * $hourlyRate * $overtimeMultiplier;

                $earnings[] = [
                    'description' => "Overtime ({$overtimeHours} hrs × ₱" . number_format($hourlyRate, 2) . "/hr × {$overtimeMultiplier}x)",
                    'amount' => $overtimePay
                ];
                $grossPay += $overtimePay;
            }
        }

        /* ==============================
       Get Adjustments (Per Period)
    ============================== */
        $stmtAdj = $this->db->prepare("
    SELECT type, description, amount
    FROM pr_employee_adjustments
    WHERE employee_id = :eid
      AND payroll_period_id = :pid
");
        $stmtAdj->execute([
            ':eid' => $employeeId,
            ':pid' => $periodId
        ]);
        $adjustments = $stmtAdj->fetchAll(PDO::FETCH_ASSOC);

        foreach ($adjustments as $adj) {
            if (in_array($adj['type'], ['allowance', 'benefit'], true)) {
                $earnings[] = [
                    'description' => $adj['description'],
                    'amount' => (float)$adj['amount']
                ];
                $grossPay += (float)$adj['amount'];
            }
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
       Build Deductions Array
    ============================== */
        $deductions = [];
        $totalDeductions = 0;

        /* ==============================
       1. TRIO DEDUCTIONS (₱200 each - fixed amount)
       Only applied if employee has submitted contribution
    ============================== */
        if (!empty($submittedContributions['sss'])) {
            $deductions[] = [
                'description' => 'SSS',
                'amount' => 200.00
            ];
            $totalDeductions += 200.00;
        }

        if (!empty($submittedContributions['philhealth'])) {
            $deductions[] = [
                'description' => 'PhilHealth',
                'amount' => 200.00
            ];
            $totalDeductions += 200.00;
        }

        if (!empty($submittedContributions['pagibig'])) {
            $deductions[] = [
                'description' => 'Pag-IBIG',
                'amount' => 200.00
            ];
            $totalDeductions += 200.00;
        }

        /* ==============================
       2. ABSENCE DEDUCTION
       Position-based: Admin = ₱1,020, Teacher = ₱1,536
       ONLY UNEXCUSED ABSENCES (Approved leaves are not deducted)
    ============================== */
        if ($unapprovedAbsentDays > 0) {
            $absenceDeduction = $unapprovedAbsentDays * $absenceDeductionAmount;
            $deductions[] = [
                'description' => "Unexcused Absence ({$unapprovedAbsentDays} day(s) × ₱" . number_format($absenceDeductionAmount, 2) . ")",
                'amount' => $absenceDeduction
            ];
            $totalDeductions += $absenceDeduction;
        }

        /* ==============================
       3. LATE CHARGES
       ₱2 per minute late
    ============================== */
        if ($lateMinutes > 0) {
            $lateDeduction = $lateMinutes * $latePerMinute;
            $deductions[] = [
                'description' => "Late ({$lateMinutes} minutes × ₱{$latePerMinute}/min)",
                'amount' => $lateDeduction
            ];
            $totalDeductions += $lateDeduction;
        }

        /* ==============================
       4. OTHER ADJUSTMENTS
    ============================== */
        foreach ($adjustments as $adj) {
            if ($adj['type'] === 'deduction') {
                $deductions[] = [
                    'description' => $adj['description'],
                    'amount' => (float)$adj['amount']
                ];
                $totalDeductions += (float)$adj['amount'];
            }
        }

        /* ==============================
       NET PAY CALCULATION
    ============================== */
        $netPay = $grossPay - $totalDeductions;

        return [
            'gross_pay' => $grossPay,
            'net_pay' => $netPay,
            'total_deductions' => $totalDeductions,
            'earnings' => $earnings,
            'deductions' => $deductions,
            'hours_worked' => $hoursWorked,
            'days_worked' => $daysWorked,
            'total_absent_days' => $totalAbsentDays,
            'unexcused_absent_days' => $unapprovedAbsentDays,
            'approved_leave_days' => ($totalAbsentDays - $unapprovedAbsentDays),
            'late_minutes' => $lateMinutes,
            'overtime_hours' => $overtimeHours,
            'overtime_pay' => $overtimePay,
            'overtime_multiplier' => $overtimeMultiplier,
            'position_type' => $positionType
        ];
    }



    public function createPayrollRun(int $periodId, ?int $userId = null): int
    {
        $stmt = $this->db->prepare("INSERT INTO pr_runs (payroll_period_id, processed_at, status, finalized_by) VALUES (:pid, NOW(), 'draft', :uid)");
        $stmt->execute([':pid' => $periodId, ':uid' => $userId]);
        return (int)$this->db->lastInsertId();
    }
    public function generatePayslip(int $runId, string $employeeId, array $data): void
    {
        if ($data['gross_pay'] <= 0) {
            return; // Do not insert empty payslips
        }

        // Insert main payslip
        $stmt = $this->db->prepare("
        INSERT INTO pr_payslips (payroll_run_id, employee_id, gross_pay, total_deductions, net_pay)
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
        INSERT INTO pr_payslip_items (payslip_id, item_type, description, amount)
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
        $stmt = $this->db->prepare("UPDATE pr_periods SET status='closed' WHERE period_id=:pid");
        return $stmt->execute([':pid' => $periodId]);
    }
    public function finalizeRun($runId)
    {
        $stmt = $this->db->prepare("UPDATE pr_runs SET status='finalized' WHERE run_id=:rid AND status='draft' ");
        return $stmt->execute([':rid' => $runId]);
    }
    // Preview payroll before processing
    public function getPayrollPreview(int $periodId): array
    {
        $employees = $this->getAllActiveEmployeesForPeriod($periodId);
        $preview = [];

        foreach ($employees as $emp) {
            $payroll = $this->calculateEmployeePayroll($emp['employee_id'], $periodId);

            if (!isset($payroll['gross_pay'])) {
                continue;
            }

            $preview[] = array_merge($emp, $payroll);
        }

        return $preview;
    }

    /* ==============================
       EXIT MANAGEMENT INTEGRATION
    ============================== */

    /**
     * Calculate final/exit payslip for a departing employee
     * Uses data from exit_resignations and exit_employee_settlements
     * 
     * @param int $employeeId
     * @param int $settlementId
     * @return array Payslip calculation data (same format as calculateEmployeePayroll)
     */
    public function calculateExitPayslip(int $employeeId, int $settlementId): array
    {
        /* ==============================
           Get Settlement & Resignation Data
        ============================== */
        $stmtSettlement = $this->db->prepare("
            SELECT 
                es.*,
                er.last_working_date,
                er.resignation_type
            FROM exit_employee_settlements es
            LEFT JOIN exit_resignations er ON es.resignation_id = er.id
            WHERE es.id = :sid AND es.employee_id = :eid
        ");
        $stmtSettlement->execute([
            ':sid' => $settlementId,
            ':eid' => $employeeId
        ]);
        $settlement = $stmtSettlement->fetch(PDO::FETCH_ASSOC);

        if (!$settlement) {
            return [];
        }

        $lastWorkingDate = $settlement['last_working_date'];

        /* ==============================
           Get Employee Configuration
        ============================== */
        $stmtConfig = $this->db->prepare("
            SELECT 
                pd.base_salary,
                pd.position_type
            FROM pr_employee_details pd
            WHERE pd.employee_id = :eid
        ");
        $stmtConfig->execute([':eid' => $employeeId]);
        $config = $stmtConfig->fetch(PDO::FETCH_ASSOC);

        if (!$config) {
            return [];
        }

        $baseSalaryMonthly = (float)($config['base_salary'] ?? 0);
        $positionType = $config['position_type'] ?? 'Admin';

        // Get position category configuration
        $positionCategory = $this->getPositionCategory($positionType);

        /* ==============================
           Get Time & Attendance (up to last_working_date)
        ============================== */
        // Assuming payroll period starts from beginning of month
        $periodStart = date('Y-m-01');
        $periodEnd = $lastWorkingDate;

        $attendance = $this->getTimeAttendanceMetrics($employeeId, $periodStart, $periodEnd);
        $daysWorked = 0;
        $unapprovedAbsentDays = 0;
        $lateMinutes = 0;

        if ($attendance) {
            $daysWorked = (int)($attendance['present_days'] ?? 0);
            $unapprovedAbsentDays = (int)($attendance['unexcused_absent_days'] ?? 0);
            $lateMinutes = (int)($attendance['total_late_minutes'] ?? 0);
        }

        /* ==============================
           Get Deduction Rates
           Uses position category defaults, with database overrides if available
        ============================== */
        $stmtRates = $this->db->prepare("
            SELECT absence_deduction_amount, late_per_minute_rate
            FROM pr_position_deduction_rates
            WHERE position_type = :ptype AND is_active = 1
        ");
        $stmtRates->execute([':ptype' => $positionType]);
        $rates = $stmtRates->fetch(PDO::FETCH_ASSOC);

        // Use config-based absence deduction, or override from database if available
        $absenceDeductionAmount = (float)($rates['absence_deduction_amount'] ?? $positionCategory['absence_deduction'] ?? 1020);
        $latePerMinute = (float)($rates['late_per_minute_rate'] ?? 2.00);

        /* ==============================
           Calculate Pro-rata Basic Salary
        ============================== */
        $semiMonthly = $baseSalaryMonthly / 2;
        $dailyRate = $semiMonthly / 15;
        $basicSalary = $dailyRate * $daysWorked;
        $salarybasis = "Pro-rata Salary ({$daysWorked} days × ₱" . number_format($dailyRate, 2) . "/day)";

        /* ==============================
           Build Earnings
        ============================== */
        $earnings = [
            [
                'description' => $salarybasis,
                'amount' => $basicSalary
            ]
        ];

        // Add gratuity from settlement
        if ($settlement['gratuity'] > 0) {
            $earnings[] = [
                'description' => 'Gratuity',
                'amount' => (float)$settlement['gratuity']
            ];
        }

        // Add notice pay from settlement
        if ($settlement['notice_pay'] > 0) {
            $earnings[] = [
                'description' => 'Notice Pay',
                'amount' => (float)$settlement['notice_pay']
            ];
        }

        $grossPay = $basicSalary + ((float)$settlement['gratuity'] ?? 0) + ((float)$settlement['notice_pay'] ?? 0);

        /* ==============================
           Build Deductions
        ============================== */
        $deductions = [];
        $totalDeductions = 0;

        // Get contributions status
        $stmtContributions = $this->db->prepare("
            SELECT contribution_type, status
            FROM employee_contributions
            WHERE employee_id = :eid
        ");
        $stmtContributions->execute([':eid' => $employeeId]);
        $contributions = $stmtContributions->fetchAll(PDO::FETCH_ASSOC);

        $submittedContributions = [];
        foreach ($contributions as $contrib) {
            if ($contrib['status'] === 'submitted') {
                $submittedContributions[$contrib['contribution_type']] = true;
            }
        }

        // 1. TRIO DEDUCTIONS
        if (!empty($submittedContributions['sss'])) {
            $deductions[] = [
                'description' => 'SSS',
                'amount' => 200.00
            ];
            $totalDeductions += 200.00;
        }

        if (!empty($submittedContributions['philhealth'])) {
            $deductions[] = [
                'description' => 'PhilHealth',
                'amount' => 200.00
            ];
            $totalDeductions += 200.00;
        }

        if (!empty($submittedContributions['pagibig'])) {
            $deductions[] = [
                'description' => 'Pag-IBIG',
                'amount' => 200.00
            ];
            $totalDeductions += 200.00;
        }

        // 2. ABSENCE DEDUCTION (only for worked period)
        if ($unapprovedAbsentDays > 0) {
            $absenceDeduction = $unapprovedAbsentDays * $absenceDeductionAmount;
            $deductions[] = [
                'description' => "Unexcused Absence ({$unapprovedAbsentDays} day(s) × ₱" . number_format($absenceDeductionAmount, 2) . ")",
                'amount' => $absenceDeduction
            ];
            $totalDeductions += $absenceDeduction;
        }

        // 3. LATE CHARGES
        if ($lateMinutes > 0) {
            $lateDeduction = $lateMinutes * $latePerMinute;
            $deductions[] = [
                'description' => "Late ({$lateMinutes} minutes × ₱{$latePerMinute}/min)",
                'amount' => $lateDeduction
            ];
            $totalDeductions += $lateDeduction;
        }

        // 4. OUTSTANDING LOANS (from settlement)
        if ($settlement['outstanding_loans'] > 0) {
            $deductions[] = [
                'description' => 'Outstanding Loans',
                'amount' => (float)$settlement['outstanding_loans']
            ];
            $totalDeductions += (float)$settlement['outstanding_loans'];
        }

        // 5. OTHER DEDUCTIONS (from settlement)
        if ($settlement['other_deductions'] > 0) {
            $deductions[] = [
                'description' => 'Other Deductions',
                'amount' => (float)$settlement['other_deductions']
            ];
            $totalDeductions += (float)$settlement['other_deductions'];
        }

        /* ==============================
           Calculate Net Pay
        ============================== */
        $netPay = $grossPay - $totalDeductions;

        return [
            'gross_pay' => $grossPay,
            'net_pay' => $netPay,
            'total_deductions' => $totalDeductions,
            'earnings' => $earnings,
            'deductions' => $deductions,
            'is_exit_settlement' => true,
            'position_type' => $positionType,
            'last_working_date' => $lastWorkingDate
        ];
    }

    /**
     * Generate exit payslip and mark settlement as paid
     */
    public function generateExitPayslip(int $runId, int $employeeId, int $settlementId, array $data): ?int
    {
        if ($data['gross_pay'] <= 0) {
            return null;
        }

        try {
            // Insert exit payslip
            $stmt = $this->db->prepare("
                INSERT INTO pr_payslips 
                (payroll_run_id, employee_id, gross_pay, total_deductions, net_pay, 
                 is_exit_settlement, settlement_id, generated_at)
                VALUES (:run, :eid, :gross, :ded, :net, 1, :sid, NOW())
            ");
            $stmt->execute([
                ':run' => $runId,
                ':eid' => $employeeId,
                ':gross' => $data['gross_pay'],
                ':ded' => $data['total_deductions'],
                ':net' => $data['net_pay'],
                ':sid' => $settlementId
            ]);

            $payslipId = (int)$this->db->lastInsertId();

            // Insert payslip items
            $stmt2 = $this->db->prepare("
                INSERT INTO pr_payslip_items (payslip_id, item_type, description, amount)
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

            // Mark settlement as paid
            $stmtUpdateSettlement = $this->db->prepare("
                UPDATE exit_employee_settlements
                SET status = 'paid'
                WHERE id = :sid
            ");
            $stmtUpdateSettlement->execute([':sid' => $settlementId]);

            return $payslipId;
        } catch (Exception $e) {
            error_log("Error generating exit payslip: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get approved settlement for employee (if exists)
     */
    public function getApprovedSettlement(int $employeeId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT es.*
            FROM exit_employee_settlements es
            WHERE es.employee_id = :eid 
            AND es.status = 'approved'
            ORDER BY es.created_at DESC
            LIMIT 1
        ");
        $stmt->execute([':eid' => $employeeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getExitSettlementsEligibleForClearance(): array
    {
        $stmt = $this->db->query("
            SELECT 
                es.id AS settlement_id,
                es.employee_id,
                e.full_name,
                e.position,
                e.department,
                er.last_working_date,
                es.net_payable,
                es.settlement_date
            FROM exit_employee_settlements es
            JOIN exit_resignations er ON es.resignation_id = er.id
            JOIN employees e ON es.employee_id = e.employee_id
            WHERE es.status = 'approved'
            AND NOT EXISTS (
                SELECT 1
                FROM payroll_clearances pc
                WHERE pc.settlement_id = es.id
                AND pc.status IN ('pending', 'approved')
            )
            ORDER BY es.settlement_date DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createPayrollClearanceRequest(int $settlementId, int $requestedBy): ?int
    {
        $stmt = $this->db->prepare("
            INSERT INTO payroll_clearances
            (settlement_id, requested_by, requested_at, status)
            VALUES (:settlement_id, :requested_by, NOW(), 'pending')
        ");
        $stmt->execute([
            ':settlement_id' => $settlementId,
            ':requested_by' => $requestedBy
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getPayrollClearanceBySettlementId(int $settlementId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM payroll_clearances
            WHERE settlement_id = :settlement_id
            ORDER BY requested_at DESC
            LIMIT 1
        ");
        $stmt->execute([':settlement_id' => $settlementId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getPayrollClearancesByStatus(string $status): array
    {
        $stmt = $this->db->prepare("
            SELECT pc.*, es.settlement_date, es.net_payable, e.full_name, e.employee_id, e.position, e.department
            FROM payroll_clearances pc
            JOIN exit_employee_settlements es ON pc.settlement_id = es.id
            JOIN employees e ON es.employee_id = e.employee_id
            WHERE pc.status = :status
            ORDER BY pc.requested_at DESC
        ");
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllPayrollClearances(): array
    {
        $stmt = $this->db->query("
            SELECT pc.*, es.settlement_date, es.net_payable, e.full_name, e.employee_id, e.position, e.department
            FROM payroll_clearances pc
            JOIN exit_employee_settlements es ON pc.settlement_id = es.id
            JOIN employees e ON es.employee_id = e.employee_id
            ORDER BY pc.requested_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPayrollClearanceById(int $clearanceId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT pc.*, es.settlement_date, es.net_payable, es.gratuity, es.notice_pay, es.outstanding_loans,
                   es.other_deductions, e.full_name, e.employee_id, e.position, e.department,
                   er.resignation_type, er.last_working_date,
                   p.gross_pay, p.total_deductions, p.net_pay
            FROM payroll_clearances pc
            JOIN exit_employee_settlements es ON pc.settlement_id = es.id
            JOIN employees e ON es.employee_id = e.employee_id
            LEFT JOIN exit_resignations er ON es.resignation_id = er.id
            LEFT JOIN pr_payslips p ON p.settlement_id = es.id AND p.is_exit_settlement = 1
            WHERE pc.id = :id
            ORDER BY p.generated_at DESC
            LIMIT 1
        ");
        $stmt->execute([':id' => $clearanceId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function updatePayrollClearanceStatus(int $clearanceId, string $status, int $approvedBy, ?string $comments = null): bool
    {
        $stmt = $this->db->prepare("
            UPDATE payroll_clearances
            SET status = :status,
                approved_by = :approved_by,
                approved_at = NOW(),
                comments = :comments,
                last_updated = NOW()
            WHERE id = :id
        ");
        return $stmt->execute([
            ':status' => $status,
            ':approved_by' => $approvedBy,
            ':comments' => $comments,
            ':id' => $clearanceId
        ]);
    }

    /**
     * Check if employee has an approved resignation
     */
    public function hasApprovedResignation(int $employeeId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM exit_resignations
            WHERE employee_id = :eid
            AND status = 'approved'
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute([':eid' => $employeeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
