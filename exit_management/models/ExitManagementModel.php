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
     * Returns users for exit_documents and other tables that reference users.id
     */
    public function getEligibleEmployees(): array
    {
        // Get employees from employees table for exit management
        $stmt = $this->db->query("
            SELECT
                e.employee_id as id,
                e.employee_id as username,
                e.full_name,
                e.email,
                e.position,
                e.department,
                e.employment_status as employee_status
            FROM employees e
            WHERE e.employment_status = 'Active' OR e.employment_status = 'active'
            ORDER BY e.full_name
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
}