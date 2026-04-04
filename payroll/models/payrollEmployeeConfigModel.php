<?php
class PayrollEmployeeConfigModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Get all employees with their payroll config
    public function getAllEmployeesWithConfig(): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                e.employee_id,
                e.full_name,
                e.position,
                e.department,
                COALESCE(pd.base_salary, 0) AS base_salary,
                COALESCE(pd.position_type, 'Admin') AS position_type,
                COALESCE(pb.has_sss, 1) AS has_sss,
                COALESCE(pb.has_philhealth, 1) AS has_philhealth,
                COALESCE(pb.has_pagibig, 1) AS has_pagibig
            FROM employees e
            LEFT JOIN pr_employee_details pd ON e.employee_id = pd.employee_id
            LEFT JOIN pr_employee_benefits pb ON e.employee_id = pb.employee_id
            WHERE e.employment_status = 'Active'
            ORDER BY e.full_name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get single employee config
    public function getEmployeeConfig(int $employeeId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                e.employee_id,
                e.full_name,
                e.position,
                e.department,
                pd.base_salary,
                pd.position_type,
                pb.has_sss,
                pb.has_philhealth,
                pb.has_pagibig
            FROM employees e
            LEFT JOIN pr_employee_details pd ON e.employee_id = pd.employee_id
            LEFT JOIN pr_employee_benefits pb ON e.employee_id = pb.employee_id
            WHERE e.employee_id = :eid
        ");
        $stmt->execute([':eid' => $employeeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update or insert employee details (Payroll Only - base salary and position)
    public function saveEmployeeDetails(
        int $employeeId,
        float $baseSalary,
        string $positionType
    ): bool {
        $stmt = $this->db->prepare("
            INSERT INTO pr_employee_details 
            (employee_id, base_salary, position_type)
            VALUES (:eid, :salary, :ptype)
            ON DUPLICATE KEY UPDATE
            base_salary = :salary,
            position_type = :ptype
        ");
        
        return $stmt->execute([
            ':eid' => $employeeId,
            ':salary' => $baseSalary,
            ':ptype' => $positionType
        ]);
    }

    // Update employee benefits
    public function saveEmployeeBenefits(
        int $employeeId,
        bool $hasSss,
        bool $hasPhilHealth,
        bool $hasPagIbig
    ): bool {
        $stmt = $this->db->prepare("
            INSERT INTO pr_employee_benefits 
            (employee_id, has_sss, has_philhealth, has_pagibig)
            VALUES (:eid, :sss, :ph, :pi)
            ON DUPLICATE KEY UPDATE
            has_sss = :sss,
            has_philhealth = :ph,
            has_pagibig = :pi
        ");
        
        return $stmt->execute([
            ':eid' => $employeeId,
            ':sss' => $hasSss ? 1 : 0,
            ':ph' => $hasPhilHealth ? 1 : 0,
            ':pi' => $hasPagIbig ? 1 : 0
        ]);
    }

    // Get deduction rates by position
    public function getPositionRates(string $positionType): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM pr_position_deduction_rates
            WHERE position_type = :ptype AND is_active = 1
        ");
        $stmt->execute([':ptype' => $positionType]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get all teacher qualification rates
    public function getTeacherQualificationRates(): array
    {
        $stmt = $this->db->query("
            SELECT * FROM pr_teacher_qualification_rates
            WHERE is_active = 1
            ORDER BY qualification ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all position types
    public function getPositionTypes(): array
    {
        return ['Admin', 'Teacher', 'Other'];
    }

    // Delete employee config
    public function deleteEmployeeConfig(string $employeeId): bool
    {
        $stmt1 = $this->db->prepare("DELETE FROM pr_employee_details WHERE employee_id = :eid");
        $stmt2 = $this->db->prepare("DELETE FROM pr_employee_benefits WHERE employee_id = :eid");
        
        $stmt1->execute([':eid' => $employeeId]);
        $stmt2->execute([':eid' => $employeeId]);
        
        return true;
    }

    // Get summary statistics
    public function getConfigurationSummary(): array
    {
        $adminStmt = $this->db->query("SELECT COUNT(*) as count FROM pr_employee_details WHERE position_type = 'Admin'");
        $teacherStmt = $this->db->query("SELECT COUNT(*) as count FROM pr_employee_details WHERE position_type = 'Teacher'");
        $sssStmt = $this->db->query("SELECT COUNT(*) as count FROM pr_employee_benefits WHERE has_sss = 1");
        $phStmt = $this->db->query("SELECT COUNT(*) as count FROM pr_employee_benefits WHERE has_philhealth = 1");
        $piStmt = $this->db->query("SELECT COUNT(*) as count FROM pr_employee_benefits WHERE has_pagibig = 1");
        
        return [
            'admin_count' => $adminStmt->fetchColumn(),
            'teacher_count' => $teacherStmt->fetchColumn(),
            'sss_count' => $sssStmt->fetchColumn(),
            'philhealth_count' => $phStmt->fetchColumn(),
            'pagibig_count' => $piStmt->fetchColumn()
        ];
    }

    /* ==============================
       EXIT MANAGEMENT INTEGRATION
    ============================== */

    /**
     * Check if employee has exit resignation record
     */
    public function getEmployeeExitStatus(int $employeeId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                er.id as resignation_id,
                er.status,
                er.last_working_date,
                er.resignation_type,
                es.id as settlement_id,
                es.status as settlement_status
            FROM exit_resignations er
            LEFT JOIN exit_employee_settlements es ON er.id = es.resignation_id
            WHERE er.employee_id = :eid
            ORDER BY er.created_at DESC
            LIMIT 1
        ");
        $stmt->execute([':eid' => $employeeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get all employees with exit status (for filtering UI)
     */
    public function getAllEmployeesWithExitStatus(): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                e.employee_id,
                e.full_name,
                e.position,
                e.department,
                COALESCE(pd.base_salary, 0) AS base_salary,
                COALESCE(pd.position_type, 'Admin') AS position_type,
                COALESCE(pb.has_sss, 1) AS has_sss,
                COALESCE(pb.has_philhealth, 1) AS has_philhealth,
                COALESCE(pb.has_pagibig, 1) AS has_pagibig,
                COALESCE(er.status, 'active') as exit_status,
                er.last_working_date,
                es.status as settlement_status
            FROM employees e
            LEFT JOIN pr_employee_details pd ON e.employee_id = pd.employee_id
            LEFT JOIN pr_employee_benefits pb ON e.employee_id = pb.employee_id
            LEFT JOIN exit_resignations er ON e.employee_id = er.employee_id
            LEFT JOIN exit_employee_settlements es ON er.id = es.resignation_id
            WHERE pd.employee_id IS NOT NULL
            ORDER BY e.full_name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get only active employees (excluding exited ones)
     */
    public function getActiveEmployeesOnly(): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                e.employee_id,
                e.full_name,
                e.position,
                e.department,
                COALESCE(pd.base_salary, 0) AS base_salary,
                COALESCE(pd.position_type, 'Admin') AS position_type,
                COALESCE(pb.has_sss, 1) AS has_sss,
                COALESCE(pb.has_philhealth, 1) AS has_philhealth,
                COALESCE(pb.has_pagibig, 1) AS has_pagibig
            FROM employees e
            LEFT JOIN pr_employee_details pd ON e.employee_id = pd.employee_id
            LEFT JOIN pr_employee_benefits pb ON e.employee_id = pb.employee_id
            WHERE e.employment_status = 'Active'
            AND NOT EXISTS (
                SELECT 1 FROM exit_resignations er
                WHERE er.employee_id = e.employee_id
                AND er.status IN ('approved', 'pending')
            )
            ORDER BY e.full_name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
