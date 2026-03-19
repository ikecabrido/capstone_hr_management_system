<?php
/**
 * Department Access Control Middleware
 * 
 * This file provides functions to enforce department-based access control.
 * Include this file in each module page to restrict access based on user's department.
 * 
 * Usage:
 *   require_once "auth/DepartmentAccess.php";
 *   DepartmentAccess::enforceAccess('module_name');
 * 
 * Or check access without redirect:
 *   if (!DepartmentAccess::hasAccess('module_name')) {
 *       // Show access denied message
 *   }
 */

class DepartmentAccess
{
    // Module name to folder mapping
    private static $moduleMap = [
        'legal_compliance' => 'legal_compliance',
        'recruitment' => 'recruitment',
        'payroll' => 'payroll',
        'time_attendance' => 'time_attendance',
        'clinic' => 'clinic',
        'workforce' => 'workforce',
        'employee_portal' => 'employee_portal',
        'employee' => 'employee',
        'learning_development' => 'learning_development',
        'performance' => 'performance',
        'engagement_relations' => 'engagement_relations',
        'exit_management' => 'exit_management'
    ];
    
    // Department access rules - which departments can access which modules
    private static $accessRules = [
        // HR Department - Full access to HR modules + employee portal
        'Human Resources' => [
            'legal_compliance',
            'recruitment', 
            'learning_development',
            'performance',
            'engagement_relations',
            'exit_management',
            'workforce',
            'time_attendance',
            'employee_portal',
            'employee'
        ],
        
        // IT Department - Limited access
        'Information Technology' => [
            'time_attendance',
            'employee_portal',
            'employee'
        ],
        
        // Finance - Payroll access
        'Finance' => [
            'payroll',
            'employee_portal',
            'employee'
        ],
        
        // Legal - Compliance access
        'Legal' => [
            'legal_compliance',
            'employee_portal',
            'employee'
        ],
        
        // Clinic - Health services access
        'Clinic' => [
            'clinic',
            'employee_portal',
            'employee'
        ],
        
        // Academic - Only employee portal
        'Academic' => [
            'employee_portal',
            'employee'
        ],
        
        // Administration
        'Administration' => [
            'legal_compliance',
            'employee_portal',
            'employee'
        ]
    ];
    
    /**
     * Check if current user has access to specific module
     * @param string $moduleName - The module/folder name
     * @return bool - True if access allowed, false otherwise
     */
    public static function hasAccess($moduleName)
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return false;
        }
        
        $user = $_SESSION['user'];
        
        // Check if this is employee-based authentication
        if (!isset($user['is_employee_auth']) || $user['is_employee_auth'] !== true) {
            // Non-employee users (admin users) have full access
            return true;
        }
        
        // Get user's department
        $department = $user['department'] ?? null;
        
        if (!$department) {
            return false;
        }
        
        // Check if department has access to this module
        $allowedModules = self::getAllowedModules($department);
        
        return in_array($moduleName, $allowedModules);
    }
    
    /**
     * Enforce access - redirect to appropriate page if no access
     * @param string $moduleName - The module/folder name
     * @param string|null $redirectTo - Custom redirect URL (optional)
     */
    public static function enforceAccess($moduleName, $redirectTo = null)
    {
        if (!self::hasAccess($moduleName)) {
            // Get the user's allowed redirect page
            $redirectTo = $redirectTo ?? $_SESSION['user']['redirect_page'] ?? 'login_form.php';
            
            // Store message for display
            $_SESSION['access_denied'] = 'You do not have access to this module. Please contact your administrator.';
            
            header("Location: " . $redirectTo);
            exit;
        }
    }
    
    /**
     * Get allowed modules for a department
     * @param string $department - Department name
     * @return array - List of allowed module names
     */
    private static function getAllowedModules($department)
    {
        // Try exact match first
        if (isset(self::$accessRules[$department])) {
            return self::$accessRules[$department];
        }
        
        // Try partial match
        $departmentLower = strtolower($department);
        
        foreach (self::$accessRules as $dept => $modules) {
            if (strpos($departmentLower, strtolower($dept)) !== false) {
                return $modules;
            }
        }
        
        // Default: only employee portal
        return ['employee_portal', 'employee'];
    }
    
    /**
     * Get current user's department
     * @return string|null - Department name or null
     */
    public static function getUserDepartment()
    {
        if (!isset($_SESSION['user'])) {
            return null;
        }
        
        return $_SESSION['user']['department'] ?? null;
    }
    
    /**
     * Get current user's role
     * @return string|null - Role name or null
     */
    public static function getUserRole()
    {
        if (!isset($_SESSION['user'])) {
            return null;
        }
        
        return $_SESSION['user']['role'] ?? null;
    }
    
    /**
     * Check if current user is from HR department
     * @return bool
     */
    public static function isHR()
    {
        $department = self::getUserDepartment();
        if (!$department) {
            return false;
        }
        
        $deptLower = strtolower($department);
        return strpos($deptLower, 'human resources') !== false || 
               strpos($deptLower, 'hr') !== false;
    }
    
    /**
     * Check if current user is from Finance department
     * @return bool
     */
    public static function isFinance()
    {
        $department = self::getUserDepartment();
        if (!$department) {
            return false;
        }
        
        $deptLower = strtolower($department);
        return strpos($deptLower, 'finance') !== false || 
               strpos($deptLower, 'accounting') !== false;
    }
    
    /**
     * Get all modules the current user has access to
     * @return array - List of allowed modules
     */
    public static function getUserModules()
    {
        $department = self::getUserDepartment();
        
        if (!$department) {
            return [];
        }
        
        return self::getAllowedModules($department);
    }
    
    /**
     * Require HR access - redirect if not HR
     */
    public static function requireHR($redirectTo = null)
    {
        if (!self::isHR()) {
            $redirectTo = $redirectTo ?? $_SESSION['user']['redirect_page'] ?? 'login_form.php';
            $_SESSION['access_denied'] = 'HR department access required.';
            header("Location: " . $redirectTo);
            exit;
        }
    }
    
    /**
     * Require Finance access - redirect if not Finance
     */
    public static function requireFinance($redirectTo = null)
    {
        if (!self::isFinance()) {
            $redirectTo = $redirectTo ?? $_SESSION['user']['redirect_page'] ?? 'login_form.php';
            $_SESSION['access_denied'] = 'Finance department access required.';
            header("Location: " . $redirectTo);
            exit;
        }
    }
}
