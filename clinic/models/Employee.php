<?php
/**
 * Employee Model
 * 
 * Manages employee data and operations within the clinic system.
 * Treat cm_patients as the primary record for clinic-related employee data.
 */

require_once __DIR__ . "/../core/BaseModel.php";

class Employee extends BaseModel {
    protected $table_name = 'cm_patients';
    protected $primary_key = 'patient_id';
    
    /**
     * Get employee statistics (from cm_patients where patient_type is Staff or Faculty)
     * @return array
     */
    public function getEmployeeStats() {
        $stats = [];
        
        try {
            // Total staff/faculty employees in clinic
            $sql = "SELECT COUNT(*) as count FROM {$this->table_name} WHERE patient_type IN ('Staff', 'Faculty')";
            $result = $this->query($sql);
            $stats['total_employees'] = $result[0]['count'] ?? 0;
            
            // Active employees
            $sql = "SELECT COUNT(*) as count FROM {$this->table_name} WHERE status = 'Active' AND patient_type IN ('Staff', 'Faculty')";
            $result = $this->query($sql);
            $stats['active_employees'] = $result[0]['count'] ?? 0;
            
            // Faculty count
            $sql = "SELECT COUNT(*) as count FROM {$this->table_name} WHERE patient_type = 'Faculty'";
            $result = $this->query($sql);
            $stats['faculty_count'] = $result[0]['count'] ?? 0;
            
            // Staff count
            $sql = "SELECT COUNT(*) as count FROM {$this->table_name} WHERE patient_type = 'Staff'";
            $result = $this->query($sql);
            $stats['staff_count'] = $result[0]['count'] ?? 0;
            
        } catch (Exception $e) {
            error_log("Error getting employee stats: " . $e->getMessage());
        }
        
        return $stats;
    }
    
    /**
     * Create or update clinic record for an employee
     * @param array $data - Employee data from HR system
     * @return bool
     */
    public function createEmployeeWithPatient($data) {
        $this->beginTransaction();
        
        try {
            $employee_id = $data['employee_id'];
            
            // Prepare patient record from employee data
            $patient_data = [
                'patient_id' => 'PAT' . $employee_id,
                'employee_id' => $employee_id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'middle_name' => $data['middle_name'] ?? '',
                'email' => $data['email'] ?? '',
                'phone' => $data['phone'] ?? '',
                'address' => $data['address'] ?? '',
                'birth_date' => $data['birth_date'] ?? '',
                'gender' => $data['gender'] ?? '',
                'blood_type' => $data['blood_type'] ?? '',
                'allergies' => $data['allergies'] ?? '',
                'medical_conditions' => $data['medical_conditions'] ?? '',
                'current_medications' => $data['current_medications'] ?? '',
                'emergency_contact_name' => $data['emergency_contact_name'] ?? '',
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? '',
                'patient_type' => 'Staff',
                'status' => 'Active'
            ];
            
            // Use ON DUPLICATE KEY UPDATE logic
            $sql = "INSERT INTO cm_patients (patient_id, employee_id, first_name, last_name, middle_name, email, phone, address, birth_date, gender, blood_type, allergies, medical_conditions, current_medications, emergency_contact_name, emergency_contact_phone, patient_type, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    first_name = VALUES(first_name),
                    last_name = VALUES(last_name),
                    email = VALUES(email),
                    phone = VALUES(phone),
                    medical_conditions = VALUES(medical_conditions),
                    current_medications = VALUES(current_medications),
                    emergency_contact_name = VALUES(emergency_contact_name),
                    emergency_contact_phone = VALUES(emergency_contact_phone)";
            
            $stmt = $this->db->prepare($sql);
            $values = array_values($patient_data);
            
            if (!$stmt->execute($values)) {
                throw new Exception("Failed to create/update patient record");
            }
            
            $this->commit();
            return true;
            
        } catch (Exception $e) {
            $this->rollback();
            error_log("Error creating employee with patient: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Search clinic employees
     * @param string $search - Search term
     * @return array
     */
    public function search($search) {
        $sql = "SELECT * FROM {$this->table_name} 
                WHERE (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR employee_id LIKE ?)
                AND patient_type IN ('Staff', 'Faculty')
                ORDER BY last_name ASC, first_name ASC";
        
        $search_term = "%{$search}%";
        $params = [$search_term, $search_term, $search_term, $search_term];
        
        $result = $this->query($sql, $params);
        return $result ?: [];
    }
    
    /**
     * Get employees with medical record count
     * @return array
     */
    public function getEmployeesWithMedicalCount() {
        $sql = "SELECT e.*, COUNT(p.record_id) as medical_records_count
                FROM {$this->table_name} e
                LEFT JOIN cm_medical_records p ON e.patient_id = p.patient_id
                WHERE e.patient_type IN ('Staff', 'Faculty')
                GROUP BY e.patient_id
                ORDER BY e.last_name ASC, e.first_name ASC";
        
        $result = $this->query($sql);
        return $result ?: [];
    }

    /**
     * Delete employee clinic record and associated patient data
     * @param int $employee_id
     * @return bool
     */
    public function deleteEmployeeAndPatient($employee_id) {
        try {
            $sql = "DELETE FROM {$this->table_name} WHERE employee_id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$employee_id]);
        } catch (Exception $e) {
            error_log("Error deleting employee clinic record: " . $e->getMessage());
            return false;
        }
    }
}
