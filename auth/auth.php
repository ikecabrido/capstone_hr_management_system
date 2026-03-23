<?php

require_once "user.php";

class Auth
{

    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login($username, $password)
    {

        $user = $this->userModel->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {

            $employeeId = $user['employee_id'] ?? null;
            if (empty($employeeId) && !empty($user['id']) && is_numeric($user['id'])) {
                $employeeId = 'EMP' . str_pad((string)$user['id'], 3, '0', STR_PAD_LEFT);
            }

            $_SESSION['user'] = [
                'id' => $user['id'],
                'employee_id' => $employeeId,
                'username' => $user['username'],
                'name' => $user['full_name'],
                'role' => $user['role'],
                'theme' => $user['theme'] ?? 'light'
            ];

            // Token support for API and cURL fallback
            if (!isset($_SESSION['token']) || empty($_SESSION['token'])) {
                $_SESSION['token'] = bin2hex(random_bytes(16));
            }

            return true;
        }

        return false;
    }

    public function check()
    {
        return isset($_SESSION['user']);
    }

    public function user()
    {
        return $_SESSION['user'] ?? null;
    }

    public function role()
    {
        return $_SESSION['user']['role'] ?? null;
    }

    public function logout()
    {
        session_destroy();
    }
}