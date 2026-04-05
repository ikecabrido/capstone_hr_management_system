<?php
/**
 * Patient Model
 * 
 * Manages patient data and operations
 */

require_once __DIR__ . "/../core/BaseModel.php";

class Patient extends BaseModel {
    protected $table_name = 'cm_patients';
    protected $primary_key = 'patient_id';
    
    /**
     * Get patient statistics
     * @return array
     */
    public function getPatientStats() {
        $stats = [];
        
        try {
            // Total patients
            $stats['total_patients'] = $this->count();
            
            // Active patients
            $stats['active_patients'] = $this->count(['status' => 'Active']);
            
            // Staff patients
            $stats['staff_patients'] = $this->count(['patient_type' => 'Staff']);
            
            // Faculty patients
            $stats['faculty_patients'] = $this->count(['patient_type' => 'Faculty']);
            
            // Student patients
            $stats['student_patients'] = $this->count(['patient_type' => 'Student']);
            
            // Visitor patients
            $stats['visitor_patients'] = $this->count(['patient_type' => 'Visitor']);
            
        } catch (Exception $e) {
            error_log("Error getting patient stats: " . $e->getMessage());
        }
        
        return $stats;
    }
    
    /**
     * Get patients by type
     * @param string $type - Patient type
     * @return array
     */
    public function getByPatientType($type) {
        return $this->read(['patient_type' => $type], 'last_name ASC, first_name ASC');
    }
    
    /**
     * Get patients by status
     * @param string $status - Patient status
     * @return array
     */
    public function getByStatus($status) {
        return $this->read(['status' => $status], 'last_name ASC, first_name ASC');
    }
    
    /**
     * Get patients by employee ID
     * @param string $employee_id - Employee ID
     * @return array
     */
    public function getByEmployeeId($employee_id) {
        return $this->read(['employee_id' => $employee_id]);
    }
    
    /**
     * Update patient by employee ID
     * @param string $employee_id - Employee ID
     * @param array $data - Patient data
     * @return bool
     */
    public function updateByEmployeeId($employee_id, $data) {
        $sql = "UPDATE {$this->table_name} 
                SET first_name = ?, last_name = ?, middle_name = ?, email = ?, phone = ?, 
                    address = ?, birth_date = ?, gender = ?, blood_type = ?, allergies = ?
                WHERE employee_id = ?";
        
        $values = [
            $data['first_name'],
            $data['last_name'],
            $data['middle_name'] ?? '',
            $data['email'] ?? '',
            $data['phone'] ?? '',
            $data['address'] ?? '',
            $data['birth_date'] ?? '',
            $data['gender'] ?? '',
            $data['blood_type'] ?? '',
            $data['allergies'] ?? '',
            $employee_id
        ];
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log("Error updating patient by employee ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Search patients by name or email
     * @param string $search - Search term
     * @return array
     */
    public function search($search) {
        $sql = "SELECT * FROM {$this->table_name} 
                WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ? 
                ORDER BY last_name ASC, first_name ASC";
        
        $search_term = "%{$search}%";
        $params = [$search_term, $search_term, $search_term];
        
        $result = $this->query($sql, $params);
        return $result ?: [];
    }
    
    /**
     * Get patients with medical record count
     * @return array
     */
    public function getPatientsWithMedicalCount() {
        $sql = "SELECT p.*, COUNT(mr.record_id) as medical_records_count,
                       MAX(mr.visit_date) as last_visit_date
                FROM {$this->table_name} p
                LEFT JOIN cm_medical_records mr ON p.patient_id = mr.patient_id
                GROUP BY p.patient_id
                ORDER BY p.last_name ASC, p.first_name ASC";
        
        $result = $this->query($sql);
        return $result ?: [];
    }
    
    /**
     * Get active patients for autocomplete
     * @param string $term - Search term
     * @param int $limit - Limit results
     * @return array
     */
    public function getActivePatientsForAutocomplete($term = '', $limit = 10) {
        $sql = "SELECT patient_id, CONCAT(first_name, ' ', last_name) as full_name, patient_type
                FROM {$this->table_name} 
                WHERE status = 'Active'";
        
        $params = [];
        
        if (!empty($term)) {
            $sql .= " AND (first_name LIKE ? OR last_name LIKE ?)";
            $search_term = "%{$term}%";
            $params = [$search_term, $search_term];
        }
        
        $sql .= " ORDER BY last_name ASC, first_name ASC LIMIT ?";
        $params[] = $limit;
        
        $result = $this->query($sql, $params);
        return $result ?: [];
    }
    
    /**
     * Get patients with recent visits
     * @param int $days - Number of days to look back
     * @return array
     */
    public function getPatientsWithRecentVisits($days = 30) {
        $sql = "SELECT p.*, mr.visit_date, mr.chief_complaint
                FROM {$this->table_name} p
                INNER JOIN cm_medical_records mr ON p.patient_id = mr.patient_id
                WHERE mr.visit_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                ORDER BY mr.visit_date DESC
                LIMIT 50";
        
        $result = $this->query($sql, [$days]);
        return $result ?: [];
    }
    
    /**
     * Validate patient data
     * @param array $data - Patient data
     * @return array - Validation errors
     */
    public function validatePatient($data) {
        $errors = [];
        
        // Required fields
        $required = ['first_name', 'last_name'];
        $errors = array_merge($errors, $this->validateRequired($data, $required));
        
        // Email validation
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        // Phone validation (basic)
        if (!empty($data['phone']) && !preg_match('/^[0-9\-\+\(\)\s]+$/', $data['phone'])) {
            $errors['phone'] = 'Invalid phone format';
        }
        
        // Birth date validation
        if (!empty($data['birth_date'])) {
            $birth_date = DateTime::createFromFormat('Y-m-d', $data['birth_date']);
            if (!$birth_date || $birth_date > new DateTime()) {
                $errors['birth_date'] = 'Invalid birth date';
            }
        }
        
        return $errors;
    }
    
    /**
     * Check if patient exists by employee ID
     * @param string $employee_id - Employee ID
     * @return bool
     */
    public function existsByEmployeeId($employee_id) {
        $count = $this->count(['employee_id' => $employee_id]);
        return $count > 0;
    }
    
    /**
     * Get patient demographics
     * @return array
     */
    public function getDemographics() {
        $demographics = [];
        
        try {
            // Gender distribution
            $sql = "SELECT gender, COUNT(*) as count 
                    FROM {$this->table_name} 
                    WHERE gender IS NOT NULL 
                    GROUP BY gender";
            $result = $this->query($sql);
            $demographics['gender'] = $result ?: [];
            
            // Patient type distribution
            $sql = "SELECT patient_type, COUNT(*) as count 
                    FROM {$this->table_name} 
                    WHERE patient_type IS NOT NULL 
                    GROUP BY patient_type";
            $result = $this->query($sql);
            $demographics['patient_type'] = $result ?: [];
            
            // Age distribution
            $sql = "SELECT 
                    CASE 
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 18 THEN 'Under 18'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 25 THEN '18-25'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 26 AND 35 THEN '26-35'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 36 AND 45 THEN '36-45'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 46 AND 55 THEN '46-55'
                        ELSE 'Over 55'
                    END as age_group,
                    COUNT(*) as count
                    FROM {$this->table_name} 
                    WHERE birth_date IS NOT NULL
                    GROUP BY age_group
                    ORDER BY age_group";
            $result = $this->query($sql);
            $demographics['age_groups'] = $result ?: [];
            
        } catch (Exception $e) {
            error_log("Error getting patient demographics: " . $e->getMessage());
        }
        
        return $demographics;
    }
}
?>
