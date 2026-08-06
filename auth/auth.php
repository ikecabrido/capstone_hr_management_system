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
        if (!$user) {
            return false;
        }

        // Block inactive/suspended accounts before checking the password.
        if (($user['account_status'] ?? 'Active') !== 'Active') {
            return false;
        }

        $bcryptValid = password_verify($password, $user['password']);

        if (!$bcryptValid && password_get_info($user['password'])['algo'] === 0) {
            $bcryptValid = ($password === $user['password']);
        }

        if (!$bcryptValid) {
            $this->userModel->incrementFailedAttempts($user['id']);
            return false;
        }

        // Regenerate session ID to prevent session fixation
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id(true);

        $userId = $user['id'];
        $employeeId = $user['employee_id'] ?? null;
        if (empty($employeeId) && !empty($userId) && is_numeric($userId)) {
            $employeeId = 'EMP' . str_pad((string)$userId, 3, '0', STR_PAD_LEFT);
        }

        $_SESSION['user'] = [
            'id' => $userId,
            'employee_id' => $employeeId,
            'username' => $user['username'],
            'name' => $user['full_name'] ?? $user['username'] ?? '',
            'full_name' => $user['full_name'] ?? $user['username'] ?? '',
            'role' => $user['role'],
            'theme' => $user['theme'] ?? 'light'
        ];

        // Token support for API and cURL fallback
        if (!isset($_SESSION['token']) || empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(16));
        }

        $this->userModel->recordLastLogin($userId);

        return true;
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