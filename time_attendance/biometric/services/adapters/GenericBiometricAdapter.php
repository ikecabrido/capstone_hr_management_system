<?php
/**
 * Generic Biometric Device Adapter
 * Base adapter for different biometric device types
 * Implement device-specific adapters by extending this class
 */

abstract class BiometricDeviceAdapter {
    protected $device;
    protected $ip;
    protected $port;
    protected $timeout;
    
    public function __construct($device) {
        $this->device = $device;
        $this->ip = $device->ip_address ?? BIOMETRIC_DEVICE_IP;
        $this->port = BIOMETRIC_DEVICE_PORT ?? 4370;
        $this->timeout = BIOMETRIC_DEVICE_TIMEOUT ?? 10;
    }
    
    /**
     * Connect to device
     */
    abstract public function connect();
    
    /**
     * Get new logs from device since timestamp
     */
    abstract public function getNewLogs($since_timestamp = null);
    
    /**
     * Enroll employee fingerprint on device
     */
    abstract public function enroll($employee_id, $biometric_data);
    
    /**
     * Delete template from device
     */
    abstract public function deleteTemplate($employee_id);
    
    /**
     * Get device status
     */
    abstract public function getDeviceStatus();
    
    /**
     * Disconnect from device
     */
    abstract public function disconnect();
}

/**
 * Generic Biometric Adapter
 * Used for development/testing until specific device adapter is implemented
 */
class GenericBiometricAdapter extends BiometricDeviceAdapter {
    private $connection;
    private $last_logs = [];
    
    public function connect() {
        // Generic connection logic
        logBiometric('DEBUG', 'Generic adapter: Attempting connection', [
            'ip' => $this->ip,
            'port' => $this->port
        ]);
        
        return true;
    }
    
    public function getNewLogs($since_timestamp = null) {
        logBiometric('DEBUG', 'Generic adapter: Fetching logs', [
            'since' => $since_timestamp
        ]);
        
        // Return empty array for now (no hardware)
        // When NGTeco is integrated, this will fetch actual device logs
        return [];
    }
    
    public function enroll($employee_id, $biometric_data) {
        logBiometric('DEBUG', 'Generic adapter: Enrollment requested', [
            'employee_id' => $employee_id
        ]);
        
        // Simulate enrollment
        return [
            'success' => true,
            'biometric_id' => rand(1, 100),
            'quality_score' => rand(80, 100)
        ];
    }
    
    public function deleteTemplate($employee_id) {
        logBiometric('DEBUG', 'Generic adapter: Delete template', [
            'employee_id' => $employee_id
        ]);
        
        return true;
    }
    
    public function getDeviceStatus() {
        return [
            'status' => 'ok',
            'device_type' => 'generic',
            'memory_usage' => 0,
            'enrolled_count' => 0,
            'firmware' => '1.0 (Generic/Development)'
        ];
    }
    
    public function disconnect() {
        $this->connection = null;
        return true;
    }
}

/**
 * NGTeco Biometric Adapter
 * PLACEHOLDER - Implement when NGTeco hardware is available
 * 
 * This adapter will handle communication with NGTeco devices
 * It follows the same interface as GenericBiometricAdapter
 */
class NGTecoAdapter extends BiometricDeviceAdapter {
    private $connection;
    private $timeout;
    
    public function __construct($device) {
        parent::__construct($device);
        $this->timeout = 10; // NGTeco connection timeout
    }
    
    public function connect() {
        try {
            // TODO: Implement NGTeco connection protocol
            // This will depend on NGTeco API documentation
            // Examples:
            // - HTTP API connection
            // - TCP/IP socket connection
            // - SDK library integration
            
            logBiometric('INFO', 'NGTeco adapter: Connecting to device', [
                'ip' => $this->ip,
                'port' => $this->port
            ]);
            
            // Placeholder connection logic
            // Replace with actual NGTeco protocol
            return $this->testConnection();
            
        } catch (Exception $e) {
            logBiometric('ERROR', 'NGTeco connection failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    public function getNewLogs($since_timestamp = null) {
        // TODO: Implement NGTeco log retrieval
        // This will fetch attendance logs from the device
        // based on NGTeco API documentation
        
        logBiometric('DEBUG', 'NGTeco: Fetching logs since ' . ($since_timestamp ?? 'start'), [
            'device_ip' => $this->ip
        ]);
        
        // Placeholder: return empty array
        // Implementation will parse NGTeco response format
        return [];
    }
    
    public function enroll($employee_id, $biometric_data) {
        // TODO: Implement NGTeco enrollment
        // This will enroll employee fingerprint on device
        
        logBiometric('DEBUG', 'NGTeco: Enrolling employee', [
            'employee_id' => $employee_id
        ]);
        
        // Placeholder
        return [
            'success' => false,
            'message' => 'NGTeco hardware not yet integrated'
        ];
    }
    
    public function deleteTemplate($employee_id) {
        // TODO: Implement template deletion
        logBiometric('DEBUG', 'NGTeco: Deleting template', [
            'employee_id' => $employee_id
        ]);
        
        return false;
    }
    
    public function getDeviceStatus() {
        // TODO: Query device status
        return [
            'status' => 'unknown',
            'device_type' => 'ngteco',
            'message' => 'NGTeco hardware not yet integrated'
        ];
    }
    
    public function disconnect() {
        // TODO: Implement disconnect
        return true;
    }
    
    /**
     * Test connection to NGTeco device
     */
    private function testConnection() {
        // Basic connection test - can be replaced with actual NGTeco API call
        return @fsockopen($this->ip, $this->port, $errno, $errstr, $this->timeout) ? true : false;
    }
}

/**
 * Other device adapters can be implemented similarly
 */
class ZKTecoAdapter extends BiometricDeviceAdapter {
    public function connect() { return false; }
    public function getNewLogs($since_timestamp = null) { return []; }
    public function enroll($employee_id, $biometric_data) { return false; }
    public function deleteTemplate($employee_id) { return false; }
    public function getDeviceStatus() { return []; }
    public function disconnect() { return false; }
}

class MorphoAdapter extends BiometricDeviceAdapter {
    public function connect() { return false; }
    public function getNewLogs($since_timestamp = null) { return []; }
    public function enroll($employee_id, $biometric_data) { return false; }
    public function deleteTemplate($employee_id) { return false; }
    public function getDeviceStatus() { return []; }
    public function disconnect() { return false; }
}

class HikvisionAdapter extends BiometricDeviceAdapter {
    public function connect() { return false; }
    public function getNewLogs($since_timestamp = null) { return []; }
    public function enroll($employee_id, $biometric_data) { return false; }
    public function deleteTemplate($employee_id) { return false; }
    public function getDeviceStatus() { return []; }
    public function disconnect() { return false; }
}
