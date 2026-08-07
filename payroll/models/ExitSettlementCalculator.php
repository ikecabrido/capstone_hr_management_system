<?php

class ExitSettlementCalculator
{
    private PDO $db;
    private string $lastResolvedSalarySource = 'none';

    private const POSITION_CONFIG = [
        'teacher' => [
            'positions' => ['Teacher', 'Assistant Teacher', 'Instructor', 'Professor', 'Associate Professor'],
            'pay_type' => 'unit_based',
            'has_overtime' => false,
            'overtime_multiplier' => null,
            'absence_deduction' => 1536
        ],
        'admin' => [
            'positions' => ['Principal', 'Vice Principal', 'Administrative Secretary', 'Admin', 'Registrar', 'Finance Officer', 'Academic Coordinator', 'HR Manager', 'Accountant', 'Administrative Officer', 'System Administrator'],
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
            'positions' => ['Librarian', 'Counselor', 'School Nurse', 'IT Support', 'Coordinator', 'Software Engineer', 'Junior Developer', 'HR Specialist', 'Financial Analyst', 'Staff Coordinator', 'Operations Manager'],
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

    public function calculate(int $employeeId, int $settlementId): array
    {
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

        $stmtEmployee = $this->db->prepare("
            SELECT position
            FROM employees
            WHERE employee_id = :eid
        ");
        $stmtEmployee->execute([':eid' => $employeeId]);
        $employee = $stmtEmployee->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            return [];
        }

        $position = $employee['position'];
        $baseSalaryMonthly = $this->resolveBaseSalary($employeeId, $settlement);
        $salarySource = $this->getLastResolvedSalarySource();

        $categoryKey = $this->mapPositionToCategory($position);
        $positionCategory = $this->getPositionCategory($categoryKey);

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

        if ($daysWorked <= 0 && !empty($lastWorkingDate)) {
            $date = DateTimeImmutable::createFromFormat('Y-m-d', $lastWorkingDate);
            if ($date) {
                $daysWorked = max(1, (int)$date->format('d'));
            }
        }

        if ($daysWorked <= 0) {
            $daysWorked = 1;
        }

        $stmtRates = $this->db->prepare("
            SELECT absence_deduction_amount, late_per_minute_rate
            FROM pr_position_deduction_rates
            WHERE position_type = :ptype AND is_active = 1
        ");
        $stmtRates->execute([':ptype' => $categoryKey]);
        $rates = $stmtRates->fetch(PDO::FETCH_ASSOC);

        $absenceDeductionAmount = (float)($rates['absence_deduction_amount'] ?? $positionCategory['absence_deduction'] ?? 1020);
        $latePerMinute = (float)($rates['late_per_minute_rate'] ?? 2.00);

        $semiMonthly = $baseSalaryMonthly / 2;
        $dailyRate = $semiMonthly / 15;
        $basicSalary = $dailyRate * $daysWorked;
        $salarybasis = "Pro-rata Salary ({$daysWorked} days × ₱" . number_format($dailyRate, 2) . "/day)";

        if ($baseSalaryMonthly <= 0) {
            $salarybasis = 'Pro-rata Salary (No salary source found)';
        }

        $earnings = [[
            'description' => $salarybasis,
            'amount' => $basicSalary
        ]];

        if ((float)($settlement['gratuity'] ?? 0) > 0) {
            $earnings[] = [
                'description' => 'Gratuity',
                'amount' => (float)$settlement['gratuity']
            ];
        }

        if ((float)($settlement['notice_pay'] ?? 0) > 0) {
            $earnings[] = [
                'description' => 'Notice Pay',
                'amount' => (float)$settlement['notice_pay']
            ];
        }

        $grossPay = $basicSalary + ((float)($settlement['gratuity'] ?? 0)) + ((float)($settlement['notice_pay'] ?? 0));

        $deductions = [];
        $totalDeductions = 0;

        $stmtContributions = $this->db->prepare("
            SELECT contribution_type, status
            FROM lc_employee_contributions
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

        if ($unapprovedAbsentDays > 0) {
            $absenceDeduction = $unapprovedAbsentDays * $absenceDeductionAmount;
            $deductions[] = [
                'description' => "Unexcused Absence ({$unapprovedAbsentDays} day(s) × ₱" . number_format($absenceDeductionAmount, 2) . ")",
                'amount' => $absenceDeduction
            ];
            $totalDeductions += $absenceDeduction;
        }

        if ($lateMinutes > 0) {
            $lateDeduction = $lateMinutes * $latePerMinute;
            $deductions[] = [
                'description' => "Late ({$lateMinutes} minutes × ₱{$latePerMinute}/min)",
                'amount' => $lateDeduction
            ];
            $totalDeductions += $lateDeduction;
        }

        if ((float)($settlement['outstanding_loans'] ?? 0) > 0) {
            $deductions[] = [
                'description' => 'Outstanding Loans',
                'amount' => (float)$settlement['outstanding_loans']
            ];
            $totalDeductions += (float)$settlement['outstanding_loans'];
        }

        if ((float)($settlement['other_deductions'] ?? 0) > 0) {
            $deductions[] = [
                'description' => 'Other Deductions',
                'amount' => (float)$settlement['other_deductions']
            ];
            $totalDeductions += (float)$settlement['other_deductions'];
        }

        $netPay = max(0, $grossPay - $totalDeductions);

        return [
            'gross_pay' => $grossPay,
            'net_pay' => $netPay,
            'total_deductions' => $totalDeductions,
            'earnings' => $earnings,
            'deductions' => $deductions,
            'is_exit_settlement' => true,
            'last_working_date' => $lastWorkingDate,
            'days_worked' => $daysWorked,
            'base_salary_monthly' => $baseSalaryMonthly,
            'salary_source' => $salarySource,
            'calculation_layer' => 'exit-settlement-calculator'
        ];
    }

    private function resolveBaseSalary(int $employeeId, array $settlement): float
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

    private function getTimeAttendanceMetrics(int $employeeId, string $startDate, string $endDate): ?array
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

    private function calculateContribution(string $contributionType, float $monthlySalary, bool $isSemiMonthly = true): float
    {
        $rate = $this->getContributionRate($contributionType, $monthlySalary);

        if (!$rate) {
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

        $monthlyContribution = $monthlySalary * ($ratePercent / 100);
        return $isSemiMonthly ? $monthlyContribution / 2 : $monthlyContribution;
    }

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

    private function mapPositionToCategory(string $position): string
    {
        $positionMap = [
            'Teacher' => 'teacher',
            'Assistant Teacher' => 'teacher',
            'Instructor' => 'teacher',
            'Professor' => 'teacher',
            'Associate Professor' => 'teacher',
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
            'Janitor' => 'support',
            'Maintenance Worker' => 'support',
            'Security Guard' => 'support',
            'Driver' => 'support',
            'Canteen Staff' => 'support',
            'Gardener' => 'support',
            'Groundskeeper' => 'support'
        ];

        return $positionMap[$position] ?? 'admin';
    }

    private function getPositionCategory(string $categoryKey): ?array
    {
        return self::POSITION_CONFIG[$categoryKey] ?? self::POSITION_CONFIG['admin'];
    }
}
