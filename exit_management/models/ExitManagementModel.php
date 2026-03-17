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
     */
    public function getEligibleEmployees(): array
    {
        // Prefer the employees table for exit management so values match foreign keys
        // used by resignations, interviews, settlements, documents, surveys, etc.
        $stmt = $this->db->query("
            SELECT
                e.employee_id AS id,
                e.employee_id AS username,
                e.full_name,
                e.email,
                e.position,
                e.department,
                'active' AS status
            FROM employees e
            -- use the employment_status column present in the employees table
            WHERE e.employment_status = 'Active'
            ORDER BY e.full_name
        ");

        // Return array shaped like the previous users-based result so the frontend
        // `loadEmployees()` and other callers continue to work without change.
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