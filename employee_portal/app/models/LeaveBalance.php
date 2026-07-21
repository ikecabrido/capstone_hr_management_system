<?php

/**
 * LeaveBalance Model for managing employee leave balances
 */

require_once __DIR__ . '/../config/Database.php';

class LeaveBalance
{
    private $conn;
    private $table = 'ta_leave_balances';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get leave balance for employee and leave type
     */
    public function getBalance($employee_id, $leave_type_id, $year = null)
    {
        $year = $year ?? date('Y');

        $query = "SELECT * FROM {$this->table}
                  WHERE employee_id = :employee_id
                  AND leave_type_id = :leave_type_id
                  AND year = :year
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':employee_id' => $employee_id,
            ':leave_type_id' => $leave_type_id,
            ':year' => $year
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all balances for an employee
     */
    public function getByEmployee($employee_id, $year = null)
    {
        $year = $year ?? date('Y');

        $query = "SELECT lb.*, lt.leave_type_name
                  FROM {$this->table} lb
                  JOIN ta_leave_types lt ON lb.leave_type_id = lt.leave_type_id
                  WHERE lb.employee_id = :employee_id
                  AND lb.year = :year
                  ORDER BY lt.leave_type_name";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':employee_id' => $employee_id,
            ':year' => $year
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create leave balance for employee
     */
    public function create($employee_id, $leave_type_id, $opening_balance, $year = null)
    {
        $year = $year ?? date('Y');

        $query = "INSERT INTO {$this->table}
                  (employee_id, leave_type_id, year, opening_balance, used_balance, remaining_balance)
                  VALUES (:employee_id, :leave_type_id, :year, :opening_balance, 0, :opening_balance)";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':employee_id' => $employee_id,
            ':leave_type_id' => $leave_type_id,
            ':year' => $year,
            ':opening_balance' => $opening_balance
        ]);
    }

    /**
     * Deduct leave balance (when leave is approved)
     */
    public function deductBalance($employee_id, $leave_type_id, $days_used, $year = null)
    {
        $year = $year ?? date('Y');

        $query = "UPDATE {$this->table}
                  SET used_balance = used_balance + :days_used,
                      remaining_balance = opening_balance - (used_balance + :days_used),
                      updated_at = NOW()
                  WHERE employee_id = :employee_id
                  AND leave_type_id = :leave_type_id
                  AND year = :year";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':days_used' => $days_used,
            ':employee_id' => $employee_id,
            ':leave_type_id' => $leave_type_id,
            ':year' => $year
        ]);
    }

    /**
     * Restore balance (when leave is rejected)
     */
    public function restoreBalance($employee_id, $leave_type_id, $days_used, $year = null)
    {
        $year = $year ?? date('Y');

        $query = "UPDATE {$this->table}
                  SET used_balance = used_balance - :days_used,
                      remaining_balance = opening_balance - (used_balance - :days_used),
                      updated_at = NOW()
                  WHERE employee_id = :employee_id
                  AND leave_type_id = :leave_type_id
                  AND year = :year";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':days_used' => $days_used,
            ':employee_id' => $employee_id,
            ':leave_type_id' => $leave_type_id,
            ':year' => $year
        ]);
    }

    /**
     * Get remaining balance
     */
    public function getRemainingBalance($employee_id, $leave_type_id, $year = null)
    {
        $balance = $this->getBalance($employee_id, $leave_type_id, $year);
        return $balance ? (float)$balance['remaining_balance'] : 0;
    }
}
