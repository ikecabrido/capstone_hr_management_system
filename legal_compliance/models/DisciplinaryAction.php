<?php
/**
 * DisciplinaryAction Model
 * Handles disciplinary action-related database operations
 */

require_once __DIR__ . '/../../auth/database.php';

class DisciplinaryAction
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create a new disciplinary action
     */
    public function create(array $data): int
    {
        $sql = "INSERT INTO lc_disciplinary_actions (
                    action_reference, incident_id, employee_id, action_type, reason,
                    violation_description, start_date, end_date, duration_days,
                    is_active, issued_by, document_path, status
                ) VALUES (
                    :action_reference, :incident_id, :employee_id, :action_type, :reason,
                    :violation_description, :start_date, :end_date, :duration_days,
                    :is_active, :issued_by, :document_path, :status
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':action_reference' => $data['action_reference'],
            ':incident_id' => $data['incident_id'],
            ':employee_id' => $data['employee_id'],
            ':action_type' => $data['action_type'],
            ':reason' => $data['reason'],
            ':violation_description' => $data['violation_description'] ?? null,
            ':start_date' => $data['start_date'] ?? null,
            ':end_date' => $data['end_date'] ?? null,
            ':duration_days' => $data['duration_days'] ?? null,
            ':is_active' => $data['is_active'] ?? 1,
            ':issued_by' => $data['issued_by'] ?? null,
            ':document_path' => $data['document_path'] ?? null,
            ':status' => $data['status'] ?? 'pending'
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Get disciplinary action by ID
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT da.*,
                       e.full_name as employee_first_name, '' as employee_last_name,
                       e.employee_no as employee_no,
                       issuer.full_name as issuer_first_name, '' as issuer_last_name,
                       i.incident_id as incident_reference, i.title as incident_title
                FROM lc_disciplinary_actions da
                LEFT JOIN employees e ON da.employee_id = e.employee_id
                LEFT JOIN employees issuer ON da.issued_by = issuer.employee_id
                LEFT JOIN lc_incidents i ON da.incident_id = i.id
                WHERE da.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get disciplinary action by action reference
     */
    public function getByActionReference(string $actionReference): ?array
    {
        $sql = "SELECT da.*,
                       e.full_name as employee_first_name, '' as employee_last_name,
                       e.employee_no as employee_no,
                       issuer.full_name as issuer_first_name, '' as issuer_last_name,
                       i.incident_id as incident_reference, i.title as incident_title
                FROM lc_disciplinary_actions da
                LEFT JOIN employees e ON da.employee_id = e.employee_id
                LEFT JOIN employees issuer ON da.issued_by = issuer.employee_id
                LEFT JOIN lc_incidents i ON da.incident_id = i.id
                WHERE da.action_reference = :action_reference";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':action_reference' => $actionReference]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get all disciplinary actions with filters
     */
    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "da.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['action_type'])) {
            $where[] = "da.action_type = :action_type";
            $params[':action_type'] = $filters['action_type'];
        }

        if (!empty($filters['incident_id'])) {
            $where[] = "da.incident_id = :incident_id";
            $params[':incident_id'] = $filters['incident_id'];
        }

        if (!empty($filters['employee_id'])) {
            $where[] = "da.employee_id = :employee_id";
            $params[':employee_id'] = $filters['employee_id'];
        }

        if (!empty($filters['issued_by'])) {
            $where[] = "da.issued_by = :issued_by";
            $params[':issued_by'] = $filters['issued_by'];
        }

        if (!empty($filters['date_from'])) {
            $where[] = "da.start_date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where[] = "da.start_date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(da.action_reference LIKE :search OR da.reason LIKE :search OR i.incident_id LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT da.*,
                       e.full_name as employee_first_name, '' as employee_last_name,
                       e.employee_no as employee_no,
                       issuer.full_name as issuer_first_name, '' as issuer_last_name,
                       i.incident_id as incident_reference, i.title as incident_title
                FROM lc_disciplinary_actions da
                LEFT JOIN employees e ON da.employee_id = e.employee_id
                LEFT JOIN employees issuer ON da.issued_by = issuer.employee_id
                LEFT JOIN lc_incidents i ON da.incident_id = i.id
                {$whereClause}
                ORDER BY da.created_at DESC
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
     * Count disciplinary actions with filters
     */
    public function count(array $filters = []): int
    {
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['action_type'])) {
            $where[] = "action_type = :action_type";
            $params[':action_type'] = $filters['action_type'];
        }

        if (!empty($filters['incident_id'])) {
            $where[] = "incident_id = :incident_id";
            $params[':incident_id'] = $filters['incident_id'];
        }

        if (!empty($filters['employee_id'])) {
            $where[] = "employee_id = :employee_id";
            $params[':employee_id'] = $filters['employee_id'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT COUNT(*) as count FROM lc_disciplinary_actions {$whereClause}";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $result['count'];
    }

    /**
     * Update disciplinary action
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        $allowedFields = [
            'action_type', 'reason', 'violation_description', 'start_date', 'end_date',
            'duration_days', 'is_active', 'document_path', 'status'
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

        $sql = "UPDATE lc_disciplinary_actions SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete disciplinary action
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM lc_disciplinary_actions WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get disciplinary actions by incident
     */
    public function getByIncident(int $incidentId): array
    {
        $sql = "SELECT da.*,
                       e.full_name as employee_first_name, '' as employee_last_name,
                       e.employee_no as employee_no,
                       issuer.full_name as issuer_first_name, '' as issuer_last_name
                FROM lc_disciplinary_actions da
                LEFT JOIN employees e ON da.employee_id = e.employee_id
                LEFT JOIN employees issuer ON da.issued_by = issuer.employee_id
                WHERE da.incident_id = :incident_id
                ORDER BY da.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':incident_id' => $incidentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get disciplinary actions by employee
     */
    public function getByEmployee(int $employeeId): array
    {
        $sql = "SELECT da.*,
                       issuer.full_name as issuer_first_name, '' as issuer_last_name,
                       i.incident_id as incident_reference, i.title as incident_title
                FROM lc_disciplinary_actions da
                LEFT JOIN employees issuer ON da.issued_by = issuer.employee_id
                LEFT JOIN lc_incidents i ON da.incident_id = i.id
                WHERE da.employee_id = :employee_id
                ORDER BY da.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':employee_id' => $employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'issued' THEN 1 ELSE 0 END) as issued,
                    SUM(CASE WHEN status = 'appealed' THEN 1 ELSE 0 END) as appealed,
                    SUM(CASE WHEN status = 'upheld' THEN 1 ELSE 0 END) as upheld,
                    SUM(CASE WHEN status = 'dismissed' THEN 1 ELSE 0 END) as dismissed,
                    SUM(CASE WHEN action_type = 'verbal_warning' THEN 1 ELSE 0 END) as verbal_warnings,
                    SUM(CASE WHEN action_type = 'written_warning' THEN 1 ELSE 0 END) as written_warnings,
                    SUM(CASE WHEN action_type = 'suspension' THEN 1 ELSE 0 END) as suspensions,
                    SUM(CASE WHEN action_type = 'termination' THEN 1 ELSE 0 END) as terminations,
                    SUM(CASE WHEN action_type = 'final_warning' THEN 1 ELSE 0 END) as final_warnings
                FROM lc_disciplinary_actions";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Generate unique action reference
     */
    public function generateActionReference(): string
    {
        $year = date('Y');
        $prefix = "DISC-{$year}-";
        
        $sql = "SELECT MAX(CAST(SUBSTRING(action_reference, :prefix_length) AS UNSIGNED)) as max_id 
                FROM lc_disciplinary_actions 
                WHERE action_reference LIKE :prefix";
        
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
