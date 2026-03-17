<?php

require_once __DIR__ . '/../models/DocumentationModel.php';

class DocumentationController extends ExitManagementController
{
    private DocumentationModel $documentationModel;

    public function __construct()
    {
        parent::__construct();
        $this->documentationModel = new DocumentationModel();
    }

    /**
     * Upload document
     */
    public function uploadDocument(array $data): array
    {
        try {
            // Validate required fields
            $required = ['employee_id', 'document_type', 'title', 'file_path'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => "Field '$field' is required"];
                }
            }

            // Set uploaded_by from session if available
            if (!isset($data['uploaded_by'])) {
                $data['uploaded_by'] = $_SESSION['user']['id'] ?? null;
            }

            $documentId = $this->documentationModel->createDocument($data);

            return [
                'success' => true,
                'message' => 'Document uploaded successfully',
                'document_id' => $documentId
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Update document
     */
    public function updateDocument(array $data): array
    {
        try {
            if (empty($data['document_id'])) {
                return ['success' => false, 'message' => 'Document ID is required'];
            }

            // Validate required fields
            $required = ['employee_id', 'document_type', 'title'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => "Field '$field' is required"];
                }
            }

            $success = $this->documentationModel->updateDocument($data['document_id'], $data);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Document updated successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to update document'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get employee documents
     */
    public function getEmployeeDocuments(int $employeeId): array
    {
        return $this->documentationModel->getDocumentsByEmployee($employeeId);
    }

    /**
     * Check required documents status
     */
    public function checkRequiredDocuments(int $employeeId): array
    {
        return $this->documentationModel->checkRequiredDocuments($employeeId);
    }

    /**
     * Generate clearance checklist
     */
    public function generateClearanceChecklist(int $employeeId): array
    {
        return $this->documentationModel->generateClearanceChecklist($employeeId);
    }

    /**
     * Complete clearance
     */
    public function completeClearance(int $employeeId, string $department, int $completedBy): array
    {
        try {
            $success = $this->documentationModel->completeClearance($employeeId, $department, $completedBy);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Clearance completed successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to complete clearance'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Delete document
     */
    public function deleteDocument(int $documentId): array
    {
        try {
            $success = $this->documentationModel->deleteDocument($documentId);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Document deleted successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to delete document'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get document types
     */
    public function getDocumentTypes(): array
    {
        return $this->documentationModel->getDocumentTypes();
    }

    /**
     * Get all documents
     */
    public function getDocuments(): array
    {
        return $this->documentationModel->getAllDocuments();
    }

    /**
     * Get single document by ID
     */
    public function getDocument(int $documentId): array
    {
        $document = $this->documentationModel->getDocumentById($documentId);
        if (!$document) {
            return ['error' => 'Document not found'];
        }
        return $document;
    }

    /**
     * View document (placeholder for file viewing)
     */
    public function viewDocument(int $documentId): array
    {
        // This would redirect to file view or return file URL
        return [
            'success' => true,
            'message' => 'Document view functionality not yet implemented'
        ];
    }

    /**
     * Download document (placeholder for file download)
     */
    public function downloadDocument(int $documentId): array
    {
        // This would force download the file
        return [
            'success' => true,
            'message' => 'Document download functionality not yet implemented'
        ];
    }

    /**
     * Handle AJAX requests for documentation
     */
    public function handleAjaxRequest(string $action, array $data = []): array
    {
        switch ($action) {
            case 'upload_document':
            case 'upload_documentation':
                return $this->uploadDocument($data);

            case 'update_document':
            case 'update_documentation':
                return $this->updateDocument($data);

            case 'get_employee_documents':
                return $this->getEmployeeDocuments($data['employee_id'] ?? 0);

            case 'check_required_documents':
                return $this->checkRequiredDocuments($data['employee_id'] ?? 0);

            case 'generate_clearance_checklist':
                return $this->generateClearanceChecklist($data['employee_id'] ?? 0);

            case 'complete_clearance':
                return $this->completeClearance(
                    $data['employee_id'] ?? 0,
                    $data['department'] ?? '',
                    $data['completed_by'] ?? 0
                );

            case 'delete_document':
                return $this->deleteDocument($data['document_id'] ?? 0);

            case 'get_document':
                return $this->getDocument($data['document_id'] ?? 0);

            case 'get_document_types':
                return $this->getDocumentTypes();

            case 'get_documents':
                return $this->getDocuments();

            case 'view_document':
                return $this->viewDocument($data['document_id'] ?? 0);

            case 'download_document':
                return $this->downloadDocument($data['document_id'] ?? 0);

            default:
                return parent::handleAjaxRequest($action, $data);
        }
    }
}