<?php
require_once __DIR__ . '/ExitSettlementCalculator.php';

class PayrollModel
{
    private PDO $db;
    private string $lastResolvedSalarySource = 'none';

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
        // Ensure payroll clearance schema exists so Exit Management can create requests even
        // if the migration hasn't been applied.
        $this->ensurePayrollClearancesSchema();
    }

    /**
     * Create payroll_clearances table if it does not exist (defensive migration)
     */
    private function ensurePayrollClearancesSchema(): void
    {
        try {
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS `payroll_clearances` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `settlement_id` int(11) NOT NULL,
                  `requested_by` int(11) DEFAULT NULL,
                  `requested_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
                  `approved_by` int(11) DEFAULT NULL,
                  `approved_at` datetime DEFAULT NULL,
                  `comments` text DEFAULT NULL,
                  `last_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_settlement_id` (`settlement_id`),
                  KEY `idx_status` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        } catch (Exception $e) {
            error_log('Failed to ensure payroll_clearances schema: ' . $e->getMessage());
        }
    }

    /**
     * Get TRAIN Law contribution rate for a given contribution type and salary
     * Handles salary brackets for Pag-IBIG (1% vs 2%)
     * @param string $contributionType - 'sss', 'philhealth', 'pagibig'
     * @param float $monthlySalary - Monthly salary for bracket matching
     * @return array|null - ['employee_rate' => float, 'is_percentage' => bool]
     */
    private function getContributionRate(string $contributionType, float $monthlySalary = 0): ?array
    {
        try {
            $sql = "
                SELECT employee_rate, is_percentage 
                FROM pr_contribution_rates 
                WHERE contribution_type = :type 
                  AND is_active = 1
                  AND :salary >= min_salary 
                  AND :salary <= max_salary
                LIMIT 1
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':type' => strtolower($contributionType),
                ':salary' => $monthlySalary
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Fallback to 2026 TRAIN Law rates if table doesn't exist
            error_log('Contribution rates table error: ' . $e->getMessage());
            $defaults = [
                'sss' => 5.00,
                'philhealth' => 2.50,
                'pagibig' => ($monthlySalary <= 1500) ? 1.00 : 2.00
            ];
            return [
                'employee_rate' => $defaults[strtolower($contributionType)] ?? 0,
                'is_percentage' => 1
            ];
        }
    }

    /**
     * Calculate contribution amount based on 2026 TRAIN Law
     * Handles salary brackets for Pag-IBIG
     * @param string $contributionType - 'sss', 'philhealth', 'pagibig'
     * @param float $monthlySalary - Monthly gross salary
     * @param bool $isSemiMonthly - If true, calculate for semi-monthly period
     * @return float - Contribution amount
     */
    private function calculateContribution(string $contributionType, float $monthlySalary, bool $isSemiMonthly = true): float
    {
        $rate = $this->getContributionRate($contributionType, $monthlySalary);
        
        if (!$rate) {
            // Fallback 2026 TRAIN Law defaults
            if (strtolower($contributionType) === 'pagibig') {
                $ratePercent = ($monthlySalary <= 1500) ? 1.00 : 2.00;
            } else {
                $defaults = [
                    'sss' => 5.00,
                    'philhealth' => 2.50
                ];
                $ratePercent = $defaults[strtolower($contributionType)] ?? 0;
            }
        } else {
            $ratePercent = (float)$rate['employee_rate'];
        }

        // Calculate contribution as percentage of salary
        $monthlyContribution = $monthlySalary * ($ratePercent / 100);

        // Divide by 2 for semi-monthly payment
        return $isSemiMonthly ? $monthlyContribution / 2 : $monthlyContribution;
    }

    /**
     * Map position name from employees table to payroll category
     * @param string $position - Position name from employees table
     * @return string Category key (teacher, admin, support, professional)
     */
    private function mapPositionToCategory(string $position): string
    {
        $positionMap = [
            // Teaching Positions
            'Teacher' => 'teacher',
            'Assistant Teacher' => 'teacher',
            'Instructor' => 'teacher',
            'Professor' => 'teacher',
            'Associate Professor' => 'teacher',

            // Admin/Management Positions
            'Principal' => 'admin',
            'Vice Principal' => 'admin',
            'HR Manager' => 'admin',
            'Accountant' => 'admin',
            'Finance Officer' => 'admin',
            'Admin' => 'admin',
            'System Administrator' => 'admin',
            'Administrative Officer' => 'admin',
            'Registrar' => 'admin',
            'Academic Coordinator' => 'admin',

            // Professional Positions
            'Software Engineer' => 'professional',
            'Junior Developer' => 'professional',
            'IT Support' => 'professional',
            'Librarian' => 'professional',
            'Counselor' => 'professional',
            'School Nurse' => 'professional',
            'HR Specialist' => 'professional',
            'Financial Analyst' => 'professional',
            'Staff Coordinator' => 'professional',
            'Operations Manager' => 'professional',

            // Support Positions
            'Janitor' => 'support',
            'Maintenance Worker' => 'support',
            'Security Guard' => 'support',
            'Driver' => 'support',
            'Canteen Staff' => 'support',
            'Gardener' => 'support',
            'Groundskeeper' => 'support'
        ];

        return $positionMap[$position] ?? 'admin'; // Default to admin
    }

    /**
     * Get position category configuration by category key
     */
    private function getPositionCategory(string $categoryKey): ?array
    {
        return self::POSITION_CONFIG[$categoryKey] ?? self::POSITION_CONFIG['admin'];
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
                COUNT(CASE WHEN status IN ('PRESENT', 'LATE') THEN 1 END) AS present_days,
                COUNT(CASE WHEN status='ABSENT' THEN 1 END) AS total_absent_days,
                COUNT(CASE WHEN status='LATE' THEN 1 END) AS late_days,
                -- Count unexcused absences only (exclude approved leaves)
                SUM(CASE 
                    WHEN status='ABSENT' AND a.attendance_id NOT IN (
                        SELECT attendance_id FROM ta_absence_late_records 
                        WHERE employee_id = :eid 
                          AND type='ABSENT'
                          AND excuse_status IN ('APPROVED', 'AWAITING_DOCUMENTS')
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
       Get Employee Data from employees table
       (Position) and Teacher Qualification from rao_jobs
    ============================== */
        $stmtEmployee = $this->db->prepare("
            SELECT 
                e.position,
                COALESCE(rj.qualifications, 'ProfEd') AS teacher_qualification
            FROM employees e
            LEFT JOIN rao_hired_applicants rha ON e.employee_id = rha.employee_id
            LEFT JOIN rao_jobs rj ON rha.job_id = rj.id
            WHERE e.employee_id = :eid
        ");
        $stmtEmployee->execute([':eid' => $employeeId]);
        $employee = $stmtEmployee->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            return []; // Employee not found
        }

        $position = $employee['position'];
        $teacherQualification = $employee['teacher_qualification'] ?? 'ProfEd';

        /* ==============================
       Get Base Salary from rao_offer_salary
       (where offer_status = 'accepted')
       Join through rao_hired_applicants
       With fallback to position-based default salary
    ============================== */
        $stmtSalary = $this->db->prepare("
            SELECT ros.salary
            FROM rao_offer_salary ros
            JOIN rao_hired_applicants rha ON ros.application_id = rha.application_id
            WHERE rha.employee_id = :eid
              AND ros.offer_status = 'accepted'
            ORDER BY ros.created_at DESC
            LIMIT 1
        ");
        $stmtSalary->execute([':eid' => $employeeId]);
        $salaryRecord = $stmtSalary->fetch(PDO::FETCH_ASSOC);

        $baseSalaryMonthly = (float)($salaryRecord['salary'] ?? 0);

        // Fallback: If no accepted offer salary, use position-based default
        if ($baseSalaryMonthly <= 0) {
            // Position-based salary defaults when no record exists
            $positionDefaults = [
                'Staff Coordinator' => 22000,
                'Teacher' => 30000,
                'Administrative Officer' => 22000,
                'HR Manager' => 30000,
                'Finance Officer' => 28000,
                'Accountant' => 28000,
                'Financial Analyst' => 30000,
                'Principal' => 45000,
                'Vice Principal' => 40000,
                'Registrar' => 32000,
                'Academic Coordinator' => 28000,
                'Librarian' => 24000,
                'Counselor' => 26000,
                'School Nurse' => 26000,
                'IT Support' => 24000,
                'Software Engineer' => 35000,
                'Junior Developer' => 25000,
                'Operations Manager' => 32000,
                'Janitor' => 15000,
                'Maintenance Worker' => 18000,
                'Security Guard' => 16000,
                'Driver' => 16000,
                'Canteen Staff' => 14000,
                'Gardener' => 14000,
                'Groundskeeper' => 14000,
                'Assistant Teacher' => 26000,
                'Instructor' => 28000,
                'Professor' => 40000,
                'Associate Professor' => 35000,
            ];
            $baseSalaryMonthly = (float)($positionDefaults[$position] ?? 20000);
        }

        /* ==============================
       Map position to category and get configuration
    ============================== */
        $categoryKey = $this->mapPositionToCategory($position);
        $positionCategory = $this->getPositionCategory($categoryKey);

        // For teachers: derive teaching units from cc_schedule table (use semester + school_year match)
        $teachingUnits = 0;

        if ($positionCategory['pay_type'] === 'unit_based') {
            // Heuristic: map payroll period start date to academic semester and school_year
            $periodStartMonth = (int)date('n', strtotime($start));
            $startYear = (int)date('Y', strtotime($start));

            if (in_array($periodStartMonth, [6,7,8,9,10])) {
                // Typical 1st semester (June/July - Oct/Nov)
                $semesterKey = '1st Sem';
                $schoolYear = $startYear . '-' . ($startYear + 1);
            } elseif (in_array($periodStartMonth, [11,12,1,2,3])) {
                // Typical 2nd semester (Nov - Mar)
                $semesterKey = '2nd Sem';
                // For months Jan-Mar, the academic year started previous year
                if ($periodStartMonth >= 1 && $periodStartMonth <= 5) {
                    $schoolYear = ($startYear - 1) . '-' . $startYear;
                } else {
                    $schoolYear = $startYear . '-' . ($startYear + 1);
                }
            } else {
                // Fallback to Summer (Apr-May)
                $semesterKey = 'Summer';
                if ($periodStartMonth >= 1 && $periodStartMonth <= 5) {
                    $schoolYear = ($startYear - 1) . '-' . $startYear;
                } else {
                    $schoolYear = $startYear . '-' . ($startYear + 1);
                }
            }

            // Default units per subject (configurable)
            $unitsPerSubject = 3.0;

            // Count schedule rows for the faculty matching semester and school_year
            $stmtSchedule = $this->db->prepare("\n                SELECT COUNT(*) as cnt\n                FROM cc_schedule\n                WHERE faculty_id = :eid\n                  AND school_year = :sy\n                  AND semester = :sem\n            ");
            $stmtSchedule->execute([
                ':eid' => $employeeId,
                ':sy' => $schoolYear,
                ':sem' => $semesterKey
            ]);
            $count = (int)$stmtSchedule->fetchColumn();

            // Each schedule row is treated as one subject assignment; multiply by units per subject
            $teachingUnits = $count * $unitsPerSubject;
        }

        /* ==============================
       Get Employee Contributions Status
       (SSS, PhilHealth, Pag-IBIG)
    ============================== */
        $stmtContributions = $this->db->prepare("
        SELECT contribution_type, status
        FROM lc_employee_contributions
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
        $stmtRates->execute([':ptype' => $categoryKey]);
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
            $salarybasis = "{$position} ({$teachingUnits} units × ₱{$payPerUnit}/unit)";
        } else {
            // All other positions (Admin, Support, Professional): base_salary ÷ 2 ÷ 15 × days_worked
            $semiMonthly = $baseSalaryMonthly / 2;
            $dailyRate = $semiMonthly / 15;
            $basicSalary = $dailyRate * $daysWorked;
            $salarybasis = "{$position} (₱{$baseSalaryMonthly} ÷ 2 ÷ 15 × {$daysWorked} days)";
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
       1. TRIO DEDUCTIONS (2026 TRAIN Law - Percentage Based)
       SSS: 5%, PhilHealth: 2.5%, Pag-IBIG: 1% (≤₱1500) or 2% (>₱1500)
       Only applied if employee has submitted contribution
    ============================== */
        if (!empty($submittedContributions['sss'])) {
            $sssAmount = $this->calculateContribution('sss', $baseSalaryMonthly, true);
            $deductions[] = [
                'description' => 'SSS (5%)',
                'amount' => $sssAmount
            ];
            $totalDeductions += $sssAmount;
        }

        if (!empty($submittedContributions['philhealth'])) {
            $philhealthAmount = $this->calculateContribution('philhealth', $baseSalaryMonthly, true);
            $deductions[] = [
                'description' => 'PhilHealth (2.5%)',
                'amount' => $philhealthAmount
            ];
            $totalDeductions += $philhealthAmount;
        }

        if (!empty($submittedContributions['pagibig'])) {
            $pagibigAmount = $this->calculateContribution('pagibig', $baseSalaryMonthly, true);
            $pagibigRate = ($baseSalaryMonthly <= 1500) ? '1%' : '2%';
            $deductions[] = [
                'description' => "Pag-IBIG ({$pagibigRate})",
                'amount' => $pagibigAmount
            ];
            $totalDeductions += $pagibigAmount;
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
        $netPay = max(0, $grossPay - $totalDeductions);

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
            'overtime_multiplier' => $overtimeMultiplier
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
        $calculator = new ExitSettlementCalculator($this->db);
        return $calculator->calculate($employeeId, $settlementId);
    }

    /**
     * Resolve the most reliable monthly base salary for exit settlement calculations.
     */
    private function resolveExitSettlementBaseSalary(int $employeeId, array $settlement): float
    {
        $this->lastResolvedSalarySource = 'none';

        try {
           $stmtSalary = $this->db->prepare("
               SELECT ros.salary
               FROM rao_offer_salary ros
               JOIN rao_hired_applicants rha ON ros.application_id = rha.application_id
               WHERE rha.employee_id = :eid
                 AND ros.offer_status = 'accepted'
               ORDER BY ros.created_at DESC
               LIMIT 1
           ");
           $stmtSalary->execute([':eid' => $employeeId]);
           $salaryRecord = $stmtSalary->fetch(PDO::FETCH_ASSOC);
           $salary = (float)($salaryRecord['salary'] ?? 0);
           if ($salary > 0) {
               $this->lastResolvedSalarySource = 'accepted_offer';
               return $salary;
           }
        } catch (Exception $e) {
           error_log('Failed to resolve accepted offer salary: ' . $e->getMessage());
        }

        try {
           $stmtSalary = $this->db->prepare("
               SELECT les.rate, lss.basic_salary
               FROM lc_employee_salary les
               LEFT JOIN lc_salary_structures lss ON les.salary_structure_id = lss.id
               WHERE les.employee_id = :eid
               ORDER BY les.effective_date DESC, les.id DESC
               LIMIT 1
           ");
           $stmtSalary->execute([':eid' => $employeeId]);
           $salaryRecord = $stmtSalary->fetch(PDO::FETCH_ASSOC);
           $salary = (float)($salaryRecord['rate'] ?? 0);
           if ($salary <= 0) {
               $salary = (float)($salaryRecord['basic_salary'] ?? 0);
           }
           if ($salary > 0) {
               $this->lastResolvedSalarySource = 'salary_structure';
               return $salary;
           }
        } catch (Exception $e) {
           error_log('Failed to resolve legacy salary structure: ' . $e->getMessage());
        }

        $settlementSalary = (float)($settlement['basic_salary'] ?? 0);
        if ($settlementSalary > 0) {
           $this->lastResolvedSalarySource = 'settlement_basic_salary';
           return $settlementSalary;
        }

        return 0.0;
    }

    private function getLastResolvedSalarySource(): string
    {
        return $this->lastResolvedSalarySource ?? 'none';
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
                es.payroll_final_amount,
                es.payroll_clearance_status,
                es.settlement_date
            FROM exit_employee_settlements es
            LEFT JOIN exit_resignations er ON es.resignation_id = er.id
            JOIN employees e ON es.employee_id = e.employee_id
            WHERE es.payroll_clearance_status = 'pending'
            ORDER BY es.settlement_date DESC, es.id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createPayrollClearanceRequest(int $settlementId, int $requestedBy): ?int
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO payroll_clearances
                (settlement_id, requested_by, requested_at, status)
                VALUES (:settlement_id, :requested_by, NOW(), 'pending')
            ");
            $stmt->execute([
                ':settlement_id' => $settlementId,
                ':requested_by' => $requestedBy
            ]);

            $requestId = (int)$this->db->lastInsertId();

            // Ensure the exit_employee_settlements row is updated so Exit Management UI reflects the request
            $stmtUpdate = $this->db->prepare(
                "UPDATE exit_employee_settlements SET payroll_clearance_id = :pid, payroll_clearance_status = 'pending' WHERE id = :sid"
            );
            $stmtUpdate->execute([':pid' => $requestId, ':sid' => $settlementId]);

            $this->db->commit();

            return $requestId;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Failed to create payroll clearance request: ' . $e->getMessage());
            return null;
        }
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

    public function updatePayrollClearanceStatus(int $clearanceId, string $status, int $approvedBy, ?string $comments = null, ?float $finalAmount = null): bool
    {
        try {
            $this->db->beginTransaction();

            $clearanceStmt = $this->db->prepare("SELECT settlement_id FROM payroll_clearances WHERE id = :id LIMIT 1");
            $clearanceStmt->execute([':id' => $clearanceId]);
            $clearance = $clearanceStmt->fetch(PDO::FETCH_ASSOC);

            if (!$clearance) {
                $this->db->rollBack();
                return false;
            }

            $settlementId = (int)$clearance['settlement_id'];

            $stmt = $this->db->prepare("
                UPDATE payroll_clearances
                SET status = :status,
                    approved_by = :approved_by,
                    approved_at = NOW(),
                    comments = :comments,
                    last_updated = NOW()
                WHERE id = :id
            ");
            $stmt->execute([
                ':status' => $status,
                ':approved_by' => $approvedBy,
                ':comments' => $comments,
                ':id' => $clearanceId
            ]);

            $settlementStatus = $status === 'approved' ? 'approved' : 'rejected';

            if ($status === 'approved') {
                $settlementStmt = $this->db->prepare(" 
                    UPDATE exit_employee_settlements
                    SET payroll_clearance_status = :status,
                        payroll_clearance_id = :clearance_id,
                        payroll_notes = :comments,
                        payroll_final_amount = COALESCE(:final_amount, payroll_final_amount),
                        net_payable = COALESCE(:final_amount, net_payable),
                        status = :settlement_status,
                        approved_by = :approved_by,
                        approved_at = NOW(),
                        updated_at = NOW()
                    WHERE id = :settlement_id
                ");
                $settlementStmt->execute([
                    ':status' => $status,
                    ':clearance_id' => $clearanceId,
                    ':comments' => $comments,
                    ':final_amount' => $finalAmount,
                    ':settlement_status' => $settlementStatus,
                    ':approved_by' => $approvedBy,
                    ':settlement_id' => $settlementId
                ]);
            } else {
                $settlementStmt = $this->db->prepare(" 
                    UPDATE exit_employee_settlements
                    SET payroll_clearance_status = :status,
                        payroll_clearance_id = :clearance_id,
                        payroll_notes = :comments,
                        status = :settlement_status,
                        updated_at = NOW()
                    WHERE id = :settlement_id
                ");
                $settlementStmt->execute([
                    ':status' => $status,
                    ':clearance_id' => $clearanceId,
                    ':comments' => $comments,
                    ':settlement_status' => $settlementStatus,
                    ':settlement_id' => $settlementId
                ]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Failed to update payroll clearance status: ' . $e->getMessage());
            return false;
        }
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
