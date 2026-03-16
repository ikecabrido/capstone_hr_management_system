<?php

namespace HRManagement\Repositories;

use HRManagement\Database\Database;
use HRManagement\Models\BaseModel;

/**
 * Base Repository Class
 * 
 * Provides common CRUD operations for all repositories
 */
abstract class BaseRepository
{
    protected Database $db;
    protected string $tableName;
    protected string $modelClass;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Create a new record
     */
    public function create(BaseModel $model): int
    {
        $data = $model->toArray();
        unset($data['id'], $data['createdAt'], $data['updatedAt']);

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $query = "INSERT INTO {$this->tableName} ($columns) VALUES ($placeholders)";

        $this->db->execute($query, array_values($data));
        return $this->db->lastInsertId();
    }

    /**
     * Get record by ID
     */
    public function findById(int $id): ?BaseModel
    {
        $query = "SELECT * FROM {$this->tableName} WHERE id = ?";
        $data = $this->db->fetchOne($query, [$id]);
        
        if (!$data) {
            return null;
        }

        $model = new $this->modelClass();
        return $model->fromArray($data);
    }

    /**
     * Get all records
     */
    public function findAll(int $limit = null, int $offset = 0): array
    {
        $query = "SELECT * FROM {$this->tableName} ORDER BY id DESC";
        
        if ($limit !== null) {
            $query .= " LIMIT ? OFFSET ?";
            $data = $this->db->fetchAll($query, [$limit, $offset]);
        } else {
            $data = $this->db->fetchAll($query);
        }

        $models = [];
        foreach ($data as $row) {
            $model = new $this->modelClass();
            $models[] = $model->fromArray($row);
        }

        return $models;
    }

    /**
     * Update a record
     */
    public function update(BaseModel $model): bool
    {
        $data = $model->toArray();
        $id = $data['id'] ?? null;
        
        if (!$id) {
            throw new \InvalidArgumentException('Model must have an ID to update');
        }

        unset($data['id'], $data['createdAt']);
        
        $updates = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));
        $query = "UPDATE {$this->tableName} SET $updates WHERE id = ?";
        
        $params = array_merge(array_values($data), [$id]);
        $this->db->execute($query, $params);
        
        return true;
    }

    /**
     * Delete a record
     */
    public function delete(int $id): bool
    {
        $query = "DELETE FROM {$this->tableName} WHERE id = ?";
        $this->db->execute($query, [$id]);
        return true;
    }

    /**
     * Find with custom where clause
     */
    public function findBy(array $criteria, int $limit = null, int $offset = 0): array
    {
        $where = implode(' AND ', array_map(fn($col) => "$col = ?", array_keys($criteria)));
        $query = "SELECT * FROM {$this->tableName} WHERE $where ORDER BY id DESC";
        
        if ($limit !== null) {
            $query .= " LIMIT ? OFFSET ?";
            $params = array_merge(array_values($criteria), [$limit, $offset]);
        } else {
            $params = array_values($criteria);
        }

        $data = $this->db->fetchAll($query, $params);

        $models = [];
        foreach ($data as $row) {
            $model = new $this->modelClass();
            $models[] = $model->fromArray($row);
        }

        return $models;
    }

    /**
     * Find one record with custom where clause
     */
    public function findOneBy(array $criteria): ?BaseModel
    {
        $where = implode(' AND ', array_map(fn($col) => "$col = ?", array_keys($criteria)));
        $query = "SELECT * FROM {$this->tableName} WHERE $where LIMIT 1";
        
        $data = $this->db->fetchOne($query, array_values($criteria));
        
        if (!$data) {
            return null;
        }

        $model = new $this->modelClass();
        return $model->fromArray($data);
    }

    /**
     * Count records
     */
    public function count(array $criteria = []): int
    {
        $query = "SELECT COUNT(*) as total FROM {$this->tableName}";
        
        if (!empty($criteria)) {
            $where = implode(' AND ', array_map(fn($col) => "$col = ?", array_keys($criteria)));
            $query .= " WHERE $where";
            $result = $this->db->fetchOne($query, array_values($criteria));
        } else {
            $result = $this->db->fetchOne($query);
        }

        return $result['total'] ?? 0;
    }

    /**
     * Execute raw query
     */
    protected function executeQuery(string $query, array $params = []): array
    {
        return $this->db->fetchAll($query, $params);
    }

    /**
     * Execute query returning single result
     */
    protected function executeSingleQuery(string $query, array $params = []): ?array
    {
        return $this->db->fetchOne($query, $params);
    }
}
