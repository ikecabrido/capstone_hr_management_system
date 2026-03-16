<?php

namespace HRManagement\Repositories;

use HRManagement\Models\LeadershipProgram;

/**
 * Leadership Program Repository
 */
class LeadershipProgramRepository extends BaseRepository
{
    protected string $tableName = 'leadership_programs';
    protected string $modelClass = LeadershipProgram::class;

    /**
     * Find active programs
     */
    public function findActive(int $limit = null): array
    {
        return $this->findBy(['status' => 'active'], $limit);
    }

    /**
     * Find programs by level
     */
    public function findByLevel(string $level, int $limit = null): array
    {
        return $this->findBy(['level' => $level], $limit);
    }

    /**
     * Find programs by focus area
     */
    public function findByFocusArea(string $focusArea, int $limit = null): array
    {
        return $this->findBy(['focus_area' => $focusArea], $limit);
    }

    /**
     * Search programs
     */
    public function search(string $searchTerm, int $limit = 20, int $offset = 0): array
    {
        $term = "%$searchTerm%";
        $query = "SELECT * FROM {$this->tableName} 
                  WHERE name LIKE ? OR description LIKE ? OR focus_area LIKE ?
                  ORDER BY created_at DESC 
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
     * Get program with enrollment statistics
     */
    public function getProgramStats(int $programId): ?array
    {
        $query = "SELECT 
                    lp.*,
                    COUNT(DISTINCT le.id) as total_enrollments,
                    SUM(CASE WHEN le.status = 'completed' THEN 1 ELSE 0 END) as completed_count
                  FROM {$this->tableName} lp
                  LEFT JOIN leadership_enrollments le ON lp.id = le.program_id
                  WHERE lp.id = ?
                  GROUP BY lp.id";
        
        return $this->db->fetchOne($query, [$programId]);
    }
}
