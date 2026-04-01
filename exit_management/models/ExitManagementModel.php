<?php

require_once __DIR__ . '/../../auth/database.php';

class ExitManagementModel
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get database connection
     */
    public function getConnection(): PDO
    {
        return $this->db;
    }

    /**
     * Get all employees eligible for exit management
     * Returns employees list, with optional user link and fallback.
     */
    public function getEligibleEmployees(): array
    {
        // Get employees from employees table to support employee_id values used by exit_resignations
        $stmt = $this->db->query("
            SELECT
                e.employee_id AS id,
                e.full_name,
                COALESCE(u.username, e.employee_id) AS username,
                e.email,
                e.department,
                e.position,
                e.employment_status AS employee_status
            FROM employees e
            LEFT JOIN users u ON e.user_id = u.id
            ORDER BY e.full_name
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get employee salary components from payroll system
     */
    public function getEmployeeSalaryComponents(string $employeeId): array
    {
        try {
            // Try to get salary from payroll database first
            $payrollDb = $this->getPayrollConnection();
            if ($payrollDb) {
                // Get current salary structure for employee
                $stmt = $payrollDb->prepare("
                    SELECT
                        es.rate,
                        ss.basic_salary,
                        ss.name as salary_structure_name
                    FROM pr_employee_salary es
                    LEFT JOIN pr_salary_structures ss ON es.salary_structure_id = ss.id
                    WHERE es.employee_id = ?
                    ORDER BY es.effective_date DESC
                    LIMIT 1
                ");
                $stmt->execute([$employeeId]);
                $salaryData = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($salaryData) {
                    $basicSalary = $salaryData['rate'] ?: $salaryData['basic_salary'] ?: 0;

                    // Get employee allowances
                    $stmt = $payrollDb->prepare("
                        SELECT a.name, a.amount, a.type
                        FROM pr_employee_allowances ea
                        JOIN pr_allowances a ON ea.allowance_id = a.id
                        WHERE ea.employee_id = ?
                    ");
                    $stmt->execute([$employeeId]);
                    $employeeAllowances = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Calculate specific allowances
                    $hra = 0;
                    $conveyance = 0;
                    $lta = 0;
                    $medicalAllowance = 0;
                    $otherAllowances = 0;

                    foreach ($employeeAllowances as $allowance) {
                        $amount = (float)$allowance['amount'];
                        $name = strtolower($allowance['name']);

                        if (strpos($name, 'hra') !== false) {
                            $hra = $allowance['type'] === 'percentage' ? ($basicSalary * $amount / 100) : $amount;
                        } elseif (strpos($name, 'conveyance') !== false) {
                            $conveyance = $amount;
                        } elseif (strpos($name, 'lta') !== false || strpos($name, 'travel') !== false) {
                            $lta = $amount;
                        } elseif (strpos($name, 'medical') !== false) {
                            $medicalAllowance = $amount;
                        } else {
                            $otherAllowances += $amount;
                        }
                    }

                    // Get employee deductions for provident fund, gratuity calculations
                    $stmt = $payrollDb->prepare("
                        SELECT d.name, d.amount, d.type, d.is_statutory
                        FROM pr_employee_deductions ed
                        JOIN pr_deductions d ON ed.deduction_id = d.id
                        WHERE ed.employee_id = ?
                    ");
                    $stmt->execute([$employeeId]);
                    $employeeDeductions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $providentFund = 0;
                    $gratuity = 0;

                    foreach ($employeeDeductions as $deduction) {
                        $amount = (float)$deduction['amount'];
                        $name = strtolower($deduction['name']);

                        if (strpos($name, 'provident') !== false || strpos($name, 'pf') !== false) {
                            $providentFund = $deduction['type'] === 'percentage' ? ($basicSalary * $amount / 100) : $amount;
                        }
                    }

                    // Calculate gratuity (typically 4.81% of basic salary per year of service)
                    // For simplicity, we'll use a standard calculation
                    $gratuity = $basicSalary * 0.0481 * 5; // Assuming 5 years of service

                    return [
                        'success' => true,
                        'basic_salary' => (float)$basicSalary,
                        'hra' => (float)$hra,
                        'conveyance' => (float)$conveyance,
                        'lta' => (float)$lta,
                        'medical_allowance' => (float)$medicalAllowance,
                        'other_allowances' => (float)$otherAllowances,
                        'provident_fund' => (float)$providentFund,
                        'gratuity' => (float)$gratuity,
                        'notice_pay' => 0, // Will be calculated based on notice period
                        'outstanding_loans' => 0, // Would need loan system integration
                        'other_deductions' => 0, // Additional deductions
                        'source' => 'payroll_system'
                    ];
                }
            }
        } catch (Exception $e) {
            // If payroll database is not available, continue to fallback
        }

        // Fallback: Try to get from employees table if salary field exists
        try {
            $stmt = $this->db->prepare("
                SELECT salary FROM employees WHERE employee_id = ?
            ");
            $stmt->execute([$employeeId]);
            $employeeSalary = $stmt->fetchColumn();

            if ($employeeSalary) {
                return [
                    'success' => true,
                    'basic_salary' => (float)$employeeSalary,
                    'hra' => (float)($employeeSalary * 0.4), // 40% of basic
                    'conveyance' => 19200, // Standard conveyance allowance
                    'lta' => 30000, // Standard LTA
                    'medical_allowance' => 5000, // Standard medical allowance
                    'other_allowances' => 3000, // Other allowances
                    'provident_fund' => (float)($employeeSalary * 0.12), // 12% of basic
                    'gratuity' => (float)($employeeSalary * 0.0481 * 5), // Gratuity for 5 years
                    'notice_pay' => 0,
                    'outstanding_loans' => 0,
                    'other_deductions' => 0,
                    'source' => 'fallback'
                ];
            }
        } catch (Exception $e) {
            // Continue to default fallback
        }

        // Default fallback values
        return [
            'success' => true,
            'basic_salary' => 25000,
            'hra' => 10000,
            'conveyance' => 19200,
            'lta' => 30000,
            'medical_allowance' => 5000,
            'other_allowances' => 3000,
            'provident_fund' => 3000,
            'gratuity' => 12000,
            'notice_pay' => 0,
            'outstanding_loans' => 0,
            'other_deductions' => 0,
            'source' => 'default'
        ];
    }

    /**
     * Get connection to payroll database
     */
    private function getPayrollConnection(): ?PDO
    {
        try {
            return new PDO(
                "mysql:host=localhost;dbname=payroll;charset=utf8mb4",
                "root",
                "",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (Exception $e) {
            return null; // Payroll database not available
        }
    }

    /**
     * Get eligible interviewers (admins and managers)
     */
    public function getEligibleInterviewers(): array
    {
        $stmt = $this->db->query("
            SELECT
                u.id,
                u.full_name,
                u.username,
                u.role,
                'admin' AS type
            FROM users u
            WHERE u.role IN ('recruitment', 'payroll', 'time', 'compliance', 'workforce', 'learning', 'performance', 'engagement_relations', 'exit', 'clinic')
            ORDER BY u.full_name
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get employee details by ID
     */
    public function getEmployeeById(int $employeeId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT u.*, u.department, u.position
            FROM users u
            WHERE u.id = ?
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Update employee status
     */
    public function updateEmployeeStatus(int $employeeId, string $status): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET status = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$status, $employeeId]);
    }

    /**
     * Get employees who have submitted resignations
     */
    public function getEmployeesWithResignations(): array
    {
        $stmt = $this->db->query("
            SELECT DISTINCT
                e.employee_id AS id,
                e.full_name,
                COALESCE(u.username, e.employee_id) AS username,
                e.email,
                e.department,
                e.position,
                r.resignation_type,
                r.status as resignation_status,
                r.notice_date,
                r.last_working_date
            FROM employees e
            INNER JOIN exit_resignations r ON e.employee_id = r.employee_id
            LEFT JOIN users u ON e.user_id = u.id
            WHERE r.status IN ('pending', 'approved')
            ORDER BY e.full_name
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}