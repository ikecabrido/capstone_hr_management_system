<?php

require_once 'ExitManagementModel.php';

class DocumentationModel extends ExitManagementModel
{
    /**
     * Create a document record
     */
    public function createDocument(array $data): int
    {
        try {
            error_log("=== DocumentationModel::createDocument START ===");
            error_log("Input data: " . json_encode($data));
            
            $sql = "
                INSERT INTO exit_documents (employee_id, document_type, title, file_path,
                                          uploaded_by, status, created_at)
                VALUES (?, ?, ?, ?, ?, 'active', NOW())
            ";
            error_log("SQL: " . $sql);
            
            $stmt = $this->db->prepare($sql);
            
            $values = [
                $data['employee_id'],
                $data['document_type'],
                $data['title'],
                $data['file_path'],
                $data['uploaded_by']
            ];
            error_log("Bind values: " . json_encode($values));

            $result = $stmt->execute($values);
            
            error_log("Execute result: " . ($result ? 'TRUE' : 'FALSE'));
            
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("SQLSTATE: " . $errorInfo[0] . ", Driver: " . $errorInfo[1] . ", Message: " . $errorInfo[2]);
                return 0;
            }

            $lastId = $this->db->lastInsertId();
            error_log("lastInsertId: " . $lastId);
            
            // Verify it was inserted
            $verifyStmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM exit_documents WHERE id = ?");
            $verifyStmt->execute([$lastId]);
            $verify = $verifyStmt->fetch(PDO::FETCH_ASSOC);
            error_log("Verification - Document ID $lastId exists: " . $verify['cnt']);
            
            // Check status value
            $statusStmt = $this->db->prepare("SELECT id, status FROM exit_documents WHERE id = ?");
            $statusStmt->execute([$lastId]);
            $statusRow = $statusStmt->fetch(PDO::FETCH_ASSOC);
            error_log("Document status in DB: " . json_encode($statusRow));
            
            error_log("=== DocumentationModel::createDocument END (ID: $lastId) ===");
            
            return (int)$lastId;
        } catch (Exception $e) {
            error_log("DocumentationModel::createDocument EXCEPTION: " . $e->getMessage());
            error_log("Stack: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Update a document record
     */
    public function updateDocument(int $documentId, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE exit_documents
            SET employee_id = ?, document_type = ?, title = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['employee_id'],
            $data['document_type'],
            $data['title'],
            $documentId
        ]);
    }

    /**
     * Get documents by employee
     */
    public function getDocumentsByEmployee(int $employeeId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM exit_documents
            WHERE employee_id = ? AND status = 'active'
            ORDER BY created_at DESC
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get document by ID
     */
    public function getDocumentById(int $documentId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT d.*, u.full_name, u.id as user_id
            FROM exit_documents d
            JOIN users u ON d.employee_id = u.id
            WHERE d.id = ? AND d.status = 'active'
        ");
        $stmt->execute([$documentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Update document status
     */
    public function updateDocumentStatus(int $documentId, string $status): bool
    {
        $stmt = $this->db->prepare("
            UPDATE exit_documents
            SET status = ?
            WHERE id = ?
        ");
        return $stmt->execute([$status, $documentId]);
    }

    /**
     * Delete document (soft delete)
     */
    public function deleteDocument(int $documentId): bool
    {
        return $this->updateDocumentStatus($documentId, 'deleted');
    }

    /**
     * Get all documents
     */
    public function getAllDocuments(): array
    {
        error_log(">>> getAllDocuments() CALLED");
        try {
            // Query all documents - no filtering
            $query = "
                SELECT 
                    d.id,
                    d.employee_id,
                    d.document_type,
                    d.title,
                    d.file_path,
                    d.uploaded_by,
                    d.status,
                    d.created_at,
                    u.full_name as employee_name
                FROM exit_documents d
                LEFT JOIN users u ON d.employee_id = u.id
                ORDER BY d.created_at DESC
            ";
            
            $stmt = $this->db->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log(">>> getAllDocuments() RESULT: " . count($result) . " documents");
            error_log(">>> JSON: " . json_encode($result));
            
            return $result;
        } catch (Exception $e) {
            error_log(">>> getAllDocuments() ERROR: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if all required documents are uploaded
     */
    public function checkRequiredDocuments(int $employeeId): array
    {
        $requiredTypes = [
            'resignation_letter',
            'exit_interview_form',
            'settlement_letter',
            'experience_letter',
            'clearance_form'
        ];

        $uploadedDocs = $this->getDocumentsByEmployee($employeeId);
        $uploadedTypes = array_column($uploadedDocs, 'document_type');

        $missing = array_diff($requiredTypes, $uploadedTypes);
        $completed = array_intersect($requiredTypes, $uploadedTypes);

        return [
            'completed' => $completed,
            'missing' => $missing,
            'is_complete' => empty($missing)
        ];
    }

    /**
     * Generate clearance checklist
     */
    public function generateClearanceChecklist(int $employeeId): array
    {
        $checklist = [
            'hr_clearance' => [
                'title' => 'HR Clearance',
                'items' => [
                    'Employee file updated',
                    'Resignation accepted',
                    'Exit interview completed',
                    'Final settlement processed'
                ]
            ],
            'it_clearance' => [
                'title' => 'IT Clearance',
                'items' => [
                    'Email account deactivated',
                    'Computer/laptop returned',
                    'Access cards returned',
                    'Software licenses revoked'
                ]
            ],
            'finance_clearance' => [
                'title' => 'Finance Clearance',
                'items' => [
                    'Salary dues cleared',
                    'Advance amounts recovered',
                    'Loans settled',
                    'Provident fund processed'
                ]
            ],
            'department_clearance' => [
                'title' => 'Department Clearance',
                'items' => [
                    'Knowledge transfer completed',
                    'Projects handed over',
                    'Documentation updated',
                    'Access rights revoked'
                ]
            ]
        ];

        // Check existing clearance records
        $stmt = $this->db->prepare("
            SELECT * FROM clearance_checklist
            WHERE employee_id = ?
        ");
        $stmt->execute([$employeeId]);
        $existingRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mark completed items
        foreach ($existingRecords as $record) {
            if (isset($checklist[$record['department']])) {
                $checklist[$record['department']]['completed'] = true;
                $checklist[$record['department']]['completed_at'] = $record['completed_at'];
                $checklist[$record['department']]['completed_by'] = $record['completed_by'];
            }
        }

        return $checklist;
    }

    /**
     * Mark clearance as completed
     */
    public function completeClearance(int $employeeId, string $department, int $completedBy): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO clearance_checklist (employee_id, department, completed_by, completed_at)
            VALUES (?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE completed_at = NOW(), completed_by = ?
        ");
        return $stmt->execute([$employeeId, $department, $completedBy, $completedBy]);
    }

    /**
     * Get document types
     */
    public function getDocumentTypes(): array
    {
        return [
            'resignation_letter' => 'Resignation Letter',
            'clearance_form' => 'Clearance Form',
            'handover_document' => 'Handover Document',
            'certificate' => 'Experience Certificate',
            'other' => 'Other Documents'
        ];
    }
}