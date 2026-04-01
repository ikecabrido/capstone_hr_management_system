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
            error_log("=== DOCUMENT UPLOAD START ===");
            error_log("POST data: " . json_encode($data));
            error_log("FILES data: " . json_encode(array_keys($_FILES)));
            
            // Validate required fields
            $required = ['employee_id', 'document_type', 'title'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    error_log("Document validation failed: $field is required. Data: " . json_encode($data));
                    return ['success' => false, 'message' => "Field '$field' is required"];
                }
            }

            // Handle file upload if present
            $filePath = $data['file_path'] ?? null;
            
            if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] === UPLOAD_ERR_OK) {
                error_log("File upload detected: " . $_FILES['document_file']['name']);
                
                // Create documents directory if it doesn't exist
                $uploadDir = __DIR__ . '/../uploads/documents/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                    error_log("Created upload directory: $uploadDir");
                }

                $fileName = basename($_FILES['document_file']['name']);
                $filePath = 'uploads/documents/' . time() . '_' . $fileName;
                $fullPath = __DIR__ . '/../' . $filePath;

                error_log("Moving file to: $fullPath");
                
                // Move uploaded file
                if (!move_uploaded_file($_FILES['document_file']['tmp_name'], $fullPath)) {
                    error_log("Failed to move uploaded file from " . $_FILES['document_file']['tmp_name'] . " to: $fullPath");
                    return ['success' => false, 'message' => 'Failed to save uploaded file'];
                }
                error_log("File uploaded successfully to: $fullPath");
            } elseif (isset($_FILES['document_file'])) {
                error_log("File upload error code: " . $_FILES['document_file']['error']);
                return ['success' => false, 'message' => 'File upload error: ' . $_FILES['document_file']['error']];
            }

            // If no file was uploaded and no file_path provided, it's an error
            if (empty($filePath)) {
                error_log("No file provided - filePath is empty");
                return ['success' => false, 'message' => 'No file provided'];
            }

            $data['file_path'] = $filePath;

            // Set uploaded_by from session if available
            if (!isset($data['uploaded_by'])) {
                $data['uploaded_by'] = $_SESSION['user']['id'] ?? null;
            }

            error_log("About to insert document with data: " . json_encode($data));
            error_log("Will insert status='active' for this document");
            
            $documentId = $this->documentationModel->createDocument($data);
            
            if (!$documentId) {
                error_log("Document creation failed - returned 0 or false");
                return ['success' => false, 'message' => 'Failed to create document record'];
            }
            
            error_log("Document created with ID: $documentId, should be queryable now");

            return [
                'success' => true,
                'message' => 'Document uploaded successfully',
                'document_id' => $documentId
            ];
        } catch (Exception $e) {
            error_log("Document upload exception: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
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
     * Get all documents with optional status filter
     */
    public function getDocuments(string $status = null): array
    {
        return $this->documentationModel->getAllDocuments($status);
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
     * View document (return file path and document details)
     */
    public function viewDocument(int $documentId): array
    {
        try {
            $document = $this->documentationModel->getDocumentById($documentId);
            
            if (!$document) {
                return ['success' => false, 'message' => 'Document not found'];
            }

            // Return document details including file path for frontend to display
            return [
                'success' => true,
                'id' => $document['id'],
                'employee_id' => $document['employee_id'],
                'employee_name' => $document['employee_name'],
                'document_type' => $document['document_type'],
                'title' => $document['title'],
                'file_path' => $document['file_path'],
                'uploaded_by_name' => $document['uploaded_by_name'],
                'created_at' => $document['created_at'],
                'message' => 'Document retrieved successfully'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Download document (force file download)
     */
    public function downloadDocument(int $documentId): array
    {
        try {
            $document = $this->documentationModel->getDocumentById($documentId);
            
            if (!$document) {
                return ['success' => false, 'message' => 'Document not found'];
            }

            // Return file path for frontend to handle download
            // Frontend should use this file_path to download the file
            return [
                'success' => true,
                'file_path' => $document['file_path'],
                'title' => $document['title'],
                'message' => 'Document ready for download'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Handle AJAX requests for documentation
     */
    public function handleAjaxRequest(string $action, array $data = []): array
    {
        switch ($action) {
            case 'upload_document':
            case 'upload_documentation':
            case 'submit_document':
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
                return $this->getDocuments($data['status'] ?? null);

            case 'view_document':
                return $this->viewDocument($data['document_id'] ?? 0);

            case 'download_document':
                return $this->downloadDocument($data['document_id'] ?? 0);

            default:
                return parent::handleAjaxRequest($action, $data);
        }
    }
}