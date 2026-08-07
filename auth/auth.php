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
            if (!$user && $username === 'admin') {
                $user = $this->userModel->findByUsername('hr_engagement');
            }

            if ($user) {
                $this->setSessionUser($user, $username);
                return true;
            }

            return false;
        }

        // Kasalukuyang validation gamit ang password_verify
        if ($user && password_verify($password, $user['password'])) {
            $this->setSessionUser($user, $username);
            return true;
        }

        return false;
    }

    private function setSessionUser(array $user, string $username)
    {
        $employeeId = $user['employee_id'] ?? null;
        if (empty($employeeId) && !empty($user['id'])) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare('SELECT employee_id FROM employees WHERE user_id = :user_id LIMIT 1');
            $stmt->execute(['user_id' => $user['id']]);
            $row = $stmt->fetch();
            if ($row && !empty($row['employee_id'])) {
                $employeeId = $row['employee_id'];
            }
        }

        $_SESSION['user'] = [
            'id' => $user['user_id'] ?? $user['id'] ?? null,
            'user_id' => $user['user_id'] ?? $user['id'] ?? null,
            'employee_id' => $employeeId,
            'username' => $user['username'] ?? $username,
            'name' => $user['full_name'] ?? $username,
            'role' => $user['role'] ?? 'user',
            'theme' => $user['theme'] ?? 'light'
        ];

        if (!isset($_SESSION['token']) || empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(16));
        }
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
