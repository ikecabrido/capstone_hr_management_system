<?php
/**
 * Incident Model
 * Handles incident-related database operations
 */

require_once __DIR__ . '/../../auth/database.php';

class Incident
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create a new incident
     */
    public function create(array $data): int
    {
        $sql = "INSERT INTO lc_incidents (
                    incident_id, reporter_id, reporter_employee_id, reporter_name, 
                    reporter_department, reporter_position, reporter_contact, reporter_role, reporter_type,
                    respondent_id, respondent_employee_id, respondent_name, 
                    respondent_department, respondent_position, respondent_relationship,
                    incident_type, type, severity, incident_date, incident_time, 
                    location, title, description, status, assigned_to, created_by
                ) VALUES (
                    :incident_id, :reporter_id, :reporter_employee_id, :reporter_name,
                    :reporter_department, :reporter_position, :reporter_contact, :reporter_role, :reporter_type,
                    :respondent_id, :respondent_employee_id, :respondent_name,
                    :respondent_department, :respondent_position, :respondent_relationship,
                    :incident_type, :type, :severity, :incident_date, :incident_time,
                    :location, :title, :description, :status, :assigned_to, :created_by
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':incident_id' => $data['incident_id'],
            ':reporter_id' => $data['reporter_id'] ?? null,
            ':reporter_employee_id' => $data['reporter_employee_no'] ?? null,
            ':reporter_name' => $data['reporter_name'] ?? null,
            ':reporter_department' => $data['reporter_category_id'] ?? null,
            ':reporter_position' => $data['reporter_position_id'] ?? null,
            ':reporter_contact' => $data['reporter_contact'] ?? null,
            ':reporter_role' => $data['reporter_role'] ?? 'reporter',
            ':reporter_type' => $data['reporter_type'] ?? 'employee',
            ':respondent_id' => $data['respondent_id'] ?? null,
            ':respondent_employee_id' => $data['respondent_employee_no'] ?? null,
            ':respondent_name' => $data['respondent_name'] ?? null,
            ':respondent_department' => $data['respondent_category_id'] ?? null,
            ':respondent_position' => $data['respondent_position_id'] ?? null,
            ':respondent_relationship' => $data['respondent_relationship'] ?? 'co_worker',
            ':incident_type' => $data['incident_type'] ?? null,
            ':type' => $data['type'] ?? 'other',
            ':severity' => $data['severity'] ?? 'medium',
            ':incident_date' => $data['incident_date'] ?? null,
            ':incident_time' => $data['incident_time'] ?? null,
            ':location' => $data['location'] ?? null,
            ':title' => $data['title'] ?? null,
            ':description' => $data['description'] ?? null,
            ':status' => $data['status'] ?? 'submitted',
            ':assigned_to' => $data['assigned_to'] ?? null,
            ':created_by' => $data['created_by'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Store evidence metadata after a file has been uploaded to disk.
     * Accepts FileUpload::upload() output or the array shape used by includes/upload_handler.php.
     *
     * @return int New evidence row id, or 0 if insert failed (e.g. table missing)
     */
    public function addEvidence(int $incidentId, array $file): int
    {
        $fileName = $file['file_name'] ?? '';
        $filePath = $file['file_path'] ?? '';
        if ($fileName === '' || $filePath === '') {
            return 0;
        }

        $sql = "INSERT INTO lc_incident_evidence (
                    incident_id, file_name, file_path, file_type, file_size, uploaded_by, description
                ) VALUES (
                    :incident_id, :file_name, :file_path, :file_type, :file_size, :uploaded_by, :description
                )";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':incident_id' => $incidentId,
                ':file_name' => $fileName,
                ':file_path' => $filePath,
                ':file_type' => $file['file_type'] ?? null,
                ':file_size' => isset($file['file_size']) ? (int) $file['file_size'] : null,
                ':uploaded_by' => isset($file['uploaded_by']) && $file['uploaded_by'] !== ''
                    ? (int) $file['uploaded_by']
                    : null,
                ':description' => $file['description'] ?? null,
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Incident::addEvidence: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Evidence rows for an incident (for API / detail views).
     *
     * @return list<array<string, mixed>>
     */
    public function getEvidenceByIncidentId(int $incidentId): array
    {
        try {
            $sql = "SELECT id, file_name, file_path, file_type, file_size, created_at
                    FROM lc_incident_evidence
                    WHERE incident_id = :incident_id
                    ORDER BY id ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':incident_id' => $incidentId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $out = [];
            foreach ($rows as $row) {
                $out[] = [
                    'id' => (int) $row['id'],
                    'file_name' => $row['file_name'],
                    'file_path' => $row['file_path'],
                    'file_type' => $row['file_type'],
                    'file_size' => (int) $row['file_size'],
                    'uploaded_at' => $row['created_at'],
                ];
            }
            return $out;
        } catch (PDOException $e) {
            error_log('Incident::getEvidenceByIncidentId: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get incident by ID
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT i.*,
                       reporter.full_name as reporter_first_name, '' as reporter_last_name,
                       reporter.employee_no as reporter_employee_no,
                       reporter.department as reporter_department,
                       reporter.position as reporter_position,
                       respondent.full_name as respondent_first_name, '' as respondent_last_name,
                       respondent.employee_no as respondent_employee_no,
                       respondent.department as respondent_department,
                       respondent.position as respondent_position,
                       assigned.full_name as assigned_first_name, '' as assigned_last_name,
                       creator.full_name as creator_first_name, '' as creator_last_name
                FROM lc_incidents i
                LEFT JOIN employees reporter ON i.reporter_id = reporter.employee_id
                LEFT JOIN employees respondent ON i.respondent_id = respondent.employee_id
                LEFT JOIN employees assigned ON i.assigned_to = assigned.employee_id
                LEFT JOIN employees creator ON i.created_by = creator.employee_id
                WHERE i.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get incident by ID (alias for getById)
     */
    public function getIncidentById(int $id): ?array
    {
        return $this->getById($id);
    }

    /**
     * Get incident by incident ID
     */
    public function getByIncidentId(string $incidentId): ?array
    {
        $sql = "SELECT i.*,
                       reporter.full_name as reporter_first_name, '' as reporter_last_name,
                       reporter.employee_no as reporter_employee_no,
                       reporter.department as reporter_department,
                       reporter.position as reporter_position,
                       respondent.full_name as respondent_first_name, '' as respondent_last_name,
                       respondent.employee_no as respondent_employee_no,
                       respondent.department as respondent_department,
                       respondent.position as respondent_position,
                       assigned.full_name as assigned_first_name, '' as assigned_last_name,
                       creator.full_name as creator_first_name, '' as creator_last_name
                FROM lc_incidents i
                LEFT JOIN employees reporter ON i.reporter_id = reporter.employee_id
                LEFT JOIN employees respondent ON i.respondent_id = respondent.employee_id
                LEFT JOIN employees assigned ON i.assigned_to = assigned.employee_id
                LEFT JOIN employees creator ON i.created_by = creator.employee_id
                WHERE i.incident_id = :incident_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':incident_id' => $incidentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get all incidents with filters
     */
    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "i.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['severity'])) {
            $where[] = "i.severity = :severity";
            $params[':severity'] = $filters['severity'];
        }

        if (!empty($filters['type'])) {
            $where[] = "i.type = :type";
            $params[':type'] = $filters['type'];
        }

        if (!empty($filters['incident_type'])) {
            $where[] = "i.incident_type = :incident_type";
            $params[':incident_type'] = $filters['incident_type'];
        }

        if (!empty($filters['date_from'])) {
            $where[] = "i.incident_date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where[] = "i.incident_date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(i.title LIKE :search OR i.description LIKE :search OR i.incident_id LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['assigned_to'])) {
            $where[] = "i.assigned_to = :assigned_to";
            $params[':assigned_to'] = $filters['assigned_to'];
        }

        if (!empty($filters['reporter_id'])) {
            $where[] = "i.reporter_id = :reporter_id";
            $params[':reporter_id'] = $filters['reporter_id'];
        }

        if (!empty($filters['respondent_id'])) {
            $where[] = "i.respondent_id = :respondent_id";
            $params[':respondent_id'] = $filters['respondent_id'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT i.*,
                       reporter.full_name as reporter_first_name, '' as reporter_last_name,
                       reporter.employee_no as reporter_employee_no,
                       reporter.department as reporter_department,
                       reporter.position as reporter_position,
                       respondent.full_name as respondent_first_name, '' as respondent_last_name,
                       respondent.employee_no as respondent_employee_no,
                       respondent.department as respondent_department,
                       respondent.position as respondent_position,
                       assigned.full_name as assigned_first_name, '' as assigned_last_name,
                       creator.full_name as creator_first_name, '' as creator_last_name
                FROM lc_incidents i
                LEFT JOIN employees reporter ON i.reporter_id = reporter.employee_id
                LEFT JOIN employees respondent ON i.respondent_id = respondent.employee_id
                LEFT JOIN employees assigned ON i.assigned_to = assigned.employee_id
                LEFT JOIN employees creator ON i.created_by = creator.employee_id
                {$whereClause}
                ORDER BY i.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count incidents with filters
     */
    public function count(array $filters = []): int
    {
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['severity'])) {
            $where[] = "severity = :severity";
            $params[':severity'] = $filters['severity'];
        }

        if (!empty($filters['type'])) {
            $where[] = "type = :type";
            $params[':type'] = $filters['type'];
        }

        if (!empty($filters['incident_type'])) {
            $where[] = "incident_type = :incident_type";
            $params[':incident_type'] = $filters['incident_type'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(title LIKE :search OR description LIKE :search OR incident_id LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT COUNT(*) as count FROM lc_incidents {$whereClause}";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $result['count'];
    }

    /**
     * Update incident
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        $allowedFields = [
            'incident_type', 'type', 'severity', 'incident_date', 'incident_time',
            'location', 'title', 'description', 'status', 'assigned_to',
            'respondent_id', 'respondent_employee_id', 'respondent_name',
            'respondent_department', 'respondent_position', 'respondent_relationship',
            'resolved_at'
        ];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "{$field} = :{$field}";
                $params[":{$field}"] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE lc_incidents SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete incident
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM lc_incidents WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get incidents by reporter
     */
    public function getByReporter(int $reporterId): array
    {
        $sql = "SELECT i.*,
                       respondent.full_name as respondent_first_name, '' as respondent_last_name,
                       assigned.full_name as assigned_first_name, '' as assigned_last_name
                FROM lc_incidents i
                LEFT JOIN employees respondent ON i.respondent_id = respondent.employee_id
                LEFT JOIN employees assigned ON i.assigned_to = assigned.employee_id
                WHERE i.reporter_id = :reporter_id
                ORDER BY i.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':reporter_id' => $reporterId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get incidents by respondent
     */
    public function getByRespondent(int $respondentId): array
    {
        $sql = "SELECT i.*,
                       reporter.full_name as reporter_first_name, '' as reporter_last_name,
                       assigned.full_name as assigned_first_name, '' as assigned_last_name
                FROM lc_incidents i
                LEFT JOIN employees reporter ON i.reporter_id = reporter.employee_id
                LEFT JOIN employees assigned ON i.assigned_to = assigned.employee_id
                WHERE i.respondent_id = :respondent_id
                ORDER BY i.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':respondent_id' => $respondentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get incident history for an employee
     */
    public function getIncidentHistory(int $employeeId): array
    {
        $sql = "SELECT i.*, 
                    COUNT(DISTINCT da.id) as disciplinary_count
                FROM lc_incidents i
                LEFT JOIN lc_disciplinary_actions da ON i.id = da.incident_id
                WHERE i.respondent_id = :employee_id
                GROUP BY i.id
                ORDER BY i.incident_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':employee_id' => $employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get incidents by assigned HR
     */
    public function getByAssigned(int $assignedTo): array
    {
        $sql = "SELECT i.*,
                       reporter.full_name as reporter_first_name, '' as reporter_last_name,
                       respondent.full_name as respondent_first_name, '' as respondent_last_name
                FROM lc_incidents i
                LEFT JOIN employees reporter ON i.reporter_id = reporter.employee_id
                LEFT JOIN employees respondent ON i.respondent_id = respondent.employee_id
                WHERE i.assigned_to = :assigned_to
                ORDER BY i.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':assigned_to' => $assignedTo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) as submitted,
                    SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) as under_review,
                    SUM(CASE WHEN status = 'investigation' THEN 1 ELSE 0 END) as investigation,
                    SUM(CASE WHEN status = 'escalated' THEN 1 ELSE 0 END) as escalated,
                    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved,
                    SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed,
                    SUM(CASE WHEN severity = 'low' THEN 1 ELSE 0 END) as low_severity,
                    SUM(CASE WHEN severity = 'medium' THEN 1 ELSE 0 END) as medium_severity,
                    SUM(CASE WHEN severity = 'high' THEN 1 ELSE 0 END) as high_severity,
                    SUM(CASE WHEN severity = 'critical' THEN 1 ELSE 0 END) as critical_severity
                FROM lc_incidents";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Generate unique incident ID
     */
    public function generateIncidentId(): string
    {
        $year = date('Y');
        $prefix = "INC-{$year}-";
        
        $sql = "SELECT MAX(CAST(SUBSTRING(incident_id, :prefix_length) AS UNSIGNED)) as max_id 
                FROM lc_incidents 
                WHERE incident_id LIKE :prefix";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':prefix_length' => strlen($prefix) + 1,
            ':prefix' => $prefix . '%'
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $nextId = ($result['max_id'] ?? 0) + 1;
        
        return $prefix . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }
}
