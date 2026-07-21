<?php
/**
 * BiometricEnrollment Model
 * Manages employee biometric enrollment data
 */

class BiometricEnrollment {
    private $db;
    private $table = 'biometric_enrollments';
    
    public $id;
    public $employee_id;
    public $device_id;
    public $biometric_id;
    public $enrollment_type;
    public $finger_position;
    public $quality_score;
    public $enrollment_status;
    public $enrollment_date;
    public $verification_count;
    public $failed_attempts;
    public $is_active;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Create new enrollment
     */
    public function create() {
        $query = "INSERT INTO {$this->table} SET
                    employee_id = :employee_id,
                    device_id = :device_id,
                    biometric_id = :biometric_id,
                    enrollment_type = :enrollment_type,
                    finger_position = :finger_position,
                    quality_score = :quality_score,
                    enrollment_status = :enrollment_status,
                    enrollment_date = NOW()";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':employee_id', $this->employee_id);
        $stmt->bindParam(':device_id', $this->device_id);
        $stmt->bindParam(':biometric_id', $this->biometric_id);
        $stmt->bindParam(':enrollment_type', $this->enrollment_type);
        $stmt->bindParam(':finger_position', $this->finger_position);
        $stmt->bindParam(':quality_score', $this->quality_score);
        $stmt->bindParam(':enrollment_status', $this->enrollment_status);
        
        return $stmt->execute();
    }
    
    /**
     * Get enrollments for employee
     */
    public function getEmployeeEnrollments($employee_id) {
        $query = "SELECT be.*, bd.device_name, bd.location
                  FROM {$this->table} be
                  JOIN biometric_devices bd ON be.device_id = bd.id
                  WHERE be.employee_id = :employee_id AND be.is_active = 1
                  ORDER BY bd.location ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Get enrollment by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Check if employee enrolled on device
     */
    public function isEnrolled($employee_id, $device_id) {
        $query = "SELECT id FROM {$this->table} 
                  WHERE employee_id = :employee_id 
                  AND device_id = :device_id 
                  AND enrollment_status = 'enrolled'
                  AND is_active = 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->bindParam(':device_id', $device_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Update enrollment status
     */
    public function updateStatus($id, $status) {
        $query = "UPDATE {$this->table} SET 
                  enrollment_status = :status,
                  updated_at = NOW()
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }
    
    /**
     * Increment verification count
     */
    public function incrementVerificationCount($id) {
        $query = "UPDATE {$this->table} SET 
                  verification_count = verification_count + 1,
                  last_verified = NOW()
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Get enrollment statistics
     */
    public function getStatistics() {
        $query = "SELECT 
                    COUNT(*) as total_enrollments,
                    COUNT(CASE WHEN enrollment_status = 'enrolled' THEN 1 END) as active_enrollments,
                    COUNT(CASE WHEN enrollment_status = 'pending' THEN 1 END) as pending_enrollments,
                    COUNT(CASE WHEN enrollment_status = 'rejected' THEN 1 END) as rejected_enrollments,
                    COUNT(DISTINCT employee_id) as enrolled_employees
                  FROM {$this->table}
                  WHERE is_active = 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Get employees not yet enrolled on device
     */
    public function getUnenrolledEmployees($device_id) {
        $query = "SELECT e.id, e.name, e.email, e.employee_number
                  FROM employee e
                  WHERE e.is_active = 1
                  AND e.id NOT IN (
                      SELECT DISTINCT employee_id FROM {$this->table}
                      WHERE device_id = :device_id AND enrollment_status = 'enrolled'
                  )
                  ORDER BY e.name ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':device_id', $device_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
