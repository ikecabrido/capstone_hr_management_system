<?php

namespace HRManagement\Services;

use HRManagement\Database\Database;
use HRManagement\Models\User;
use HRManagement\Repositories\UserRepository;

/**
 * User Service
 * 
 * Handles user-related business logic
 */
class UserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * Register a new user
     */
    public function register(array $data): int
    {
        $user = new User();
        $user->setUsername($data['username'])
            ->setEmail($data['email'])
            ->setPassword(password_hash($data['password'], PASSWORD_BCRYPT))
            ->setFullName($data['full_name'] ?? '')
            ->setRole($data['role'] ?? 'employee')
            ->setDepartment($data['department'] ?? '')
            ->setPosition($data['position'] ?? '')
            ->setStatus('active');

        return $this->userRepository->create($user);
    }

    /**
     * Authenticate user
     */
    public function authenticate(string $username, string $password): ?User
    {
        $user = $this->userRepository->findByUsername($username);
        
        if (!$user || !password_verify($password, $user->getPassword())) {
            return null;
        }

        return $user;
    }

    /**
     * Update user
     */
    public function updateUser(User $user): bool
    {
        return $this->userRepository->update($user);
    }

    /**
     * Change password
     */
    public function changePassword(int $userId, string $newPassword): bool
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            return false;
        }

        $user->setPassword(password_hash($newPassword, PASSWORD_BCRYPT));
        return $this->userRepository->update($user);
    }

    /**
     * Get user with all details
     */
    public function getUserWithDetails(int $userId): ?array
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            return null;
        }

        return array_merge($user->toArray(), [
            'is_admin' => $user->isAdmin(),
            'is_learning_admin' => $user->isLearningAdmin(),
        ]);
    }

    /**
     * Get all users by department
     */
    public function getUsersByDepartment(string $department): array
    {
        return $this->userRepository->findByDepartment($department);
    }

    /**
     * Get team for manager
     */
    public function getManagerTeam(int $managerId): array
    {
        return $this->userRepository->findByManager($managerId);
    }

    /**
     * Search users
     */
    public function searchUsers(string $term, int $limit = 20): array
    {
        return $this->userRepository->search($term, $limit);
    }

    /**
     * Delete user with cascade cleanup
     */
    public function deleteUser(int $userId): bool
    {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            // Delete related records first (cascade)
            $db->execute('DELETE FROM training_enrollments WHERE user_id = ?', [$userId]);
            $db->execute('DELETE FROM individual_development_plans WHERE user_id = ?', [$userId]);
            $db->execute('DELETE FROM performance_reviews WHERE employee_id = ? OR reviewer_id = ?', [$userId, $userId]);
            $db->execute('DELETE FROM leadership_enrollments WHERE user_id = ?', [$userId]);
            $db->execute('DELETE FROM user_competencies WHERE user_id = ? OR assessed_by = ?', [$userId, $userId]);
            $db->execute('DELETE FROM feedback_360 WHERE employee_id = ? OR reviewer_id = ?', [$userId, $userId]);
            $db->execute('DELETE FROM lms_enrollments WHERE user_id = ?', [$userId]);
            $db->execute('DELETE FROM compliance_assignments WHERE user_id = ?', [$userId]);
            
            // Update any users with this user as manager
            $db->execute('UPDATE users SET manager_id = NULL WHERE manager_id = ?', [$userId]);
            
            // Finally delete user
            $this->userRepository->delete($userId);
            
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            throw new Exception('Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Validate username uniqueness
     */
    public function isUsernameAvailable(string $username, ?int $excludeUserId = null): bool
    {
        $user = $this->userRepository->findByUsername($username);
        
        if (!$user) {
            return true;
        }

        return $excludeUserId !== null && $user->getId() === $excludeUserId;
    }

    /**
     * Validate email uniqueness
     */
    public function isEmailAvailable(string $email, ?int $excludeUserId = null): bool
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user) {
            return true;
        }

        return $excludeUserId !== null && $user->getId() === $excludeUserId;
    }
}
