<?php

require_once __DIR__ . '/../models/ResignationModel.php';

class ResignationController extends ExitManagementController
{
    private ResignationModel $resignationModel;

    public function __construct()
    {
        parent::__construct();
        $this->resignationModel = new ResignationModel();
    }

    /**
     * Submit resignation
     */
    public function submitResignation(array $data): array
    {
        try {
            // Validate required fields
            $required = ['employee_id', 'resignation_type', 'reason', 'notice_date', 'last_working_date'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => "Field '$field' is required"];
                }
            }

            // Add submitted_by from session
            $data['submitted_by'] = $_SESSION['user']['id'] ?? 0;

            $resignationId = $this->resignationModel->submitResignation($data);

            return [
                'success' => true,
                'message' => 'Resignation submitted successfully',
                'resignation_id' => $resignationId
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get resignation details
     */
    public function getResignation(int $resignationId): array
    {
        $resignation = $this->resignationModel->getResignationById($resignationId);

        if (!$resignation) {
            return ['error' => 'Resignation not found'];
        }

        return $resignation;
    }

    /**
     * Approve or reject resignation
     */
    public function processResignation(int $resignationId, string $action, int $approvedBy): array
    {
        try {
            $status = ($action === 'approve') ? 'approved' : 'rejected';
            $success = $this->resignationModel->updateResignationStatus($resignationId, $status, $approvedBy);

            if ($success) {
                return [
                    'success' => true,
                    'message' => "Resignation $status successfully"
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to update resignation status'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get pending resignations
     */
    public function getPendingResignations(): array
    {
        return $this->resignationModel->getResignations('pending');
    }

    /**
     * Get all resignations
     */
    public function getResignations(): array
    {
        return $this->resignationModel->getAllResignations();
    }

    /**
     * Delete resignation
     */
    public function deleteResignation(int $resignationId): array
    {
        try {
            $success = $this->resignationModel->deleteResignation($resignationId);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Resignation deleted successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to delete resignation'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Handle AJAX requests for resignations
     */
    public function handleAjaxRequest(string $action, array $data = []): array
    {
        switch ($action) {
            case 'submit_resignation':
            case 'update_resignation':
                return $this->submitResignation($data);

            case 'get_resignation':
                return $this->getResignation($data['resignation_id'] ?? 0);

            case 'process_resignation':
                return $this->processResignation(
                    $data['resignation_id'] ?? 0,
                    $data['action'] ?? '',
                    $data['approved_by'] ?? 0
                );

            case 'get_pending_resignations':
                return $this->getPendingResignations();

            case 'get_resignations':
                return $this->getResignations();

            case 'delete_resignation':
                return $this->deleteResignation($data['resignation_id'] ?? 0);

            default:
                return parent::handleAjaxRequest($action, $data);
        }
    }
}