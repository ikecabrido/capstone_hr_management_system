<?php
/**
 * MedicalRecord Model
 * 
 * Manages medical record data and operations
 */

require_once __DIR__ . "/../core/BaseModel.php";

class MedicalRecord extends BaseModel {
    protected $table_name = 'cm_medical_records';
    protected $primary_key = 'record_id';
    
    /**
     * Get medical statistics
     * @return array
     */
    public function getMedicalStats() {
        $stats = [];
        
        try {
            // Total visits
            $stats['total_visits'] = $this->count();
            
            // Today's visits
            $sql = "SELECT COUNT(*) as count FROM {$this->table_name} 
                    WHERE DATE(visit_date) = CURDATE()";
            $result = $this->query($sql);
            $stats['today_visits'] = $result[0]['count'] ?? 0;
            
            // Completed visits
            $stats['completed_visits'] = $this->count(['status' => 'Completed']);
            
            // Pending visits
            $stats['pending_visits'] = $this->count(['status' => 'Pending']);
            
            // Follow-up visits
            $stats['followup_visits'] = $this->count(['status' => 'Follow-up']);
            
            // This month visits
            $sql = "SELECT COUNT(*) as count FROM {$this->table_name} 
                    WHERE MONTH(visit_date) = MONTH(CURDATE()) 
                    AND YEAR(visit_date) = YEAR(CURDATE())";
            $result = $this->query($sql);
            $stats['this_month_visits'] = $result[0]['count'] ?? 0;
            
        } catch (Exception $e) {
            error_log("Error getting medical stats: " . $e->getMessage());
        }
        
        return $stats;
    }
    
