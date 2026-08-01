<?php
/**
 * Leave Management Controller
 * 
 * Handles all server-side processing for leave management including:
 * - Fetching leave requests
 * - Processing leave approvals/rejections
 * - Managing eligibility checklist data
 * - Philippine labor law validation
 */

require_once "../auth/database.php";

class LeaveManagementController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Initialize database tables
     */
    public function initializeDatabase() {
        $createTableSQL = "
        CREATE TABLE IF NOT EXISTS leave_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            employee_id INT NOT NULL,
            leave_type VARCHAR(50) NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            total_days DECIMAL(5,2) NOT NULL,
            reason TEXT,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            checked_by INT NULL,
            checked_at TIMESTAMP NULL,
            hr_comments TEXT,
            checklist_data TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_employee_id (employee_id),
            INDEX idx_status (status),
            INDEX idx_leave_type (leave_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        
        $this->db->exec($createTableSQL);
        
        // Add checklist_data column if it doesn't exist
        try {
            $checkColumn = "SHOW COLUMNS FROM lc_leave_requests LIKE 'checklist_data'";
            $stmt = $this->db->query($checkColumn);
            if ($stmt->rowCount() === 0) {
                $addColumn = "ALTER TABLE leave_requests ADD COLUMN checklist_data TEXT";
                $this->db->exec($addColumn);
            }
        } catch (PDOException $e) {
            error_log('Error adding checklist_data column: ' . $e->getMessage());
        }
        
        // Create leave_documents table
        $createDocsTable = "
        CREATE TABLE IF NOT EXISTS leave_documents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            leave_id INT NOT NULL,
            document_type VARCHAR(100) NOT NULL,
            file_path VARCHAR(255) NOT NULL,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        
        try {
            $this->db->exec($createDocsTable);
            
            // Check if leave_id column exists, add if not
            $checkColumn = "SHOW COLUMNS FROM lc_leave_documents LIKE 'leave_id'";
            $stmt = $this->db->query($checkColumn);
            if (!$stmt->fetch()) {
                // Add the column
                $addColumn = "ALTER TABLE leave_documents ADD COLUMN leave_id INT NOT NULL DEFAULT 0 AFTER id";
                $this->db->exec($addColumn);
            }
        } catch (PDOException $e) {
            error_log('Error creating leave_documents table: ' . $e->getMessage());
        }
        
        // Add gender column to employees table if it doesn't exist
        $this->addGenderColumnToEmployees();
        
        // Insert sample data if table is empty
        $this->insertSampleDataIfEmpty();
        
        // Ensure sample documents exist (for existing installations)
        $this->ensureSampleDocuments();
    }
    
    /**
     * Add gender column to employees table if it doesn't exist
     */
    private function addGenderColumnToEmployees() {
        try {
            // Check if gender column exists
            $checkColumn = "SHOW COLUMNS FROM employees LIKE 'gender'";
            $stmt = $this->db->query($checkColumn);
            $columnExists = $stmt->fetch();
            
            if (!$columnExists) {
                // Add gender column
                $addColumn = "ALTER TABLE employees ADD COLUMN gender VARCHAR(20) DEFAULT NULL";
                $this->db->exec($addColumn);
            }
            
            // Check if marital_status column exists
            $checkMarital = "SHOW COLUMNS FROM employees LIKE 'marital_status'";
            $stmt = $this->db->query($checkMarital);
            $maritalExists = $stmt->fetch();
            
            if (!$maritalExists) {
                // Add marital_status column
                $addMarital = "ALTER TABLE employees ADD COLUMN marital_status VARCHAR(20) DEFAULT NULL";
                $this->db->exec($addMarital);
            }
            
            // Also ensure hire_date/date_hired consistency if needed
            $checkHireDate = "SHOW COLUMNS FROM employees LIKE 'date_hired'";
            $stmt = $this->db->query($checkHireDate);
            if (!$stmt->fetch()) {
                $addHireDate = "ALTER TABLE employees ADD COLUMN date_hired DATE DEFAULT NULL";
                $this->db->exec($addHireDate);
            }
            
            // Update gender data if needed
            $this->updateEmployeeGenders();
            
        } catch (PDOException $e) {
            error_log('Error adding columns: ' . $e->getMessage());
        }
    }
    
    /**
     * Update employee genders based on known names in sample data
     */
    private function updateEmployeeGenders() {
        // Known female names
        $femaleNames = ['niki', 'ana', 'patricia', 'maria', 'sophia', 'angela', 'diane'];
        // Known male names
        $maleNames = ['mark', 'john', 'lance', 'jose', 'brian', 'pedro'];
        
        try {
            // Get all employees
            $query = "SELECT employee_id, LOWER(full_name) as full_name FROM employees";
            $stmt = $this->db->query($query);
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($employees as $emp) {
                $firstName = strtolower(explode(' ', trim($emp['full_name']))[0]);
                $gender = null;
                
                if (in_array($firstName, $femaleNames)) {
                    $gender = 'Female';
                } elseif (in_array($firstName, $maleNames)) {
                    $gender = 'Male';
                }
                
                if ($gender) {
                    $update = "UPDATE employees SET gender = ? WHERE employee_id = ?";
                    $stmt = $this->db->prepare($update);
                    $stmt->execute([$gender, $emp['employee_id']]);
                }
            }
        } catch (PDOException $e) {
            error_log('Error updating employee genders: ' . $e->getMessage());
        }
    }
    
    /**
     * Insert sample leave requests for demonstration
     */
    private function insertSampleDataIfEmpty() {
        $checkData = "SELECT COUNT(*) as count FROM lc_leave_requests";
        $stmt = $this->db->query($checkData);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] == 0) {
            $sampleData = "
            INSERT INTO lc_leave_requests (employee_id, leave_type, start_date, end_date, total_days, reason, status, checklist_data) VALUES
            (1, 'Maternity Leave', '2026-04-01', '2026-07-15', 105, 'Pregnancy with expected delivery date July 2026 - Required for maternity coverage under RA 11210', 'pending', NULL),
            (2, 'Paternity Leave', '2026-03-20', '2026-03-26', 7, 'Wife Maria Santos scheduled for normal delivery on March 22, 2026', 'pending', NULL),
            (3, 'Sick Leave', '2026-03-10', '2026-03-11', 2, 'Medical certificate attached - Upper respiratory infection', 'pending', NULL),
            (4, 'Vacation Leave', '2026-04-10', '2026-04-15', 6, 'Family vacation to Palawan - filed 2 weeks in advance', 'pending', NULL),
            (5, 'Bereavement Leave', '2026-03-05', '2026-03-07', 3, 'Death of father - death certificate attached', 'pending', NULL),
            (6, 'Emergency Leave', '2026-03-12', '2026-03-12', 1, 'Immediate family medical emergency - hospitalization', 'pending', NULL),
            (1, 'Sick Leave', '2026-02-10', '2026-02-12', 3, 'Flu with fever', 'approved', '{\"requirements\":{\"medical_cert\":true,\"leave_credits\":true,\"reason_stated\":true},\"hrChecks\":{\"days_reasonable\":true,\"medical_proof\":true,\"not_abused\":true}}'),
            (2, 'Vacation Leave', '2026-02-15', '2026-02-18', 4, 'Personal matter', 'approved', '{\"requirements\":{\"leave_credits\":true,\"filed_advance\":true},\"hrChecks\":{\"no_schedule_conflict\":true,\"enough_balance\":true,\"coverage_arranged\":true}}'),
            (3, 'Emergency Leave', '2026-02-20', '2026-02-20', 1, 'Power outage at home', 'rejected', '{\"requirements\":{\"valid_reason\":true,\"supporting_explanation\":false},\"hrChecks\":{\"urgency_justified\":false,\"not_abused\":true,\"documents_if_applicable\":false}}')
            ";
            $this->db->exec($sampleData);
            
            // Insert sample documents
            $this->insertSampleDocuments();
        }
    }
    
    /**
     * Insert sample documents for demonstration
     */
    private function insertSampleDocuments() {
        // Create upload directory if not exists
        $uploadDir = __DIR__ . '/../uploads/leave_documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Sample documents based on user's table:
        // ID 3 - Mark Lee - Sick Leave (needs Medical Certificate)
        // ID 4 - John Rey - Vacation Leave (no documents needed)
        // ID 5 - Niki Zepanya - Bereavement Leave (needs Death Certificate)
        // ID 6 - Ana Cruz - Emergency Leave (no documents needed)
        // ID 7 - Niki Zepanya - Sick Leave (needs Medical Certificate)
        // ID 8 - Ana Cruz - Vacation Leave (no documents needed)
        $sampleDocs = [
            // Sick Leave (ID 3) - Mark Lee - Medical Certificate
            [
                'leave_id' => 3,
                'document_type' => 'Medical Certificate',
                'file_path' => 'uploads/leave_documents/sick_medical_cert_3.pdf'
            ],
            // Bereavement Leave (ID 5) - Niki Zepanya - Death Certificate
            [
                'leave_id' => 5,
                'document_type' => 'Death Certificate',
                'file_path' => 'uploads/leave_documents/bereavement_death_cert_5.pdf'
            ],
            // Sick Leave (ID 7) - Niki Zepanya - Medical Certificate
            [
                'leave_id' => 7,
                'document_type' => 'Medical Certificate',
                'file_path' => 'uploads/leave_documents/sick_medical_cert_7.pdf'
            ],
        ];
        
        // Create sample PDF files (placeholder)
        foreach ($sampleDocs as $doc) {
            $filePath = __DIR__ . '/../' . $doc['file_path'];
            if (!file_exists($filePath)) {
                // Create a simple PDF placeholder
                $pdfContent = "%PDF-1.4
1 0 obj
<< /Type /Catalog /Pages 2 0 R >>
endobj
2 0 obj
<< /Type /Pages /Kids [3 0 R] /Count 1 >>
endobj
3 0 obj
<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << >> >>
endobj
4 0 obj
<< /Length 44 >>
stream
BT
/F1 12 Tf
100 700 Td
(Sample Document - {$doc['document_type']}) Tj
ET
endstream
endobj
xref
0 5
0000000000 65535 f 
0000000009 00000 n 
0000000058 00000 n 
0000000115 00000 n 
0000000214 00000 n 
trailer
<< /Size 5 /Root 1 0 R >>
startxref
307
%%EOF";
                file_put_contents($filePath, $pdfContent);
            }
            
            // INSERT INTO lc_database
            $insertDoc = "INSERT INTO lc_leave_documents (leave_id, document_type, file_path, uploaded_at) 
                          VALUES (:leave_id, :doc_type, :file_path, NOW())";
            $stmt = $this->db->prepare($insertDoc);
            $stmt->execute([
                ':leave_id' => $doc['leave_id'],
                ':doc_type' => $doc['document_type'],
                ':file_path' => $doc['file_path']
            ]);
        }
    }
    
    /**
     * Ensure sample documents exist for existing installations
     * This is called on every page load to ensure documents exist
     */
    private function ensureSampleDocuments() {
        // Check if leave_documents table has any records
        $checkDocs = "SELECT COUNT(*) as count FROM lc_leave_documents";
        $stmt = $this->db->query($checkDocs);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] == 0) {
            // No documents exist, call insertSampleDocuments
            $this->insertSampleDocuments();
        }
        
        // Also create the uploads directory if it doesn't exist
        $uploadDir = __DIR__ . '/../uploads/leave_documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
    }
    
    /**
     * Get all leave requests with optional status filter
     */
    public function getLeaveRequests($status = null) {
        $query = "
            SELECT lr.*, e.full_name as first_name, '' as last_name
            FROM lc_leave_requests lr
            LEFT JOIN employees e ON lr.employee_id = e.employee_id
        ";
        
        if ($status && $status !== 'all') {
            $query .= " WHERE lr.status = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$status]);
        } else {
            $stmt = $this->db->query($query);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get leave statistics
     */
    public function getStatistics() {
        $query = "
            SELECT 
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                COUNT(*) as total
            FROM lc_leave_requests
        ";
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get detailed leave request by ID
     */
    public function getLeaveDetails($leaveId) {
        // First get basic leave request info
        $query = "SELECT * FROM lc_leave_requests WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$leaveId]);
        $leave = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$leave) {
            return null;
        }
        
        // Try to get employee info if employees table exists
        try {
            $empQuery = "SELECT full_name as first_name, '' as last_name, gender, marital_status as civil_status, date_hired as hire_date 
                        FROM employees WHERE employee_id = ?";
            $stmt = $this->db->prepare($empQuery);
            $stmt->execute([$leave['employee_id']]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($employee) {
                $leave = array_merge($leave, $employee);
                
                // Calculate service duration
                if ($employee['hire_date']) {
                    $hireDate = new DateTime($employee['hire_date']);
                    $now = new DateTime();
                    $serviceYears = $hireDate->diff($now)->y;
                    $leave['service_years'] = $serviceYears;
                }
            }
        } catch (PDOException $e) {
            // Employees table might not exist or have different structure
            error_log('Error fetching employee: ' . $e->getMessage());
        }
        
        return $leave;
    }
    
    /**
     * Update leave request status (approve/reject)
     */
    public function updateLeaveStatus($leaveId, $status, $hrComments = '', $hrId = null, $checklistData = null) {
        // Update query to include checklist_data
        $query = "UPDATE lc_leave_requests SET status = ?, checked_by = ?, checked_at = NOW(), hr_comments = ?, checklist_data = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        
        try {
            $stmt->execute([$status, $hrId, $hrComments, $checklistData, $leaveId]);
            return ['success' => true, 'message' => 'Leave request updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error updating leave request: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get employee data for checklist
     */
    public function getEmployeeChecklistData($employeeId, $leaveType) {
        $result = [
            'employee' => null,
            'leave_balance' => null
        ];
        
        // Try to get employee data
        try {
            $query = "SELECT * FROM employees WHERE employee_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$employeeId]);
            $result['employee'] = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error fetching employee: ' . $e->getMessage());
        }
        
        // Try to get leave balance
        try {
            $balanceQuery = "SELECT * FROM lc_leave_balances WHERE employee_id = ? AND leave_type = ?";
            $stmt = $this->db->prepare($balanceQuery);
            $stmt->execute([$employeeId, $leaveType]);
            $result['leave_balance'] = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error fetching leave balance: ' . $e->getMessage());
        }
        
        return $result;
    }
    
    /**
     * Check if employee is female
     * 
     * @param int $employeeId The employee ID to check
     * @return bool True if employee is female, false otherwise
     */
    public function isFemaleEmployee($employeeId) {
        try {
            // First try with gender column
            $query = "SELECT gender FROM employees WHERE employee_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$employeeId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && isset($result['gender']) && $result['gender']) {
                return strtolower($result['gender']) === 'female';
            }
            
            // Fallback: Try to guess FROM full_name
            $nameQuery = "SELECT full_name FROM employees WHERE employee_id = ?";
            $stmt = $this->db->prepare($nameQuery);
            $stmt->execute([$employeeId]);
            $nameResult = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($nameResult) {
                $firstName = strtolower(explode(' ', trim($nameResult['full_name']))[0]);
                $femaleNames = ['niki', 'ana', 'patricia', 'maria', 'sophia', 'angela', 'diane', 'jennifer', 'jessica', 'sarah', 'emily'];
                return in_array($firstName, $femaleNames);
            }
            
            return false;
        } catch (PDOException $e) {
            error_log('Error checking employee gender: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if employee is male
     * 
     * @param int $employeeId The employee ID to check
     * @return bool True if employee is male, false otherwise
     */
    public function isMaleEmployee($employeeId) {
        try {
            // First try with gender column
            $query = "SELECT gender FROM employees WHERE employee_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$employeeId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && isset($result['gender']) && $result['gender']) {
                return strtolower($result['gender']) === 'male';
            }
            
            // Fallback: Try to guess FROM full_name
            $nameQuery = "SELECT full_name FROM employees WHERE employee_id = ?";
            $stmt = $this->db->prepare($nameQuery);
            $stmt->execute([$employeeId]);
            $nameResult = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($nameResult) {
                $firstName = strtolower(explode(' ', trim($nameResult['full_name']))[0]);
                $maleNames = ['mark', 'john', 'lance', 'jose', 'brian', 'pedro', 'james', 'michael', 'david', 'richard'];
                return in_array($firstName, $maleNames);
            }
            
            return false;
        } catch (PDOException $e) {
            error_log('Error checking employee gender: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if employee is married
     * 
     * @param int $employeeId The employee ID to check
     * @return bool True if employee is married, false otherwise
     */
    public function isMarriedEmployee($employeeId) {
        try {
            // Check for marital_status column
            $columns = ['marital_status', 'civil_status', 'marital'];
            
            foreach ($columns as $column) {
                try {
                    $query = "SELECT $column FROM employees WHERE employee_id = ?";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute([$employeeId]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($result && isset($result[$column]) && $result[$column]) {
                        return strtolower($result[$column]) === 'married';
                    }
                } catch (PDOException $e) {
                    continue;
                }
            }
            
            // Default: Assume married for Paternity Leave eligibility
            // (HR can verify manually)
            return false;
        } catch (Exception $e) {
            error_log('Error checking employee marital status: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if employee has completed minimum service duration
     * 
     * @param int $employeeId The employee ID to check
     * @param int $months Minimum months required (default 6)
     * @return bool True if employee has completed minimum service, false otherwise
     */
    public function hasMinimumServiceDuration($employeeId, $months = 6) {
        try {
            $query = "SELECT date_hired FROM employees WHERE employee_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$employeeId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && $result['date_hired']) {
                $hireDate = new DateTime($result['date_hired']);
                $now = new DateTime();
                $interval = $hireDate->diff($now);
                $monthsOfService = ($interval->y * 12) + $interval->m;
                
                return $monthsOfService >= $months;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log('Error checking service duration: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get eligibility checklist configuration for a leave type
     * Returns the dynamic checklist based on Philippine labor laws
     */
    public function getEligibilityChecklist($leaveType) {
        $checklists = [
            'Maternity Leave' => [
                'title' => 'Maternity Leave Eligibility (RA 11210 - Magna Carta of Women)',
                'icon' => 'fa-baby',
                'color' => 'pink',
                'requirements' => [
                    ['id' => 'pregnancy_proof', 'label' => 'Proof of pregnancy (medical certificate)', 'required' => true],
                    ['id' => 'delivery_date', 'label' => 'Expected delivery date provided', 'required' => true],
                    ['id' => 'sss_eligibility', 'label' => 'SSS maternity benefit eligibility verified', 'required' => true],
                    ['id' => 'leave_duration', 'label' => 'Leave duration within allowed days (105 days)', 'required' => true],
                    ['id' => 'documents_complete', 'label' => 'All required documents submitted', 'required' => true],
                    ['id' => 'not_exceeding', 'label' => 'Employee is not exceeding allowed benefits', 'required' => true],
                    ['id' => 'first_claim', 'label' => 'First/second claim verification (for SSS)', 'required' => false]
                ],
                'legalRef' => 'RA 11210 - Expanded Maternity Leave Law (105 days with full pay)'
            ],
            'Paternity Leave' => [
                'title' => 'Paternity Leave Eligibility (RA 8187 - Paternity Leave Act)',
                'icon' => 'fa-baby-carriage',
                'color' => 'blue',
                'requirements' => [
                    ['id' => 'marriage_proof', 'label' => 'Legally married to partner', 'required' => true],
                    ['id' => 'delivery_proof', 'label' => "Wife's delivery proof provided", 'required' => true],
                    ['id' => 'childbirth_claims', 'label' => 'Within allowed number of childbirth claims (4)', 'required' => true],
                    ['id' => 'service_duration', 'label' => 'Employment duration requirement met', 'required' => false],
                    ['id' => 'valid_marriage', 'label' => 'Valid marriage record on file', 'required' => true],
                    ['id' => 'documents_submitted', 'label' => 'Supporting documents submitted', 'required' => true],
                    ['id' => 'within_entitlement', 'label' => 'Within leave entitlement (7 days normal, 15 days cesarean)', 'required' => true]
                ],
                'legalRef' => 'RA 8187 - Paternity Leave Act (7 days for normal delivery, 15 days for cesarean)'
            ],
            'Sick Leave' => [
                'title' => 'Sick Leave Eligibility',
                'icon' => 'fa-user-nurse',
                'color' => 'red',
                'requirements' => [
                    ['id' => 'medical_cert', 'label' => 'Medical certificate attached (if required)', 'required' => true],
                    ['id' => 'leave_credits', 'label' => 'Leave credits available', 'required' => true],
                    ['id' => 'reason_stated', 'label' => 'Proper reason stated', 'required' => true],
                    ['id' => 'paid_leave_check', 'label' => 'Has paid leave days remaining', 'required' => true],
                    ['id' => 'days_reasonable', 'label' => 'Number of days reasonable for illness', 'required' => true],
                    ['id' => 'medical_proof', 'label' => 'Medical proof attached (for 2+ days)', 'required' => false],
                    ['id' => 'not_abused', 'label' => 'No pattern of abuse detected', 'required' => true]
                ],
                'legalRef' => 'Company policy and Labor Code of the Philippines'
            ],
            'Vacation Leave' => [
                'title' => 'Vacation Leave Eligibility',
                'icon' => 'fa-umbrella-beach',
                'color' => 'teal',
                'requirements' => [
                    ['id' => 'leave_credits', 'label' => 'Leave credits available', 'required' => true],
                    ['id' => 'filed_advance', 'label' => 'Filed at least 30 days in advance', 'required' => true],
                    ['id' => 'paid_leave_check', 'label' => 'Has paid leave days remaining', 'required' => true],
                    ['id' => 'no_schedule_conflict', 'label' => 'No conflict with department schedule', 'required' => true],
                    ['id' => 'enough_balance', 'label' => 'Enough leave balance for duration', 'required' => true],
                    ['id' => 'coverage_arranged', 'label' => 'Work coverage arranged', 'required' => false]
                ],
                'legalRef' => 'Company policy and Labor Code of the Philippines'
            ],
            'Bereavement Leave' => [
                'title' => 'Bereavement Leave Eligibility',
                'icon' => 'fa-dove',
                'color' => 'gray',
                'requirements' => [
                    ['id' => 'death_proof', 'label' => 'Proof of death (death certificate)', 'required' => true],
                    ['id' => 'relationship', 'label' => 'Relationship to deceased (immediate family)', 'required' => true],
                    ['id' => 'paid_leave_check', 'label' => 'Has paid leave days remaining', 'required' => true],
                    ['id' => 'within_days', 'label' => 'Within allowed number of days (3-5 days)', 'required' => true],
                    ['id' => 'valid_relationship', 'label' => 'Valid relationship verified', 'required' => true],
                    ['id' => 'documents_on_file', 'label' => 'Death certificate on file', 'required' => true]
                ],
                'legalRef' => 'Company policy - typically 3-5 days for immediate family'
            ],
            'Emergency Leave' => [
                'title' => 'Emergency Leave Eligibility',
                'icon' => 'fa-exclamation-triangle',
                'color' => 'orange',
                'requirements' => [
                    ['id' => 'valid_reason', 'label' => 'Valid emergency reason', 'required' => true],
                    ['id' => 'supporting_explanation', 'label' => 'Supporting explanation provided', 'required' => true],
                    ['id' => 'paid_leave_check', 'label' => 'Has paid leave days remaining', 'required' => true],
                    ['id' => 'urgency_justified', 'label' => 'Urgency is justified', 'required' => true],
                    ['id' => 'not_abused', 'label' => 'Not abused (check frequency of use)', 'required' => true],
                    ['id' => 'documents_if_applicable', 'label' => 'Supporting documents (if applicable)', 'required' => false]
                ],
                'legalRef' => 'Company policy - subject to HR discretion'
            ]
        ];
        
        return $checklists[$leaveType] ?? null;
    }
    
    /**
     * Get all available leave types
     */
    public function getLeaveTypes() {
        return [
            'Maternity Leave',
            'Paternity Leave',
            'Sick Leave',
            'Vacation Leave',
            'Bereavement Leave',
            'Emergency Leave'
        ];
    }
    
    /**
     * Handle AJAX requests
     */
    public function handleAjaxRequest() {
        if (!isset($_GET['action'])) {
            return null;
        }
        
        header('Content-Type: application/json');
        
        // Get leave request details
        if ($_GET['action'] === 'get_leave_details' && isset($_GET['id'])) {
            $leaveId = intval($_GET['id']);
            $leave = $this->getLeaveDetails($leaveId);
            echo json_encode($leave);
            exit;
        }
        
        // Update leave status
        if ($_GET['action'] === 'update_status' && isset($_POST['leave_id']) && isset($_POST['status'])) {
            $leaveId = intval($_POST['leave_id']);
            $newStatus = $_POST['status'];
            $hrComments = $_POST['comments'] ?? '';
            // Try different session structures
            $hrId = $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? 1;
            $checklistData = $_POST['checklist_data'] ?? null;
            
            $result = $this->updateLeaveStatus($leaveId, $newStatus, $hrComments, $hrId, $checklistData);
            echo json_encode($result);
            exit;
        }
        
        // Get employee checklist data
        if ($_GET['action'] === 'get_employee_checklist_data' && isset($_GET['employee_id']) && isset($_GET['leave_type'])) {
            $employeeId = intval($_GET['employee_id']);
            $leaveType = $_GET['leave_type'];
            
            $data = $this->getEmployeeChecklistData($employeeId, $leaveType);
            echo json_encode($data);
            exit;
        }
        
        // Get eligibility checklist
        if ($_GET['action'] === 'get_eligibility_checklist' && isset($_GET['leave_type'])) {
            $leaveType = $_GET['leave_type'];
            $checklist = $this->getEligibilityChecklist($leaveType);
            echo json_encode($checklist);
            exit;
        }
        
        // Get documents for a leave request
        if ($_GET['action'] === 'get_documents' && isset($_GET['leave_id'])) {
            $leaveId = intval($_GET['leave_id']);
            $documents = $this->getLeaveDocuments($leaveId);
            echo json_encode($documents);
            exit;
        }
        
        // Check if employee is female
        if ($_GET['action'] === 'check_female' && isset($_GET['employee_id'])) {
            $employeeId = intval($_GET['employee_id']);
            $isFemale = $this->isFemaleEmployee($employeeId);
            echo json_encode(['is_female' => $isFemale]);
            exit;
        }
        
        // Check if employee is male
        if ($_GET['action'] === 'check_male' && isset($_GET['employee_id'])) {
            $employeeId = intval($_GET['employee_id']);
            $isMale = $this->isMaleEmployee($employeeId);
            echo json_encode(['is_male' => $isMale]);
            exit;
        }
        
        // Check if employee is married
        if ($_GET['action'] === 'check_married' && isset($_GET['employee_id'])) {
            $employeeId = intval($_GET['employee_id']);
            $isMarried = $this->isMarriedEmployee($employeeId);
            echo json_encode(['is_married' => $isMarried]);
            exit;
        }
        
        // Check if employee has minimum service duration
        if ($_GET['action'] === 'check_service_duration' && isset($_GET['employee_id'])) {
            $employeeId = intval($_GET['employee_id']);
            $months = isset($_GET['months']) ? intval($_GET['months']) : 6;
            $hasMinService = $this->hasMinimumServiceDuration($employeeId, $months);
            echo json_encode(['has_minimum_service' => $hasMinService, 'months_required' => $months]);
            exit;
        }
        
        // Check if employee has paid leave days left
        if ($_GET['action'] === 'check_paid_leave' && isset($_GET['employee_id']) && isset($_GET['leave_type'])) {
            $employeeId = intval($_GET['employee_id']);
            $leaveType = $_GET['leave_type'];
            $requestedDays = isset($_GET['requested_days']) ? floatval($_GET['requested_days']) : 1;
            $result = $this->hasPaidLeaveDaysLeft($employeeId, $leaveType, $requestedDays);
            echo json_encode($result);
            exit;
        }
        
        // Check if leave was filed at least 30 days in advance
        if ($_GET['action'] === 'check_filed_advance' && isset($_GET['leave_id'])) {
            $leaveId = intval($_GET['leave_id']);
            $result = $this->wasFiledInAdvance($leaveId);
            echo json_encode($result);
            exit;
        }
        
        // Upload document
        if ($_GET['action'] === 'upload_document' && isset($_POST['leave_id']) && isset($_POST['document_type'])) {
            $leaveId = intval($_POST['leave_id']);
            $documentType = $_POST['document_type'];
            $result = $this->uploadDocument($leaveId, $documentType);
            echo json_encode($result);
            exit;
        }
        
        return null;
    }
    
    /**
     * Check if employee has paid leave days left
     * This check is NOT applied to Maternity Leave and Paternity Leave
     * 
     * @param int $employeeId The employee ID to check
     * @param string $leaveType The type of leave being requested
     * @param float $requestedDays Number of days being requested
     * @return array Contains 'has_paid_leave' (bool) and 'remaining_days' (int)
     */
    public function hasPaidLeaveDaysLeft($employeeId, $leaveType, $requestedDays = 1) {
        // Skip check for Maternity Leave and Paternity Leave
        if (in_array($leaveType, ['Maternity Leave', 'Paternity Leave'])) {
            return [
                'has_paid_leave' => true,
                'remaining_days' => 999,
                'message' => 'Not applicable for ' . $leaveType
            ];
        }
        
        try {
            // Check if leave_balances table exists
            $tableCheck = "SHOW TABLES LIKE 'leave_balances'";
            $stmt = $this->db->query($tableCheck);
            $tableExists = $stmt->rowCount() > 0;
            
            if ($tableExists) {
                // Get leave balance FROM lc_leave_balances table
                $balanceQuery = "SELECT 
                    COALESCE(SUM(remaining_days), 0) as total_remaining
                    FROM lc_leave_balances 
                    WHERE employee_id = ? 
                    AND leave_type = ?";
                $stmt = $this->db->prepare($balanceQuery);
                $stmt->execute([$employeeId, $leaveType]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $remainingDays = (float)($result['total_remaining'] ?? 0);
                $hasPaidLeave = $remainingDays >= $requestedDays;
                
                return [
                    'has_paid_leave' => $hasPaidLeave,
                    'remaining_days' => (int)$remainingDays,
                    'message' => $hasPaidLeave 
                        ? "Employee has {$remainingDays} paid leave days remaining" 
                        : "Employee only has {$remainingDays} paid leave days remaining (needs {$requestedDays})"
                ];
            } else {
                // Fallback: Check FROM lc_approved leave requests
                // Calculate total approved days for this leave type
                $approvedQuery = "SELECT 
                    COALESCE(SUM(total_days), 0) as total_approved
                    FROM lc_leave_requests 
                    WHERE employee_id = ? 
                    AND leave_type = ? 
                    AND status = 'approved'";
                $stmt = $this->db->prepare($approvedQuery);
                $stmt->execute([$employeeId, $leaveType]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $totalApproved = (float)($result['total_approved'] ?? 0);
                
                // Default annual allocation based on leave type
                $annualAllocation = 15; // Default 15 days per year
                if ($leaveType === 'Sick Leave') {
                    $annualAllocation = 15;
                } elseif ($leaveType === 'Vacation Leave') {
                    $annualAllocation = 15;
                } elseif ($leaveType === 'Bereavement Leave') {
                    $annualAllocation = 5;
                } elseif ($leaveType === 'Emergency Leave') {
                    $annualAllocation = 5;
                }
                
                $remainingDays = max(0, $annualAllocation - $totalApproved);
                $hasPaidLeave = $remainingDays >= $requestedDays;
                
                return [
                    'has_paid_leave' => $hasPaidLeave,
                    'remaining_days' => (int)$remainingDays,
                    'message' => $hasPaidLeave 
                        ? "Employee has {$remainingDays} paid leave days remaining (FROM lc_{$annualAllocation} annual allocation)" 
                        : "Employee only has {$remainingDays} paid leave days remaining (needs {$requestedDays})"
                ];
            }
        } catch (PDOException $e) {
            error_log('Error checking paid leave days: ' . $e->getMessage());
            return [
                'has_paid_leave' => false,
                'remaining_days' => 0,
                'message' => 'Unable to verify paid leave balance'
            ];
        }
    }
    
    /**
     * Check if leave request was filed at least 30 days before start date
     * 
     * @param int $leaveId The leave request ID
     * @return array Contains 'filed_in_advance' (bool) and 'days_before' (int)
     */
    public function wasFiledInAdvance($leaveId) {
        try {
            $query = "SELECT created_at, start_date FROM lc_leave_requests WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$leaveId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return [
                    'filed_in_advance' => false,
                    'days_before' => 0,
                    'message' => 'Leave request not found'
                ];
            }
            
            $createdDate = new DateTime($result['created_at']);
            $startDate = new DateTime($result['start_date']);
            $interval = $createdDate->diff($startDate);
            $daysBefore = $interval->days;
            
            $filedInAdvance = $daysBefore >= 30;
            
            return [
                'filed_in_advance' => $filedInAdvance,
                'days_before' => $daysBefore,
                'message' => $filedInAdvance 
                    ? "Leave was filed {$daysBefore} days before start date" 
                    : "Leave was only filed {$daysBefore} days before start date (requires 30 days)"
            ];
        } catch (PDOException $e) {
            error_log('Error checking filed in advance: ' . $e->getMessage());
            return [
                'filed_in_advance' => false,
                'days_before' => 0,
                'message' => 'Unable to verify filing date'
            ];
        }
    }
    
    /**
     * Get documents for a leave request
     */
    public function getLeaveDocuments($leaveId) {
        try {
            $query = "SELECT * FROM lc_leave_documents WHERE leave_id = ? ORDER BY uploaded_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$leaveId]);
            $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Add file_name if not exists (for backward compatibility)
            foreach ($docs as &$doc) {
                if (!isset($doc['file_name']) || empty($doc['file_name'])) {
                    $doc['file_name'] = basename($doc['file_path']);
                }
            }
            return $docs;
        } catch (PDOException $e) {
            error_log('Error fetching documents: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Upload document for a leave request
     */
    public function uploadDocument($leaveId, $documentType) {
        // Check if file was uploaded
        if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'No file uploaded or upload error'];
        }
        
        $file = $_FILES['document'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        
        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid file type. Allowed: JPEG, PNG, GIF, PDF'];
        }
        
        // Create upload directory if not exists
        $uploadDir = __DIR__ . '/../uploads/leave_documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = 'leave_' . $leaveId . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $newFilename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Save to database
            try {
                $query = "INSERT INTO lc_leave_documents (leave_id, document_type, file_path) VALUES (?, ?, ?)";
                $stmt = $this->db->prepare($query);
                $relativePath = 'uploads/leave_documents/' . $newFilename;
                $stmt->execute([$leaveId, $documentType, $relativePath]);
                
                return ['success' => true, 'message' => 'Document uploaded successfully'];
            } catch (PDOException $e) {
                return ['success' => false, 'message' => 'Error saving to database: ' . $e->getMessage()];
            }
        }
        
        return ['success' => false, 'message' => 'Error moving uploaded file'];
    }
}
