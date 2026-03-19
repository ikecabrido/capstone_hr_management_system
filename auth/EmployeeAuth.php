<?php
/**
 * Employee Authentication Model
 * Handles authentication using employees table with department-based role mapping
 * Enforces department-based access control
 */

require_once "database.php";

class EmployeeAuth
{
    private $db;
    
    // Department to system role mapping
    private $departmentRoleMap = [
        'Human Resources' => 'hr_admin',
        'Information Technology' => 'it_admin', 
        'Finance' => 'payroll',
        'Legal' => 'compliance',
        'Clinic' => 'clinic',
        'Academic' => 'employee',
        'Administration' => 'admin'
    ];
    
    // Department to redirect page mapping
    private $departmentRedirectMap = [
        'Human Resources' => 'legal_compliance/legal_compliance.php',
        'Information Technology' => 'time_attendance/time_attendance.php',
        'Finance' => 'payroll/payroll.php',
        'Legal' => 'legal_compliance/legal_compliance.php',
        'Clinic' => 'clinic/clinic.php',
        'Academic' => 'employee_portal/employee_portal.php',
        'Administration' => 'legal_compliance/legal_compliance.php'
    ];

    // Allowed departments for the system
    private $allowedDepartments = [
        'Human Resources',
        'Information Technology', 
        'Finance',
        'Legal',
        'Clinic',
        'Academic',
        'Administration'
    ];

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Find employee by username (email or employee_number)
     */
    public function findByUsername($username)
    {
        try {
            // Try to find by email first, then by employee_number
            $sql = "SELECT * FROM employees 
                    WHERE email = ? OR employee_number = ? 
                    LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$username, $username]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            // If there's an error (e.g., column doesn't exist), return null
            return null;
        }
    }
    
    /**
     * Authenticate employee with username and password
     * Validates department during authentication
     * Returns employee data with department role info on success, false on failure
     */
    public function authenticate($username, $password)
    {
        try {
            $employee = $this->findByUsername($username);
            
            if (!$employee) {
                return false;
            }
            
            // Check password - support both plain text and hashed passwords
            $passwordValid = false;
            
            // Check if password is hashed
            if (password_get_info($employee['password'])['algo'] !== 0) {
                // Password is hashed
                $passwordValid = password_verify($password, $employee['password']);
            } else {
                // Plain text password check (for demo employees)
                $passwordValid = ($password === $employee['password']);
            }
            
            if (!$passwordValid) {
                return false;
            }
            
            // Check if employee is active
            if (isset($employee['status']) && $employee['status'] !== 'Active') {
                return ['error' => 'Account is not active. Please contact HR.'];
            }
            
            // Validate department - must have a valid department assigned
            $department = $employee['department'] ?? null;
            
            if (empty($department)) {
                return ['error' => 'No department assigned. Please contact HR to assign your department.'];
            }
            
            // Validate that department is in the allowed list
            if (!$this->isValidDepartment($department)) {
                return ['error' => 'Invalid department assignment. Please contact HR.'];
            }
            
            $position = $employee['position'] ?? null;
            
            // Determine system role based on department
            $systemRole = $this->getSystemRole($department);
            
            // Determine redirect page based on department
            $redirectPage = $this->getRedirectPage($department);
            
            // Build user session data with department info - ENFORCE department access
            $userData = [
                'id' => $employee['id'],
                'employee_id' => $employee['employee_number'] ?? $employee['id'],
                'username' => $employee['email'] ?? $username,
                'name' => trim(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? '')),
                'first_name' => $employee['first_name'] ?? '',
                'last_name' => $employee['last_name'] ?? '',
                'email' => $employee['email'] ?? '',
                'department' => $department,        // MUST have department
                'position' => $position,
                'role' => $systemRole,
                'redirect_page' => $redirectPage,
                'theme' => 'light',
                'is_employee_auth' => true,
                'department_access' => $this->getDepartmentAccess($department) // Array of allowed modules
            ];
            
            return $userData;
        } catch (PDOException $e) {
            // If there's a database error, return false to fall back to regular auth
            return false;
        }
    }
    
    /**
     * Validate if department is in allowed list
     */
    private function isValidDepartment($department)
    {
        if (!$department) {
            return false;
        }
        
        // Check exact match
        if (in_array($department, $this->allowedDepartments)) {
            return true;
        }
        
        // Check partial match (case insensitive)
        $departmentLower = strtolower($department);
        foreach ($this->allowedDepartments as $allowed) {
            if (strpos($departmentLower, strtolower($allowed)) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get allowed access modules for a department
     */
    private function getDepartmentAccess($department)
    {
        $accessMap = [
            'Human Resources' => [
                'legal_compliance',
                'recruitment',
                'learning_development',
                'performance',
                'engagement_relations',
                'exit_management',
                'workforce',
                'time_attendance',
                'employee_portal'
            ],
            'Information Technology' => [
                'time_attendance',
                'employee_portal'
            ],
            'Finance' => [
                'payroll',
                'employee_portal'
            ],
            'Legal' => [
                'legal_compliance',
                'employee_portal'
            ],
            'Clinic' => [
                'clinic',
                'employee_portal'
            ],
            'Academic' => [
                'employee_portal'
            ],
            'Administration' => [
                'legal_compliance',
                'employee_portal'
            ]
        ];
        
        $departmentLower = strtolower($department);
        
        foreach ($accessMap as $dept => $access) {
            if (strpos($departmentLower, strtolower($dept)) !== false) {
                return $access;
            }
        }
        
        // Default: only employee portal
        return ['employee_portal'];
    }
    
    /**
     * Get system role based on department
     */
    public function getSystemRole($department)
    {
        if (!$department) {
            return 'employee'; // Default role
        }
        
        // Check exact match first
        if (isset($this->departmentRoleMap[$department])) {
            return $this->departmentRoleMap[$department];
        }
        
        // Check partial match (contains keyword)
        $departmentLower = strtolower($department);
        
        if (strpos($departmentLower, 'human resources') !== false || 
            strpos($departmentLower, 'hr') !== false) {
            return 'hr_admin';
        }
        
        if (strpos($departmentLower, 'information technology') !== false || 
            strpos($departmentLower, 'it ') !== false || 
            strpos($departmentLower, 'tech') !== false) {
            return 'it_admin';
        }
        
        if (strpos($departmentLower, 'finance') !== false || 
            strpos($departmentLower, 'accounting') !== false ||
            strpos($departmentLower, 'accountancy') !== false) {
            return 'payroll';
        }
        
        if (strpos($departmentLower, 'legal') !== false || 
            strpos($departmentLower, 'compliance') !== false) {
            return 'compliance';
        }
        
        if (strpos($departmentLower, 'clinic') !== false || 
            strpos($departmentLower, 'health') !== false ||
            strpos($departmentLower, 'medical') !== false) {
            return 'clinic';
        }
        
        if (strpos($departmentLower, 'academic') !== false || 
            strpos($departmentLower, 'faculty') !== false ||
            strpos($departmentLower, 'teaching') !== false) {
            return 'employee';
        }
        
        return 'employee'; // Default role
    }
    
    /**
     * Get redirect page based on department
     */
    public function getRedirectPage($department)
    {
        if (!$department) {
            return 'employee_portal/employee_portal.php'; // Default page
        }
        
        // Check exact match first
        if (isset($this->departmentRedirectMap[$department])) {
            return $this->departmentRedirectMap[$department];
        }
        
        // Check partial match
        $departmentLower = strtolower($department);
        
        if (strpos($departmentLower, 'human resources') !== false || 
            strpos($departmentLower, 'hr') !== false) {
            return 'legal_compliance/legal_compliance.php';
        }
        
        if (strpos($departmentLower, 'information technology') !== false || 
            strpos($departmentLower, 'it ') !== false || 
            strpos($departmentLower, 'tech') !== false) {
            return 'time_attendance/time_attendance.php';
        }
        
        if (strpos($departmentLower, 'finance') !== false || 
            strpos($departmentLower, 'accounting') !== false ||
            strpos($departmentLower, 'accountancy') !== false) {
            return 'payroll/payroll.php';
        }
        
        if (strpos($departmentLower, 'legal') !== false || 
            strpos($departmentLower, 'compliance') !== false) {
            return 'legal_compliance/legal_compliance.php';
        }
        
        if (strpos($departmentLower, 'clinic') !== false || 
            strpos($departmentLower, 'health') !== false ||
            strpos($departmentLower, 'medical') !== false) {
            return 'clinic/clinic.php';
        }
        
        return 'employee_portal/employee_portal.php'; // Default
    }
    
    /**
     * Get all departments for dropdown
     */
    public function getDepartments()
    {
        $sql = "SELECT DISTINCT department FROM employees 
                WHERE department IS NOT NULL AND department != '' 
                ORDER BY department";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Check if an employee exists by email or employee_number
     */
    public function employeeExists($username)
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM employees 
                    WHERE email = ? OR employee_number = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$username, $username]);
            $result = $stmt->fetch();
            
            return $result['count'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Validate if user has access to specific module
     * Call this in each module to enforce department-based access
     */
    public static function hasModuleAccess($moduleName)
    {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['department_access'])) {
            return false;
        }
        
        $userAccess = $_SESSION['user']['department_access'];
        return in_array($moduleName, $userAccess);
    }
    
    /**
     * Enforce department-based access - redirect if no access
     * Call this at the top of each module page
     */
    public static function enforceDepartmentAccess($moduleName, $redirectTo = null)
    {
        if (!self::hasModuleAccess($moduleName)) {
            // No access - redirect to appropriate page
            $redirectTo = $redirectTo ?? $_SESSION['user']['redirect_page'] ?? 'login_form.php';
            header("Location: " . $redirectTo);
            exit;
        }
    }
}
