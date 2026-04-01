<?php

require_once 'ExitManagementModel.php';

class ResignationModel extends ExitManagementModel
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureArchivedFromStatusColumn();
    }

    private function ensureArchivedFromStatusColumn(): void
    {
        $stmt = $this->db->prepare("SHOW COLUMNS FROM exit_resignations LIKE 'archived_from_status'");
        $stmt->execute();
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->db->exec("ALTER TABLE exit_resignations ADD COLUMN archived_from_status ENUM('pending','approved','rejected','withdrawn') DEFAULT NULL");
        }
    }

    /**
     * Submit a resignation request
     */
    public function submitResignation(array $data): int
    {
        try {
            // Validate preclearance desk person as valid user
            if (empty($data['preclearance_desk_person']) || !is_numeric($data['preclearance_desk_person'])) {
                throw new Exception('Invalid pre-clearance desk person');
            }

            $stmt = $this->db->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$data['preclearance_desk_person']]);
            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception('Selected pre-clearance desk person does not exist');
            }

            $stmt = $this->db->prepare("
                INSERT INTO exit_resignations (employee_id, resignation_type, reason, notice_date,
                                        last_working_date, comments, submitted_by, preclearance_desk_person, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");

            $result = $stmt->execute([
                $data['employee_id'],
                $data['resignation_type'],
                $data['reason'],
                $data['notice_date'],
                $data['last_working_date'],
                $data['comments'] ?? null,
                $data['submitted_by'] ?? 0,
                $data['preclearance_desk_person']
            ]);

            if (!$result) {
                throw new Exception('Failed to insert resignation');
            }

            return (int)$this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }

    /**
     * Get resignation by ID
     */
    public function getResignationById(int $resignationId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, e.full_name AS employee_name, e.employee_id AS emp_id,
                   e.email, e.department,
                   p.full_name AS preclearance_desk_person_name
            FROM exit_resignations r
            LEFT JOIN employees e ON r.employee_id = e.employee_id
            LEFT JOIN users p ON r.preclearance_desk_person = p.id
            WHERE r.id = ?
        ");
        $stmt->execute([$resignationId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get all resignations (active by default, archived optional)
     */
    public function getResignations(string $status = null): array
    {
        $sql = "
            SELECT 
                r.id,
                r.employee_id,
                r.resignation_type,
                r.reason,
                r.notice_date,
                r.last_working_date,
                r.comments,
                r.status,
                r.archived_from_status,
                r.created_at,
                r.updated_at,
                e.full_name as employee_name,
                e.email,
                e.department,
                p.full_name AS preclearance_desk_person_name
            FROM exit_resignations r
            LEFT JOIN employees e ON r.employee_id = e.employee_id
            LEFT JOIN users p ON r.preclearance_desk_person = p.id
        ";

        if ($status === 'archived') {
            $sql .= " WHERE r.status = 'archived'";
            $stmt = $this->db->query($sql);
        } elseif ($status === 'all') {
            $stmt = $this->db->query($sql);
        } elseif ($status) {
            $sql .= " WHERE r.status = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$status]);
        } else {
            $sql .= " WHERE r.status != 'archived'";
            $stmt = $this->db->query($sql);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update resignation
     */
    public function updateResignation(int $resignationId, array $data): bool
    {
        // Validate preclearance desk person as valid user
        if (empty($data['preclearance_desk_person']) || !is_numeric($data['preclearance_desk_person'])) {
            throw new Exception('Invalid pre-clearance desk person');
        }

        $stmt = $this->db->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$data['preclearance_desk_person']]);
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception('Selected pre-clearance desk person does not exist');
        }

        $stmt = $this->db->prepare("
            UPDATE exit_resignations
            SET employee_id = ?, resignation_type = ?, reason = ?, notice_date = ?,
                last_working_date = ?, comments = ?, preclearance_desk_person = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['employee_id'],
            $data['resignation_type'],
            $data['reason'],
            $data['notice_date'],
            $data['last_working_date'],
            $data['comments'] ?? null,
            $data['preclearance_desk_person'],
            $resignationId
        ]);
    }

    /**
     * Update resignation status
     */
    public function updateResignationStatus(int $resignationId, string $status, string $approvedBy = null): bool
    {
        $stmt = $this->db->prepare("
            UPDATE exit_resignations
            SET status = ?, approved_by = ?, approved_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$status, $approvedBy, $resignationId]);
    }

    /**
     * Get resignations by employee ID
     */
    public function getResignationsByEmployee(int $employeeId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM exit_resignations
            WHERE employee_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all resignations
     */
    public function getAllResignations(): array
    {
        return $this->getResignations();
    }

    /**
     * Check if employee is eligible for resignation
     */
    public function checkEmployeeEligibility(string $employeeId): array
    {
        // Check if employee exists in employees table
        $stmt = $this->db->prepare("SELECT employee_id, full_name, employment_status FROM employees WHERE employee_id = ?");
        $stmt->execute([$employeeId]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            return [
                'eligible' => false,
                'reason' => 'Employee not found in the system.'
            ];
        }

        // Check employment status
        if (strtolower($employee['employment_status']) !== 'active') {
            return [
                'eligible' => false,
                'reason' => 'Employee is not currently active.'
            ];
        }

        // Check for existing pending resignation
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM exit_resignations WHERE employee_id = ? AND status = 'pending'");
        $stmt->execute([$employeeId]);
        $pendingCount = (int)$stmt->fetchColumn();

        if ($pendingCount > 0) {
            return [
                'eligible' => false,
                'reason' => 'Employee already has a pending resignation request.'
            ];
        }

        // Check for unresolved settlements
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM exit_employee_settlements WHERE employee_id = ? AND status IN ('draft', 'approved')");
        $stmt->execute([$employeeId]);
        $settlementCount = (int)$stmt->fetchColumn();

        if ($settlementCount > 0) {
            return [
                'eligible' => false,
                'reason' => 'Employee has unresolved settlement processes.'
            ];
        }

        // Check for scheduled exit interviews
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM exit_interviews WHERE employee_id = ? AND status IN ('scheduled', 'completed')");
        $stmt->execute([$employeeId]);
        $interviewCount = (int)$stmt->fetchColumn();

        if ($interviewCount > 0) {
            return [
                'eligible' => false,
                'reason' => 'Employee has ongoing or completed exit interview processes.'
            ];
        }

        // Check for active knowledge transfer plans
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM exit_knowledge_transfer_plans WHERE employee_id = ? AND status = 'active'");
        $stmt->execute([$employeeId]);
        $transferCount = (int)$stmt->fetchColumn();

        if ($transferCount > 0) {
            return [
                'eligible' => false,
                'reason' => 'Employee has active knowledge transfer processes.'
            ];
        }

        return [
            'eligible' => true,
            'reason' => 'Employee is eligible for resignation.'
        ];
    }

    /**
     * Archive resignation
     */
    public function archiveResignation(int $resignationId): bool
    {
        $stmt = $this->db->prepare("SELECT status FROM exit_resignations WHERE id = ?");
        $stmt->execute([$resignationId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }

        $previousStatus = $row['status'] ?? 'pending';

        $stmt = $this->db->prepare("UPDATE exit_resignations SET status = 'archived', archived_from_status = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$previousStatus, $resignationId]);
    }

    /**
     * Unarchive resignation
     */
    public function unarchiveResignation(int $resignationId): bool
    {
        $stmt = $this->db->prepare("SELECT archived_from_status FROM exit_resignations WHERE id = ?");
        $stmt->execute([$resignationId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }

        $restoreStatus = $row['archived_from_status'] ?: 'pending';

        $stmt = $this->db->prepare("UPDATE exit_resignations SET status = ?, archived_from_status = NULL, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$restoreStatus, $resignationId]);
    }
}