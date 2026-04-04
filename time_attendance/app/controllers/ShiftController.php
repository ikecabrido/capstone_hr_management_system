<?php
/**
 * Shift Controller
 * Handles shift management operations
 * 
 * @package Time_and_Attendance
 * @subpackage Controllers
 */

require_once(__DIR__ . '/../models/Shift.php');
require_once(__DIR__ . '/../models/EmployeeShift.php');
require_once(__DIR__ . '/../models/Employee.php');
require_once(__DIR__ . '/../helpers/AuditLog.php');

class ShiftController {
    private $db;
    private $shift;
    private $employeeShift;
    private $employee;
    private $auditLog;

    public function __construct($db) {
        $this->db = $db;
        $this->shift = new Shift($db);
        $this->employeeShift = new EmployeeShift($db);
        $this->employee = new Employee();
        $this->auditLog = new AuditLog($db);
    }

    /**
     * Get all shifts
     */
    public function getAllShifts($active_only = false) {
        return $this->shift->getAll($active_only);
    }

    /**
     * Get shift by ID
     */
    public function getShiftById($shift_id) {
        return $this->shift->getById($shift_id);
    }

    /**
     * Create new shift
     */
    public function createShift($data) {
        // Validate required fields
        if (empty($data['shift_name']) || empty($data['start_time']) || empty($data['end_time'])) {
            return ['success' => false, 'message' => 'Missing required fields'];
        }

        $this->shift->shift_name = $data['shift_name'];
        $this->shift->start_time = $data['start_time'];
        $this->shift->end_time = $data['end_time'];
        $this->shift->break_duration = $data['break_duration'] ?? 60;
        $this->shift->description = $data['description'] ?? null;
        $this->shift->include_saturday = $data['include_saturday'] ?? 0;
        $this->shift->is_active = $data['is_active'] ?? 1;

        if ($this->shift->create()) {
            // Log action
            $this->auditLog->log(
                $_SESSION['user_id'] ?? null,
                'SHIFT_CREATED',
                'shifts',
                $this->shift->shift_name,
                'Created new shift: ' . $this->shift->shift_name
            );

            return ['success' => true, 'message' => 'Shift created successfully'];
        }

        return ['success' => false, 'message' => 'Failed to create shift'];
    }

    /**
     * Update shift
     */
    public function updateShift($shift_id, $data) {
        $shift = $this->shift->getById($shift_id);
        if (empty($shift)) {
            return ['success' => false, 'message' => 'Shift not found'];
        }

        $this->shift->shift_id = $shift_id;
        $this->shift->shift_name = $data['shift_name'] ?? $shift['shift_name'];
        $this->shift->start_time = $data['start_time'] ?? $shift['start_time'];
        $this->shift->end_time = $data['end_time'] ?? $shift['end_time'];
        $this->shift->break_duration = $data['break_duration'] ?? $shift['break_duration'];
        $this->shift->description = $data['description'] ?? $shift['description'];
        $this->shift->include_saturday = isset($data['include_saturday']) ? $data['include_saturday'] : ($shift['include_saturday'] ?? 0);
        $this->shift->is_active = isset($data['is_active']) ? $data['is_active'] : $shift['is_active'];

        if ($this->shift->update()) {
            // Log action
            $this->auditLog->log(
                $_SESSION['user_id'] ?? null,
                'SHIFT_UPDATED',
                'shifts',
                $shift_id,
                'Updated shift: ' . $this->shift->shift_name
            );

            return ['success' => true, 'message' => 'Shift updated successfully'];
        }

        return ['success' => false, 'message' => 'Failed to update shift'];
    }

    /**
     * Delete shift
     */
    public function deleteShift($shift_id) {
        $shift = $this->shift->getById($shift_id);
        if (empty($shift)) {
            return ['success' => false, 'message' => 'Shift not found'];
        }

        if ($this->shift->delete($shift_id)) {
            // Log action
            $this->auditLog->log(
                $_SESSION['user_id'] ?? null,
                'SHIFT_DELETED',
                'shifts',
                $shift_id,
                'Deleted shift: ' . $shift['shift_name']
            );

            return ['success' => true, 'message' => 'Shift deleted successfully'];
        }

        return ['success' => false, 'message' => 'Failed to delete shift'];
    }

