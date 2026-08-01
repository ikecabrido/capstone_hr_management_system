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
            SELECT d.*, e.full_name as employee_name, u.full_name as uploaded_by_name
            FROM exit_documents d
            LEFT JOIN employees e ON d.employee_id = e.employee_id
            LEFT JOIN users u ON d.uploaded_by = u.id
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
     * Get all documents with optional status filter and pagination
     */
    public function getAllDocuments(string $status = null, int $page = 1, int $limit = 10, string $search = ''): array
    {
        error_log(">>> getAllDocuments() CALLED status=" . $status . ", page=" . $page . ", limit=" . $limit);
        try {
            $offset = ($page - 1) * $limit;

            $sql = "
                SELECT
                    d.id,
                    d.employee_id,
                    d.document_type,
                    d.title,
                    d.file_path,
                    d.uploaded_by,
                    d.status,
                    d.created_at,
                    e.full_name as employee_name,
                    u.full_name as uploaded_by_name
                FROM exit_documents d
                LEFT JOIN employees e ON d.employee_id = e.employee_id
                LEFT JOIN users u ON d.uploaded_by = u.id
            ";

            $countSql = "
                SELECT COUNT(*) as total
                FROM exit_documents d
                LEFT JOIN employees e ON d.employee_id = e.employee_id
                LEFT JOIN users u ON d.uploaded_by = u.id
            ";

            $params = [];
            $whereClause = "";

            if ($status && $status !== 'all') {
                $whereClause = " WHERE d.status = :status";
                $params['status'] = $status;
            }

            // Add search condition if provided
            if (!empty($search)) {
                $searchCondition = $whereClause ? " AND" : " WHERE";
                $searchCondition .= " (e.full_name LIKE :search0 OR d.title LIKE :search1 OR d.document_type LIKE :search2)";
                $whereClause .= $searchCondition;
                $searchParam = "%$search%";
                $params['search0'] = $searchParam;
                $params['search1'] = $searchParam;
                $params['search2'] = $searchParam;
            }

            // Get total count
            $countStmt = $this->db->prepare($countSql . $whereClause);
            $countStmt->execute($params);
            $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Get paginated data
            $stmt = $this->db->prepare($sql . $whereClause . " ORDER BY d.created_at DESC LIMIT :limit OFFSET :offset");
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $result = [
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'total' => $total,
                'page' => $page,
                'limit' => $limit
            ];

            error_log(">>> getAllDocuments() RESULT: " . count($result['data']) . " documents, total: " . $total);
            return $result;
        } catch (Exception $e) {
            error_log(">>> getAllDocuments() ERROR: " . $e->getMessage());
            return [
                'data' => [],
                'total' => 0,
                'page' => $page,
                'limit' => $limit
            ];
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

    /**
     * Archive document
     */
    public function archiveDocument(int $documentId, string $archiveReason = 'Manual archive'): bool
    {
        // Get the full document data
        $stmt = $this->db->prepare("SELECT * FROM exit_documents WHERE id = ?");
        $stmt->execute([$documentId]);
        $document = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$document) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            // Insert into exit_archive
            $archiveStmt = $this->db->prepare("
                INSERT INTO exit_archive (
                    archive_type, original_id, employee_id, title, description, content,
                    status, original_created_by, archived_by, archive_reason, archive_data
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $title = "Document - " . ($document['title'] ?? 'Unknown Document');
            $description = "Archived document record";
            $content = json_encode($document);
            $archivedBy = $_SESSION['user']['id'] ?? 1;

            $archiveStmt->execute([
                'document',
                $documentId,
                $document['employee_id'],
                $title,
                $description,
                $content,
                $document['status'],
                $document['uploaded_by'],
                $archivedBy,
                $archiveReason,
                $content
            ]);

            // Delete from exit_documents
            $deleteStmt = $this->db->prepare("DELETE FROM exit_documents WHERE id = ?");
            $deleteStmt->execute([$documentId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Document archive error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Unarchive document
     */
    public function unarchiveDocument(int $documentId): bool
    {
        // Get archived data
        $stmt = $this->db->prepare("SELECT * FROM exit_archive WHERE archive_type = 'document' AND original_id = ?");
        $stmt->execute([$documentId]);
        $archive = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$archive) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            // Decode the archived data
            $documentData = json_decode($archive['archive_data'], true);
            if (!$documentData) {
                return false;
            }

            // Insert back into exit_documents
            $insertStmt = $this->db->prepare("
                INSERT INTO exit_documents (
                    id, employee_id, document_type, title, file_path, uploaded_by, status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $insertStmt->execute([
                $documentData['id'],
                $documentData['employee_id'],
                $documentData['document_type'],
                $documentData['title'],
                $documentData['file_path'],
                $documentData['uploaded_by'],
                $documentData['status'] ?? 'active',
                $documentData['created_at']
            ]);

            // Update archive record to mark as restored
            $updateStmt = $this->db->prepare("
                UPDATE exit_archive
                SET restored = 1, restored_by = ?, restored_at = NOW()
                WHERE id = ?
            ");
            $restoredBy = $_SESSION['user']['id'] ?? 1;
            $updateStmt->execute([$restoredBy, $archive['id']]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Document unarchive error: " . $e->getMessage());
            return false;
        }
    }
}