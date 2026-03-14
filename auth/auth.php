<?php

require_once "user.php";

class Auth
{

    private $userModel;

    public function __construct()
    {
        try {
            $this->userModel = new User();
        } catch (Exception $e) {
            throw new Exception("Failed to initialize authentication: " . $e->getMessage());
        }
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login($username, $password)
    {

        $user = $this->userModel->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {

            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'name' => $user['full_name'],
                'role' => $user['role'],
                'theme' => $user['theme'] ?? 'light'
            ];

            // Log the login activity
            $this->logLoginActivity($user['id'], $username);

            return true;
        }

        return false;
    }

    private function logLoginActivity($userId, $username)
    {
        try {
            require_once "database.php";
            $db = new Database();
            $conn = $db->connect();

            // Try to log to activity_logs table
            $sql = "INSERT INTO activity_logs (user_id, username, action, details, timestamp) 
                    VALUES (:user_id, :username, :action, :details, NOW())";
            $stmt = $conn->prepare($sql);
            $success = $stmt->execute([
                ':user_id' => $userId,
                ':username' => $username,
                ':action' => 'LOGIN',
                ':details' => 'User logged in from IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown')
            ]);
            
            if ($success) {
                error_log("[AUTH] ✓ Login logged for user: {$username} (ID: {$userId})");
            } else {
                error_log("[AUTH] ✗ Login log insert returned false for user: {$username}");
            }
        } catch (Exception $e) {
            // If activity_logs table doesn't exist, silently fail (graceful degradation)
            error_log("[AUTH] Activity log failed for {$username}: " . $e->getMessage());
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
