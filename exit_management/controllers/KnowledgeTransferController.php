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

            // Add created_by from session
            $data['created_by'] = $_SESSION['user']['id'] ?? 0;

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
     * Delete transfer plan
     */
    public function deleteTransferPlan(int $planId): array
    {
        try {
            $success = $this->transferModel->deleteTransferPlan($planId);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Knowledge transfer plan deleted successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to delete transfer plan'];
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
     * Get all transfer plans with optional status filter
     */
    public function getTransferPlans(string $status = null): array
    {
        return $this->transferModel->getAllTransferPlans($status);
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
            case 'submit_transfer_plan':
            case 'update_transfer_plan':
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

            case 'delete_transfer_plan':
                return $this->deleteTransferPlan($data['plan_id'] ?? 0);

            case 'get_active_plans':
                return $this->getActiveTransferPlans();

            case 'get_transfer_plans':
                return $this->transferModel->getAllTransferPlans(
                    $data['status'] ?? null,
                    $data['page'] ?? 1,
                    $data['limit'] ?? 10,
                    $data['search'] ?? ''
                );

            case 'get_transfer_items':
                return $this->getTransferItems($data['plan_id'] ?? 0);

            case 'archive_transfer_plan':
                return $this->archiveTransferPlan(
                    $data['plan_id'] ?? 0,
                    $data['reason'] ?? ''
                );

            case 'unarchive_transfer_plan':
                return $this->unarchiveTransferPlan($data['plan_id'] ?? 0);

            case 'archive_transfer_item':
                return $this->archiveTransferItem(
                    $data['item_id'] ?? 0,
                    $data['reason'] ?? ''
                );

            case 'unarchive_transfer_item':
                return $this->unarchiveTransferItem($data['item_id'] ?? 0);

            case 'get_transfer_item_details':
                return $this->getTransferItemDetails($data['item_id'] ?? 0);

            default:
                return parent::handleAjaxRequest($action, $data);
        }
    }

    /**
     * Archive a transfer plan
     */
    private function archiveTransferPlan(int $planId, string $reason): array
    {
        try {
            if (empty($planId) || empty($reason)) {
                return [
                    'success' => false,
                    'message' => 'Plan ID and archive reason are required'
                ];
            }

            $result = $this->transferModel->archiveTransferPlan($planId, $reason);

            return [
                'success' => $result,
                'message' => $result ? 'Transfer plan archived successfully' : 'Failed to archive transfer plan'
            ];
        } catch (Exception $e) {
            error_log("Error archiving transfer plan: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while archiving the transfer plan'
            ];
        }
    }

    /**
     * Unarchive a transfer plan
     */
    private function unarchiveTransferPlan(int $planId): array
    {
        try {
            if (empty($planId)) {
                return [
                    'success' => false,
                    'message' => 'Plan ID is required'
                ];
            }

            $result = $this->transferModel->unarchiveTransferPlan($planId);

            return [
                'success' => $result,
                'message' => $result ? 'Transfer plan unarchived successfully' : 'Failed to unarchive transfer plan'
            ];
        } catch (Exception $e) {
            error_log("Error unarchiving transfer plan: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while unarchiving the transfer plan'
            ];
        }
    }

    /**
     * Archive a transfer item
     */
    private function archiveTransferItem(int $itemId, string $reason): array
    {
        try {
            if (empty($itemId) || empty($reason)) {
                return [
                    'success' => false,
                    'message' => 'Item ID and archive reason are required'
                ];
            }

            $result = $this->transferModel->archiveTransferItem($itemId, $reason);

            return [
                'success' => $result,
                'message' => $result ? 'Transfer item archived successfully' : 'Failed to archive transfer item'
            ];
        } catch (Exception $e) {
            error_log("Error archiving transfer item: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while archiving the transfer item'
            ];
        }
    }

    /**
     * Unarchive a transfer item
     */
    private function unarchiveTransferItem(int $itemId): array
    {
        try {
            if (empty($itemId)) {
                return [
                    'success' => false,
                    'message' => 'Item ID is required'
                ];
            }

            $result = $this->transferModel->unarchiveTransferItem($itemId);

            return [
                'success' => $result,
                'message' => $result ? 'Transfer item unarchived successfully' : 'Failed to unarchive transfer item'
            ];
        } catch (Exception $e) {
            error_log("Error unarchiving transfer item: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while unarchiving the transfer item'
            ];
        }
    }

    /**
     * Get transfer item details for archiving
     */
    private function getTransferItemDetails(int $itemId): array
    {
        try {
            if (empty($itemId)) {
                return [
                    'success' => false,
                    'message' => 'Item ID is required'
                ];
            }

            $item = $this->transferModel->getTransferItem($itemId);

            if (!$item) {
                return [
                    'success' => false,
                    'message' => 'Transfer item not found'
                ];
            }

            // Get plan to find employee
            $plan = $this->transferModel->getTransferPlan($item['plan_id']);
            $employee = null;
            if ($plan) {
                $employee = $this->transferModel->getEmployeeById($plan['employee_id']);
            }

            return [
                'success' => true,
                'data' => [
                    'id' => $item['id'],
                    'employee_id' => $plan ? $plan['employee_id'] : 0,
                    'employee_name' => $employee ? $employee['first_name'] . ' ' . $employee['last_name'] : 'Unknown',
                    'type' => $item['type'],
                    'title' => $item['title'],
                    'status' => $item['status']
                ]
            ];
        } catch (Exception $e) {
            error_log("Error getting transfer item details: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while retrieving transfer item details'
            ];
        }
    }
}