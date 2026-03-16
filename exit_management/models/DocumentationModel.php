<?php

require_once 'ExitManagementModel.php';

class DocumentationModel extends ExitManagementModel
{
    /**
     * Create a document record
     */
    public function createDocument(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO exit_documents (employee_id, document_type, title, file_path,
                                      uploaded_by, status, created_at)
            VALUES (?, ?, ?, ?, ?, 'active', NOW())
        ");

        $stmt->execute([
            $data['employee_id'],
            $data['document_type'],
            $data['title'],
            $data['file_path'],
            $data['uploaded_by']
        ]);

        return (int)$this->db->lastInsertId();
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
            SELECT d.*, u.full_name, u.username as emp_id
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
            SET status = ?, updated_at = NOW()
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
     * Get document types
     */
    public function getDocumentTypes(): array
    {
        return [
            'resignation_letter' => 'Resignation Letter',
            'exit_interview_form' => 'Exit Interview Form',
            'settlement_letter' => 'Settlement Letter',
            'experience_letter' => 'Experience Letter',
            'clearance_form' => 'Clearance Form',
            'handover_document' => 'Handover Document',
            'asset_return_form' => 'Asset Return Form',
            'nda_agreement' => 'NDA Agreement',
            'other' => 'Other'
        ];
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
}