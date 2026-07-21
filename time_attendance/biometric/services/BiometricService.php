<?php
/**
 * BiometricService
 * Core service for biometric operations (sync, verify, match)
 * Handles device communication abstraction and data processing
 */

require_once(__DIR__ . '/../config/biometric_config.php');

class BiometricService {
    private $db;
    private $device_model;
    private $enrollment_model;
    private $log_model;
    
    public function __construct($db) {
        $this->db = $db;
        $this->device_model = new BiometricDevice($db);
        $this->enrollment_model = new BiometricEnrollment($db);
        $this->log_model = new BiometricLog($db);
    }
    
    /**
     * Sync biometric logs from device
     * This is the main sync function that will be called periodically
     */
    public function syncDevice($device_id) {
        logBiometric('INFO', 'Starting sync for device', ['device_id' => $device_id]);
        
        try {
            $device = $this->device_model->getById($device_id);
            
            if (!$device) {
                throw new Exception("Device not found: $device_id");
            }
            
            // Check device connectivity
            if (!$this->device_model->checkConnectivity($device->ip_address)) {
                logBiometric('ERROR', 'Device not reachable', ['ip' => $device->ip_address]);
                return [
                    'success' => false,
                    'error' => 'Device not reachable',
                    'message' => "Cannot connect to device at {$device->ip_address}"
                ];
            }
            
            // Get device adapter
            $adapter = $this->getDeviceAdapter($device);
            
            // Fetch new logs from device
            $new_logs = $adapter->getNewLogs($device->last_sync);
            
            logBiometric('INFO', 'Logs fetched from device', [
                'device_id' => $device_id,
                'log_count' => count($new_logs)
            ]);
            
            // Process and store logs
            $processed_count = 0;
            $failed_count = 0;
            
            foreach ($new_logs as $log_data) {
                try {
                    $this->processLog($device, $log_data);
                    $processed_count++;
                } catch (Exception $e) {
                    logBiometric('WARNING', 'Failed to process log', [
                        'device_id' => $device_id,
                        'error' => $e->getMessage()
                    ]);
                    $failed_count++;
                }
            }
            
            // Update device last sync time
            $this->device_model->updateLastSync($device_id);
            
            // Record sync operation
            $this->recordSync($device_id, 'success', $processed_count, $failed_count);
            
            logBiometric('INFO', 'Sync completed', [
                'device_id' => $device_id,
                'processed' => $processed_count,
                'failed' => $failed_count
            ]);
            
            return [
                'success' => true,
                'message' => "Synced $processed_count records",
                'processed' => $processed_count,
                'failed' => $failed_count
            ];
            
        } catch (Exception $e) {
            logBiometric('ERROR', 'Sync failed', [
                'device_id' => $device_id,
                'error' => $e->getMessage()
            ]);
            
            $this->recordSync($device_id, 'failed', 0, 0, $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Process individual biometric log
     */
    private function processLog($device, $log_data) {
        // Parse log data from device format
        $log = new BiometricLog($this->db);
        $log->device_id = $device->id;
        $log->biometric_id = $log_data['user_id'] ?? null;
        $log->log_datetime = $log_data['timestamp'] ?? date('Y-m-d H:i:s');
        $log->punch_type = $log_data['punch_type'] ?? 'check_in';
        $log->quality_score = $log_data['quality'] ?? 0;
        $log->raw_data = json_encode($log_data);
        $log->status = 'pending';
        
        $log_id = $log->create();
        
        if ($log_id) {
            // Verify the log (match employee and check rules)
            $this->verifyLog($log_id, $device);
        }
        
        return $log_id;
    }
    
    /**
     * Verify biometric log and match to employee
     */
    public function verifyLog($log_id, $device = null) {
        $log = $this->log_model->getById($log_id);
        
        if (!$log) {
            throw new Exception("Log not found: $log_id");
        }
        
        if (!$device) {
            $device = $this->device_model->getById($log->device_id);
        }
        
        try {
            // Find matching employee by biometric_id
            $enrollment = $this->findEnrollmentByBiometricId(
                $device->id,
                $log->biometric_id
            );
            
            if (!$enrollment) {
                logBiometric('WARNING', 'No enrollment found for biometric_id', [
                    'device_id' => $device->id,
                    'biometric_id' => $log->biometric_id
                ]);
                return false;
            }
            
            // Check for duplicates
            $duplicate = $this->log_model->checkDuplicate(
                $enrollment->employee_id,
                $log->log_datetime
            );
            
            if ($duplicate) {
                logBiometric('INFO', 'Duplicate log detected', [
                    'employee_id' => $enrollment->employee_id,
                    'log_id' => $log_id
                ]);
                
                // Mark as duplicate
                $this->recordException($enrollment->employee_id, $device->id, 'duplicate_detection');
                return false;
            }
            
            // Match employee
            $this->log_model->matchEmployee($log_id, $enrollment->employee_id, 100);
            $this->enrollment_model->incrementVerificationCount($enrollment->id);
            
            // Create attendance record (link to existing attendance system)
            $this->createAttendanceRecord($enrollment->employee_id, $log, $device);
            
            logBiometric('INFO', 'Log verified successfully', [
                'log_id' => $log_id,
                'employee_id' => $enrollment->employee_id
            ]);
            
            return true;
            
        } catch (Exception $e) {
            logBiometric('ERROR', 'Log verification failed', [
                'log_id' => $log_id,
                'error' => $e->getMessage()
            ]);
            
            $this->recordException($log->employee_id ?? null, $log->device_id, 'verification_failed');
            return false;
        }
    }
    
    /**
     * Find enrollment by biometric ID on device
     */
    private function findEnrollmentByBiometricId($device_id, $biometric_id) {
        $query = "SELECT * FROM biometric_enrollments 
                  WHERE device_id = :device_id 
                  AND biometric_id = :biometric_id 
                  AND enrollment_status = 'enrolled'
                  AND is_active = 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':device_id', $device_id);
        $stmt->bindParam(':biometric_id', $biometric_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Create attendance record from biometric log
     */
    private function createAttendanceRecord($employee_id, $log, $device) {
        // This integrates with your existing attendance system
        // Insert into biometric_attendance_link table
        
        $query = "INSERT INTO biometric_attendance_link SET
                    employee_id = :employee_id,
                    verification_datetime = :verification_datetime,
                    attendance_type = 'biometric',
                    confidence_level = 'high',
                    is_verified = 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->bindParam(':verification_datetime', $log->log_datetime);
        
        return $stmt->execute();
    }
    
    /**
     * Record sync operation
     */
    private function recordSync($device_id, $status, $processed, $failed, $error = null) {
        $query = "INSERT INTO biometric_sync_logs SET
                    device_id = :device_id,
                    sync_type = 'scheduled',
                    sync_start = NOW(),
                    sync_end = NOW(),
                    records_synced = :synced,
                    records_processed = :processed,
                    records_failed = :failed,
                    status = :status,
                    error_message = :error";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':device_id', $device_id);
        $stmt->bindParam(':synced', $processed);
        $stmt->bindParam(':processed', $processed);
        $stmt->bindParam(':failed', $failed);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':error', $error);
        
        return $stmt->execute();
    }
    
    /**
     * Record exception for admin review
     */
    private function recordException($employee_id, $device_id, $exception_type, $description = null) {
        $query = "INSERT INTO biometric_exceptions SET
                    employee_id = :employee_id,
                    device_id = :device_id,
                    exception_type = :exception_type,
                    severity = 'warning',
                    description = :description,
                    resolution_status = 'pending'";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->bindParam(':device_id', $device_id);
        $stmt->bindParam(':exception_type', $exception_type);
        $stmt->bindParam(':description', $description);
        
        return $stmt->execute();
    }
    
    /**
     * Get device adapter (abstraction for different device types)
     */
    private function getDeviceAdapter($device) {
        $adapter_class = getBiometricDeviceAdapter($device->device_type ?? 'ngteco');
        
        // For now, return generic adapter
        // When NGTeco hardware arrives, implement NGTecoAdapter
        require_once(__DIR__ . '/../services/adapters/GenericBiometricAdapter.php');
        
        return new GenericBiometricAdapter($device);
    }
    
    /**
     * Get system statistics
     */
    public function getStatistics() {
        return [
            'enrollment_stats' => $this->enrollment_model->getStatistics(),
            'log_stats' => $this->log_model->getStatistics(),
            'devices' => count($this->device_model->getAll()),
        ];
    }
}
