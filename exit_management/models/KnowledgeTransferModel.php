<?php

require_once 'ExitManagementModel.php';

class KnowledgeTransferModel extends ExitManagementModel
{
    /**
     * Create a knowledge transfer plan
     */
    public function createTransferPlan(array $data): int
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO exit_knowledge_transfer_plans (employee_id, successor_id, start_date,
                                                    end_date, status, created_by, created_at)
                VALUES (?, ?, ?, ?, 'active', ?, NOW())
            ");

            $result = $stmt->execute([
                $data['employee_id'],
                $data['successor_id'],
                $data['start_date'],
                $data['end_date'],
                $data['created_by'] ?? 0
            ]);

            if (!$result) {
                throw new Exception('Failed to insert knowledge transfer plan');
            }

            $planId = (int)$this->db->lastInsertId();

            // Insert transfer items if provided
            if (isset($data['items']) && is_array($data['items'])) {
                $this->addTransferItems($planId, $data['items']);
            }

            return $planId;
        } catch (Exception $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }

    /**
     * Update a knowledge transfer plan
     */
    public function updateTransferPlan(int $planId, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE exit_knowledge_transfer_plans
            SET employee_id = ?, successor_id = ?, start_date = ?,
                end_date = ?, updated_at = NOW()
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['employee_id'],
            $data['successor_id'],
            $data['start_date'],
            $data['end_date'],
            $planId
        ]);
    }

    /**
     * Add items to a transfer plan
     */
    public function addTransferItems(int $planId, array $items): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO exit_knowledge_transfer_items (plan_id, item_type, title,
                                               description, priority, status, created_at)
            VALUES (?, ?, ?, ?, ?, 'pending', NOW())
        ");

        foreach ($items as $item) {
            $stmt->execute([
                $planId,
                $item['type'],
                $item['title'],
                $item['description'] ?? null,
                $item['priority'] ?? 'medium'
            ]);
        }

        return true;
    }

    /**
     * Get transfer plan by ID
     */
    public function getTransferPlanById(int $planId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                ktp.id,
                ktp.employee_id,
                ktp.successor_id,
                ktp.start_date,
                ktp.end_date,
                ktp.status,
                ktp.created_at,
                ktp.updated_at,
                e.full_name as employee_name,
                e.employee_id as emp_id,
                s.full_name as successor_name
            FROM exit_knowledge_transfer_plans ktp
            JOIN employees e ON ktp.employee_id = e.employee_id
            LEFT JOIN employees s ON ktp.successor_id = s.employee_id
            WHERE ktp.id = ?
        ");
        $stmt->execute([$planId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get transfer plans by employee
     */
    public function getTransferPlansByEmployee(string $employeeId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                ktp.id,
                ktp.employee_id,
                ktp.successor_id,
                ktp.start_date,
                ktp.end_date,
                ktp.status,
                ktp.created_at,
                ktp.updated_at,
                s.full_name as successor_name
            FROM exit_knowledge_transfer_plans ktp
            LEFT JOIN employees s ON ktp.successor_id = s.employee_id
            WHERE ktp.employee_id = ?
            ORDER BY ktp.created_at DESC
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get transfer items by plan ID
     */
    public function getTransferItems(int $planId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM exit_knowledge_transfer_items
            WHERE plan_id = ?
            ORDER BY priority DESC, created_at ASC
        ");
        $stmt->execute([$planId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update transfer item status
     */
    public function updateItemStatus(int $itemId, string $status, string $notes = null): bool
    {
        $stmt = $this->db->prepare("
            UPDATE exit_knowledge_transfer_items
            SET status = ?, completed_at = ?, notes = ?
            WHERE id = ?
        ");

        $completedAt = ($status === 'completed') ? date('Y-m-d H:i:s') : null;

        return $stmt->execute([$status, $completedAt, $notes, $itemId]);
    }

    /**
     * Complete transfer plan
     */
    public function completeTransferPlan(int $planId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE exit_knowledge_transfer_plans
            SET status = 'completed', completed_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$planId]);
    }

    /**
     * Get active transfer plans
     */
    public function getActiveTransferPlans(): array
    {
        $stmt = $this->db->query("
            SELECT 
                ktp.id,
                ktp.employee_id,
                ktp.successor_id,
                ktp.start_date,
                ktp.end_date,
                ktp.status,
                ktp.created_at,
                ktp.updated_at,
                e.full_name as employee_name,
                s.full_name as successor_name
            FROM exit_knowledge_transfer_plans ktp
            JOIN employees e ON ktp.employee_id = e.employee_id
            LEFT JOIN employees s ON ktp.successor_id = s.employee_id
            WHERE ktp.status = 'active'
            ORDER BY ktp.end_date ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all transfer plans with optional status filter
     */
    public function getAllTransferPlans(string $status = null): array
    {
        $sql = "
            SELECT 
                ktp.id,
                ktp.employee_id,
                ktp.successor_id,
                ktp.start_date,
                ktp.end_date,
                ktp.status,
                ktp.created_at,
                ktp.updated_at,
                e.full_name as employee_name,
                s.full_name as successor_name
            FROM exit_knowledge_transfer_plans ktp
            JOIN employees e ON ktp.employee_id = e.employee_id
            LEFT JOIN employees s ON ktp.successor_id = s.employee_id
        ";

        if ($status && $status !== 'all') {
            $sql .= " WHERE ktp.status = ?";
            $stmt = $this->db->prepare($sql . " ORDER BY ktp.created_at DESC");
            $stmt->execute([$status]);
        } else {
            $stmt = $this->db->query($sql . " ORDER BY ktp.created_at DESC");
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete a knowledge transfer plan and its associated items
     */
    public function deleteTransferPlan(int $planId): bool
    {
        try {
            // Delete associated transfer items first (cascade)
            $stmt = $this->db->prepare("
                DELETE FROM exit_knowledge_transfer_items
                WHERE plan_id = ?
            ");
            $stmt->execute([$planId]);

            // Delete the transfer plan
            $stmt = $this->db->prepare("
                DELETE FROM exit_knowledge_transfer_plans
                WHERE id = ?
            ");
            return $stmt->execute([$planId]);
        } catch (Exception $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }
}