<?php

require_once __DIR__ . '/../../auth/database.php';

class ExitManagementModel
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->ensureTableAutoIncrement('exit_employee_settlements');
        $this->ensureTableAutoIncrement('exit_archive');
    }

    /**
     * Ensure a table primary key uses AUTO_INCREMENT and repair any id=0 rows.
     */
    protected function ensureTableAutoIncrement(string $tableName, string $primaryKey = 'id'): void
    {
        try {
            $stmt = $this->db->prepare("SHOW COLUMNS FROM {$tableName} LIKE ?");
            $stmt->execute([$primaryKey]);
            $column = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$column) {
                return;
            }

            if (stripos($column['Extra'] ?? '', 'auto_increment') === false) {
                $rows = $this->db->query("SELECT {$primaryKey} FROM {$tableName} WHERE {$primaryKey} = 0")->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    $maxId = (int)$this->db->query("SELECT MAX({$primaryKey}) AS max_id FROM {$tableName}")->fetchColumn();
                    $nextId = max(1, $maxId + 1);

                    foreach ($rows as $row) {
                        $this->db->exec("UPDATE {$tableName} SET {$primaryKey} = {$nextId} WHERE {$primaryKey} = 0 LIMIT 1");
                        $nextId++;
                    }
                }

                $indexStmt = $this->db->prepare("SHOW INDEX FROM {$tableName} WHERE Column_name = ?");
                $indexStmt->execute([$primaryKey]);
                $indexInfo = $indexStmt->fetchAll(PDO::FETCH_ASSOC);

                $hasPrimaryKey = false;
                foreach ($indexInfo as $indexRow) {
                    if (($indexRow['Key_name'] ?? '') === 'PRIMARY') {
                        $hasPrimaryKey = true;
                        break;
                    }
                }

                if (!$hasPrimaryKey) {
                    $this->db->exec("ALTER TABLE {$tableName} ADD PRIMARY KEY ({$primaryKey})");
                }

                $this->db->exec("ALTER TABLE {$tableName} MODIFY {$primaryKey} int(11) NOT NULL AUTO_INCREMENT");
            }
        } catch (Exception $e) {
            // Ignore repair failures in the model init; table may not be fully available yet.
        }
    }

    /**
     * Get database connection
     */
    public function getConnection(): PDO
    {
        return $this->db;
    }

    /**
     * Check whether a table exists in the current database
     */
    protected function tableExists(string $tableName): bool
    {
        $stmt = $this->db->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$tableName]);
        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get approved exit cases for interviews
     */
    public function getApprovedExitCases(): array
    {
        $cases = [];

        if ($this->tableExists('exit_resignations')) {
            $stmt = $this->db->query("SELECT
                'resignation' AS exit_case_type,
                r.id AS exit_case_id,
                e.employee_id AS employee_id,
                e.full_name,
                COALESCE(u.username, e.employee_id) AS username,
                e.email,
                e.department,
                e.position,
                e.date_hired,
                e.employment_status,
                '' AS manager_name,
                r.reason AS exit_reason,
                r.notice_date,
                r.last_working_date,
                r.last_working_date AS exit_date,
                r.approved_by,
                approver.full_name AS approved_by_name,
                r.approved_at,
                r.resignation_type AS exit_subtype
            FROM exit_resignations r
            JOIN employees e ON r.employee_id = e.employee_id
            LEFT JOIN users u ON e.user_id = u.id
            LEFT JOIN users approver ON r.approved_by = approver.id
            WHERE r.status = 'approved'
            ORDER BY e.full_name");
            $cases = array_merge($cases, $stmt->fetchAll(PDO::FETCH_ASSOC));
        }

        if ($this->tableExists('exit_terminations')) {
            $stmt = $this->db->query("SELECT
                'termination' AS exit_case_type,
                t.id AS exit_case_id,
                e.employee_id AS employee_id,
                e.full_name,
                COALESCE(u.username, e.employee_id) AS username,
                e.email,
                e.department,
                e.position,
                e.date_hired,
                e.employment_status,
                '' AS manager_name,
                t.termination_reason AS exit_reason,
                t.effective_date,
                t.effective_date AS last_working_date,
                t.effective_date AS exit_date,
                t.approved_by,
                approver.full_name AS approved_by_name,
                t.approved_at,
                NULL AS exit_subtype
            FROM exit_terminations t
            JOIN employees e ON t.employee_id = e.employee_id
            LEFT JOIN users u ON e.user_id = u.id
            LEFT JOIN users approver ON t.approved_by = approver.id
            WHERE t.status = 'approved'
            ORDER BY e.full_name");
            $cases = array_merge($cases, $stmt->fetchAll(PDO::FETCH_ASSOC));
        }

        usort($cases, function ($a, $b) {
            return strcasecmp($a['full_name'] ?? '', $b['full_name'] ?? '');
        });

        return $cases;
    }

    /**
     * Get approved exit case details by type and ID
     */
    public function getExitCaseDetails(string $exitCaseType, int $exitCaseId): ?array
    {
        if ($exitCaseType === 'resignation') {
            $stmt = $this->db->prepare("SELECT
                'resignation' AS exit_case_type,
                r.id AS exit_case_id,
                e.employee_id AS employee_id,
                e.full_name,
                e.department,
                e.position,
                e.date_hired,
                e.employment_status,
                '' AS manager_name,
                r.reason AS exit_reason,
                r.notice_date,
                r.last_working_date,
                r.approved_by,
                approver.full_name AS approved_by_name,
                r.approved_at
            FROM exit_resignations r
            JOIN employees e ON r.employee_id = e.employee_id
            LEFT JOIN users u ON e.user_id = u.id
            LEFT JOIN users approver ON r.approved_by = approver.id
            WHERE r.id = ? AND r.status = 'approved'");
        } elseif ($exitCaseType === 'termination') {
            $stmt = $this->db->prepare("SELECT
                'termination' AS exit_case_type,
                t.id AS exit_case_id,
                e.employee_id AS employee_id,
                e.full_name,
                e.department,
                e.position,
                e.date_hired,
                e.employment_status,
                '' AS manager_name,
                t.termination_reason AS exit_reason,
                t.effective_date,
                t.effective_date AS last_working_date,
                t.approved_by,
                approver.full_name AS approved_by_name,
                t.approved_at
            FROM exit_terminations t
            JOIN employees e ON t.employee_id = e.employee_id
            LEFT JOIN users u ON e.user_id = u.id
            LEFT JOIN users approver ON t.approved_by = approver.id
            WHERE t.id = ? AND t.status = 'approved'");
        } else {
            return null;
        }

        $stmt->execute([$exitCaseId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get employee engagement-related records for an employee
     */
    public function getEngagementRecords(string $employeeId): array
    {
        $records = [
            'exit_surveys' => [],
            'grievances' => [],
            'feedback_history' => $this->getFeedbackHistoryByEmployee($employeeId)
        ];

        if ($this->tableExists('exit_survey_responses')) {
            $stmt = $this->db->prepare("SELECT
                sr.id AS response_id,
                sr.survey_id,
                sv.title AS survey_title,
                sr.responses,
                sr.submitted_at
            FROM exit_survey_responses sr
            LEFT JOIN exit_surveys sv ON sr.survey_id = sv.id
            WHERE sr.employee_id = ?
            ORDER BY sr.submitted_at DESC");
            $stmt->execute([$employeeId]);
            $records['exit_surveys'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($this->tableExists('grievances')) {
            $stmt = $this->db->prepare("SELECT
                g.id,
                g.subject,
                g.description,
                g.status,
                g.priority,
                g.created_at,
                g.updated_at,
                COALESCE(u.full_name, '') AS assigned_to_name
            FROM grievances g
            LEFT JOIN users u ON g.assigned_to = u.id
            WHERE g.employee_id = ?
            ORDER BY g.created_at DESC");
            $stmt->execute([$employeeId]);
            $records['grievances'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $records;
    }

    /**
     * Get exit interview feedback history for an employee
     */
    public function getFeedbackHistoryByEmployee(string $employeeId): array
    {
        if (!$this->tableExists('exit_interview_feedback')) {
            return [];
        }

        $stmt = $this->db->prepare("SELECT
                ei.id AS interview_id,
                ei.scheduled_date,
                ei.status,
                f.overall_satisfaction,
                f.submitted_at
            FROM exit_interviews ei
            JOIN exit_interview_feedback f ON ei.id = f.interview_id
            WHERE ei.employee_id = ?
            ORDER BY f.submitted_at DESC");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            ORDER BY e.created_at DESC
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
    public function getEmployeeById($employeeId): ?array
    {
        $stmt = $this->db->prepare("SELECT
                e.*, 
                u.username AS user_username,
                u.full_name AS user_full_name,
                NULL AS user_email,
                NULL AS user_status,
                NULL AS manager_id,
                '' AS manager_name
            FROM employees e
            LEFT JOIN users u ON e.user_id = u.id
            WHERE e.employee_id = ?
            LIMIT 1");
        $stmt->execute([$employeeId]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($employee) {
            return $employee;
        }

        if (is_numeric($employeeId)) {
            $stmt = $this->db->prepare("SELECT
                    u.*,
                    NULL AS manager_id,
                    '' AS manager_name
                FROM users u
                WHERE u.id = ?
                LIMIT 1");
            $stmt->execute([$employeeId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $user['employee_id'] = $user['id'];
                $user['full_name'] = $user['full_name'] ?? null;
                $user['department'] = $user['department'] ?? null;
                $user['position'] = $user['position'] ?? null;
                $user['date_hired'] = $user['date_hired'] ?? null;
                $user['employment_status'] = $user['status'] ?? null;
                return $user;
            }
        }

        return null;
    }

    /**
     * Get user details by ID
     */
    public function getUserById(int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
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