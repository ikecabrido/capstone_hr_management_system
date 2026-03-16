<?php

require_once 'ExitManagementModel.php';

class KnowledgeTransferModel extends ExitManagementModel
{
    /**
     * Create a knowledge transfer plan
     */
    public function createTransferPlan(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO knowledge_transfer_plans (employee_id, successor_id, start_date,
                                                end_date, status, created_by, created_at)
            VALUES (?, ?, ?, ?, 'active', ?, NOW())
        ");

        $stmt->execute([
            $data['employee_id'],
            $data['successor_id'],
            $data['start_date'],
            $data['end_date'],
            $data['created_by']
        ]);

        $planId = (int)$this->db->lastInsertId();

        // Insert transfer items if provided
        if (isset($data['items']) && is_array($data['items'])) {
            $this->addTransferItems($planId, $data['items']);
        }

        return $planId;
    }

    /**
     * Add items to a transfer plan
     */
    public function addTransferItems(int $planId, array $items): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO knowledge_transfer_items (plan_id, item_type, title,
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
            SELECT ktp.*, u.full_name, u.username as emp_id,
                   s.full_name as successor_first
            FROM knowledge_transfer_plans ktp
            JOIN users u ON ktp.employee_id = u.id
            LEFT JOIN users s ON ktp.successor_id = s.id
            WHERE ktp.id = ?
        ");
        $stmt->execute([$planId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get transfer plans by employee
     */
    public function getTransferPlansByEmployee(int $employeeId): array
    {
        $stmt = $this->db->prepare("
            SELECT ktp.*, s.full_name as successor_first
            FROM knowledge_transfer_plans ktp
            LEFT JOIN users s ON ktp.successor_id = s.id
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
            SELECT * FROM knowledge_transfer_items
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
            UPDATE knowledge_transfer_items
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
            UPDATE knowledge_transfer_plans
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
            SELECT ktp.*, u.full_name, u.username as emp_id,
                   s.full_name as successor_first
            FROM knowledge_transfer_plans ktp
            JOIN users u ON ktp.employee_id = u.id
            LEFT JOIN users s ON ktp.successor_id = s.id
            WHERE ktp.status = 'active'
            ORDER BY ktp.end_date ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}