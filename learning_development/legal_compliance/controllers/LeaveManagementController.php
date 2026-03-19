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
            $checkColumn = "SHOW COLUMNS FROM leave_requests LIKE 'checklist_data'";
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
            $checkColumn = "SHOW COLUMNS FROM leave_documents LIKE 'leave_id'";
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
            $query = "SELECT id, LOWER(first_name) as first_name FROM employees";
            $stmt = $this->db->query($query);
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($employees as $emp) {
                $firstName = $emp['first_name'];
                $gender = null;
                
                if (in_array($firstName, $femaleNames)) {
                    $gender = 'Female';
                } elseif (in_array($firstName, $maleNames)) {
                    $gender = 'Male';
                }
                
                if ($gender) {
                    $update = "UPDATE employees SET gender = ? WHERE id = ?";
                    $stmt = $this->db->prepare($update);
                    $stmt->execute([$gender, $emp['id']]);
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
        $checkData = "SELECT COUNT(*) as count FROM leave_requests";
        $stmt = $this->db->query($checkData);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] == 0) {
            $sampleData = "
            INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, total_days, reason, status, checklist_data) VALUES
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
            
            // Insert into database
            $insertDoc = "INSERT INTO leave_documents (leave_id, document_type, file_path, uploaded_at) 
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
        $checkDocs = "SELECT COUNT(*) as count FROM leave_documents";
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
            SELECT lr.*, e.first_name, e.last_name, e.department
            FROM leave_requests lr
            LEFT JOIN employees e ON lr.employee_id = e.id
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
            FROM leave_requests
        ";
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get detailed leave request by ID
     */
    public function getLeaveDetails($leaveId) {
        // First get basic leave request info
        $query = "SELECT * FROM leave_requests WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$leaveId]);
        $leave = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$leave) {
            return null;
        }
        
        // Try to get employee info if employees table exists
        try {
            $empQuery = "SELECT first_name, last_name, gender, civil_status, hire_date 
                        FROM employees WHERE id = ?";
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
        $query = "UPDATE leave_requests SET status = ?, checked_by = ?, checked_at = NOW(), hr_comments = ?, checklist_data = ? WHERE id = ?";
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
            $query = "SELECT * FROM employees WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$employeeId]);
            $result['employee'] = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error fetching employee: ' . $e->getMessage());
        }
        
        // Try to get leave balance
        try {
            $balanceQuery = "SELECT * FROM leave_balances WHERE employee_id = ? AND leave_type = ?";
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
            $query = "SELECT gender FROM employees WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$employeeId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && isset($result['gender']) && $result['gender']) {
                return strtolower($result['gender']) === 'female';
            }
            
            // Fallback: Try to guess from first name
            $nameQuery = "SELECT first_name FROM employees WHERE id = ?";
            $stmt = $this->db->prepare($nameQuery);
            $stmt->execute([$employeeId]);
            $nameResult = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($nameResult) {
                $firstName = strtolower($nameResult['first_name']);
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
            $query = "SELECT gender FROM employees WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$employeeId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && isset($result['gender']) && $result['gender']) {
                return strtolower($result['gender']) === 'male';
            }
            
            // Fallback: Try to guess from first name
            $nameQuery = "SELECT first_name FROM employees WHERE id = ?";
            $stmt = $this->db->prepare($nameQuery);
            $stmt->execute([$employeeId]);
            $nameResult = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($nameResult) {
                $firstName = strtolower($nameResult['first_name']);
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
                    $query = "SELECT $column FROM employees WHERE id = ?";
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
            $query = "SELECT hire_date FROM employees WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$employeeId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && $result['hire_date']) {
                $hireDate = new DateTime($result['hire_date']);
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
                    ['id' => 'documents_complete', 'label' => 'All required documents submitted', 'required' => true]
                ],
                'hrChecks' => [
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
                    ['id' => 'service_duration', 'label' => 'Employment duration requirement met', 'required' => false]
                ],
                'hrChecks' => [
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
                    ['id' => 'reason_stated', 'label' => 'Proper reason stated', 'required' => true]
                ],
                'hrChecks' => [
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
                    ['id' => 'filed_advance', 'label' => 'Filed in advance (per company policy)', 'required' => false]
                ],
                'hrChecks' => [
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
                    ['id' => 'relationship', 'label' => 'Relationship to deceased (immediate family)', 'required' => true]
                ],
                'hrChecks' => [
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
                    ['id' => 'supporting_explanation', 'label' => 'Supporting explanation provided', 'required' => true]
                ],
                'hrChecks' => [
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
            $hrId = $_SESSION['user']['id'] ?? 1;
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
     * Get documents for a leave request
     */
    public function getLeaveDocuments($leaveId) {
        try {
            $query = "SELECT * FROM leave_documents WHERE leave_id = ? ORDER BY uploaded_at DESC";
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
                $query = "INSERT INTO leave_documents (leave_id, document_type, file_path) VALUES (?, ?, ?)";
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
