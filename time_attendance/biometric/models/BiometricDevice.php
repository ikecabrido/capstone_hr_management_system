<?php
/**
 * BiometricDevice Model
 * Manages biometric device information and operations
 */

class BiometricDevice {
    private $db;
    private $table = 'biometric_devices';
    
    public $id;
    public $device_name;
    public $device_type;
    public $manufacturer;
    public $model;
    public $serial_number;
    public $ip_address;
    public $mac_address;
    public $location;
    public $status;
    public $last_sync;
    public $capacity;
    public $enrolled_count;
    public $firmware_version;
    public $notes;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Create a new biometric device
     */
    public function create() {
        $query = "INSERT INTO {$this->table} SET
                    device_name = :device_name,
                    device_type = :device_type,
                    manufacturer = :manufacturer,
                    model = :model,
                    serial_number = :serial_number,
                    ip_address = :ip_address,
                    mac_address = :mac_address,
                    location = :location,
                    status = :status,
                    capacity = :capacity,
                    firmware_version = :firmware_version,
                    notes = :notes";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':device_name', $this->device_name);
        $stmt->bindParam(':device_type', $this->device_type);
        $stmt->bindParam(':manufacturer', $this->manufacturer);
        $stmt->bindParam(':model', $this->model);
        $stmt->bindParam(':serial_number', $this->serial_number);
        $stmt->bindParam(':ip_address', $this->ip_address);
        $stmt->bindParam(':mac_address', $this->mac_address);
        $stmt->bindParam(':location', $this->location);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':capacity', $this->capacity);
        $stmt->bindParam(':firmware_version', $this->firmware_version);
        $stmt->bindParam(':notes', $this->notes);
        
        return $stmt->execute();
    }
    
    /**
     * Get all active devices
     */
    public function getAll() {
        $query = "SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY location ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Get device by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Update device
     */
    public function update() {
        $query = "UPDATE {$this->table} SET
                    device_name = :device_name,
                    status = :status,
                    location = :location,
                    firmware_version = :firmware_version,
                    notes = :notes,
                    updated_at = NOW()
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':device_name', $this->device_name);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':location', $this->location);
        $stmt->bindParam(':firmware_version', $this->firmware_version);
        $stmt->bindParam(':notes', $this->notes);
        
        return $stmt->execute();
    }
    
    /**
     * Update last sync time
     */
    public function updateLastSync($id) {
        $query = "UPDATE {$this->table} SET last_sync = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    /**
     * Update enrolled count
     */
    public function updateEnrolledCount($id, $count) {
        $query = "UPDATE {$this->table} SET enrolled_count = :count WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':count', $count);
        return $stmt->execute();
    }
    
    /**
     * Get device performance stats
     */
    public function getPerformanceStats($id) {
        $query = "SELECT 
                    bd.id,
                    bd.device_name,
                    bd.location,
                    bd.enrolled_count,
                    COUNT(bl.id) as total_logs,
                    COUNT(CASE WHEN bl.status = 'processed' THEN 1 END) as processed_logs,
                    ROUND(COUNT(CASE WHEN bl.is_matched = 1 THEN 1 END) * 100 / COUNT(bl.id), 2) as match_rate,
                    bd.last_sync
                  FROM {$this->table} bd
                  LEFT JOIN biometric_logs bl ON bd.id = bl.device_id
                  WHERE bd.id = :id
                  GROUP BY bd.id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Check device connectivity
     */
    public function checkConnectivity($ip, $port = 4370) {
        $timeout = BIOMETRIC_DEVICE_TIMEOUT ?? 5;
        $connected = @fsockopen($ip, $port, $errno, $errstr, $timeout);
        
        if ($connected) {
            fclose($connected);
            return true;
        }
        return false;
    }
    
    /**
     * Delete device
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
