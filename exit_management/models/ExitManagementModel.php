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
     * Get all employees eligible for exit management
     */
    public function getEligibleEmployees(): array
    {
        $stmt = $this->db->query("
            SELECT u.id, u.username as employee_id, u.full_name, u.email, u.position,
                   u.department, u.status
            FROM users u
            WHERE u.role IN ('employee', 'manager', 'trainer') AND u.status = 'active'
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
}