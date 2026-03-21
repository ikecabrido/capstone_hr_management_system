<?php

/**
 * Authentication Controller for Time & Attendance System
 * Handles login, logout, and session management
 */

require_once __DIR__ . '/../models/Users.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../helpers/Helper.php';
require_once __DIR__ . '/../helpers/AuditLog.php';

class AuthController
{
    private $userModel;
    private $auditLog;

    private $employeeModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->auditLog = new AuditLog();
    }

    /**
     * Handle user login
     * 
     * @param string $username - Username
     * @param string $password - Password
     * @return string - Empty string on success, error message on failure
     */
    public function index()
    {
        $title = "Employee Portal Login";
        require __DIR__ . '/../views/auth/login.php';
    }

    public function login()
    {
        Session::start();

        try {
            $employeeID = $_POST['employee_id'] ?? '';
            $password   = $_POST['password'] ?? '';

            if (empty($employeeID) || empty($password)) {
                throw new Exception("Please fill in all fields");
            }

            $employeeID = Helper::sanitize($employeeID);
            $password   = trim($password);

            $user = $this->userModel->login($employeeID);

            if (!$user) {
                throw new Exception("Employee not found");
            }

            if (empty($user['user_id'])) {
                throw new Exception("Employee has no users data");
            }

            if (!password_verify($password, $user['password'])) {
                throw new Exception("Invalid password");
            }

            Session::set('user_id', $user['user_id']);
            Session::set('employee_id', $user['employee_id']);
            Session::set('username', $user['username']);
            Session::set('role', $user['role']);
            Session::set('full_name', $user['full_name']);

            Session::set('success', "Login successful!");

            Helper::redirect('index.php?url=dashboard');
        } catch (Exception $e) {

            Session::set('error', $e->getMessage());

            Helper::redirect('index.php?url=auth-index');
        }
    }

    /**
     * Handle user logout
     */
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            Session::start();
        }

        $user_id = Session::get('user_id');

        if (!empty($user_id)) {
            $this->auditLog->log('LOGOUT', $user_id, null, null, [], 'SUCCESS');
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        Helper::redirect('index.php');
        exit;
    }

    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    public static function isAuthenticated()
    {
        Session::start();

        // Check time_attendance session format
        if (!is_null(Session::get('user_id'))) {
            return true;
        }

        // Check global login session format
        if (isset($_SESSION['user']) && is_array($_SESSION['user']) && isset($_SESSION['user']['id'])) {
            // Auto-sync global session to time_attendance format for compatibility
            $userId = $_SESSION['user']['id'];
            Session::set('user_id', $userId);
            Session::set('email', $_SESSION['user']['username']);

            // Keep the role as-is from global session
            $role = $_SESSION['user']['role'];
            Session::set('role', $role);

            return true;
        }

        return false;
    }

    /**
     * Check if user has specific role
     * 
     * @param string $role - Role to check
     * @return bool
     */
    public static function hasRole($role)
    {
        Session::start();

        // Check time_attendance session format
        $sessionRole = Session::get('role');
        if ($sessionRole === $role) {
            return true;
        }

        // Check global login session format
        if (isset($_SESSION['user']) && is_array($_SESSION['user']) && isset($_SESSION['user']['role'])) {
            return $_SESSION['user']['role'] === $role;
        }

        return false;
    }

    /**
     * Get current user ID
     * 
     * @return int|null
     */
    public static function getCurrentUserId()
    {
        Session::start();

        // Check time_attendance session format
        $userId = Session::get('user_id');
        if (!is_null($userId)) {
            return $userId;
        }

        // Check global login session format
        if (isset($_SESSION['user']) && is_array($_SESSION['user']) && isset($_SESSION['user']['id'])) {
            return $_SESSION['user']['id'];
        }

        return null;
    }

    /**
     * Get current user role
     * 
     * @return string|null
     */
    public static function getCurrentRole()
    {
        Session::start();

        // Check time_attendance session format
        $role = Session::get('role');
        if (!is_null($role)) {
            return $role;
        }

        // Check global login session format
        if (isset($_SESSION['user']) && is_array($_SESSION['user']) && isset($_SESSION['user']['role'])) {
            return $_SESSION['user']['role'];
        }

        return null;
    }
}
