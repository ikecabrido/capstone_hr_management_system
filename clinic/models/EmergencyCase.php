<?php
/**
 * EmergencyCase Model
 * 
 * Manages emergency case data and operations
 */

require_once __DIR__ . "/../core/BaseModel.php";

class EmergencyCase extends BaseModel {
    protected $table_name = 'cm_emergency_cases';
    protected $primary_key = 'case_id';
    
    /**
     * Get table name
     * @return string
     */
    public function getTableName() {
        return $this->table_name;
    }
    
    /**
     * Get primary key
     * @return string
     */
    public function getPrimaryKey() {
        return $this->primary_key;
    }
    
    /**
     * Get emergency statistics
     * @return array
     */
    public function getEmergencyStats() {
        $stats = [];
        
        try {
            // Total emergencies
            $stats['total_emergencies'] = $this->count();
            
            // Active emergencies
            $stats['active_emergencies'] = $this->count(['case_status' => 'Active']) + $this->count(['case_status' => 'Open']) + $this->count(['case_status' => 'Transferred']);
            
            // Critical cases
            $stats['critical_cases'] = $this->count(['severity_level' => 'Critical']);
            
            // High severity cases
            $stats['high_severity_cases'] = $this->count(['severity_level' => 'High']);
            
            // Resolved cases
            $stats['resolved_cases'] = $this->count(['case_status' => 'Resolved']);
            
            // Transferred cases
            $stats['transferred_cases'] = $this->count(['case_status' => 'Transferred']);
            
            // Ambulance calls
            $stats['ambulance_calls'] = $this->count(['ambulance_called' => 1]);
            
            // Today's emergencies
            $sql = "SELECT COUNT(*) as count FROM {$this->table_name} 
                    WHERE DATE(incident_date) = CURDATE()";
            $result = $this->query($sql);
            $stats['today_emergencies'] = $result[0]['count'] ?? 0;
            
            // Average response time (in minutes)
            $sql = "SELECT AVG(TIME_TO_SEC(TIMEDIFF(ambulance_arrival_time, incident_date))/60) as avg_response_time
                    FROM {$this->table_name} 
                    WHERE ambulance_arrival_time IS NOT NULL";
            $result = $this->query($sql);
            $stats['emergency_response_time'] = round($result[0]['avg_response_time'] ?? 0, 2);
            
        } catch (Exception $e) {
            error_log("Error getting emergency stats: " . $e->getMessage());
        }
        
        return $stats;
    }
    
