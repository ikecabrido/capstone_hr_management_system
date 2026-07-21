<?php
/**
 * BiometricLog Model
 * Manages raw biometric logs from devices
 */

class BiometricLog {
    private $db;
    private $table = 'biometric_logs';
    
    public $id;
    public $device_id;
    public $employee_id;
    public $biometric_id;
    public $log_datetime;
    public $punch_type;
    public $is_matched;
    public $match_confidence;
    public $quality_score;
    public $status;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Create new biometric log
     */
    public function create() {
        $query = "INSERT INTO {$this->table} SET
                    device_id = :device_id,
                    biometric_id = :biometric_id,
                    log_datetime = :log_datetime,
                    punch_type = :punch_type,
                    quality_score = :quality_score,
                    raw_data = :raw_data,
                    status = :status";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':device_id', $this->device_id);
        $stmt->bindParam(':biometric_id', $this->biometric_id);
        $stmt->bindParam(':log_datetime', $this->log_datetime);
        $stmt->bindParam(':punch_type', $this->punch_type);
        $stmt->bindParam(':quality_score', $this->quality_score);
        $stmt->bindParam(':raw_data', $this->raw_data ?? null);
        $stmt->bindParam(':status', $this->status);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Get unprocessed logs
     */
    public function getUnprocessed($device_id = null, $limit = 100) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE status = 'pending'";
        
        if ($device_id) {
            $query .= " AND device_id = :device_id";
        }
        
        $query .= " ORDER BY log_datetime ASC LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        
        if ($device_id) {
            $stmt->bindParam(':device_id', $device_id);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Update log with matched employee
     */
    public function matchEmployee($id, $employee_id, $confidence) {
        $query = "UPDATE {$this->table} SET
                    employee_id = :employee_id,
                    is_matched = 1,
                    match_confidence = :confidence,
                    status = 'processed'
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->bindParam(':confidence', $confidence);
        
        return $stmt->execute();
    }
    
    /**
     * Mark log as verified
     */
    public function verify($id) {
        $query = "UPDATE {$this->table} SET status = 'verified' WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    /**
     * Get today's logs for employee
     */
    public function getTodayLogsForEmployee($employee_id) {
        $query = "SELECT * FROM {$this->table}
                  WHERE employee_id = :employee_id
                  AND DATE(log_datetime) = CURDATE()
                  ORDER BY log_datetime DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Get logs statistics
     */
    public function getStatistics() {
        $query = "SELECT 
                    COUNT(*) as total_logs,
                    COUNT(CASE WHEN status = 'processed' THEN 1 END) as processed_logs,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_logs,
                    COUNT(CASE WHEN is_matched = 1 THEN 1 END) as matched_logs,
                    ROUND(COUNT(CASE WHEN is_matched = 1 THEN 1 END) * 100 / COUNT(*), 2) as match_rate
                  FROM {$this->table}
                  WHERE DATE(log_datetime) = CURDATE()";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Check for duplicate logs (same employee within time window)
     */
    public function checkDuplicate($employee_id, $log_datetime, $window_seconds = 300) {
        $query = "SELECT id FROM {$this->table}
                  WHERE employee_id = :employee_id
                  AND ABS(TIMESTAMPDIFF(SECOND, log_datetime, :log_datetime)) < :window_seconds
                  AND log_datetime != :log_datetime
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->bindParam(':log_datetime', $log_datetime);
        $stmt->bindParam(':window_seconds', $window_seconds, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
