<?php

require_once "database.php";
require_once "User.php";

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

    // Idagdag ang kondisyon para sa admin123
if (($username === 'admin' || $username === 'hr_engagement') && $password === 'admin123') {
    $_SESSION['user'] = [
        'id' => $user['user_id'] ?? null,
        'employee_id' => $user['employee_id'] ?? null,
        'username' => $username,
        'name' => $user['full_name'] ?? 'Administrator',
        'role' => $user['role'] ?? 'default_admin',
        'theme' => $user['theme'] ?? 'light'
    ];

    if (!isset($_SESSION['token']) || empty($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(16));
    }

    return true;
}

    // Kasalukuyang validation gamit ang password_verify
    if ($user && password_verify($password, $user['password'])) {
        // ...existing code...
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
