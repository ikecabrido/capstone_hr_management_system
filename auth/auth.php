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

        if (!$user || !password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Invalid username or password.'
            ];
        }

        $employeeId = $user['employee_id'] ?? null;
        if (empty($employeeId) && !empty($user['id']) && is_numeric($user['id'])) {
            $employeeId = 'EMP' . str_pad((string)$user['id'], 3, '0', STR_PAD_LEFT);
        }

        $user = [
            'id' => $user['id'],
            'employee_id' => $employeeId,
            'username' => $user['username'],
            'is_admin' => $user['is_admin'],
            'role' => $user['role'],
            'theme' => $user['theme'] ?? 'light'
        ];

        if (empty($user['is_admin'])) {
            return [
                'success' => false,
                'message' => 'Only admin and authorized personnel can login here.'
            ];
        }

        if (!isset($_SESSION['token']) || empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(16));
        }

        return [
            'success' => true,
            'user' => $user
        ];
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