    /**
     * Get active emergency cases
     * @return array
     */
    public function readActive() {
        $sql = "SELECT ec.*, p.first_name, p.last_name, p.patient_type
                FROM {$this->table_name} ec
                INNER JOIN cm_patients p ON ec.patient_id = p.patient_id
                WHERE ec.case_status IN ('Active', 'Open', 'Transferred')
                ORDER BY ec.incident_date DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting active cases: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all emergency cases with patient details
     * @param string $order_by
     * @return array
     */
    public function readAllWithPatientDetails($order_by = 'incident_date DESC') {
        $sql = "SELECT ec.*, p.first_name, p.last_name, p.patient_type
                FROM {$this->table_name} ec
                LEFT JOIN cm_patients p ON ec.patient_id = p.patient_id
                ORDER BY $order_by";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting all cases with patient details: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Close an emergency case
     * @param string $case_id - Case ID
     * @param string $closed_by - Name of person closing the case
     * @return bool
     */
    public function closeCase($case_id, $closed_by = '') {
        $sql = "UPDATE {$this->table_name} 
                SET case_status = 'Closed', 
                    updated_at = CURRENT_TIMESTAMP,
                    notes = CONCAT(IFNULL(notes, ''), '\n\nCase closed by: ', ?)
                WHERE {$this->primary_key} = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$closed_by, $case_id]);
        } catch (PDOException $e) {
            error_log("Error closing case: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get cases with patient details
     * @param array $conditions - WHERE conditions
     * @param string $order_by - ORDER BY clause
     * @param int $limit - LIMIT clause
     * @return array
     */
    public function getCasesWithPatientDetails($conditions = [], $order_by = 'incident_date DESC', $limit = '') {
        $sql = "SELECT ec.*, p.first_name, p.last_name, p.patient_type, e.first_name as staff_first_name, e.last_name as staff_last_name
                FROM {$this->table_name} ec
                INNER JOIN cm_patients p ON ec.patient_id = p.patient_id
                LEFT JOIN cm_patients e ON ec.attending_staff = e.patient_id";
        
        $params = [];
        
        // Add WHERE conditions
        if (!empty($conditions)) {
            $where_clauses = [];
            foreach ($conditions as $field => $value) {
                $where_clauses[] = "ec.$field = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where_clauses);
        }
        
        // Add ORDER BY
        if (!empty($order_by)) {
            $sql .= " ORDER BY $order_by";
        }
        
        // Add LIMIT
        if (!empty($limit)) {
            $sql .= " LIMIT $limit";
        }
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting cases with patient details: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get cases by severity level
     * @param string $severity - Severity level
     * @return array
     */
    public function getBySeverity($severity) {
        return $this->getCasesWithPatientDetails(['severity_level' => $severity]);
    }
    
    /**
     * Get cases by status
     * @param string $status - Case status
     * @return array
     */
    public function getByStatus($status) {
        return $this->getCasesWithPatientDetails(['case_status' => $status]);
    }
    
    /**
     * Get cases by incident type
     * @param string $incident_type - Incident type
     * @return array
     */
    public function getByIncidentType($incident_type) {
        return $this->getCasesWithPatientDetails(['incident_type' => $incident_type]);
    }
    
    /**
     * Get cases by date range
     * @param string $start_date - Start date
     * @param string $end_date - End date
     * @return array
     */
    public function getByDateRange($start_date, $end_date) {
        $sql = "SELECT ec.*, p.first_name, p.last_name, p.patient_type
                FROM {$this->table_name} ec
                INNER JOIN cm_patients p ON ec.patient_id = p.patient_id
                WHERE DATE(ec.incident_date) BETWEEN ? AND ?
                ORDER BY ec.incident_date DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$start_date, $end_date]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting cases by date range: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get recent cases
     * @param int $limit - Number of cases to get
     * @return array
     */
    public function getRecentCases($limit = 10) {
        return $this->getCasesWithPatientDetails([], 'incident_date DESC', $limit);
    }
    
    /**
     * Update case status
     * @param string $case_id - Case ID
     * @param string $status - New status
     * @return bool
     */
    public function updateStatus($case_id, $status) {
        return $this->update($case_id, ['case_status' => $status]);
    }
    
    /**
     * Call ambulance for case
     * @param string $case_id - Case ID
     * @param string $called_by - Who called ambulance
     * @return bool
     */
    public function callAmbulance($case_id, $called_by) {
        $this->beginTransaction();
        
        try {
            // Update ambulance_called flag
            if (!$this->update($case_id, ['ambulance_called' => 1])) {
                throw new Exception("Failed to update ambulance status");
            }
            
            // Add notes
            $notes = "Ambulance called by: $called_by on " . date('Y-m-d H:i:s');
            $sql = "UPDATE {$this->table_name} 
                    SET notes = CONCAT(IFNULL(notes, ''), '\n', ?),
                        updated_at = NOW()
                    WHERE case_id = ?";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt->execute([$notes, $case_id])) {
                throw new Exception("Failed to add ambulance notes");
            }
            
            $this->commit();
            return true;
            
        } catch (Exception $e) {
            $this->rollback();
            error_log("Error calling ambulance: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update ambulance arrival time
     * @param string $case_id - Case ID
     * @param string $arrival_time - Arrival time
     * @return bool
     */
    public function updateAmbulanceArrival($case_id, $arrival_time) {
        return $this->update($case_id, ['ambulance_arrival_time' => $arrival_time]);
    }
    
    /**
     * Transfer patient to hospital
     * @param string $case_id - Case ID
     * @param string $hospital - Hospital name
     * @return bool
     */
    public function transferToHospital($case_id, $hospital) {
        $this->beginTransaction();
        
        try {
            // Update transfer details
            $update_data = [
                'transfer_hospital' => $hospital,
                'case_status' => 'Transferred'
            ];
            
            if (!$this->update($case_id, $update_data)) {
                throw new Exception("Failed to update transfer details");
            }
            
            // Add transfer notes
            $notes = "Patient transferred to $hospital on " . date('Y-m-d H:i:s');
            $sql = "UPDATE {$this->table_name} 
                    SET notes = CONCAT(IFNULL(notes, ''), '\n', ?),
                        updated_at = NOW()
                    WHERE case_id = ?";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt->execute([$notes, $case_id])) {
                throw new Exception("Failed to add transfer notes");
            }
            
            $this->commit();
            return true;
            
        } catch (Exception $e) {
            $this->rollback();
            error_log("Error transferring patient: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get emergency trends
     * @param int $days - Number of days to analyze
     * @return array
     */
    public function getEmergencyTrends($days = 30) {
        $sql = "SELECT DATE(incident_date) as incident_date, COUNT(*) as cases
                FROM {$this->table_name}
                WHERE incident_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY DATE(incident_date)
                ORDER BY incident_date ASC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$days]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting emergency trends: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get severity distribution
     * @return array
     */
    public function getSeverityDistribution() {
        $sql = "SELECT severity_level, COUNT(*) as count
                FROM {$this->table_name}
                GROUP BY severity_level
                ORDER BY FIELD(severity_level, 'Critical', 'High', 'Medium', 'Low')";
        
        $result = $this->query($sql);
        return $result ?: [];
    }
    
    /**
     * Get incident type distribution
     * @return array
     */
    public function getIncidentTypeDistribution() {
        $sql = "SELECT incident_type, COUNT(*) as count
                FROM {$this->table_name}
                GROUP BY incident_type
                ORDER BY count DESC";
        
        $result = $this->query($sql);
        return $result ?: [];
    }
    
    /**
     * Validate emergency case data
     * @param array $data - Emergency case data
     * @return array - Validation errors
     */
    public function validateEmergencyCase($data) {
        $errors = [];
        
        // Required fields
        $required = ['patient_id', 'incident_type', 'severity_level', 'chief_complaint'];
        $errors = array_merge($errors, $this->validateRequired($data, $required));
        
        // Incident date validation
        if (!empty($data['incident_date'])) {
            $incident_date = DateTime::createFromFormat('Y-m-d H:i:s', $data['incident_date']);
            if (!$incident_date || $incident_date > new DateTime()) {
                $errors['incident_date'] = 'Invalid incident date';
            }
        }
        
        // Follow-up date validation
        if (!empty($data['follow_up_date'])) {
            $follow_up_date = DateTime::createFromFormat('Y-m-d', $data['follow_up_date']);
            if (!$follow_up_date || $follow_up_date < new DateTime()) {
                $errors['follow_up_date'] = 'Follow-up date must be in the future';
            }
        }
        
        return $errors;
    }
    
    /**
     * Get cases requiring follow-up
     * @return array
     */
    public function getFollowUpRequired() {
        $sql = "SELECT ec.*, p.first_name, p.last_name, p.patient_type
                FROM {$this->table_name} ec
                INNER JOIN cm_patients p ON ec.patient_id = p.patient_id
                WHERE ec.follow_up_required = 1 
                AND ec.follow_up_date <= CURDATE()
                AND ec.case_status != 'Closed'
                ORDER BY ec.follow_up_date ASC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting follow-up required cases: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get response time statistics
     * @return array
     */
    public function getResponseTimeStats() {
        $stats = [];
        
        try {
            // Average response time
            $sql = "SELECT AVG(TIME_TO_SEC(TIMEDIFF(ambulance_arrival_time, incident_date))/60) as avg_time,
                    MIN(TIME_TO_SEC(TIMEDIFF(ambulance_arrival_time, incident_date))/60) as min_time,
                    MAX(TIME_TO_SEC(TIMEDIFF(ambulance_arrival_time, incident_date))/60) as max_time
                    FROM {$this->table_name} 
                    WHERE ambulance_arrival_time IS NOT NULL";
            $result = $this->query($sql);
            
            if ($result) {
                $stats['average'] = round($result[0]['avg_time'] ?? 0, 2);
                $stats['minimum'] = round($result[0]['min_time'] ?? 0, 2);
                $stats['maximum'] = round($result[0]['max_time'] ?? 0, 2);
            }
            
        } catch (Exception $e) {
            error_log("Error getting response time stats: " . $e->getMessage());
        }
        
        return $stats;
    }
}
?>
