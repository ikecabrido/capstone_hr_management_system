<?php

namespace HRManagement\Repositories;

use HRManagement\Models\CareerPath;

/**
 * Career Path Repository
 */
class CareerPathRepository extends BaseRepository
{
    protected string $tableName = 'career_paths';
    protected string $modelClass = CareerPath::class;

    /**
     * Find active career paths
     */
    public function findActive(int $limit = null): array
    {
        return $this->findBy(['status' => 'active'], $limit);
    }

    /**
     * Find paths by status
     */
    public function findByStatus(string $status, int $limit = null): array
    {
        return $this->findBy(['status' => $status], $limit);
    }

    /**
     * Find paths by target position
     */
    public function findByTargetPosition(string $position): array
    {
        $query = "SELECT * FROM {$this->tableName} WHERE target_position LIKE ?";
        $data = $this->db->fetchAll($query, ["%$position%"]);

        $models = [];
        foreach ($data as $row) {
            $model = new $this->modelClass();
            $models[] = $model->fromArray($row);
        }

        return $models;
    }

    /**
     * Search career paths
     */
    public function search(string $searchTerm, int $limit = 20, int $offset = 0): array
    {
        $term = "%$searchTerm%";
        $query = "SELECT * FROM {$this->tableName} 
                  WHERE name LIKE ? OR description LIKE ? OR target_position LIKE ?
                  ORDER BY name ASC 
                  LIMIT ? OFFSET ?";
        
        $data = $this->db->fetchAll($query, [$term, $term, $term, $limit, $offset]);

        $models = [];
        foreach ($data as $row) {
            $model = new $this->modelClass();
            $models[] = $model->fromArray($row);
        }

        return $models;
    }

    /**
     * Get path with enrollment statistics
     */
    public function getPathStats(int $pathId): ?array
    {
        $query = "SELECT 
                    cp.*,
                    COUNT(DISTINCT idp.id) as total_idps,
                    SUM(CASE WHEN idp.status = 'completed' THEN 1 ELSE 0 END) as completed_idps
                  FROM {$this->tableName} cp
                  LEFT JOIN individual_development_plans idp ON cp.id = idp.career_path_id
                  WHERE cp.id = ?
                  GROUP BY cp.id";
        
        return $this->db->fetchOne($query, [$pathId]);
    }
}
