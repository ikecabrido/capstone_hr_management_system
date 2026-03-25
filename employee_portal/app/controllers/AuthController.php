<?php

require_once __DIR__ . '/../models/Users.php';
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../core/Helper.php';
require_once __DIR__ . '/../core/AuditLog.php';

class AuthController
{
    private $userModel;
    private $auditLog;

    public function __construct()
    {
        $this->userModel = new User();
        $this->auditLog  = new AuditLog();
    }

    public function login()
    {
        Session::start();

        try {
            $employee_no = Helper::sanitize($_POST['employee_no'] ?? '');
            $password   = trim($_POST['password'] ?? '');

            if (empty($employee_no) || empty($password)) {
                throw new Exception("Please fill in all fields");
            }

            $user = $this->userModel->login($employee_no);
            
            if (!$user || empty($user['user_id'])) {
                throw new Exception("Invalid credentials");
            }

            if (!password_verify($password, $user['password'])) {
                throw new Exception("Invalid credentials");
            }

            Session::set('user_id', $user['user_id']);
            Session::set('employee_no', $user['employee_no']);
            Session::set('username', $user['username']);
            Session::set('role', $user['role']);
            Session::set('first_name', $user['first_name']);
            Session::set('last_name', $user['last_name']);
            Session::set('success', "Login successful!");

            Helper::redirect('index.php?url=dashboard');
        } catch (Exception $e) {
            Session::set('error', $e->getMessage());
            Helper::redirect('index.php?url=auth-index');
        }
    }

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

    public function index()
    {
        $title = "Employee Portal Login";
        require __DIR__ . '/../views/auth/login.php';
    }

    public static function getCurrentUserId()
    {
        Session::start();
        return Session::get('user_id');
    }

    public static function requireAuth()
    {
        session_start(); 

        if (!isset($_SESSION['user_id'])) {
            header('Location: /index.php'); 
            exit(); 
        }

        return $_SESSION['user_id'];
    }
}