    /**
     * Assign shift to employee
     */
    public function assignShiftToEmployee($employee_id, $shift_id, $effective_from, $effective_to = null) {
        // Validate employee exists
        $employee = $this->employee->getById($employee_id);
        if (empty($employee)) {
            return ['success' => false, 'message' => 'Employee not found in the system', 'duplicate' => false];
        }

        // Validate shift exists
        $shift = $this->shift->getById($shift_id);
        if (empty($shift)) {
            return ['success' => false, 'message' => 'Shift not found', 'duplicate' => false];
        }

        $this->employeeShift->employee_id = $employee_id;
        $this->employeeShift->shift_id = $shift_id;
        $this->employeeShift->effective_from = $effective_from;
        $this->employeeShift->effective_to = $effective_to;
        $this->employeeShift->is_active = 1;

        try {
            $result = $this->employeeShift->assign();
            
            // Check for duplicate detection
            if (isset($result['duplicate']) && $result['duplicate']) {
                return [
                    'success' => false,
                    'duplicate' => true,
                    'existing_id' => $result['existing_id'],
                    'employee_id' => $employee_id,
                    'employee_name' => $employee['full_name'],
                    'shift_name' => $shift['shift_name'],
                    'message' => $employee['full_name'] . ' already has ' . $shift['shift_name'] . ' assigned for this date. Overwrite?'
                ];
            }
            
            if ($result['success']) {
                // Log action
                $this->auditLog->log(
                    $_SESSION['user_id'] ?? null,
                    'SHIFT_ASSIGNED',
                    'employee_shifts',
                    $employee_id,
                    'Assigned ' . $shift['shift_name'] . ' to employee ID: ' . $employee_id
                );

                return ['success' => true, 'message' => 'Shift assigned successfully', 'duplicate' => false];
            }

            return ['success' => false, 'message' => 'Failed to assign shift', 'duplicate' => false];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage(), 'duplicate' => false];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage(), 'duplicate' => false];
        }
    }

    /**
     * Force assign shift (overwrite existing)
     */
    public function forceAssignShift($employee_id, $shift_id, $effective_from, $effective_to = null, $existing_employee_shift_id) {
        // Validate employee exists
        $employee = $this->employee->getById($employee_id);
        if (empty($employee)) {
            return ['success' => false, 'message' => 'Employee not found in the system'];
        }

        // Validate shift exists
        $shift = $this->shift->getById($shift_id);
        if (empty($shift)) {
            return ['success' => false, 'message' => 'Shift not found'];
        }

        $this->employeeShift->employee_id = $employee_id;
        $this->employeeShift->shift_id = $shift_id;
        $this->employeeShift->effective_from = $effective_from;
        $this->employeeShift->effective_to = $effective_to;
        $this->employeeShift->is_active = 1;

        try {
            if ($this->employeeShift->forceAssign($existing_employee_shift_id)) {
                // Log action
                $this->auditLog->log(
                    $_SESSION['user_id'] ?? null,
                    'SHIFT_OVERWRITTEN',
                    'employee_shifts',
                    $employee_id,
                    'Overwritten ' . $shift['shift_name'] . ' for employee ID: ' . $employee_id
                );

                return ['success' => true, 'message' => 'Shift overwritten successfully'];
            }
            return ['success' => false, 'message' => 'Failed to overwrite shift'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()];
        }
    }

    /**
     * Get employee shift assignments
     */
    public function getEmployeeShifts($employee_id) {
        return $this->employeeShift->getEmployeeAssignments($employee_id, false);
    }

    /**
     * Get current shift for employee
     */
    public function getCurrentShift($employee_id) {
        return $this->employeeShift->getCurrentShift($employee_id);
    }

    /**
     * Get employees on specific shift
     */
    public function getEmployeesOnShift($shift_id) {
        return $this->employeeShift->getAllAssignments($shift_id);
    }

    /**
     * Check if time is within shift hours
     */
    public function isWithinShiftHours($employee_id, $time, $date = null) {
        return $this->shift->isWithinShiftHours($employee_id, $time, $date);
    }

    /**
     * Get shift statistics
     */
    public function getShiftStatistics() {
        $shifts = $this->shift->getAll();
        $stats = [];

        foreach ($shifts as $shift) {
            $count = $this->employeeShift->getShiftEmployeeCount($shift['shift_id']);
            $stats[] = [
                'shift_id' => $shift['shift_id'],
                'shift_name' => $shift['shift_name'],
                'start_time' => $shift['start_time'],
                'end_time' => $shift['end_time'],
                'employee_count' => $count,
                'is_active' => $shift['is_active']
            ];
        }

        return $stats;
    }
}
?>