    /**
     * Get records with patient details
     * @param array $conditions - WHERE conditions
     * @param string $order_by - ORDER BY clause
     * @param int $limit - LIMIT clause
     * @return array
     */
    public function getRecordsWithPatientDetails($conditions = [], $order_by = 'visit_date DESC', $limit = '') {
        $sql = "SELECT mr.*, p.first_name, p.last_name, p.patient_type, p.avatar,
                        CONCAT(p.first_name, ' ', p.last_name) as patient_name
                FROM {$this->table_name} mr
                INNER JOIN cm_patients p ON mr.patient_id = p.patient_id";
        
        $params = [];
        
        // Add WHERE conditions
        if (!empty($conditions)) {
            $where_clauses = [];
            foreach ($conditions as $field => $value) {
                $where_clauses[] = "mr.$field = ?";
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
            error_log("Error getting records with patient details: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get records by patient ID
     * @param string $patient_id - Patient ID
     * @return array
     */
    public function getByPatientId($patient_id) {
        return $this->getRecordsWithPatientDetails(['patient_id' => $patient_id], 'visit_date DESC');
    }
    
    /**
     * Get records by date range
     * @param string $start_date - Start date
     * @param string $end_date - End date
     * @return array
     */
    public function getByDateRange($start_date, $end_date) {
        $sql = "SELECT mr.*, p.first_name, p.last_name, p.patient_type
                FROM {$this->table_name} mr
                INNER JOIN cm_patients p ON mr.patient_id = p.patient_id
                WHERE DATE(mr.visit_date) BETWEEN ? AND ?
                ORDER BY mr.visit_date DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$start_date, $end_date]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting records by date range: " . $e->getMessage());
            return [];
        }
    }
    
    public function getMedicalRecordsByDateRange($start_date, $end_date) {
        $query = "SELECT mr.*, p.first_name, p.last_name FROM " . $this->table_name . " mr LEFT JOIN cm_patients p ON mr.patient_id = p.patient_id WHERE mr.visit_date BETWEEN :start_date AND :end_date ORDER BY mr.visit_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get records by consultation type
     * @param string $consultation_type - Consultation type
     * @return array
     */
    public function getByConsultationType($consultation_type) {
        return $this->getRecordsWithPatientDetails(['consultation_type' => $consultation_type]);
    }
    
    /**
     * Get records by status
     * @param string $status - Record status
     * @return array
     */
    public function getByStatus($status) {
        return $this->getRecordsWithPatientDetails(['status' => $status]);
    }
    
    /**
     * Get recent records
     * @param int $limit - Number of records to get
     * @return array
     */
    public function getRecentRecords($limit = 10) {
        return $this->getRecordsWithPatientDetails([], 'visit_date DESC', $limit);
    }
    
    /**
     * Get records requiring follow-up
     * @return array
     */
    public function getFollowUpRequired() {
        $sql = "SELECT mr.*, p.first_name, p.last_name, p.patient_type
                FROM {$this->table_name} mr
                INNER JOIN cm_patients p ON mr.patient_id = p.patient_id
                WHERE mr.follow_up_date IS NOT NULL 
                AND mr.follow_up_date <= CURDATE()
                AND mr.status != 'Completed'
                ORDER BY mr.follow_up_date ASC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting follow-up required records: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get visit trends
     * @param int $days - Number of days to analyze
     * @return array
     */
    public function getVisitTrends($days = 30) {
        $sql = "SELECT DATE(visit_date) as visit_date, COUNT(*) as visits
                FROM {$this->table_name}
                WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY DATE(visit_date)
                ORDER BY visit_date ASC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$days]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting visit trends: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get most common diagnoses
     * @param int $limit - Number of diagnoses to return
     * @return array
     */
    public function getCommonDiagnoses($limit = 10) {
        $sql = "SELECT diagnosis, COUNT(*) as count
                FROM {$this->table_name}
                WHERE diagnosis IS NOT NULL AND diagnosis != ''
                GROUP BY diagnosis
                ORDER BY count DESC
                LIMIT ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting common diagnoses: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create medical record with vital signs
     * @param array $data - Medical record data
     * @return bool
     */
    public function createWithVitalSigns($data) {
        $this->beginTransaction();
        
        try {
            // Create medical record
            $record_id = $data['record_id'];
            unset($data['record_id']);
            
            if (!$this->create($data)) {
                throw new Exception("Failed to create medical record");
            }
            
            // Create vital signs if provided
            if (isset($data['vital_signs']) && !empty($data['vital_signs'])) {
                $vital_signs = json_decode($data['vital_signs'], true);
                $vital_sign_id = 'VS' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
                
                $vital_sql = "INSERT INTO cm_vital_signs (vital_sign_id, record_id, blood_pressure_systolic, blood_pressure_diastolic, heart_rate, respiratory_rate, temperature, weight, height, oxygen_saturation, blood_sugar, recorded_by) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $vital_stmt = $this->db->prepare($vital_sql);
                
                $vital_values = [
                    $vital_sign_id,
                    $record_id,
                    $vital_signs['bp_systolic'] ?? null,
                    $vital_signs['bp_diastolic'] ?? null,
                    $vital_signs['heart_rate'] ?? null,
                    $vital_signs['respiratory_rate'] ?? null,
                    $vital_signs['temperature'] ?? null,
                    $vital_signs['weight'] ?? null,
                    $vital_signs['height'] ?? null,
                    $vital_signs['oxygen_saturation'] ?? null,
                    $vital_signs['blood_sugar'] ?? null,
                    $data['created_by'] ?? ''
                ];
                
                if (!$vital_stmt->execute($vital_values)) {
                    throw new Exception("Failed to create vital signs");
                }
            }
            
            $this->commit();
            return true;
            
        } catch (Exception $e) {
            $this->rollback();
            error_log("Error creating medical record with vital signs: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update record status
     * @param string $record_id - Record ID
     * @param string $status - New status
     * @return bool
     */
    public function updateStatus($record_id, $status) {
        return $this->update($record_id, ['status' => $status]);
    }
    
    /**
     * Validate medical record data
     * @param array $data - Medical record data
     * @return array - Validation errors
     */
    public function validateMedicalRecord($data) {
        $errors = [];
        
        // Required fields
        $required = ['patient_id', 'chief_complaint'];
        $errors = array_merge($errors, $this->validateRequired($data, $required));
        
        // Visit date validation
        if (!empty($data['visit_date'])) {
            $visit_date = DateTime::createFromFormat('Y-m-d H:i:s', $data['visit_date']);
            if (!$visit_date || $visit_date > new DateTime()) {
                $errors['visit_date'] = 'Invalid visit date';
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
     * Get records by physician
     * @param string $physician - Physician name
     * @return array
     */
    public function getByPhysician($physician) {
        return $this->getRecordsWithPatientDetails(['attending_physician' => $physician]);
    }
    
    /**
     * Get consultation statistics
     * @return array
     */
    public function getConsultationStats() {
        $stats = [];
        
        try {
            // By consultation type
            $sql = "SELECT consultation_type, COUNT(*) as count
                    FROM {$this->table_name}
                    GROUP BY consultation_type";
            $result = $this->query($sql);
            $stats['consultation_types'] = $result ?: [];
            
            // By status
            $sql = "SELECT status, COUNT(*) as count
                    FROM {$this->table_name}
                    GROUP BY status";
            $result = $this->query($sql);
            $stats['status_distribution'] = $result ?: [];
            
        } catch (Exception $e) {
            error_log("Error getting consultation stats: " . $e->getMessage());
        }
        
        return $stats;
    }
}
?>
