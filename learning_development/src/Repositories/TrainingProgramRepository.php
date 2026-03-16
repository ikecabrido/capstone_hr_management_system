<?php

namespace HRManagement\Repositories;

use HRManagement\Models\TrainingProgram;

/**
 * Training Program Repository
 */
class TrainingProgramRepository extends BaseRepository
{
    protected string $tableName = 'training_programs';
    protected string $modelClass = TrainingProgram::class;

    /**
     * Find programs by category
     */
    public function findByCategory(string $category, int $limit = null): array
    {
        return $this->findBy(['category' => $category], $limit);
    }

    /**
     * Find programs by status
     */
    public function findByStatus(string $status, int $limit = null): array
    {
        return $this->findBy(['status' => $status], $limit);
    }

    /**
     * Find active programs
     */
    public function findActive(int $limit = null): array
    {
        return $this->findByStatus('active', $limit);
    }

    /**
     * Find programs created by user
     */
    public function findByCreator(int $userId, int $limit = null): array
    {
        return $this->findBy(['created_by' => $userId], $limit);
    }

    /**
     * Search programs by name or description
     */
    public function search(string $searchTerm, int $limit = 20, int $offset = 0): array
    {
        $term = "%$searchTerm%";
        $query = "SELECT * FROM {$this->tableName} 
                  WHERE name LIKE ? OR description LIKE ? OR category LIKE ?
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
                    tp.*,
                    COUNT(DISTINCT te.id) as total_enrollments,
                    SUM(CASE WHEN te.status = 'completed' THEN 1 ELSE 0 END) as completed_count,
                    AVG(te.progress_percentage) as avg_progress
                  FROM {$this->tableName} tp
                  LEFT JOIN training_enrollments te ON tp.id = te.program_id
                  WHERE tp.id = ?
                  GROUP BY tp.id";
        
        return $this->db->fetchOne($query, [$programId]);
    }
}
