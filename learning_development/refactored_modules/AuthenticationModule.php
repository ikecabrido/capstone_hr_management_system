<?php

/**
 * Refactored Login/Authentication Module
 * 
 * Shows how to implement authentication with the OOP architecture
 */

require_once __DIR__ . '/../autoload.php';

use HRManagement\Services\UserService;
use HRManagement\Utils\AuthManager;
use HRManagement\Utils\Validator;
use HRManagement\Utils\ResponseHandler;

AuthManager::startSession();

class AuthenticationModule
{
    private UserService $userService;
    private Validator $validator;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->validator = new Validator();
    }

    /**
     * Handle login
     */
    public function handleLogin(string $username, string $password): void
    {
        if (empty($username) || empty($password)) {
            ResponseHandler::error('Username and password are required')->send();
        }

        $user = $this->userService->authenticate($username, $password);
        
        if (!$user) {
            ResponseHandler::error('Invalid username or password', 401)->send();
        }

        // Login successful
        AuthManager::login($user->getId(), $user->toArray());
        
        ResponseHandler::success([
            'user_id' => $user->getId(),
            'username' => $user->getUsername(),
            'role' => $user->getRole(),
            'full_name' => $user->getFullName(),
        ], 'Login successful')->send();
    }

    /**
     * Handle registration
     */
    public function handleRegister(array $data): void
    {
        if (!$this->validator->validateRegistration($data)) {
            ResponseHandler::validationError($this->validator->getErrors())->send();
        }

        // Check username availability
        if (!$this->userService->isUsernameAvailable($data['username'])) {
            ResponseHandler::error('Username already taken', 400)->send();
        }

        // Check email availability
        if (!$this->userService->isEmailAvailable($data['email'])) {
            ResponseHandler::error('Email already registered', 400)->send();
        }

        try {
            $userId = $this->userService->register($data);
            
            ResponseHandler::created(['id' => $userId], 'Registration successful')->send();
        } catch (Exception $e) {
            ResponseHandler::error($e->getMessage())->send();
        }
    }

    /**
     * Handle logout
     */
    public function handleLogout(): void
    {
        AuthManager::logout();
        ResponseHandler::success(null, 'Logged out successfully')->send();
    }

    /**
     * Handle change password
     */
    public function handleChangePassword(string $currentPassword, string $newPassword): void
    {
        if (!AuthManager::isLoggedIn()) {
            ResponseHandler::unauthorized()->send();
        }

        $userId = AuthManager::getCurrentUserId();
        $user = $this->userService->getUserWithDetails($userId);

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            ResponseHandler::error('Current password is incorrect', 400)->send();
        }

        if (!Validator::isValidPassword($newPassword)) {
            ResponseHandler::error('New password must be at least 8 characters', 400)->send();
        }

        try {
            $this->userService->changePassword($userId, $newPassword);
            ResponseHandler::success(null, 'Password changed successfully')->send();
        } catch (Exception $e) {
            ResponseHandler::error($e->getMessage())->send();
        }
    }

    /**
     * Get current user info
     */
    public function getCurrentUserInfo(): ?array
    {
        if (!AuthManager::isLoggedIn()) {
            return null;
        }

        $userId = AuthManager::getCurrentUserId();
        return $this->userService->getUserWithDetails($userId);
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool
    {
        return AuthManager::isLoggedIn();
    }

    /**
     * Check if current user is admin
     */
    public function isAdmin(): bool
    {
        return AuthManager::isAdmin();
    }

    /**
     * Search users
     */
    public function searchUsers(string $term, int $limit = 20): array
    {
        $users = $this->userService->searchUsers($term, $limit);
        
        // Don't expose passwords
        return array_map(function($user) {
            $data = $user->toArray();
            unset($data['password']);
            return $data;
        }, $users);
    }
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module = new AuthenticationModule();
    $action = $_POST['action'] ?? '';

    match ($action) {
        'login' => $module->handleLogin(
            $_POST['username'] ?? '',
            $_POST['password'] ?? ''
        ),
        'register' => $module->handleRegister($_POST),
        'logout' => $module->handleLogout(),
        'change_password' => $module->handleChangePassword(
            $_POST['current_password'] ?? '',
            $_POST['new_password'] ?? ''
        ),
        default => ResponseHandler::error('Invalid action')->send(),
    };
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $module = new AuthenticationModule();
    
    if ($_GET['action'] ?? '' === 'get_current_user') {
        $user = $module->getCurrentUserInfo();
        
        if ($user) {
            ResponseHandler::success($user, 'User info retrieved')->send();
        } else {
            ResponseHandler::unauthorized('Not logged in')->send();
        }
    }
}
