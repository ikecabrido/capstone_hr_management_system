<?php

require_once 'ExitManagementModel.php';

class ResignationModel extends ExitManagementModel
{
    /**
     * Submit a resignation request
     */
    public function submitResignation(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO resignations (employee_id, resignation_type, reason, notice_date,
                                    last_working_date, comments, submitted_by, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
        ");

        $stmt->execute([
            $data['employee_id'],
            $data['resignation_type'],
            $data['reason'],
            $data['notice_date'],
            $data['last_working_date'],
            $data['comments'] ?? null,
            $data['submitted_by']
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Get resignation by ID
     */
    public function getResignationById(int $resignationId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, u.full_name, u.username as emp_id,
                   u.email, u.department
            FROM resignations r
            JOIN users u ON r.employee_id = u.id
            WHERE r.id = ?
        ");
        $stmt->execute([$resignationId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get all resignations with optional status filter
     */
    public function getResignations(string $status = null): array
    {
        $sql = "
            SELECT r.*, u.full_name, u.username as emp_id,
                   u.email, u.department
            FROM resignations r
            JOIN users u ON r.employee_id = u.id
        ";

        if ($status) {
            $sql .= " WHERE r.status = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$status]);
        } else {
            $stmt = $this->db->query($sql);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update resignation status
     */
    public function updateResignationStatus(int $resignationId, string $status, string $approvedBy = null): bool
    {
        $stmt = $this->db->prepare("
            UPDATE resignations
            SET status = ?, approved_by = ?, approved_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$status, $approvedBy, $resignationId]);
    }

    /**
     * Get resignations by employee ID
     */
    public function getResignationsByEmployee(int $employeeId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM resignations
            WHERE employee_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all resignations
     */
    public function getAllResignations(): array
    {
        return $this->getResignations();
    }

    /**
     * Delete resignation
     */
    public function deleteResignation(int $resignationId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM resignations WHERE id = ?");
        return $stmt->execute([$resignationId]);
    }
}