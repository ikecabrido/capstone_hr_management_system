<?php

namespace HRManagement\Utils;

/**
 * Authentication Manager
 * 
 * Handles session management and user authentication checks
 */
class AuthManager
{
    /**
     * Start session if not already started
     */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn(): bool
    {
        self::startSession();
        return isset($_SESSION['user_id']) || isset($_SESSION['user']);
    }

    /**
     * Get current user ID
     */
    public static function getCurrentUserId(): ?int
    {
        self::startSession();
        
        if (isset($_SESSION['user_id'])) {
            return (int)$_SESSION['user_id'];
        }

        if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
            return (int)($_SESSION['user']['id'] ?? null);
        }

        return null;
    }

    /**
     * Get current user role
     */
    public static function getCurrentRole(): ?string
    {
        self::startSession();
        
        if (isset($_SESSION['role'])) {
            return $_SESSION['role'];
        }

        if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
            return $_SESSION['user']['role'] ?? null;
        }

        return null;
    }

    /**
     * Login user
     */
    public static function login(int $userId, array $userData): void
    {
        self::startSession();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user'] = $userData;
        $_SESSION['username'] = $userData['username'] ?? '';
        $_SESSION['role'] = $userData['role'] ?? 'employee';
        $_SESSION['full_name'] = $userData['full_name'] ?? '';
        $_SESSION['login_time'] = time();
    }

    /**
     * Logout user
     */
    public static function logout(): void
    {
        self::startSession();
        session_destroy();
    }

    /**
     * Check if user has admin role
     */
    public static function isAdmin(): bool
    {
        $role = self::getCurrentRole();
        return in_array($role, ['admin', 'manager']);
    }

    /**
     * Check if user is learning admin
     */
    public static function isLearningAdmin(): bool
    {
        $role = self::getCurrentRole();
        return in_array($role, ['admin', 'manager', 'learning']);
    }

    /**
     * Check if user can manage
     */
    public static function canManage(): bool
    {
        return self::isLearningAdmin();
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole(string $role): bool
    {
        return self::getCurrentRole() === $role;
    }

    /**
     * Check if user has any of the specified roles
     */
    public static function hasAnyRole(array $roles): bool
    {
        $currentRole = self::getCurrentRole();
        return in_array($currentRole, $roles);
    }

    /**
     * Get current user data
     */
    public static function getCurrentUserData(): ?array
    {
        self::startSession();
        
        if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
            return $_SESSION['user'];
        }

        return null;
    }

    /**
     * Update session data
     */
    public static function updateSessionData(array $data): void
    {
        self::startSession();
        $_SESSION['user'] = array_merge($_SESSION['user'] ?? [], $data);
    }
}
