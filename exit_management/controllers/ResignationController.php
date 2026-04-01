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
            $required = ['employee_id', 'resignation_type', 'reason', 'notice_date', 'last_working_date', 'preclearance_desk_person'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => "Field '$field' is required"];
                }
            }

            // Check employee eligibility
            $eligibility = $this->resignationModel->checkEmployeeEligibility($data['employee_id']);
            if (!$eligibility['eligible']) {
                return ['success' => false, 'message' => $eligibility['reason']];
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
     * Get resignations by status
     */
    public function getResignations(string $status = null): array
    {
        return $this->resignationModel->getResignations($status);
    }

    /**
     * Get archived resignations
     */
    public function getArchivedResignations(): array
    {
        return $this->resignationModel->getResignations('archived');
    }

    /**
     * Unarchive resignation
     */
    public function unarchiveResignation(int $resignationId): array
    {
        try {
            $success = $this->resignationModel->unarchiveResignation($resignationId);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Resignation unarchived successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to unarchive resignation'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Archive resignation
     */
    public function archiveResignation(int $resignationId): array
    {
        try {
            $success = $this->resignationModel->archiveResignation($resignationId);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Resignation archived successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to archive resignation'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Check employee eligibility for resignation
     */
    public function checkEmployeeEligibility(string $employeeId): array
    {
        try {
            if (empty($employeeId)) {
                return ['success' => false, 'message' => 'Employee ID is required'];
            }

            $eligibility = $this->resignationModel->checkEmployeeEligibility($employeeId);

            return [
                'success' => $eligibility['eligible'],
                'message' => $eligibility['reason']
            ];
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
                $status = $data['status'] ?? null;
                if ($status === 'archived') {
                    return $this->getArchivedResignations();
                }
                if ($status === 'all') {
                    return $this->resignationModel->getResignations('all');
                }
                return $this->getResignations($status);

            case 'get_archived_resignations':
                return $this->getArchivedResignations();

            case 'archive_resignation':
                return $this->archiveResignation($data['resignation_id'] ?? 0);

            case 'unarchive_resignation':
                return $this->unarchiveResignation($data['resignation_id'] ?? 0);

            case 'check_eligibility':
                return $this->checkEmployeeEligibility($data['employee_id'] ?? '');

            default:
                return parent::handleAjaxRequest($action, $data);
        }
    }
}