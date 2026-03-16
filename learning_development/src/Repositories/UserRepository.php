<?php

namespace HRManagement\Repositories;

use HRManagement\Models\User;

/**
 * User Repository
 */
class UserRepository extends BaseRepository
{
    protected string $tableName = 'users';
    protected string $modelClass = User::class;

    /**
     * Find user by username
     */
    public function findByUsername(string $username): ?User
    {
        return $this->findOneBy(['username' => $username]);
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * Find users by role
     */
    public function findByRole(string $role, int $limit = null): array
    {
        return $this->findBy(['role' => $role], $limit);
    }

    /**
     * Find users by department
     */
    public function findByDepartment(string $department, int $limit = null): array
    {
        return $this->findBy(['department' => $department], $limit);
    }

    /**
     * Find active users
     */
    public function findActive(int $limit = null): array
    {
        return $this->findBy(['status' => 'active'], $limit);
    }

    /**
     * Find users managed by a specific manager
     */
    public function findByManager(int $managerId): array
    {
        $query = "SELECT * FROM {$this->tableName} WHERE manager_id = ? ORDER BY full_name ASC";
        $data = $this->db->fetchAll($query, [$managerId]);

        $models = [];
        foreach ($data as $row) {
            $model = new $this->modelClass();
            $models[] = $model->fromArray($row);
        }

        return $models;
    }

    /**
     * Get users with pagination and search
     */
    public function search(string $searchTerm, int $limit = 20, int $offset = 0): array
    {
        $term = "%$searchTerm%";
        $query = "SELECT * FROM {$this->tableName} 
                  WHERE username LIKE ? OR email LIKE ? OR full_name LIKE ? 
                  ORDER BY full_name ASC 
                  LIMIT ? OFFSET ?";
        
        $data = $this->db->fetchAll($query, [$term, $term, $term, $limit, $offset]);

        $models = [];
        foreach ($data as $row) {
            $model = new $this->modelClass();
            $models[] = $model->fromArray($row);
        }

        return $models;
    }
}
