<?php

class Grievance {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create grievance
    public function create($employee_id, $subject, $description, $status = 'open', $assigned_to = null) {
        $sql = "INSERT INTO grievances (employee_id, subject, description, status, assigned_to) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$employee_id, $subject, $description, $status, $assigned_to]);
    }

    private function resolveEmployeeIdentifiers($userId = null, $employeeSessionId = null) {
        $list = [];

        // 1) Use explicit session employee_id first
        if (!empty($employeeSessionId)) {
            $value = trim((string)$employeeSessionId);
            if ($value !== '') {
                $list[] = $value;
            }
            if (preg_match('/^EMP0*(\d+)$/i', $value, $matches)) {
                $list[] = $matches[1];
            }
        }

        // 2) Try to fetch the linked users.employee_id from users table if available
        if (empty($list) && !empty($userId) && is_numeric($userId)) {
            try {
                $userStmt = $this->pdo->prepare('SELECT employee_id FROM users WHERE id = ? LIMIT 1');
                $userStmt->execute([$userId]);
                $userRow = $userStmt->fetch();
                if ($userRow && !empty($userRow['employee_id'])) {
                    $value = trim((string)$userRow['employee_id']);
                    if ($value !== '') {
                        $list[] = $value;
                    }
                    if (preg_match('/^EMP0*(\d+)$/i', $value, $matches)) {
                        $list[] = $matches[1];
                    }
                }
            } catch (Exception $e) {
                // Log or ignore, this is best-effort
            }
        }

        // 3) As a fallback, use numeric userId mapping to EMP### pattern
        if (!empty($userId)) {
            $value = trim((string)$userId);
            if ($value !== '' && !in_array($value, $list, true)) {
                $list[] = $value;
            }
            if (ctype_digit($value)) {
                $list[] = 'EMP' . str_pad(ltrim($value, '0'), 3, '0', STR_PAD_LEFT);
            }
        }

        $list = array_unique(array_filter($list, function($v){ return $v !== ''; }));
        return $list;
    }

    // Get all grievances (with role-based filtering)
    public function getAll($userRole = null, $userId = null, $employeeSessionId = null) {
        if ($userRole === 'employee') {
            $employeeIdentifiers = $this->resolveEmployeeIdentifiers($userId, $employeeSessionId);
            if (count($employeeIdentifiers) === 0) {
                return [];
            }

            $placeholders = implode(',', array_fill(0, count($employeeIdentifiers), '?'));
            $sql = "SELECT id, employee_id, subject, description, status, priority, assigned_to, created_at, updated_at FROM grievances WHERE employee_id IN ($placeholders) ORDER BY id DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($employeeIdentifiers);
            return $stmt->fetchAll();
        }

        // Admins and HR see all grievances
        $stmt = $this->pdo->query('SELECT id, employee_id, subject, description, status, priority, assigned_to, created_at, updated_at FROM grievances ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    // Get grievance by ID (with role-based access control)
    public function getById($id, $userRole = null, $userId = null, $employeeSessionId = null) {
        // Employees can only view their own grievances
        if ($userRole === 'employee') {
            $employeeIdentifiers = $this->resolveEmployeeIdentifiers($userId, $employeeSessionId);
            if (count($employeeIdentifiers) === 0) {
                return null;
            }

            $placeholders = implode(',', array_fill(0, count($employeeIdentifiers), '?'));
            $sql = "SELECT id, employee_id, subject, description, status, priority, assigned_to, created_at, updated_at FROM grievances WHERE id = ? AND employee_id IN ($placeholders)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_merge([$id], $employeeIdentifiers));
        } else {
            $stmt = $this->pdo->prepare('SELECT id, employee_id, subject, description, status, priority, assigned_to, created_at, updated_at FROM grievances WHERE id = ?');
            $stmt->execute([$id]);
        }
        return $stmt->fetch();
    }

    // Update grievance status
    public function updateStatus($id, $status) {
        $sql = "UPDATE grievances SET status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    // Assign grievance
    public function assign($id, $assigned_to) {
        $sql = "UPDATE grievances SET assigned_to = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$assigned_to, $id]);
    }

    // Delete grievance
    public function delete($id) {
        $sql = "DELETE FROM grievances WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Get grievances by employee
    public function getByEmployee($employee_id) {
        $stmt = $this->pdo->prepare('SELECT id, employee_id, subject, description, status, priority, assigned_to, created_at, updated_at FROM grievances WHERE employee_id = ? ORDER BY id DESC');
        $stmt->execute([$employee_id]);
        return $stmt->fetchAll();
    }
}
