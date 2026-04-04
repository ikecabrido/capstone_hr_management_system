<?php
/**
 * EmployeeShift Model
 * Handles employee-to-shift assignment operations
 * 
 * @package Time_and_Attendance
 * @subpackage Models
 */

class EmployeeShift {
    private $conn;
    private $table = 'ta_employee_shifts';

    public $employee_shift_id;
    public $employee_id;
    public $shift_id;
    public $effective_from;
    public $effective_to;
    public $is_active;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Assign shift to employee
     * 
     * @return array ['success' => bool, 'duplicate' => bool, 'existing_id' => int|null]
     */
    public function assign() {
        // Check if this exact assignment already exists
        $existing = $this->checkExistingAssignment($this->employee_id, $this->shift_id, $this->effective_from);
        if ($existing) {
            // Return duplicate indicator instead of silently overwriting
            return [
                'success' => false,
                'duplicate' => true,
                'existing_id' => $existing['employee_shift_id']
            ];
        }

        // Deactivate any other active shifts for this employee (only one shift per employee)
        $this->deactivateOtherShifts($this->employee_id);

        $query = "INSERT INTO " . $this->table . "
                  (employee_id, shift_id, effective_from, effective_to, is_active)
                  VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->employee_id);
        $stmt->bindParam(2, $this->shift_id);
        $stmt->bindParam(3, $this->effective_from);
        $stmt->bindParam(4, $this->effective_to);
        $stmt->bindParam(5, $this->is_active);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'duplicate' => false,
                'existing_id' => null
            ];
        }
        return [
            'success' => false,
            'duplicate' => false,
            'existing_id' => null
        ];
    }

    /**
     * Force assign shift by overwriting existing assignment
     * Used when user confirms overwrite in confirmation modal
     * 
     * @param int $existing_employee_shift_id
     * @return bool
     */
    public function forceAssign($existing_employee_shift_id) {
        // Delete the old assignment
        $deleteQuery = "DELETE FROM " . $this->table . " WHERE employee_shift_id = ?";
        $deleteStmt = $this->conn->prepare($deleteQuery);
        $deleteStmt->execute([$existing_employee_shift_id]);

        // Deactivate any other active shifts
        $this->deactivateOtherShifts($this->employee_id);

        // Create new assignment
        $query = "INSERT INTO " . $this->table . "
                  (employee_id, shift_id, effective_from, effective_to, is_active)
                  VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->employee_id);
        $stmt->bindParam(2, $this->shift_id);
        $stmt->bindParam(3, $this->effective_from);
        $stmt->bindParam(4, $this->effective_to);
        $stmt->bindParam(5, $this->is_active);

        return $stmt->execute();
    }

    /**
     * Update employee shift assignment
     * 
     * @return bool
     */
    public function update() {
        $query = "UPDATE " . $this->table . "
                  SET shift_id = ?, effective_from = ?, effective_to = ?, is_active = ?
                  WHERE employee_shift_id = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->shift_id);
        $stmt->bindParam(2, $this->effective_from);
        $stmt->bindParam(3, $this->effective_to);
        $stmt->bindParam(4, $this->is_active);
        $stmt->bindParam(5, $this->employee_shift_id);

        return $stmt->execute();
    }

    /**
     * Get shift assignment for employee
     * 
     * @param int $employee_id
     * @param bool $active_only
     * @return array
     */
    public function getEmployeeAssignments($employee_id, $active_only = true) {
        $query = "SELECT es.*, s.shift_name, s.start_time, s.end_time, 
                         e.full_name
                  FROM " . $this->table . " es
                  INNER JOIN ta_shifts s ON es.shift_id = s.shift_id
                  INNER JOIN employees e ON es.employee_id = e.employee_id
                  WHERE es.employee_id = ?";

        if ($active_only) {
            $query .= " AND es.is_active = 1";
        }

        $query .= " ORDER BY es.effective_from DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$employee_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get current shift for employee
     * 
     * @param int $employee_id
     * @return array
     */
    public function getCurrentShift($employee_id) {
        $query = "SELECT es.*, s.shift_name, s.start_time, s.end_time, s.break_duration
                  FROM " . $this->table . " es
                  INNER JOIN ta_shifts s ON es.shift_id = s.shift_id
                  WHERE es.employee_id = ? AND es.is_active = 1 
                  AND es.effective_from <= CURDATE()
                  AND (es.effective_to IS NULL OR es.effective_to >= CURDATE())
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$employee_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all employees with their current shifts
     * 
     * @param int $shift_id - Filter by specific shift (optional)
     * @return array
     */
    public function getAllAssignments($shift_id = null) {
        $query = "SELECT es.*, s.shift_name, s.start_time, s.end_time,
                         e.full_name, e.department
                  FROM " . $this->table . " es
                  INNER JOIN ta_shifts s ON es.shift_id = s.shift_id
                  INNER JOIN employees e ON es.employee_id = e.employee_id
                  WHERE es.is_active = 1";

        if ($shift_id) {
            $query .= " AND es.shift_id = ?";
        }

        $query .= " ORDER BY s.shift_name, e.full_name";

        $stmt = $this->conn->prepare($query);

        if ($shift_id) {
            $stmt->execute([$shift_id]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get count of employees on a specific shift
     * 
     * @param int $shift_id
     * @return int
     */
    public function getShiftEmployeeCount($shift_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . "
                  WHERE shift_id = ? AND is_active = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$shift_id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    /**
     * Deactivate other shift assignments for employee
     * 
     * @param int $employee_id
     * @return bool
     */
    private function deactivateOtherShifts($employee_id) {
        $query = "UPDATE " . $this->table . "
                  SET is_active = 0
                  WHERE employee_id = ? AND is_active = 1";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$employee_id]);
    }

    /**
     * Check if assignment already exists for employee and shift
     * 
     * @param int $employee_id
     * @param int $shift_id
     * @param string $effective_from
     * @return array|false
     */
    private function checkExistingAssignment($employee_id, $shift_id, $effective_from) {
        $query = "SELECT employee_shift_id FROM " . $this->table . "
                  WHERE employee_id = ? AND shift_id = ? AND effective_from = ?
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$employee_id, $shift_id, $effective_from]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Reactivate existing shift assignment (if it was deactivated)
     * 
     * @param int $employee_shift_id
     * @return bool
     */
    private function assignExisting($employee_shift_id) {
        $query = "UPDATE " . $this->table . "
                  SET is_active = 1, effective_to = ?
                  WHERE employee_shift_id = ?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->effective_to, $employee_shift_id]);
    }

    /**
     * Remove shift assignment
     * 
     * @param int $employee_shift_id
     * @return bool
     */
    public function remove($employee_shift_id) {
        $query = "DELETE FROM " . $this->table . " WHERE employee_shift_id = ?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$employee_shift_id]);
    }

    /**
     * Check if employee has shift assigned
     * 
     * @param int $employee_id
     * @return bool
     */
    public function hasActiveShift($employee_id) {
        $shift = $this->getCurrentShift($employee_id);
        return !empty($shift);
    }
}
?>
