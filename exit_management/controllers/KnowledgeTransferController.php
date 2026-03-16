<?php

require_once __DIR__ . '/../models/KnowledgeTransferModel.php';

class KnowledgeTransferController extends ExitManagementController
{
    private KnowledgeTransferModel $transferModel;

    public function __construct()
    {
        parent::__construct();
        $this->transferModel = new KnowledgeTransferModel();
    }

    /**
     * Create knowledge transfer plan
     */
    public function createTransferPlan(array $data): array
    {
        try {
            // Validate required fields
            $required = ['employee_id', 'successor_id', 'start_date', 'end_date'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => "Field '$field' is required"];
                }
            }

            $planId = $this->transferModel->createTransferPlan($data);

            return [
                'success' => true,
                'message' => 'Knowledge transfer plan created successfully',
                'plan_id' => $planId
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Add items to transfer plan
     */
    public function addTransferItems(int $planId, array $items): array
    {
        try {
            $success = $this->transferModel->addTransferItems($planId, $items);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Transfer items added successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to add transfer items'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Update transfer item status
     */
    public function updateItemStatus(int $itemId, string $status, string $notes = null): array
    {
        try {
            $success = $this->transferModel->updateItemStatus($itemId, $status, $notes);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Item status updated successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to update item status'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get transfer plan details
     */
    public function getTransferPlan(int $planId): array
    {
        $plan = $this->transferModel->getTransferPlanById($planId);

        if (!$plan) {
            return ['error' => 'Transfer plan not found'];
        }

        // Get transfer items
        $items = $this->transferModel->getTransferItems($planId);
        $plan['items'] = $items;

        return $plan;
    }

    /**
     * Complete transfer plan
     */
    public function completeTransferPlan(int $planId): array
    {
        try {
            $success = $this->transferModel->completeTransferPlan($planId);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Transfer plan completed successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to complete transfer plan'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get active transfer plans
     */
    public function getActiveTransferPlans(): array
    {
        return $this->transferModel->getActiveTransferPlans();
    }

    /**
     * Get all transfer plans
     */
    public function getTransferPlans(): array
    {
        return $this->transferModel->getAllTransferPlans();
    }

    /**
     * Get transfer items for a plan
     */
    public function getTransferItems(int $planId): array
    {
        return $this->transferModel->getTransferItems($planId);
    }

    /**
     * Handle AJAX requests for knowledge transfer
     */
    public function handleAjaxRequest(string $action, array $data = []): array
    {
        switch ($action) {
            case 'create_transfer_plan':
                return $this->createTransferPlan($data);

            case 'add_transfer_items':
                return $this->addTransferItems(
                    $data['plan_id'] ?? 0,
                    $data['items'] ?? []
                );

            case 'update_item_status':
                return $this->updateItemStatus(
                    $data['item_id'] ?? 0,
                    $data['status'] ?? '',
                    $data['notes'] ?? null
                );

            case 'get_transfer_plan':
                return $this->getTransferPlan($data['plan_id'] ?? 0);

            case 'complete_transfer_plan':
                return $this->completeTransferPlan($data['plan_id'] ?? 0);

            case 'get_active_plans':
                return $this->getActiveTransferPlans();

            case 'get_transfer_plans':
                return $this->getTransferPlans();

            case 'get_transfer_items':
                return $this->getTransferItems($data['plan_id'] ?? 0);

            default:
                return parent::handleAjaxRequest($action, $data);
        }
    }
}